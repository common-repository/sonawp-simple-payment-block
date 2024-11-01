<?php

/**
 * Class SonaRegisterBlocks.
 * @package SonaWP
 */

if ( ! class_exists( 'Sona_Register_Blocks' ) ) {
	class Sona_Register_Blocks {
		public function __construct() {
			add_action( 'init', array( $this, 'sona_register_blocks' ) );
		}

		public function sona_register_blocks() {

			if ( function_exists( 'register_block_type' ) ) {
				$db       = new Sona_Paypal_Block();
				$response = $db->block_attributes();

				register_block_type(
					SONA_DIR . 'includes/blocks/sona-paypal/build',
					$response
				);

				$attr             = new Sona_Stripe_Block();
				$sona_stripe_attr = $attr->block_attributes();

				register_block_type(
					SONA_DIR . 'includes/blocks/sona-stripe/build',
					$sona_stripe_attr
				);
			}
		}
	}

	new Sona_Register_Blocks();
}
