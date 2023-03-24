<?php
// Require once the Composer Autoload

if ( file_exists( PLUGIN_PATH . 'inc/Base/CheckInOrderListTable.php' ) ) {
	require_once PLUGIN_PATH . 'inc/Base/CheckInOrderListTable.php';
}

//send checkin mail to customer
//if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkin']) && isset($_POST['customer_email']) && isset($_POST['count'])) {
//    $customer_email = $_POST['customer_email'];
//    $customer_name = $_POST['customer_name'];
//    $order_id = $_POST['order_id'];
//    $admin_email = get_option( 'admin_email' );
//    $order_url = wc_get_endpoint_url( 'view-order', $_POST['order_id'], wc_get_page_permalink( 'myaccount' ) );
//    $meta_counter = $_POST['count'];
//
//    require_once PLUGIN_PATH . 'templates/check-in-email.php';
//}


/**
 * This function is responsible for render the drafts table
 */
$woocusch_order_table = new CheckInOrderListTable();
?>
<div id="woocusch_loader" style="display: none;">
    <div class="sk-chase">
        <div class="sk-chase-dot"></div>
        <div class="sk-chase-dot"></div>
        <div class="sk-chase-dot"></div>
        <div class="sk-chase-dot"></div>
        <div class="sk-chase-dot"></div>
        <div class="sk-chase-dot"></div>
    </div>
</div>
<div class="wrap checkin-table">
    <h2><?php esc_html_e( 'Customers Informations by Orders', 'admin-table-tut' ); ?></h2>
    <form id="woocusch-orders-forms" method="get">
        <input type="hidden" name="page" value="woocusch_customer_checking" />

        <p class="search-box custom-customer-search">
	        <label class="screen-reader-text" for="search-search-input">Search:</label>
            <select class="woocusch_field" name="search_by" id="search-search-select">
                <option value="name" <?php echo isset($_REQUEST['search_by']) && ($_REQUEST['search_by'] == 'name') ? 'selected' : ''; ?>>Customer name</option>
                <option value="email" <?php echo isset($_REQUEST['search_by']) && ($_REQUEST['search_by'] == 'email') ? 'selected' : ''; ?>>Customer email</option>
            </select>
	        <input class="woocusch_field" type="search" id="search-search-input" placeholder="Search text..." name="search_text" value="<?php echo isset($_REQUEST['search_text']) ? $_REQUEST['search_text'] : ''; ?>">
		    <input type="submit" id="search-submit" class="button button-primary button-large" value="Search Customer">
        </p>
        </form>
        <?php
        $woocusch_order_table->prepare_items();
        // $woocusch_order_table->search_box( 'Search', 'search' );
        $woocusch_order_table->display();
        ?>
</div>