<?php
/**
 * Integration tests for AC-32 (D-045, slice S-060): the two actions the
 * Google Pay redirection gateway fires once a notification is settled.
 *
 * Both paths end in a `return`, so they run in-process (the paths of this
 * handler that end in `exit` are driven over HTTP in
 * tests/e2e/notification-outcomes.spec.js).
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class GatewayGooglePayHooksTest extends WP_UnitTestCase {

	/**
	 * A fixed, syntactically valid but non-production Base64 secret.
	 *
	 * @var string
	 */
	private $secret;

	/**
	 * What each action was called with, in order.
	 *
	 * @var array
	 */
	private $calls = array();

	public function set_up() {
		parent::set_up();
		if ( null === WC()->cart ) {
			wc_load_cart();
		}
		$this->secret = base64_encode( str_repeat( 'G', 24 ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$this->calls  = array();
		add_action( 'googlepayredirecredsys_post_payment_complete', array( $this, 'record_complete' ), 10, 5 );
		add_action( 'googlepayredirecredsys_post_payment_error', array( $this, 'record_error' ), 10, 5 );
	}

	public function tear_down() {
		remove_action( 'googlepayredirecredsys_post_payment_complete', array( $this, 'record_complete' ), 10 );
		remove_action( 'googlepayredirecredsys_post_payment_error', array( $this, 'record_error' ), 10 );
		parent::tear_down();
	}

	/**
	 * Listener: the "complete" action, with every argument it was given.
	 *
	 * @return void
	 */
	public function record_complete() {
		$this->calls[] = array( 'complete', func_get_args() );
	}

	/**
	 * Listener: the "error" action, with every argument it was given.
	 *
	 * @return void
	 */
	public function record_error() {
		$this->calls[] = array( 'error', func_get_args() );
	}

	/**
	 * Create a 1.00 order and deliver a signed notification for it.
	 *
	 * @param string $response Ds_Response.
	 * @param string $amount   Ds_Amount.
	 * @return WC_Order The order as stored afterwards.
	 */
	private function notify( $response, $amount = '100' ) {
		$order = wc_create_order();
		$order->set_total( 1.00 );
		$order->save();
		$ds_order = WCRedL()->prepare_order_number( $order->get_id() );

		$gateway                   = new WC_Gateway_GooglePay_Redirection_Redsys();
		$gateway->testmode         = 'yes';
		$gateway->customtestsha256 = $this->secret;
		$gateway->debug            = 'no';
		$gateway->orderdo          = 'processing';

		$param = strtr(
			base64_encode( // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				wp_json_encode(
					array(
						'Ds_Order'             => $ds_order,
						'Ds_Response'          => $response,
						'Ds_Amount'            => $amount,
						'Ds_MerchantCode'      => '999999999',
						'Ds_AuthorisationCode' => '123456',
					)
				)
			),
			'+/',
			'-_'
		);
		$api   = new RedsysLiteAPI();

		$gateway->successful_request(
			array(
				'Ds_MerchantParameters' => $param,
				'Ds_Signature'          => $api->create_merchant_signature_notif( $this->secret, $param ),
				'Ds_SignatureVersion'   => 'HMAC_SHA256_V1',
			)
		);

		return wc_get_order( $order->get_id() );
	}

	public function test_a_completed_payment_fires_the_complete_action_with_the_order_id() {
		$order = $this->notify( '0000' );

		$this->assertNotNull( $order->get_date_paid(), 'The payment was completed.' );
		$this->assertSame( array( array( 'complete', array( $order->get_id() ) ) ), $this->calls );
	}

	public function test_a_denied_payment_fires_the_error_action_with_the_order_id_and_the_error_text() {
		$order = $this->notify( '0190' );

		$this->assertSame( 'cancelled', $order->get_status() );
		$this->assertCount( 1, $this->calls );
		list( $action, $arguments ) = $this->calls[0];
		$this->assertSame( 'error', $action );
		$this->assertCount( 2, $arguments );
		$this->assertSame( $order->get_id(), $arguments[0] );
		// The response text, a space, and the (absent) Ds_ErrorCode text.
		$this->assertSame( WCRedL()->get_error( 190 ) . ' ', $arguments[1] );
		$this->assertNotSame( ' ', $arguments[1], 'Response 190 has a text.' );
	}

	public function test_a_notification_with_a_forged_signature_fires_neither_action() {
		$order = wc_create_order();
		$order->set_total( 1.00 );
		$order->save();

		$gateway                   = new WC_Gateway_GooglePay_Redirection_Redsys();
		$gateway->testmode         = 'yes';
		$gateway->customtestsha256 = $this->secret;
		$gateway->debug            = 'no';

		$param = base64_encode( // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			wp_json_encode(
				array(
					'Ds_Order'    => WCRedL()->prepare_order_number( $order->get_id() ),
					'Ds_Response' => '0000',
					'Ds_Amount'   => '100',
				)
			)
		);
		$gateway->successful_request(
			array(
				'Ds_MerchantParameters' => $param,
				'Ds_Signature'          => 'not-the-signature',
				'Ds_SignatureVersion'   => 'HMAC_SHA256_V1',
			)
		);

		$this->assertSame( array(), $this->calls );
		$this->assertNull( wc_get_order( $order->get_id() )->get_date_paid() );
	}
}
