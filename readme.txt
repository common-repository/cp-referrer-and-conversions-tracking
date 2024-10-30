=== CP Referrer and Conversion Tracking ===
Contributors: codepeople
Donate link: https://wordpress.dwbooster.com
Tags: referrer,conversion,referer,logs,stats
Requires at least: 4.0
Tested up to: 6.6
Stable tag: 1.01.23
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

CP Referrer and Conversion Tracking registers how the website visitors reached the website, identifying the referral website. Also track conversions.
 
== Description ==
 
CP Referrer and Conversion Tracking is a useful tool for evaluating the result of marketing campaigns. It records the referral website of each visitor and it can also be used to identify the referral of the conversions (purchases, contact form submissions, appointments, bookings).

The main features are:

* Records the referrer (HTTP referer) of each website visitor (tracking logs)

* Provides graphics / stats of referral websites and visitors (tracking stats)

* Allow to create different referral links for different marketing campaigns (referral sources)

* Records the original referrer and the latest referrer of the conversions

* Includes integration with multiple plugins for tracking its conversions


= Recording the referrer = 

The CP Referrer and Conversion Tracking plugin implements an efficient way to detect and store the referral website when the HTTP referer header is present(when the user clicks on a link pointing to a webpage). This automatically identify for example when the user comes from Google, Facebook or from other external website.

The visitors with identified referrers are listed under the "Tracking Logs" menu. The referrer, IP address and time of the first visit is recorded as part of the logs.

To avoid storing a large number of referrer logs, the old logs are deleted as default every 90 days. This number of days to delete old logs can be edited from the plugin settings.

= Graphics / stats of referral websites =

The recorded logs are used to render graphics indicating the evolution of logs received per day, the logs received per hour and the referral websites identified.

Stats are provided also for logs received per year, during the latest 12 months, during the latest 12 weeks and during the latest 30 days. This helps to evaluate evolution of referrers and visitors during different periods of time. Note: These stats may be impacted by the automatic deletion of old logs.

This section is located under the plugin menu "Tracking Stats".

= Creating different referral links for different marketing campaigns =

The purpose of this section is to create links for different marketing platforms, making easier to identify the referral. 

For example you can setup a different entry point for a Google Adwords campaign and for a Facebook Ads, this way the exact referrer source will be reported even if the automatic HTTP referer info is not sent.

This section is located under the plugin menu "Referral Sources".

= Tracking Conversions =

The CP Referrer and Conversion Tracking plugin can identify the referral of conversions, for example contact form submissions, purchases, bookings, appointment requests, etc...

The conversions are listed with the referrer of the initial visit and also with the referrer of the latest user session in the case the visitor used a different referrer for the latest sessions when the conversion happened.

To register the conversions the related add-on must be activated (add-ons are included in the plugin). Currently the plugin support several conversion add-ons for different plugins (contact forms, appointment requests, bookings, paypal payments) and we will be continuously working adding new integrations to identify conversions of third party plugins.

This section is located under the menu "Tracking Conversions".

= Tracking conversions originated in other plugins =

The "Add Ons" menu already includes several conversion add-ons for different plugins, for example to track conversions like the following:

 * Contact forms
 * Quote request forms
 * Appointment bookings
 * General bookings
 * Polls
 * Payment forms 
 
Includes conversion tracking for the following plugins:

 * [WooCommerce](https://wordpress.org/plugins/woocommerce/)
 * [Contact Form 7](https://wordpress.org/plugins/contact-form-7/)
 * [Contact Form to Email](https://wordpress.org/plugins/contact-form-to-email/)
 * [Appointment Hour Booking](https://wordpress.org/plugins/appointment-hour-booking/)
 * [Appointment Booking Calendar](https://wordpress.org/plugins/appointment-booking-calendar/)
 * [Booking Calendar Contact Form](https://wordpress.org/plugins/booking-calendar-contact-form/)
 * [Calculated Fields Form](https://wordpress.org/plugins/calculated-fields-form/)
 * [Contact Form with PayPal](https://wordpress.org/plugins/cp-contact-form-with-paypal/)
 * [CP Polls](https://wordpress.org/plugins/cp-polls/)
 * [WP Time Slots Booking Form](https://wordpress.org/plugins/wp-time-slots-booking-form/)

New add-ons will be added soon.

For developers: If you need to track a custom conversion writing the please see in the FAQ the supported hooks.

== Installation ==
 
 
To install **CP Referrer and Conversion Tracking**, follow these steps:

1.	Download and unzip the CP Referrer and Conversion Tracking plugin
2.	Upload the entire cp-referrer-and-conversions-tracking/ directory to the /wp-content/plugins/ directory
3.	Activate the CP Referrer and Conversion Tracking plugin through the Plugins menu in WordPress
4.	Configure the settings at the administration menu >> Settings >> CP Referrer Tracking
 
== Frequently Asked Questions ==
 
= I need a new plugin conversion integrated or a new feature =
 
Feel free to contact us in the support forum and we will point you to the related solution if already available or add your request to the plugin development list. 

= For developers: Which are hooks supported to register custom conversions =
 
For registering the conversion:

    do_action( 'cpreftrack_register_conversion', 'my_custom_conversion_identifier', 'My Conversion ID or Data' );

For customizing the info in the conversions list:

    add_filter( 'my_custom_conversion_identifier', 'my_custom_conversion_identifier_fn', 10, 1 );
    
    function my_custom_conversion_identifier_fn($text)
    {
        // modify the text as needed
        return $text;
    }
 
 
== Screenshots ==
 
1. Tracking logs with referrer
2. Stats section
3. Defining custom entry points for specific marketing platforms
4. Conversions
 
== Changelog ==

= 1.01.01 =
* First version published

= 1.01.03 =
* Conversion tracking for WooCommerce orders
* Conversion tracking for Contact Form 7 submissions
* Auto-detection added for Contact Form to Email
* Auto-detection added for Appointment Hour Booking
* Auto-detection added for Appointment Booking Calendar
* Auto-detection added for Booking Calendar Contact Form
* Auto-detection added for Calculated Fields Form
* Auto-detection added for Contact Form with PayPal
* Auto-detection added for CP Polls
* Auto-detection added for WP Time Slots Booking Form
  
= 1.01.04 =
* Compatible with WordPress 5.4

= 1.01.05 =
* Compatible with WordPress 5.5

= 1.01.06 =
* Compatible with WordPress 5.6
 
= 1.01.07 =
* Compatible with WordPress 5.7

= 1.01.08 =
* Compatible with WordPress 5.8
 
= 1.01.09 =
* Compatible with WordPress 5.9
 
= 1.01.10 =
* Fix to CSV export feature

= 1.01.11 =
* Added tracking of entry page
* Compatible with WordPress 6.0

= 1.01.12 =
* Added user registration tracking add-on
* Added tracking of entry page

= 1.01.14 =
* Contact Form 7 integration improvement

= 1.01.15 =
* Compatible with WordPress 6.1

= 1.01.16 =
* PHP 8 updates

= 1.01.17 =
* Compatible with WordPress 6.2

= 1.01.18 =
* Compatible with WordPress 6.3

= 1.01.19 =
* Code improvements

= 1.01.20 =
* Compatible with WordPress 6.4

= 1.01.21 =
* Tracking fixes

= 1.01.22 =
* Compatible with WordPress 6.5
* Removed tag: marketing

= 1.01.23 =
* Compatible with WordPress 6.6

== Upgrade Notice ==
 
= 1.01.23 =
* Compatible with WordPress 6.6

 