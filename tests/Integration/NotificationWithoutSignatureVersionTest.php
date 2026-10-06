<?php
/**
 * A notification that arrives without its `Ds_SignatureVersion` field — or
 * with no field at all — is rejected by check_ipn_request_is_valid() of the
 * card, Bizum and Google Pay gateways, and rejecting it raises nothing.
 *
 * The Bizum gateway read its payload and signature only inside the
 * `isset( $_POST['Ds_SignatureVersion'] )` branch and used both after it:
 * on PHP 8 that ended in a TypeError (an unauthenticated HTTP 500), on
 * PHP 7 in a warning. Slice S-064.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class NotificationWithoutSignatureVersionTest extends WP_UnitTestCase {

	public function tear_down() {
		unset( $_POST['Ds_MerchantParameters'], $_POST['Ds_Signature'], $_POST['Ds_SignatureVersion'] );
		parent::tear_down();
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
	 * @dataProvider gateway_classes
	 * @param string $class Gateway class name.
	 */
	public function test_a_notification_without_the_signature_version_is_rejected_quietly( $class ) {
		$_POST['Ds_MerchantParameters'] = base64_encode( wp_json_encode( array( 'Ds_Order' => '123000000001', 'Ds_Response' => '0000', 'Ds_Amount' => '100' ) ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$_POST['Ds_Signature']          = 'not-a-signature';

		$this->assertFalse( (bool) $this->configured_gateway( $class )->check_ipn_request_is_valid() );
	}

	/**
	 * @dataProvider gateway_classes
	 * @param string $class Gateway class name.
	 */
	public function test_an_empty_post_is_rejected_quietly( $class ) {
		$this->assertFalse( (bool) $this->configured_gateway( $class )->check_ipn_request_is_valid() );
	}
}
