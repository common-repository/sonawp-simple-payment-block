<?php
/**
 * Dashboard page
 * Since 1.2.3
 * @package SonaWP
 */

$general_settings       = get_option( 'sonawp_general_settings' );
$sonawp_stripe_currency = '';
$sonawp_paypal_currency = '';

if ( is_array( $general_settings ) ) {
	$sonawp_stripe_currency = strtolower( $general_settings['sonawp_stripe_currency'] ?? '' );
	$sonawp_paypal_currency = strtolower( $general_settings['sonawp_paypal_currency'] ?? '' );
}

// if $sonawp_stripe_currency has values inr, ron, sgd, chf, aed, gbp then make it as final_selected_currency else use $sonawp_paypal_currency whereas $sona_stripe_currency is not an array
$final_selected_currency = ( in_array( $sonawp_stripe_currency, array( 'inr', 'ron', 'sgd', 'chf', 'aed', 'gbp' ) ) ) ? $sonawp_stripe_currency : $sonawp_paypal_currency;

$total_price = get_prices_for_days( 'sonaorder', $final_selected_currency, 1 );
$total_price = $total_price[0];

// Query to get count of total number of posts published today in sonaorder post type
global $wpdb;
$today_count = $wpdb->get_var(
	$wpdb->prepare(
		"SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s AND DATE(post_date) = CURDATE()",
		'sonaorder',
		'publish'
	)
);

?>

<div class="sonawp-activity-reports">
	<div class="top-block">
		<div class="today-payments">
			<div class="card-header">
				<h2>Today</h2>
			</div>
			<div class="card-body">
				<div class="reports-stat">
					<h6>Gross Volume</h6>
					<h4><?php echo $total_price . ' ' . strtoupper( $final_selected_currency ); ?></h4>
				</div>
				<!-- <div class="reports-stat">
					<h6>Successful Payments</h6>
					<h4>0</h4>
				</div>
				<div class="reports-stat">
					<h6>Customers</h6>
					<h4>0</h4>
				</div> -->
				<div class="reports-stat">
					<h6>All Payments</h6>
					<h4><?php echo isset( $today_count ) ? esc_html( $today_count ) : ''; ?></h4>
				</div>
			</div>
		</div>
		<div class="latest-payments">
			<div class="card-header">
				<h2>Latest Payments</h2>
				<div class="link-block">
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=sonaorder' ) ); ?>">View All</a>
					<span>
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 33 34" fill="none">
							<path
								d="M25.9636 6.31936L14.5022 6.31936C14.0344 6.31936 13.6835 6.67022 13.6835 7.13803C13.6835 7.60585 14.0344 7.95671 14.5022 7.95671L23.9754 7.95671L5.73071 26.2014L6.90024 27.3709L25.1449 9.12624L25.1449 18.5995C25.1449 19.0673 25.4958 19.4181 25.9636 19.4181C26.1975 19.4181 26.373 19.3596 26.5484 19.1842C26.7238 19.0088 26.7823 18.8334 26.7823 18.5995L26.7823 7.13803C26.7823 6.67022 26.4314 6.31936 25.9636 6.31936Z"
								fill="#3858e9" />
						</svg>
					</span>
				</div>
			</div>
			<div class="card-body">
				<?php

				global $wpdb;
				$query = $wpdb->prepare(
					"SELECT p.post_title, pm1.meta_value, pm2.meta_value as meta_value2, p.post_date, p.ID
					FROM {$wpdb->postmeta} pm1
					INNER JOIN {$wpdb->postmeta} pm2 ON pm1.post_id = pm2.post_id
					INNER JOIN {$wpdb->posts} p ON pm1.post_id = p.ID
					WHERE p.post_type = %s
					AND pm1.meta_key = 'sona_filter_price'
					AND pm2.meta_key = 'sona_filter_currency'
					ORDER BY p.post_date DESC
					LIMIT 5
					",
					'sonaorder'
				);

				$results = $wpdb->get_results( $query );

				if ( ! empty( $results ) ) {
					foreach ( $results as $result ) {
						$post_date = human_time_diff( strtotime( $result->post_date ), current_time( 'timestamp' ) ) . ' ago';
						?>
						<div class="succeeded-block">
							<h4>
								<?php echo $result->meta_value; ?> <span><?php echo strtoupper( $result->meta_value2 ); ?></span>
							</h4>
							<a href="<?php echo esc_url( admin_url( 'post.php?post=' . $result->ID . '&action=edit' ) ); ?>" class="email"><?php echo $result->post_title; ?></a>
						
							<a href="<?php echo esc_url( admin_url( 'post.php?post=' . $result->ID . '&action=edit' ) ); ?>"><?php echo $post_date; ?></a>
							<h4 class="button button-secondary">
								Succeeded
								<span>
									<svg fill="#2fb122" width="15px" height="16px" viewBox="0 0 30 30"
										xmlns="http://www.w3.org/2000/svg">
										<path
											d="M.5 14a.5.5 0 0 0-.348.858l9.988 9.988a.5.5 0 1 0 .706-.706L.858 14.152A.5.5 0 0 0 .498 14zm28.99-9c-.13.004-.254.057-.345.15L12.163 22.13c-.49.47.236 1.197.707.707l16.982-16.98c.324-.318.077-.857-.363-.857z" />
									</svg>
								</span>
							</h4>
						</div>
						<?php
					}
				} else {
					?>
						<div class="transactions-block">
							<h4>No USD transactions found</h4>
							<p>Please select a different currency or check back later.</p>
						</div>
					<?php
				}
				?>
			</div>
		</div>
	</div>
	<div class="bottom-block">
		<div class="search-box">
			<h2>Reports</h2>
			<form action="" method="post">
				<select name="date" id="filter">
					<option value="last7" selected>Last 7 Days</option> <!-- Set as the default -->
					<option value="last30">Last 30 Days</option>
					<!-- <option value="last12">Last 12 Months</option>
					<option value="month-to-date">Month to Date</option>
					<option value="year-to-date">Year to Date</option> -->
				</select>
				<select name="filter_currency" id="currency"> <!-- Added an ID for the currency select element -->
				<option value="usd" selected>United States ($)</option>
					<option value="eur">Euro (€)</option>
					<option value="aud">Australia ($)</option>
					<option value="brl">Brazil (R$)</option>
					<option value="cad">Canada ($)</option>
					<option value="czk">Czech Republic (Kč)</option>
					<option value="dkk">Denmark (kr)</option>
					<option value="hkd">Hong Kong ($)</option>
					<option value="huf">Hungary (Ft)</option>
					<option value="inr">India (₹)</option>
					<option value="jpy">Japan (¥)</option>
					<option value="myr">Malaysia (RM)</option>
					<option value="mxn">Mexico ($)</option>
					<option value="nzd">New Zealand ($)</option>
					<option value="nok">Norway (kr)</option>
					<option value="pln">Poland (zł)</option>
					<option value="ron">Romania (lei)</option>
					<option value="sgd">Singapore ($)</option>
					<option value="sek">Sweden (kr)</option>
					<option value="chf">Switzerland (CHF)</option>
					<option value="thb">Thailand (฿)</option>
					<option value="aed">United Arab Emirates (د.إ)</option>
					<option value="gbp">United Kingdom (£)</option>
				</select>
			</form>
		</div>
		<div>
			<canvas id="myChart"></canvas>
		</div>
	</div>
</div>
