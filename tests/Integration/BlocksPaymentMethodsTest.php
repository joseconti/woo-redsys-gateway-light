<?php
/**
 * Integration tests for AC-46 (D-045, slice S-060): what Bizum, Google Pay
 * redirection and Inespay hand the Blocks checkout, and when each is active
 * there. The browser half — the three options as a customer sees them — is
 * tests/e2e/checkout-blocks-other-gateways.spec.js.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class BlocksPaymentMethodsTest extends WP_UnitTestCase {

	/**
	 * Option names written by a test.
	 *
	 * @var string[]
	 */
	private $options = array();

	public function set_up() {
		parent::set_up();
		if ( null === WC()->cart ) {
			wc_load_cart();
		}
	}

	public function tear_down() {
		foreach ( $this->options as $option ) {
			delete_option( $option );
		}
		WC()->customer->set_billing_country( '' );
		WC()->customer->set_shipping_country( '' );
		parent::tear_down();
	}

	/**
	 * Store a gateway's settings.
	 *
	 * @param string $gateway_id Gateway ID.
	 * @param array  $settings   Settings.
	 * @return void
	 */
	private function configure( $gateway_id, $settings ) {
		$option          = 'woocommerce_' . $gateway_id . '_settings';
		$this->options[] = $option;
		update_option( $option, $settings );
	}

	/**
	 * The three gateways of AC-46 with their Blocks support class and default icon.
	 *
	 * @return array
	 */
	public function data_gateways() {
		return array(
			'Bizum'      => array( 'bizumredsys', 'WC_Gateway_Bizum_Lite_Support' ),
			'Google Pay' => array( 'googlepayredirecredsys', 'WC_Gateway_GooglePay_Redirection_Redsys_Support' ),
			'Inespay'    => array( 'inespayredsys', 'WC_Gateway_Inespay_Lite_Support' ),
		);
	}

	/**
	 * @dataProvider data_gateways
	 *
	 * @param string $gateway_id    Gateway ID.
	 * @param string $support_class Blocks support class name.
	 */
	public function test_the_blocks_checkout_is_given_the_gateway_title_description_and_icon( $gateway_id, $support_class ) {
		$this->configure(
			$gateway_id,
			array(
				'enabled'     => 'yes',
				'title'       => 'Title of ' . $gateway_id,
				'description' => 'Description of ' . $gateway_id,
			)
		);

		$support = new $support_class();
		$support->initialize();
		$data = $support->get_payment_method_data();

		$this->assertSame( $gateway_id, $support->get_name(), 'The name the Blocks script registers the method under.' );
		$this->assertSame( 'Title of ' . $gateway_id, $data['title'] );
		$this->assertSame( 'Description of ' . $gateway_id, $data['description'] );
		$this->assertStringStartsWith( REDSYS_PLUGIN_URL . 'assets/images/', $data['icon'], 'Without a Logo setting the icon is the plugin\'s own image.' );
		$icon_file = REDSYS_PLUGIN_PATH . 'assets/images/' . basename( $data['icon'] );
		$this->assertFileExists( $icon_file );
	}

	/**
	 * @dataProvider data_gateways
	 *
	 * @param string $gateway_id    Gateway ID.
	 * @param string $support_class Blocks support class name.
	 */
	public function test_a_gateway_is_active_in_the_blocks_checkout_only_when_enabled( $gateway_id, $support_class ) {
		// An allowed country, so that for Inespay only the setting differs.
		WC()->customer->set_shipping_country( 'ES' );

		$this->configure( $gateway_id, array( 'enabled' => 'yes' ) );
		$support = new $support_class();
		$support->initialize();
		$this->assertTrue( $support->is_active() );

		$this->configure( $gateway_id, array( 'enabled' => 'no' ) );
		$support = new $support_class();
		$support->initialize();
		$this->assertFalse( $support->is_active() );
	}

	/**
	 * @dataProvider data_countries
	 *
	 * @param string $shipping Customer's shipping country.
	 * @param string $billing  Customer's billing country.
	 * @param string $base     Store base country.
	 * @param bool   $active   Whether Inespay is active in the Blocks checkout.
	 */
	public function test_inespay_is_active_in_the_blocks_checkout_only_for_es_pt_and_it( $shipping, $billing, $base, $active ) {
		$this->configure( 'inespayredsys', array( 'enabled' => 'yes' ) );
		update_option( 'woocommerce_default_country', $base );
		WC()->customer->set_shipping_country( $shipping );
		WC()->customer->set_billing_country( $billing );

		$support = new WC_Gateway_Inespay_Lite_Support();
		$support->initialize();

		$this->assertSame( $active, $support->is_active() );
	}

	public function data_countries() {
		return array(
			'shipping ES'                              => array( 'ES', '', 'US', true ),
			'shipping PT'                              => array( 'PT', '', 'US', true ),
			'shipping IT'                              => array( 'IT', '', 'US', true ),
			'shipping FR'                              => array( 'FR', 'ES', 'ES', false ),
			'no shipping country: billing decides'     => array( '', 'DE', 'ES', false ),
			'neither known: the store base decides ES' => array( '', '', 'ES', true ),
			'neither known: the store base decides US' => array( '', '', 'US', false ),
		);
	}
}
