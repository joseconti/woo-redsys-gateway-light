<?php
/**
 * The hooks of docs/api/INDEX.md that no other test names fire, where the
 * reference says they fire and with the arguments it gives:
 *
 * - `redsys_status_pending` (filter) — the list of order statuses that count
 *   as not paid;
 * - `woocommerce_redsys_args` (filter, card and Bizum) and
 *   `woocommerce_googlepayredirecredsys_args` (filter) — the three fields of
 *   the payment form;
 * - `valid_redsys_standard_ipn_request`,
 *   `valid_bizumredsys_standard_ipn_request` and
 *   `valid_googlepayredirecredsys_standard_ipn_request` (actions) — a
 *   notification that passed its signature check, with the posted fields.
 *
 * Slice S-075 (self-audit: a documented extension point with no test).
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

/**
 * Thrown by the listener of the notification actions, so the handler that
 * shares the action (successful_request(), which may end in `exit`) never
 * runs inside the test runner (L-012).
 */
class Redsyslite_Test_Stop_After_Hook extends RuntimeException {}

class DocumentedHooksFireTest extends WP_UnitTestCase {

	/**
	 * What the listeners saw.
	 *
	 * @var array
	 */
	private $seen = array();

	public function tear_down() {
		unset( $_POST['Ds_MerchantParameters'], $_POST['Ds_Signature'], $_POST['Ds_SignatureVersion'] );
		parent::tear_down();
	}

	/**
	 * @return array<string, array{0: string, 1: string}>
	 */
	public function gateways() {
		return array(
			'card'       => array( 'WC_Gateway_redsys', 'redsys' ),
			'Bizum'      => array( 'WC_Gateway_Bizum_Redsys', 'bizumredsys' ),
			'Google Pay' => array( 'WC_Gateway_GooglePay_Redirection_Redsys', 'googlepayredirecredsys' ),
		);
	}

	/**
	 * @param string $class Gateway class name.
	 * @return WC_Payment_Gateway
	 */
	private function configured_gateway( $class ) {
		$gateway               = new $class();
		$gateway->secretsha256 = base64_encode( str_repeat( 'S', 24 ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$gateway->testmode     = 'no';
		$gateway->debug        = 'no';
		return $gateway;
	}

	/**
	 * The handler answers `200 OK` with header() before it fires the action.
	 * On the command line the test runner has already written output, so
	 * that call raises a warning a web request never sees. Only that
	 * warning is set aside; any other still fails the test.
	 */
	private function ignore_the_status_header() {
		set_error_handler( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_set_error_handler
			function ( $number, $message ) {
				return 0 === strpos( $message, 'Cannot modify header information' );
			},
			E_WARNING
		);
	}

	public function test_an_order_in_a_status_added_through_the_pending_filter_counts_as_not_paid() {
		$order = wc_create_order();
		$order->set_status( 'failed' );
		$order->save();

		$this->assertTrue( WCRedL()->is_paid( $order->get_id() ), 'Without the filter a failed order counts as paid (AC-12).' );

		$seen   = array();
		$filter = function ( $statuses ) use ( &$seen ) {
			$seen[]     = $statuses;
			$statuses[] = 'failed';
			return $statuses;
		};
		add_filter( 'redsys_status_pending', $filter );
		$paid = WCRedL()->is_paid( $order->get_id() );
		remove_filter( 'redsys_status_pending', $filter );

		$this->assertNotEmpty( $seen, 'The filter never ran.' );
		$this->assertContains( 'pending', $seen[0], 'The filter receives the default list.' );
		$this->assertNotContains( 'failed', $seen[0] );
		$this->assertFalse( $paid );
	}

	/**
	 * The filter name per gateway: Bizum reuses the card gateway's, as the
	 * reference records.
	 *
	 * @return array<string, array{0: string, 1: string}>
	 */
	public function gateways_and_args_filters() {
		return array(
			'card'       => array( 'WC_Gateway_redsys', 'woocommerce_redsys_args' ),
			'Bizum'      => array( 'WC_Gateway_Bizum_Redsys', 'woocommerce_redsys_args' ),
			'Google Pay' => array( 'WC_Gateway_GooglePay_Redirection_Redsys', 'woocommerce_googlepayredirecredsys_args' ),
		);
	}

	/**
	 * @dataProvider gateways_and_args_filters
	 * @param string $class  Gateway class name.
	 * @param string $filter The filter the reference documents for it.
	 */
	public function test_the_args_filter_receives_the_three_form_fields_and_its_answer_is_the_form( $class, $filter ) {
		$order = wc_create_order();
		$order->set_total( 12.34 );
		$order->save();
		$gateway = $this->configured_gateway( $class );
		if ( 'WC_Gateway_GooglePay_Redirection_Redsys' === $class ) {
			// Pre-existing and deferred (S-042): this class reads the billing name through the generic meta API.
			$this->setExpectedIncorrectUsage( 'is_internal_meta_key' );
		}

		$seen     = array();
		$callback = function ( $args ) use ( &$seen ) {
			$seen[]                 = $args;
			$args['redsyslite_tag'] = 'added by the filter';
			return $args;
		};
		add_filter( $filter, $callback );
		$args = $gateway->get_redsys_args( $order );
		remove_filter( $filter, $callback );

		$this->assertCount( 1, $seen, 'The filter runs once per form.' );
		$this->assertSame( array( 'Ds_SignatureVersion', 'Ds_MerchantParameters', 'Ds_Signature' ), array_keys( $seen[0] ) );
		$this->assertSame( 'HMAC_SHA256_V1', $seen[0]['Ds_SignatureVersion'] );
		$this->assertNotEmpty( $seen[0]['Ds_MerchantParameters'] );
		$this->assertNotEmpty( $seen[0]['Ds_Signature'] );
		$this->assertSame( 'added by the filter', $args['redsyslite_tag'], 'What the filter returns is what the form is built from.' );
	}

	/**
	 * @dataProvider gateways
	 * @param string $class Gateway class name.
	 * @param string $id    Gateway id.
	 */
	public function test_a_verified_notification_fires_the_gateways_action_with_the_posted_fields( $class, $id ) {
		$order = wc_create_order();
		$order->set_total( 1 );
		$order->save();
		$gateway = $this->configured_gateway( $class );
		$params  = base64_encode( wp_json_encode( array( 'Ds_Order' => WCRedL()->prepare_order_number( $order->get_id() ), 'Ds_Response' => '0000', 'Ds_Amount' => '100' ) ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$api     = new RedsysLiteAPI();

		$_POST['Ds_SignatureVersion']   = 'HMAC_SHA256_V1';
		$_POST['Ds_MerchantParameters'] = $params;
		$_POST['Ds_Signature']          = $api->create_merchant_signature_notif( $gateway->secretsha256, $params );

		$listener = function ( $post ) {
			$this->seen[] = $post;
			throw new Redsyslite_Test_Stop_After_Hook();
		};
		add_action( 'valid_' . $id . '_standard_ipn_request', $listener, 1 );
		$this->ignore_the_status_header();
		try {
			$gateway->check_ipn_response();
			$this->fail( 'The action did not fire for a notification that verifies.' );
		} catch ( Redsyslite_Test_Stop_After_Hook $stop ) {
			$this->assertCount( 1, $this->seen );
		} finally {
			restore_error_handler();
			remove_action( 'valid_' . $id . '_standard_ipn_request', $listener, 1 );
		}

		$this->assertSame( $params, $this->seen[0]['Ds_MerchantParameters'] );
		$this->assertSame( $_POST['Ds_Signature'], $this->seen[0]['Ds_Signature'] );
		$this->assertSame( 'HMAC_SHA256_V1', $this->seen[0]['Ds_SignatureVersion'] );
		$this->assertSame( 'pending', wc_get_order( $order->get_id() )->get_status(), 'The listener stopped the request before the order was touched.' );
	}

	/**
	 * The counterpart: with a signature that does not verify the action does
	 * not fire, so the test above passes for the verification and not for
	 * the call.
	 *
	 * @dataProvider gateways
	 * @param string $class Gateway class name.
	 * @param string $id    Gateway id.
	 */
	public function test_a_notification_that_does_not_verify_fires_no_action( $class, $id ) {
		$gateway = $this->configured_gateway( $class );

		$_POST['Ds_SignatureVersion']   = 'HMAC_SHA256_V1';
		$_POST['Ds_MerchantParameters'] = base64_encode( wp_json_encode( array( 'Ds_Order' => '123000000001', 'Ds_Response' => '0000', 'Ds_Amount' => '100' ) ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$_POST['Ds_Signature']          = 'not-a-signature';

		$fired    = 0;
		$listener = function () use ( &$fired ) {
			++$fired;
			throw new Redsyslite_Test_Stop_After_Hook();
		};
		add_action( 'valid_' . $id . '_standard_ipn_request', $listener, 1 );
		try {
			$gateway->check_ipn_response();
		} catch ( WPDieException $refused ) {
			$this->assertStringContainsStringIgnoringCase( 'do not access this page directly', $refused->getMessage() );
		} finally {
			remove_action( 'valid_' . $id . '_standard_ipn_request', $listener, 1 );
		}

		$this->assertSame( 0, $fired );
	}
}
