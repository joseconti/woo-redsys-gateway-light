<?php
/**
 * Integration tests: the transaction limit of the Bizum and Inespay
 * gateways holds wherever an order can be paid (S-050).
 *
 * The limit existed only as a filter on the list of available gateways,
 * applied only on the checkout page and only against the cart total. It did
 * not apply on the order-pay page (the cart is usually empty there), nor in
 * a Store API request (the Blocks checkout), and nothing checked it again
 * when the payment was actually started.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class TransactionLimitTest extends WP_UnitTestCase {

	/**
	 * Requests that tried to leave during a test.
	 *
	 * @var int
	 */
	private $http_calls = 0;

	public function set_up() {
		parent::set_up();
		$this->http_calls = 0;
		add_filter(
			'pre_http_request',
			function () {
				++$this->http_calls;
				return new WP_Error( 'redsyslite_test_stub', 'stubbed for test' );
			}
		);
		if ( null === WC()->cart ) {
			wc_load_cart();
		}
		WC()->cart->set_total( 0 );
		wc_clear_notices();
	}

	public function tear_down() {
		remove_all_filters( 'pre_http_request' );
		remove_all_filters( 'woocommerce_is_checkout' );
		unset( $GLOBALS['wp']->query_vars['order-pay'] );
		delete_option( 'woocommerce_bizumredsys_settings' );
		delete_option( 'woocommerce_inespayredsys_settings' );
		WC()->cart->set_total( 0 );
		wc_clear_notices();
		parent::tear_down();
	}

	/**
	 * @return array[]
	 */
	public function gateways_with_a_limit() {
		return array(
			'Bizum'   => array( 'WC_Gateway_Bizum_Redsys', 'bizumredsys', 'disable_bizum' ),
			'Inespay' => array( 'WC_Gateway_Inespay_Redsys', 'inespayredsys', 'disable_inespay' ),
		);
	}

	/**
	 * @param string $class Gateway class name.
	 * @param string $limit Transaction limit setting.
	 * @return WC_Payment_Gateway
	 */
	private function gateway_with_limit( $class, $limit ) {
		// Both gateways read their settings in the constructor. The Inespay
		// credentials are not real ones: without any, it refuses to start a payment.
		$settings = array(
			'enabled'          => 'yes',
			'debug'            => 'no',
			'testmode'         => 'no',
			'transactionlimit' => $limit,
			'api_key'          => 'test-api-key',
			'api_token'        => 'test-api-token',
		);
		update_option( 'woocommerce_bizumredsys_settings', $settings );
		update_option( 'woocommerce_inespayredsys_settings', $settings );
		return new $class();
	}

	/**
	 * @param float $total Order total.
	 * @return WC_Order
	 */
	private function order_of( $total ) {
		$order = wc_create_order();
		$order->set_total( $total );
		$order->save();
		return $order;
	}

	/**
	 * @dataProvider gateways_with_a_limit
	 * @param string $class Gateway class name.
	 */
	public function test_an_order_above_the_limit_is_not_sent_to_the_processor( $class ) {
		$order  = $this->order_of( 150 );
		$result = $this->gateway_with_limit( $class, '100' )->process_payment( $order->get_id() );

		$this->assertSame( 'failure', $result['result'], 'An order above the merchant\'s limit was accepted for payment through this gateway.' );
		$this->assertSame( 0, $this->http_calls, 'Nothing may be sent to the processor for it.' );
		$this->assertSame( 1, wc_notice_count( 'error' ), 'The customer is told why.' );
	}

	/**
	 * @dataProvider gateways_with_a_limit
	 * @param string $class Gateway class name.
	 */
	public function test_an_order_at_the_limit_or_with_no_limit_starts_its_payment( $class ) {
		foreach ( array( '100', '', '0' ) as $limit ) {
			$this->http_calls = 0;
			wc_clear_notices();
			$order  = $this->order_of( 100 );
			$result = $this->gateway_with_limit( $class, $limit )->process_payment( $order->get_id() );

			if ( 'WC_Gateway_Inespay_Redsys' === $class ) {
				// Inespay starts by calling its API, which is stubbed here to fail: reaching it is the proof.
				$this->assertSame( 1, $this->http_calls, 'limit "' . $limit . '"' );
			} else {
				$this->assertSame( 'success', $result['result'], 'limit "' . $limit . '"' );
			}
		}
	}

	/**
	 * @dataProvider gateways_with_a_limit
	 * @param string $class      Gateway class name.
	 * @param string $gateway_id Gateway id.
	 * @param string $filter     Name of the gateway's availability callback.
	 */
	public function test_the_limit_applies_on_the_order_pay_page_where_the_cart_is_empty( $class, $gateway_id, $filter ) {
		add_filter( 'woocommerce_is_checkout', '__return_true' );
		$gateway = $this->gateway_with_limit( $class, '100' );

		$GLOBALS['wp']->query_vars['order-pay'] = $this->order_of( 150 )->get_id();
		$this->assertArrayNotHasKey( $gateway_id, $gateway->$filter( array( $gateway_id => $gateway ) ), 'Offered for an order above the limit.' );

		$GLOBALS['wp']->query_vars['order-pay'] = $this->order_of( 100 )->get_id();
		$this->assertArrayHasKey( $gateway_id, $gateway->$filter( array( $gateway_id => $gateway ) ) );
	}

	/**
	 * A Store API request (the Blocks checkout) is not "the checkout page".
	 *
	 * @dataProvider gateways_with_a_limit
	 * @param string $class      Gateway class name.
	 * @param string $gateway_id Gateway id.
	 * @param string $filter     Name of the gateway's availability callback.
	 */
	public function test_the_limit_applies_outside_the_checkout_page( $class, $gateway_id, $filter ) {
		add_filter( 'woocommerce_is_checkout', '__return_false' );
		$gateway = $this->gateway_with_limit( $class, '100' );

		WC()->cart->set_total( 150 );
		$this->assertArrayNotHasKey( $gateway_id, $gateway->$filter( array( $gateway_id => $gateway ) ), 'Offered for a cart above the limit.' );

		WC()->cart->set_total( 100 );
		$this->assertArrayHasKey( $gateway_id, $gateway->$filter( array( $gateway_id => $gateway ) ) );
	}

	/**
	 * Bizum's payment form can be opened without process_payment(): the
	 * order-pay address of a pending order whose payment method is already
	 * Bizum renders it directly.
	 */
	public function test_bizum_does_not_build_its_payment_form_for_an_order_above_the_limit() {
		$gateway = $this->gateway_with_limit( 'WC_Gateway_Bizum_Redsys', '100' );

		$order = $this->order_of( 150 );
		ob_start();
		$gateway->receipt_page( $order->get_id() );
		$above = ob_get_clean();

		$this->assertStringNotContainsString( 'Ds_MerchantParameters', $above, 'The signed payment form was built for an order above the limit.' );
		$this->assertStringNotContainsString( '<form', $above );
		$this->assertNotSame( '', trim( wp_strip_all_tags( $above ) ), 'The customer is told why there is nothing to pay with.' );

		$order = $this->order_of( 100 );
		ob_start();
		$gateway->receipt_page( $order->get_id() );
		$this->assertStringContainsString( 'Ds_MerchantParameters', ob_get_clean(), 'An order at the limit still gets its form.' );
	}

	/**
	 * The comparison at the cent.
	 *
	 * @dataProvider gateways_with_a_limit
	 * @param string $class Gateway class name.
	 */
	public function test_the_limit_is_compared_to_the_cent( $class ) {
		$gateway = $this->gateway_with_limit( $class, '200.50' );

		$this->assertFalse( $gateway->is_over_transaction_limit( '200.50' ) );
		$this->assertTrue( $gateway->is_over_transaction_limit( '200.51' ) );
		$this->assertFalse( $gateway->is_over_transaction_limit( 0 ) );
	}
}
