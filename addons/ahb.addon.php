<?php
/*
    iCal Import Addon
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'CPREFTRACK_AppointmentHourBooking' ) )
{

    class CPREFTRACK_AppointmentHourBooking extends CPREFTRACK_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-AppointmentHourBooking-20200119";
		protected $name = "Appointment Hour Booking Conversion Tracking";
		protected $description;
        protected $conversion_submission_indicator = 'Appointment Hour Booking - Form submission';
        protected $conversion_details_prefix = 'Submission ID #';


		/************************ ADDON CODE *****************************/

        /************************ ATTRIBUTES *****************************/


        /************************ CONSTRUCT *****************************/

        function __construct()
        {
			$this->description = __("The add-on enables referrer tracking for conversions in the <a style=\"font-weight:bold\" href=\"plugin-install.php?tab=plugin-information&amp;plugin=appointment-hour-booking&amp;\">Appointment Hour Booking</a> plugin", 'cp-referrer-and-conversions-tracking' );
            // Check if the plugin is active
            
            add_action( 'init', array( &$this, 'check_active' ), 10, 1 );

			if( !$this->addon_is_active() ) return;

			add_action( 'cpappb_process_data_before_insert', array( &$this, 'register_referrer' ), 10, 1);

            add_action( 'cpappb_process_data', array( &$this, 'track_conversion_submission' ), 10, 1 );

            add_filter( $this->conversion_submission_indicator, array( &$this, 'filter_conversion_list' ), 10, 1 );

            // filter en post


        } // End __construct



        /************************ PRIVATE METHODS *****************************/





		/************************ PUBLIC METHODS  *****************************/


        public function check_active()
        {
            global $cp_appb_plugin;
            if (is_object($cp_appb_plugin) && !$this->addon_is_active())
            {
                global $cpreftrack_addons_active_list;
                $cpreftrack_addons_active_list[] = $this->get_addon_id();
                update_option( 'cpreftrack_addons_active_list', $cpreftrack_addons_active_list );
            }
        }
        

        public function get_addon_settings()
        {
            global $cp_appb_plugin;
            if (!is_object($cp_appb_plugin))
            {
                if(!file_exists(dirname(__FILE__).'/../../appointment-hour-booking/app-booking-plugin.php'))
                {
?>
            <div class="card h-100" style="background-color: #ffdddd">
                <div class="mt-2"><strong><span style="color:red">Configuration issue:</span></strong> The <strong><?php echo $this->name; ?></strong> add-on has been enabled but the related plugin has not been detected. Please remember to install the related plugin to get listed its conversions.</div>

                <input class="button mt-2" type="button" name="calreport_<?php echo $item->id; ?>" value="<?php _e('Install Appointment Hour Booking plugin','cp-referrer-and-conversions-tracking'); ?>" onclick="document.location='plugin-install.php?tab=plugin-information&amp;plugin=appointment-hour-booking&amp;';" />
            </div>
<?php
                }
                else
                {
?>
            <div class="card h-100" style="background-color: #ffdddd">
                <div class="mt-2"><strong><span style="color:red">Configuration issue:</span></strong> The <strong><?php echo $this->name; ?></strong> add-on has been enabled but the related plugin is not active. Please remember to <a href="plugins.php">activate the related plugin</a> to get listed its conversions.</div>
            </div>
<?php
                }
            }
        }


		public function register_referrer(&$params)
		{
            $referrer = apply_filters( 'cpreftrack_referrer', '' );
            $params["referrer_url"] = $referrer;
            $params["referrer_url_latest"] = isset($_COOKIE['cprreftracklatest']) ? sanitize_text_field($_COOKIE['cprreftracklatest']) : '';
        }


		public function track_conversion_submission($params)
		{
            do_action( 'cpreftrack_register_conversion', $this->conversion_submission_indicator, $this->conversion_details_prefix.$params["itemnumber"]);
        }


		public function filter_conversion_list($param_registered)
		{
            global $wpdb, $cp_appb_plugin;
            if (!is_object($cp_appb_plugin))
                return $param_registered;
            $inum = intval(str_replace($this->conversion_details_prefix,'',$param_registered));
            $myrows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM ".$wpdb->prefix.$cp_appb_plugin->table_messages." WHERE id=%d",$inum) );
            if (count($myrows))
            {
                $param_registered = "<nobr>".$param_registered."</nobr><br /><b><span style=\"color:#FF9415\">Email: <a href=\"?page=cp_apphourbooking&cal=0&list=1&search=".urlencode($myrows[0]->notifyto)."&dfrom&dto&ds=Filter\">".sanitize_email($myrows[0]->notifyto)."</a></span></b>";
            }

            return $param_registered;
        }


    } // End Class

    // Main add-on code
    $CPREFTRACK_AppointmentHourBooking_obj = new CPREFTRACK_AppointmentHourBooking();

	// Add addon object to the objects list
	global $cpreftrack_addons_objs_list;
	$cpreftrack_addons_objs_list[ $CPREFTRACK_AppointmentHourBooking_obj->get_addon_id() ] = $CPREFTRACK_AppointmentHourBooking_obj;
}
