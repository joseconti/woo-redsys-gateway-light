<?php
/**
 * Integration tests for AC-15 and AC-16 (D-045, slice S-060): the test-mode
 * warning of the card gateway at the checkout, and the gateway switching
 * itself off when the store currency is not one Redsys lists.
 *
 * As-built: the tests describe the code as it is and change none of it.
 * Each case sits beside the one that takes the other branch — test mode on
 * and off, an allowed currency and one that is not — so a harness that
 * could not tell them apart would fail one side.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class CardGatewayAsBuiltTest extends WP_UnitTestCase {

	const WARNING = 'Warning: WooCommerce Redsys Gateway Light is in test mode. Remember to uncheck it when you go live';

	/**
	 * The currency the store reports, when a test sets one.
	 *
	 * @var string
	 */
	private $currency = '';

	public function set_up() {
		parent::set_up();
		$this->currency = '';
		add_filter( 'woocommerce_currency', array( $this, 'store_currency' ), 99 );
	}

	public function tear_down() {
		remove_filter( 'woocommerce_currency', array( $this, 'store_currency' ), 99 );
		delete_option( 'woocommerce_redsys_settings' );
		parent::tear_down();
	}

	/**
	 * Filter: the store currency of the running test.
	 *
	 * @param string $currency Currency WooCommerce was going to report.
	 * @return string
	 */
	public function store_currency( $currency ) {
		return '' === $this->currency ? $currency : $this->currency;
	}

	/**
	 * A card gateway built from stored settings, the way WooCommerce builds it.
	 *
	 * @param string $enabled  Stored `enabled`.
	 * @param string $testmode Stored `testmode`.
	 * @return WC_Gateway_redsys
	 */
	private function gateway( $enabled, $testmode ) {
		update_option(
			'woocommerce_redsys_settings',
			array(
				'enabled'  => $enabled,
				'testmode' => $testmode,
			)
		);
		return new WC_Gateway_redsys();
	}

	/**
	 * What the gateway prints above the checkout form.
	 *
	 * @param WC_Gateway_redsys $gateway Gateway.
	 * @return string
	 */
	private function banner_of( $gateway ) {
		ob_start();
		$gateway->warning_checkout_test_mode();
		return (string) ob_get_clean();
	}

	public function test_the_warning_is_printed_above_the_checkout_form_in_test_mode() {
		$gateway = $this->gateway( 'yes', 'yes' );

		$this->assertNotFalse(
			has_action( 'woocommerce_before_checkout_form', array( $gateway, 'warning_checkout_test_mode' ) ),
			'The gateway must print its warning where the classic checkout form starts.'
		);
		$this->assertStringContainsString( self::WARNING, $this->banner_of( $gateway ) );
	}

	/**
	 * @dataProvider data_no_warning
	 * @param string $enabled  Stored `enabled`.
	 * @param string $testmode Stored `testmode`.
	 */
	public function test_no_warning_outside_test_mode_or_when_the_gateway_is_off( $enabled, $testmode ) {
		$this->assertSame( '', $this->banner_of( $this->gateway( $enabled, $testmode ) ) );
	}

	/**
	 * @return array[]
	 */
	public function data_no_warning() {
		return array(
			'live mode, gateway on'  => array( 'yes', 'no' ),
			'test mode, gateway off' => array( 'no', 'yes' ),
		);
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
	 * @dataProvider data_redsys_protocol_gateways
	 * @param string $class      Gateway class name.
	 * @param string $gateway_id Gateway id.
	 */
	public function test_a_gateway_switches_itself_off_for_a_currency_redsys_does_not_list( $class, $gateway_id ) {
		update_option( 'woocommerce_' . $gateway_id . '_settings', array( 'enabled' => 'yes' ) );
		$this->currency = 'BTC';
		$this->assertNotContains( 'BTC', WCRedL()->allowed_currencies() );

		$gateway = new $class();
		delete_option( 'woocommerce_' . $gateway_id . '_settings' );

		$this->assertFalse( $gateway->is_valid_for_use() );
		$this->assertFalse( $gateway->enabled, 'Stored as enabled, the gateway must still be off for this currency.' );
	}

	/**
	 * @dataProvider data_redsys_protocol_gateways
	 * @param string $class      Gateway class name.
	 * @param string $gateway_id Gateway id.
	 */
	public function test_the_same_gateway_stays_on_for_a_listed_currency( $class, $gateway_id ) {
		update_option( 'woocommerce_' . $gateway_id . '_settings', array( 'enabled' => 'yes' ) );
		$this->currency = 'EUR';

		$gateway = new $class();
		delete_option( 'woocommerce_' . $gateway_id . '_settings' );

		$this->assertTrue( $gateway->is_valid_for_use() );
		$this->assertSame( 'yes', $gateway->enabled );
	}

	public function test_the_card_gateway_is_not_offered_and_its_settings_screen_says_why() {
		$this->currency = 'BTC';
		$gateway        = $this->gateway( 'yes', 'yes' );

		$this->assertFalse( $gateway->is_available() );

		ob_start();
		$gateway->admin_options();
		$screen = (string) ob_get_clean();

		$this->assertStringContainsString( 'Gateway Disabled', $screen );
		$this->assertStringNotContainsString( 'woocommerce_redsys_enabled', $screen, 'No settings field is drawn while the gateway is switched off by the currency.' );
	}

	public function test_the_card_gateway_is_offered_and_shows_its_settings_for_a_listed_currency() {
		$this->currency = 'EUR';
		$gateway        = $this->gateway( 'yes', 'yes' );

		$this->assertTrue( $gateway->is_available() );

		ob_start();
		$gateway->admin_options();
		$screen = (string) ob_get_clean();

		$this->assertStringNotContainsString( 'Gateway Disabled', $screen );
		$this->assertStringContainsString( 'woocommerce_redsys_enabled', $screen );
	}
}
