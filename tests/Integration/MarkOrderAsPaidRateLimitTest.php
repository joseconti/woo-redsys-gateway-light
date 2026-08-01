<?php
/**
 * Regression tests for redsyslite_mark_order_as_paid()'s rate limiting.
 *
 * Found during the full-plugin review requested by the user 2026-08-01
 * (docs/decisions.md D-024): this function ran an unauthenticated, blocking
 * sleep(5) on the order-received page before any signature check, reachable
 * by anyone with a valid order key (not a secret — distributed in
 * confirmation emails/URLs). Repeating the request cost 5s of a PHP
 * worker each time, and it also delayed every legitimate customer's
 * thank-you page load by 5s.
 *
 * The fix (woocommerce-redsys.php): a short-lived per-order transient
 * guards against repeat calls, and an already-paid order exits before the
 * sleep. These tests time actual calls to prove the guard works, rather
 * than only asserting on order state.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class MarkOrderAsPaidRateLimitTest extends WP_UnitTestCase {

	public function tear_down() {
		delete_transient( 'redsyslite_mark_paid_attempt_' . $this->order_id );
		parent::tear_down();
	}

	/**
	 * @var int
	 */
	private $order_id;

	public function test_repeat_calls_for_the_same_unpaid_order_are_rate_limited() {
		$order          = wc_create_order();
		$this->order_id = $order->get_id();

		$start = microtime( true );
		redsyslite_mark_order_as_paid( $this->order_id );
		$first_call_seconds = microtime( true ) - $start;

		$start = microtime( true );
		redsyslite_mark_order_as_paid( $this->order_id );
		$second_call_seconds = microtime( true ) - $start;

		$this->assertGreaterThanOrEqual(
			5,
			$first_call_seconds,
			'The first call for a not-yet-paid order is expected to hit the real sleep(5).'
		);
		$this->assertLessThan(
			1,
			$second_call_seconds,
			'A repeat call within the rate-limit window must return immediately instead of sleeping again — this is exactly the fix for the resource-exhaustion finding.'
		);
	}

	public function test_an_already_paid_order_skips_the_sleep_entirely() {
		$order = wc_create_order();
		$order->update_status( 'processing' );
		$this->order_id = $order->get_id();

		$start = microtime( true );
		redsyslite_mark_order_as_paid( $this->order_id );
		$elapsed_seconds = microtime( true ) - $start;

		$this->assertLessThan(
			1,
			$elapsed_seconds,
			'An order already in a paid status must return before the sleep(5), not after it — this is the common case on a repeat visit to the thank-you page.'
		);
	}
}
