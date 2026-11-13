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

	/**
	 * The "1 billion bug": prepare_order_number() only reserves 9 digits for
	 * the real order ID inside the 12-character Ds_Order (str_pad(...,12)
	 * then substr_replace(..., 0, -9)). For an order_id of 10+ digits (HPOS
	 * installs with a long order history routinely reach this), the
	 * high-order digits are overwritten by the random prefix and are gone
	 * from the Ds_Order string itself — the substr()/ltrim() fallback can
	 * never reconstruct them once the mapping transient (1h TTL) expires.
	 * clean_order_number() must recover the order via the persistent
	 * `_payment_order_number_redsys` postmeta instead.
	 */
	public function test_clean_order_number_recovers_large_order_ids_via_persistent_meta_lookup() {
		$lite = $this->global_lite();

		$large_order_id = '9788419493178'; // 13 digits, as seen in a real HPOS report.
		$ds_order       = $lite->prepare_order_number( $large_order_id );
		delete_transient( 'redys_order_temp_' . $ds_order );

		// A real order stands in for the order the payment was originally
		// made against, carrying the persistent meta a gateway writes at
		// payment time.
		$order = wc_create_order();
		$order->update_meta_data( '_payment_order_number_redsys', $ds_order );
		$order->save();

		$recovered = $lite->clean_order_number( $ds_order );

		$this->assertSame(
			(string) $order->get_id(),
			$recovered,
			'clean_order_number() must recover the order via the persistent _payment_order_number_redsys meta lookup once the transient is gone, even when the substr()/ltrim() fallback is lossy for a 10+ digit order ID.'
		);
	}

	/**
	 * With neither a live transient nor a matching persistent meta record,
	 * clean_order_number() still has to return SOMETHING (its legacy
	 * substr()/ltrim() heuristic) rather than error out.
	 */
	public function test_clean_order_number_falls_back_to_the_legacy_heuristic_when_nothing_matches() {
		$lite = $this->global_lite();

		$ds_order = $lite->prepare_order_number( 42 );
		delete_transient( 'redys_order_temp_' . $ds_order );

		$this->assertSame(
			ltrim( substr( $ds_order, 3 ), '0' ),
			$lite->clean_order_number( $ds_order )
		);
	}
}
