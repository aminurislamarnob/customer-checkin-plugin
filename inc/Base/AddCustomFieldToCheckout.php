<?php
namespace IncPath\Base;

/**
* Add custom field to woocommerce checkout page
*/
class AddCustomFieldToCheckout{

	public function register() {
        //Add custom field
		add_action( 'woocommerce_before_order_notes', array( $this, 'woocusch_customise_checkout_field' ) );
        
        //Add validation
        add_action('woocommerce_checkout_process', array( $this, 'woocusch_customise_checkout_field_process' ) );

        //Update checkout meta
        add_action('woocommerce_checkout_update_order_meta', array( $this, 'woocusch_customise_checkout_field_update_order_meta' ) );

        //Show meta data on admin order edit page
        add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'woocusch_show_on_edit_order_page' ) );

        //limit only one items to checkout
        add_filter( 'woocommerce_add_to_cart_validation',  array( $this, 'woocusch_one_item_to_checkout'), 10, 3 );
	}
	
	function woocusch_customise_checkout_field($checkout){
        global $woocommerce;
        $total_items = $woocommerce->cart->get_cart_contents_count();
        echo '<div id="woocusch_customise_checkout_field"><h3>' . __('Customer Informations') . '</h3>';
        for($i=1; $i<=$total_items; $i++){
            echo '<div class="woocusch-customer-checkout-extra-info"><h5>' . __( '0'. $i .'. Customer Informations') . '</h5>';
            //name field
            woocommerce_form_field('woocusch_customer_name_' . $i, array(
                'type' => 'text',
                'class' => array(
                    'woocusch-field form-row form-row-first'
                ) ,
                'label' => __('Customer Name') ,
                'placeholder' => __('') ,
                'required' => true,
            ) , $checkout->get_value('woocusch_customer_name_' . $i));
            
            //email field
            woocommerce_form_field('woocusch_customer_email_' . $i, array(
                'type' => 'text',
                'class' => array(
                    'woocusch-field form-row form-row-last'
                ) ,
                'label' => __('Customer Email') ,
                'placeholder' => __('') ,
                'required' => true,
            ) , $checkout->get_value('woocusch_customer_email_' . $i));
            echo '</div>';
        }
        echo '</div>';
    }

    function woocusch_customise_checkout_field_process(){
        global $woocommerce;
        $total_items = $woocommerce->cart->get_cart_contents_count();
        // if the field is set, if not then show an error message.
        for($i=1; $i<=$total_items; $i++){
            if (!$_POST['woocusch_customer_name_' . $i]) wc_add_notice(__('Please enter 0'.$i.' customer name.') , 'error');
            if (!$_POST['woocusch_customer_email_' . $i]) wc_add_notice(__('Please enter 0'.$i.' customer email.') , 'error');
        }
    }

    function woocusch_customise_checkout_field_update_order_meta($order_id){
        global $woocommerce;
        $total_items = $woocommerce->cart->get_cart_contents_count();

        for($i=1; $i<=$total_items; $i++){
            if (!empty($_POST['woocusch_customer_name_' . $i])) {
                update_post_meta($order_id, 'woocusch_customer_name_' . $i, sanitize_text_field($_POST['woocusch_customer_name_' . $i]));
            }
            if (!empty($_POST['woocusch_customer_email_' . $i])) {
                update_post_meta($order_id, 'woocusch_customer_email_' . $i, sanitize_text_field($_POST['woocusch_customer_email_' . $i]));
            }
        }
    }

    function woocusch_show_on_edit_order_page($order){
        global $post_id;
        $order = new \WC_Order( $post_id );

        for($i=1; $i<=$order->get_item_count(); $i++){
            echo '<div class="woocusch-info form-field form-field-wide"><h3>' . __( '0'. $i .'. Customer Informations') . '</h3>';
            echo '<div><strong>'.__('Name').':</strong> ' . get_post_meta($order->get_id(), 'woocusch_customer_name_' . $i, true ) . '</div>';
            echo '<div><strong>'.__('Email').':</strong> ' . get_post_meta($order->get_id(), 'woocusch_customer_email_' . $i, true ) . '</div></div>';
        }
    }

    // Checking and validating when products are added to cart
    function woocusch_one_item_to_checkout( $passed, $product_id, $quantity ) {
        $cart_items_count = count(WC()->cart->get_cart());
        if( $cart_items_count >= 1 ){
            // Set to false
            $passed = false;
            // Display a message
            wc_add_notice( __( "You can’t have more than 1 service in cart", "woocommerce" ), "error" );
        }
        return $passed;
    }
}