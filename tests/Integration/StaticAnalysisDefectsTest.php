<?php
/**
 * Reproduction tests for the low defects PHPStan found on the 7.1.0
 * candidate (docs/decisions.md D-102, slice S-092). Each one was written
 * and seen failing against the code as it was before its fix.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class StaticAnalysisDefectsTest extends WP_UnitTestCase {

	/**
	 * @var int[]
	 */
	private $tokens = array();

	public function tear_down() {
		foreach ( $this->tokens as $token_id ) {
			$token = WC_Payment_Tokens::get( $token_id );
			if ( $token ) {
				delete_option( 'token_type_' . $token->get_token() );
				WC_Payment_Tokens::delete( $token_id );
			}
		}
		wp_set_current_user( 0 );
		parent::tear_down();
	}

	/**
	 * @return array[]
	 */
	public function data_redsys_protocol_gateways() {
		return array(
			'card'       => array( 'WC_Gateway_redsys' ),
			'Bizum'      => array( 'WC_Gateway_Bizum_Redsys' ),
			'Google Pay' => array( 'WC_Gateway_GooglePay_Redirection_Redsys' ),
		);
	}

	/**
	 * D-102 (a): with debug on, a refund of an order without its Redsys
	 * order number read an undefined variable and never logged why it failed.
	 *
	 * @dataProvider data_redsys_protocol_gateways
	 * @param string $class Gateway class name.
	 */
	public function test_a_refund_without_transaction_id_is_logged_when_debug_is_on( $class ) {
		$order = wc_create_order();
		$order->set_total( 10 );
		$order->save();

		$gateway        = new $class();
		$gateway->debug = 'yes';
		$gateway->log   = new class() {
			/**
			 * @var string[]
			 */
			public $lines = array();

			public function add( $handle, $message ) {
				$this->lines[] = (string) $message;
				return true;
			}
		};

		$result = $gateway->process_refund( $order->get_id(), 4 );

		$this->assertWPError( $result );
		$this->assertContains( 'Refund Failed: No transaction ID', $gateway->log->lines );
	}

	/**
	 * D-102 (b): the 3DS2 shipping-address usage fields were computed for a
	 * customer with an account and then dropped, so Redsys never got them.
	 */
	public function test_acct_info_sends_the_shipping_address_usage_of_a_customer_with_an_account() {
		$user_id = self::factory()->user->create();
		wp_set_current_user( $user_id );
		$order = $this->order_with_shipping_address( $user_id );

		$emv3ds = json_decode( WCPSD2L()->get_acctinfo( $order ), true );

		$this->assertArrayHasKey( 'shipAddressUsage', $emv3ds['acctInfo'] );
		$this->assertMatchesRegularExpression( '/^\d{8}$/', $emv3ds['acctInfo']['shipAddressUsage'] );
		$this->assertContains( $emv3ds['acctInfo']['shipAddressUsageInd'], array( '01', '02', '03', '04' ) );
	}

	/**
	 * A guest has no account history: the fields stay out, with no notice.
	 */
	public function test_acct_info_of_a_guest_has_no_shipping_address_usage() {
		$order = $this->order_with_shipping_address( 0 );

		$emv3ds = json_decode( WCPSD2L()->get_acctinfo( $order ), true );

		$this->assertSame( '01', $emv3ds['acctInfo']['chAccAgeInd'] );
		$this->assertArrayNotHasKey( 'shipAddressUsage', $emv3ds['acctInfo'] );
		$this->assertArrayNotHasKey( 'shipAddressUsageInd', $emv3ds['acctInfo'] );
	}

	/**
	 * D-102 (c): a WPML language missing from the plugin's table returned
	 * nothing; it now gets the same English code as an empty table.
	 */
	public function test_get_lang_code_of_an_unknown_language_is_english() {
		$this->assertSame( '1', WCRedL()->get_lang_code( 'es' ) );
		$this->assertSame( '2', WCRedL()->get_lang_code( 'xx-unknown' ) );
	}

	/**
	 * D-102 (c): no tokens returned nothing instead of false.
	 */
	public function test_check_type_exist_in_tokens_without_tokens_is_false() {
		$this->assertFalse( WCRedL()->check_type_exist_in_tokens( array(), 'C' ) );
	}

	/**
	 * Found while fixing D-102 (c): the method gave up at the first token of
	 * another type, against its own contract ("true if the type exists in
	 * the tokens").
	 */
	public function test_check_type_exist_in_tokens_looks_past_a_token_of_another_type() {
		$user_id = self::factory()->user->create();
		$tokens  = array(
			$this->redsys_token( $user_id, 'R' ),
			$this->redsys_token( $user_id, 'C' ),
		);

		$this->assertTrue( WCRedL()->check_type_exist_in_tokens( $tokens, 'C' ) );
		$this->assertFalse( WCRedL()->check_type_exist_in_tokens( $tokens, 'X' ) );
	}

	/**
	 * Found by the review of S-092: an expired token of the requested type
	 * also ended the search, so a valid one after it was never seen.
	 */
	public function test_check_type_exist_in_tokens_looks_past_an_expired_token_of_the_type() {
		$user_id = self::factory()->user->create();
		$tokens  = array(
			$this->redsys_token( $user_id, 'C', (int) gmdate( 'Y' ) - 1 ),
			$this->redsys_token( $user_id, 'C' ),
		);

		$this->assertTrue( WCRedL()->check_type_exist_in_tokens( $tokens, 'C' ) );
	}

	/**
	 * @param int $user_id Customer, 0 for a guest.
	 * @return WC_Order
	 */
	private function order_with_shipping_address( $user_id ) {
		$order = wc_create_order( array( 'customer_id' => $user_id ) );
		$order->set_billing_email( 'buyer@example.test' );
		$order->set_billing_city( 'Valencia' );
		$order->set_billing_address_1( 'Calle Falsa 1' );
		$order->set_billing_postcode( '46001' );
		$order->set_billing_country( 'ES' );
		$order->set_shipping_city( 'Valencia' );
		$order->set_shipping_address_1( 'Calle Falsa 1' );
		$order->set_shipping_postcode( '46001' );
		$order->set_shipping_country( 'ES' );
		$order->set_total( 10 );
		$order->save();
		return $order;
	}

	/**
	 * A saved, unexpired Redsys card token of the given Redsys type.
	 *
	 * @param int      $user_id Owner.
	 * @param string   $type    Redsys token type.
	 * @param int|null $year    Expiry year; null is two years from now.
	 * @return WC_Payment_Token_CC
	 */
	private function redsys_token( $user_id, $type, $year = null ) {
		$token = new WC_Payment_Token_CC();
		$token->set_token( 'test-token-' . $type . '-' . wp_generate_password( 8, false ) );
		$token->set_gateway_id( 'redsys' );
		$token->set_card_type( 'visa' );
		$token->set_last4( '0004' );
		$token->set_expiry_month( '12' );
		$token->set_expiry_year( (string) ( null === $year ? (int) gmdate( 'Y' ) + 2 : $year ) );
		$token->set_user_id( $user_id );
		$token->save();
		update_option( 'token_type_' . $token->get_token(), $type );
		$this->tokens[] = $token->get_id();
		return $token;
	}
}
