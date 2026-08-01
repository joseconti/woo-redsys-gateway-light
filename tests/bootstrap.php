<?php
/**
 * PHPUnit bootstrap for unit tests.
 *
 * Scope decision (see docs/decisions.md): RedsysLiteAPI has no WordPress
 * runtime dependency beyond wp_json_encode(), so these are true unit tests
 * against the class in isolation, not WP_UnitTestCase integration tests
 * against a booted WordPress. wp_json_encode() is stubbed here rather than
 * booting WordPress core, which the class does not otherwise need.
 *
 * @package WooCommerce Redsys Gateway Light
 */

if ( ! function_exists( 'wp_json_encode' ) ) {
	/**
	 * Minimal stand-in for WordPress's wp_json_encode().
	 *
	 * @param mixed $data    Data to encode.
	 * @param int   $options json_encode() options.
	 * @param int   $depth   Maximum depth.
	 * @return string|false
	 */
	function wp_json_encode( $data, $options = 0, $depth = 512 ) {
		return json_encode( $data, $options, $depth ); // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
	}
}

require_once __DIR__ . '/../includes/class-redsysliteapi.php';
