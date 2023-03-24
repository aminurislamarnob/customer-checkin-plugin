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
	Activate::activate();
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
}
add_action( 'admin_notices', 'woocusch_gd_extension_admin_notice__error' );