<?php
/**
 * A PHP file of the plugin requested directly, outside WordPress, stops at
 * once and prints nothing: no PHP error naming a path, no code run.
 * Slice S-074; the project's rule is a guard in every PHP file.
 *
 * @package WooCommerce Redsys Gateway Light
 */

use PHPUnit\Framework\TestCase;

class DirectAccessTest extends TestCase {

	/**
	 * @return array<string, array{0: string}>
	 */
	public function plugin_files() {
		return array(
			'the main file'      => array( 'woocommerce-redsys.php' ),
			'the Redsys API'     => array( 'includes/class-redsysliteapi.php' ),
			'a gateway, guarded'   => array( 'classes/class-wc-gateway-redsys.php' ),
		);
	}

	/**
	 * @dataProvider plugin_files
	 * @param string $file Path from the plugin root.
	 */
	public function test_a_file_requested_directly_prints_nothing( $file ) {
		$path    = dirname( __DIR__, 2 ) . '/' . $file;
		$command = escapeshellarg( PHP_BINARY ) . ' -d display_errors=1 -d error_reporting=-1 -r ' . escapeshellarg( 'include ' . var_export( $path, true ) . '; echo class_exists( "RedsysLiteAPI", false ) ? "DEFINED" : "";' ) . ' 2>&1';
		$output  = shell_exec( $command ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.system_calls_shell_exec

		$this->assertSame( '', (string) $output, $file . ' ran outside WordPress.' );
	}
}
