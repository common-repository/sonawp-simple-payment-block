<?php
/**
 * Sona Notices
 * Currently used to show admin rating notices.
 * @package SonaWP
 */

if ( ! class_exists( 'Sona_Notices' ) ) {
	class Sona_Notices {
		public function __construct() {
			add_action( 'admin_notices', array( $this, 'sonawp_rating_notice' ) );
			add_action( 'admin_notices', array( $this, 'sonawp_general_notice' ) );
			add_action( 'wp_ajax_sonawp_rating', array( $this, 'sonawp_rating_ajax_callback' ) );
			add_action( 'wp_ajax_sonawp_general_notice', array( $this, 'sonawp_general_notice_ajax_callback' ) );
		}

		public function sonawp_rating_notice() {

			$sona_activation_date = get_option( 'sona' );
			$install_date         = $sona_activation_date['installed_date'];

			$current_date = gmdate( 'Y-m-d H:i:s' );

			if ( strtotime( $current_date ) > strtotime( '+7 days', strtotime( $install_date ) ) ) {

				$sonawp_rating = get_option( 'sonawp_rating' );

				if ( $sonawp_rating ) {
					return;
				}

				?>
				<div class="notice notice-info is-dismissible sonawp-rating-notice">
					<p>Awesome, you have been using <strong>SonaWP - Simple Payment Blocks </strong> for more than a week. We would really appreciate it if you please do us a BIG favor and give it a 5-star rating on WordPress? Just to help us spread the word and boost our motivation.</p>
					<p>
			
					<a href="https://wordpress.org/support/plugin/sonawp-simple-payment-block/reviews?filter=5#new-post" target="_blank" class="button button-primary">Ok, you deserve it</a>
			
					<a href="javascript:void(0);" class="button button-secondary">Maybe later</a></p>
			
				</div>
				<script>
					jQuery(document).ready(function(){
						jQuery('.sonawp-rating-notice.notice-info .button-secondary').click(function(){
							jQuery('.notice-info').remove();
						});
				
						jQuery('.sonawp-rating-notice.notice-info .button-primary').click(function(){
							jQuery.ajax({
								url: ajaxurl,
								type: 'POST',
								data: {
									action: 'sonawp_rating',
									nonce: '<?php echo wp_create_nonce( 'sonawp_rating_nonce' ); ?>'
								},
								success: function(response){
									jQuery('.notice-info').remove();
								}
							});
						});
					});
				</script>
				<?php
			}
		}

		public function sonawp_rating_ajax_callback() {
			//verify nonce
			if ( ! wp_verify_nonce( $_POST['nonce'], 'sonawp_rating_nonce' ) ) {
				die( 'Busted!' );
			}

			//update option sonawp_rating to true
			update_option( 'sonawp_rating', true );

			wp_die();
		}
		public function sonawp_general_notice() {
			$sonawp_general_notice = get_option( 'sonawp_general_notice' );

			// if ( $sonawp_general_notice ) {
			// 	return;
			// }

			?>
			<div class="notice notice-info is-dismissible sonawp-general-notice">
				<p><b>Sonawp</b> Exciting News: You can now create Sonawp product shortcodes! Simply <a href="<?php echo admin_url( 'edit.php?post_type=sonaproduct' ); ?>"
				target="_blank">create shortcode now</a> to customize and insert them on your site.</p>
				<p>
					<a href="javascript:void(0);" class="button button-primary sonawp-general-okay-button">Close</a>
				</p>
			</div>
			<script>
				jQuery(document).ready(function(){
					jQuery('.sonawp-general-notice .sonawp-general-okay-button').click(function(){
						jQuery.ajax({
							url: ajaxurl,
							type: 'POST',
							data: {
								action: 'sonawp_general_notice',
								nonce: '<?php echo wp_create_nonce( 'sonawp_general_notice_nonce' ); ?>'
							},
							success: function(response){
								jQuery('.sonawp-general-notice').remove();
							}
						});
					});
				});
			</script>
			<?php
		}
		public function sonawp_general_notice_ajax_callback() {
			// Verify nonce for general notice
			if ( ! wp_verify_nonce( $_POST['nonce'], 'sonawp_general_notice_nonce' ) ) {
				die( 'Busted!' );
			}

			// Update option sonawp_general_notice to true
			update_option( 'sonawp_general_notice', true );

			wp_die();
		}
	}
	new Sona_Notices();
}
