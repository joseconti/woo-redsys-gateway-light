<?php
/**
 * Integration tests: a refund is confirmed only by a confirmation that
 * arrives for it (S-049).
 *
 * process_refund() of the card, Bizum and Google Pay gateways sends the
 * request to Redsys and then waits for Redsys's notification, which the
 * notification handler records as a per-order flag (a transient). The flag
 * had no expiry and was never cleared before a new request, so one left
 * over from earlier — a confirmation that arrived after the wait had ended,
 * or a refund made in the Redsys back office — made the NEXT refund of that
 * order report success at once, whatever Redsys answered. WooCommerce then
 * recorded a refund that moved no money.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class RefundConfirmationFlagTest extends WP_UnitTestCase {

	/**
	 * A fixed, syntactically valid but non-production Base64 secret.
	 *
	 * @var string
	 */
	private $secret;

	/**
	 * Transients created by a test.
	 *
	 * @var string[]
	 */
	private $transients = array();

	/**
	 * What the confirmation flag held at the moment the refund request left.
	 *
	 * @var mixed
	 */
	private $flag_when_asked = 'the request never left';

	public function set_up() {
		parent::set_up();
		$this->secret          = base64_encode( str_repeat( 'F', 24 ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$this->transients      = array();
		$this->flag_when_asked = 'the request never left';
	}

	public function tear_down() {
		remove_all_filters( 'pre_http_request' );
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
	public function redsys_protocol_gateways() {
		return array(
			'card'       => array( 'WC_Gateway_redsys', 'redsys' ),
			'Bizum'      => array( 'WC_Gateway_Bizum_Redsys', 'bizumredsys' ),
			'Google Pay' => array( 'WC_Gateway_GooglePay_Redirection_Redsys', 'googlepayredirecredsys' ),
		);
	}

	/**
	 * A paid order that has the Redsys order number a refund needs.
	 *
	 * @return WC_Order
	 */
	private function refundable_order() {
		$order = wc_create_order();
		$order->set_total( 10 );
		$order->update_meta_data( '_payment_order_number_redsys', '000000' . $order->get_id() );
		$order->update_meta_data( '_redsys_secretsha256', $this->secret );
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
		$gateway->testmode     = 'no';
		$gateway->debug        = 'no';
		return $gateway;
	}

	/**
	 * Redsys accepts the HTTP request and sends no confirmation: the answer
	 * a declined or unprocessed refund gets. Records the flag as it is when
	 * the request leaves.
	 *
	 * @param int $order_id Order being refunded.
	 */
	private function redsys_takes_the_request_and_confirms_nothing( $order_id ) {
		add_filter(
			'pre_http_request',
			function () use ( $order_id ) {
				$this->flag_when_asked = get_transient( $order_id . '_redsys_refund' );
				return array(
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
		);
	}

	/**
	 * @dataProvider redsys_protocol_gateways
	 * @param string $class      Gateway class name.
	 * @param string $gateway_id Gateway id.
	 */
	public function test_a_confirmation_left_over_from_before_does_not_confirm_a_new_refund( $class, $gateway_id ) {
		$order = $this->refundable_order();
		set_transient( $order->get_id() . '_redsys_refund', 'yes' );

		$this->redsys_takes_the_request_and_confirms_nothing( $order->get_id() );
		// One look for the confirmation instead of twenty-one: five seconds, not a hundred.
		add_filter( 'woocommerce_' . $gateway_id . '_refund_confirmation_attempts', '__return_zero' );

		$result = $this->configured_gateway( $class )->process_refund( $order->get_id(), 4 );

		$this->assertNotTrue( $result, 'A refund Redsys never confirmed was reported as done: WooCommerce would record a refund that moved no money.' );
		$this->assertFalse( $this->flag_when_asked, 'The request left with an old confirmation still in place.' );
		$this->assertFalse( get_transient( $order->get_id() . '_redsys_refund' ) );
	}

	/**
	 * @dataProvider redsys_protocol_gateways
	 * @param string $class      Gateway class name.
	 * @param string $gateway_id Gateway id.
	 */
	public function test_a_confirmation_that_arrives_while_waiting_confirms_the_refund( $class, $gateway_id ) {
		$order    = $this->refundable_order();
		$order_id = $order->get_id();
		$gateway  = $this->configured_gateway( $class );

		// The confirmation "arrives" right after the request leaves.
		add_filter(
			'pre_http_request',
			function () use ( $gateway, $order_id ) {
				$gateway->set_refund_confirmed( $order_id );
				return array(
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
		);
		add_filter( 'woocommerce_' . $gateway_id . '_refund_confirmation_attempts', '__return_zero' );

		$this->assertTrue( $gateway->process_refund( $order_id, 4 ) );
		$this->assertFalse( get_transient( $order_id . '_redsys_refund' ), 'A confirmation is used once.' );
	}

	/**
	 * @dataProvider redsys_protocol_gateways
	 * @param string $class Gateway class name.
	 */
	public function test_a_confirmation_does_not_outlive_the_wait_by_long( $class ) {
		$order = $this->refundable_order();

		$this->configured_gateway( $class )->set_refund_confirmed( $order->get_id() );

		$this->assertSame( 'yes', get_transient( $order->get_id() . '_redsys_refund' ) );
		$expires = (int) get_option( '_transient_timeout_' . $order->get_id() . '_redsys_refund' );
		$this->assertGreaterThan( time(), $expires, 'The confirmation must carry an expiry.' );
		$this->assertLessThanOrEqual( time() + 10 * MINUTE_IN_SECONDS, $expires );
	}

	/**
	 * The notification handler itself: a signed refund confirmation from
	 * Redsys records the flag through set_refund_confirmed(), expiry included.
	 *
	 * @dataProvider redsys_protocol_gateways
	 * @param string $class Gateway class name.
	 */
	public function test_a_signed_refund_notification_records_a_confirmation_that_expires( $class ) {
		$order    = $this->refundable_order();
		$ds_order = '000000' . $order->get_id();
		set_transient( 'redys_order_temp_' . $ds_order, $order->get_id(), 3600 );

		$json  = wp_json_encode(
			array(
				'Ds_Order'           => $ds_order,
				'Ds_Response'        => '0900',
				'Ds_Amount'          => '400',
				'Ds_TransactionType' => '3',
				'Ds_MerchantCode'    => '999999999',
			)
		);
		$param = strtr( base64_encode( $json ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		$this->configured_gateway( $class )->successful_request(
			array(
				'Ds_MerchantParameters' => $param,
				'Ds_Signature'          => ( new RedsysLiteAPI() )->create_merchant_signature_notif( $this->secret, $param ),
				'Ds_SignatureVersion'   => 'HMAC_SHA256_V1',
			)
		);

		$this->assertSame( 'yes', get_transient( $order->get_id() . '_redsys_refund' ) );
		$this->assertGreaterThan( time(), (int) get_option( '_transient_timeout_' . $order->get_id() . '_redsys_refund' ), 'The handler must record the confirmation with an expiry.' );
	}
}
