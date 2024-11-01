<?php
/**
 * Sonawp Meta Boxes
 * Order single page meta boxes
 * @package sonawp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Adding meta boxes on sonaorder post type.
 */

add_action( 'add_meta_boxes', 'add_sonaorder_meta_boxes', 10, 2 );

function add_sonaorder_meta_boxes() {
	add_meta_box( 'sonaorder_meta_box_1', 'Order Details', 'sonaorder_meta_box_1_callback', 'sonaorder', 'normal', 'high' );
	add_meta_box( 'sonaorder_meta_box_2', 'Item Details', 'sonaorder_meta_box_2_callback', 'sonaorder', 'normal', 'high' );
}

/**
 * Displaying data in meta boxes of sonaorder post type.
 */

function sonaorder_meta_box_1_callback( $post ) {
	$post_id           = $post->ID;
	$post              = get_post( $post_id );
	$meta_values       = get_post_meta( $post_id );
	$sona_order_method = $meta_values['sona_order_method'][0];

	if ( 'stripe' === $sona_order_method ) {

		$sona_stripe_order = $meta_values['sona_stripe_order'][0];
		$sona_stripe_order = unserialize( $sona_stripe_order );

		$sona_mode                      = empty( $sona_stripe_order['livemode'] ) ? 'test' : 'live';
		$sona_order_intent              = $sona_stripe_order['payment_intent'];
		$sona_order_created_time        = DateTime::createFromFormat( 'U', $sona_stripe_order['created'] )->format( 'Y-m-d H:i:s' );
		$sona_order_status              = $sona_stripe_order['payment_status'];
		$sona_order_payment_method_type = $sona_stripe_order['payment_method_type'];
		$sona_order_payer_email_address = $sona_stripe_order['customer_email'];
		$sona_order_customer_name       = $sona_stripe_order['customer_name'];
		$sona_order_country_code        = $sona_stripe_order['customer_country'];

	} else {

		$sona_paypal_order = $meta_values['sona_paypal_order'][0];
		$sona_paypal_order = unserialize( $sona_paypal_order );

		$sona_order_id                       = $sona_paypal_order['id'];
		$sona_order_intent                   = $sona_paypal_order['intent'];
		$sona_order_status                   = $sona_paypal_order['status'];
		$sona_order_created_time             = $sona_paypal_order['create_time'];
		$sona_order_payer_email_address      = $sona_paypal_order['payer']['email_address'];
		$sona_order_payer_first_name         = $sona_paypal_order['payer']['name']['given_name'];
		$sona_order_payer_last_name          = $sona_paypal_order['payer']['name']['surname'];
		$sona_order_shipping_address         = $sona_paypal_order['purchase_units'][0]['shipping']['address']['address_line_1'];
		$order_shipping_address_admin_area_1 = $sona_paypal_order['purchase_units'][0]['shipping']['address']['admin_area_2'];
		$order_shipping_address_admin_area_2 = $sona_paypal_order['purchase_units'][0]['shipping']['address']['admin_area_1'];
		$sona_order_postal_code              = $sona_paypal_order['purchase_units'][0]['shipping']['address']['postal_code'];
		$sona_order_country_code             = $sona_paypal_order['purchase_units'][0]['shipping']['address']['country_code'];
		$sona_order_merchant_email           = $sona_paypal_order['purchase_units'][0]['payee']['email_address'];

	}

	echo empty( $sona_order_status ) ? null : '<div>
	<strong><big>Order Status: </big></strong>
	<span>' . esc_html( $sona_order_status ) . '</span>
	</div>';
	echo empty( $sona_mode ) ? null : '<div>
	<strong><big>Mode: </big></strong>
	<span>' . esc_html( $sona_mode ) . '</span>
	</div>';
	echo empty( $sona_order_method ) ? null : '<div>
	<strong><big>Method: </big></strong>
	<span>' . esc_html( $sona_order_method ) . '</span>
	</div>';
	echo empty( $sona_order_payment_method_type ) ? null : '<div>
	<strong><big>Payment Method Type: </big></strong>
	<span>' . esc_html( $sona_order_payment_method_type ) . '</span>
	</div>';
	echo empty( $sona_order_intent ) ? null : '<div>
	<strong><big>Payment Intent: </big></strong>
	<span>' . esc_html( $sona_order_intent ) . '</span>
	</div>';
	echo empty( $sona_order_id ) ? null : '<div>
	<strong><big>Order ID: </big></strong>
	<span>' . esc_html( $sona_order_id ) . '</span>
	</div>';
	echo empty( $sona_order_created_time ) ? null : '<div>
	<strong><big>Created Time: </big></strong>
	<span>' . esc_html( $sona_order_created_time ) . '</span>
	</div>';
	echo empty( $sona_order_customer_name ) ? null : '<div>
	<strong><big>Customer Name: </big></strong>
	<span>' . esc_html( $sona_order_customer_name ) . '</span>
	</div>';
	echo empty( $sona_order_payer_email_address ) ? null : '<div>
	<strong><big>Payer Email Address: </big></strong>
	<span>' . esc_html( $sona_order_payer_email_address ) . '</span>
	</div>';
	echo empty( $sona_order_payer_first_name ) ? null : '<div>
	<strong><big>Firstname: </big></strong>
	<span>' . esc_html( $sona_order_payer_first_name ) . '</span>
	</div>';
	echo empty( $sona_order_payer_last_name ) ? null : '<div>
	<strong><big>Lastname: </big></strong>
	<span>' . esc_html( $sona_order_payer_last_name ) . '</span>
	</div>';
	echo empty( $sona_order_shipping_address ) ? null : '<div>
	<strong><big>Address: </big></strong>
	<span>' . esc_html( $sona_order_shipping_address ) . '</span>
	</div>';
	echo empty( $order_shipping_address_admin_area_1 ) ? null : '<div>
	<strong><big>Address Area 1: </big></strong>
	<span>' . esc_html( $order_shipping_address_admin_area_1 ) . '</span>
	</div>';
	echo empty( $order_shipping_address_admin_area_2 ) ? null : '<div>
	<strong><big>Address Area 2: </big></strong>
	<span>' . esc_html( $order_shipping_address_admin_area_2 ) . '</span>
	</div>';
	echo empty( $sona_order_postal_code ) ? null : '<div>
	<strong><big>Postal Code: </big></strong>
	<span>' . esc_html( $sona_order_postal_code ) . '</span>
	</div>';
	echo empty( $sona_order_country_code ) ? null : '<div>
	<strong><big>Country Code: </big></strong>
	<span>' . esc_html( $sona_order_country_code ) . '</span>
	</div>';
	echo empty( $sona_order_merchant_email ) ? null : '<div>
	<strong><big>Merchent Email: </big></strong>
	<span>' . esc_html( $sona_order_merchant_email ) . '</span>
	</div>';
}

function sonaorder_meta_box_2_callback( $post ) {
	$post_id              = $post->ID;
	$post                 = get_post( $post_id );
	$meta_values          = get_post_meta( $post_id );
	$sona_order_method    = $meta_values['sona_order_method'][0];
	$post_title           = $meta_values['sona_order_post_name'][0];
	$sona_order_post_name = $post_title;

	if ( 'stripe' === $sona_order_method ) {

		$sona_stripe_order = $meta_values['sona_stripe_order'][0];
		$sona_stripe_order = unserialize( $sona_stripe_order );

		$sona_order_currency_code = $sona_stripe_order['currency'];

		$sona_order_amount = $sona_stripe_order['amount_total'];

	} else {

		$sona_paypal_order = $meta_values['sona_paypal_order'][0];

		$sona_paypal_order = unserialize( $sona_paypal_order );

		$sona_order_currency_code = $sona_paypal_order['purchase_units'][0]['amount']['currency_code'];
		$sona_order_amount        = $sona_paypal_order['purchase_units'][0]['amount']['value'];

	}

	echo '<div>
		<strong><big>Item Name: </big></strong>
		<span>' . esc_html( $sona_order_post_name ) . '</span>
		</div>';
	echo '<div>
		<strong><big>Currency Code: </big></strong>
		<span>' . esc_html( $sona_order_currency_code ) . '</span>
	</div>';
	echo '<div>
		<strong><big>Amount: </big></strong>
		<span>' . floatval( $sona_order_amount ) . '</span>
	</div>';
}

/**
 * Removing supports from sonaorder post type.
 */
add_action( 'init', 'remove_sonaorder_supports' );

function remove_sonaorder_supports() {
	//remove post type support of column title
	remove_post_type_support( 'sonaorder', 'title' );

	//remove post type support of column editor
	remove_post_type_support( 'sonaorder', 'editor' );

	//remove post type support of column date
	remove_post_type_support( 'sonaorder', 'date' );

	//remove post type support of column author
	remove_post_type_support( 'sonaorder', 'author' );

	//remove support of published date column
	remove_post_type_support( 'sonaorder', 'revisions' );

	//remove support of post publish date
	remove_post_type_support( 'sonaorder', 'comments' );

	// //remove support of add new post button
	remove_post_type_support( 'sonaorder', 'add-new' );
}

/**
 * Removing inline edit from sonaorder post type.
 */

add_filter(
	'post_row_actions',
	function ( $actions, $post ) {
		if ( 'sonaorder' === $post->post_type ) {
			unset( $actions['inline hide-if-no-js'] );
			unset( $actions['edit'] );

		}
		return $actions;
	},
	10,
	2
);

/**
 * Removing quick edit from sonaorder post type.
 */

add_action(
	'admin_menu',
	function () {
		remove_meta_box( 'submitdiv', 'sonaorder', 'side' );
	}
);

/**
 * Adding custom columns on sonaorder post type, and displaying data in those columns.
 * Columns are Payment Method, Order Status, Order Date, Order Amount, Order ID, Order Email, Order Address, Order Country, Order Mode.
 */

add_filter( 'manage_sonaorder_posts_columns', 'sonaorder_columns_head' );

/**
 * Adding custom columns on sonaorder post type.
 */
function sonaorder_columns_head( $defaults ) {

	$defaults['sona_order_id']       = 'Order ID';
	$defaults['sona_order_amount']   = 'Price';
	$defaults['sona_payment_method'] = 'Payment Method';
	$defaults['sona_order_status']   = 'Payment Status';
	$defaults['sona_order_date']     = 'Date';
	$defaults['title']               = 'Product Name';

	unset( $defaults['date'] );

	return $defaults;
}

add_action( 'manage_sonaorder_posts_custom_column', 'sonaorder_columns_content', 10, 2 );

/**
 * Displaying data in custom columns of sonaorder post type.
 */

function sonaorder_columns_content( $column, $post_id ) {
	$meta_values       = get_post_meta( $post_id );
	$sona_order_method = $meta_values['sona_order_method'][0];

	if ( 'stripe' === $sona_order_method ) {

		$sona_stripe_order = $meta_values['sona_stripe_order'][0];
		$sona_stripe_mode  = empty( $meta_values['sona_stripe_mode'][0] ) ? '' : $meta_values['sona_stripe_mode'][0];
		$sona_stripe_order = unserialize( $sona_stripe_order );

		$sona_order_id           = $sona_stripe_order['payment_intent'];
		$sona_order_created_time = DateTime::createFromFormat( 'U', $sona_stripe_order['created'] )->format( 'Y-m-d H:i:s' );
		$sona_order_status       = $sona_stripe_order['payment_status'];
		$sona_order_status       = str_replace( 'paid', 'COMPLETED', $sona_order_status );

		$sona_order_payment_method_type = 'Stripe (' . $sona_stripe_mode . ')';
		$sona_order_currency_code       = strtoupper( $sona_stripe_order['currency'] );

		$sona_price = $sona_order_currency_code . ' ' . $sona_stripe_order['amount_total'];

	} else {

		$sona_paypal_order = $meta_values['sona_paypal_order'][0];
		$sona_paypal_order = unserialize( $sona_paypal_order );

		$sona_order_id                  = $sona_paypal_order['id'];
		$sona_order_status              = $sona_paypal_order['status'];
		$sona_order_created_time        = $sona_paypal_order['create_time'];
		$sona_order_currency_code       = strtoupper( $sona_paypal_order['purchase_units'][0]['amount']['currency_code'] );
		$sona_order_amount              = $sona_paypal_order['purchase_units'][0]['amount']['value'];
		$sona_order_mode                = empty( $meta_values['sona_paypal_mode'][0] ) ? '' : $meta_values['sona_paypal_mode'][0];
		$sona_order_payment_method_type = 'PayPal (' . $sona_order_mode . ')';

		$sona_price = $sona_order_currency_code . ' ' . $sona_order_amount;

	}

	switch ( $column ) {
		case 'sona_order_id':
			echo empty( $sona_order_id ) ? null : esc_html( $sona_order_id );
			break;
		case 'sona_order_amount':
			echo empty( $sona_price ) ? null : esc_html( $sona_price );
			break;
		case 'sona_payment_method':
			echo empty( $sona_order_payment_method_type ) ? null : esc_html( $sona_order_payment_method_type );
			break;
		case 'sona_order_status':
			echo empty( $sona_order_status ) ? null : '
				<p class="button button-secondary">' . esc_html( $sona_order_status ) . '</p>';
			break;
		case 'sona_order_date':
			echo empty( $sona_order_created_time ) ? null : esc_html( gmdate( 'Y-m-d H:i:s', strtotime( $sona_order_created_time ) ) );
			break;
	}
}

/**
 * Removing bulk edit option from sonaorder post type.
 */

add_filter(
	'bulk_actions-edit-sonaorder',
	function ( $actions ) {
		unset( $actions['edit'] );
		return $actions;
	}
);

/**
 * Removing Published link which shows all publish post of sonaorder post type.
 * @param string $views
 * @since 1.2.3
 */

add_filter(
	'views_edit-sonaorder',
	function ( $views ) {
		unset( $views['publish'] );
		return $views;
	}
);

/**
 * Ajax request for Sending data for charts to js.
 * @since 1.2.3
 */

add_action( 'wp_ajax_order_chart', 'sona_ajax_order_chart' );
add_action( 'wp_ajax_nopriv_order_chart', 'sona_ajax_order_chart' );

function sona_ajax_order_chart() {
	// Get the currency from the AJAX request
	$currency = $_POST['currency'];
	// Define an array with different time frames
	$data = array(
		'last7'  => charts_price_and_currency( 'sonaorder', 'last7', $currency ),
		'last30' => charts_price_and_currency( 'sonaorder', 'last30', $currency ),
	);

	// Get the selected filter from the AJAX request
	$filter = sanitize_text_field( $_POST['filter'] );

	// Verify the nonce
	if ( ! isset( $_POST['sona_ajax_order_chart_nonce'] ) || ! wp_verify_nonce( $_POST['sona_ajax_order_chart_nonce'], 'sona_ajax_order_chart' ) ) {
		wp_send_json_error( 'Invalid nonce' );
	}

	// Check if the filter exists in the data array
	if ( array_key_exists( $filter, $data ) ) {
		$response = $data[ $filter ];
		wp_send_json_success( $response );
	} else {
		// Handle invalid or unsupported filters
		wp_send_json_error( 'Invalid filter' );
	}
}
