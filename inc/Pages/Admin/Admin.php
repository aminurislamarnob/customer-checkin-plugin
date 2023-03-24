<?php 
/**
 * @package  AlecadddPlugin
 */
namespace IncPath\Pages\Admin;

/**
* 
*/
class Admin
{
	public function register() {
		add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
	}

	public function add_admin_pages() {
		add_menu_page( 'Customer Check-In', 'Check-In', 'manage_options', 'woocusch_customer_checking', array( $this, 'woocusch_customer_checking' ), 'dashicons-location-alt', 50 );
	}

    public function woocusch_customer_checking() {
		require_once PLUGIN_PATH . 'templates/admin.php';
	}
}