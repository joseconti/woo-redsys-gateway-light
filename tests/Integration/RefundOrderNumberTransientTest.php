<?php
/**
 * Regression tests for the refund side of the "1 billion bug".
 *
 * clean_order_number()'s only mapping back from a Ds_Order to a real
 * WooCommerce order ID that never loses information is the
 * `redys_order_temp_*` transient created by prepare_order_number() at
 * payment time — but that transient has a 1h TTL, and a refund's
 * confirmation notification typically arrives hours or days after the
 * original payment, well past that window. process_refund() already knows
 * the real order_id for certain at refund-request time, so it must
 * re-save that mapping with a longer TTL (24h) before asking Redsys for
 * the refund, so the confirmation IPN can resolve it reliably.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class RefundOrderNumberTransientTest extends WP_UnitTestCase {

	public function tear_down() {
		remove_all_filters( 'pre_http_request' );
		parent::tear_down();
	}

	/**
	 * Stops process_refund() from ever reaching its check_redsys_refund()
	 * polling loop (sleep(5) up to 20 times): wp_remote_post() short-circuits
	 * to a WP_Error, ask_for_refund() returns it, and process_refund()
	 * returns immediately. The transient renewal happens BEFORE that call,
	 * so it is still observable.
	 */
	private function stub_http_as_failed() {
		add_filter(
			'pre_http_request',
			function () {
				return new WP_Error( 'redsyslite_test_stub', 'stubbed for test' );
			}
		);
	}

	/**
	 * @param object $gateway         Gateway instance.
	 * @param int    $order_id        Real order ID.
	 * @param string $transaction_id  The Ds_Order value stored at payment time.
	 */
	private function assert_refund_renews_the_transient( $gateway, $order_id, $transaction_id ) {
		$this->stub_http_as_failed();

		$gateway->process_refund( $order_id, 10 );

		$this->assertSame(
			$order_id,
			get_transient( 'redys_order_temp_' . $transaction_id ),
			'process_refund() must re-save the Ds_Order -> order_id mapping when a refund is requested, so a delayed confirmation notification can still resolve the order after the original payment-time transient (1h TTL) has expired.'
		);
	}

	public function test_redsys_process_refund_renews_the_order_number_transient() {
		$order = wc_create_order();
		$order->set_total( 10 );
		$order->save();
		$transaction_id = WCRedL()->prepare_order_number( $order->get_id() );
		delete_transient( 'redys_order_temp_' . $transaction_id );
		$order->update_meta_data( '_payment_order_number_redsys', $transaction_id );
		$order->save();

		$gateway = new WC_Gateway_redsys();
		$this->assert_refund_renews_the_transient( $gateway, $order->get_id(), $transaction_id );
	}

	public function test_bizum_process_refund_renews_the_order_number_transient() {
		$order = wc_create_order();
		$order->set_total( 10 );
		$order->save();
		$transaction_id = WCRedL()->prepare_order_number( $order->get_id() );
		delete_transient( 'redys_order_temp_' . $transaction_id );
		$order->update_meta_data( '_payment_order_number_redsys', $transaction_id );
		$order->save();

		$gateway = new WC_Gateway_Bizum_Redsys();
		$this->assert_refund_renews_the_transient( $gateway, $order->get_id(), $transaction_id );
	}

	public function test_googlepay_process_refund_renews_the_order_number_transient() {
		$order = wc_create_order();
		$order->set_total( 10 );
		$order->save();
		$transaction_id = WCRedL()->prepare_order_number( $order->get_id() );
		delete_transient( 'redys_order_temp_' . $transaction_id );
		$order->update_meta_data( '_payment_order_number_redsys', $transaction_id );
		$order->save();

		$gateway = new WC_Gateway_GooglePay_Redirection_Redsys();
		$this->assert_refund_renews_the_transient( $gateway, $order->get_id(), $transaction_id );
	}
}
