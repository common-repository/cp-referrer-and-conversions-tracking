<?php


global $wpdb;

$this->item = 1; //intval($_GET["cal"]);

$current_user = wp_get_current_user();
$current_user_access = current_user_can('edit_pages');

if ( !is_admin() || !$current_user_access )
{
    echo 'Direct access not allowed.';
    exit;
}


if ( !is_admin() )
{
    echo 'Direct access not allowed.';
    exit;
}


$current_page = (isset($_GET["p"]) ? intval($_GET["p"]) : 1);
if (!$current_page) $current_page = 1;
$records_per_page = 50;

$date_start = '';
$date_end = '';
$search_value = '';

$cond = '';
if (isset($_GET["search"]) && $_GET["search"] != '') 
{
    $search_value = sanitize_text_field($_GET["search"]);
    $cond .= " AND (data like '%".esc_sql($search_value)."%' OR referrer LIKE '%".esc_sql($search_value)."%')";
}   

if (isset($_GET["dfrom"]) && $_GET["dfrom"] != '')
{
    $date_start = sanitize_text_field($_GET["dfrom"]);
    $cond .= " AND (`time` >= '".esc_sql($date_start)."')";
}

if (isset($_GET["dto"]) && $_GET["dto"] != '')
{
    $date_end = sanitize_text_field($_GET["dto"]);
    $cond .= " AND (`time` <= '".esc_sql($date_end)." 23:59:59')";
}

$events = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->table_messages." WHERE 1=1 ".$cond." ORDER BY `time` DESC" );

// general initialization
$fields = array();
$fields["referrer"] = array();
$fields["time"] = array();
$fields["date"] = array();
$fields["ip"] = array();

foreach ($events as $item)
{
    if (!isset($fields["referrer"]["k".$item->referrer])) $fields["referrer"]["k".$item->referrer] = 0;
    if (!isset($fields["date"]["k".substr($item->time,0,10)])) $fields["date"]["k".substr($item->time,0,10)] = 0;
    if (!isset($fields["time"]["k".substr($item->time,11,2)])) $fields["time"]["k".substr($item->time,11,2)] = 0;
    if (isset($item->ipaddr) && !isset($fields["ip"]["k".$item->ipaddr])) $fields["ip"]["k".$item->ipaddr] = 0;
    
    $fields["referrer"]["k".$item->referrer]++;
    $fields["date"]["k".substr($item->time,0,10)]++;
    $fields["time"]["k".substr($item->time,11,2)]++;
    if (isset($item->ipaddr)) $fields["ip"]["k".$item->ipaddr]++;
    if (isset($item->posted_data))
    {
        $params = unserialize($item->posted_data);
        foreach ($params as $param => $value)
            if (strlen($value) < 100)
                $fields[$param]["k".$value]++;
    }
}


// line graphs
$hourly_messages = '';
for ($i=0;$i<=23;$i++)
    if (isset($fields['time']['k'.($i<10?'0':'').$i]))
        $hourly_messages .= $fields['time']['k'.($i<10?'0':'').$i].($i<23?',':'');
    else
        $hourly_messages .='0'.($i<23?',':'');

if ($date_start == '' && is_array($fields["date"]) && count($fields["date"]))
    $date_start = substr(min(array_keys($fields["date"])),1);
if ($date_end == '' && is_array($fields["date"]) && count($fields["date"]))
    $date_end = substr(max(array_keys($fields["date"])),1);

$daily_messages = '';
$date = $date_start;
while ($date <= $date_end)
{
    if (isset($fields['date']['k'.$date]))
        $daily_messages .= ','.$fields['date']['k'.$date];
    else
        $daily_messages .=',0';
    $date = date("Y-m-d",strtotime($date." +1 day"));
}
$daily_messages = substr($daily_messages,1);

if (!isset($_GET["field"])) 
    $field_filter = 'referrer';
else
    $field_filter = sanitize_key($_GET["field"]);

$color_array = array('ffb3ba','ffdfba','ffffba', 'baffc9', 'bae1ff', 'a8e6cf', 'dcedc1', 'ffd3b6', 'ffaaa5', 'ff8b94', 'eea990', 'adcbe3', 'e2f4c7');


$form = array();

?>

<h1><?php _e('Stats','cp-referrer-and-conversions-tracking'); ?></h1>

<div class="ahb-buttons-container">
	<a href="<?php print esc_attr(admin_url('admin.php?page='.$this->menu_parameter));?>" class="ahb-return-link">&larr;<?php _e('Return to the main settings page','cp-referrer-and-conversions-tracking'); ?></a>
	<div class="clear"></div>
</div>

<?php require_once dirname( __FILE__ ).'/cp-full-stats.inc.php'; ?>

<div class="ahb-section-container">
	<div class="ahb-section">
       <form action="admin.php" method="get">
        <input type="hidden" name="page" value="<?php echo esc_attr($this->menu_parameter); ?>_report" />
        <input type="hidden" name="field" value="<?php echo esc_attr($field_filter); ?>" />
		<nobr><label><?php _e('Search for','cp-referrer-and-conversions-tracking'); ?>:</label> <input type="text" name="search" value="<?php echo esc_attr($search_value); ?>">&nbsp;&nbsp;</nobr>
		<nobr><label><?php _e('From','cp-referrer-and-conversions-tracking'); ?>:</label> <input autocomplete="off" type="text" id="dfrom" name="dfrom" value="<?php echo esc_attr($date_start); ?>" >&nbsp;&nbsp;</nobr>
		<nobr><label><?php _e('To','cp-referrer-and-conversions-tracking'); ?>:</label> <input autocomplete="off" type="text" id="dto" name="dto" value="<?php echo esc_attr($date_end); ?>" >&nbsp;&nbsp;</nobr>
		<nobr><label><?php _e('Item','cp-referrer-and-conversions-tracking'); ?>:</label> <select id="cal" name="cal">
          <option value="0">[<?php _e('All Items','cp-referrer-and-conversions-tracking'); ?>]</option>
    </select></nobr>
		<nobr>
			<input type="submit" name="<?php echo esc_attr($this->prefix); ?>_csv" value="<?php _e('Export to CSV','cp-referrer-and-conversions-tracking'); ?>" class="button" style="float:right;margin-left:10px;">
			<input type="submit" name="ds" value="<?php _e('Filter','cp-referrer-and-conversions-tracking'); ?>" class="button-primary button" style="float:right;">
		</nobr>
       </form>
	</div>
</div>


<div class="ahb-statssection-container" style="background:#f6f6f6;float:left;width:48%;">
	<div class="ahb-statssection-header">
		<h3><?php _e('Logs per day','cp-referrer-and-conversions-tracking'); ?></h3>
	</div>
	<div class="ahb-statssection">
        <div class="canvas" id="cardiocontainer1" style="margin-left:10px;position:relative;">
         <canvas id="cardio1"  width="300" height="200" questions='[{"color":"#008ec2","values":[<?php echo htmlentities($daily_messages); ?>]}]'></canvas>
        </div>
        <div style="padding-right:5px;padding-left:5px;color:#888888;">* <?php _e('Logs per day in the selected date range.','cp-referrer-and-conversions-tracking'); ?><br />&nbsp;&nbsp; <?php _e('Days from','cp-referrer-and-conversions-tracking'); ?> <?php echo esc_html($date_start); ?> to <?php echo esc_html($date_end); ?>.</div>
        <div class="clear"></div>
	</div>
</div>

<div class="ahb-statssection-container" style="background:#f6f6f6;float:right;width:48%;">
	<div class="ahb-statssection-header">
		<h3><?php _e('Logs per hour','cp-referrer-and-conversions-tracking'); ?></h3>
	</div>
	<div class="ahb-statssection" >
		<div class="canvas" id="cardiocontainer2" style="margin-left:10px;position:relative;">
         <canvas id="cardio2"  width="312" height="200" questions='[{"color":"#008ec2","values":[<?php echo esc_html($hourly_messages); ?>]}]'></canvas>
        </div>
        <div style="padding-right:5px;padding-left:5px;color:#888888;">* <?php _e('Total logs per hour in the selected date range.','cp-referrer-and-conversions-tracking'); ?><br />&nbsp;&nbsp; <?php _e('Hours from 0 to 23','cp-referrer-and-conversions-tracking'); ?>.</div>
        <div class="clear"></div>
	</div>
</div>
<div class="clear"></div>

<br />

<div class="ahb-statssection-container" style="background:#f6f6f6;">
	<div class="ahb-statssection-header">
        <form action="admin.php" name="cfm_formrep" method="get">
         <input type="hidden" name="page" value="<?php echo esc_attr($this->menu_parameter); ?>_report" />
         <input type="hidden" name="search" value="<?php echo esc_attr($search_value); ?>" />
         <input type="hidden" name="dfrom" value="<?php echo esc_attr($date_start); ?>" />
         <input type="hidden" name="dto" value="<?php echo esc_attr($date_end); ?>" />
		 <h3><?php _e('Select field for the report','cp-referrer-and-conversions-tracking'); ?>: <select name="field" onchange="document.cfm_formrep.submit();">
              <?php
                   foreach ($fields as $item => $value)
                       echo '<option value="'.esc_attr($item).'"'.($field_filter==$item?' selected':'').'>'.esc_html($item).'</option>';
              ?>
         </select></h3>
        </form>
	</div>
	<div class="ahb-statssection">
        <div id="dex_printable_contents">

        <div style="width:100%;padding:0;background:white;border:1px solid #e6e6e6;">
         <div style="padding:10px;background:#ECECEC;color:#21759B;font-weight: bold;">
           <?php _e('Report of values for','cp-referrer-and-conversions-tracking'); ?>: <em><?php echo esc_html($field_filter); ?></em>
         </div>

        <div style="padding:10px;">
        <?php
          $arr = $fields[ $field_filter ];
          arsort($arr, SORT_NUMERIC);
          $total = 0;
          /* $totalsize = 600; */
          foreach ($arr as $item => $value)
              $total += $value;
          /* $max = max($arr);
          $totalsize = round(600 / ($max/$total) ); */
          $count = 0;
          foreach ($arr as $item => $value)
          {
              echo esc_html($value).' times: '.esc_html(strlen($item)>50?substr($item,1,50).'...':substr($item,1));
              echo '<div style="width:'.round($value/$total*100).'%;border:1px solid white;margin-bottom:3px;font-size:9px;text-align:center;font-weight:bold;background-color:#'.$color_array[$count].'">'.round($value/$total*100,2).'%</div>';
              $count++;
              if ($count >= count($color_array)) $count = count($color_array)-1;
          }
        ?>
        </div>

         <div style="padding-right:5px;padding-left:5px;margin-bottom:20px;color:#888888;">&nbsp;&nbsp;* <?php _e('Number of times that appears each value. Percent in relation to the total of logs.','cp-referrer-and-conversions-tracking'); ?><br />&nbsp;&nbsp;&nbsp;&nbsp; <?php _e('Date range from','cp-referrer-and-conversions-tracking'); ?> <?php echo esc_html($date_start); ?> <?php _e('to','cp-referrer-and-conversions-tracking'); ?> <?php echo esc_html($date_end); ?>.</div>
        </div>

        <div style="clear:both"></div>
        </div>
	</div>
</div>



<div class="ahb-buttons-container">
	<input type="button" value="<?php _e('Print Stats','cp-referrer-and-conversions-tracking'); ?>" onclick="do_dexapp_print();" class="button button-primary" />
	<a href="<?php print esc_attr(admin_url('admin.php?page='.$this->menu_parameter));?>" class="ahb-return-link">&larr;<?php _e('Return to the main settings page','cp-referrer-and-conversions-tracking'); ?></a>
	<div class="clear"></div>
</div>

<script type="text/javascript">

 function do_dexapp_print()
 {
      w=window.open();
      w.document.write("<style>.cpnopr{display:none;};table{border:2px solid black;width:100%;}th{border-bottom:2px solid black;text-align:left}td{padding-left:10px;border-bottom:1px solid black;}</style>"+document.getElementById('dex_printable_contents').innerHTML);
      w.print();
      w.close();
 }

 var $j = jQuery.noConflict();
 $j(function() {
 	$j("#dfrom").datepicker({
                    dateFormat: 'yy-mm-dd'
                 });
 	$j("#dto").datepicker({
                    dateFormat: 'yy-mm-dd'
                 });
 });

</script>


<script type='text/javascript' src='<?php echo esc_js(plugins_url('js/excanvas.min.js', __FILE__)); ?>'></script>
<script type="text/javascript">
var $ = jQuery.noConflict();
$j(document).ready(function(){
		    /////////////////////////canvas//////////////////////////
		    $(window).on('load',function(){
                drawGraph($("#cardio1"), $("#cardiocontainer1"));
                drawGraph($("#cardio2"), $("#cardiocontainer2"));
                function drawGraph(canvas, canvasContainer)
                {
		            if( typeof(G_vmlCanvasManager) != 'undefined' ){ G_vmlCanvasManager.init(); G_vmlCanvasManager.initElement(canvas[0]); }
		            ctx = canvas[0].getContext("2d");
		            var data = jQuery.parseJSON(canvas.attr("questions"));
		            var height = canvas.attr("height");
		            var width = canvas.attr("width");
		            var maxquestions = 0,maxpos = 0,minpos = 0,interval = 5;

		            jQuery.each(data,function(index,v){
		                maxquestions = (maxquestions<v.values.length)?v.values.length:maxquestions;
		                postmp = 0;
		                jQuery.each(v.values,function(index1,v1){
		                    maxpos = (maxpos<v1)?v1:maxpos;
		                    minpos = (minpos>v1)?v1:minpos;
		                });

		            });
		            maxpos = maxpos;//Math.ceil(maxpos/interval)*interval;
		            minpos = 0; //Math.floor(minpos/interval)*interval;
		            interval = Math.ceil(maxpos / 10);
		            total = maxpos - minpos + interval;
		            h = Math.round(height/total);
		            var start = 10;
		            var radius = 2;
		            if (maxquestions>1)
		                w = Math.round((width-start-radius)/(maxquestions-1));
		            else
		                w =  width/2;

		            if(ctx)
		            {
		                for (i=0;i<total/interval;i++)
		                {
		                    if ((maxpos-i*interval) >= 0) canvasContainer.append('<div class="legend" style="position:absolute;left:-10px;top:'+(parseInt((i*interval+interval/2)*h-5))+'px">'+(maxpos-i*interval)+'</div>');
		                    ctx.beginPath();
                            ctx.moveTo(start,Math.round((i*interval+interval/2)*h) );
                            ctx.lineTo(width,Math.round((i*interval+interval/2)*h) );
							ctx.lineWidth=1;
                            ctx.strokeStyle='#d0d0d0';
                            ctx.stroke();
		                }
		                jQuery.each(data,function(index,v){
		                    ctx.beginPath();
		                    ctx.strokeStyle = v.color;
		                    ctx.fillStyle = v.color;

		                    //ctx.moveTo(start,Math.round((maxpos+interval/2)*h) );
		                    var i = 0,j = 0;
		                    jQuery.each(v.values,function(index1,v1){
		                        j=-v1;
		                        if (i!=0)
		                            ctx.lineTo(i*w+start,Math.round((maxpos+interval/2)*h+j*h));
		                        else
		                            ctx.moveTo(i*w+start,Math.round((maxpos+interval/2)*h+j*h));
		                        i++;
		                     });

		                     ctx.stroke();
		                     var i = 0,j = 0;
		                     jQuery.each(v.values,function(index1,v1){
		                         j=-v1;
		                         ctx.beginPath();
		                         ctx.arc(i*w+start,Math.round((maxpos+interval/2)*h+j*h), radius, 0, 2 * Math.PI, true);
		                         ctx.fill();
		                         i++;
		                     });
		                });
		            }
		        }
            });

		    ////////////////////////end canvas///////////////////////
});
</script>










