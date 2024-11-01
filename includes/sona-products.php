<?php
/**
 * Sonawp products cpt
 * @package sonawp
 * contains: metabox, custom cpt columns, custom cpt post count, custom cpt query
 */


// Add the action hook to modify the query
function hide_sonaproduct_posts_in_admin( $query ) {
	// Check if we are in the admin dashboard and working with the main query
	if ( is_admin() && $query->is_main_query() ) {

		// Check if the current post type is 'sonaproduct'
		if ( $query->get( 'post_type' ) === 'sonaproduct' ) {

			// Add a meta query to filter posts where 'sona_blockid' does not exist
			$meta_query = array(
				array(
					'key'     => 'sona_blockid',
					'compare' => 'NOT EXISTS',
				),
			);

			$query->set( 'meta_query', $meta_query );
		}
	}
}

// Hook the function to the pre_get_posts action
add_action( 'pre_get_posts', 'hide_sonaproduct_posts_in_admin' );

// Add the action hook to modify the post count in the admin
function modify_sonaproduct_post_count_in_admin( $views ) {
	// Check if we are in the 'sonaproduct' post type
	if ( isset( $_REQUEST['post_type'] ) && 'sonaproduct' === $_REQUEST['post_type'] ) {

		// Add a meta query to get the count of posts where 'sona_blockid' does not exist
		$meta_query = array(
			'relation' => 'AND',
			array(
				'key'     => 'sona_blockid',
				'compare' => 'NOT EXISTS',
			),
		);

		// Build the query to get the count
		$count_query = new WP_Query(
			array(
				'post_type'  => 'sonaproduct',
				'meta_query' => $meta_query,
				'fields'     => 'ids', // Only fetch post IDs to improve performance
				'nopaging'   => true,  // Retrieve all posts without pagination
			)
		);

		// Get the count of posts
		$count = $count_query->post_count;

		// Update the 'All' link with the modified count
		$views['all'] = preg_replace( '/\(\d+\)/', "($count)", $views['all'] );

		// Update the 'Published' link with the modified count
		if ( isset( $views['publish'] ) ) {
			$views['publish'] = preg_replace( '/\(\d+\)/', "($count)", $views['publish'] );
		}
	}

	return $views;
}

// Hook the function to the views_edit-{$post_type} filter
add_filter( 'views_edit-sonaproduct', 'modify_sonaproduct_post_count_in_admin' );

//filter hook to remove quickedit in sonaproduct cpt in admin dashboard
add_filter( 'post_row_actions', 'sona_remove_quick_edit', 10, 2 );

function sona_remove_quick_edit( $actions ) {
	global $post;

	if ( 'sonaproduct' === $post->post_type ) {
		unset( $actions['inline hide-if-no-js'] );
	}

	return $actions;
}


// Save Meta Box values.
add_action( 'save_post_sonaproduct', 'sona_products_metabox_save' );

function sona_products_metabox_save( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( ! isset( $_POST['sona_products_metabox_nonce_field'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sona_products_metabox_nonce_field'] ) ), 'sona_products_metabox_nonce_action' ) ) {
		return;
	}

	$sonawp_general_settings = get_option( 'sonawp_general_settings' );
	if ( ! empty( $sonawp_general_settings ) ) {
		$sonawp_paypal_currency = $sonawp_general_settings['sonawp_paypal_currency'];
		$sonawp_stripe_currency = $sonawp_general_settings['sonawp_stripe_currency'];
	}

	$sonawp_paypal_settings      = get_option( 'sonawp_paypal_settings' );
	$sonawp_payment_confirmation = get_option( 'sonawp_payment_confirmation' );
	$sonawp_payment_failed_page  = $sonawp_payment_confirmation['sonawp_payment_failed_page'];
	$payment_failed_page_url     = get_permalink( $sonawp_payment_failed_page );

	$paypal_merchent_email = empty( $sonawp_paypal_settings['sonawp_paypal_email'] ) ? '' : $sonawp_paypal_settings['sonawp_paypal_email'];

	if ( isset( $_POST['sona_products_gallery_post'] ) ) {
		$images_meta = array();

		$images_id = sanitize_text_field( $_POST['sona_products_gallery_post'] ); //521,519,517,516

		$images_id = explode( ',', $images_id ); //521,519,517,516
		//save image id and url and alt in array
		foreach ( $images_id as $image_id ) {
			$image_id = absint( $image_id );
			if ( $image_id ) {
				$images_meta[] = array(
					'id'  => $image_id,
					'url' => wp_get_attachment_url( $image_id ),
					'alt' => get_post_meta( $image_id, '_wp_attachment_image_alt', true ),
				);
			}
		}

		update_post_meta( $post_id, 'sona_product_image_url', $images_meta );

	}

	if ( isset( $_POST['sona_payment_gateway'] ) ) {
		update_post_meta( $post_id, 'sona_payment_gateway', sanitize_text_field( $_POST['sona_payment_gateway'] ) );
	}

	if ( isset( $_POST['sona_payment_gateway'] ) && 'paypal' === $_POST['sona_payment_gateway'] ) {
		update_post_meta( $post_id, 'sona_payment_failed_page', $payment_failed_page_url );
		update_post_meta( $post_id, 'sona_paypal_button', 'paypal' );

		if ( isset( $_POST['sona_email'] ) && $_POST['sona_email'] ) {
			update_post_meta( $post_id, 'sona_email', sanitize_text_field( $_POST['sona_email'] ) );
		} else {
			update_post_meta( $post_id, 'sona_email', $paypal_merchent_email );
		}

		if ( isset( $_POST['sona_paylater_button'] ) && 'paylater' === $_POST['sona_paylater_button'] ) {
			update_post_meta( $post_id, 'sona_paylater_button', 'paylater' );
		} else {
			delete_post_meta( $post_id, 'sona_paylater_button' );
		}

		if ( isset( $_POST['sona_debit_credit_button'] ) && 'card' === $_POST['sona_debit_credit_button'] ) {
			update_post_meta( $post_id, 'sona_debit_credit_button', 'card' );
		} else {
			delete_post_meta( $post_id, 'sona_debit_credit_button' );
		}

		if ( isset( $_POST['sona_venmo_button'] ) && 'venmo' === $_POST['sona_venmo_button'] ) {
			update_post_meta( $post_id, 'sona_venmo_button', 'venmo' );
		} else {
			delete_post_meta( $post_id, 'sona_venmo_button' );
		}
	}

	if ( isset( $_POST['sona_price'] ) ) {
		update_post_meta( $post_id, 'sona_price', sanitize_text_field( $_POST['sona_price'] ) );
	}

	if ( isset( $_POST['sona_product_description'] ) ) {
		$post_content = sanitize_text_field( $_POST['sona_product_description'] );
		//update post meta
		update_post_meta( $post_id, 'sona_product_description', $post_content );

	}
}

function sona_products_metabox_cb_func( $post ) {

	$images = get_post_meta( $post->ID, 'sona_product_image_url', true );

	?>
	<div class="sona-products-metabox">
	<form method="post" enctype="multipart/form-data">
		<?php wp_nonce_field( 'sona_products_metabox_nonce_action', 'sona_products_metabox_nonce_field' ); ?>
	<table class="form-table">
		<tbody>

		<tr>
			<th>Payment Method</th>
			<td>
				<input type="radio" name="sona_payment_gateway" value="paypal" <?php checked( get_post_meta( $post->ID, 'sona_payment_gateway', true ), 'paypal' ); ?>> Paypal
				<input type="radio" name="sona_payment_gateway" value="stripe" <?php checked( get_post_meta( $post->ID, 'sona_payment_gateway', true ), 'stripe' ); ?>> Stripe
			</td>
		</tr>

		<?php
			$sonawp_general_settings = get_option( 'sonawp_general_settings' );
		if ( ! empty( $sonawp_general_settings ) ) {
			$sonawp_paypal_currency = $sonawp_general_settings['sonawp_paypal_currency'];
			$sonawp_stripe_currency = $sonawp_general_settings['sonawp_stripe_currency'];
			$change_currency_link   = esc_url( admin_url( 'admin.php?page=sonawp-settings' ) );
		} else {
			$change_currency_link   = esc_url( admin_url( 'admin.php?page=sonawp-settings' ) );
			$sonawp_paypal_currency = 'USD';
			$sonawp_stripe_currency = 'USD';
		}
		?>

		<tr class="if-paypal-checked" style="display: none;">
			<th>PayPal Currency</th>
			<td>
				<input type="text" name="sona_currency" value="<?php echo esc_attr( $sonawp_paypal_currency ); ?>" readonly />
				<a href="<?php echo esc_url( $change_currency_link ); ?>">Change Currency</a>
			</td>
		</tr>

		<tr class="if-stripe-checked" style="display: none;">
				<th>Stripe Currency</th>
				<td>
					<input type="text" name="sona_currency" value="<?php echo esc_attr( strtoupper( $sonawp_stripe_currency ) ); ?>" readonly />
					<a href="<?php echo esc_url( $change_currency_link ); ?>">Change Currency</a>
				</td>
		</tr>

		<tr class="if-paypal-checked" style="display: none;">
			<th>Paypal options</th>
			<td>
				<label><input type="checkbox" name="sona_paylater_button" value="paylater" <?php checked( get_post_meta( $post->ID, 'sona_paylater_button', true ), 'paylater' ); ?>> Pay Later</label>
				<label><input type="checkbox" name="sona_debit_credit_button" value="card" <?php checked( get_post_meta( $post->ID, 'sona_debit_credit_button', true ), 'card' ); ?>> Debit/Credit Card</label>
				<label><input type="checkbox" name="sona_venmo_button" value="venmo" <?php checked( get_post_meta( $post->ID, 'sona_venmo_button', true ), 'venmo' ); ?>> Venmo</label> 
			</td>
		</tr>

		<tr>
			<th>Price</th>
			<td>
				<input type="number" name="sona_price" value="<?php echo esc_attr( get_post_meta( $post->ID, 'sona_price', true ) ); ?>" />
				<p class="notice notice-info if-paypal-checked" style="display: none;">HUF, JPY, TWD does not support decimals. minimum amount is $1 US or equivalent in charge currency otherwise it will not work. <a target="_blank" href="https://developer.paypal.com/reference/currency-codes/">need more info?</a></p>
				<p class="notice notice-info if-stripe-checked" style="display: none;">The minimum amount is $0.50 US or equivalent in charge currency otherwise it will not work.</p>
			</td>
		</tr>

		<tr class="if-paypal-checked" style="display: none;">
			<th>PayPal Merchent Email</th>
			<td>
				<input type="email" name="sona_email" value="<?php echo esc_attr( get_post_meta( $post->ID, 'sona_email', true ) ); ?>" />
				<p class="notice notice-info if-paypal-checked" style="display: none;">This email will override global PayPal merchent email, as this is product level email. like each product has its own merchent. ( only for PayPal ). <a target="_blank" href="<?php echo esc_url( admin_url( 'admin.php?page=sonawp-settings' ) ); ?>">PayPal settings</a></p>

			</td>
		</tr>

		<tr>
			<th>Description</th>
			<td>
				<textarea name="sona_product_description" rows="5" cols="70"><?php echo esc_attr( get_post_meta( $post->ID, 'sona_product_description', true ) ); ?></textarea>
			</td>
		</tr>

		<tr>
			<th>Images</th>
			<td>
				<?php echo sona_products_gallery_post_function( 'sona_products_gallery_post', $images ); // phpcs:ignore ?>
				<p class="notice notice-info">You can select multiple images by pressing <span>"Ctrl + Mouse click"</span></p>
			</td>
		</tr>

		<tr>
			<th>Shortcode</th>
			<td class="d-flex">
				<p class="sonawp-product-shortcode" > <?php echo esc_html( '[sonawp product_id="' . $post->ID . '"]' ); ?> </p>
				<button type="button" class="copy-shortcode button button-primary">Copy</button>
				<span></span>
			</td>
		</tr>

		</tbody>
	</table>
	</form>
	</div>

	<?php
}

//add column in sonaproduct cpt copy shortcode
add_filter( 'manage_sonaproduct_posts_columns', 'sona_products_columns_head' );
function sona_products_columns_head( $defaults ) {
	$defaults['price']          = 'Price';
	$defaults['payment_method'] = 'Payment Method';
	$defaults['shortcode']      = 'Shortcode';
	//unset date column
	unset( $defaults['date'] );
	return $defaults;
}

//add column in sonaproduct cpt copy shortcode
add_action( 'manage_sonaproduct_posts_custom_column', 'sona_products_columns_content', 10, 2 );
function sona_products_columns_content( $column_name, $post_id ) {
	if ( 'shortcode' === $column_name ) {
		echo '<p class="sonawp-product-shortcode" >' . esc_html( '[sonawp product_id="' . $post_id . '"]' ) . '</p>';
		echo '<button type="button" class="copy-shortcode button button-primary">Copy</button>';
		echo '<span></span>';
	}
	if ( 'price' === $column_name ) {
		echo esc_html( get_post_meta( $post_id, 'sona_price', true ) );
	}
	if ( 'payment_method' === $column_name ) {
		echo esc_html( get_post_meta( $post_id, 'sona_payment_gateway', true ) );
	}
}

function sona_products_gallery_post_function( $name, $value ) {

	$image      = '">Add/Edit Images';
	$image_str  = '';
	$display    = 'none';
	$string_ids = array();

	if ( ! empty( $value ) && ! empty( $value[0]['id'] ) && ! empty( $value[0]['url'] ) ) {
		foreach ( $value as $values ) {
			if ( isset( $values['id'], $values['url'] ) ) {
				$image_str   .= '<li data-attachment-id=' . esc_attr( $values['id'] ) . '><a href="' . esc_url( $values['url'] ) . '" target="_blank"><img src="' . esc_url( $values['url'] ) . '" /></a><i class="dashicons dashicons-no delete-img"></i></li>';
				$string_ids[] = $values['id'];
			}
		}
	}

	$string_ids = implode( ',', $string_ids );

	if ( $image_str ) {
		$display = 'inline-block';
	}

	return '<div class="gallery-metabox-main"><ul>' . $image_str . '</ul><a href="#" class="gallery-metabox-upload-button button' . $image . '</a><input type="hidden" class="attachment-ids ' . $name . '" name="' . $name . '" id="' . $name . '" value="' . esc_attr( $string_ids ) . '" /><a href="#" class="gallery-metabox-remove-button button" style="display:inline-block;display:' . $display . '">Remove All</a></div>';
}

add_action( 'add_meta_boxes', 'sona_products_metabox' );
function sona_products_metabox() {
	add_meta_box( 'sona_products_metabox_id', 'Product Details', 'sona_products_metabox_cb_func', 'sonaproduct', 'normal', 'high' );
}
