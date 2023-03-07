<?php
namespace IncPath\Base;

/**
* Add customer info on order details page
*/
class ViewCustomerInfo{
    public function register() {
        //Add customer info on order details page
		add_action( 'woocommerce_after_order_details', array( $this, 'woocusch_customer_informations_on_order' ) );
	}
	
	public function woocusch_customer_informations_on_order( $order ){
    	$order_id = $order->get_id();
    	?>
    		<h2>Customer Informations</h2>
    		<table class="woocommerce-table shop_table gift_info">
    		    <tbody>
    		        <tr>
    		            <th>#</th>
    		            <th>Name</th>
    		            <th>Email</th>
    		            <th>Status</th>
    		        </tr>
    		    </tbody>
    			<tbody>
    				<?php for($i=1; $i<=$order->get_item_count(); $i++): ?>
    					<tr>
    						<td><strong><?php echo $i; ?></strong></td>
					        <td><?php echo get_post_meta($order->get_id(), 'woocusch_customer_name_' . $i, true ); ?></td>
					        <td><?php echo get_post_meta($order->get_id(), 'woocusch_customer_email_' . $i, true ); ?></td>
					        <td>
        					    <?php 
        					    $is_set_wocusch_mail =  get_post_meta($order->get_id(), 'is_woocusch_mail_sent_' . $i, true );
        					    if($is_set_wocusch_mail == 1){
        					        echo "Successfully Checked-In";
        					    }else{
        					        echo "Not Checked-In Yet";
        					    }
        					    ?>
        					</td>
    					</tr>
    				<?php endfor; ?>
    			</tbody>
    		</table>
    	<?php
    }
}