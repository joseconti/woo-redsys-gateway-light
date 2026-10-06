<?php
/**
 * Integration tests: a Redsys notification that names no order is never
 * accepted (S-043).
 *
 * The notification signature is an HMAC whose key is the merchant secret
 * diversified by the order number. For a payload with no order number the
 * diversified key has zero bytes, so the expected signature no longer
 * depends on the secret and any caller can compute it. Redsys always sends
 * an order number, so a genuine notification is never of this shape.
 *
 * The card, Bizum and Google Pay gateways share the signature function;
 * Inespay has its own algorithm and is not concerned.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class NotificationWithoutOrderTest extends WP_UnitTestCase {

	/**
	 * A fixed, syntactically valid but non-production Base64 secret.
	 *
	 * @var string
	 */
	private $secret;

	public function set_up() {
		parent::set_up();
		$this->secret = base64_encode( str_repeat( 'C', 24 ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}

	public function tear_down() {
		unset( $_POST['Ds_MerchantParameters'], $_POST['Ds_Signature'], $_POST['Ds_SignatureVersion'] );
		parent::tear_down();
	}

	/**
	 * The three gateways that verify with RedsysLiteAPI, each crossed with
	 * the payload shapes that name no order.
	 *
	 * @return array[]
	 */
	public function gateways_and_payloads() {
		$payloads = array(
			'Ds_Order absent' => '{"Ds_Response":"0000","Ds_Amount":"100"}',
			'Ds_Order empty'  => '{"Ds_Order":"","Ds_Response":"0000","Ds_Amount":"100"}',
			'Ds_Order "0"'    => '{"Ds_Order":"0","Ds_Response":"0000","Ds_Amount":"100"}',
			'Ds_Order null'   => '{"Ds_Order":null,"Ds_Response":"0000","Ds_Amount":"100"}',
			'Ds_Order zero'   => '{"Ds_Order":0,"Ds_Response":"0000","Ds_Amount":"100"}',
			'not JSON'        => 'this is not JSON',
		);
		$cases    = array();
		foreach ( array( 'WC_Gateway_redsys', 'WC_Gateway_Bizum_Redsys', 'WC_Gateway_GooglePay_Redirection_Redsys' ) as $class ) {
			foreach ( $payloads as $label => $decoded ) {
				$cases[ $class . ' / ' . $label ] = array( $class, $decoded );
			}
		}
		return $cases;
	}

	/**
	 * @param string $class Gateway class name.
	 * @return WC_Payment_Gateway
	 */
	private function configured_gateway( $class ) {
		$gateway               = new $class();
		$gateway->secretsha256 = $this->secret;
		$gateway->testmode     = 'no';
		$gateway->debug        = 'no';
		if ( property_exists( $gateway, 'testforuser' ) ) {
			$gateway->testforuser = 'no';
		}
		return $gateway;
	}

	/**
	 * Puts in $_POST the notification a caller WITHOUT the secret can build:
	 * the payload, and its HMAC-SHA256 under an empty key.
	 *
	 * @param string $decoded Decoded Ds_MerchantParameters content.
	 */
	private function post_secretless_notification( $decoded ) {
		$param = strtr( base64_encode( $decoded ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		$_POST['Ds_MerchantParameters'] = $param;
		$_POST['Ds_Signature']          = strtr( base64_encode( hash_hmac( 'sha256', $param, '', true ) ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$_POST['Ds_SignatureVersion']   = 'HMAC_SHA256_V1';
	}

	/**
	 * @dataProvider gateways_and_payloads
	 * @param string $class   Gateway class name.
	 * @param string $decoded Decoded Ds_MerchantParameters content.
	 */
	public function test_a_notification_without_an_order_signed_without_the_secret_is_rejected( $class, $decoded ) {
		// The @ below: the gateways read Ds_Order from the decoded data before
		// the comparison and raise a notice when the data is not a JSON object.
		$gateway = $this->configured_gateway( $class );
		$this->post_secretless_notification( $decoded );

		$this->assertFalse(
			@$gateway->check_ipn_request_is_valid(), // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			'A notification that names no order was accepted with a signature computed without the merchant secret.'
		);
	}

	/**
	 * A guard on the fix itself, not a reproduction: it passed before the fix,
	 * and fails if the expected value for these payloads ever becomes ''.
	 *
	 * @dataProvider gateways_and_payloads
	 * @param string $class   Gateway class name.
	 * @param string $decoded Decoded Ds_MerchantParameters content.
	 */
	public function test_a_notification_without_an_order_and_with_an_empty_signature_is_rejected( $class, $decoded ) {
		$gateway = $this->configured_gateway( $class );
		$this->post_secretless_notification( $decoded );
		$_POST['Ds_Signature'] = '';

		$this->assertFalse( @$gateway->check_ipn_request_is_valid() ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	}

	/**
	 * The other side: a notification that names an order and is signed with
	 * the merchant secret is still accepted by the card gateway. Bizum and
	 * Google Pay need a real order for this and have it in their own suites.
	 */
	public function test_a_notification_that_names_an_order_is_still_accepted() {
		$gateway = $this->configured_gateway( 'WC_Gateway_redsys' );
		$param   = strtr( base64_encode( '{"Ds_Order":"000123456789","Ds_Response":"0000","Ds_Amount":"100"}' ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		$_POST['Ds_MerchantParameters'] = $param;
		$_POST['Ds_Signature']          = ( new RedsysLiteAPI() )->create_merchant_signature_notif( $this->secret, $param );
		$_POST['Ds_SignatureVersion']   = 'HMAC_SHA256_V1';

		$this->assertTrue( $gateway->check_ipn_request_is_valid() );
	}
}
