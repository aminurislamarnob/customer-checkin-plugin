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