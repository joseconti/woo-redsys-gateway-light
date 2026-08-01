<?php
/**
 * Integration tests for WC_Gateway_GooglePay_Redirection_Redsys::check_ipn_request_is_valid().
 *
 * Like WC_Gateway_Bizum_Redsys (see GatewayBizumIpnTest), this method
 * requires a real WC_Order once a secret is configured (it maps Ds_Order
 * back to a real order via WCRedL()->clean_order_number() /
 * WCRedL()->get_order()).
 *
 * Before 2026-08-01 this class did NOT fail closed when no SHA-256 secret
 * was configured: it accepted the notification if the (attacker-supplied)
 * Ds_MerchantCode matched the gateway's own merchant code — which is not a
 * secret, it is sent in plaintext in every outgoing payment form. See
 * docs/threat-model.md "Known vulnerabilities" and docs/decisions.md D-020
 * for the fix. test_rejects_the_notification_when_no_secret_is_configured()
 * below is what would have caught this before it shipped.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class GatewayGooglePayIpnTest extends WP_UnitTestCase {

	/**
	 * A fixed, syntactically valid but non-production Base64 secret —
	 * shaped like a real Redsys SHA-256 merchant key (24 raw bytes for
	 * 3DES) but not a real credential.
	 *
	 * @var string
	 */
	private $secret;

	/**
	 * Ds_Order values used across a single test, so tearDown can clean up
	 * the order-number-mapping transients it created.
	 *
	 * @var string[]
	 */
	private $ds_orders_used = array();

	public function set_up() {
		parent::set_up();
		$this->secret         = base64_encode( str_repeat( 'G', 24 ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$this->ds_orders_used = array();
	}

	public function tear_down() {
		unset( $_POST['Ds_MerchantParameters'], $_POST['Ds_Signature'], $_POST['Ds_SignatureVersion'] );
		foreach ( $this->ds_orders_used as $ds_order ) {
			delete_transient( 'redys_order_temp_' . $ds_order );
		}
		parent::tear_down();
	}

	/**
	 * @param string $ds_order The Ds_Order value the fixture notification will carry.
	 * @return WC_Order
	 */
	private function create_mapped_order( $ds_order ) {
		$order = wc_create_order();
		set_transient( 'redys_order_temp_' . $ds_order, $order->get_id(), 3600 );
		$this->ds_orders_used[] = $ds_order;
		return $order;
	}

	/**
	 * @param string $order       Ds_Order value.
	 * @param string $merchant_code Ds_MerchantCode value to embed in the payload.
	 * @return array{param: string, signature: string}
	 */
	private function build_signed_notification( $order, $merchant_code = '999999999' ) {
		$json  = wp_json_encode( array(
			'Ds_Order'        => $order,
			'Ds_Response'     => '0000',
			'Ds_Amount'       => '100',
			'Ds_MerchantCode' => $merchant_code,
		) );
		$param = strtr( base64_encode( $json ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		$api       = new RedsysLiteAPI();
		$signature = $api->create_merchant_signature_notif( $this->secret, $param );

		return array(
			'param'     => $param,
			'signature' => $signature,
		);
	}

	/**
	 * @return WC_Gateway_GooglePay_Redirection_Redsys
	 */
	private function configured_gateway() {
		$gateway               = new WC_Gateway_GooglePay_Redirection_Redsys();
		$gateway->secretsha256 = $this->secret;
		$gateway->testmode     = 'no';
		$gateway->debug        = 'no';
		return $gateway;
	}

	public function test_rejects_the_notification_when_no_secret_is_configured() {
		// Regression test for the fail-open bug fixed 2026-08-01 (D-020):
		// this must reject even when Ds_MerchantCode in the payload matches
		// the gateway's own (public, non-secret) merchant code.
		$gateway               = new WC_Gateway_GooglePay_Redirection_Redsys();
		$gateway->secretsha256 = '';
		$gateway->testmode     = 'no';
		$gateway->debug        = 'no';
		$gateway->customer     = '999999999';

		$fixture                        = $this->build_signed_notification( '000000000001', '999999999' );
		$_POST['Ds_MerchantParameters'] = $fixture['param'];
		$_POST['Ds_Signature']          = $fixture['signature'];
		$_POST['Ds_SignatureVersion']   = 'HMAC_SHA256_V1';

		$this->assertFalse(
			$gateway->check_ipn_request_is_valid(),
			'With no SHA-256 secret configured, the gateway must fail closed — even when Ds_MerchantCode matches the gateway\'s own (public) merchant code.'
		);
	}

	public function test_accepts_a_correctly_signed_notification_for_a_real_order() {
		$this->create_mapped_order( '000000000002' );
		$gateway = $this->configured_gateway();

		$fixture                        = $this->build_signed_notification( '000000000002' );
		$_POST['Ds_MerchantParameters'] = $fixture['param'];
		$_POST['Ds_Signature']          = $fixture['signature'];
		$_POST['Ds_SignatureVersion']   = 'HMAC_SHA256_V1';

		$this->assertTrue( $gateway->check_ipn_request_is_valid() );
	}

	public function test_rejects_a_notification_with_a_forged_signature() {
		$this->create_mapped_order( '000000000003' );
		$gateway = $this->configured_gateway();

		$fixture                        = $this->build_signed_notification( '000000000003' );
		$_POST['Ds_MerchantParameters'] = $fixture['param'];
		$_POST['Ds_Signature']          = strtr( base64_encode( str_repeat( 'x', 32 ) ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$_POST['Ds_SignatureVersion']   = 'HMAC_SHA256_V1';

		$this->assertFalse( $gateway->check_ipn_request_is_valid() );
	}

	public function test_rejects_a_notification_whose_amount_was_tampered_with_after_signing() {
		$this->create_mapped_order( '000000000004' );
		$gateway = $this->configured_gateway();

		$fixture = $this->build_signed_notification( '000000000004' );

		$tampered_json  = wp_json_encode( array(
			'Ds_Order'        => '000000000004',
			'Ds_Response'     => '0000',
			'Ds_Amount'       => '999999',
			'Ds_MerchantCode' => '999999999',
		) );
		$tampered_param = strtr( base64_encode( $tampered_json ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		$_POST['Ds_MerchantParameters'] = $tampered_param;
		$_POST['Ds_Signature']          = $fixture['signature'];
		$_POST['Ds_SignatureVersion']   = 'HMAC_SHA256_V1';

		$this->assertFalse( $gateway->check_ipn_request_is_valid() );
	}

	public function test_rejects_a_notification_signed_for_a_different_order() {
		$this->create_mapped_order( '000000000005' );
		$this->create_mapped_order( '000000000006' );
		$gateway = $this->configured_gateway();

		$order_a = $this->build_signed_notification( '000000000005' );
		$order_b = $this->build_signed_notification( '000000000006' );

		$_POST['Ds_MerchantParameters'] = $order_b['param'];
		$_POST['Ds_Signature']          = $order_a['signature'];
		$_POST['Ds_SignatureVersion']   = 'HMAC_SHA256_V1';

		$this->assertFalse( $gateway->check_ipn_request_is_valid() );
	}

	/**
	 * Regression test for the bug fixed 2026-08-01 (docs/decisions.md D-023):
	 * successful_request() used to always verify against $this->secretsha256
	 * (the live-mode secret), never the testmode-aware one
	 * check_ipn_request_is_valid() and get_redsys_sha256() use. A genuinely
	 * valid test-mode notification, correctly signed with the CUSTOM TEST
	 * secret, would fail this second (redundant) verification and the order
	 * would silently never be marked paid.
	 */
	public function test_successful_request_completes_the_order_when_signed_with_the_test_mode_secret() {
		$order = $this->create_mapped_order( '000000000007' );
		$order->set_total( 1.00 );
		$order->save();

		$gateway                   = new WC_Gateway_GooglePay_Redirection_Redsys();
		$gateway->testmode         = 'yes';
		$gateway->customtestsha256 = $this->secret; // Different from secretsha256 below.
		$gateway->secretsha256     = base64_encode( str_repeat( 'L', 24 ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- the (deliberately different) "live" secret.
		$gateway->debug            = 'no';
		$gateway->orderdo          = 'processing';

		$json  = wp_json_encode(
			array(
				'Ds_Order'        => '000000000007',
				'Ds_Response'     => '0000',
				'Ds_Amount'       => '100',
				'Ds_MerchantCode' => '999999999',
			)
		);
		$param = strtr( base64_encode( $json ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		$api       = new RedsysLiteAPI();
		$signature = $api->create_merchant_signature_notif( $this->secret, $param );

		$gateway->successful_request(
			array(
				'Ds_MerchantParameters' => $param,
				'Ds_Signature'          => $signature,
				'Ds_SignatureVersion'   => 'HMAC_SHA256_V1',
			)
		);

		$order = wc_get_order( $order->get_id() );
		$this->assertTrue(
			$order->has_status( array( 'processing', 'completed' ) ),
			'successful_request() must complete a test-mode order correctly signed with the CUSTOM TEST secret, not silently do nothing because it checked the wrong (live) secret.'
		);
	}
}
