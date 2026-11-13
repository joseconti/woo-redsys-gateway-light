<?php
/**
 * Integration tests: the "Logo" setting of the card, Bizum and Inespay
 * gateways is a URL and is treated as one (S-045).
 *
 * The setting is a free text field whose value becomes the gateway icon.
 * WooCommerce prints the icon inside an <img src="…"> on the classic
 * checkout without escaping it, so the value has to be a clean URL both
 * when it is saved and when it is used: a value saved before this rule
 * existed is still in the database.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class GatewayLogoTest extends WP_UnitTestCase {

	/**
	 * A value with no tags, so WooCommerce's default text-field filter
	 * leaves it alone, that closes the src attribute and adds another one.
	 *
	 * @var string
	 */
	const BREAKOUT = 'https://example.com/logo.png" onerror="window.redsysLogoBreakout=1';

	/**
	 * Option names written by a test.
	 *
	 * @var string[]
	 */
	private $options = array();

	public function tear_down() {
		foreach ( $this->options as $option ) {
			delete_option( $option );
		}
		parent::tear_down();
	}

	/**
	 * Gateways that have the Logo setting, with their Blocks support class.
	 *
	 * @return array[]
	 */
	public function gateways_with_a_logo_setting() {
		return array(
			'card'    => array( 'WC_Gateway_redsys', 'redsys', 'WC_Gateway_Redsys_Lite_Support' ),
			'Bizum'   => array( 'WC_Gateway_Bizum_Redsys', 'bizumredsys', 'WC_Gateway_Bizum_Lite_Support' ),
			'Inespay' => array( 'WC_Gateway_Inespay_Redsys', 'inespayredsys', 'WC_Gateway_Inespay_Lite_Support' ),
		);
	}

	/**
	 * Stores a Logo value the way an earlier version would have left it.
	 *
	 * @param string $gateway_id Gateway id.
	 * @param string $logo       Stored value.
	 */
	private function store_logo( $gateway_id, $logo ) {
		$option   = 'woocommerce_' . $gateway_id . '_settings';
		$settings = get_option( $option, array() );
		$settings = is_array( $settings ) ? $settings : array();

		$settings['logo'] = $logo;
		update_option( $option, $settings );
		$this->options[] = $option;
	}

	/**
	 * Attributes of the one <img> in a piece of markup.
	 *
	 * @param string $html Markup returned by WC_Payment_Gateway::get_icon().
	 * @return string[] Attribute name => value.
	 */
	private function img_attributes( $html ) {
		$document = new DOMDocument();
		@$document->loadHTML( '<html><body>' . $html . '</body></html>' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- libxml warns on HTML5 fragments.

		$images = $document->getElementsByTagName( 'img' );
		$this->assertSame( 1, $images->length, 'The icon markup is expected to be exactly one image: ' . $html );

		$attributes = array();
		foreach ( $images->item( 0 )->attributes as $attribute ) {
			$attributes[ strtolower( $attribute->name ) ] = $attribute->value;
		}
		return $attributes;
	}

	/**
	 * @dataProvider gateways_with_a_logo_setting
	 * @param string $class      Gateway class name.
	 * @param string $gateway_id Gateway id.
	 */
	public function test_saving_the_logo_setting_keeps_only_a_url( $class, $gateway_id ) {
		$gateway = new $class();
		$fields  = $gateway->get_form_fields();
		$key     = $gateway->get_field_key( 'logo' );

		$saved = $gateway->get_field_value( 'logo', $fields['logo'], array( $key => self::BREAKOUT ) );

		$this->assertStringNotContainsString( '"', $saved, 'A quote in the stored value closes the src attribute it is printed in.' );
		$this->assertStringNotContainsString( ' ', $saved );
		$this->assertSame( esc_url_raw( $saved ), $saved, 'The stored value must already be a clean URL.' );
	}

	/**
	 * @dataProvider gateways_with_a_logo_setting
	 * @param string $class      Gateway class name.
	 * @param string $gateway_id Gateway id.
	 */
	public function test_saving_the_logo_setting_keeps_an_ordinary_url_as_it_is( $class, $gateway_id ) {
		$gateway = new $class();
		$fields  = $gateway->get_form_fields();
		$key     = $gateway->get_field_key( 'logo' );
		$url     = 'https://example.com/wp-content/uploads/2026/10/logo.png?ver=2&size=small';

		$this->assertSame( $url, $gateway->get_field_value( 'logo', $fields['logo'], array( $key => $url ) ) );
		$this->assertSame( '', $gateway->get_field_value( 'logo', $fields['logo'], array( $key => '' ) ), 'An empty value means the default icon.' );
	}

	/**
	 * A value already in the database, saved before the setting was
	 * validated, must not break out of the image either.
	 *
	 * @dataProvider gateways_with_a_logo_setting
	 * @param string $class      Gateway class name.
	 * @param string $gateway_id Gateway id.
	 */
	public function test_a_stored_logo_value_cannot_add_attributes_to_the_checkout_icon( $class, $gateway_id ) {
		$this->store_logo( $gateway_id, self::BREAKOUT );

		$gateway    = new $class();
		$attributes = $this->img_attributes( $gateway->get_icon() );

		foreach ( array_keys( $attributes ) as $name ) {
			$this->assertStringStartsNotWith( 'on', $name, 'The Logo setting added an event-handler attribute to the checkout icon.' );
		}
		$this->assertArrayHasKey( 'src', $attributes );
		$this->assertStringStartsWith( 'https://example.com/logo.png', $attributes['src'] );
	}

	/**
	 * @dataProvider gateways_with_a_logo_setting
	 * @param string $class      Gateway class name.
	 * @param string $gateway_id Gateway id.
	 */
	public function test_a_stored_ordinary_logo_url_is_the_icon( $class, $gateway_id ) {
		$url = 'https://example.com/logo.png?ver=2&size=small';
		$this->store_logo( $gateway_id, $url );

		$gateway    = new $class();
		$attributes = $this->img_attributes( $gateway->get_icon() );

		$this->assertSame( $url, $attributes['src'], 'The browser must receive the URL the merchant entered.' );
	}

	/**
	 * The Blocks checkout receives the icon as data, not as markup.
	 *
	 * @dataProvider gateways_with_a_logo_setting
	 * @param string $class         Gateway class name.
	 * @param string $gateway_id    Gateway id.
	 * @param string $support_class Blocks support class name.
	 */
	public function test_the_blocks_checkout_receives_a_clean_icon_url( $class, $gateway_id, $support_class ) {
		$this->store_logo( $gateway_id, self::BREAKOUT );

		$data = ( new $support_class() )->get_payment_method_data();

		$this->assertStringNotContainsString( '"', $data['icon'] );
		$this->assertSame( esc_url_raw( $data['icon'] ), $data['icon'] );

		$url = 'https://example.com/logo.png?ver=2&size=small';
		$this->store_logo( $gateway_id, $url );
		$data = ( new $support_class() )->get_payment_method_data();
		$this->assertSame( $url, $data['icon'], 'An ordinary URL reaches the script unchanged, with a literal ampersand.' );
	}

	/**
	 * The four icon filters, with the gateway and Blocks classes they feed.
	 *
	 * @return array[]
	 */
	public function icon_filters() {
		return array(
			'card'       => array( 'woocommerce_redsys_icon', 'WC_Gateway_redsys', 'WC_Gateway_Redsys_Lite_Support' ),
			'Bizum'      => array( 'woocommerce_bizumredsys_icon', 'WC_Gateway_Bizum_Redsys', 'WC_Gateway_Bizum_Lite_Support' ),
			'Google Pay' => array( 'woocommerce_googlepayredirecredsys_icon', 'WC_Gateway_GooglePay_Redirection_Redsys', 'WC_Gateway_GooglePay_Redirection_Redsys_Support' ),
			'Inespay'    => array( 'woocommerce_inespayredsys_icon', 'WC_Gateway_Inespay_Redsys', 'WC_Gateway_Inespay_Lite_Support' ),
		);
	}

	/**
	 * A third party's filter decides the icon; what it returns is still
	 * printed inside an attribute, on both checkouts, for all four gateways.
	 *
	 * @dataProvider icon_filters
	 * @param string $filter        Filter name.
	 * @param string $class         Gateway class name.
	 * @param string $support_class Blocks support class name.
	 */
	public function test_a_filtered_icon_is_escaped_too( $filter, $class, $support_class ) {
		add_filter(
			$filter,
			static function () {
				return GatewayLogoTest::BREAKOUT;
			}
		);

		$attributes = $this->img_attributes( ( new $class() )->get_icon() );

		foreach ( array_keys( $attributes ) as $name ) {
			$this->assertStringStartsNotWith( 'on', $name );
		}
		$this->assertStringStartsWith( 'https://example.com/logo.png', $attributes['src'] );

		$data = ( new $support_class() )->get_payment_method_data();
		$this->assertStringNotContainsString( '"', $data['icon'] );
	}

	/**
	 * What the setting accepts and what it turns away, on save.
	 *
	 * @dataProvider logo_values
	 * @param string $entered  Value typed in the field.
	 * @param string $expected Value stored.
	 */
	public function test_what_the_logo_setting_stores( $entered, $expected ) {
		$gateway = new WC_Gateway_redsys();
		$fields  = $gateway->get_form_fields();

		$this->assertSame( $expected, $gateway->get_field_value( 'logo', $fields['logo'], array( $gateway->get_field_key( 'logo' ) => $entered ) ) );
	}

	/**
	 * @return array[]
	 */
	public function logo_values() {
		return array(
			'script scheme is refused'      => array( 'javascript:alert(1)', '' ),
			'data URI is refused'           => array( 'data:image/svg+xml;base64,PHN2Zz48L3N2Zz4=', '' ),
			'path from the site root'       => array( '/wp-content/uploads/logo.png', '/wp-content/uploads/logo.png' ),
			'protocol-relative URL'         => array( '//cdn.example.com/logo.png', '//cdn.example.com/logo.png' ),
			'surrounding spaces are removed' => array( '  https://example.com/logo.png  ', 'https://example.com/logo.png' ),
			// A path with no leading slash was never reliable (it resolved against the page URL); it is read as a host name now.
			'path without a leading slash'  => array( 'wp-content/uploads/logo.png', 'http://wp-content/uploads/logo.png' ),
		);
	}
}
