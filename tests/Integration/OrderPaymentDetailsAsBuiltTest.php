<?php
/**
 * Integration tests for AC-55, AC-56 and AC-57 (D-045, slice S-060): the
 * payment details on the admin order screen, the transaction details in the
 * order-received text, and the declaration of compatibility with
 * High-Performance Order Storage.
 *
 * As-built: the tests describe the code as it is and change none of it.
 * Each case sits beside the one that takes the other branch — an order of
 * another gateway, an order that is not paid, a plugin that declared
 * nothing. The two screens in a real browser session are driven in
 * tests/e2e/as-built-screens.spec.js.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class OrderPaymentDetailsAsBuiltTest extends WP_UnitTestCase {

	const ORIGINAL_TEXT = 'Thank you. Your order has been received.';

	/**
	 * @return array[]
	 */
	public function data_redsys_protocol_gateways() {
		return array(
			'card'       => array( 'redsys' ),
			'Bizum'      => array( 'bizumredsys' ),
			'Google Pay' => array( 'googlepayredirecredsys' ),
		);
	}

	/**
	 * @return array[]
	 */
	public function data_other_gateways() {
		return array(
			'Inespay'       => array( 'inespayredsys' ),
			'bank transfer' => array( 'bacs' ),
		);
	}

	/**
	 * An order carrying what a Redsys notification stores, as it stores it
	 * (date and hour arrive URL-encoded and are kept that way — D-073).
	 *
	 * @param string $payment_method Gateway id.
	 * @param string $status         Order status.
	 * @return WC_Order
	 */
	private function order_with_redsys_data( $payment_method, $status = 'processing' ) {
		$order = wc_create_order();
		$order->set_total( 10 );
		$order->set_payment_method( $payment_method );
		$order->update_meta_data( '_payment_order_number_redsys', '000000123456' );
		$order->update_meta_data( '_payment_date_redsys', '06%2F10%2F2026' );
		$order->update_meta_data( '_payment_hour_redsys', '21%3A15' );
		$order->update_meta_data( '_authorisation_code_redsys', '654321' );
		$order->update_meta_data( '_order_fuc_redsys', '999008881' );
		$order->set_status( $status );
		$order->save();
		return wc_get_order( $order->get_id() );
	}

	/**
	 * What the plugin prints under the billing address of the admin order screen.
	 *
	 * @param WC_Order $order Order.
	 * @return string
	 */
	private function admin_details_of( $order ) {
		ob_start();
		add_redsys_meta_box( $order );
		return (string) ob_get_clean();
	}

	/**
	 * @dataProvider data_redsys_protocol_gateways
	 * @param string $gateway_id Gateway id.
	 */
	public function test_the_admin_order_screen_shows_the_payment_details( $gateway_id ) {
		$this->assertNotFalse( has_action( 'woocommerce_admin_order_data_after_billing_address', 'add_redsys_meta_box' ) );

		$details = $this->admin_details_of( $this->order_with_redsys_data( $gateway_id ) );

		$this->assertStringContainsString( 'Payment Details', $details );
		$this->assertStringContainsString( 'Paid with: </strong><br />' . $gateway_id . '</p>', $details );
		$this->assertStringContainsString( 'Redsys Order Number: </strong><br />000000123456</p>', $details );
		$this->assertStringContainsString( 'Redsys Date: </strong><br />06/10/2026</p>', $details );
		$this->assertStringContainsString( 'Redsys Hour: </strong><br />21:15</p>', $details );
		$this->assertStringContainsString( 'Redsys Authorisation Code: </strong><br />654321</p>', $details );
	}

	/**
	 * @dataProvider data_other_gateways
	 * @param string $gateway_id Gateway id.
	 */
	public function test_the_admin_order_screen_shows_nothing_for_an_order_of_another_gateway( $gateway_id ) {
		$this->assertSame( '', $this->admin_details_of( $this->order_with_redsys_data( $gateway_id ) ) );
	}

	public function test_the_admin_order_screen_leaves_out_what_the_order_does_not_have() {
		$order = wc_create_order();
		$order->set_payment_method( 'redsys' );
		$order->save();

		$details = $this->admin_details_of( wc_get_order( $order->get_id() ) );

		$this->assertStringContainsString( 'Paid with: </strong><br />redsys</p>', $details );
		foreach ( array( 'Redsys Order Number', 'Redsys Date', 'Redsys Hour', 'Redsys Authorisation Code' ) as $label ) {
			$this->assertStringNotContainsString( $label, $details );
		}
	}

	/**
	 * @dataProvider data_redsys_protocol_gateways
	 * @param string $gateway_id Gateway id.
	 */
	public function test_the_order_received_text_lists_the_transaction_details_of_a_paid_order( $gateway_id ) {
		$text = apply_filters( 'woocommerce_thankyou_order_received_text', self::ORIGINAL_TEXT, $this->order_with_redsys_data( $gateway_id ) );

		$this->assertStringContainsString( 'Thanks for your purchase, the details of your transaction are: <br />', $text );
		$this->assertStringContainsString( 'Website: ' . esc_url( get_site_url() ) . '<br />', $text );
		$this->assertStringContainsString( 'FUC: 999008881<br />', $text );
		$this->assertStringContainsString( 'Authorization Number: 654321<br />', $text );
		$this->assertStringContainsString( 'Commerce Name: ' . esc_html( get_bloginfo( 'name' ) ) . '<br />', $text );
		$this->assertStringContainsString( 'Date: 06/10/2026<br />', $text );
		$this->assertStringContainsString( 'Hour: 21:15<br />', $text );
		$this->assertStringNotContainsString( self::ORIGINAL_TEXT, $text, 'As built, the details replace WooCommerce\'s sentence instead of following it.' );
	}

	/**
	 * @dataProvider data_redsys_protocol_gateways
	 * @param string $gateway_id Gateway id.
	 */
	public function test_the_order_received_text_is_untouched_while_the_order_is_not_paid( $gateway_id ) {
		$order = $this->order_with_redsys_data( $gateway_id, 'pending' );

		$this->assertSame( self::ORIGINAL_TEXT, apply_filters( 'woocommerce_thankyou_order_received_text', self::ORIGINAL_TEXT, $order ) );
	}

	/**
	 * @dataProvider data_other_gateways
	 * @param string $gateway_id Gateway id.
	 */
	public function test_the_order_received_text_is_untouched_for_an_order_of_another_gateway( $gateway_id ) {
		$order = $this->order_with_redsys_data( $gateway_id );

		$this->assertSame( self::ORIGINAL_TEXT, apply_filters( 'woocommerce_thankyou_order_received_text', self::ORIGINAL_TEXT, $order ) );
	}

	public function test_the_plugin_declares_compatibility_with_high_performance_order_storage() {
		if ( ! class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			$this->markTestSkipped( 'This WooCommerce has no FeaturesUtil: there is nothing to declare compatibility to.' );
		}
		$this->assertTrue( did_action( 'before_woocommerce_init' ) > 0 );

		$plugin   = plugin_basename( dirname( __DIR__, 2 ) . '/woocommerce-redsys.php' );
		$declared = \Automattic\WooCommerce\Utilities\FeaturesUtil::get_compatible_features_for_plugin( $plugin );
		$this->assertSame( 'woo-redsys-gateway-light/woocommerce-redsys.php', $plugin );
		$this->assertContains( 'custom_order_tables', $declared['compatible'] );
		$this->assertNotContains( 'custom_order_tables', $declared['incompatible'] );

		// A plugin that declared nothing is not listed as compatible: the answer above is this plugin's doing.
		$silent = \Automattic\WooCommerce\Utilities\FeaturesUtil::get_compatible_features_for_plugin( 'a-plugin-that-declared-nothing/plugin.php' );
		$this->assertNotContains( 'custom_order_tables', $silent['compatible'] );
	}
}
