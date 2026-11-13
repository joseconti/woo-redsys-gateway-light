<?php
/**
 * Reproduction test for S-065 (D-072): the Spanish translation of the
 * Blocks checkout script was shipped in a JSON file named after the source
 * path (resources/js/frontend/index.js), while WordPress looks for one named
 * after the script it loads (assets/js/frontend/blocks.js). Written and seen
 * failing against the files as they were before the fix.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class BlocksScriptTranslationTest extends WP_UnitTestCase {

	public function tear_down() {
		remove_all_filters( 'determine_locale' );
		parent::tear_down();
	}

	/**
	 * @return array[]
	 */
	public function data_supports() {
		return array(
			'Redsys'     => array( 'WC_Gateway_Redsys_Lite_Support' ),
			'Bizum'      => array( 'WC_Gateway_Bizum_Lite_Support' ),
			'Google Pay' => array( 'WC_Gateway_GooglePay_Redirection_Redsys_Support' ),
			'Inespay'    => array( 'WC_Gateway_Inespay_Lite_Support' ),
		);
	}

	/**
	 * @dataProvider data_supports
	 * @param string $class Blocks integration class.
	 */
	public function test_the_blocks_script_gets_its_spanish_translation( $class ) {
		add_filter(
			'determine_locale',
			function () {
				return 'es_ES';
			}
		);
		$support = new $class();
		$support->initialize();
		$handles = $support->get_payment_method_script_handles();

		$json = load_script_textdomain( $handles[0], 'woo-redsys-gateway-light', plugin_abspath_redsys() . 'languages/' );

		$this->assertIsString( $json, 'WordPress found no translation file for the script it loads.' );
		$translations = json_decode( $json, true );
		$this->assertSame( array( 'Transferencia bancaria Inespay' ), $translations['locale_data']['messages']['Inespay Bank Transfer'] );
	}
}
