<?php
namespace IncPath\Base;

/**
* Enqueue admin styles and scripts
*/
class AdminEnqueue
{
	public function register() {
		add_action( 'admin_enqueue_scripts', array( $this, 'woocusch_admin_enqueue' ) );
	}
	
	function woocusch_admin_enqueue() {
		// enqueue all our scripts
		wp_enqueue_style( 'woocusch_admin_style', PLUGIN_URL . 'assets/admin/css/admin-style.css' );
		wp_enqueue_style( 'sweetalert2_style', '//cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css' );

        //For button click to check ajax
        wp_enqueue_script( 'sweetalert2_script', '//cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js', ['jquery'], '1.0.0', true );
        wp_register_script( "woocusch_script", PLUGIN_URL.'assets/admin/js/admin-script.js', array('jquery') );
        wp_localize_script( 'woocusch_script', 'woocuschAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'PLUGIN_PATH' => PLUGIN_PATH));
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'woocusch_script' );
	}
}