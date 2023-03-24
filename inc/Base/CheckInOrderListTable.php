<?php
// namespace IncPath\Base;


/**
 * Adding WP List table class if it's not available.
 */
if ( ! class_exists( WP_List_Table::class ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class Drafts_List_Table.
 *
 * @since 0.1.0
 * @package Admin_Table_Tut
 * @see WP_List_Table
 */
class CheckInOrderListTable extends WP_List_Table {

	/**
	 * Const to declare number of posts to show per page in the table.
	 */
	const POSTS_PER_PAGE = 10;

	/**
	 * Property to store post types
	 *
	 * @var  array Array of post types
	 */
	private $allowed_post_types;

	/**
	 * Draft_List_Table constructor.
	 */
	public function __construct() {

		parent::__construct(
			array(
				'singular' => 'Draft',
				'plural'   => 'Drafts',
				'ajax'     => false,
			)
		);

		$this->allowed_post_types = 'shop_order';

	}

	/**
	 * Retrieve post types to be shown in the table.
	 *
	 * @return array Allowed post types in an array.
	 */
	// private function allowed_post_types() {
	// 	$post_types = get_post_types( array( 'public' => true ) );
	// 	unset( $post_types['attachment'] );

	// 	return $post_types;
	// }

	/**
	 * Convert slug string to human readable.
	 *
	 * @param string $title String to transform human readable.
	 *
	 * @return string Human readable of the input string.
	 */
	private function human_readable( $title ) {
		return ucwords( str_replace( '_', ' ', $title ) );
	}

	/**
	 * A map method return all allowed post types to human readable format.
	 *
	 * @return array Array of allowed post types in human readable format.
	 */
	private function allowed_post_types_readable() {
		return 'Orders';
	}

	/**
	 * Return instances post object.
	 *
	 * @return WP_Query Custom query object with passed arguments.
	 */
	protected function get_posts_object() {
		$post_types = $this->allowed_post_types;

		$post_args = array(
			'post_type'      => $post_types,
			// 'post_status'    => array( 'draft' ),
			'post_status'       =>  array_keys( wc_get_order_statuses() ),
			'posts_per_page' => self::POSTS_PER_PAGE,
		);

		$paged = filter_input( INPUT_GET, 'paged', FILTER_VALIDATE_INT );

		if ( $paged ) {
			$post_args['paged'] = $paged;
		}

		$post_type = filter_input( INPUT_GET, 'type', FILTER_SANITIZE_STRING );

		if ( $post_type ) {
			$post_args['post_type'] = $post_type;
		}

		$orderby = sanitize_sql_orderby( filter_input( INPUT_GET, 'orderby' ) );
		$order   = esc_sql( filter_input( INPUT_GET, 'order' ) );

		if ( empty( $orderby ) ) {
			$orderby = 'date';
		}

		if ( empty( $order ) ) {
			$order = 'DESC';
		}

		$post_args['orderby'] = $orderby;
		$post_args['order']   = $order;

		$search = esc_sql( filter_input( INPUT_GET, 's' ) );
		if ( ! empty( $search ) ) {
			$post_args['s'] = $search;
		}


		//custom search for customer by name/email
		$search_by = esc_sql( filter_input( INPUT_GET, 'search_by' ) );
		$search_text = esc_sql( filter_input( INPUT_GET, 'search_text' ) );
		if ( ! empty( $search_by ) && ! empty( $search_text ) ) {
			
			$post_args['meta_query'] = array(
				array(
					'value' => trim($search_text),
                    'compare' => 'LIKE',
				)
			);

		}


		return new WP_Query( $post_args );
	}

	/**
	 * Display text for when there are no items.
	 */
	public function no_items() {
		esc_html_e( 'No order found.', 'admin-table-tut' );
	}

	/**
	 * The Default columns
	 *
	 * @param  array  $item        The Item being displayed.
	 * @param  string $column_name The column we're currently in.
	 * @return string              The Content to display
	 */
	public function column_default( $item, $column_name ) {
		$result = '';
		switch ( $column_name ) {
			case 'date':
				$t_time    = get_the_time( 'Y/m/d g:i:s a', $item['id'] );
				$time      = get_post_timestamp( $item['id'] );
				$time_diff = time() - $time;

				if ( $time && $time_diff > 0 && $time_diff < DAY_IN_SECONDS ) {
					/* translators: %s: Human-readable time difference. */
					$h_time = sprintf( __( '%s ago', 'admin-table-tut' ), human_time_diff( $time ) );
				} else {
					$h_time = get_the_time( 'Y/m/d', $item['id'] );
				}

				$result = '<span title="' . $t_time . '">' . apply_filters( 'post_date_column_time', $h_time, $item['id'], 'date', 'list' ) . '</span>';
				break;

//			case 'author':
//				$result = $item['author'];
//				break;

			case 'type':
				$result = $item['type'];
				break;
		}

		return $result;
	}

	/**
	 * Get list columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'cb'     => '<input type="checkbox"/>',
			'title'  => __( 'Order', 'admin-table-tut' ),
			'customer'  => __( 'Customers', 'admin-table-tut' ),
			// 'type'   => __( 'Type', 'admin-table-tut' ),
//			'author' => __( 'Author', 'admin-table-tut' ),
			'date'   => __( 'Date', 'admin-table-tut' ),
		);
	}

	/**
	 * Return title column.
	 *
	 * @param  array $item Item data.
	 * @return string
	 */
	public function column_title( $item ) {
		$order = new \WC_Order( $item['id'] );
		$edit_url    = get_edit_post_link( $item['id'] );
		$post_link   = get_permalink( $item['id'] );
		$delete_link = get_delete_post_link( $item['id'] );
		
		//customer name
		$first_name = $order->get_billing_first_name();
		$last_name  = $order->get_billing_last_name();
		if(empty($first_name) && empty($last_name)){
			$first_name = $order->get_shipping_first_name();
			$last_name  = $order->get_shipping_last_name();
		}

		$output = '<strong>';

		/* translators: %s: Post Title */
		$output .= '<a class="row-title" href="' . esc_url( $edit_url ) . '" aria-label="' . sprintf( __( '%s (Edit)', 'admin-table-tut' ), $item['title'] ) . '">#' . esc_html( $item['id'] .'-'. $first_name .' '. $last_name) . '</a>';
		$output .= '</strong>';

		// Get actions.
		$actions = array(
			'edit'  => '<a href="' . esc_url( $edit_url ) . '">' . __( 'Edit', 'admin-table-tut' ) . '</a>',
			'trash' => '<a href="' . esc_url( $delete_link ) . '" class="submitdelete">' . __( 'Trash', 'admin-table-tut' ) . '</a>',
			'view'  => '<a href="' . esc_url( $post_link ) . '">' . __( 'View', 'admin-table-tut' ) . '</a>',
		);

		$row_actions = array();

		foreach ( $actions as $action => $link ) {
			$row_actions[] = '<span class="' . esc_attr( $action ) . '">' . $link . '</span>';
		}

		$output .= '<div class="row-actions">' . implode( ' | ', $row_actions ) . '</div>';

		return $output;
	}

	/**
	 * Column customer.
	 *
	 * @param  array $item Item data.
	 * @return string
	 */
	public function column_customer( $item ) {
		$order = new \WC_Order( $item['id'] );
		?>
		<table class="wp-list-table widefat fixed striped table-view-list">
			<thead>
				<tr>
					<td class="" width="25">#</td>
					<td>Name</td>
					<td>Email</td>
					<td>Action</td>
				</tr>
			</thead>
			<tbody>
			<?php
        	for($i=1; $i<=$order->get_item_count(); $i++){ ?>
				<tr>
					<td><strong><?php echo $i; ?></strong></td>
					<td><?php echo get_post_meta($order->get_id(), 'woocusch_customer_name_' . $i, true ); ?></td>
					<td><?php echo get_post_meta($order->get_id(), 'woocusch_customer_email_' . $i, true ); ?></td>
					<td>
					    <?php 
					    $is_set_wocusch_mail =  get_post_meta($order->get_id(), 'is_woocusch_mail_sent_' . $i, true );
					    if($is_set_wocusch_mail == 1){
					    ?>
					        <button class="button button-success button-small checked-btn" name="checkin" disabled>Already Checked-In</button>
                            <?php
                            $name_without_space = preg_replace('/\s+/', '_', strtolower(get_post_meta($order->get_id(), 'woocusch_customer_name_' . $i, true )));
                            $certificate_name = 'Red-White-Professional-Certificate-Of-Appreciation' . $i . $order->get_id() . '_' . get_post_meta($order->get_id(), 'woocusch_customer_email_' . $i, true ) . '_' . $name_without_space;
                            $certificate_full_path = PLUGIN_PATH . '/certificates/' . $certificate_name . '.pdf';
                            if (file_exists($certificate_full_path)) {
                            ?>
                                <a href="<?php echo PLUGIN_URL . '/certificates/' . $certificate_name . '.pdf'; ?>" class="woocusch-action-btn" title="Download certificate" target="_blank" download><span class="dashicons dashicons-pdf"></span></a>
                            <?php } ?>
                            <form class="customer-checkin-form" method="POST">
                                <input type="hidden" name="customer_email" value="<?php echo get_post_meta($order->get_id(), 'woocusch_customer_email_' . $i, true ); ?>">
                                <input type="hidden" name="customer_name" value="<?php echo get_post_meta($order->get_id(), 'woocusch_customer_name_' . $i, true ); ?>">
                                <input type="hidden" name="order_id" value="<?php echo $order->get_id(); ?>">
                                <input type="hidden" name="count" value="<?php echo $i; ?>">
                                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce("woocusch_checkin_nonce"); ?>">
                                <button type="submit" class="button button-primary button-small woocusch-action-btn" name="checkin" title="Resend mail"><span class="dashicons dashicons-redo"></span></button>
                            </form>
					    <?php 
					    }else{
					    ?>
						<form class="customer-checkin-form" method="POST">
							<input type="hidden" name="customer_email" value="<?php echo get_post_meta($order->get_id(), 'woocusch_customer_email_' . $i, true ); ?>">
							<input type="hidden" name="customer_name" value="<?php echo get_post_meta($order->get_id(), 'woocusch_customer_name_' . $i, true ); ?>">
							<input type="hidden" name="order_id" value="<?php echo $order->get_id(); ?>">
							<input type="hidden" name="count" value="<?php echo $i; ?>">
                            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce("woocusch_checkin_nonce"); ?>">
							<button type="submit" class="button button-primary button-small" name="checkin">Check-In</button>
						</form>
						<?php } ?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php
        // for($i=1; $i<=$order->get_item_count(); $i++){
        //     echo '<div class="woocusch-info form-field form-field-wide"><h3>' . __( '0'. $i .'. Customer Informations') . '</h3>';
        //     echo '<div><strong>'.__('Name').':</strong> ' . get_post_meta($order->get_id(), 'woocusch_customer_name_' . $i, true ) . '</div>';
        //     echo '<div><strong>'.__('Email').':</strong> ' . get_post_meta($order->get_id(), 'woocusch_customer_email_' . $i, true ) . '</div></div>';
        // }
	}


	/**
	 * Column cb.
	 *
	 * @param  array $item Item data.
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s_id[]" value="%2$s" />',
			esc_attr( $this->_args['singular'] ),
			esc_attr( $item['id'] )
		);
	}

	/**
	 * Prepare the data for the WP List Table
	 *
	 * @return void
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$sortable              = $this->get_sortable_columns();
		$hidden                = array();
		$primary               = 'title';
		$this->_column_headers = array( $columns, $hidden, $sortable, $primary );
		$data                  = array();

		// $this->process_bulk_action();

		$get_posts_obj = $this->get_posts_object();

		
		if ( $get_posts_obj->have_posts() ) {
			while ( $get_posts_obj->have_posts() ) {

				$get_posts_obj->the_post();

				$data[ get_the_ID() ] = array(
					'id'     => get_the_ID(),
					'title'  => get_the_title(),
					'type'   => ucwords( get_post_type_object( get_post_type() )->labels->singular_name ),
					'date'   => get_post_datetime(),
					'author' => get_the_author(),
				);
			}
			wp_reset_postdata();
		}

		$this->items = $data;

		$this->set_pagination_args(
			array(
				'total_items' => $get_posts_obj->found_posts,
				'per_page'    => $get_posts_obj->post_count,
				'total_pages' => $get_posts_obj->max_num_pages,
			)
		);
	}

	/**
	 * Get bulk actions.
	 *
	 * @return array
	 */
	// public function get_bulk_actions() {
	// 	return array(
	// 		'trash' => __( 'Move to Trash', 'admin-table-tut' ),
	// 	);
	// }

	/**
	 * Get bulk actions.
	 *
	 * @return void
	 */
	// public function process_bulk_action() {
	// 	if ( 'trash' === $this->current_action() ) {
	// 		$post_ids = filter_input( INPUT_GET, 'draft_id', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

	// 		if ( is_array( $post_ids ) ) {
	// 			$post_ids = array_map( 'intval', $post_ids );

	// 			if ( count( $post_ids ) ) {
	// 				array_map( 'wp_trash_post', $post_ids );
	// 			}
	// 		}
	// 	}
	// }

	/**
	 * Generates the table navigation above or below the table
	 *
	 * @param string $which Position of the navigation, either top or bottom.
	 *
	 * @return void
	 */
	protected function display_tablenav( $which ) {
		?>
	<div class="tablenav <?php echo esc_attr( $which ); ?>">

		<?php if ( $this->has_items() ) : ?>
		<div class="alignleft actions bulkactions">
			<?php $this->bulk_actions( $which ); ?>
		</div>
			<?php
		endif;
		// $this->extra_tablenav( $which );
		$this->pagination( $which );
		?>

		<br class="clear" />
	</div>
		<?php
	}

	/**
	 * Overriden method to add dropdown filters column type.
	 *
	 * @param string $which Position of the navigation, either top or bottom.
	 *
	 * @return void
	 */
	protected function extra_tablenav( $which ) {

		if ( 'top' === $which ) {
			$drafts_dropdown_arg = array(
				'options'   => array( '' => 'All', 'processing' => 'Processing' ),
				'container' => array(
					'class' => 'alignleft actions',
				),
				'label'     => array(
					'class'      => 'screen-reader-text',
					'inner_text' => __( 'Filter by Post Type', 'admin-table-tut' ),
				),
				'select'    => array(
					'name'     => 'type',
					'id'       => 'filter-by-type',
					'selected' => filter_input( INPUT_GET, 'type', FILTER_SANITIZE_STRING ),
				),
			);

			$this->html_dropdown( $drafts_dropdown_arg );

			submit_button( __( 'Filter', 'admin-table-tut' ), 'secondary', 'action', false );
		}
	}

	/**
	 * Navigation dropdown HTML generator
	 *
	 * @param array $args Argument array to generate dropdown.
	 *
	 * @return void
	 */
	private function html_dropdown( $args ) {
		?>

		<div class="<?php echo( esc_attr( $args['container']['class'] ) ); ?>">
			<label
				for="<?php echo( esc_attr( $args['select']['id'] ) ); ?>"
				class="<?php echo( esc_attr( $args['label']['class'] ) ); ?>">
			</label>
			<select
				name="<?php echo( esc_attr( $args['select']['name'] ) ); ?>"
				id="<?php echo( esc_attr( $args['select']['id'] ) ); ?>">
				<?php
				foreach ( $args['options'] as $id => $title ) {
					?>
					<option
					<?php if ( $args['select']['selected'] === $id ) { ?>
						selected="selected"
					<?php } ?>
					value="<?php echo( esc_attr( $id ) ); ?>">
					<?php echo esc_html( ucwords( $title ) ); ?>
					</option>
					<?php
				}
				?>
			</select>
		</div>

		<?php
	}

	/**
	 * Include the columns which can be sortable.
	 *
	 * @return Array $sortable_columns Return array of sortable columns.
	 */
	public function get_sortable_columns() {

		return array(
			'title'  => array( 'title', false ),
			'type'   => array( 'type', false ),
			'date'   => array( 'date', false ),
			'author' => array( 'author', false ),
		);
	}
}