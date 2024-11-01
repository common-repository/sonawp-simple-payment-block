<?php

/**
 * Plugin Name: SonaWP
 * Plugin URI: https://wordpress.org/plugins/sonawp
 * Description: Simply pay your product with block
 * Author: Sonawp
 * Version: 1.3.0
 * Text Domain: sonawp
 * Author URI: https://sonawp.com/
 */

if ( ! function_exists( 'sonawp_fs' ) ) {
	// Create a helper function for easy SDK access.
	function sonawp_fs() {
		global $sonawp_fs;

		if ( ! isset( $sonawp_fs ) ) {
			// Include Freemius SDK.
			require_once __DIR__ . '/freemius/start.php';

			$sonawp_fs = fs_dynamic_init(
				array(
					'id'             => '12876',
					'slug'           => 'sonawp-simple-payment-block',
					'type'           => 'plugin',
					'public_key'     => 'pk_4c0686d311ecd7aa86ee6737d5a8f',
					'is_premium'     => false,
					'has_addons'     => false,
					'has_paid_plans' => false,
					'menu'           => array(
						'slug' => 'sonawp',
					),
				)
			);
		}

		return $sonawp_fs;
	}

	// Init Freemius.
	sonawp_fs();
	// Signal that SDK was initiated.
	do_action( 'sonawp_fs_loaded' );
}

/**
 * SonaWP Constants.
 * @return void
 */

add_action( 'init', 'sona_plugin_constants' );

if ( ! function_exists( 'sona_plugin_constants' ) ) {
	/**
	 * Define all plugins required constants on init.
	 * @return void
	 */
	function sona_plugin_constants() {
		$version = '1.3.0';

		if ( ! defined( 'SONA_VERSION' ) ) {
			define( 'SONA_VERSION', $version );
		}

		if ( ! defined( 'SONA_DIR' ) ) {
			define( 'SONA_DIR', plugin_dir_path( __FILE__ ) );
		}

		if ( ! defined( 'SONA_URL' ) ) {
			define( 'SONA_URL', plugin_dir_url( __FILE__ ) );
		}
	}
}

/*
* SonaWp Plugin Activation.
*/

register_activation_hook( __FILE__, 'sona_activating_plugin' );

if ( ! function_exists( 'sona_activating_plugin' ) ) {
	/**
	 * SonaWP Plugin Activation.
	 * @return void
	 */

	function sona_activating_plugin() {
		$settings                   = get_option( 'sona', false );
		$settings['version']        = sanitize_text_field( '1.3.0' );
		$settings['installed_date'] = sanitize_text_field( gmdate( 'Y-m-d h:i:s' ) );
		update_option( 'sona', $settings );

		if ( ! get_option( 'sonawp_stripe_settings' ) ) {
			$sonawp_stripe_settings = array(
				'sonawp_stripe_mode'                 => 'test',
				'sonawp_stripe_test_publishable_key' => 'pk_test_TYooMQauvdEDq54NiTphI7jx',
				'sonawp_stripe_test_secret_key'      => 'sk_test_4eC39HqLyjWDarjtT1zdp7dc',
				'sonawp_stripe_live_publishable_key' => '',
				'sonawp_stripe_live_secret_key'      => '',
			);

			update_option( 'sonawp_stripe_settings', $sonawp_stripe_settings );
		}

		if ( ! get_option( 'sonawp_paypal_settings' ) ) {
			$sonawp_paypal_settings = array(
				'sonawp_paypal_mode' => 'test',
			);

			update_option( 'sonawp_paypal_settings', $sonawp_paypal_settings );
		}

		$sonawp_payment_confirmation = get_option( 'sonawp_payment_confirmation', array() );

		// Check and handle "Payment Success" page
		if ( ! array_key_exists( 'sonawp_payment_success_page', $sonawp_payment_confirmation ) ) {
			$sona_page = array(
				'post_title'   => 'Payment Success',
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_content' => '[sona_payment_success]',
			);

			$sona_page_id = wp_insert_post( $sona_page );

			// Update the 'sonawp_payment_confirmation' option to include the new page ID
			$sonawp_payment_confirmation['sonawp_payment_success_page'] = $sona_page_id;
			update_option( 'sonawp_payment_confirmation', $sonawp_payment_confirmation );
		} elseif ( get_post( $sonawp_payment_confirmation['sonawp_payment_success_page'] )->post_status === 'trash' ) {
			// Restore the "Payment Success" page if it's in the trash
			wp_publish_post( $sonawp_payment_confirmation['sonawp_payment_success_page'] );
		}

		// Check and handle "Payment Failed" page
		if ( ! array_key_exists( 'sonawp_payment_failed_page', $sonawp_payment_confirmation ) ) {
			$sona_page = array(
				'post_title'  => 'Payment Failed',
				'post_type'   => 'page',
				'post_status' => 'publish',
			);

			$sona_page_id = wp_insert_post( $sona_page );

			// Update the 'sonawp_payment_confirmation' option to include the new page ID
			$sonawp_payment_confirmation['sonawp_payment_failed_page'] = $sona_page_id;
			update_option( 'sonawp_payment_confirmation', $sonawp_payment_confirmation );
		} elseif ( get_post( $sonawp_payment_confirmation['sonawp_payment_failed_page'] )->post_status === 'trash' ) {
			// Restore the "Payment Failed" page if it's in the trash
			wp_publish_post( $sonawp_payment_confirmation['sonawp_payment_failed_page'] );
		}

		if ( empty( $sonawp_payment_confirmation['sonawp_confirmation_message'] ) ) {
			$sonawp_payment_confirmation['sonawp_confirmation_message'] = 'Thank you for purchasing {sona_product_name} for {sona_payment_currency} {sona_product_price} via {sona_payment_gateway} ({sona_payment_mode}).';
			update_option( 'sonawp_payment_confirmation', $sonawp_payment_confirmation );
		}
	}
}

/**
 * SonaWP Custom Post Types.
 */

require_once 'includes/classes/class-sona-post-types.php';

/**
 *  SonaWP Plugin Scripts.
 */

require_once 'includes/classes/class-sona-scripts.php';

/**
 * SonaWP Registering Blocks.
 */

require_once 'includes/classes/class-sona-register-blocks.php';

/**
 * SonaWp Payment Block.
 */

require_once 'includes/classes/class-sona-paypal-block.php';

/**
 * SonaWp Stripe Block.
 */

require_once 'includes/classes/class-sona-stripe-block.php';

/**
 * SonaWP Shortcodes.
 */

require_once 'includes/classes/class-sona-shortcodes.php';

/**
 * Sonawp notices
 * Contains: admin rating notice
 */

require_once 'includes/classes/class-sona-notices.php';

/**
 * Sonawp Orders cpt.
 * Contains: Orders Metaboxes, Cpt custom columns, filters to remove default cpt functionality.
 */

require_once 'includes/sona-orders-cpt.php';

/**
 * Sonawp Emails
 * Contains: Order emails to merchent and customer
 */

require_once 'includes/sona-emails.php';

/**
 * Sonawp Shortcodes
 * Contains: Product shortcode
 */

require_once 'includes/sona-shortcodes.php';

/**
 * Sonawp Functions
 * Contains: sonawp_product_frontend() which is used to display frontend of product
 */

require_once 'includes/sona-functions.php';

/**
 * Sonawp products
 * Contains: metaboxes for product, product custom columns, filters to remove default cpt functionality.
 */

require_once 'includes/sona-products.php';

/**
 * Adding Welcome Page in admin panel after plugin activation.
 */

add_action( 'admin_menu', 'sona_add_menu' );

if ( ! function_exists( 'sona_add_menu' ) ) {
	/**
	 * Adding menu in admin panel after plugin activation.
	 * @return void
	 */
	function sona_add_menu() {
		add_menu_page( 'SonaWP', 'SonaWP', 'manage_options', 'sonawp', 'sona_welcome_page', 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 34"><defs><style>.cls-1{fill:#fff;opacity:0;}.cls-2{fill:#f7f7f7;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><ellipse class="cls-1" cx="18" cy="17" rx="18" ry="17"/><path class="cls-2" d="M28.21,25.18a12.06,12.06,0,0,1-.85.91,13.87,13.87,0,0,1-3.56,2.53,9.68,9.68,0,0,0,.82-1.56,7.15,7.15,0,0,0,.17-5.27c-1-2.5-3.48-3.75-6.13-5.07a18.52,18.52,0,0,1-5.42-3.53A5.47,5.47,0,0,1,11.7,9.31V9.22a1.28,1.28,0,0,1,0-.19,3.71,3.71,0,0,1,.06-.45,5.74,5.74,0,0,1,1.48-2.84c1.54-1.53,4.1-2.25,7.15-1.25a16.27,16.27,0,0,1,9,7.24c-5-1.77-7.81-1-9-1.47a5,5,0,0,1-2.18-1.82c-2.42-3.53-4.3-1.51-3.82.92a7.26,7.26,0,0,0,2.94,4.23,18.93,18.93,0,0,0,6.13,2.56c2.39.68,4.45,1.26,5.41,2.77A5.67,5.67,0,0,1,28.21,25.18Z"/><path class="cls-2" d="M17.36,17.29c-2.66-1.33-5.17-2.58-6.14-5.08a7.15,7.15,0,0,1,.18-5.27,8.24,8.24,0,0,1,.81-1.56A13.92,13.92,0,0,0,8.64,7.91a11.79,11.79,0,0,0-.84.9,5.71,5.71,0,0,0-.6,6.27c1,1.51,3,2.09,5.41,2.77a18.71,18.71,0,0,1,6.13,2.56,7.26,7.26,0,0,1,2.94,4.23c.48,2.43-1.4,4.45-3.82.92h0a5.05,5.05,0,0,0-2.18-1.82c-1.23-.52-4,.3-9-1.47a16.27,16.27,0,0,0,9,7.24h0c3.05,1,5.61.28,7.14-1.25a5.75,5.75,0,0,0,1.49-2.84l.06-.45c0-.07,0-.13,0-.19v-.09a5.48,5.48,0,0,0-1.55-3.88,18,18,0,0,0-5.39-3.52Z"/></g></g></svg>' ), 20 ); //phpcs:ignore

		add_submenu_page( 'sonawp', 'Settings', 'Settings', 'manage_options', 'sonawp-settings', 'sona_admin_settings', 1 );

		//Dashboard page for analytics.
		add_submenu_page( 'sonawp', 'Dashboard', 'Dashboard', 'manage_options', 'sonawp-dashboard', 'sona_dashboard_page', 0 );
	}
}

if ( ! function_exists( 'sona_welcome_page' ) ) {
	/**
	 * Welcome page for plugin.
	 * @return void
	 */
	function sona_welcome_page() {
		include 'includes/views/welcome.php';
	}
}

if ( ! function_exists( 'sona_admin_settings' ) ) {
	/**
	 * Settings for plugin.
	 * @return void
	 */
	function sona_admin_settings() {
		include 'includes/admin/sonawp-settings.php';
	}
}

if ( ! function_exists( 'sona_dashboard_page' ) ) {
	/**
	 * Dashboard page for plugin.
	 * @return void
	 */
	function sona_dashboard_page() {
		include 'includes/admin/sonawp-dashboard.php';
	}
}

/**
 * Load Sona Payment Block text domain.
 *
 * @return void
 * @since 1.0.0
 *
 */

add_action( 'init', 'sona_loading_text_domain', 10 );

if ( ! function_exists( 'sona_loading_text_domain' ) ) {
	/**
	 * loading text domain on plugin_loaded hook.
	 * @return void
	 */
	function sona_loading_text_domain() {
		wp_register_script(
			'sona-translation',
			plugins_url( 'includes/blocks/sona-paypal/build/index.js', __FILE__ ),
			array(
				'wp-blocks',
				'wp-element',
				'wp-i18n',
				'wp-block-editor',
			),
			SONA_VERSION,
			true
		);

		wp_set_script_translations( 'sona-translation', 'sonawp', plugin_dir_path( __FILE__ ) . 'languages' );

		load_plugin_textdomain(
			'sonawp',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);
	}
}

/**
 * Recursive sanitation for text or array
 *
 * @param $array_or_string (array|string)
 * @return mixed
 */
function sanitize_text_or_array_field( $array_or_string ) {
	if ( is_string( $array_or_string ) ) {
		$array_or_string = sanitize_text_field( $array_or_string );
	} elseif ( is_array( $array_or_string ) ) {
		foreach ( $array_or_string as $key => &$value ) {
			if ( is_array( $value ) ) {
				$value = sanitize_text_or_array_field( $value );
			} else {
				$value = sanitize_text_field( $value );
			}
		}
	}

	return $array_or_string;
}

/**
 * Adding settings page for sonawp plugin.
 */

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'sonawp_plugin_action_links' );

function sonawp_plugin_action_links( $links ) {
	$links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=sonawp-settings' ) ) . '">' . esc_html__( 'Settings', 'sonawp' ) . '</a>';
	return $links;
}

/**
 * Adding info bar at footer of sonawp settings page.
 */

add_filter( 'admin_footer_text', 'sonawp_admin_footer_text' );
function sonawp_admin_footer_text( $html ) {
	if ( strpos( $_SERVER['REQUEST_URI'], 'page=sonawp-settings' ) !== false ) {
		$html = '<div class="notice notice-info sonawp-footer-text">Need more info? <a href="http://sonawp.com/wp-content/uploads/2023/10/sonawp.mp4" target="_blank">Click to Watch how SonaWP blocks work.</a></div>';
	}
	return $html;
}

/**
 * Since version 1.2.2.
 * @return void
 * These settings will overwrite the default settings of previous version.
 */

add_action( 'admin_init', 'sonawp_update_settings' );

function sonawp_update_settings() {

	if ( ! get_option( 'sonawp_stripe_settings' ) ) {
		$paypal_mode            = get_option( 'sonawp_paypal_mode' ); //depricated
		$sonawp_stripe_settings = array(
			'sonawp_stripe_mode'                 => isset( $paypal_mode ) ? $paypal_mode : 'test',
			'sonawp_stripe_test_publishable_key' => 'pk_test_TYooMQauvdEDq54NiTphI7jx',
			'sonawp_stripe_test_secret_key'      => 'sk_test_4eC39HqLyjWDarjtT1zdp7dc',
			'sonawp_stripe_live_publishable_key' => '',
			'sonawp_stripe_live_secret_key'      => '',
		);

		update_option( 'sonawp_stripe_settings', $sonawp_stripe_settings );
	}

	if ( ! get_option( 'sonawp_paypal_settings' ) ) {
		$stripe_mode            = get_option( 'sonawp_stripe_mode' ); //depricated
		$sonawp_paypal_settings = array(
			'sonawp_paypal_mode' => isset( $stripe_mode ) ? $stripe_mode : 'test',
		);

		update_option( 'sonawp_paypal_settings', $sonawp_paypal_settings );
	}

		$sonawp_payment_confirmation = get_option( 'sonawp_payment_confirmation', array() );

		// Check and handle "Payment Success" page
	if ( ! array_key_exists( 'sonawp_payment_success_page', $sonawp_payment_confirmation ) ) {

		$success_page = get_option( 'sonawp_payment_success_page' ); //depricated
		if ( $success_page ) {
			$sonawp_payment_confirmation['sonawp_payment_success_page'] = $success_page;
		} else {
			$sona_page = array(
				'post_title'   => 'Payment Success',
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_content' => '[sona_payment_success]',
			);

			$sona_page_id = wp_insert_post( $sona_page );
			// Update the 'sonawp_payment_confirmation' option to include the new page ID
			$sonawp_payment_confirmation['sonawp_payment_success_page'] = $sona_page_id;
		}
		update_option( 'sonawp_payment_confirmation', $sonawp_payment_confirmation );
	}

		// Check and handle "Payment Failed" page
	if ( ! array_key_exists( 'sonawp_payment_failed_page', $sonawp_payment_confirmation ) ) {
		$failed_page = get_option( 'sonawp_payment_failed_page' ); //depricated
		if ( $failed_page ) {
			$sonawp_payment_confirmation['sonawp_payment_failed_page'] = $failed_page;
		} else {
			$sona_page = array(
				'post_title'  => 'Payment Failed',
				'post_type'   => 'page',
				'post_status' => 'publish',
			);

			$sona_page_id = wp_insert_post( $sona_page );

			// Update the 'sonawp_payment_confirmation' option to include the new page ID
			$sonawp_payment_confirmation['sonawp_payment_failed_page'] = $sona_page_id;
		}

		update_option( 'sonawp_payment_confirmation', $sonawp_payment_confirmation );
	}

	if ( empty( $sonawp_payment_confirmation['sonawp_confirmation_message'] ) ) {
		$confiramtion_message = get_option( 'sonawp_confirmation_message' ); //depricated

		if ( $confiramtion_message ) {
			$sonawp_payment_confirmation['sonawp_confirmation_message'] = $confiramtion_message;
		} else {
			$sonawp_payment_confirmation['sonawp_confirmation_message'] = 'Thank you for purchasing {sona_product_name} for {sona_payment_currency} {sona_product_price} via {sona_payment_gateway} ({sona_payment_mode}).';
		}
		update_option( 'sonawp_payment_confirmation', $sonawp_payment_confirmation );
	}
}
