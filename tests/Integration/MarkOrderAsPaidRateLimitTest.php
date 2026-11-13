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
		delete_transient( 'redys_order_temp_000000000701' );
		delete_option( 'woocommerce_redsys_settings' );
		unset( $_GET['Ds_MerchantParameters'], $_GET['Ds_Signature'], $_GET['Ds_SignatureVersion'] );
		WC()->payment_gateways()->init();
		parent::tear_down();
	}

	/**
	 * @var int
	 */
	private $order_id;

	/**
	 * Revised by S-046 (D-060): the wait is now spent only on a return that
	 * Redsys signed, so the fixture is a card order with a signed return. What
	 * the test protects is unchanged — a second call inside the 30-second
	 * window must not wait again. The order is put back to pending between the
	 * two calls so that only the per-order guard can stop the second one.
	 */
	public function test_repeat_calls_for_the_same_unpaid_order_are_rate_limited() {
		$secret = base64_encode( str_repeat( 'L', 24 ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		update_option(
			'woocommerce_redsys_settings',
			array(
				'enabled'      => 'yes',
				'testmode'     => 'no',
				'debug'        => 'no',
				'customer'     => '999999999',
				'secretsha256' => $secret,
			)
		);
		WC()->payment_gateways()->init();

		$order = wc_create_order();
		$order->set_total( 1.00 );
		$order->set_payment_method( 'redsys' );
		$order->save();
		$this->order_id = $order->get_id();
		set_transient( 'redys_order_temp_000000000701', $this->order_id, 3600 );

		$json  = wp_json_encode(
			array(
				'Ds_Order'        => '000000000701',
				'Ds_Response'     => '0000',
				'Ds_Amount'       => '100',
				'Ds_MerchantCode' => '999999999',
			)
		);
		$param = strtr( base64_encode( $json ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		$_GET['Ds_MerchantParameters'] = $param;
		$_GET['Ds_Signature']          = ( new RedsysLiteAPI() )->create_merchant_signature_notif( $secret, $param );
		$_GET['Ds_SignatureVersion']   = 'HMAC_SHA256_V1';

		$start = microtime( true );
		redsyslite_mark_order_as_paid( $this->order_id );
		$first_call_seconds = microtime( true ) - $start;

		$order = wc_get_order( $this->order_id );
		$order->set_status( 'pending' );
		$order->save();

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
		$this->assertTrue( wc_get_order( $this->order_id )->has_status( 'pending' ), 'The repeat call must not have processed the return again.' );
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
