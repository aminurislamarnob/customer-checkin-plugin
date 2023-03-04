<?php
namespace IncPath\Base;

/**
* Enqueue public/frontend styles and scripts
*/
class PublicEnqueue
{
	public function register() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	}
	
	function enqueue() {
		// enqueue all our scripts
		wp_enqueue_style( 'mypluginstyle', PLUGIN_URL . 'assets/public/css/public-style.css' );
		wp_enqueue_script( 'mypluginscript', PLUGIN_URL . 'assets/public/js/public-script.js' );
	}
}