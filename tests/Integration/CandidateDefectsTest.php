<?php
/**
 * Reproduction tests for the candidate defects recorded in
 * docs/decisions.md D-041 (slice S-035). Each one was written and seen
 * failing against the code as it was before its fix.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

/**
 * Stand-in for WC_Logger that keeps every line it is given.
 */
class Redsyslite_Test_Recording_Logger {

	/**
	 * @var string[]
	 */
	public $lines = array();

	public function add( $handle, $message ) {
		$this->lines[] = (string) $message;
		return true;
	}
}

class CandidateDefectsTest extends WP_UnitTestCase {

	/**
	 * @var string
	 */
	private $secret;

	/**
	 * @var int
	 */
	private $http_calls = 0;

	/**
	 * @var string[]
	 */
	private $transients = array();

	public function set_up() {
		parent::set_up();
		$this->secret     = base64_encode( str_repeat( 'S', 24 ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$this->http_calls = 0;
		$this->transients = array();
	}

	public function tear_down() {
		remove_all_filters( 'pre_http_request' );
		remove_all_filters( 'woocommerce_is_checkout' );
		foreach ( $this->transients as $transient ) {
			delete_transient( $transient );
		}
		delete_option( 'woocommerce_googlepayredirecredsys_settings' );
		WC()->payment_gateways()->init();
		if ( null !== WC()->cart ) {
			WC()->cart->set_total( 0 );
		}
		unset( $_GET['Ds_MerchantParameters'], $_GET['Ds_Signature'], $_GET['Ds_SignatureVersion'] );
		parent::tear_down();
	}

	private function count_http_requests() {
		add_filter(
			'pre_http_request',
			function () {
				++$this->http_calls;
				return new WP_Error( 'redsyslite_test_stub', 'stubbed for test' );
			}
		);
	}

	private function order_with_transaction( $total ) {
		$order = wc_create_order();
		$order->set_total( $total );
		$order->update_meta_data( '_payment_order_number_redsys', '000000' . $order->get_id() );
		$order->save();
		$this->transients[] = 'redys_order_temp_000000' . $order->get_id();
		return $order;
	}

	private function mapped_order( $ds_order, $total ) {
		$order = wc_create_order();
		$order->set_total( $total );
		$order->save();
		set_transient( 'redys_order_temp_' . $ds_order, $order->get_id(), 3600 );
		$this->transients[] = 'redys_order_temp_' . $ds_order;
		return $order;
	}

	private function signed_params( $ds_order, $amount ) {
		$json  = wp_json_encode(
			array(
				'Ds_Order'        => $ds_order,
				'Ds_Response'     => '0000',
				'Ds_Amount'       => $amount,
				'Ds_MerchantCode' => '999999999',
			)
		);
		$param = strtr( base64_encode( $json ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$api   = new RedsysLiteAPI();

		return array(
			'Ds_MerchantParameters' => $param,
			'Ds_Signature'          => $api->create_merchant_signature_notif( $this->secret, $param ),
			'Ds_SignatureVersion'   => 'HMAC_SHA256_V1',
		);
	}

	/**
	 * D-041 item 3: a refund amount of 0 was read as "no amount given" and
	 * the gateway asked Redsys to refund the whole order.
	 *
	 * @dataProvider redsys_protocol_gateways
	 */
	public function test_a_refund_of_zero_never_asks_redsys_for_anything( $gateway_class ) {
		$order = $this->order_with_transaction( 10 );
		$this->count_http_requests();

		$gateway = new $gateway_class();
		$result  = $gateway->process_refund( $order->get_id(), 0 );

		$this->assertSame( 0, $this->http_calls, 'A refund of 0 must not send a refund request to Redsys (it used to send one for the full order total).' );
		$this->assertInstanceOf( 'WP_Error', $result );
	}

	/**
	 * Guard for the fix above: leaving the amount out still means the full
	 * total, and a real amount still reaches Redsys.
	 *
	 * @dataProvider redsys_protocol_gateways
	 */
	public function test_a_refund_with_an_amount_or_without_one_still_asks_redsys( $gateway_class ) {
		$order = $this->order_with_transaction( 10 );
		$this->count_http_requests();

		$gateway = new $gateway_class();
		$gateway->process_refund( $order->get_id(), 4 );
		$gateway->process_refund( $order->get_id() );

		$this->assertSame( 2, $this->http_calls );
	}

	public function redsys_protocol_gateways() {
		return array(
			'card'       => array( 'WC_Gateway_redsys' ),
			'Bizum'      => array( 'WC_Gateway_Bizum_Redsys' ),
			'Google Pay' => array( 'WC_Gateway_GooglePay_Redirection_Redsys' ),
		);
	}

	/**
	 * D-041 item 4: the Bizum transaction limit compared integers, so a
	 * limit with cents was cut down and a total equal to the limit hid the
	 * gateway.
	 *
	 * @dataProvider bizum_limit_cases
	 */
	public function test_bizum_transaction_limit_compares_real_amounts( $limit, $total, $offered ) {
		add_filter( 'woocommerce_is_checkout', '__return_true' );
		if ( null === WC()->cart ) {
			wc_load_cart();
		}
		WC()->cart->set_total( $total );

		$gateway                   = new WC_Gateway_Bizum_Redsys();
		$gateway->debug            = 'no';
		$gateway->transactionlimit = $limit;

		$available = $gateway->disable_bizum( array( 'bizumredsys' => $gateway ) );

		$this->assertSame( $offered, isset( $available['bizumredsys'] ) );
	}

	public function bizum_limit_cases() {
		return array(
			'total equal to the limit is allowed'         => array( '200', '200.00', true ),
			'total under a limit that has cents'          => array( '200.50', '200.25', true ),
			'guard: a total over the limit is still refused' => array( '200', '200.50', false ),
			'total well under the limit'                  => array( '200', '10.00', true ),
			'no limit set'                                => array( '', '5000.00', true ),
		);
	}

	/**
	 * D-041 item 5: with debug logging on, the notification handler wrote
	 * the signing secret into the log in clear.
	 *
	 * @dataProvider gateways_that_logged_the_secret
	 */
	public function test_debug_logging_never_writes_the_signing_secret( $gateway_class, $ds_order ) {
		$order = $this->mapped_order( $ds_order, 1.00 );
		set_transient( 'redsys_signature_' . $ds_order, $this->secret, 3600 );
		$this->transients[] = 'redsys_signature_' . $ds_order;

		$gateway               = new $gateway_class();
		$gateway->secretsha256 = $this->secret;
		$gateway->testmode     = 'no';
		$gateway->debug        = 'yes';
		$gateway->log          = new Redsyslite_Test_Recording_Logger();

		$gateway->successful_request( $this->signed_params( $ds_order, '100' ) );

		$this->assertNotEmpty( $gateway->log->lines, 'The handler is expected to log when debug is on; an empty log would make this test prove nothing.' );
		foreach ( $gateway->log->lines as $line ) {
			$this->assertStringNotContainsString( $this->secret, $line, 'The signing secret must never be written to the debug log.' );
			$this->assertStringNotContainsString( 'create_merchant_signature_notif', $line, 'The locally computed signature must not be logged either.' );
		}
		$this->assertTrue( wc_get_order( $order->get_id() )->has_status( array( 'processing', 'completed' ) ) );
	}

	/**
	 * Same defect on the refund path: asking Redsys for a refund logged
	 * the secret it was about to sign with.
	 *
	 * @dataProvider redsys_protocol_gateways
	 */
	public function test_debug_logging_of_a_refund_never_writes_the_signing_secret( $gateway_class ) {
		$order = $this->order_with_transaction( 10 );
		$order->update_meta_data( '_redsys_secretsha256', $this->secret );
		$order->save();
		$this->count_http_requests();

		$gateway               = new $gateway_class();
		$gateway->secretsha256 = $this->secret;
		$gateway->testmode     = 'no';
		$gateway->debug        = 'yes';
		$gateway->log          = new Redsyslite_Test_Recording_Logger();

		$gateway->process_refund( $order->get_id(), 4 );

		$this->assertSame( 1, $this->http_calls, 'The refund request is expected to be built and sent; otherwise the signing code never ran.' );
		foreach ( $gateway->log->lines as $line ) {
			$this->assertStringNotContainsString( $this->secret, $line, 'The signing secret must never be written to the debug log.' );
		}
	}

	public function gateways_that_logged_the_secret() {
		return array(
			'Bizum'      => array( 'WC_Gateway_Bizum_Redsys', '000000000501' ),
			'Google Pay' => array( 'WC_Gateway_GooglePay_Redirection_Redsys', '000000000502' ),
		);
	}

	/**
	 * D-041 item 2: Google Pay had no already-paid guard, so a repeated
	 * notification was processed again.
	 */
	public function test_googlepay_ignores_a_notification_for_an_order_that_is_already_paid() {
		$order = $this->mapped_order( '000000000503', 1.00 );

		$gateway               = new WC_Gateway_GooglePay_Redirection_Redsys();
		$gateway->secretsha256 = $this->secret;
		$gateway->testmode     = 'no';
		$gateway->debug        = 'no';

		$params = $this->signed_params( '000000000503', '100' );
		$gateway->successful_request( $params );
		$this->assertTrue( wc_get_order( $order->get_id() )->has_status( array( 'processing', 'completed' ) ), 'The first notification must pay the order.' );
		$notes_after_first = count( wc_get_order_notes( array( 'order_id' => $order->get_id() ) ) );

		$gateway->successful_request( $params );
		$notes_after_second = count( wc_get_order_notes( array( 'order_id' => $order->get_id() ) ) );

		$this->assertSame( $notes_after_first, $notes_after_second, 'A duplicate notification for a paid order must change nothing.' );
	}

	/**
	 * D-041 item 1: the thank-you page fallback handed the gateway the
	 * return parameters without the signature version, and Google Pay
	 * answered with wp_die().
	 */
	public function test_thank_you_fallback_passes_the_signature_version_to_the_gateway() {
		update_option(
			'woocommerce_googlepayredirecredsys_settings',
			array(
				'enabled'      => 'yes',
				'testmode'     => 'no',
				'debug'        => 'no',
				'customer'     => '999999999',
				'secretsha256' => $this->secret,
			)
		);
		WC()->payment_gateways()->init();

		$order = $this->mapped_order( '000000000504', 1.00 );
		$order->set_payment_method( 'googlepayredirecredsys' );
		$order->save();
		$this->transients[] = 'redsyslite_mark_paid_attempt_' . $order->get_id();

		foreach ( $this->signed_params( '000000000504', '100' ) as $key => $value ) {
			$_GET[ $key ] = $value;
		}

		redsyslite_mark_order_as_paid( $order->get_id() );

		$this->assertTrue(
			wc_get_order( $order->get_id() )->has_status( array( 'processing', 'completed' ) ),
			'A correctly signed return to the thank-you page must pay a Google Pay order, not kill the page.'
		);
	}

	/**
	 * D-041 item 6: with no "show to these users" list — and the settings
	 * screen has no field to create one — Google Pay was hidden from
	 * everybody whenever test mode was on.
	 */
	public function test_googlepay_in_test_mode_is_offered_when_no_user_list_exists() {
		delete_option( 'woocommerce_googlepayredirecredsys_settings' );
		$gateway           = new WC_Gateway_GooglePay_Redirection_Redsys();
		$gateway->testmode = 'yes';

		$this->assertTrue( $gateway->check_user_show_payment_method(), 'guest' );
		$this->assertTrue( $gateway->check_user_show_payment_method( 7 ), 'logged-in user' );
	}

	public function test_googlepay_in_test_mode_still_honours_a_user_list() {
		update_option(
			'woocommerce_googlepayredirecredsys_settings',
			array(
				'testmode'        => 'yes',
				'testshowgateway' => array( '7' ),
			)
		);
		$gateway           = new WC_Gateway_GooglePay_Redirection_Redsys();
		$gateway->testmode = 'yes';

		$this->assertTrue( $gateway->check_user_show_payment_method( 7 ) );
		$this->assertFalse( $gateway->check_user_show_payment_method( 8 ) );
		$this->assertFalse( $gateway->check_user_show_payment_method() );
	}
}
