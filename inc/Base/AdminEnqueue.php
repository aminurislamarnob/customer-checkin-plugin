<?php
namespace IncPath\Base;

/**
* Enqueue admin styles and scripts
*/
class AdminEnqueue
{
	public function register() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}
	
	function enqueue() {
		// enqueue all our scripts
		wp_enqueue_style( 'mypluginstyle', PLUGIN_URL . 'assets/admin/css/admin-style.css' );
		wp_enqueue_script( 'mypluginscript', PLUGIN_URL . 'assets/admin/js/admin-script.js' );
	}
}