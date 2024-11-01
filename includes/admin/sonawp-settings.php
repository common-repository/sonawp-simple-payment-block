<?php
/**
 * Settings page
 * @package sonawp
 */

$sonawp_general_settings     = get_option( 'sonawp_general_settings', array() );
$sonawp_stripe_settings      = get_option( 'sonawp_stripe_settings', array() );
$sonawp_paypal_settings      = get_option( 'sonawp_paypal_settings', array() );
$sonawp_payment_confirmation = get_option( 'sonawp_payment_confirmation', array() );
$sonawp_email_settings       = get_option( 'sonawp_email_settings', array() );

if ( isset( $_POST['save_settings'] ) ) {

	if ( ! isset( $_POST['sonawp_settings_nonce'] ) || ! wp_verify_nonce( $_POST['sonawp_settings_nonce'], 'sonawp_settings' ) ) {
		echo '<div class="notice notice-error is-dismissible"><p>Something wents wrong refresh and try again.</p></div>';
		return;
	}

	if ( isset( $_POST['sonawp_general_settings'] ) ) {
		$sonawp_general_settings = $_POST['sonawp_general_settings'];
		update_option( 'sonawp_general_settings', array_map( 'sanitize_text_field', $sonawp_general_settings ) );
	}

	if ( isset( $_POST['sonawp_stripe_settings'] ) ) {
		$sonawp_stripe_settings = $_POST['sonawp_stripe_settings'];
		update_option( 'sonawp_stripe_settings', array_map( 'sanitize_text_field', $sonawp_stripe_settings ) );
	}

	if ( isset( $_POST['sonawp_paypal_settings'] ) ) {
		$sonawp_paypal_settings = $_POST['sonawp_paypal_settings'];
		update_option( 'sonawp_paypal_settings', array_map( 'sanitize_text_field', $sonawp_paypal_settings ) );
	}

	if ( isset( $_POST['sonawp_payment_confirmation'] ) ) {
		$sonawp_payment_confirmation = $_POST['sonawp_payment_confirmation'];
		update_option( 'sonawp_payment_confirmation', array_map( 'sanitize_text_field', $sonawp_payment_confirmation ) );
	}

	if ( isset( $_POST['sonawp_email_settings'] ) ) {
		$sonawp_email_settings = $_POST['sonawp_email_settings'];
		update_option( 'sonawp_email_settings', array_map( 'sanitize_text_field', $sonawp_email_settings ) );
	}

	echo '<div class="notice notice-success is-dismissible"><p>Settings saved successfully.</p></div>';

}

$stripe_currencies = array(
	array(
		'label'  => 'United States ($)',
		'value'  => 'usd',
		'symbol' => '$',
	),
	array(
		'label'  => 'Euro (€)',
		'value'  => 'eur',
		'symbol' => '€',
	),
	array(
		'label'  => 'Australia ($)',
		'value'  => 'aud',
		'symbol' => '$',
	),
	array(
		'label'  => 'Brazil (R$)',
		'value'  => 'brl',
		'symbol' => 'R$',
	),
	array(
		'label'  => 'Canada ($)',
		'value'  => 'cad',
		'symbol' => '$',
	),
	array(
		'label'  => 'Czech Republic (Kč)',
		'value'  => 'czk',
		'symbol' => 'Kč',
	),
	array(
		'label'  => 'Denmark (kr)',
		'value'  => 'dkk',
		'symbol' => 'kr',
	),

	array(
		'label'  => 'Hong Kong ($)',
		'value'  => 'hkd',
		'symbol' => '$',
	),
	array(
		'label'  => 'Hungary (Ft)',
		'value'  => 'huf',
		'symbol' => 'Ft',
	),
	array(
		'label'  => 'India (₹)',
		'value'  => 'inr',
		'symbol' => '₹',
	),
	array(
		'label'  => 'Japan (¥)',
		'value'  => 'jpy',
		'symbol' => '¥',
	),
	array(
		'label'  => 'Malaysia (RM)',
		'value'  => 'myr',
		'symbol' => 'RM',
	),
	array(
		'label'  => 'Mexico ($)',
		'value'  => 'mxn',
		'symbol' => '$',
	),
	array(
		'label'  => 'New Zealand ($)',
		'value'  => 'nzd',
		'symbol' => '$',
	),
	array(
		'label'  => 'Norway (kr)',
		'value'  => 'nok',
		'symbol' => 'kr',
	),
	array(
		'label'  => 'Poland (zł)',
		'value'  => 'pln',
		'symbol' => 'zł',
	),
	array(
		'label'  => 'Romania (lei)',
		'value'  => 'ron',
		'symbol' => 'lei',
	),
	array(
		'label'  => 'Singapore ($)',
		'value'  => 'sgd',
		'symbol' => '$',
	),
	array(
		'label'  => 'Sweden (kr)',
		'value'  => 'sek',
		'symbol' => 'kr',
	),
	array(
		'label'  => 'Switzerland (CHF)',
		'value'  => 'chf',
		'symbol' => 'CHF',
	),
	array(
		'label'  => 'Thailand (฿)',
		'value'  => 'thb',
		'symbol' => '฿',
	),
	array(
		'label'  => 'United Arab Emirates (د.إ)',
		'value'  => 'aed',
		'symbol' => 'د.إ',
	),
	array(
		'label'  => 'United Kingdom (£)',
		'value'  => 'gbp',
		'symbol' => '£',
	),
);

$paypal_currencies = array(
	array(
		'label'  => 'United States ($)',
		'value'  => 'USD',
		'symbol' => '$',
	),
	array(
		'label'  => 'Euro (€)',
		'value'  => 'EUR',
		'symbol' => '€',
	),
	array(
		'label'  => 'Australia (A$)',
		'value'  => 'AUD',
		'symbol' => 'A$',
	),
	array(
		'label'  => 'Brazil (R$)',
		'value'  => 'BRL',
		'symbol' => 'R$',
	),
	array(
		'label'  => 'Canada (C$)',
		'value'  => 'CAD',
		'symbol' => 'C$',
	),
	array(
		'label'  => 'Czech Republic (Kč)',
		'value'  => 'CZK',
		'symbol' => 'Kč',
	),
	array(
		'label'  => 'Denmark (kr)',
		'value'  => 'DKK',
		'symbol' => 'kr',
	),
	array(
		'label'  => 'Hong Kong (HK$)',
		'value'  => 'HKD',
		'symbol' => 'HK$',
	),
	array(
		'label'  => 'Hungary (Ft)',
		'value'  => 'HUF',
		'symbol' => 'Ft',
	),
	array(
		'label'  => 'Israel (₪)',
		'value'  => 'ILS',
		'symbol' => '₪',
	),
	array(
		'label'  => 'Japan (¥)',
		'value'  => 'JPY',
		'symbol' => '¥',
	),
	array(
		'label'  => 'Malaysia (RM)',
		'value'  => 'MYR',
		'symbol' => 'RM',
	),
	array(
		'label'  => 'Mexico (MX$)',
		'value'  => 'MXN',
		'symbol' => 'MX$',
	),
	array(
		'label'  => 'Taiwan (NT$)',
		'value'  => 'TWD',
		'symbol' => 'NT$',
	),
	array(
		'label'  => 'New Zealand (NZ$)',
		'value'  => 'NZD',
		'symbol' => 'NZ$',
	),
	array(
		'label'  => 'Norway (kr)',
		'value'  => 'NOK',
		'symbol' => 'kr',
	),
	array(
		'label'  => 'Philippines (₱)',
		'value'  => 'PHP',
		'symbol' => '₱',
	),
	array(
		'label'  => 'Poland (zł)',
		'value'  => 'PLN',
		'symbol' => 'zł',
	),
	array(
		'label'  => 'United Kingdom (£)',
		'value'  => 'GBP',
		'symbol' => '£',
	),
	array(
		'label'  => 'Russia (₽)',
		'value'  => 'RUB',
		'symbol' => '₽',
	),
	array(
		'label'  => 'Singapore (S$)',
		'value'  => 'SGD',
		'symbol' => 'S$',
	),
	array(
		'label'  => 'Sweden (kr)',
		'value'  => 'SEK',
		'symbol' => 'kr',
	),
	array(
		'label'  => 'Switzerland (CHf)',
		'value'  => 'CHF',
		'symbol' => 'CHf',
	),
	array(
		'label'  => 'Thailand (฿)',
		'value'  => 'THB',
		'symbol' => '฿',
	),
);

?>

<div class="sona-main-div">

	<div class="sona-admin-tab">
		<button class="tablinks" data-sonatabs="General">General</button>
		<button class="tablinks" data-sonatabs="Stripe">Stripe</button>
		<button class="tablinks" data-sonatabs="Paypal">PayPal</button>
		<button class="tablinks" data-sonatabs="Confirmation">Payment Confirmation</button>
		<button class="tablinks" data-sonatabs="Email">Emails</button>
	</div>

	<form action="" method="post">

		<?php wp_nonce_field( 'sonawp_settings', 'sonawp_settings_nonce' ); ?>

		<div class="general-settings tabcontent" id="General">

			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="sonawp_stripe_currency">
								<?php echo esc_html__( 'Stripe Supported Currencies', 'sonawp' ); ?>
							</label>
						</th>
						<td>
							<select name="sonawp_general_settings[sonawp_stripe_currency]" id="sonawp_stripe_currency">
								<?php foreach ( $stripe_currencies as $stripe_currency ) : ?>
									<option value="<?php echo esc_attr( $stripe_currency['value'] ); ?>" <?php isset( $sonawp_general_settings['sonawp_stripe_currency'] ) ? selected( $sonawp_general_settings['sonawp_stripe_currency'], $stripe_currency['value'] ) : ''; ?>>
										<?php echo esc_html( $stripe_currency['label'] ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="sonawp_paypal_currency">
								<?php echo esc_html__( 'PayPal Supported Currencies', 'sonawp' ); ?>
							</label>
						</th>
						<td>
							<select name="sonawp_general_settings[sonawp_paypal_currency]" id="sonawp_paypal_currency">
								<?php foreach ( $paypal_currencies as $currency ) : ?>
									<option value="<?php echo $currency['value']; ?>" <?php isset( $sonawp_general_settings['sonawp_paypal_currency'] ) ? selected( $sonawp_general_settings['sonawp_paypal_currency'], $currency['value'] ) : ''; ?>>
										<?php echo esc_html( $currency['label'] ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="sonawp_currency_position">
								<?php echo esc_html__( 'Currency Position', 'sonawp' ); ?>
							</label>
						</th>
						<td>
							<select name="sonawp_general_settings[sonawp_currency_position]" id="sonawp_currency_position">

								<option value="left" <?php isset( $sonawp_general_settings['sonawp_currency_position'] ) ? selected( $sonawp_general_settings['sonawp_currency_position'], 'left' ) : ''; ?>>
									<?php echo esc_html__( 'Left : $10.00', 'sonawp' ); ?>
								</option>

								<option value="right" <?php isset( $sonawp_general_settings['sonawp_currency_position'] ) ? selected( $sonawp_general_settings['sonawp_currency_position'], 'right' ) : ''; ?>>
									<?php echo esc_html__( 'Right : 10.00$', 'sonawp' ); ?>
								</option>

								<option value="left_space" <?php isset( $sonawp_general_settings['sonawp_currency_position'] ) ? selected( $sonawp_general_settings['sonawp_currency_position'], 'left_space' ) : ''; ?>>
									<?php echo esc_html__( 'Left with space : $ 10.00', 'sonawp' ); ?>
								</option>

								<option value="right_space" <?php isset( $sonawp_general_settings['sonawp_currency_position'] ) ? selected( $sonawp_general_settings['sonawp_currency_position'], 'right_space' ) : ''; ?>>
									<?php echo esc_html__( 'Right with space : 10.00 $', 'sonawp' ); ?>
								</option>

							</select>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="sonawp_decimal_separator">
								<?php echo esc_html__( 'Decimal Separator', 'sonawp' ); ?>
							</label>
						</th>
						<td>
							<select name="sonawp_general_settings[sonawp_decimal_separator]" id="sonawp_decimal_separator">

								<option value="dot_separator" <?php isset( $sonawp_general_settings['sonawp_decimal_separator'] ) ? selected( $sonawp_general_settings['sonawp_decimal_separator'], 'dot_separator' ) : ''; ?>>
									<?php echo esc_html__( 'Dot: 10.00', 'sonawp' ); ?>
								</option>

								<option value="comma_separator" <?php isset( $sonawp_general_settings['sonawp_decimal_separator'] ) ? selected( $sonawp_general_settings['sonawp_decimal_separator'], 'comma_separator' ) : ''; ?>>
									<?php echo esc_html__( 'Comma: 10,00', 'sonawp' ); ?>
								</option>

							</select>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="sonawp_thousand_separator">
								<?php echo esc_html__( 'Thousand Separator', 'sonawp' ); ?>
							</label>
						</th>
						<td>
							<select name="sonawp_general_settings[sonawp_thousand_separator]" id="sonawp_thousand_separator">

								<option value="none" <?php isset( $sonawp_general_settings['sonawp_thousand_separator'] ) ? selected( $sonawp_general_settings['sonawp_thousand_separator'], 'none' ) : ''; ?>>
									<?php echo esc_html__( 'None', 'sonawp' ); ?>
								</option>

								<option value="comma" <?php isset( $sonawp_general_settings['sonawp_thousand_separator'] ) ? selected( $sonawp_general_settings['sonawp_thousand_separator'], 'comma' ) : ''; ?>>
									<?php echo esc_html__( 'Comma: 1,000', 'sonawp' ); ?>
								</option>

								<option value="dot" <?php isset( $sonawp_general_settings['sonawp_thousand_separator'] ) ? selected( $sonawp_general_settings['sonawp_thousand_separator'], 'dot' ) : ''; ?>>
									<?php echo esc_html__( 'Dot: 1.000', 'sonawp' ); ?>
								</option>

								<option value="space" <?php isset( $sonawp_general_settings['sonawp_thousand_separator'] ) ? selected( $sonawp_general_settings['sonawp_thousand_separator'], 'space' ) : ''; ?>>
									<?php echo esc_html__( 'Space: 1 000', 'sonawp' ); ?>
								</option>

							</select>
						</td>
					</tr>

				</tbody>
			</table>

		</div>

		<div class="stripe-connection tabcontent" id="Stripe">

			<table class="form-table">
				<tbody>

				<tr>
					<th scope="row">
					<label for="sonawp_stripe_mode"><?php echo esc_html__( 'Payment Mode:', 'sonawp' ); ?></label>
					</th>

					<td>
					<fieldset>
						<label for="sonawp_stripe_mode_test" class="sonawp-stripe-mode-test">
							<input type="radio" name="sonawp_stripe_settings[sonawp_stripe_mode]" id="sonawp_stripe_mode_test" class="sonawp_stripe_mode" value="test" <?php echo esc_attr( isset( $sonawp_stripe_settings['sonawp_stripe_mode'] ) ? checked( $sonawp_stripe_settings['sonawp_stripe_mode'], 'test' ) : '' ); ?> >
							<?php echo esc_html__( 'Test', 'sonawp' ); ?>
							
						</label>
						<br>
						<label for="sonawp_stripe_mode_live" class="sonawp-stripe-mode-live">
							<input type="radio" name="sonawp_stripe_settings[sonawp_stripe_mode]" id="sonawp_stripe_mode_live" class="sonawp_stripe_mode" value="live" <?php echo esc_attr( isset( $sonawp_stripe_settings['sonawp_stripe_mode'] ) ? checked( $sonawp_stripe_settings['sonawp_stripe_mode'], 'live' ) : '' ); ?>>
							<?php echo esc_html__( 'Live', 'sonawp' ); ?>
						</label>
					</fieldset>

					<div class="notice notice-info" style="margin: 10px 0; width: 50%; padding:12px;">
						<?php echo esc_html__( 'You can get keys from your ', 'sonawp' ); ?>
						<a href="https://dashboard.stripe.com/" target="_blank">
							<?php echo esc_html__( 'Stripe Dashboard', 'sonawp' ); ?>
						</a>
						<?php echo esc_html__( ', Need more info ', 'sonawp' ); ?>
						<a href="http://sonawp.com/wp-content/uploads/2023/10/sonawp_stripe.mp4" target="_blank">
							<?php echo esc_html__( 'Watch this.', 'sonawp' ); ?>
						</a>
					</div>

					</td>
				</tr>

				<tr class="test-key-fields">
					<th scope="row">
						<label for="sonawp_stripe_test_publishable_key">
							<?php echo esc_html__( 'Test Publishable Key', 'sonawp' ); ?>
						</label>
					</th>
					<td>
						<input class="regular-text" placeholder="Enter Test Publishable Key..." type="text" name="sonawp_stripe_settings[sonawp_stripe_test_publishable_key]" id="sonawp_stripe_test_publishable_key"
								value="<?php echo esc_attr( isset( $sonawp_stripe_settings['sonawp_stripe_test_publishable_key'] ) ? $sonawp_stripe_settings['sonawp_stripe_test_publishable_key'] : '' ); ?>">
					</td>
				</tr>

				<tr class="test-key-fields">
					<th scope="row">
						<label for="sonawp_stripe_test_secret_key">
							<?php echo esc_html__( 'Test Secret Key', 'sonawp' ); ?>
						</label>
					</th>
					<td>
						<input class="regular-text" placeholder="Enter Test Secret Key..." type="password" name="sonawp_stripe_settings[sonawp_stripe_test_secret_key]" id="sonawp_stripe_test_secret_key"
								value="<?php echo esc_attr( isset( $sonawp_stripe_settings['sonawp_stripe_test_secret_key'] ) ? $sonawp_stripe_settings['sonawp_stripe_test_secret_key'] : '' ); ?>">
					</td>
				</tr>

				<tr class="live-key-fields">
					<th scope="row">
						<label for="sonawp_stripe_live_publishable_key">
							<?php echo esc_html__( 'Live Publishable Key', 'sonawp' ); ?>
						</label>
					</th>
					<td>
						<input class="regular-text" placeholder="Enter Live Publishable Key..." type="text" name="sonawp_stripe_settings[sonawp_stripe_live_publishable_key]" id="sonawp_stripe_live_publishable_key"
								value="<?php echo esc_attr( isset( $sonawp_stripe_settings['sonawp_stripe_live_publishable_key'] ) ? $sonawp_stripe_settings['sonawp_stripe_live_publishable_key'] : '' ); ?>">
					</td>
				</tr>

				<tr class="live-key-fields">
					<th scope="row">
						<label for="sonawp_stripe_live_secret_key">
							<?php echo esc_html__( 'Live Secret Key', 'sonawp' ); ?>
						</label>
					</th>
					<td>
						<input class="regular-text" placeholder="Enter Live Secret Key..." type="password" name="sonawp_stripe_settings[sonawp_stripe_live_secret_key]" id="sonawp_stripe_live_secret_key"
								value="<?php echo esc_attr( isset( $sonawp_stripe_settings['sonawp_stripe_live_secret_key'] ) ? $sonawp_stripe_settings['sonawp_stripe_live_secret_key'] : '' ); ?>">
					</td>
				</tr>

				</tbody>
			</table>
		</div>

		<div class="paypal-settings tabcontent" id="Paypal">

			<table class="form-table">
				<tbody>

					<tr>
						<th scope="row">
							<label for="sonawp_paypal_mode"><?php echo esc_html__( 'Payment Mode:', 'sonawp' ); ?></label>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span><?php echo esc_html__( 'Payment Mode', 'sonawp' ); ?></span></legend>
								<label for="sonawp_paypal_mode_test" class="sonawp-paypal-mode-test">
									<input type="radio" name="sonawp_paypal_settings[sonawp_paypal_mode]" id="sonawp_paypal_mode_test" value="test" <?php echo esc_attr( isset( $sonawp_paypal_settings['sonawp_paypal_mode'] ) ? checked( $sonawp_paypal_settings['sonawp_paypal_mode'], 'test' ) : '' ); ?>>
									<?php echo esc_html__( 'Test', 'sonawp' ); ?>
								</label>
								<br>
								<label for="sonawp_paypal_mode_live" class="sonawp-paypal-mode-live">
									<input type="radio" name="sonawp_paypal_settings[sonawp_paypal_mode]" id="sonawp_paypal_mode_live" value="live" <?php echo esc_attr( isset( $sonawp_paypal_settings['sonawp_paypal_mode'] ) ? checked( $sonawp_paypal_settings['sonawp_paypal_mode'], 'live' ) : '' ); ?>>
									<?php echo esc_html__( 'Live', 'sonawp' ); ?>
								</label>
							</fieldset>

							<p class="description">
								<?php echo esc_html__( 'Select the mode in which you want to use PayPal.', 'sonawp' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="sonawp_paypal_email">
								<?php echo esc_html__( 'PayPal Global Email', 'sonawp' ); ?>
							</label>
						</th>
						<td>
							<input class="regular-text"  placeholder="Enter PayPal Merchent Email..." type="email" name="sonawp_paypal_settings[sonawp_paypal_email]" id="sonawp_paypal_email"
								value="<?php echo esc_attr( isset( $sonawp_paypal_settings['sonawp_paypal_email'] ) ? $sonawp_paypal_settings['sonawp_paypal_email'] : '' ); ?>">
								<p class="description">
							</p>
							<div class="notice notice-info" style="margin:10px 0; width: 50%">
								<p>
									<?php echo esc_html__( 'This email will be used for every PayPal block and product, you can override the email for individual block or product in there respective settings. as each PayPal Block/Product can have individual merchent email to receive payments.', 'sonawp' ); ?>
								</p>
							</div>
						</td>
					</tr>

				</tbody>
			</table>
		</div>

		<div class="payment-confirmation-page tabcontent" id="Confirmation">

			<table class="form-table">
				<tbody>

					<tr>
						<th scope="row">
							<label for="sonawp_payment_success_page">
								<?php echo esc_html__( 'Payment Success Page', 'sonawp' ); ?>
							</label>
						</th>

						<td>
							<select name="sonawp_payment_confirmation[sonawp_payment_success_page]" id="sonawp_payment_success_page">
								<option value="">
									<?php echo esc_html__( 'Select Page', 'sonawp' ); ?>
								</option>
								<?php
								foreach ( get_pages() as $pagee ) {
									$selected = isset( $sonawp_payment_confirmation['sonawp_payment_success_page'] ) && $sonawp_payment_confirmation['sonawp_payment_success_page'] == $pagee->ID ? 'selected' : '';
									?>
									<option value="<?php echo esc_attr( $pagee->ID ); ?>" <?php echo $selected; ?>>
										<?php echo esc_html( $pagee->post_title ); ?>
									</option>
								<?php } ?>
							</select>
							<p class="description">Shortcode must be added <code>[sona_payment_success]</code> to save order details.</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="sonawp_confirmation_message">
								<?php echo esc_html__( 'Success Message On Selected page', 'sonawp' ); ?>
							</label>
						</th>

						<td>
							<textarea name="sonawp_payment_confirmation[sonawp_confirmation_message]" id="sonawp_confirmation_message" cols="100"
								rows="5"><?php echo isset( $sonawp_payment_confirmation['sonawp_confirmation_message'] ) ? esc_html( $sonawp_payment_confirmation['sonawp_confirmation_message'] ) : ''; ?></textarea>
							<p class="description">Use
								<code>{sona_product_name}{sona_product_price}{sona_payment_gateway}{sona_payment_mode}{sona_payment_currency}</code> to display Product name, Price, Payment gateway( Stripe/PayPal ), Mode(live/test) and Currency.
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="sonawp_payment_failed_page">
								<?php echo esc_html__( 'Payment Failed Page', 'sonawp' ); ?>
							</label>
						</th>

						<td>
							<select name="sonawp_payment_confirmation[sonawp_payment_failed_page]" id="sonawp_payment_failed_page">
								<option value="">
									<?php echo esc_html__( 'Select Page', 'sonawp' ); ?>
								</option>
								<?php
								foreach ( get_pages() as $pagee ) {
									$selected = isset( $sonawp_payment_confirmation['sonawp_payment_failed_page'] ) && $sonawp_payment_confirmation['sonawp_payment_failed_page'] == $pagee->ID ? 'selected' : '';
									?>
									<option value="<?php echo esc_attr( $pagee->ID ); ?>" <?php echo $selected; ?>>
										<?php echo esc_html( $pagee->post_title ); ?>
									</option>
								<?php } ?>
							</select>
						</td>
					</tr>

				</tbody>
			</table>

		</div>

		<div class="email-settings tabcontent" id="Email">

			<table class="form-table">
				<tbody>

					<tr>
						<th scope="row">
							<label for="sonawp_stripe_order_email">
								<?php echo esc_html__( 'Stripe Email', 'sonawp' ); ?>
							</label>
						</th>
						<td>
							<input class="regular-text" placeholder="Enter Email..." type="email" name="sonawp_email_settings[sonawp_stripe_order_email]" id="sonawp_stripe_order_email"
								value="<?php echo isset( $sonawp_email_settings['sonawp_stripe_order_email'] ) ? esc_html( $sonawp_email_settings['sonawp_stripe_order_email'] ) : ''; ?>">
							<p class="description">
								<?php echo esc_html__( 'Enter Email to receive Stripe order details.', 'sonawp' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="sonawp_paypal_order_email">
								<?php echo esc_html__( 'PayPal Email', 'sonawp' ); ?>
							</label>
						</th>
						<td>
							<input class="regular-text" placeholder="Enter Email..." type="email" name="sonawp_email_settings[sonawp_paypal_order_email]" id="sonawp_paypal_order_email"
								value="<?php echo isset( $sonawp_email_settings['sonawp_paypal_order_email'] ) ? esc_html( $sonawp_email_settings['sonawp_paypal_order_email'] ) : ''; ?>">

							<p class="description">
								<?php echo esc_html__( 'Enter Email to receive PayPal order details. if empty then Global/Individual block email will be used.', 'sonawp' ); ?>
							</p>
							<div class="notice notice-info" style="margin: 10px 0; width: 60%">
								<p>
									<?php echo esc_html__( 'Please note that you will receive email notifications if SMTP or email services are enabled on this website.', 'sonawp' ); ?>
								</p>
							</div>
						</td>
					</tr>

				</tbody>
			</table>

		</div>

		<p class="submit">
			<input type="submit" class="button button-primary" name="save_settings"
			value="<?php echo esc_attr__( 'Save Changes', 'sonawp' ); ?>" />
		</p>

	</form>

</div>
