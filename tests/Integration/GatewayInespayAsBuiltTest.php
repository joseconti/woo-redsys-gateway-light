<?php
/**
 * Integration tests for the Inespay criteria that were recorded as built and
 * never driven (D-045, slice S-060): AC-35, AC-41, AC-42, AC-43 and AC-54.
 *
 * Every path here ends in a `return` or in wp_die(), which the WordPress
 * test scaffold turns into a WPDieException, so they run in-process. The
 * Inespay API is never reached: `pre_http_request` answers in its place.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class GatewayInespayAsBuiltTest extends WP_UnitTestCase {

	/**
	 * A fixed, non-production API key — not a real credential.
	 *
	 * @var string
	 */
	private $api_key = 'test-api-key-not-a-real-secret';

	/**
	 * The `pre_http_request` callback of the running test, removed in tear_down().
	 *
	 * @var callable|null
	 */
	private $intercept = null;

	/**
	 * How many requests the gateway tried to send.
	 *
	 * @var int
	 */
	private $requests = 0;

	public function set_up() {
		parent::set_up();
		if ( null === WC()->cart ) {
			wc_load_cart();
		}
		wc_clear_notices();
		$this->requests = 0;
		$this->configure();
	}

	public function tear_down() {
		if ( $this->intercept ) {
			remove_filter( 'pre_http_request', $this->intercept, 10 );
			$this->intercept = null;
		}
		unset( $_POST['dataReturn'], $_POST['signatureDataReturn'] );
		WC()->customer->set_billing_country( '' );
		WC()->customer->set_shipping_country( '' );
		wc_clear_notices();
		delete_option( 'woocommerce_inespayredsys_settings' );
		parent::tear_down();
	}

	/**
	 * Store the gateway settings the constructor reads.
	 *
	 * @param array $overrides Settings that differ from the complete configuration.
	 * @return void
	 */
	private function configure( $overrides = array() ) {
		update_option(
			'woocommerce_inespayredsys_settings',
			array_merge(
				array(
					'enabled'   => 'yes',
					'api_key'   => $this->api_key,
					'api_token' => 'test-api-token-not-a-real-secret',
					'debug'     => 'no',
					'testmode'  => 'yes',
					'orderdo'   => '',
				),
				$overrides
			)
		);
	}

	/**
	 * Answer every outgoing HTTP request with the given response.
	 *
	 * @param array|WP_Error $response What the Inespay API "answers".
	 * @return void
	 */
	private function api_answers( $response ) {
		$this->intercept = function () use ( $response ) {
			++$this->requests;
			return $response;
		};
		add_filter( 'pre_http_request', $this->intercept, 10 );
	}

	/**
	 * A response of the Inespay API.
	 *
	 * @param int   $code HTTP status.
	 * @param array $body Decoded body.
	 * @return array
	 */
	private function api_response( $code, $body ) {
		return array(
			'headers'  => array(),
			'response' => array(
				'code'    => $code,
				'message' => '',
			),
			'body'     => wp_json_encode( $body ),
			'cookies'  => array(),
		);
	}

	/**
	 * A pending order, optionally already matched to an Inespay pay-in.
	 *
	 * @param string $payin_id Pay-in ID, or '' for none.
	 * @param float  $total    Order total.
	 * @param string $currency Order currency.
	 * @return WC_Order
	 */
	private function create_order( $payin_id = '', $total = 10.00, $currency = 'EUR' ) {
		$order = wc_create_order();
		$order->set_currency( $currency );
		$order->set_total( $total );
		if ( '' !== $payin_id ) {
			$order->update_meta_data( '_inespay_single_payin_id', $payin_id );
		}
		$order->save();
		return $order;
	}

	/**
	 * Deliver a callback signed with the configured API key and return what the handler answered.
	 *
	 * @param array $payload The decoded dataReturn payload.
	 * @return WPDieException
	 */
	private function deliver_signed_callback( $payload ) {
		$data_return                  = base64_encode( wp_json_encode( $payload ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$_POST['dataReturn']          = $data_return;
		$_POST['signatureDataReturn'] = base64_encode( hash_hmac( 'sha256', $data_return, $this->api_key, false ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		try {
			( new WC_Gateway_Inespay_Redsys() )->handle_callback();
		} catch ( WPDieException $e ) {
			return $e;
		}
		$this->fail( 'handle_callback() always ends in wp_die().' );
	}

	/**
	 * The notes of an order, newest first.
	 *
	 * @param WC_Order $order Order.
	 * @return string[]
	 */
	private function notes_of( $order ) {
		return wp_list_pluck( wc_get_order_notes( array( 'order_id' => $order->get_id() ) ), 'content' );
	}

	/**
	 * Assert the customer was sent back to the checkout with exactly one error notice.
	 *
	 * @param array  $result  What process_payment() returned.
	 * @param string $message The notice text.
	 * @return void
	 */
	private function assert_stays_on_checkout_with_error( $result, $message ) {
		$this->assertSame( 'failure', $result['result'] );
		$this->assertSame( wc_get_checkout_url(), $result['redirect'], 'The customer stays on the checkout page.' );
		$notices = wc_get_notices( 'error' );
		$this->assertCount( 1, $notices );
		$this->assertSame( $message, $notices[0]['notice'] );
	}

	/**
	 * AC-35: the two credentials. Without either, no request leaves the site.
	 *
	 * @dataProvider data_missing_credentials
	 *
	 * @param array $settings The credential left empty.
	 */
	public function test_checkout_shows_an_error_when_a_credential_is_missing( $settings ) {
		$this->configure( $settings );
		$this->api_answers(
			$this->api_response(
				200,
				array(
					'singlePayinLink' => 'https://example.com/pay',
					'singlePayinId'   => 'payin-35',
				)
			)
		);
		$order = $this->create_order();

		$result = ( new WC_Gateway_Inespay_Redsys() )->process_payment( $order->get_id() );

		$this->assert_stays_on_checkout_with_error( $result, 'Payment error: Inespay credentials are missing.' );
		$this->assertSame( 0, $this->requests, 'Nothing is sent to Inespay without both credentials.' );
	}

	public function data_missing_credentials() {
		return array(
			'no API key'   => array( array( 'api_key' => '' ) ),
			'no API token' => array( array( 'api_token' => '' ) ),
		);
	}

	/**
	 * AC-35: what the API can answer short of a usable payment link.
	 *
	 * @dataProvider data_unusable_api_answers
	 *
	 * @param array|WP_Error $response The API's answer.
	 */
	public function test_checkout_shows_an_error_when_the_api_gives_no_usable_payment_link( $response ) {
		$this->api_answers( $response );
		$order = $this->create_order();

		$result = ( new WC_Gateway_Inespay_Redsys() )->process_payment( $order->get_id() );

		$this->assertSame( 1, $this->requests, 'The request was made: the failure is the answer, not a missing setting.' );
		$this->assert_stays_on_checkout_with_error( $result, 'Could not start payment with Inespay. Please try again or use another method.' );
		$this->assertSame( '', (string) wc_get_order( $order->get_id() )->get_meta( '_inespay_single_payin_id' ), 'No pay-in is recorded for a payment that did not start.' );
	}

	public function data_unusable_api_answers() {
		$complete = array(
			'singlePayinLink' => 'https://example.com/pay',
			'singlePayinId'   => 'payin-35',
		);
		return array(
			'the call fails'            => array( new WP_Error( 'http_request_failed', 'Connection timed out' ) ),
			'a non-200 status'          => array( $this->api_response( 500, $complete ) ),
			'no link in the answer'     => array( $this->api_response( 200, array( 'singlePayinId' => 'payin-35' ) ) ),
			'no pay-in ID in the answer' => array( $this->api_response( 200, array( 'singlePayinLink' => 'https://example.com/pay' ) ) ),
		);
	}

	/**
	 * AC-35's counterpart, so the cases above are known to fail for their own
	 * reason: with both credentials and a complete answer the payment starts.
	 */
	public function test_checkout_redirects_to_the_payment_link_when_the_api_answers_in_full() {
		$this->api_answers(
			$this->api_response(
				200,
				array(
					'singlePayinLink' => 'https://example.com/pay',
					'singlePayinId'   => 'payin-35',
				)
			)
		);
		$order = $this->create_order();

		$result = ( new WC_Gateway_Inespay_Redsys() )->process_payment( $order->get_id() );

		$this->assertSame( 'success', $result['result'] );
		$this->assertSame( 'https://example.com/pay', $result['redirect'] );
		$this->assertSame( 0, wc_notice_count( 'error' ) );
	}

	/**
	 * AC-41: a paid callback for an order in another currency.
	 */
	public function test_a_paid_callback_for_an_order_not_in_eur_puts_it_on_hold() {
		$order = $this->create_order( 'payin-41', 10.00, 'USD' );

		$answer = $this->deliver_signed_callback(
			array(
				'singlePayinId' => 'payin-41',
				'codStatus'     => 'OK',
				'amount'        => '1000',
			)
		);

		$this->assertSame( 'OK', $answer->getMessage() );
		$order = wc_get_order( $order->get_id() );
		$this->assertSame( 'on-hold', $order->get_status() );
		$this->assertNull( $order->get_date_paid(), 'The payment is not completed.' );
		$this->assertStringContainsString(
			'Inespay validation error: unexpected order currency (USD). Inespay settles in EUR. Payment NOT completed.',
			implode( "\n", $this->notes_of( $order ) )
		);
	}

	/**
	 * AC-41's counterpart: the same callback for the same total in EUR completes the payment.
	 */
	public function test_the_same_paid_callback_for_an_order_in_eur_completes_it() {
		// handle_callback() writes `_payment_method` through the generic meta helper (L-004).
		$this->setExpectedIncorrectUsage( 'is_internal_meta_key' );
		$order = $this->create_order( 'payin-41b', 10.00, 'EUR' );

		$this->deliver_signed_callback(
			array(
				'singlePayinId' => 'payin-41b',
				'codStatus'     => 'OK',
				'amount'        => '1000',
			)
		);

		$order = wc_get_order( $order->get_id() );
		$this->assertNotNull( $order->get_date_paid() );
		$this->assertNotSame( 'on-hold', $order->get_status() );
	}

	/**
	 * AC-42: a signed callback whose status is neither OK nor SETTLED.
	 */
	public function test_a_signed_callback_with_another_status_adds_a_note_and_stores_the_inespay_fields_without_changing_the_order_status() {
		$order = $this->create_order( 'payin-42' );
		$this->assertSame( 'pending', $order->get_status() );

		$answer = $this->deliver_signed_callback(
			array(
				'singlePayinId'   => 'payin-42',
				'codStatus'       => 'REJECTED',
				'amount'          => '1000',
				'debtorAccount'   => 'ES0000000000000000000001',
				'debtorName'      => 'Ada Lovelace',
				'reference'       => '123000000042',
				'creditorAccount' => 'ES0000000000000000000002',
			)
		);

		$this->assertSame( 'OK', $answer->getMessage() );
		$this->assertSame( 200, $answer->getCode() );
		$order = wc_get_order( $order->get_id() );
		$this->assertSame( 'pending', $order->get_status(), 'The order status does not change.' );
		$this->assertNull( $order->get_date_paid() );
		$this->assertContains( 'Inespay callback received with status: REJECTED', $this->notes_of( $order ) );
		$this->assertSame( 'payin-42', $order->get_meta( '_inespay_single_payin_id' ) );
		$this->assertSame( 'REJECTED', $order->get_meta( '_inespay_status' ) );
		$this->assertSame( 'ES0000000000000000000001', $order->get_meta( '_inespay_debtor_account' ) );
		$this->assertSame( 'Ada Lovelace', $order->get_meta( '_inespay_debtor_name' ) );
		$this->assertSame( '123000000042', $order->get_meta( '_inespay_reference' ) );
		$this->assertSame( 'ES0000000000000000000002', $order->get_meta( '_inespay_creditor_account' ) );
	}

	/**
	 * AC-43: which country decides, and which countries are allowed.
	 *
	 * @dataProvider data_countries
	 *
	 * @param string $shipping Customer's shipping country.
	 * @param string $billing  Customer's billing country.
	 * @param string $base     Store base country.
	 * @param bool   $offered  Whether Inespay is offered.
	 */
	public function test_inespay_is_offered_by_country( $shipping, $billing, $base, $offered ) {
		update_option( 'woocommerce_default_country', $base );
		WC()->customer->set_shipping_country( $shipping );
		WC()->customer->set_billing_country( $billing );

		$this->assertSame( $offered, ( new WC_Gateway_Inespay_Redsys() )->is_available() );
	}

	public function data_countries() {
		return array(
			'shipping ES'                                  => array( 'ES', '', 'US', true ),
			'shipping PT'                                  => array( 'PT', '', 'US', true ),
			'shipping IT'                                  => array( 'IT', '', 'US', true ),
			'shipping FR'                                  => array( 'FR', '', 'ES', false ),
			'shipping decides over billing (FR over ES)'   => array( 'FR', 'ES', 'ES', false ),
			'shipping decides over billing (ES over FR)'   => array( 'ES', 'FR', 'US', true ),
			'no shipping country: billing decides (PT)'    => array( '', 'PT', 'US', true ),
			'no shipping country: billing decides (DE)'    => array( '', 'DE', 'ES', false ),
			'neither known: the store base decides (ES)'   => array( '', '', 'ES', true ),
			'neither known: the store base decides (IT)'   => array( '', '', 'IT', true ),
			'neither known: the store base decides (US)'   => array( '', '', 'US', false ),
		);
	}

	/**
	 * AC-43 is about the country only when the gateway is enabled.
	 */
	public function test_inespay_is_not_offered_when_disabled_even_in_an_allowed_country() {
		$this->configure( array( 'enabled' => 'no' ) );
		WC()->customer->set_shipping_country( 'ES' );

		$this->assertFalse( ( new WC_Gateway_Inespay_Redsys() )->is_available() );
	}

	/**
	 * AC-54: an order that was never paid through Inespay.
	 */
	public function test_a_refund_for_an_order_without_a_pay_in_id_is_an_error_and_calls_nothing() {
		$this->api_answers( $this->api_response( 200, array( 'status' => '200' ) ) );
		$order = $this->create_order();

		$result = ( new WC_Gateway_Inespay_Redsys() )->process_refund( $order->get_id(), 5.00, 'test' );

		$this->assertWPError( $result );
		$this->assertSame( 'inespay_refund_missing_payin', $result->get_error_code() );
		$this->assertSame( 0, $this->requests );
	}

	/**
	 * AC-54: what the API can answer short of accepting the refund.
	 *
	 * @dataProvider data_refused_refunds
	 *
	 * @param array|WP_Error $response The API's answer.
	 * @param string         $code     The error code process_refund() returns.
	 */
	public function test_a_refund_the_api_does_not_accept_is_an_error_and_adds_no_note( $response, $code ) {
		$this->api_answers( $response );
		$order = $this->create_order( 'payin-54' );

		$result = ( new WC_Gateway_Inespay_Redsys() )->process_refund( $order->get_id(), 5.00, 'test' );

		$this->assertSame( 1, $this->requests );
		$this->assertWPError( $result );
		$this->assertSame( $code, $result->get_error_code() );
		$this->assertSame( array(), $this->notes_of( $order ), 'A refund that did not start leaves no "refund initiated" note.' );
	}

	public function data_refused_refunds() {
		return array(
			'the call fails'                  => array( new WP_Error( 'http_request_failed', 'Connection timed out' ), 'http_request_failed' ),
			'a non-200 HTTP status'           => array( $this->api_response( 500, array( 'status' => '200' ) ), 'inespay_refund_failed' ),
			'a body status other than 200'    => array( $this->api_response( 200, array( 'status' => '400' ) ), 'inespay_refund_failed' ),
			'no status in the body'           => array( $this->api_response( 200, array() ), 'inespay_refund_failed' ),
		);
	}

	/**
	 * AC-54: the accepted refund.
	 */
	public function test_an_accepted_refund_adds_a_note_with_the_amount_and_the_pay_in_id() {
		$this->api_answers( $this->api_response( 200, array( 'status' => '200' ) ) );
		$order = $this->create_order( 'payin-54' );

		$result = ( new WC_Gateway_Inespay_Redsys() )->process_refund( $order->get_id(), 5.00, 'test' );

		$this->assertTrue( $result );
		$this->assertContains( 'Inespay refund initiated for 500 EUR. Payin ID: payin-54', $this->notes_of( $order ) );
	}
}
