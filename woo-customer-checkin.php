<?php
/*
Plugin Name: Woo Customer Checkin
Plugin URI: https://aiarnob.com/
Description: Sell service and customer physical location checkin plugin for WooCommerce.
Version: 1.0.0
Author: Aminur Islam Arnob
Author URI: https://aiarnob.com/
License: GPLv2 or later
Text Domain: woo-customer-checkin
*/


// If this file is called firectly, abort!!!
defined( 'ABSPATH' ) or die( 'Hey, what are you doing here?' );

// Require once the Composer Autoload
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

// Define CONSTANTS
define( 'PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PLUGIN', plugin_basename( __FILE__ ) );

use IncPath\Base\Activate;
use IncPath\Base\Deactivate;
use IncPath\PluginInit;


/**
 * The code that runs during plugin activation
 */
function activate_woo_customer_checkin() {
	// Activate::activate();
    wp_schedule_event( time(), 'daily', 'woo_customer_certificate_validity_event_daily'); //Run daily
    wp_schedule_event( time(), 'daily_after_1hours', 'woo_customer_certificate_validity_event_daily_after_1hour'); //Run daily+2 hours
    wp_schedule_event( time(), 'daily_after_2hours', 'woo_customer_certificate_validity_event_daily_after_2hours'); //Run daily+2 hours
    wp_schedule_event( time(), 'daily_after_4hours', 'woo_customer_certificate_validity_event_daily_after_4hours'); //Run daily+4 hours
}

/**
 * Run daily cron event to check certificate validity
 */
add_action( 'woo_customer_certificate_validity_event_daily', 'woo_customer_certificate_validity_daily' );
function woo_customer_certificate_validity_daily(){
    $post_args = array(
        'post_type'      => 'shop_order',
        'post_status'       =>  array_keys( wc_get_order_statuses() ),
        'posts_per_page' => -1,
    );
    $query = new WP_Query( $post_args );

    if ( $query->have_posts() ) :
        while ( $query->have_posts() ) : $query->the_post();
            $order = new \WC_Order( get_the_ID() );
            for($i=1; $i<=$order->get_item_count(); $i++){
                $timestamp = get_post_meta($order->get_id(), 'woocusch_customer_checkin_' . $i, true );
                if(!empty($timestamp)){
                    $customer_email = get_post_meta($order->get_id(), 'woocusch_customer_email_' . $i, true );
                    $customer_name = get_post_meta($order->get_id(), 'woocusch_customer_name_' . $i, true );
                    $customer_customer_certificate_expired_on = get_post_meta($order->get_id(), 'woocusch_customer_certificate_expire_date_' . $i, true );
                    $admin_email = get_option( 'admin_email' );
                    $order_id = $order->get_id();
                    $order_url = wc_get_endpoint_url( 'view-order', $order_id, wc_get_page_permalink( 'myaccount' ) );


                    // Define the future date
                    $certificate_expired = new DateTime($customer_customer_certificate_expired_on);

                    // Get the current date
                    $current_date = new DateTime();

                    // Calculate the difference between the two dates in days
                    $diff = $current_date->diff($certificate_expired);
                    $total_days = $diff->format('%a');
                    
                    //generate coupon name
                    $coupon_name = generate_coupon_name($customer_name, $order_id);
                    $coupon_percent = get_option('woocusch_customer_checking_coupon_percentace_30');

                    if($total_days == 30){
                        //sent mail before 30 days

                        //create coupon
                        $coupon = create_certificate_purchase_coupon($coupon_name, $coupon_percent, $customer_customer_certificate_expired_on, $customer_email);

                        //mail functions
                        require PLUGIN_PATH . 'templates/certificate-expire-alert-email.php';
                    }
                }

            }
        endwhile;
    endif;

    wp_reset_postdata();
}

/**
 * Run daily after 1 hour cron event to check certificate validity
 */
add_action( 'woo_customer_certificate_validity_event_daily_after_1hour', 'woo_customer_certificate_validity_daily_after_1_hour' );
function woo_customer_certificate_validity_daily_after_1_hour(){
    $post_args = array(
        'post_type'      => 'shop_order',
        'post_status'       =>  array_keys( wc_get_order_statuses() ),
        'posts_per_page' => -1,
    );
    $query = new WP_Query( $post_args );

    if ( $query->have_posts() ) :
        while ( $query->have_posts() ) : $query->the_post();
            $order = new \WC_Order( get_the_ID() );
            for($i=1; $i<=$order->get_item_count(); $i++){
                $timestamp = get_post_meta($order->get_id(), 'woocusch_customer_checkin_' . $i, true );
                if(!empty($timestamp)){
                    $customer_email = get_post_meta($order->get_id(), 'woocusch_customer_email_' . $i, true );
                    $customer_name = get_post_meta($order->get_id(), 'woocusch_customer_name_' . $i, true );
                    $customer_customer_certificate_expired_on = get_post_meta($order->get_id(), 'woocusch_customer_certificate_expire_date_' . $i, true );
                    $admin_email = get_option( 'admin_email' );
                    $order_id = $order->get_id();
                    $order_url = wc_get_endpoint_url( 'view-order', $order_id, wc_get_page_permalink( 'myaccount' ) );


                    // Define the future date
                    $certificate_expired = new DateTime($customer_customer_certificate_expired_on);

                    // Get the current date
                    $current_date = new DateTime();

                    // Calculate the difference between the two dates in days
                    $diff = $current_date->diff($certificate_expired);
                    $total_days = $diff->format('%a');
                    
                    //generate coupon name
                    $coupon_name = generate_coupon_name($customer_name, $order_id);
                    $coupon_percent = get_option('woocusch_customer_checking_coupon_percentace_15');

                    if($total_days == 15){
                        //sent mail before 15 days

                        //create coupon
                        $coupon = create_certificate_purchase_coupon($coupon_name, $coupon_percent, $customer_customer_certificate_expired_on, $customer_email);

                        //mail functions
                        require PLUGIN_PATH . 'templates/certificate-expire-alert-email.php';
                    }
                }

            }
        endwhile;
    endif;

    wp_reset_postdata();
}

/**
 * Run daily +2 hours cron event to check certificate validity
 */
add_action( 'woo_customer_certificate_validity_event_daily_after_2hours', 'woo_customer_certificate_validity_after_daily_plus_2hours' );
function woo_customer_certificate_validity_after_daily_plus_2hours(){
    $post_args = array(
        'post_type'      => 'shop_order',
        'post_status'       =>  array_keys( wc_get_order_statuses() ),
        'posts_per_page' => -1,
    );
    $query = new WP_Query( $post_args );

    if ( $query->have_posts() ) :
        while ( $query->have_posts() ) : $query->the_post();
            $order = new \WC_Order( get_the_ID() );
            for($i=1; $i<=$order->get_item_count(); $i++){
                $timestamp = get_post_meta($order->get_id(), 'woocusch_customer_checkin_' . $i, true );
                if(!empty($timestamp)){
                    $customer_email = get_post_meta($order->get_id(), 'woocusch_customer_email_' . $i, true );
                    $customer_name = get_post_meta($order->get_id(), 'woocusch_customer_name_' . $i, true );
                    $customer_customer_certificate_expired_on = get_post_meta($order->get_id(), 'woocusch_customer_certificate_expire_date_' . $i, true );
                    $admin_email = get_option( 'admin_email' );
                    $order_id = $order->get_id();
                    $order_url = wc_get_endpoint_url( 'view-order', $order_id, wc_get_page_permalink( 'myaccount' ) );


                    // Define the future date
                    $certificate_expired = new DateTime($customer_customer_certificate_expired_on);

                    // Get the current date
                    $current_date = new DateTime();

                    // Calculate the difference between the two dates in days
                    $diff = $current_date->diff($certificate_expired);
                    $total_days = $diff->format('%a');
                    
                    //generate coupon name
                    $coupon_name = generate_coupon_name($customer_name, $order_id);
                    $coupon_percent = get_option('woocusch_customer_checking_coupon_percentace_7');

                    if($total_days == 7){
                        //sent mail before 7 days

                        //create coupon
                        $coupon = create_certificate_purchase_coupon($coupon_name, $coupon_percent, $customer_customer_certificate_expired_on, $customer_email);

                        //mail functions
                        require PLUGIN_PATH . 'templates/certificate-expire-alert-email.php';
                    }
                }

            }
        endwhile;
    endif;

    wp_reset_postdata();
}


/**
 * Run daily +4 hours cron event to check certificate validity
 */
add_action( 'woo_customer_certificate_validity_event_daily_after_4hours', 'woo_customer_certificate_validity_after_daily_plus_4hours' );
function woo_customer_certificate_validity_after_daily_plus_4hours(){
    $post_args = array(
        'post_type'      => 'shop_order',
        'post_status'       =>  array_keys( wc_get_order_statuses() ),
        'posts_per_page' => -1,
    );
    $query = new WP_Query( $post_args );

    if ( $query->have_posts() ) :
        while ( $query->have_posts() ) : $query->the_post();
            $order = new \WC_Order( get_the_ID() );
            for($i=1; $i<=$order->get_item_count(); $i++){
                $timestamp = get_post_meta($order->get_id(), 'woocusch_customer_checkin_' . $i, true );
                if(!empty($timestamp)){
                    $customer_email = get_post_meta($order->get_id(), 'woocusch_customer_email_' . $i, true );
                    $customer_name = get_post_meta($order->get_id(), 'woocusch_customer_name_' . $i, true );
                    $customer_customer_certificate_expired_on = get_post_meta($order->get_id(), 'woocusch_customer_certificate_expire_date_' . $i, true );
                    $admin_email = get_option( 'admin_email' );
                    $order_id = $order->get_id();
                    $order_url = wc_get_endpoint_url( 'view-order', $order_id, wc_get_page_permalink( 'myaccount' ) );


                    // Define the future date
                    $certificate_expired = new DateTime($customer_customer_certificate_expired_on);

                    // Get the current date
                    $current_date = new DateTime();

                    // Calculate the difference between the two dates in days
                    $diff = $current_date->diff($certificate_expired);
                    $total_days = $diff->format('%a');
                    
                    //generate coupon name
                    $coupon_name = generate_coupon_name($customer_name, $order_id);
                    $coupon_percent = get_option('woocusch_customer_checking_coupon_percentace_1');

                    if($total_days == 1){
                        //sent mail before 1 day

                        //create coupon
                        $coupon = create_certificate_purchase_coupon($coupon_name, $coupon_percent, $customer_customer_certificate_expired_on, $customer_email);

                        //mail functions
                        require PLUGIN_PATH . 'templates/certificate-expire-alert-email.php';
                    }
                }

            }
        endwhile;
    endif;

    wp_reset_postdata();
}


/**
 * Create a Coupon Programmatically
 */
function create_certificate_purchase_coupon($coupon_name, $coupon_percent, $expire_date, $customer_mail){
    $coupon = new WC_Coupon($coupon_name);
    if ($coupon->get_id() == 0) { // if coupon not exists
        $coupon = new WC_Coupon();
        $coupon->set_code( $coupon_name );
        $coupon->set_discount_type( 'percent' );
        $coupon->set_amount( $coupon_percent );
        $coupon->set_date_expires( $expire_date );
        $coupon->set_email_restrictions( 
            array($customer_mail)
        );
        $coupon->save();
    }else{
        $coupon->set_amount($coupon_percent);
        $coupon->save();
    }
    return $coupon;
}


function generate_coupon_name($customer_name, $order_id){
    $name_without_space = preg_replace('/\s+/', '_', strtolower($customer_name));
    return $name_without_space . '_' . $order_id;
}
    


/**
 * The code that runs during plugin deactivation
 */
function deactivate_woo_customer_checkin() {
	Deactivate::deactivate();
}

register_activation_hook( __FILE__, 'activate_woo_customer_checkin' );
register_deactivation_hook( __FILE__, 'deactivate_woo_customer_checkin' );

/**
 * Initialize all the core classes of the plugin
 */
if ( class_exists( 'IncPath\\PluginInit' ) ) {
	PluginInit::register_services();
}




/**
 * User Checkin ajax implementations
 */
add_action("wp_ajax_woocusch_checkin", "woocusch_customer_checkin");
function woocusch_customer_checkin() {

    if ( !wp_verify_nonce( $_REQUEST['nonce'], "woocusch_checkin_nonce")) {
        exit("This is not your area!");
    }

    $customer_email = $_REQUEST['customer_email'];
    $customer_name = $_REQUEST['customer_name'];
    $order_id = $_REQUEST['order_id'];
    $meta_counter = $_REQUEST['count'];
    
    $admin_email = get_option( 'admin_email' );
    $order_url = wc_get_endpoint_url( 'view-order', $_POST['order_id'], wc_get_page_permalink( 'myaccount' ) );


    //create unique image name for customer
    $name_without_space = preg_replace('/\s+/', '_', strtolower($customer_name));
    $certificate_name = 'Red-White-Professional-Certificate-Of-Appreciation' . $meta_counter . $order_id . '_' . $customer_email . '_' . $name_without_space;

    //pdf generate
    //Write Text on Image
    $text = $customer_name;
    $date = new DateTime();
    $formated_date = $date->format('Y-m-d');
    $today_date = $formated_date;
    $image = imagecreatefromjpeg(PLUGIN_PATH . '/assets/admin/img/Red-White-Professional-Certificate-Of-Appreciation.jpg');
    $textColor = imagecolorallocate($image, 182,45,26);
    $fontPath = PLUGIN_PATH . '/assets/admin/fonts/Carattere-Regular.ttf';
    imagettftext($image, 55, 0, 565, 985, $textColor, $fontPath, $text);
    imagettftext($image, 55, 0, 485, 1470, $textColor, $fontPath, $today_date);
    header('Content-type: image/jpeg');
    imagejpeg($image, PLUGIN_PATH . "/certificates/" . $certificate_name . ".jpeg");
    imagedestroy($image);



    $mpdf = new \Mpdf\Mpdf([
        'orientation' => 'L'
    ]);
    $mpdf->WriteHTML('<h1>Red-White-Professional-Certificate-Of-Appreciation</h1>');
    $mpdf->Image(PLUGIN_PATH . '/certificates/' . $certificate_name . '.jpeg', 0, 0, 297, 0, 'jpg', '', true, false);

    //save as pdf
    $mpdf->Output(PLUGIN_PATH . '/certificates/' . $certificate_name . '.pdf', \Mpdf\Output\Destination::FILE);

    //delete image certificate
    unlink(PLUGIN_PATH . "/certificates/" . $certificate_name . ".jpeg");

    //save checkin date.
    $now = new DateTime();
    update_post_meta($order_id, 'woocusch_customer_checkin_' . $meta_counter, $now->format('Y-m-d H:i:s'));

    //save expire date
    $expire_date = new DateTime('+1 year -1 day'); //1 YEAR
    update_post_meta($order_id, 'woocusch_customer_certificate_expire_date_' . $meta_counter, $expire_date->format('Y-m-d H:i:s')); //save expire date


    //mail functions
    require_once PLUGIN_PATH . 'templates/check-in-email.php';
    die();
}


/**
 * GD extension enable admin notice[Error]
 */
function woocusch_gd_extension_admin_notice__error() {
    if (!extension_loaded('gd')) {
        $class = 'notice notice-error';
        $message = __( 'GD support is not enabled for "Woo Customer Checkin" plugin. To enable GD extension on server please follow the link: https://www.hostingb2b.com/blog/how-to-ebable-the-gd-extension-of-php-using-cloudlinux-selector-in-cpanel/', 'woo-customer-checkin' );  printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
    }

    if( empty(get_option('woocusch_customer_checking_coupon_percentace_30')) || empty(get_option('woocusch_customer_checking_coupon_percentace_15')) || empty(get_option('woocusch_customer_checking_coupon_percentace_7')) || empty(get_option('woocusch_customer_checking_coupon_percentace_1')) ){
        $class = 'notice notice-error';
        $link = '<a href="'.get_admin_url().'admin.php?page=wc-settings&tab=products&section=woocusch_customer_checking_settings">'.get_admin_url().'admin.php?page=wc-settings&tab=products&section=woocusch_customer_checking_settings</a>';
        $message = __( 'Please enter coupon discount percentance. Go to the link: ', 'woo-customer-checkin' );  
        printf( '<div class="%1$s"><p>%2$s %3$s</p></div>', esc_attr( $class ), esc_html( $message ), $link );
    }
}
add_action( 'admin_notices', 'woocusch_gd_extension_admin_notice__error' );



/**
 * Custom Cron Schedules
 */
function woocusch_custom_cron_schedules( $schedules ) {
    $schedules['daily_after_1hours'] = array(
        'interval' => 90000,
        'display' => __('Once Daily After 1 Hour (Each 25 hours)')
    );
	$schedules['daily_after_2hours'] = array(
		'interval' => 93600,
		'display' => __('Once Daily After 2 Hours (Each 26 hours)')
	);
    $schedules['daily_after_4hours'] = array(
		'interval' => 100800,
		'display' => __('Once Daily After 4 Hours (Each 28 hours)')
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'woocusch_custom_cron_schedules' );





/**
 * Create the section beneath the products tab
 **/
add_filter( 'woocommerce_get_sections_products', 'woocusch_add_section' );
function woocusch_add_section( $sections ) {
	
	$sections['woocusch_customer_checking_settings'] = __( 'Woo Customer Checking Settings', 'woo-customer-checkin' );
	return $sections;
	
}

/**
 * Add settings to the specific section we created before
 */
add_filter( 'woocommerce_get_settings_products', 'woocusch_all_settings', 10, 2 );
function woocusch_all_settings( $settings, $current_section ) {
	/**
	 * Check the current section is what we want
	 **/
	if ( $current_section == 'woocusch_customer_checking_settings' ) {
		$settings_slider = array();
		// Add Title to the Settings
		$settings_slider[] = array( 'name' => __( 'Woo Customer Checking Settings', 'woo-customer-checkin' ), 'type' => 'title', 'desc' => __( 'The following options are used to configure Woo Customer Checking Plugin', 'woo-customer-checkin' ), 'id' => 'woocusch_customer_checking_title' );
		// Add first text field option
		$settings_slider[] = array(
			'name'     => __( 'Coupon discount before 30 days', 'woo-customer-checkin' ),
			'desc_tip' => __( 'Discount percentance before 30 days', 'woo-customer-checkin' ),
			'id'       => 'woocusch_customer_checking_coupon_percentace_30',
			'type'     => 'number',
		);
		$settings_slider[] = array(
			'name'     => __( 'Coupon discount before 15 days', 'woo-customer-checkin' ),
			'desc_tip' => __( 'Discount percentance before 15 days', 'woo-customer-checkin' ),
			'id'       => 'woocusch_customer_checking_coupon_percentace_15',
			'type'     => 'number',
		);
        $settings_slider[] = array(
			'name'     => __( 'Coupon discount before 07 days', 'woo-customer-checkin' ),
			'desc_tip' => __( 'Discount percentance before 07 days', 'woo-customer-checkin' ),
			'id'       => 'woocusch_customer_checking_coupon_percentace_7',
			'type'     => 'number',
		);
        $settings_slider[] = array(
			'name'     => __( 'Coupon discount before 01 days', 'woo-customer-checkin' ),
			'desc_tip' => __( 'Discount percentance before 01 days', 'woo-customer-checkin' ),
			'id'       => 'woocusch_customer_checking_coupon_percentace_1',
			'type'     => 'number',
		);
		
		$settings_slider[] = array( 'type' => 'sectionend', 'id' => 'wcslider' );
		return $settings_slider;
	
	/**
	 * If not, return the standard settings
	 **/
	} else {
		return $settings;
	}
}