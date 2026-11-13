<?php
/**
 * Fixture for tests/e2e/notification-outcomes.spec.js and
 * tests/e2e/as-built-screens.spec.js, run with
 * `wp eval-file` inside the wp-env `cli` container.
 *
 * The notification handlers of the card, Bizum and Google Pay gateways end
 * in a bare `exit` on several paths (amount mismatch, already paid), so
 * those outcomes cannot be driven inside PHPUnit: the test runner would end
 * with them (L-012). This fixture prepares what a real HTTP notification
 * needs — an order, and parameters signed the way Redsys signs them — and
 * reads the order back afterwards. The POST itself is made by the spec.
 *
 * Usage:
 *   wp eval-file <this file> make <gateway id> <order status> <Ds_Response> <Ds_Amount|match>
 *   wp eval-file <this file> read <order id>
 *   wp eval-file <this file> orderdo <gateway id> <processing|completed>
 *   wp eval-file <this file> urls <order id>
 *
 * Every answer is one line: `E2E-JSON:` followed by a JSON object.
 *
 * Dev-only: `tests/` is export-ignored and never ships.
 *
 * @package WooCommerce Redsys Gateway Light
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Print the fixture's answer in the one-line form the spec parses.
 *
 * @param array $data Answer.
 * @return void
 */
function redsyslite_e2e_fixture_answer( $data ) {
	echo 'E2E-JSON:' . wp_json_encode( $data ) . "\n";
}

/**
 * The secret a notification for this gateway is verified against in the
 * playground's configuration (test mode, guest order).
 *
 * @param WC_Payment_Gateway $gateway Gateway instance.
 * @return string
 */
function redsyslite_e2e_fixture_secret( $gateway ) {
	// Bizum and Google Pay resolve it per user; a guest order uses user 0.
	if ( method_exists( $gateway, 'get_redsys_sha256' ) ) {
		return (string) $gateway->get_redsys_sha256( 0 );
	}
	if ( 'yes' === $gateway->testmode && ! empty( $gateway->customtestsha256 ) ) {
		return (string) $gateway->customtestsha256;
	}
	return (string) $gateway->secretsha256;
}

/**
 * What the spec compares before and after a notification.
 *
 * @param WC_Order $order Order.
 * @return array
 */
function redsyslite_e2e_fixture_snapshot( $order ) {
	$notes = array();
	foreach ( wc_get_order_notes( array( 'order_id' => $order->get_id() ) ) as $note ) {
		$notes[] = $note->content;
	}
	$meta = array();
	foreach ( array(
		'_payment_order_number_redsys',
		'_payment_date_redsys',
		'_payment_hour_redsys',
		'_order_fuc_redsys',
		'_authorisation_code_redsys',
		'_card_country_redsys',
		'_card_type_redsys',
		'_redsys_error_payment_ds_response_value',
	) as $key ) {
		$meta[ $key ] = (string) $order->get_meta( $key, true );
	}
	return array(
		'order_id'  => $order->get_id(),
		'status'    => $order->get_status(),
		'total'     => $order->get_total(),
		'date_paid' => $order->get_date_paid() ? $order->get_date_paid()->getTimestamp() : null,
		'notes'     => $notes,
		'meta'      => $meta,
	);
}

$redsyslite_e2e_action = isset( $args[0] ) ? $args[0] : '';

if ( 'make' === $redsyslite_e2e_action ) {
	list( , $redsyslite_e2e_gateway_id, $redsyslite_e2e_status, $redsyslite_e2e_response, $redsyslite_e2e_amount ) = $args;

	$redsyslite_e2e_gateways = WC()->payment_gateways()->payment_gateways();
	if ( empty( $redsyslite_e2e_gateways[ $redsyslite_e2e_gateway_id ] ) ) {
		WP_CLI::error( 'Unknown gateway: ' . $redsyslite_e2e_gateway_id );
	}
	$redsyslite_e2e_gateway = $redsyslite_e2e_gateways[ $redsyslite_e2e_gateway_id ];

	$redsyslite_e2e_order = wc_create_order();
	$redsyslite_e2e_order->add_product( wc_get_product( 10 ), 1 );
	$redsyslite_e2e_order->set_payment_method( $redsyslite_e2e_gateway_id );
	$redsyslite_e2e_order->calculate_totals();
	$redsyslite_e2e_order->set_status( $redsyslite_e2e_status );
	$redsyslite_e2e_order->save();

	// The same call the payment form makes: it also stores the mapping the
	// handler resolves the order from.
	$redsyslite_e2e_ds_order = WCRedL()->prepare_order_number( $redsyslite_e2e_order->get_id() );
	$redsyslite_e2e_match    = ltrim( number_format( (float) $redsyslite_e2e_order->get_total(), 2, '', '' ), '0' );

	$redsyslite_e2e_param = base64_encode( // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		wp_json_encode(
			array(
				'Ds_Date'              => '06%2F10%2F2026',
				'Ds_Hour'              => '21%3A15',
				'Ds_Amount'            => 'match' === $redsyslite_e2e_amount ? $redsyslite_e2e_match : $redsyslite_e2e_amount,
				'Ds_Currency'          => '978',
				'Ds_Order'             => $redsyslite_e2e_ds_order,
				'Ds_MerchantCode'      => '999008881',
				'Ds_Terminal'          => '1',
				'Ds_Response'          => $redsyslite_e2e_response,
				'Ds_TransactionType'   => '0',
				'Ds_SecurePayment'     => '1',
				'Ds_AuthorisationCode' => '123456',
				'Ds_Card_Country'      => '724',
				'Ds_Card_Type'         => 'C',
			)
		)
	);
	$redsyslite_e2e_param = strtr( $redsyslite_e2e_param, '+/', '-_' );
	$redsyslite_e2e_api   = new RedsysLiteAPI();

	redsyslite_e2e_fixture_answer(
		array(
			'ds_order' => $redsyslite_e2e_ds_order,
			'match'    => $redsyslite_e2e_match,
			'before'   => redsyslite_e2e_fixture_snapshot( $redsyslite_e2e_order ),
			'form'     => array(
				'Ds_SignatureVersion'   => 'HMAC_SHA256_V1',
				'Ds_MerchantParameters' => $redsyslite_e2e_param,
				'Ds_Signature'          => $redsyslite_e2e_api->create_merchant_signature_notif( redsyslite_e2e_fixture_secret( $redsyslite_e2e_gateway ), $redsyslite_e2e_param ),
			),
		)
	);
} elseif ( 'read' === $redsyslite_e2e_action ) {
	redsyslite_e2e_fixture_answer( redsyslite_e2e_fixture_snapshot( wc_get_order( (int) $args[1] ) ) );
} elseif ( 'orderdo' === $redsyslite_e2e_action ) {
	$redsyslite_e2e_option   = 'woocommerce_' . $args[1] . '_settings';
	$redsyslite_e2e_settings = get_option( $redsyslite_e2e_option, array() );
	$redsyslite_e2e_previous = isset( $redsyslite_e2e_settings['orderdo'] ) ? $redsyslite_e2e_settings['orderdo'] : '';

	$redsyslite_e2e_settings['orderdo'] = $args[2];
	update_option( $redsyslite_e2e_option, $redsyslite_e2e_settings );
	redsyslite_e2e_fixture_answer( array( 'previous' => $redsyslite_e2e_previous ) );
} elseif ( 'urls' === $redsyslite_e2e_action ) {
	// Where a person finds this order: the admin screen and the order-received page.
	$redsyslite_e2e_order = wc_get_order( (int) $args[1] );
	redsyslite_e2e_fixture_answer(
		array(
			'edit'     => $redsyslite_e2e_order->get_edit_order_url(),
			'received' => $redsyslite_e2e_order->get_checkout_order_received_url(),
			'site'     => get_site_url(),
			'name'     => get_bloginfo( 'name' ),
		)
	);
} else {
	WP_CLI::error( 'Unknown action: ' . $redsyslite_e2e_action );
}
