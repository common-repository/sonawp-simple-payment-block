<?php
/**
 * Sonawp Emails
 * Current: Order emails to merchent and customer
 * @package sonawp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function sonawp_send_buyer_email( $order_id, $order_created_time, $order_product_name, $order_amount, $order_shipping_address, $order_shipping_address_admin_area_1, $order_shipping_address_admin_area_2, $order_payer_email_address ) {
	$to      = $order_payer_email_address;
	$subject = 'You have successfully placed an order!';
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	$message = '
	<div style="
				display: flex;
				align-items: center;
				flex-direction: column;
				max-width: 500px;
				margin: 0px auto;
				box-shadow: 0px 0px 15px rgb(0 0 0 / 10%);
				padding: 0px 0px 30px 0px;
			">
		<div style="text-align: center; width: 500px; background: #2827CC;">
			<h2 style="color:white;padding:15px;">Order # ' . $order_id . '</h2>
		</div>

		<div style="width: 450px;">
			<p style="padding-top: 5px;">The order is as follows: </p>
			<h4 style="color:#2827CC;"> Order (' . $order_created_time . ')</h4>
		</div>

		<table border="0" cellpadding="0" cellspacing="0" style="border-width: 0 0 2px 2px;border-style: solid;border-color: #e6e6e6;width: 450px;">
			<thead>
				<tr>
					<th style="font-weight:bold; border-width: 2px 2px 0 0;border-style: solid;border-color: #e6e6e6;padding: 10px;
						text-align: left;">Product</th>
					<th style=" border-width: 2px 2px 0 0; border-style: solid; border-color: #e6e6e6; padding: 10px; 
						text-align: left; ">Quantity</th>
					<th style="border-width: 2px 2px 0 0; border-style: solid; border-color: #e6e6e6; padding: 10px;">Price</th>
				</tr>
			</thead>

			<tbody>
				<tr>
					<td rowspan="1" style="border-width: 2px 2px 0 0;border-style: solid;border-color: #e6e6e6;padding: 10px;">' . $order_product_name . '</td>
					<td rowspan="1" style="border-width: 2px 2px 0 0;border-style: solid;border-color: #e6e6e6;padding: 10px;">1</td>
					<td rowspan="1" style="border-width: 2px 2px 0 0;border-style: solid;border-color: #e6e6e6;padding: 10px;">$' . $order_amount . '</th>
				</tr>
				<tr>
					<td colspan="2" style="border-width: 2px 2px 0 0;border-style: solid;border-color: #e6e6e6;padding: 10px;">Total: </th>
					<td style="border-width: 2px 2px 0 0;border-style: solid;border-color: #e6e6e6;padding: 10px;">$' . $order_amount . '</td>
				</tr>

			</tbody>
		</table>

		<div>
			<h4 style="color:#2827CC; padding-top: 10px;"> Billing Address </h4>
			<div style="width:450px; height: 100px; border:1px solid lightgray; padding: 0px 10px 10px;">
				<p>' . $order_shipping_address . '</p>
				<p>' . $order_shipping_address_admin_area_1 . '</p>
				<p>' . $order_shipping_address_admin_area_2 . '</p>
			</div>
			<p style="padding-top: 20px;"> Congratulations! </p>
		</div>
	</div>
	';

	wp_mail( $to, $subject, $message, $headers );
}

function sonawp_send_merchant_email( $order_id, $order_created_time, $order_product_name, $order_amount, $order_merchant_email, $order_payer_email_address, $order_shipping_address, $order_shipping_address_admin_area_1, $order_shipping_address_admin_area_2 ) {
		$to_merchent  = $order_merchant_email;
	$subject_mechent  = 'You have received an order!';
	$headers_merchent = array( 'Content-Type: text/html; charset=UTF-8' );
	$message_merchent = '
	<div style="
				display: flex;
				align-items: center;
				flex-direction: column;
				max-width: 500px;
				margin: 0px auto;
				box-shadow: 0px 0px 15px rgb(0 0 0 / 10%);
				padding: 0px 0px 30px 0px;
			">
		<div style="text-align: center; width: 500px; background: #2827CC;">
			<h2 style="color:white;padding:15px;">Order # ' . $order_id . '</h2>
		</div>

		<div style="width: 450px;">
			<p style="padding-top: 5px;">The order is as follows: </p>
			<h4 style="color:#2827CC;"> Order (' . $order_created_time . ')</h4>
		</div>

		<table border="0" cellpadding="0" cellspacing="0" style="border-width: 0 0 2px 2px;border-style: solid;border-color: #e6e6e6;width: 450px;">
			<thead>
				<tr>
					<th style="font-weight:bold; border-width: 2px 2px 0 0;border-style: solid;border-color: #e6e6e6;padding: 10px;
						text-align: left;">Product</th>
					<th style=" border-width: 2px 2px 0 0; border-style: solid; border-color: #e6e6e6; padding: 10px; 
						text-align: left; ">Quantity</th>
					<th style="border-width: 2px 2px 0 0; border-style: solid; border-color: #e6e6e6; padding: 10px;">Price</th>
				</tr>
			</thead>

			<tbody>
				<tr>
					<td rowspan="1" style="border-width: 2px 2px 0 0;border-style: solid;border-color: #e6e6e6;padding: 10px;">' . $order_product_name . '</td>
					<td rowspan="1" style="border-width: 2px 2px 0 0;border-style: solid;border-color: #e6e6e6;padding: 10px;">1</td>
					<td rowspan="1" style="border-width: 2px 2px 0 0;border-style: solid;border-color: #e6e6e6;padding: 10px;">$' . $order_amount . '</th>
				</tr>
				<tr>
					<td colspan="2" style="border-width: 2px 2px 0 0;border-style: solid;border-color: #e6e6e6;padding: 10px;">Total: </th>
					<td style="border-width: 2px 2px 0 0;border-style: solid;border-color: #e6e6e6;padding: 10px;">$' . $order_amount . '</td>
				</tr>

			</tbody>
		</table>

		<div style="width:450px;">
			<h4 style="color:#2827CC; padding-top: 20px;"> Customer Details </h4>
			<ul>
				<li> Email Address: ' . $order_payer_email_address . '</li>
			</ul>
		</div>

		<div style="width:450px;">
			<h4 style="color:#2827CC; padding-top: 10px;"> Billing Address </h4>
			<div style="width:450px; height: 100px; border:1px solid lightgray; padding: 0px 10px 10px;">
				<p>' . $order_shipping_address . '</p>
				<p>' . $order_shipping_address_admin_area_1 . '</p>
				<p>' . $order_shipping_address_admin_area_2 . '</p>
			</div>
			<p style="padding-top: 20px;"> Congratulations! </p>
		</div>
	</div>
	';

	wp_mail( $to_merchent, $subject_mechent, $message_merchent, $headers_merchent );
}
