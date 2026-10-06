<?php
/**
 * Integration tests for AC-50 and AC-52 (D-045, slice S-060): what a refund
 * from the order screen sends to Redsys, how it waits for the confirmation,
 * and what it answers when the refund cannot be asked for or is never
 * confirmed.
 *
 * As-built: the tests describe the code as it is and change none of it.
 * No request reaches Redsys: `pre_http_request` answers in its place and
 * records what was sent. The waits are real (`sleep( 5 )` per look), so the
 * number of looks is kept small through the gateway's own filter and the
 * default of that filter is read without running it out.
 *
 * AC-69 (which confirmation counts) and AC-51 (the order-number mapping) are
 * in RefundConfirmationFlagTest.php and RefundOrderNumberTransientTest.php.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class RefundAsBuiltTest extends WP_UnitTestCase {

	/**
	 * A fixed, syntactically valid but non-production Base64 secret.
	 *
	 * @var string
	 */
	private $secret;

	/**
	 * Requests the gateway tried to send: URL and arguments.
	 *
	 * @var array[]
	 */
	private $requests = array();

	/**
	 * How many times the gateway looked for the confirmation.
	 *
	 * @var int
	 */
	private $looks = 0;

	/**
	 * Hooks added by a test, as [ name, callback ].
	 *
	 * @var array[]
	 */
	private $hooks = array();

	/**
	 * Transients created by a test.
	 *
	 * @var string[]
	 */
	private $transients = array();

	public function set_up() {
		parent::set_up();
		$this->secret     = base64_encode( str_repeat( 'R', 24 ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$this->requests   = array();
		$this->looks      = 0;
		$this->hooks      = array();
		$this->transients = array();
	}

	public function tear_down() {
		remove_all_filters( 'pre_http_request' );
		foreach ( $this->hooks as $hook ) {
			remove_filter( $hook[0], $hook[1], 10 );
		}
		foreach ( array( 'redsys', 'bizumredsys', 'googlepayredirecredsys' ) as $gateway_id ) {
			remove_all_filters( 'woocommerce_' . $gateway_id . '_refund_confirmation_attempts' );
		}
		foreach ( $this->transients as $transient ) {
			delete_transient( $transient );
		}
		parent::tear_down();
	}

	/**
	 * @return array[]
	 */
	public function data_redsys_protocol_gateways() {
		return array(
			'card'       => array( 'WC_Gateway_redsys', 'redsys' ),
			'Bizum'      => array( 'WC_Gateway_Bizum_Redsys', 'bizumredsys' ),
			'Google Pay' => array( 'WC_Gateway_GooglePay_Redirection_Redsys', 'googlepayredirecredsys' ),
		);
	}

	/**
	 * A paid order, with or without the Redsys order number a refund needs.
	 *
	 * @param bool $with_number Whether the order has its Redsys order number.
	 * @return WC_Order
	 */
	private function paid_order( $with_number = true ) {
		$order = wc_create_order();
		$order->set_total( 10 );
		if ( $with_number ) {
			$order->update_meta_data( '_payment_order_number_redsys', '000000' . $order->get_id() );
		}
		// Bizum and Google Pay sign a refund with the secret kept with the order.
		$order->update_meta_data( '_redsys_secretsha256', $this->secret );
		$order->update_meta_data( '_payment_terminal_redsys', '1' );
		$order->save();

		$this->transients[] = 'redys_order_temp_000000' . $order->get_id();
		$this->transients[] = $order->get_id() . '_redsys_refund';
		return $order;
	}

	/**
	 * @param string $class Gateway class name.
	 * @return WC_Payment_Gateway
	 */
	private function configured_gateway( $class ) {
		$gateway               = new $class();
		$gateway->secretsha256 = $this->secret;
		$gateway->customer     = '999008881';
		$gateway->terminal     = '1';
		$gateway->testmode     = 'no';
		$gateway->debug        = 'no';
		return $gateway;
	}

	/**
	 * Redsys answers the HTTP request (or the request fails) and what was
	 * sent is recorded.
	 *
	 * @param array|WP_Error|null $answer What the request gets; null is a plain 200.
	 */
	private function redsys_answers( $answer = null ) {
		if ( null === $answer ) {
			$answer = array(
				'headers'  => array(),
				'body'     => '',
				'response' => array(
					'code'    => 200,
					'message' => 'OK',
				),
				'cookies'  => array(),
				'filename' => null,
			);
		}
		add_filter(
			'pre_http_request',
			function ( $preempt, $args, $url ) use ( $answer ) {
				$this->requests[] = array( $url, $args );
				return $answer;
			},
			10,
			3
		);
	}

	/**
	 * Count every look for the confirmation of this order and, when asked,
	 * let the confirmation arrive right after one of them.
	 *
	 * @param WC_Payment_Gateway $gateway       Gateway that is waiting.
	 * @param int                $order_id      Order being refunded.
	 * @param int                $arrives_after Look after which the confirmation arrives; 0 is never.
	 */
	private function watch_the_wait( $gateway, $order_id, $arrives_after = 0 ) {
		$callback = function () use ( $gateway, $order_id, $arrives_after ) {
			++$this->looks;
			if ( $arrives_after === $this->looks ) {
				$gateway->set_refund_confirmed( $order_id );
				// This look came before the confirmation: it finds none.
				return 'not yet';
			}
			// Not answered here: the look reads what is really stored.
			return false;
		};
		$this->hooks[] = array( 'pre_transient_' . $order_id . '_redsys_refund', $callback );
		add_filter( 'pre_transient_' . $order_id . '_redsys_refund', $callback );
	}

	/**
	 * Limit the looks after the first, and record the default the gateway offered.
	 *
	 * @param string $gateway_id Gateway id.
	 * @param int    $further    Looks after the first.
	 * @param mixed  $offered    Receives the default number of further looks.
	 */
	private function further_looks( $gateway_id, $further, &$offered = null ) {
		add_filter(
			'woocommerce_' . $gateway_id . '_refund_confirmation_attempts',
			function ( $attempts ) use ( $further, &$offered ) {
				$offered = $attempts;
				return $further;
			}
		);
	}

	/**
	 * The merchant parameters of a recorded request, decoded.
	 *
	 * @param array $request Recorded request.
	 * @return array
	 */
	private function parameters_of( $request ) {
		return json_decode( base64_decode( $request[1]['body']['Ds_MerchantParameters'] ), true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
	}

	/**
	 * The signature Redsys would compute for these parameters with this secret.
	 *
	 * @param array  $parameters Decoded merchant parameters.
	 * @param string $secret     Signing secret.
	 * @return string
	 */
	private function signature_for( $parameters, $secret ) {
		$api = new RedsysLiteAPI();
		foreach ( $parameters as $key => $value ) {
			$api->set_parameter( $key, $value );
		}
		$api->create_merchant_parameters();
		return $api->create_merchant_signature( $secret );
	}

	/**
	 * @dataProvider data_redsys_protocol_gateways
	 * @param string $class      Gateway class name.
	 * @param string $gateway_id Gateway id.
	 */
	public function test_a_refund_sends_one_signed_type_3_request_for_the_stored_order_number( $class, $gateway_id ) {
		$order   = $this->paid_order();
		$gateway = $this->configured_gateway( $class );
		$this->redsys_answers();
		$this->watch_the_wait( $gateway, $order->get_id(), 1 );
		$this->further_looks( $gateway_id, 1, $offered );

		$this->assertTrue( $gateway->process_refund( $order->get_id(), 4 ) );

		$this->assertCount( 1, $this->requests );
		$this->assertStringContainsString( 'redsys.es', (string) wp_parse_url( $this->requests[0][0], PHP_URL_HOST ) );
		$this->assertSame( 'POST', $this->requests[0][1]['method'] );

		$body       = $this->requests[0][1]['body'];
		$parameters = $this->parameters_of( $this->requests[0] );
		$this->assertSame( 'HMAC_SHA256_V1', $body['Ds_SignatureVersion'] );
		$this->assertSame( '3', $parameters['DS_MERCHANT_TRANSACTIONTYPE'] );
		$this->assertSame( '000000' . $order->get_id(), $parameters['DS_MERCHANT_ORDER'] );
		$this->assertSame( '400', $parameters['DS_MERCHANT_AMOUNT'] );
		$this->assertSame( '999008881', $parameters['DS_MERCHANT_MERCHANTCODE'] );
		$this->assertSame( '1', $parameters['DS_MERCHANT_TERMINAL'] );

		$this->assertSame( $this->signature_for( $parameters, $this->secret ), $body['Ds_Signature'], 'The request must be signed with the secret of the payment.' );
		$this->assertNotSame( $this->signature_for( $parameters, base64_encode( str_repeat( 'X', 24 ) ) ), $body['Ds_Signature'] ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		$this->assertSame( 20, $offered, 'By default the gateway looks twenty more times after the first: twenty-one in all.' );
	}

	/**
	 * @dataProvider data_redsys_protocol_gateways
	 * @param string $class      Gateway class name.
	 * @param string $gateway_id Gateway id.
	 */
	public function test_a_refund_is_reported_done_at_the_first_look_that_finds_the_confirmation( $class, $gateway_id ) {
		$order   = $this->paid_order();
		$gateway = $this->configured_gateway( $class );
		$this->redsys_answers();
		// The confirmation arrives between the first look and the second; three more are allowed.
		$this->watch_the_wait( $gateway, $order->get_id(), 1 );
		$this->further_looks( $gateway_id, 3 );

		$started = microtime( true );
		$result  = $gateway->process_refund( $order->get_id(), 4 );
		$elapsed = microtime( true ) - $started;

		$this->assertTrue( $result );
		$this->assertSame( 2, $this->looks, 'The gateway must stop looking once the confirmation is there.' );
		$this->assertGreaterThanOrEqual( 9.5, $elapsed, 'Two looks, five seconds before each.' );
		$this->assertLessThan( 15, $elapsed );
	}

	/**
	 * @dataProvider data_redsys_protocol_gateways
	 * @param string $class      Gateway class name.
	 * @param string $gateway_id Gateway id.
	 */
	public function test_a_refund_nobody_confirms_fails_after_the_last_look( $class, $gateway_id ) {
		$order   = $this->paid_order();
		$gateway = $this->configured_gateway( $class );
		$this->redsys_answers();
		$this->watch_the_wait( $gateway, $order->get_id() );
		$this->further_looks( $gateway_id, 1 );

		$started = microtime( true );
		$result  = $gateway->process_refund( $order->get_id(), 4 );
		$elapsed = microtime( true ) - $started;

		$this->assertFalse( $result, 'A refund Redsys never confirmed must be reported as failed, not as an error object and not as done.' );
		$this->assertCount( 1, $this->requests, 'The request is sent once, whatever the number of looks.' );
		$this->assertSame( 2, $this->looks, 'One look plus the one further look allowed.' );
		$this->assertGreaterThanOrEqual( 9.5, $elapsed );
	}

	/**
	 * @dataProvider data_redsys_protocol_gateways
	 * @param string $class Gateway class name.
	 */
	public function test_a_refund_for_an_order_without_its_redsys_order_number_is_an_error_and_sends_nothing( $class ) {
		$order   = $this->paid_order( false );
		$gateway = $this->configured_gateway( $class );
		$this->redsys_answers();
		$this->watch_the_wait( $gateway, $order->get_id() );

		$result = $gateway->process_refund( $order->get_id(), 4 );

		$this->assertWPError( $result );
		$this->assertSame( 'Refund Failed: No transaction ID', $result->get_error_message() );
		$this->assertCount( 0, $this->requests );
		$this->assertSame( 0, $this->looks );
	}

	/**
	 * @dataProvider data_redsys_protocol_gateways
	 * @param string $class Gateway class name.
	 */
	public function test_a_refund_whose_request_does_not_reach_redsys_is_an_error_and_is_not_waited_for( $class ) {
		$order   = $this->paid_order();
		$gateway = $this->configured_gateway( $class );
		$this->redsys_answers( new WP_Error( 'http_request_failed', 'Could not resolve host' ) );
		$this->watch_the_wait( $gateway, $order->get_id() );

		$started = microtime( true );
		$result  = $gateway->process_refund( $order->get_id(), 4 );
		$elapsed = microtime( true ) - $started;

		$this->assertWPError( $result );
		$this->assertSame( 'Could not resolve host', $result->get_error_message() );
		$this->assertCount( 1, $this->requests );
		$this->assertSame( 0, $this->looks );
		$this->assertLessThan( 4, $elapsed, 'A request that failed is not waited for.' );
	}
}
