<?php
/**
 * Sonawp shortcodes
 * @package sonawp
 */

/**
 * shortcode name: sonawp
 * Description: used to display front end of product
 * sample: [sonawp product_id=""]
 */

add_shortcode( 'sonawp', 'sonawp_product_shortcode' );

function sonawp_product_shortcode( $atts ) {

	$atts = shortcode_atts(
		array(
			'product_id' => '',
		),
		$atts,
		'sonawp'
	);

	$block_id = $atts['product_id'];

	if ( get_post_status( $block_id ) !== 'publish' ) {
		return;
	}

	$sona_product = get_post( $block_id );

	$images = get_post_meta( $block_id, 'sona_product_image_url', true );

	$title           = $sona_product->post_title;
	$description     = get_post_meta( $block_id, 'sona_product_description', true );
	$price           = get_post_meta( $block_id, 'sona_price', true );
	$sonawp_currency = get_option( 'sonawp_currency' );
	$payment_gateway = get_post_meta( $block_id, 'sona_payment_gateway', true );

	$content = sonawp_product_frontend( $block_id, $images, $title, $description, $price, $sonawp_currency, $payment_gateway );

	return $content;
}
