<?php
/**
 * SonaWP Paypal Class.
 * Adding product, managing paypal orders and sending emails.
 * @package SonaWP
 */

if ( ! class_exists( 'Sona_Paypal_Block' ) ) {
	class Sona_Paypal_Block {
		public function __construct() {
			add_action(
				'wp_ajax_nopriv_get_sona',
				array(
					$this,
					'sona_product_ajax_callback',
				)
			);

			add_action(
				'wp_ajax_get_sona',
				array(
					$this,
					'sona_product_ajax_callback',
				)
			);

			add_action( 'wp_ajax_nopriv_get_sona_order', array( $this, 'sona_order_ajax_callback' ) );
			add_action( 'wp_ajax_get_sona_order', array( $this, 'sona_order_ajax_callback' ) );
		}
		/**
		 * Define block attributes
		 *
		 * @return mixed|void
		 */
		public function block_attributes() {

			$sonawp_general_settings = get_option( 'sonawp_general_settings' );
			$sonawp_paypal_currency  = isset( $sonawp_general_settings['sonawp_paypal_currency'] ) ? $sonawp_general_settings['sonawp_paypal_currency'] : 'USD';

			return apply_filters(
				'sona_paypal_block_attributes',
				array(

					'icon'            => 'money-alt',
					'textdomain'      => 'sonawp',
					'editor_script'   => 'sona-translation',
					'description'     => __( 'Simply pay your product with block.', 'sonawp' ),
					'attributes'      => array(
						'blockID'     => array(
							'type' => 'string',
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
						'title'       => array(
							'type' => 'string',
						),
						'description' => array(
							'type' => 'string',
						),
						'currency'    => array(
							'type'    => 'string',
							'default' => $sonawp_paypal_currency,
						),
						'price'       => array(
							'type'    => 'string',
							'default' => '0',
						),
						'payeeEmail'  => array(
							'type' => 'string',
						),
						'paypal'      => array(
							'type'    => 'string',
							'default' => 'paypal',
						),
						'paylater'    => array(
							'type'    => 'string',
							'default' => 'paylater',
						),
						'card'        => array(
							'type'    => 'string',
							'default' => 'card',
						),
						'venmo'       => array(
							'type'    => 'string',
							'default' => 'venmo',
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
		 * Load block html
		 * @param $atts
		 * @param $content
		 */

		public function load_block_html( $atts, $content ) {

			if ( isset( $atts['price'] ) && $atts['price'] > 0 ) {

				$sonawp_paypal_settings      = get_option( 'sonawp_paypal_settings' );
				$sonawp_payment_confirmation = get_option( 'sonawp_payment_confirmation' );
				$sonawp_payment_failed_page  = $sonawp_payment_confirmation['sonawp_payment_failed_page'];
				$payment_failed_page_url     = get_permalink( $sonawp_payment_failed_page );

				if ( ! empty( $atts['payeeEmail'] ) ) {
					$merchent_email = $atts['payeeEmail'];
				} else {
					$master_merchent_email = empty( $sonawp_paypal_settings['sonawp_paypal_email'] ) ? '' : $sonawp_paypal_settings['sonawp_paypal_email'];
					$merchent_email        = $master_merchent_email;
				}

				global $wpdb;

				$meta_key = 'sona_blockid';

				$blockid = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value=%s", $meta_key, $atts['blockID'] ) );

				$custom_post = array(

					'post_title'   => ( isset( $atts['title'] ) ? $atts['title'] : 'Product Name' ),
					'post_content' => ( isset( $atts['description'] ) ? $atts['description'] : 'Product description' ),
					'post_type'    => 'sonaproduct',
					'post_status'  => 'publish',

					'meta_input'   => array(
						'sona_blockid'             => $atts['blockID'],
						'sona_product_title'       => ( isset( $atts['title'] ) ? $atts['title'] : 'Product Name' ),
						'sona_email'               => $merchent_email,
						'sona_currency'            => $atts['currency'],
						'sona_price'               => $atts['price'],
						'sona_paypal_button'       => $atts['paypal'],
						'sona_paylater_button'     => $atts['paylater'],
						'sona_debit_credit_button' => $atts['card'],
						'sona_venmo_button'        => $atts['venmo'],
						'sona_product_image_url'   => $atts['images'],
						'sona_payment_failed_page' => $payment_failed_page_url,
					),

				);

				if ( isset( $blockid[0] ) ) {

					$custom_post['ID'] = $blockid[0]->post_id;
				}

				$product_id = wp_insert_post( $custom_post );

				if ( ! is_wp_error( $product_id ) && isset( $atts['productImgID'] ) ) {

					set_post_thumbnail( $product_id, $atts['productImgID'] );
				}
			}

			$description = ( isset( $atts['description'] ) ? $atts['description'] : '' );

			$content = sonawp_product_frontend( $product_id, $atts['images'], $atts['title'], $description, $atts['price'], $atts['currency'], 'paypal' );

			return $content;
		}

		/**
		 * Sending block content through ajax
		 *
		 * @return mixed|void
		 */
		public function sona_product_ajax_callback() {

			if ( wp_verify_nonce( $_POST['nonce'], 'sona_nonce' ) ) {
				$post_id = ( isset( $_POST['blockid'] ) ? sanitize_text_field( $_POST['blockid'] ) : '' );

				$sonawp_general_settings = get_option( 'sonawp_general_settings' );
				if ( ! empty( $sonawp_general_settings ) ) {
					$sonawp_paypal_currency = $sonawp_general_settings['sonawp_paypal_currency'];
				} else {
					$sonawp_paypal_currency = 'USD';
				}

				global $wpdb;

				$meta_table = $wpdb->prefix . 'postmeta';

				$query = $wpdb->prepare(
					"SELECT meta_key, meta_value
					FROM $meta_table
					WHERE post_id = %d",
					$post_id
				);

				$results = $wpdb->get_results( $query );

				if ( $results ) {
					$meta_values = array();
					foreach ( $results as $result ) {
						$meta_values[ $result->meta_key ] = $result->meta_value;
					}
					$meta_values['sonawp_paypal_currency'] = $sonawp_paypal_currency;

					wp_send_json_success( $meta_values );
				}
			}
		}

		public function sona_order_ajax_callback() {
			if ( wp_verify_nonce( $_POST['nonce'], 'sona_nonce' ) ) {

				$order_product_name = ( isset( $_POST['order_product_name'] ) ? sanitize_text_field( $_POST['order_product_name'] ) : '' );
				$order_method       = ( isset( $_POST['order_method'] ) ? sanitize_text_field( $_POST['order_method'] ) : '' );
				$order_data         = ( isset( $_POST['order_data'] ) ? sanitize_text_or_array_field( $_POST['order_data'] ) : '' );

				$order_id                            = $order_data['id'];
				$order_created_time                  = $order_data['create_time'];
				$order_amount                        = $order_data['purchase_units'][0]['amount']['value'];
				$filter_currency                     = $order_data['purchase_units'][0]['amount']['currency_code'];
				$order_shipping_address              = $order_data['purchase_units'][0]['shipping']['address']['address_line_1'];
				$order_shipping_address_admin_area_1 = $order_data['purchase_units'][0]['shipping']['address']['admin_area_2'];
				$order_shipping_address_admin_area_2 = $order_data['purchase_units'][0]['shipping']['address']['admin_area_1'];
				$order_merchant_email                = $order_data['purchase_units'][0]['payee']['email_address'];
				$order_payer_email_address           = $order_data['payer']['email_address'];

				$sonawp_paypal_settings = get_option( 'sonawp_paypal_settings' );
				$paypal_mode            = $sonawp_paypal_settings['sonawp_paypal_mode'];

				$custom_post = array(

					'post_title'  => $order_product_name,
					'post_type'   => 'sonaorder',
					'post_status' => 'publish',
					'meta_input'  => array(
						'sona_order_post_name' => $order_product_name,
						'sona_order_method'    => $order_method,
						'sona_paypal_order'    => $order_data,
						'sona_paypal_mode'     => $paypal_mode,
						'sona_filter_price'    => $order_amount,
						'sona_filter_currency' => $filter_currency,
					),

				);

				$order_insert = wp_insert_post( $custom_post );

				//send json success in which we have to send success message

				if ( ! is_wp_error( $order_insert ) ) {
					$sonawp_payment_confirmation = get_option( 'sonawp_payment_confirmation' );
					$success_page                = $sonawp_payment_confirmation['sonawp_payment_success_page'];
					$success_page_id             = get_post( $success_page );

					if ( $success_page_id ) {
						$success_page_url = get_permalink( $success_page_id->ID );
					} else {
						$sona_page    = array(
							'post_title'   => 'Payment Success',
							'post_type'    => 'page',
							'post_status'  => 'publish',
							'post_content' => '[sona_payment_success]',
						);
						$sona_page_id = wp_insert_post( $sona_page );

						$success_page_url = get_permalink( $sona_page_id );

						$sonawp_payment_confirmation['sonawp_payment_success_page'] = $sona_page_id;
						update_option( 'sonawp_payment_confirmation', $sonawp_payment_confirmation );
					}
					$success_page_url = $success_page_url . '?orderid=' . $order_insert . '&nonce=' . wp_create_nonce( 'sona_payment_success' );
					wp_send_json_success( $success_page_url );
				}

				$sonawp_paypal_settings = get_option( 'sonawp_paypal_settings' );
				if ( isset( $sonawp_paypal_settings['sonawp_paypal_order_email'] ) ) {
					$order_merchant_email = $sonawp_paypal_settings['sonawp_paypal_order_email'];
				}

				if ( ! is_wp_error( $order_insert ) ) {
					sonawp_send_buyer_email( $order_id, $order_created_time, $order_product_name, $order_amount, $order_shipping_address, $order_shipping_address_admin_area_1, $order_shipping_address_admin_area_2, $order_payer_email_address );
					sonawp_send_merchant_email( $order_id, $order_created_time, $order_product_name, $order_amount, $order_merchant_email, $order_payer_email_address, $order_shipping_address, $order_shipping_address_admin_area_1, $order_shipping_address_admin_area_2 );
				}
			}
		}
	}

	new Sona_Paypal_Block();
} // Closing of if class exists
