<?php
/**
 * Sona Stripe Block Class
 *
 * @package sonawp
 */

if ( ! class_exists( 'Sona_Stripe_Block' ) ) {
	class Sona_Stripe_Block {

		public function __construct() {
			add_action( 'init', array( $this, 'sonawp_stripe_payment' ) );
		}

		public function sonawp_stripe_payment() {
			$stripe_connect = get_option( 'sonawp_stripe_settings' );
			if ( empty( $stripe_connect ) ) {
				return;
			}
			$stripe_mode = $stripe_connect['sonawp_stripe_mode'];

			if ( $stripe_mode == 'test' ) {
				$stripe_secret_key = $stripe_connect['sonawp_stripe_test_secret_key'];
				if ( empty( $stripe_secret_key ) ) {
					return;
				}
			}

			if ( $stripe_mode == 'live' ) {
				$stripe_secret_key = $stripe_connect['sonawp_stripe_live_secret_key'];
				if ( empty( $stripe_secret_key ) ) {
					return;
				}
			}

			require_once SONA_DIR . 'includes/vendor/autoload.php';
			\Stripe\Stripe::setApiKey( $stripe_secret_key );
			header( 'Content-Type: application/json' );

			if ( isset( $_POST['sona_stripe'] ) && isset( $_POST['product_id'] ) ) {

				if ( empty( $stripe_connect ) ) {
					return;
				}

				$sonawp_payment_confirmation = get_option( 'sonawp_payment_confirmation' );
				$success_page                = $sonawp_payment_confirmation['sonawp_payment_success_page'];
				$success_page_id             = get_post( $success_page );

				if ( $success_page_id ) {
					$success_page_url = get_permalink( $success_page_id->ID );

				} else {

					$sona_page = array(
						'post_title'   => 'Payment Success',
						'post_type'    => 'page',
						'post_status'  => 'publish',
						'post_content' => '[sona_payment_success]',
					);

					$sona_page_id = wp_insert_post( $sona_page );

					$success_page_url = get_permalink( $sona_page_id );

					// update option.
					$sonawp_payment_confirmation['sonawp_payment_success_page'] = $sona_page_id;
					update_option( 'sonawp_payment_confirmation', $sonawp_payment_confirmation );

				}

				$failure_page    = $sonawp_payment_confirmation['sonawp_payment_failed_page'];
				$failure_page_id = get_post( $failure_page );

				if ( $failure_page_id ) {
					$failure_page_url = $failure_page ? get_permalink( $failure_page_id->ID ) : '';

				} else {

					$sona_page = array(
						'post_title'  => 'Payment Failed',
						'post_type'   => 'page',
						'post_status' => 'publish',
					);

					$sona_page_id = wp_insert_post( $sona_page );

					$failure_page_url = get_permalink( $sona_page_id );

					// update option.
					$sonawp_payment_confirmation['sonawp_payment_failed_page'] = $sona_page_id;
					update_option( 'sonawp_payment_confirmation', $sonawp_payment_confirmation );

				}

				$post_id = $_POST['product_id'];

				$post_title = get_the_title( $post_id );

				$sonawp_general_settings  = get_option( 'sonawp_general_settings' );
				$sona_order_currency_code = isset( $sonawp_general_settings['sonawp_stripe_currency'] ) ? $sonawp_general_settings['sonawp_stripe_currency'] : 'usd';

				$sona_price = get_post_meta( $post_id, 'sona_price', true );

				$zero_decimal_currencies = array(
					'bif',
					'clp',
					'djf',
					'gnf',
					'jpy',
					'kmf',
					'krw',
					'mga',
					'pyg',
					'rwf',
					'vnd',
					'vuv',
					'xaf',
					'xof',
					'xpf',
				);

				if ( in_array( $sona_order_currency_code, $zero_decimal_currencies ) ) {
					$final_price = $sona_price;
				} else {
					$final_price = $sona_price * 100;
				}

				$sona_image = get_post_meta( $post_id, 'sona_product_image_url', true );

				$sona_image = ! empty( $sona_image ) ? $sona_image[0]['url'] : '';

				if ( $sona_image ) {
					$sona_image = array( $sona_image );
				} else {
					$sona_image = array();
				}

				try {

					$stripe = new \Stripe\StripeClient( $stripe_secret_key );

					$a = $stripe->products->create(
						array(
							'name'   => $post_title,
							'images' => $sona_image,
						)
					);

					$product_id = $a->id;

					$b = $stripe->prices->create(
						array(
							'product'     => $product_id,
							'unit_amount' => $final_price,
							'currency'    => $sona_order_currency_code,
						)
					);

					$price_id = $b->id;

					$checkout_session = \Stripe\Checkout\Session::create(
						array(
							'line_items'  => array(
								array(
									'price'    => $price_id,
									'quantity' => 1,
								),
							),
							'mode'        => 'payment',
							'success_url' => $success_page_url . '?session_id={CHECKOUT_SESSION_ID}&nonce=' . wp_create_nonce( 'sona_payment_success' ),
							'cancel_url'  => $failure_page_url,
						)
					);

					header( 'HTTP/1.1 303 See Other' );
					header( 'Location: ' . $checkout_session->url );
				} catch ( \Stripe\Exception\ApiErrorException $e ) {
					echo '<script>';
					if ( current_user_can( 'administrator' ) ) {
						echo 'document.addEventListener(
							"DOMContentLoaded",
							function () {
								document . querySelector( "#sona-' . esc_html( $post_id ) . '" ) . innerHTML = "' . $e->getMessage() . '";
							}
						);';
					} else {
						echo 'document.addEventListener(
							"DOMContentLoaded",
							function () {
								document . querySelector( "#sona-' . esc_html( $post_id ) . '" ) . innerHTML = "Something went wrong try again. or contact site admin.";
							}
						);';
					}
					echo '</script>';
				}
			}
		}

		/**
		 * Define block attributes
		 *
		 * @return mixed|void
		 */
		public function block_attributes() {
			$sonawp_general_settings = get_option( 'sonawp_general_settings' );
			$sonawp_stripe_currency  = isset( $sonawp_general_settings['sonawp_stripe_currency'] ) ? $sonawp_general_settings['sonawp_stripe_currency'] : 'usd';
			return apply_filters(
				'sona_stripe_block_attributes',
				array(

					'icon'            => 'money-alt',
					'textdomain'      => 'sonawp',
					'description'     => __( 'Simply pay your product with block using Stripe payment gateway.', 'sonawp' ),
					'attributes'      => array(
						'blockID'     => array(
							'type' => 'string',
						),
						'title'       => array(
							'type' => 'string',
						),
						'description' => array(
							'type' => 'string',
						),
						'currency'    => array(
							'type'    => 'string',
							'default' => $sonawp_stripe_currency,
						),
						'price'       => array(
							'type'    => 'string',
							'default' => '0',
						),
						'images'      => array(
							'type'    => 'array',
							'default' => array(),
							'items'   => array(
								'type'       => 'object',
								'properties' => array(
									'id'  => array(
										'type' => 'number',
									),
									'url' => array(
										'type' => 'string',
									),
									'alt' => array(
										'type' => 'string',
									),
								),
							),
						),

					),
					'render_callback' => function ( $atts, $content ) {
						$block_html = $this->load_block_html( $atts, $content );
						return apply_filters( 'sona_paypal_block_html', $block_html, $atts, $content );
					},
				)
			);
		}

		/**
		 * loading html of block.
		 *
		 * @return mixed|void
		 */
		public function load_block_html( $atts, $content ) {

			if ( isset( $atts['price'] ) ) {

				global $wpdb;

				$meta_key = 'sona_blockid';

				$blockid = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value=%s", $meta_key, $atts['blockID'] ) );

				$custom_post = array(

					'post_title'   => ( isset( $atts['title'] ) ? $atts['title'] : 'Product Name' ),
					'post_content' => ( isset( $atts['description'] ) ? $atts['description'] : 'Product description' ),
					'post_type'    => 'sonaproduct',
					'post_status'  => 'publish',

					'meta_input'   => array(
						'sona_blockid'           => $atts['blockID'],
						'sona_product_title'     => ( isset( $atts['title'] ) ? $atts['title'] : 'Product Name' ),
						'sona_price'             => $atts['price'],
						'sona_currency'          => $atts['currency'],
						'sona_product_image_url' => $atts['images'],
					),

				);

				if ( isset( $blockid[0] ) ) {

					$custom_post['ID'] = $blockid[0]->post_id;
				}

				$product_id = wp_insert_post( $custom_post );
			}

			$content = sonawp_product_frontend( $product_id, $atts['images'], $atts['title'], $atts['description'], $atts['price'], $atts['currency'], 'stripe' );

			return $content;
		}
	}

	new Sona_Stripe_Block();

}
