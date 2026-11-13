<?php
/**
 * Reproduction tests for forge issue 112 (slice S-038): the cancel URL
 * handed to Redsys was HTML, not a URL, and a customer sent back to it
 * after Redsys had already cancelled the order was told the order could
 * no longer be cancelled. Each test was written and seen failing against
 * the code as it was before its fix.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class CancelUrlTest extends WP_UnitTestCase {

	const NO_LONGER_CANCELLABLE = 'Your order can no longer be cancelled. Please contact us if you need assistance.';

	public function tear_down() {
		unset( $_GET['cancel_order'], $_GET['order'], $_GET['order_id'], $_GET['redirect'], $_GET['_wpnonce'] );
		wc_clear_notices();
		wp_set_current_user( 0 );
		parent::tear_down();
	}

	public function redsys_protocol_gateways() {
		return array(
			'card'       => array( 'WC_Gateway_redsys' ),
			'Bizum'      => array( 'WC_Gateway_Bizum_Redsys' ),
			'Google Pay' => array( 'WC_Gateway_GooglePay_Redirection_Redsys' ),
		);
	}

	private function decoded_merchant_parameters( $args ) {
		$json = base64_decode( strtr( $args['Ds_MerchantParameters'], '-_', '+/' ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		return json_decode( $json, true );
	}

	private function cancelled_order_of( $payment_method ) {
		$customer = self::factory()->user->create( array( 'role' => 'customer' ) );
		wp_set_current_user( $customer );

		$order = wc_create_order( array( 'customer_id' => $customer ) );
		$order->set_total( 10 );
		$order->set_payment_method( $payment_method );
		$order->save();
		$order->update_status( 'cancelled', 'Cancelled by Redsys' );
		return $order;
	}

	private function return_through_cancel_url( $order ) {
		$query = array();
		parse_str( (string) wp_parse_url( WCRedL()->get_cancel_url_raw( $order ), PHP_URL_QUERY ), $query );
		foreach ( $query as $key => $value ) {
			$_GET[ $key ] = $value;
		}
		// An empty `redirect` keeps WC_Form_Handler::cancel_order() from exiting.
		$_GET['redirect'] = '';
		WC_Form_Handler::cancel_order();
	}

	/**
	 * Issue 112, first half: DS_MERCHANT_URLKO carried `&amp;` separators,
	 * so the browser came back with parameters named `amp;order_id` and
	 * `amp;_wpnonce` and WooCommerce's cancel handler never ran.
	 *
	 * @dataProvider redsys_protocol_gateways
	 */
	public function test_the_cancel_url_sent_to_redsys_is_a_url_not_html( $gateway_class ) {
		$order = wc_create_order();
		$order->set_total( 10 );
		$order->save();

		if ( 'WC_Gateway_GooglePay_Redirection_Redsys' === $gateway_class ) {
			// Pre-existing and out of scope here (deferred, S-042): this gateway reads
			// the billing name through the generic meta API instead of the order's getters.
			$this->setExpectedIncorrectUsage( 'is_internal_meta_key' );
		}

		$gateway = new $gateway_class();
		$params  = $this->decoded_merchant_parameters( $gateway->get_redsys_args( $order ) );

		$this->assertArrayHasKey( 'DS_MERCHANT_URLKO', $params );
		$this->assertStringNotContainsString( '&amp;', $params['DS_MERCHANT_URLKO'], 'DS_MERCHANT_URLKO is followed by a browser, so it must carry literal & separators.' );

		$query = array();
		parse_str( (string) wp_parse_url( $params['DS_MERCHANT_URLKO'], PHP_URL_QUERY ), $query );
		$this->assertSame( (string) $order->get_id(), $query['order_id'] ?? null );
		$this->assertSame( $order->get_order_key(), $query['order'] ?? null );
		$this->assertArrayHasKey( '_wpnonce', $query );
	}

	/**
	 * One canonical answer: outside an `href`, no gateway asks WooCommerce
	 * for the HTML-escaped cancel URL.
	 */
	public function test_no_gateway_uses_the_html_escaped_cancel_url_outside_markup() {
		$offenders = array();
		foreach ( glob( dirname( __DIR__, 2 ) . '/classes/*.php' ) as $file ) {
			if ( 'class-wc-gateway-redsys-global-lite.php' === basename( $file ) ) {
				continue; // The helper itself: its fallback is the one legitimate caller.
			}
			foreach ( file( $file ) as $number => $line ) {
				if ( false === strpos( $line, '->get_cancel_order_url()' ) || false !== strpos( $line, 'href=' ) ) {
					continue;
				}
				$offenders[] = basename( $file ) . ':' . ( $number + 1 );
			}
		}
		$this->assertSame( array(), $offenders );
	}

	public function test_the_cancel_url_helper_decodes_an_order_object_without_the_raw_method() {
		$legacy = new class() {
			public function get_cancel_order_url() {
				return 'https://example.org/cart/?cancel_order=true&amp;order_id=7&amp;_wpnonce=abc';
			}
		};

		$this->assertSame( 'https://example.org/cart/?cancel_order=true&order_id=7&_wpnonce=abc', WCRedL()->get_cancel_url_raw( $legacy ) );
		$this->assertSame( '', WCRedL()->get_cancel_url_raw( null ) );
	}

	/**
	 * Issue 112, second half: Redsys notifies the failure server to server
	 * (the order becomes `cancelled`) and then sends the customer to the
	 * cancel URL, where WooCommerce answered with an error.
	 *
	 * @dataProvider redsys_protocol_gateway_ids
	 */
	public function test_returning_to_an_order_redsys_already_cancelled_is_not_an_error( $payment_method ) {
		$order = $this->cancelled_order_of( $payment_method );

		$this->return_through_cancel_url( $order );

		$this->assertSame( 0, wc_notice_count( 'error' ), 'The customer cancelled at Redsys and must not be shown an error for it.' );
		$this->assertTrue( wc_has_notice( 'Your order was cancelled.', 'notice' ) );
		$this->assertSame( 'cancelled', wc_get_order( $order->get_id() )->get_status() );
	}

	public function redsys_protocol_gateway_ids() {
		return array(
			'card'       => array( 'redsys' ),
			'Bizum'      => array( 'bizumredsys' ),
			'Google Pay' => array( 'googlepayredirecredsys' ),
		);
	}

	/**
	 * Guards for the fix above: it reaches only the order named in a
	 * verified cancel request, and only orders paid through these gateways.
	 */
	public function test_an_order_of_another_gateway_keeps_woocommerce_behaviour() {
		$order = $this->cancelled_order_of( 'bacs' );

		$this->return_through_cancel_url( $order );

		$this->assertTrue( wc_has_notice( self::NO_LONGER_CANCELLABLE, 'error' ) );
	}

	public function test_a_cancelled_order_is_not_cancellable_outside_its_own_cancel_request() {
		$order = $this->cancelled_order_of( 'redsys' );
		$other = $this->cancelled_order_of( 'redsys' );

		$this->assertNotContains( 'cancelled', apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array( 'pending', 'failed' ), $order ) );

		$_GET['cancel_order'] = 'true';
		$_GET['order_id']     = (string) $other->get_id();
		$_GET['_wpnonce']     = wp_create_nonce( 'woocommerce-cancel_order' );
		$this->assertNotContains( 'cancelled', apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array( 'pending', 'failed' ), $order ) );

		$_GET['_wpnonce'] = 'not-a-nonce';
		$this->assertNotContains( 'cancelled', apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array( 'pending', 'failed' ), $other ) );

		// A third party applying the filter its own way gets its value back untouched.
		$_GET['_wpnonce'] = wp_create_nonce( 'woocommerce-cancel_order' );
		$this->assertSame( array( 'pending', 'failed' ), apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array( 'pending', 'failed' ) ) );
		$this->assertSame( 'pending', apply_filters( 'woocommerce_valid_order_statuses_for_cancel', 'pending', $other ) );
	}

	public function test_a_completed_order_is_never_made_cancellable() {
		$order = $this->cancelled_order_of( 'redsys' );
		$order->update_status( 'completed' );

		$this->return_through_cancel_url( $order );

		$this->assertTrue( wc_has_notice( self::NO_LONGER_CANCELLABLE, 'error' ) );
		$this->assertSame( 'completed', wc_get_order( $order->get_id() )->get_status() );
	}
}
