<?php
/**
 * Class Sona_Shortcodes.
 * @package SonaWP
 */

if ( ! class_exists( 'Sona_Shortcodes' ) ) {
	class Sona_Shortcodes {
		public function __construct() {
			add_shortcode( 'sona_payment_success', array( $this, 'sona_payment_success' ) );
		}

		public function sona_payment_success() {

			$message = '';

			if ( isset( $_GET['session_id'] ) && isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'sona_payment_success' ) ) {

				require_once SONA_DIR . 'includes/vendor/autoload.php';

				$stripe_connect = get_option( 'sonawp_stripe_settings' );

				if ( empty( $stripe_connect ) ) {
					return;
				}

				$id               = $_GET['session_id'];
				$checkout_session = \Stripe\Checkout\Session::retrieve( $id );

				$sessions = \Stripe\Checkout\Session::all(
					array(
						'payment_intent' => $checkout_session['payment_intent'],
						'expand'         => array( 'data.line_items' ),
					)
				);

				$productname = $sessions['data'][0]['line_items']['data'][0]['description'];

				$order_created_time = DateTime::createFromFormat( 'U', $sessions['data'][0]['created'] )->format( 'Y-m-d H:i:s' );

				$order_currency = $sessions['data'][0]['currency'];

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

				if ( in_array( $order_currency, $zero_decimal_currencies ) ) {
					$order_amount = $sessions['data'][0]['amount_total'];
				} else {
					$order_amount = $sessions['data'][0]['amount_total'] / 100;
				}

				$order_payer_email_address = $sessions['data'][0]['customer_details']['email'];

				$order_shipping_address = $sessions['data'][0]['customer_details']['address']['country'];

				$order_shipping_address = empty( $order_shipping_address ) ? 'N/A' : $order_shipping_address;

				$sonawp_stripe_settings = get_option( 'sonawp_stripe_settings' );
				$sonawp_stripe_mode     = $sonawp_stripe_settings['sonawp_stripe_mode'];

				$stripe_order_data = array(
					'livemode'            => $sessions['data'][0]['livemode'],
					'payment_intent'      => $sessions['data'][0]['payment_intent'],
					'created'             => $sessions['data'][0]['created'],
					'amount_total'        => $order_amount,
					'currency'            => $order_currency,
					'payment_status'      => $sessions['data'][0]['payment_status'],
					'payment_method_type' => $sessions['data'][0]['payment_method_types'][0],
					'customer_email'      => $order_payer_email_address,
					'customer_name'       => $sessions['data'][0]['customer_details']['name'],
					'customer_country'    => $order_shipping_address,
				);

				$sona_stripe_order = array(

					'post_title'  => $productname,
					'post_type'   => 'sonaorder',
					'post_status' => 'publish',
					'meta_input'  => array(
						'sona_order_post_name' => $productname,
						'sona_order_method'    => 'stripe',
						'sona_stripe_order'    => $stripe_order_data,
						'sona_stripe_mode'     => $sonawp_stripe_mode,
						'sona_filter_price'    => $order_amount,
						'sona_filter_currency' => $order_currency,
					),

				);

				$order_insert = wp_insert_post( $sona_stripe_order );

				if ( $order_insert ) {

					$sonawp_payment_confirmation = get_option( 'sonawp_payment_confirmation' );
					$confirmation_message        = $sonawp_payment_confirmation['sonawp_confirmation_message'];

					$confirmation_message = str_replace( '{sona_product_name}', $productname, $confirmation_message );
					$confirmation_message = str_replace( '{sona_product_price}', $sessions['data'][0]['amount_total'] / 100, $confirmation_message );
					$confirmation_message = str_replace( '{sona_payment_gateway}', 'Stripe', $confirmation_message );
					$confirmation_message = str_replace( '{sona_payment_mode}', $sessions['data'][0]['livemode'] ? 'Live' : 'Test', $confirmation_message );
					$confirmation_message = str_replace( '{sona_payment_currency}', $sessions['data'][0]['currency'], $confirmation_message );

					$message = $confirmation_message;

				}

				if ( ! is_wp_error( $order_insert ) ) {
					sonawp_send_buyer_email( $sessions['data'][0]['payment_intent'], $order_created_time, $productname, $order_amount, $order_shipping_address, '', '', $order_payer_email_address );

					$sonawp_email_settings     = get_option( 'sonawp_email_settings' );
					$sonawp_stripe_order_email = isset( $sonawp_email_settings['sonawp_stripe_order_email'] ) ? $sonawp_email_settings['sonawp_stripe_order_email'] : '';

					if ( $sonawp_stripe_order_email ) {

						sonawp_send_merchant_email( $sessions['data'][0]['payment_intent'], $order_created_time, $productname, $order_amount, $sonawp_stripe_order_email, $order_payer_email_address, $order_shipping_address, '', '' );
					}
				}
			}

			if ( isset( $_GET['orderid'] ) && ! empty( $_GET['orderid'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'sona_payment_success' ) ) {

				$order_id = sanitize_text_field( $_GET['orderid'] );

				//get sona_order post
				$order_post = get_post( $order_id );

				//get post titlte
				$order_post_title = $order_post->post_title;

				//get post meta sona_paypal_order
				$order_data = get_post_meta( $order_id, 'sona_paypal_order', true );

				$currency = $order_data['purchase_units'][0]['amount']['currency_code'];
				$price    = $order_data['purchase_units'][0]['amount']['value'];

				$sonawp_payment_confirmation = get_option( 'sonawp_payment_confirmation' );
				$confirmation_message        = $sonawp_payment_confirmation['sonawp_confirmation_message'];

				$sonawp_paypal_settings = get_option( 'sonawp_paypal_settings' );
				$paypal_mode            = $sonawp_paypal_settings['sonawp_paypal_mode'];

				$confirmation_message = str_replace( '{sona_product_name}', $order_post_title, $confirmation_message );
				$confirmation_message = str_replace( '{sona_product_price}', $price, $confirmation_message );
				$confirmation_message = str_replace( '{sona_payment_gateway}', 'Paypal', $confirmation_message );
				$confirmation_message = str_replace( '{sona_payment_mode}', $paypal_mode, $confirmation_message );
				$confirmation_message = str_replace( '{sona_payment_currency}', $currency, $confirmation_message );

				$message = $confirmation_message;

			}

			//apply filter the_content
			$message = apply_filters( 'the_content', $message );

			return $message;
		}
	}

	new Sona_Shortcodes();
}
