<?php
/**
 * PHPUnit bootstrap for integration tests (a real WordPress + WooCommerce).
 *
 * Unlike tests/bootstrap.php (true unit tests, no WordPress), this suite
 * exercises code that genuinely depends on WordPress/WooCommerce being
 * loaded — e.g. WC_Gateway_redsys, which extends WC_Payment_Gateway. It
 * runs only inside the wp-env `tests-cli` container, which already provides
 * the WordPress core PHPUnit test scaffold at WP_TESTS_DIR (see
 * docs/playground.md).
 *
 * @package WooCommerce Redsys Gateway Light
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

if ( ! file_exists( "{$_tests_dir}/includes/functions.php" ) ) {
	fwrite( STDERR, "Could not find {$_tests_dir}/includes/functions.php — this suite must run inside wp-env's tests-cli container (WP_TESTS_DIR is set there). See docs/03-technical-plan.md.\n" );
	exit( 1 );
}

require_once "{$_tests_dir}/includes/functions.php";

require_once dirname( __DIR__ ) . '/vendor/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php';

/**
 * Loads WooCommerce, then this plugin, at the same point WordPress itself
 * loads must-use plugins — mirroring how a real site activates them.
 */
function _woo_redsys_gateway_light_manually_load_plugin() {
	require WP_PLUGIN_DIR . '/woocommerce/woocommerce.php';
	require dirname( __DIR__ ) . '/woocommerce-redsys.php';
}
tests_add_filter( 'muplugins_loaded', '_woo_redsys_gateway_light_manually_load_plugin' );

require "{$_tests_dir}/includes/bootstrap.php";
