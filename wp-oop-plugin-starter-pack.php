<?php
/*
Plugin Name: WP Plugin Starter Pack
Plugin URI: https://aiarnob.com/
Description: WordPress OOP Plugin Starter Pack
Version: 1.0.0
Author: Aminur Islam Arnob
Author URI: https://aiarnob.com/
License: GPLv2 or later
Text Domain: plugin-starter-pack
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
function activate_wp_starter_plugin() {
	Activate::activate();
}

/**
 * The code that runs during plugin deactivation
 */
function deactivate_wp_starter_plugin() {
	Deactivate::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_starter_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_wp_starter_plugin' );

/**
 * Initialize all the core classes of the plugin
 */
if ( class_exists( 'IncPath\\PluginInit' ) ) {
	PluginInit::register_services();
}