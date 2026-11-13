<?php
/**
 * Integration tests for WC_Gateway_redsys::check_ipn_request_is_valid().
 *
 * This is the fail-closed gate in front of every payment notification
 * (docs/threat-model.md, "Notification signature verification" /
 * "no rate limiting..." rows): it decides whether an incoming
 * `?wc-api=WC_Gateway_redsys` POST is trusted. docs/05-test-points.md
 * already recorded a real, driven playground run of this exact path with
 * fabricated POSTs (2026-08-01) — this test suite automates the same
 * cases so they run on every future change instead of only once, by hand.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class GatewayRedsysIpnTest extends WP_UnitTestCase {

	/**
	 * A fixed, syntactically valid but non-production Base64 secret —
	 * shaped like a real Redsys SHA-256 merchant key (24 raw bytes for
	 * 3DES) but not a real credential.
	 *
	 * @var string
	 */
	private $secret;

	public function set_up() {
		parent::set_up();
		$this->secret = base64_encode( str_repeat( 'A', 24 ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}

	public function tear_down() {
		unset( $_POST['Ds_MerchantParameters'], $_POST['Ds_Signature'], $_POST['Ds_SignatureVersion'] );
		parent::tear_down();
	}

	/**
	 * Builds a real, correctly-signed Ds_MerchantParameters + Ds_Signature
	 * pair for the given order/response, using RedsysLiteAPI itself — the
	 * same class a genuine Redsys notification would have been signed
	 * with. RedsysLiteAPI's own signature algorithm is covered independently
	 * in tests/Unit/RedsysLiteAPITest.php; here it's only a fixture builder.
	 *
	 * @param string $order Ds_Order value.
	 * @return array{param: string, signature: string}
	 */
	private function build_signed_notification( $order = '0000000001' ) {
		$json  = wp_json_encode( array(
			'Ds_Order'    => $order,
			'Ds_Response' => '0000',
			'Ds_Amount'   => '100',
		) );
		$param = strtr( base64_encode( $json ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		$api       = new RedsysLiteAPI();
		$signature = $api->create_merchant_signature_notif( $this->secret, $param );

		return array(
			'param'     => $param,
			'signature' => $signature,
		);
	}

	public function test_rejects_the_notification_when_no_secret_is_configured() {
		$gateway               = new WC_Gateway_redsys();
		$gateway->secretsha256 = '';
		$gateway->testmode     = 'no';
		$gateway->debug        = 'no';

		$fixture                             = $this->build_signed_notification();
		$_POST['Ds_MerchantParameters']      = $fixture['param'];
		$_POST['Ds_Signature']               = $fixture['signature'];
		$_POST['Ds_SignatureVersion']        = 'HMAC_SHA256_V1';

		$this->assertFalse(
			$gateway->check_ipn_request_is_valid(),
			'With no SHA-256 secret configured, the gateway must fail closed even for an otherwise well-formed notification.'
		);
	}

	public function test_accepts_a_correctly_signed_notification() {
		$gateway               = new WC_Gateway_redsys();
		$gateway->secretsha256 = $this->secret;
		$gateway->testmode     = 'no';
		$gateway->debug        = 'no';

		$fixture                        = $this->build_signed_notification();
		$_POST['Ds_MerchantParameters'] = $fixture['param'];
		$_POST['Ds_Signature']          = $fixture['signature'];
		$_POST['Ds_SignatureVersion']   = 'HMAC_SHA256_V1';

		$this->assertTrue( $gateway->check_ipn_request_is_valid() );
	}

	public function test_rejects_a_notification_with_a_forged_signature() {
		$gateway               = new WC_Gateway_redsys();
		$gateway->secretsha256 = $this->secret;
		$gateway->testmode     = 'no';
		$gateway->debug        = 'no';

		$fixture                        = $this->build_signed_notification();
		$_POST['Ds_MerchantParameters'] = $fixture['param'];
		// A signature that was never computed from this payload/secret.
		$_POST['Ds_Signature']        = strtr( base64_encode( str_repeat( 'x', 32 ) ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$_POST['Ds_SignatureVersion'] = 'HMAC_SHA256_V1';

		$this->assertFalse( $gateway->check_ipn_request_is_valid() );
	}

	public function test_rejects_a_notification_whose_amount_was_tampered_with_after_signing() {
		$gateway               = new WC_Gateway_redsys();
		$gateway->secretsha256 = $this->secret;
		$gateway->testmode     = 'no';
		$gateway->debug        = 'no';

		$fixture = $this->build_signed_notification();

		// The attacker keeps the genuine signature but edits the amount in
		// the (base64url-encoded) payload before it reaches the endpoint.
		$tampered_json  = wp_json_encode( array(
			'Ds_Order'    => '0000000001',
			'Ds_Response' => '0000',
			'Ds_Amount'   => '999999',
		) );
		$tampered_param = strtr( base64_encode( $tampered_json ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		$_POST['Ds_MerchantParameters'] = $tampered_param;
		$_POST['Ds_Signature']          = $fixture['signature'];
		$_POST['Ds_SignatureVersion']   = 'HMAC_SHA256_V1';

		$this->assertFalse( $gateway->check_ipn_request_is_valid() );
	}

	public function test_rejects_a_notification_signed_for_a_different_order() {
		$gateway               = new WC_Gateway_redsys();
		$gateway->secretsha256 = $this->secret;
		$gateway->testmode     = 'no';
		$gateway->debug        = 'no';

		// Signed for order A, but the attacker swaps in order B's payload
		// while keeping order A's signature — the key is diversified by
		// order number, so this must not validate.
		$order_a = $this->build_signed_notification( '0000000001' );
		$order_b = $this->build_signed_notification( '0000000002' );

		$_POST['Ds_MerchantParameters'] = $order_b['param'];
		$_POST['Ds_Signature']          = $order_a['signature'];
		$_POST['Ds_SignatureVersion']   = 'HMAC_SHA256_V1';

		$this->assertFalse( $gateway->check_ipn_request_is_valid() );
	}
}
