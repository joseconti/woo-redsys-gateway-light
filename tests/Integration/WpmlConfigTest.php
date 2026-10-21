<?php
/**
 * Reproduction test for S-100: wpml-config.xml registered the settings of a
 * gateway this plugin does not contain (iupay) and only the card gateway's
 * title and description, so WPML offered the other three gateways' texts for
 * no translation. Written and seen failing against the file as it was before
 * the fix.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class WpmlConfigTest extends WP_UnitTestCase {

	public function test_wpml_config_registers_exactly_the_plugins_gateways() {
		$xml  = simplexml_load_file( dirname( __DIR__, 2 ) . '/wpml-config.xml' );
		$keys = array();
		foreach ( $xml->{'admin-texts'}->key as $key ) {
			$fields = array();
			foreach ( $key->key as $field ) {
				$fields[] = (string) $field['name'];
			}
			$keys[ (string) $key['name'] ] = $fields;
		}

		$expected = array();
		foreach ( array( 'redsys', 'bizumredsys', 'googlepayredirecredsys', 'inespayredsys' ) as $id ) {
			$this->assertArrayHasKey( $id, WC()->payment_gateways()->payment_gateways(), "$id is a gateway of this plugin" );
			$expected[ 'woocommerce_' . $id . '_settings' ] = array( 'title', 'description' );
		}
		$this->assertSame( $expected, $keys );
	}
}
