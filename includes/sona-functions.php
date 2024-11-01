<?php
/**
 * Sonawp Functions
 * sonawp_product_frontend() function is used to display front end of product
 * charts_price_and_currency() function is used to get prices for charts
 * get_prices_for_days() function is used to get prices for charts
 * @package sonawp
 */

/**
 * sonawp_product_frontend() function is used to display front end of product
 * used in files: includes/sona-shortcodes.php, icludes/classes/class-sona-paypal-block.php, includes/classes/class-sona-stripe-block.php
* @param int $block_id
* @param array $images
* @param string $title
* @param string $description
* @param string $price
* @param string $sonawp_currency
* @param string $payment_gateway
* @return string $content
 */

function sonawp_product_frontend( $block_id, $images = array(), $title = '', $description = '', $price = '', $sonawp_currency = '', $payment_gateway = '' ) {

	if ( 'paypal' === $payment_gateway ) {
		$currency = array(
			'USD' => '$',
			'EUR' => '€',
			'AUD' => 'A$',
			'BRL' => 'R$',
			'CAD' => 'C$',
			'CZK' => 'Kč',
			'DKK' => 'kr',
			'HKD' => 'HK',
			'HUF' => 'Ft',
			'ILS' => '₪',
			'JPY' => '¥',
			'MYR' => 'RM',
			'MXN' => 'MX',
			'TWD' => 'NT',
			'NZD' => 'NZ',
			'NOK' => 'kr',
			'PHP' => '₱',
			'PLN' => 'zł',
			'GBP' => '£',
			'RUB' => '₽',
			'SGD' => 'S$',
			'SEK' => 'kr',
			'CHF' => 'CHf',
			'THB' => '฿',
		);

	} elseif ( 'stripe' === $payment_gateway ) {
		$currency = array(
			'usd' => '$',
			'aud' => '$',
			'eur' => '€',
			'brl' => 'R$',
			'cad' => '$',
			'czk' => 'Kč',
			'dkk' => 'kr',
			'hkd' => '$',
			'huf' => 'Ft',
			'inr' => '₹',
			'jpy' => '¥',
			'myr' => 'RM',
			'nzd' => '$',
			'nok' => 'kr',
			'pln' => 'zł',
			'ron' => 'lei',
			'sgd' => '$',
			'sek' => 'kr',
			'chf' => 'CHF',
			'thb' => '฿',
			'aed' => 'د.إ',
			'gbp' => '£',
		);
	}

	$sonawp_general_settings = get_option( 'sonawp_general_settings' );
	if ( ! empty( $sonawp_general_settings && ! empty( $price ) ) ) {

		if ( 'paypal' === $payment_gateway ) {
			$sonawp_currency = $sonawp_general_settings['sonawp_paypal_currency'];
		} elseif ( 'stripe' === $payment_gateway ) {
			$sonawp_currency = $sonawp_general_settings['sonawp_stripe_currency'];
		}

		$sonawp_decimal_separator  = $sonawp_general_settings['sonawp_decimal_separator']; // dot_separator, comma_separator
		$sonawp_thousand_separator = $sonawp_general_settings['sonawp_thousand_separator']; // none, comma, dot, space

		$numeric_value = $price;

		$decimal_separator  = '.';
		$thousand_separator = '';

		if ( 'comma_separator' === $sonawp_decimal_separator ) {
			$decimal_separator = ',';
		}

		if ( 'comma' === $sonawp_thousand_separator ) {
			$thousand_separator = ',';
		} elseif ( 'dot' === $sonawp_thousand_separator ) {
			$thousand_separator = '.';
		} elseif ( 'space' === $sonawp_thousand_separator ) {
			$thousand_separator = ' ';
		}

		$final_price = number_format( $numeric_value, 2, $decimal_separator, $thousand_separator );

		$currency_position = $sonawp_general_settings['sonawp_currency_position'];
		$final_currency    = $currency[ $sonawp_currency ] . $final_price;

		switch ( $currency_position ) {
			case 'left':
				$final_currency = $currency[ $sonawp_currency ] . $final_price;
				break;
			case 'right':
				$final_currency = $price . $currency[ $sonawp_currency ];
				break;
			case 'left_space':
				$final_currency = $currency[ $sonawp_currency ] . ' ' . $final_price;
				break;
			case 'right_space':
				$final_currency = $final_price . ' ' . $currency[ $sonawp_currency ];
				break;
			default:
				$final_currency = $currency[ $sonawp_currency ] . $final_price;
		}
	} elseif ( ! empty( $price ) && empty( $sonawp_general_settings ) ) {
		$final_currency = 'USD ' . $price;
	} else {
		$final_currency = '';
	}

	$unique_id = uniqid();

	$content  = '<div class="sona-payments-product-wrapper-' . $payment_gateway . '" data-unique="' . $unique_id . '" data-id="' . $block_id . '">';
	$content .= '<div class="sona-payments-product">';
	if ( isset( $images ) && count( $images ) > 0 ) {
		$content .= '<div class="sona-payments-product-image">';
		$content .= '<div class="f-carousel" id="myCarousel-' . $block_id . '">';
		foreach ( $images as $image ) {
			$content .= '<div class="f-carousel__slide" data-thumb-src="' . $image['url'] . '">';
			$content .= '<a href="' . $image['url'] . '" data-fancybox="gallery-' . $block_id . '">';
			$content .= '<img width="640" height="480" alt="" data-lazy-src="' . $image['url'] . '" />';
			$content .= '</a>';
			$content .= '</div>';
		}
		$content .= '</div>';
		$content .= '</div>';
	}
	$content .= '<div class="sona-payments-details">';
	$content .= '<div class="sona-payments-title"><h4>' . $title . '</h4></div>';
	$content .= '<div class="sona-payments-description"><p>' . $description . '</p></div>';
	$content .= '<div class="sona-payments-price">';
	$content .= '<h5 class="sona-payments-currency">';
	$content .= $final_currency;
	$content .= '</h5>';
	$content .= '</div>';
	$content .= '<div class="sona-payments-purchase-box" id="sona-' . $block_id . '">';

	if ( 'paypal' === $payment_gateway ) {
		$content .= '<div class="sona-payments-button" id="sona-' . $unique_id . '"></div>';
	} elseif ( 'stripe' === $payment_gateway ) {
		$content .= '<form method="POST">
							<input type="hidden" name="product_id" value="' . $block_id . '">
							<button name="sona_stripe" type="submit" class="stripe-btn stripe-btn-rounded stripe-btn-fill">Buy Now</button>
						</form>';
	}

	$content .= '</div>';
	$content .= '</div>';
	$content .= '</div>';
	$content .= '</div>';

	return $content;
}

/**
 * charts_price_and_currency() function is used to get prices for charts
 * used in files: includes/sona-orders-cpt.php
 * @param string $post_type
 * @param string $time_frame
 * @param string $currency
 * @return array $prices
 */

function charts_price_and_currency( $post_type, $time_frame, $currency ) {

	$prices = array();

	if ( 'last7' === $time_frame ) {
		$prices = get_prices_for_days( $post_type, $currency, 7 );
	} elseif ( 'last30' === $time_frame ) {
		$prices = get_prices_for_days( $post_type, $currency, 30 );
	}

	return $prices;
}

function get_prices_for_days( $post_type, $currency, $days ) {
	global $wpdb;

	$prices = array();

	for ( $i = $days - 1; $i >= 0; $i-- ) {
		$date        = gmdate( 'Y-m-d', strtotime( "-$i days" ) );
		$total_price = 0;

		$query = $wpdb->prepare(
			"SELECT SUM(pm1.meta_value)
			FROM {$wpdb->postmeta} pm1
			INNER JOIN {$wpdb->postmeta} pm2 ON pm1.post_id = pm2.post_id
			INNER JOIN {$wpdb->posts} p ON pm1.post_id = p.ID
			WHERE p.post_type = %s
			AND pm1.meta_key = 'sona_filter_price'
			AND pm1.meta_value != ''
			AND pm1.meta_value IS NOT NULL
			AND pm1.meta_value != '0'
			AND pm2.meta_key = 'sona_filter_currency'
			AND pm2.meta_value = %s
			AND p.post_date >= %s
			AND p.post_date < %s
			",
			$post_type,
			$currency,
			$date,
			gmdate( 'Y-m-d', strtotime( '+1 day', strtotime( $date ) ) )
		);

		$result = $wpdb->get_var( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		if ( $result ) {
			$total_price = floatval( $result );
		}

		$prices[] = $total_price;
	}

	return $prices;
}
