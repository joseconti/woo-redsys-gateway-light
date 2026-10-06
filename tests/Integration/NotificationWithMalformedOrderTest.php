<?php
/**
 * A notification whose `Ds_Order` is not a string — a JSON array, an object,
 * a number, a boolean, null — is rejected by check_ipn_request_is_valid() of
 * the card, Bizum and Google Pay gateways, and rejecting it raises nothing.
 *
 * The payload is decoded from the request before any signature is compared,
 * so its shape is whatever the caller sent. The Google Pay gateway passed an
 * array straight to the order lookup, which ends in substr(): on PHP 8 a
 * TypeError (an unauthenticated HTTP 500). The Bizum gateway cast it to a
 * string, which raises an "Array to string conversion" warning. Slice S-072;
 * the sibling of S-064.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class NotificationWithMalformedOrderTest extends WP_UnitTestCase {

	public function tear_down() {
		unset( $_POST['Ds_MerchantParameters'], $_POST['Ds_Signature'], $_POST['Ds_SignatureVersion'] );
		parent::tear_down();
	}

	/**
	 * Every gateway against every shape.
	 *
	 * @return array<string, array{0: string, 1: mixed}>
	 */
	public function gateways_and_order_values() {
		$classes = array(
			'card'       => 'WC_Gateway_redsys',
			'Bizum'      => 'WC_Gateway_Bizum_Redsys',
			'Google Pay' => 'WC_Gateway_GooglePay_Redirection_Redsys',
		);
		$values  = array(
			'an array'        => array( 'x' ),
			'a nested array'  => array( array( '123000000001' ) ),
			'an object'       => array( 'a' => 'b' ),
			'an empty array'  => array(),
			'a number'        => 123000000001,
			'a float'         => 1.5,
			'true'            => true,
			'false'           => false,
			'null'            => null,
		);
		$cases   = array();
		foreach ( $classes as $gateway => $class ) {
			foreach ( $values as $shape => $value ) {
				$cases[ $gateway . ', Ds_Order is ' . $shape ] = array( $class, $value );
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
		$gateway->secretsha256 = base64_encode( str_repeat( 'S', 24 ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$gateway->testmode     = 'no';
		$gateway->debug        = 'no';
		return $gateway;
	}

	/**
	 * @dataProvider gateways_and_order_values
	 * @param string $class Gateway class name.
	 * @param mixed  $value What the payload carries as Ds_Order.
	 */
	public function test_a_notification_whose_order_is_not_a_string_is_rejected_quietly( $class, $value ) {
		$_POST['Ds_SignatureVersion']   = 'HMAC_SHA256_V1';
		$_POST['Ds_MerchantParameters'] = base64_encode( wp_json_encode( array( 'Ds_Order' => $value, 'Ds_Response' => '0000', 'Ds_Amount' => '100' ) ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$_POST['Ds_Signature']          = 'not-a-signature';

		$this->assertFalse( (bool) $this->configured_gateway( $class )->check_ipn_request_is_valid() );
	}

	/**
	 * The counterpart: the same harness accepts a notification that is
	 * signed, so the cases above are refused for what they carry and not
	 * because this harness can only be refused.
	 *
	 * @dataProvider gateway_classes
	 * @param string $class Gateway class name.
	 */
	public function test_the_same_harness_accepts_a_signed_notification( $class ) {
		$order = wc_create_order();
		$order->set_total( 1 );
		$order->save();
		$gateway  = $this->configured_gateway( $class );
		$ds_order = WCRedL()->prepare_order_number( $order->get_id() );
		$params   = base64_encode( wp_json_encode( array( 'Ds_Order' => $ds_order, 'Ds_Response' => '0000', 'Ds_Amount' => '100' ) ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$api      = new RedsysLiteAPI();

		$_POST['Ds_SignatureVersion']   = 'HMAC_SHA256_V1';
		$_POST['Ds_MerchantParameters'] = $params;
		$_POST['Ds_Signature']          = $api->create_merchant_signature_notif( $gateway->secretsha256, $params );

		$this->assertTrue( (bool) $gateway->check_ipn_request_is_valid() );
	}

	/**
	 * @return array<string, array{0: string}>
	 */
	public function gateway_classes() {
		return array(
			'card'       => array( 'WC_Gateway_redsys' ),
			'Bizum'      => array( 'WC_Gateway_Bizum_Redsys' ),
			'Google Pay' => array( 'WC_Gateway_GooglePay_Redirection_Redsys' ),
		);
	}

	/**
	 * The lookup both handlers end in takes whatever it is given.
	 *
	 * @dataProvider order_values
	 * @param mixed $value What a caller passes as the order number.
	 */
	public function test_cleaning_an_order_number_that_is_not_a_string_returns_no_order( $value ) {
		$this->assertSame( '', WCRedL()->clean_order_number( $value ) );
	}

	/**
	 * @return array<string, array{0: mixed}>
	 */
	public function order_values() {
		return array(
			'an array'       => array( array( 'x' ) ),
			'an empty array' => array( array() ),
			'null'           => array( null ),
			'true'           => array( true ),
			'false'          => array( false ),
		);
	}
}
