<?php
/**
 * Integration tests for AC-47 (D-045, slice S-060): the fields each gateway
 * draws on its settings screen, their defaults, and the option they are
 * saved in.
 *
 * The expected keys, types and defaults below are the tables of
 * docs/usage/configuration.md, written out per gateway: a field added,
 * removed or given another default in the code fails here until the
 * document says the same.
 *
 * As-built: the tests describe the code as it is and change none of it.
 * The real screen (WooCommerce → Settings → Payments) is driven in
 * tests/e2e/as-built-screens.spec.js.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class GatewaySettingsAsBuiltTest extends WP_UnitTestCase {

	public function tear_down() {
		$_POST = array();
		foreach ( $this->data_gateways() as $gateway ) {
			delete_option( 'woocommerce_' . $gateway[1] . '_settings' );
		}
		parent::tear_down();
	}

	/**
	 * Class, id and documented fields (key => type, default; null is "empty").
	 *
	 * @return array[]
	 */
	public function data_gateways() {
		return array(
			'card'       => array(
				'WC_Gateway_redsys',
				'redsys',
				array(
					'enabled'          => array( 'checkbox', 'no' ),
					'title'            => array( 'text', 'Servired/RedSys' ),
					'description'      => array( 'textarea', 'Pay via Servired/RedSys; you can pay with your credit card.' ),
					'logo'             => array( 'text', null ),
					'customer'         => array( 'text', null ),
					'commercename'     => array( 'text', null ),
					'payoptions'       => array( 'select', 'T' ),
					'terminal'         => array( 'text', null ),
					'not_use_https'    => array( 'checkbox', 'no' ),
					'lwvactive'        => array( 'checkbox', 'no' ),
					'orderdo'          => array( 'select', 'processing' ),
					'secretsha256'     => array( 'text', null ),
					'customtestsha256' => array( 'text', null ),
					'redsyslanguage'   => array( 'select', '001' ),
					'testmode'         => array( 'checkbox', 'yes' ),
					'debug'            => array( 'checkbox', 'no' ),
				),
			),
			'Bizum'      => array(
				'WC_Gateway_Bizum_Redsys',
				'bizumredsys',
				array(
					'enabled'          => array( 'checkbox', 'no' ),
					'title'            => array( 'text', 'Bizum' ),
					'description'      => array( 'textarea', 'Pay via Bizum you can pay with your Bizum account.' ),
					'logo'             => array( 'text', null ),
					'customer'         => array( 'text', null ),
					'commercename'     => array( 'text', null ),
					'terminal'         => array( 'text', null ),
					'orderdo'          => array( 'select', 'processing' ),
					'transactionlimit' => array( 'text', null ),
					'not_use_https'    => array( 'checkbox', 'no' ),
					'secretsha256'     => array( 'text', null ),
					'customtestsha256' => array( 'text', null ),
					'redsyslanguage'   => array( 'select', '001' ),
					'testmode'         => array( 'checkbox', 'yes' ),
					'debug'            => array( 'checkbox', 'no' ),
				),
			),
			'Google Pay' => array(
				'WC_Gateway_GooglePay_Redirection_Redsys',
				'googlepayredirecredsys',
				array(
					'enabled'          => array( 'checkbox', 'no' ),
					'title'            => array( 'text', 'Google Pay' ),
					'description'      => array( 'textarea', 'Pay via GPay you can pay with your Google account.' ),
					'customer'         => array( 'text', null ),
					'commercename'     => array( 'text', null ),
					'terminal'         => array( 'text', null ),
					'not_use_https'    => array( 'checkbox', 'no' ),
					'secretsha256'     => array( 'text', null ),
					'customtestsha256' => array( 'text', null ),
					'redsyslanguage'   => array( 'select', '001' ),
					'testmode'         => array( 'checkbox', 'yes' ),
					'debug'            => array( 'checkbox', 'no' ),
				),
			),
			'Inespay'    => array(
				'WC_Gateway_Inespay_Redsys',
				'inespayredsys',
				array(
					'enabled'          => array( 'checkbox', 'no' ),
					'title'            => array( 'text', 'Inespay Bank Transfer' ),
					'description'      => array( 'textarea', 'Pay securely via your online banking with Inespay.' ),
					'logo'             => array( 'text', null ),
					'api_key'          => array( 'text', null ),
					'api_token'        => array( 'password', null ),
					'creditor_account' => array( 'text', null ),
					'expiration'       => array( 'number', null ),
					'orderdo'          => array( 'select', 'processing' ),
					'transactionlimit' => array( 'text', null ),
					'testmode'         => array( 'checkbox', 'yes' ),
					'debug'            => array( 'checkbox', 'no' ),
				),
			),
		);
	}

	/**
	 * A value a person could type or pick in a field of this type.
	 *
	 * @param string $key  Field key.
	 * @param string $type Field type.
	 * @return string|null Posted value; null leaves the field out, as an unticked checkbox is.
	 */
	private function posted_value( $key, $type ) {
		if ( 'checkbox' === $type ) {
			// Tick the ones that default to "no", untick the ones that default to "yes".
			return in_array( $key, array( 'testmode' ), true ) ? null : '1';
		}
		$values = array(
			'logo'           => 'https://example.org/logo.png',
			'payoptions'     => 'C',
			'orderdo'        => 'completed',
			'redsyslanguage' => '002',
			'expiration'     => '15',
		);
		return isset( $values[ $key ] ) ? $values[ $key ] : 'typed-' . $key;
	}

	/**
	 * What WooCommerce stores for that posted value.
	 *
	 * @param string $key  Field key.
	 * @param string $type Field type.
	 * @return string
	 */
	private function stored_value( $key, $type ) {
		$posted = $this->posted_value( $key, $type );
		if ( 'checkbox' === $type ) {
			return null === $posted ? 'no' : 'yes';
		}
		return $posted;
	}

	/**
	 * @dataProvider data_gateways
	 * @param string $class      Gateway class name.
	 * @param string $gateway_id Gateway id.
	 * @param array  $documented Documented fields.
	 */
	public function test_the_gateway_has_the_documented_fields_types_and_defaults( $class, $gateway_id, $documented ) {
		$gateway = new $class();

		$this->assertSame( $gateway_id, $gateway->id );
		$this->assertSame( 'woocommerce_' . $gateway_id . '_settings', $gateway->get_option_key() );
		$this->assertSame( array_keys( $documented ), array_keys( $gateway->form_fields ), 'Fields, in the order the screen draws them.' );

		foreach ( $documented as $key => $field ) {
			$this->assertSame( $field[0], $gateway->form_fields[ $key ]['type'], $key . ': type' );
			$this->assertSame( $field[1], isset( $gateway->form_fields[ $key ]['default'] ) ? $gateway->form_fields[ $key ]['default'] : null, $key . ': default' );
		}
	}

	/**
	 * @dataProvider data_gateways
	 * @param string $class      Gateway class name.
	 * @param string $gateway_id Gateway id.
	 * @param array  $documented Documented fields.
	 */
	public function test_a_store_that_never_saved_reads_the_defaults( $class, $gateway_id, $documented ) {
		delete_option( 'woocommerce_' . $gateway_id . '_settings' );
		$gateway = new $class();

		foreach ( $documented as $key => $field ) {
			$this->assertSame( (string) $field[1], $gateway->get_option( $key ), $key );
		}
	}

	/**
	 * @dataProvider data_gateways
	 * @param string $class      Gateway class name.
	 * @param string $gateway_id Gateway id.
	 * @param array  $documented Documented fields.
	 */
	public function test_the_settings_screen_draws_every_documented_field( $class, $gateway_id, $documented ) {
		$gateway = new $class();

		ob_start();
		$gateway->admin_options();
		$screen = (string) ob_get_clean();

		foreach ( array_keys( $documented ) as $key ) {
			$this->assertStringContainsString( 'id="woocommerce_' . $gateway_id . '_' . $key . '"', $screen, $key );
		}
		$this->assertSame(
			count( $documented ),
			preg_match_all( '/ id="woocommerce_' . preg_quote( $gateway_id, '/' ) . '_[a-z0-9_]+"/', $screen ),
			'The screen draws a field the document does not list.'
		);
	}

	/**
	 * @dataProvider data_gateways
	 * @param string $class      Gateway class name.
	 * @param string $gateway_id Gateway id.
	 * @param array  $documented Documented fields.
	 */
	public function test_saving_the_screen_stores_every_field_in_the_gateway_option( $class, $gateway_id, $documented ) {
		$option = 'woocommerce_' . $gateway_id . '_settings';
		delete_option( $option );
		$this->assertFalse( get_option( $option ), 'Nothing is stored before the screen is saved.' );

		$gateway  = new $class();
		$expected = array();
		foreach ( $documented as $key => $field ) {
			$posted = $this->posted_value( $key, $field[0] );
			if ( null !== $posted ) {
				$_POST[ 'woocommerce_' . $gateway_id . '_' . $key ] = $posted;
			}
			$expected[ $key ] = $this->stored_value( $key, $field[0] );
		}

		$gateway->process_admin_options();

		$this->assertSame( $expected, get_option( $option ) );
	}
}
