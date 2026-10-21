<?php
/**
 * Reproduction test for S-042 (D-053): Google Pay's get_redsys_args() read
 * the billing name through the generic meta API, which WooCommerce reports
 * as incorrect usage of its internal meta. Written and seen failing against
 * the code as it was before the fix.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class GooglePayBillingNameTest extends WP_UnitTestCase {

	public function test_the_cardholder_is_the_order_billing_name() {
		$order = wc_create_order();
		$order->set_billing_first_name( 'Ana' );
		$order->set_billing_last_name( 'Garcia' );
		$order->set_total( 10 );
		$order->save();

		$gateway = new WC_Gateway_GooglePay_Redirection_Redsys();
		$args    = $gateway->get_redsys_args( $order );
		$params  = json_decode( base64_decode( strtr( $args['Ds_MerchantParameters'], '-_', '+/' ) ), true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode

		$this->assertSame( 'Ana Garcia', $params['DS_MERCHANT_TITULAR'] );
	}
}
