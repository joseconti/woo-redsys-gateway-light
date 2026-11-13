<?php
/**
 * Integration tests: the order-received fallback waits only for a return
 * that Redsys signed (S-046).
 *
 * redsyslite_mark_order_as_paid() pauses five seconds to let the
 * server-to-server notification arrive first. The pause used to run before
 * anything was checked: any order of any gateway, with any text in
 * Ds_MerchantParameters, held a PHP worker for five seconds, and the
 * per-order limit (MarkOrderAsPaidRateLimitTest) did not help against a
 * visitor who places several orders of their own. The checks that cost
 * nothing now come first, and the signature is verified before the wait.
 *
 * These tests time real calls, like MarkOrderAsPaidRateLimitTest.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class MarkOrderAsPaidUnsignedReturnTest extends WP_UnitTestCase {

	/**
	 * A fixed, syntactically valid but non-production Base64 secret.
	 *
	 * @var string
	 */
	private $secret;

	/**
	 * Transients created by a test.
	 *
	 * @var string[]
	 */
	private $transients = array();

	public function set_up() {
		parent::set_up();
		$this->secret     = base64_encode( str_repeat( 'R', 24 ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$this->transients = array();

		foreach ( array( 'redsys', 'bizumredsys', 'googlepayredirecredsys' ) as $gateway_id ) {
			update_option(
				'woocommerce_' . $gateway_id . '_settings',
				array(
					'enabled'      => 'yes',
					'testmode'     => 'no',
					'debug'        => 'no',
					'customer'     => '999999999',
					'secretsha256' => $this->secret,
				)
			);
		}
		WC()->payment_gateways()->init();
	}

	public function tear_down() {
		unset( $_GET['Ds_MerchantParameters'], $_GET['Ds_Signature'], $_GET['Ds_SignatureVersion'] );
		foreach ( $this->transients as $transient ) {
			delete_transient( $transient );
		}
		foreach ( array( 'redsys', 'bizumredsys', 'googlepayredirecredsys' ) as $gateway_id ) {
			delete_option( 'woocommerce_' . $gateway_id . '_settings' );
		}
		WC()->payment_gateways()->init();
		parent::tear_down();
	}

	/**
	 * A pending order of the given gateway, reachable from a Ds_Order.
	 *
	 * @param string $gateway_id Payment method of the order.
	 * @param string $ds_order   Ds_Order that maps to it.
	 * @return WC_Order
	 */
	private function pending_order( $gateway_id, $ds_order ) {
		$order = wc_create_order();
		$order->set_total( 1.00 );
		$order->set_payment_method( $gateway_id );
		$order->save();

		set_transient( 'redys_order_temp_' . $ds_order, $order->get_id(), 3600 );
		$this->transients[] = 'redys_order_temp_' . $ds_order;
		$this->transients[] = 'redsyslite_mark_paid_attempt_' . $order->get_id();
		return $order;
	}

	/**
	 * Puts a return signed with the merchant secret in the query string.
	 *
	 * @param string $ds_order Ds_Order of the return.
	 */
	private function return_signed_by_redsys( $ds_order ) {
		$json  = wp_json_encode(
			array(
				'Ds_Order'        => $ds_order,
				'Ds_Response'     => '0000',
				'Ds_Amount'       => '100',
				'Ds_MerchantCode' => '999999999',
			)
		);
		$param = strtr( base64_encode( $json ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		$_GET['Ds_MerchantParameters'] = $param;
		$_GET['Ds_Signature']          = ( new RedsysLiteAPI() )->create_merchant_signature_notif( $this->secret, $param );
		$_GET['Ds_SignatureVersion']   = 'HMAC_SHA256_V1';
	}

	/**
	 * Puts in the query string what a visitor without the secret can send.
	 *
	 * @param string $ds_order Ds_Order named by the parameters.
	 */
	private function return_not_signed_by_redsys( $ds_order ) {
		$json  = wp_json_encode(
			array(
				'Ds_Order'    => $ds_order,
				'Ds_Response' => '0000',
				'Ds_Amount'   => '100',
			)
		);
		$param = strtr( base64_encode( $json ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		$_GET['Ds_MerchantParameters'] = $param;
		$_GET['Ds_Signature']          = strtr( base64_encode( str_repeat( 'x', 32 ) ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$_GET['Ds_SignatureVersion']   = 'HMAC_SHA256_V1';
	}

	/**
	 * @return array[]
	 */
	public function redsys_protocol_gateways() {
		return array(
			'card'       => array( 'redsys', '000000000601' ),
			'Bizum'      => array( 'bizumredsys', '000000000602' ),
			'Google Pay' => array( 'googlepayredirecredsys', '000000000603' ),
		);
	}

	/**
	 * @dataProvider redsys_protocol_gateways
	 * @param string $gateway_id Gateway id.
	 * @param string $ds_order   Ds_Order of the fixture.
	 */
	public function test_a_return_redsys_did_not_sign_does_not_wait( $gateway_id, $ds_order ) {
		$order = $this->pending_order( $gateway_id, $ds_order );
		$this->return_not_signed_by_redsys( $ds_order );

		$start = microtime( true );
		redsyslite_mark_order_as_paid( $order->get_id() );
		$elapsed = microtime( true ) - $start;

		$this->assertLessThan( 1, $elapsed, 'A return nobody signed held the request for the five-second wait.' );
		$this->assertTrue( wc_get_order( $order->get_id() )->has_status( 'pending' ) );
		$this->assertFalse(
			get_transient( 'redsyslite_mark_paid_attempt_' . $order->get_id() ),
			'An unsigned return must not use up the order\'s attempt: the genuine return would then be ignored for 30 seconds.'
		);
	}

	public function test_a_return_without_a_signature_does_not_wait() {
		$order                         = $this->pending_order( 'redsys', '000000000604' );
		$_GET['Ds_MerchantParameters'] = 'junk';

		$start = microtime( true );
		redsyslite_mark_order_as_paid( $order->get_id() );

		$this->assertLessThan( 1, microtime( true ) - $start );
	}

	/**
	 * One genuine signed return must not buy a wait on every other order of
	 * its holder: the signature has to be the one of the order in the URL.
	 *
	 * @dataProvider redsys_protocol_gateways
	 * @param string $gateway_id Gateway id.
	 * @param string $ds_order   Ds_Order of the fixture.
	 */
	public function test_a_genuine_return_of_another_order_does_not_wait( $gateway_id, $ds_order ) {
		$paid_elsewhere = $this->pending_order( $gateway_id, $ds_order );
		$other          = $this->pending_order( $gateway_id, '0000000009' . substr( $ds_order, -2 ) );
		$this->return_signed_by_redsys( $ds_order );

		$start = microtime( true );
		redsyslite_mark_order_as_paid( $other->get_id() );

		$this->assertLessThan( 1, microtime( true ) - $start, 'A return signed for one order held the request of another.' );
		$this->assertTrue( wc_get_order( $other->get_id() )->has_status( 'pending' ) );
		$this->assertTrue( wc_get_order( $paid_elsewhere->get_id() )->has_status( 'pending' ), 'The order the return was signed for is not this request\'s to complete.' );
	}

	/**
	 * Parameters that decode to nothing must end quietly in every gateway.
	 *
	 * @dataProvider redsys_protocol_gateways
	 * @param string $gateway_id Gateway id.
	 * @param string $ds_order   Ds_Order of the fixture.
	 */
	public function test_parameters_that_are_not_json_do_not_wait_and_raise_nothing( $gateway_id, $ds_order ) {
		$order                         = $this->pending_order( $gateway_id, $ds_order );
		$_GET['Ds_MerchantParameters'] = 'AAAA';
		$_GET['Ds_Signature']          = 'x';

		$start = microtime( true );
		redsyslite_mark_order_as_paid( $order->get_id() );

		$this->assertLessThan( 1, microtime( true ) - $start );
	}

	public function test_an_order_of_another_gateway_does_not_wait() {
		$order = $this->pending_order( 'bacs', '000000000605' );
		$this->return_signed_by_redsys( '000000000605' );

		$start = microtime( true );
		redsyslite_mark_order_as_paid( $order->get_id() );

		$this->assertLessThan( 1, microtime( true ) - $start, 'An order that was not placed with one of these gateways has nothing to wait for.' );
		$this->assertTrue( wc_get_order( $order->get_id() )->has_status( 'pending' ) );
	}

	public function test_an_order_that_does_not_exist_does_not_wait() {
		$this->return_signed_by_redsys( '000000000606' );

		$start = microtime( true );
		redsyslite_mark_order_as_paid( 987654321 );

		$this->assertLessThan( 1, microtime( true ) - $start );
	}

	/**
	 * The other side: the genuine return still waits for the notification
	 * and, when it has not arrived, completes the order itself.
	 *
	 * @dataProvider redsys_protocol_gateways
	 * @param string $gateway_id Gateway id.
	 * @param string $ds_order   Ds_Order of the fixture.
	 */
	public function test_a_return_signed_by_redsys_waits_and_completes_the_order( $gateway_id, $ds_order ) {
		$order = $this->pending_order( $gateway_id, $ds_order );
		$this->return_signed_by_redsys( $ds_order );

		$start = microtime( true );
		redsyslite_mark_order_as_paid( $order->get_id() );
		$elapsed = microtime( true ) - $start;

		$this->assertGreaterThanOrEqual( 5, $elapsed, 'The signed return gives the notification its five seconds.' );
		$this->assertTrue( wc_get_order( $order->get_id() )->has_status( array( 'processing', 'completed' ) ) );
	}

	/**
	 * In test mode with a test secret of its own, the gateway verifies
	 * against that one: a return signed with the live secret is not genuine,
	 * and one signed with the test secret is. The verdict is read from the
	 * gateway directly for the accepted case, so that this test does not wait.
	 *
	 * @dataProvider redsys_protocol_gateways
	 * @param string $gateway_id Gateway id.
	 * @param string $ds_order   Ds_Order of the fixture.
	 */
	public function test_a_return_signed_with_another_secret_does_not_wait_in_test_mode( $gateway_id, $ds_order ) {
		$test_secret = base64_encode( str_repeat( 'T', 24 ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$settings    = get_option( 'woocommerce_' . $gateway_id . '_settings' );

		$settings['testmode']         = 'yes';
		$settings['customtestsha256'] = $test_secret;
		update_option( 'woocommerce_' . $gateway_id . '_settings', $settings );
		WC()->payment_gateways()->init();

		$order = $this->pending_order( $gateway_id, $ds_order );
		$this->return_signed_by_redsys( $ds_order ); // Signed with the live secret.

		$start = microtime( true );
		redsyslite_mark_order_as_paid( $order->get_id() );

		$this->assertLessThan( 1, microtime( true ) - $start );
		$this->assertTrue( wc_get_order( $order->get_id() )->has_status( 'pending' ) );

		$gateways = WC()->payment_gateways()->payment_gateways();
		$param    = $_GET['Ds_MerchantParameters'];

		$this->assertTrue(
			$gateways[ $gateway_id ]->is_valid_return(
				array(
					'Ds_MerchantParameters' => $param,
					'Ds_Signature'          => ( new RedsysLiteAPI() )->create_merchant_signature_notif( $test_secret, $param ),
				)
			),
			'The same return signed with the test secret is the genuine one in test mode.'
		);
		$this->assertFalse( $gateways[ $gateway_id ]->is_valid_return( array( 'Ds_MerchantParameters' => $param ) ) );
		$this->assertFalse( $gateways[ $gateway_id ]->is_valid_return( null ) );
	}
}
