<?php
/**
 * Tests for RedsysLiteAPI's signature creation/verification.
 *
 * This is the highest-risk code path in the plugin (see docs/threat-model.md,
 * "Notification signature verification"): classes/class-wc-gateway-redsys.php
 * trusts a Redsys/Inespay payment notification only if the locally
 * recomputed HMAC matches the one supplied in the request. A bug here would
 * mean either rejecting legitimate payments or, far worse, accepting forged
 * ones.
 *
 * The expected values below are computed by an independent implementation
 * of Redsys's documented signature algorithm (3DES-derive-key, then
 * HMAC-SHA256), built directly from PHP's openssl/hash primitives in this
 * test file rather than by calling RedsysLiteAPI itself — so a bug shared
 * between the class and the test cannot hide a wrong result.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class RedsysLiteAPITest extends PHPUnit\Framework\TestCase {

	/**
	 * Reference (independent) implementation of Redsys's key-derivation +
	 * HMAC-SHA256 signature algorithm, per the documented protocol:
	 *   1. decode the merchant secret from Base64
	 *   2. derive a per-order key: 3DES-CBC-encrypt(order, secret, IV = 8 zero bytes)
	 *   3. signature = HMAC-SHA256(payload, derived_key), raw bytes
	 *
	 * @param string $secret_b64 Base64-encoded merchant secret.
	 * @param string $order      Order number used to diversify the key.
	 * @param string $payload    The exact string that gets signed.
	 * @return string Raw (binary) HMAC-SHA256 digest.
	 */
	private function reference_signature( $secret_b64, $order, $payload ) {
		$secret = base64_decode( $secret_b64 ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		$iv     = str_repeat( "\0", 8 );

		$long        = (int) ceil( strlen( $order ) / 16 ) * 16;
		$padded      = $order . str_repeat( "\0", $long - strlen( $order ) );
		$derived_key = substr( openssl_encrypt( $padded, 'des-ede3-cbc', $secret, OPENSSL_RAW_DATA, $iv ), 0, $long );

		return hash_hmac( 'sha256', $payload, $derived_key, true );
	}

	/**
	 * A fixed, syntactically valid but non-production Base64 secret used
	 * across these tests — shaped like a real Redsys SHA-256 merchant key
	 * (24 raw bytes for 3DES) but not a real credential.
	 *
	 * @return string
	 */
	private function fixture_secret() {
		return base64_encode( str_repeat( 'A', 24 ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}

	public function test_create_merchant_signature_matches_the_documented_algorithm() {
		$api = new RedsysLiteAPI();
		$api->set_parameter( 'DS_MERCHANT_ORDER', '000123456' );
		$api->set_parameter( 'Ds_Merchant_Amount', '100' );
		$api->set_parameter( 'Ds_Merchant_Currency', '978' );

		$secret = $this->fixture_secret();
		$actual = $api->create_merchant_signature( $secret );

		$json    = wp_json_encode( array(
			'DS_MERCHANT_ORDER'    => '000123456',
			'Ds_Merchant_Amount'   => '100',
			'Ds_Merchant_Currency' => '978',
		) );
		$payload  = base64_encode( $json ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$expected = base64_encode( $this->reference_signature( $secret, '000123456', $payload ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		$this->assertSame( $expected, $actual );
	}

	public function test_create_merchant_signature_notif_matches_the_documented_algorithm() {
		$api    = new RedsysLiteAPI();
		$secret = $this->fixture_secret();

		// A realistic Ds_MerchantParameters value: base64url(json(...)).
		$decoded_json = wp_json_encode( array(
			'Ds_Order'    => '000123456',
			'Ds_Response' => '0000',
			'Ds_Amount'   => '100',
		) );
		$notif_param  = strtr( base64_encode( $decoded_json ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		$actual   = $api->create_merchant_signature_notif( $secret, $notif_param );
		$expected = strtr( base64_encode( $this->reference_signature( $secret, '000123456', $notif_param ) ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		$this->assertSame( $expected, $actual );
	}

	public function test_notif_signature_changes_when_the_payload_is_tampered_with() {
		$api    = new RedsysLiteAPI();
		$secret = $this->fixture_secret();

		$genuine_json = wp_json_encode( array(
			'Ds_Order'    => '000123456',
			'Ds_Response' => '0000',
			'Ds_Amount'   => '100',
		) );
		$genuine_param = strtr( base64_encode( $genuine_json ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$genuine_sig   = $api->create_merchant_signature_notif( $secret, $genuine_param );

		// An attacker changes the paid amount but keeps everything else,
		// including the order number the key is diversified with.
		$tampered_json  = wp_json_encode( array(
			'Ds_Order'    => '000123456',
			'Ds_Response' => '0000',
			'Ds_Amount'   => '999999',
		) );
		$tampered_param = strtr( base64_encode( $tampered_json ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$tampered_sig   = $api->create_merchant_signature_notif( $secret, $tampered_param );

		$this->assertNotSame(
			$genuine_sig,
			$tampered_sig,
			'A tampered Ds_MerchantParameters payload must not reuse the genuine signature — this is exactly the check check_ipn_request_is_valid() relies on.'
		);
	}

	public function test_notif_signature_differs_for_a_different_merchant_secret() {
		$api = new RedsysLiteAPI();

		$json  = wp_json_encode( array( 'Ds_Order' => '000123456', 'Ds_Response' => '0000' ) );
		$param = strtr( base64_encode( $json ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		$secret_a = base64_encode( str_repeat( 'A', 24 ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$secret_b = base64_encode( str_repeat( 'B', 24 ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		$sig_a = $api->create_merchant_signature_notif( $secret_a, $param );
		$sig_b = $api->create_merchant_signature_notif( $secret_b, $param );

		$this->assertNotSame( $sig_a, $sig_b );
	}

	/**
	 * Payloads that name no order. The signing key is diversified by the
	 * order number, so each of these derives a key of zero bytes.
	 *
	 * @return array[]
	 */
	public function payloads_without_an_order() {
		return array(
			'Ds_Order absent'     => array( '{"Ds_Response":"0000","Ds_Amount":"100"}' ),
			'Ds_Order empty'      => array( '{"Ds_Order":"","Ds_Response":"0000","Ds_Amount":"100"}' ),
			'Ds_Order null'       => array( '{"Ds_Order":null,"Ds_Response":"0000"}' ),
			'Ds_Order zero'       => array( '{"Ds_Order":0,"Ds_Response":"0000"}' ),
			'Ds_Order "0"'        => array( '{"Ds_Order":"0","Ds_Response":"0000"}' ),
			'Ds_Order false'      => array( '{"Ds_Order":false,"Ds_Response":"0000"}' ),
			'Ds_Order array'      => array( '{"Ds_Order":["x"],"Ds_Response":"0000"}' ),
			'JSON scalar'         => array( '5' ),
			'not JSON'            => array( 'this is not JSON' ),
			'empty parameters'    => array( '' ),
		);
	}

	/**
	 * S-043: a notification signature must never be computable without the
	 * merchant secret. For a payload that names no order the derived key is
	 * empty, and an HMAC with an empty key is something any caller can
	 * compute.
	 *
	 * @dataProvider payloads_without_an_order
	 * @param string $decoded The decoded Ds_MerchantParameters content.
	 */
	public function test_notif_signature_for_a_payload_without_an_order_cannot_be_computed_without_the_secret( $decoded ) {
		$param = strtr( base64_encode( $decoded ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		// What a caller who does not hold the secret can compute.
		$secretless = strtr( base64_encode( hash_hmac( 'sha256', $param, '', true ) ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		$api    = new RedsysLiteAPI();
		$actual = $api->create_merchant_signature_notif( $this->fixture_secret(), $param );

		$this->assertNotSame( $secretless, $actual, 'The signature of a payload without an order does not depend on the merchant secret.' );
		$this->assertNotSame( '', $actual, 'An empty expected signature would match an empty Ds_Signature.' );

		// It must not be a fixed value either: nothing a caller sees once can be replayed.
		$again = ( new RedsysLiteAPI() )->create_merchant_signature_notif( $this->fixture_secret(), $param );
		$this->assertNotSame( $actual, $again );
	}

	/**
	 * S-043: a merchant secret that decodes to nothing signs nothing either,
	 * even for a real order number.
	 *
	 * @dataProvider secrets_that_decode_to_nothing
	 * @param string $secret A Base64 merchant secret with no bytes in it.
	 */
	public function test_notif_signature_under_an_empty_secret_is_unmatchable( $secret ) {
		$param = strtr( base64_encode( '{"Ds_Order":"000123456","Ds_Response":"0000"}' ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		$first  = ( new RedsysLiteAPI() )->create_merchant_signature_notif( $secret, $param );
		$second = ( new RedsysLiteAPI() )->create_merchant_signature_notif( $secret, $param );

		$this->assertNotSame( '', $first );
		$this->assertNotSame( $first, $second, 'A signature that does not depend on a secret must not be a fixed value.' );
	}

	/**
	 * @return array[]
	 */
	public function secrets_that_decode_to_nothing() {
		return array(
			'empty'          => array( '' ),
			'blank'          => array( '   ' ),
			'not Base64'     => array( '!!!' ),
		);
	}

	/**
	 * S-043: the same rule for the SOAP response variant, which receives the
	 * order number as an argument.
	 *
	 * @dataProvider empty_order_numbers
	 * @param mixed $order An order number that names no order.
	 */
	public function test_soap_response_signature_without_an_order_cannot_be_computed_without_the_secret( $order ) {
		$datos = '<Response Ds_Version="0.0"><Ds_Response_Merchant>OK</Ds_Response_Merchant></Response>';

		$secretless = base64_encode( hash_hmac( 'sha256', $datos, '', true ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$actual     = ( new RedsysLiteAPI() )->create_merchant_signature_notif_soap_response( $this->fixture_secret(), $datos, $order );

		$this->assertNotSame( $secretless, $actual );
		$this->assertNotSame( '', $actual );
		$this->assertNotSame( $actual, ( new RedsysLiteAPI() )->create_merchant_signature_notif_soap_response( $this->fixture_secret(), $datos, $order ) );
	}

	/**
	 * @return array[]
	 */
	public function empty_order_numbers() {
		return array(
			'empty string' => array( '' ),
			'null'         => array( null ),
			'false'        => array( false ),
			'zero'         => array( 0 ),
			'"0"'          => array( '0' ),
			'array'        => array( array() ),
		);
	}

	/**
	 * S-043, the other side: a notification that names its order through the
	 * upper-case key keeps its documented, secret-dependent signature.
	 */
	public function test_notif_signature_for_an_upper_case_order_key_matches_the_documented_algorithm() {
		$decoded = '{"DS_ORDER":"000123456","Ds_Response":"0000"}';
		$param   = strtr( base64_encode( $decoded ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		$expected = strtr( base64_encode( $this->reference_signature( $this->fixture_secret(), '000123456', $param ) ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		$this->assertSame( $expected, ( new RedsysLiteAPI() )->create_merchant_signature_notif( $this->fixture_secret(), $param ) );
	}

	/**
	 * @dataProvider provide_sanitize_merchant_parameters_cases
	 */
	public function test_sanitize_merchant_parameters( $raw, $expected ) {
		$this->assertSame( $expected, RedsysLiteAPI::sanitize_merchant_parameters( $raw ) );
	}

	public function provide_sanitize_merchant_parameters_cases() {
		return array(
			'spaces are restored to plus signs (proxy/urldecode artifact)' => array(
				'eyJhbGciOiJIUzI1 iJ9==',
				'eyJhbGciOiJIUzI1+iJ9==',
			),
			'valid Base64URL characters pass through untouched'            => array(
				'abcDEF012+/=_-',
				'abcDEF012+/=_-',
			),
			'characters outside the Base64/Base64URL alphabet are stripped' => array(
				'abc<script>def',
				'abcscriptdef',
			),
		);
	}

	public function test_sanitize_merchant_parameters_rejects_null_byte_injection() {
		$raw = "abc\0def";
		$this->assertSame( 'abcdef', RedsysLiteAPI::sanitize_merchant_parameters( $raw ) );
	}

	/**
	 * S-046: reading a parameter after decoding something that is not a JSON
	 * object answers null, on every PHP version, instead of raising.
	 *
	 * @dataProvider payloads_that_are_not_an_object
	 * @param string $decoded The decoded Ds_MerchantParameters content.
	 */
	public function test_get_parameter_after_decoding_a_non_object_is_null( $decoded ) {
		$api = new RedsysLiteAPI();
		$api->decode_merchant_parameters( strtr( base64_encode( $decoded ), '+/', '-_' ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		$this->assertNull( $api->get_parameter( 'Ds_Order' ) );
	}

	/**
	 * @return array[]
	 */
	public function payloads_that_are_not_an_object() {
		return array(
			'not JSON'    => array( 'this is not JSON' ),
			'JSON null'   => array( 'null' ),
			'JSON number' => array( '5' ),
			'JSON string' => array( '"Ds_Order"' ),
			'empty'       => array( '' ),
		);
	}
}
