<?php
/**
 * Loads the plugin's main file the way WordPress does when WooCommerce is
 * not there, and runs what the plugin hooked on the front end.
 *
 * Run as its own PHP process by PluginWithoutWooCommerceTest: a fatal error
 * here must not take the test run down with it. WordPress itself is replaced
 * by the few functions the main file calls while it loads; no WooCommerce
 * function or class is defined, which is the point.
 *
 * @package WooCommerce Redsys Gateway Light
 */

define( 'ABSPATH', __DIR__ . '/' );

$GLOBALS['redsyslite_test_hooks'] = array();

function plugin_dir_url( $file ) {
	return 'http://example.test/wp-content/plugins/' . basename( dirname( $file ) ) . '/';
}

function plugin_dir_path( $file ) {
	return dirname( $file ) . '/';
}

function add_action( $hook, $callback ) {
	$GLOBALS['redsyslite_test_hooks'][ $hook ][] = $callback;
	return true;
}

function add_filter( $hook, $callback ) {
	return add_action( $hook, $callback );
}

require dirname( __DIR__, 3 ) . '/woocommerce-redsys.php';

// Every front-end page prints its head.
foreach ( $GLOBALS['redsyslite_test_hooks']['wp_head'] as $callback ) {
	call_user_func( $callback );
}

echo 'FRONT END OK';
