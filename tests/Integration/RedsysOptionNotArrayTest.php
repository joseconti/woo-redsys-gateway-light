<?php
/**
 * Reproduction test for S-099: get_redsys_option() of the global and PSD2
 * helpers passed whatever the gateway's settings option held to
 * array_key_exists(). A settings option that is not an array (a corrupted
 * or hand-edited row) made PHP 7 warn and PHP 8 throw a TypeError. Written
 * and seen failing against the code as it was before the fix.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class RedsysOptionNotArrayTest extends WP_UnitTestCase {

	/**
	 * @return array[]
	 */
	public function data_helpers() {
		return array(
			'global helper' => array( 'WCRedL' ),
			'PSD2 helper'   => array( 'WCPSD2L' ),
		);
	}

	/**
	 * @dataProvider data_helpers
	 * @param string $helper Function returning the helper instance.
	 */
	public function test_a_settings_option_that_is_not_an_array_reads_as_absent( $helper ) {
		update_option( 'woocommerce_redsys_settings', 'not an array' );

		$this->assertFalse( $helper()->get_redsys_option( 'testmode', 'redsys' ) );
	}

	/**
	 * @dataProvider data_helpers
	 * @param string $helper Function returning the helper instance.
	 */
	public function test_a_settings_array_is_still_read( $helper ) {
		update_option( 'woocommerce_redsys_settings', array( 'testmode' => 'yes' ) );

		$this->assertSame( 'yes', $helper()->get_redsys_option( 'testmode', 'redsys' ) );
	}
}
