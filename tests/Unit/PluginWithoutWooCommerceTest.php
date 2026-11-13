<?php
/**
 * The plugin must not take a site down when WooCommerce is not there (S-051).
 *
 * WordPress enforces the plugin's "Requires Plugins" header from version
 * 6.5 and only in the admin screens: WooCommerce can still go missing under
 * an active copy of this plugin (removed over FTP, deactivated with WP-CLI,
 * a failed update, an older WordPress). The main file hooks the front end at
 * global scope, so whatever it hooks there has to stand on its own.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class PluginWithoutWooCommerceTest extends PHPUnit\Framework\TestCase {

	public function test_the_front_end_does_not_fatal_when_woocommerce_is_absent() {
		$script = __DIR__ . '/fixtures/load-plugin-without-woocommerce.php';
		$output = array();
		$status = null;

		exec( escapeshellarg( PHP_BINARY ) . ' -d display_errors=1 -d error_reporting=E_ALL ' . escapeshellarg( $script ) . ' 2>&1', $output, $status ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.system_calls_exec

		$output = implode( "\n", $output );

		$this->assertSame( 0, $status, 'Loading the plugin without WooCommerce and printing a page head ended in an error: ' . $output );
		$this->assertStringEndsWith( 'FRONT END OK', $output );
		$this->assertStringNotContainsString( 'Fatal error', $output );
	}
}
