<?php
/**
 * Regression tests for WC_Gateway_Redsys_Global_Lite::prepare_order_number()
 * / clean_order_number().
 *
 * Found during the full-plugin review requested by the user 2026-08-01
 * (docs/decisions.md D-024): prepare_order_number() zero-pads the real
 * order ID to 12 digits, then replaces the first 3 characters with a
 * random prefix (previously wp_rand(1,999), which can be 1-3 digits) and
 * remembers the mapping in a short-lived transient. If a later
 * notification arrives after that transient has expired,
 * clean_order_number()'s fallback unconditionally strips a FIXED 3
 * characters and left-trims zeros to recover the real order ID — which
 * only works if the random prefix was genuinely always 3 characters. With
 * a 1-2 digit prefix, the fallback ate into the real order ID and could
 * resolve to the wrong order (or none).
 *
 * @package WooCommerce Redsys Gateway Light
 */

class GlobalLiteOrderNumberTest extends WP_UnitTestCase {

	/**
	 * @return WC_Gateway_Redsys_Global_Lite
	 */
	private function global_lite() {
		return WCRedL();
	}

	public function test_prepare_order_number_always_produces_a_fixed_width_number() {
		$lite = $this->global_lite();

		// Run many times: the old wp_rand(1,999) had roughly a 10% chance
		// per call of producing a 1- or 2-digit prefix, so this reliably
		// exercises the bug across enough iterations.
		for ( $order_id = 1; $order_id <= 200; $order_id++ ) {
			$generated = $lite->prepare_order_number( $order_id );
			$this->assertSame(
				12,
				strlen( $generated ),
				"prepare_order_number({$order_id}) must always return a 12-character string, or clean_order_number()'s post-transient-expiry fallback (which always strips exactly 3 characters) silently resolves the wrong order."
			);
			delete_transient( 'redys_order_temp_' . $generated );
		}
	}

	public function test_clean_order_number_recovers_the_real_order_id_after_the_transient_expires() {
		$lite = $this->global_lite();

		for ( $order_id = 1; $order_id <= 50; $order_id++ ) {
			$generated = $lite->prepare_order_number( $order_id );
			// Simulate the transient having expired.
			delete_transient( 'redys_order_temp_' . $generated );

			$recovered = $lite->clean_order_number( $generated );
			$this->assertSame(
				(string) $order_id,
				$recovered,
				"clean_order_number() must recover order {$order_id} from \"{$generated}\" via the fallback path once its transient is gone."
			);
		}
	}
}
