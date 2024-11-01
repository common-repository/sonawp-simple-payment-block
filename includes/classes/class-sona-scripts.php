<?php
/**
 * SonaWP Plugin scripts.
 * @package SonaWP
 */

if ( ! class_exists( 'Sona_Scripts' ) ) {
	class Sona_Scripts {
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'sona_enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'sona_enqueue_admin_scripts' ) );
		}

		public function sona_enqueue_scripts() {
			/**
			 * FancyBox
			 * @since 1.2.0
			 */
			wp_enqueue_style( 'fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css', array(), null, 'all' );
			wp_enqueue_script( 'fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js', array( 'jquery' ), null, true );

			//fancy carousel
			wp_enqueue_style( 'fancy-carousel-css', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/carousel/carousel.css', array(), null, 'all' );

			wp_enqueue_style( 'fancy-carousel-thumb-css', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/carousel/carousel.thumbs.css', array(), null, 'all' );

			wp_enqueue_script( 'fancy-carousel-js', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/carousel/carousel.umd.js', array( 'jquery' ), null, true );

			wp_enqueue_script( 'fancy-carousel-thumb-js', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/carousel/carousel.thumbs.umd.js', array( 'jquery' ), null, true );

			wp_enqueue_style( 'sona-style', plugins_url( 'style.css', __FILE__ ), array(), SONA_VERSION, 'all' );
			wp_enqueue_style( 'sona-frontend-main', SONA_URL . 'assets/css/frontend.css', array(), SONA_VERSION, 'all' );
			wp_enqueue_script( 'sona-frontend-main', SONA_URL . 'assets/js/frontend.js', array( 'jquery' ), SONA_VERSION, true );

			wp_enqueue_script(
				'sona-frontend',
				SONA_URL . 'assets/js/sona-frontend.js',
				array( 'jquery' ),
				SONA_VERSION,
				true,
			);

			if ( is_user_logged_in() ) {
				$current_user = wp_get_current_user();
				if ( $current_user->roles[0] == 'administrator' ) {
					$is_admin = 'admin_true';
				}
			}

			wp_localize_script(
				'sona-frontend',
				'ajax_object',
				array(
					'url'   => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( 'sona_nonce' ),
					'admin' => isset( $is_admin ) ? $is_admin : 'admin_false',
				)
			);

			$sonawp_paypal_settings = get_option( 'sonawp_paypal_settings' );
			if ( isset( $sonawp_paypal_settings['sonawp_paypal_mode'] ) ) {
				$paypal_mode = $sonawp_paypal_settings['sonawp_paypal_mode'];
			} else {
				$paypal_mode = 'test';
			}

			if ( $paypal_mode == 'test' ) {
				$paypal_client_id = 'AZILjwaFhGyFN9T4LajCz-HymUtUxLE2dBkMdtdUGrPeyWnPZNm4LS_0iAH2HseCgyVYlRsykRX_yO9R';
			} else {
				$paypal_client_id = 'AUVut56chm7NDBP-2zaIzTcKM0HlWkEMC55S-urYQfHIjuu1Bj01Cy1bGFxAFaaEVWiXKAlTt7SDzD98';
			}

			$sonawp_general_settings = get_option( 'sonawp_general_settings' );
			if ( ! empty( $sonawp_general_settings ) && isset( $sonawp_general_settings['sonawp_paypal_currency'] ) ) {
				$sonawp_paypal_currency = $sonawp_general_settings['sonawp_paypal_currency'];

			} else {
				$sonawp_paypal_currency = 'USD';
			}

			wp_enqueue_script(
				'sona-paypal-sdk',
				'https://www.paypal.com/sdk/js?client-id=' . $paypal_client_id . '&components=buttons,funding-eligibility&currency=' . $sonawp_paypal_currency,
				false,
				null,
				true,
			);
		}

		public function sona_enqueue_admin_scripts() {
			wp_enqueue_style( 'sona-admin', SONA_URL . 'assets/css/admin.css', array(), SONA_VERSION, 'all' );

			/**
			 *  Settings page.
			 * @since 1.2.2
			 */
			wp_enqueue_script( 'sona-admin-settings', SONA_URL . 'assets/js/admin-settings.js', array( 'jquery', 'media-upload' ), SONA_VERSION, true );

			if ( ! did_action( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}

			if ( isset( $_GET['page'] ) && $_GET['page'] === 'sonawp-dashboard' ) {
				/**
				 *  Analytics and reports of transactions.
				 * @since 1.2.3
				 */
				wp_enqueue_script( 'sona-admin-dashboard', SONA_URL . 'assets/js/admin-dashboard.js', array( 'jquery' ), SONA_VERSION, true );

				/**
				 * Localize scripts.
				 * For realtime chart.
				 */
				wp_localize_script(
					'sona-admin-dashboard',
					'ajax_object',
					array(
						'url'                         => admin_url( 'admin-ajax.php' ),
						'sona_ajax_order_chart_nonce' => wp_create_nonce( 'sona_ajax_order_chart' ),
					)
				);
			}

			/**
			 * Font Awesome
			 */
			wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css', array(), '5.15.3', 'all' );

			/**
			 * Chart JS
			 * For analytics and reports.
			 * @since 1.2.3
			 */
			wp_enqueue_script( 'sonawp-chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js', array( 'jquery' ), '4.4.0', false );
		}
	}

	new Sona_Scripts();
}
