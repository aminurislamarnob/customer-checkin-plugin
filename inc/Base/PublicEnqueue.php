<?php
namespace IncPath\Base;

/**
* Enqueue public/frontend styles and scripts
*/
class PublicEnqueue
{
	public function register() {
		add_action( 'wp_enqueue_scripts', array( $this, 'woocusch_public_enqueue' ) );
	}
	
	function woocusch_public_enqueue() {
		// enqueue all our scripts
		wp_enqueue_style( 'woocusch_public_style', PLUGIN_URL . 'assets/public/css/woocusch-style.css' );
		// wp_enqueue_script( 'mypluginscript', PLUGIN_URL . 'assets/public/js/public-script.js' );
	}
}