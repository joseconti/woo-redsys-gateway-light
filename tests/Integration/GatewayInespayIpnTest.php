<?php
/**
 * Integration tests for WC_Gateway_Inespay_Redsys::handle_callback().
 *
 * Unlike the other three gateways (Redsys, Bizum, GooglePay — all backed by
 * RedsysLiteAPI's 3DES-derived-key HMAC), Inespay authenticates callbacks
 * with a plain HMAC-SHA256 over `dataReturn` using the merchant's API key
 * directly: `signatureDataReturn = base64( hash_hmac( 'sha256', dataReturn,
 * API_KEY, false ) )` — note the inner HMAC is hex (the `false` "raw output"
 * argument), then THAT hex string is base64-encoded, not the raw bytes.
 * This suite has its own fixture builder rather than reusing RedsysLiteAPI,
 * which is unrelated to this algorithm.
 *
 * handle_callback() also differs in shape from the other gateways:
 * instead of returning true/false, it calls wp_die() on every path. WordPress
 * core's own PHPUnit test scaffold (WP_UnitTestCase) turns that into a
 * catchable WPDieException carrying the die message and HTTP response code,
 * which is what these tests assert on.
 *
 * `api_key` is a `protected` property (unlike the public `secretsha256` on
 * the other three gateways), so it can't be set directly on the instance —
 * it is read from the WooCommerce gateway settings option
 * (`woocommerce_inespayredsys_settings`) in the constructor, so the fixture
 * sets that option before instantiating the gateway.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class GatewayInespayIpnTest extends WP_UnitTestCase {

	/**
	 * A fixed, non-production API key — not a real credential.
	 *
	 * @var string
	 */
	private $api_key = 'test-api-key-not-a-real-secret';

	public function set_up() {
		parent::set_up();
		update_option(
			'woocommerce_inespayredsys_settings',
			array(
				'api_key'  => $this->api_key,
				'debug'    => 'no',
				'testmode' => 'no',
				'orderdo'  => '',
			)
		);
	}

	public function tear_down() {
		unset( $_POST['dataReturn'], $_POST['signatureDataReturn'] );
		delete_option( 'woocommerce_inespayredsys_settings' );
		parent::tear_down();
	}

	/**
	 * @return WC_Gateway_Inespay_Redsys
	 */
	private function configured_gateway() {
		return new WC_Gateway_Inespay_Redsys();
	}

	/**
	 * @param array $payload The decoded dataReturn payload.
	 * @return array{data_return: string, signature: string}
	 */
	private function build_signed_callback( $payload ) {
		$data_return = base64_encode( wp_json_encode( $payload ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$signature   = base64_encode( hash_hmac( 'sha256', $data_return, $this->api_key, false ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		return array(
			'data_return' => $data_return,
			'signature'   => $signature,
		);
	}

	/**
	 * Creates a real, EUR-denominated WC_Order matched to the given payin ID
	 * and amount-in-cents, the way get_order_by_payin_id() looks it up
	 * (a `_inespay_single_payin_id` order meta value).
	 *
	 * @param string $payin_id     The singlePayinId to map.
	 * @param float  $total        Order total (major units, e.g. 1.00).
	 * @return WC_Order
	 */
	private function create_matched_order( $payin_id, $total = 1.00 ) {
		$order = wc_create_order();
		$order->set_currency( 'EUR' );
		$order->set_total( $total );
		$order->save();
		$order->update_meta_data( '_inespay_single_payin_id', $payin_id );
		$order->save();
		return $order;
	}

	public function test_rejects_the_callback_when_the_api_key_is_not_configured() {
		update_option(
			'woocommerce_inespayredsys_settings',
			array(
				'api_key'  => '',
				'debug'    => 'no',
				'testmode' => 'no',
			)
		);
		$gateway = $this->configured_gateway();

		$fixture                        = $this->build_signed_callback( array( 'singlePayinId' => 'payin-1', 'codStatus' => 'OK', 'amount' => '100' ) );
		$_POST['dataReturn']            = $fixture['data_return'];
		$_POST['signatureDataReturn']   = $fixture['signature'];

		try {
			$gateway->handle_callback();
			$this->fail( 'Expected handle_callback() to call wp_die().' );
		} catch ( WPDieException $e ) {
			$this->assertSame( 'KO', $e->getMessage() );
			$this->assertSame( 401, $e->getCode() );
		}
	}

	public function test_rejects_a_callback_with_a_forged_signature() {
		$this->create_matched_order( 'payin-2' );
		$gateway = $this->configured_gateway();

		$fixture                      = $this->build_signed_callback( array( 'singlePayinId' => 'payin-2', 'codStatus' => 'OK', 'amount' => '100' ) );
		$_POST['dataReturn']          = $fixture['data_return'];
		$_POST['signatureDataReturn'] = base64_encode( str_repeat( 'x', 32 ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		try {
			$gateway->handle_callback();
			$this->fail( 'Expected handle_callback() to call wp_die().' );
		} catch ( WPDieException $e ) {
			$this->assertSame( 'KO', $e->getMessage() );
			$this->assertSame( 401, $e->getCode() );
		}
	}

	public function test_rejects_a_callback_tampered_with_after_signing() {
		$this->create_matched_order( 'payin-3' );
		$gateway = $this->configured_gateway();

		$fixture = $this->build_signed_callback( array( 'singlePayinId' => 'payin-3', 'codStatus' => 'OK', 'amount' => '100' ) );

		// Genuine signature, but a different (attacker-edited) dataReturn.
		$tampered_data_return          = base64_encode( wp_json_encode( array( 'singlePayinId' => 'payin-3', 'codStatus' => 'OK', 'amount' => '999999' ) ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$_POST['dataReturn']           = $tampered_data_return;
		$_POST['signatureDataReturn']  = $fixture['signature'];

		try {
			$gateway->handle_callback();
			$this->fail( 'Expected handle_callback() to call wp_die().' );
		} catch ( WPDieException $e ) {
			$this->assertSame( 'KO', $e->getMessage() );
			$this->assertSame( 401, $e->getCode() );
		}
	}

	public function test_accepts_a_correctly_signed_callback_and_completes_the_order() {
		$order = $this->create_matched_order( 'payin-4', 1.00 );
		$this->assertNotSame( 'completed', $order->get_status() );

		// Pre-existing issue (not introduced by this test, not fixed here —
		// see docs/lessons-learned.md L-004): handle_callback() writes
		// '_payment_method' via the generic update_order_meta() helper
		// instead of $order->set_payment_method(), which is WooCommerce
		// internal meta and triggers a doing_it_wrong() notice.
		$this->setExpectedIncorrectUsage( 'is_internal_meta_key' );

		$gateway = $this->configured_gateway();

		$fixture                       = $this->build_signed_callback(
			array(
				'singlePayinId' => 'payin-4',
				'codStatus'     => 'OK',
				'amount'        => '100',
			)
		);
		$_POST['dataReturn']           = $fixture['data_return'];
		$_POST['signatureDataReturn']  = $fixture['signature'];

		try {
			$gateway->handle_callback();
			$this->fail( 'Expected handle_callback() to call wp_die().' );
		} catch ( WPDieException $e ) {
			$this->assertSame( 'OK', $e->getMessage() );
			$this->assertSame( 200, $e->getCode() );
		}

		$order = wc_get_order( $order->get_id() );
		$this->assertTrue( $order->has_status( array( 'processing', 'completed' ) ) );
	}

	public function test_flags_an_order_on_hold_when_the_signed_amount_does_not_match_the_order_total() {
		// Defence in depth: even a validly-signed callback must not
		// complete an order whose total doesn't match the signed amount.
		$order = $this->create_matched_order( 'payin-5', 1.00 );

		$gateway = $this->configured_gateway();

		$fixture                       = $this->build_signed_callback(
			array(
				'singlePayinId' => 'payin-5',
				'codStatus'     => 'OK',
				'amount'        => '999999',
			)
		);
		$_POST['dataReturn']           = $fixture['data_return'];
		$_POST['signatureDataReturn']  = $fixture['signature'];

		try {
			$gateway->handle_callback();
			$this->fail( 'Expected handle_callback() to call wp_die().' );
		} catch ( WPDieException $e ) {
			$this->assertSame( 'OK', $e->getMessage() );
			$this->assertSame( 200, $e->getCode() );
		}

		$order = wc_get_order( $order->get_id() );
		$this->assertSame( 'on-hold', $order->get_status() );
	}
}
