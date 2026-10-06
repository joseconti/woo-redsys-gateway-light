<?php
/**
 * Slice S-027: every shipped stylesheet and script exists as a readable
 * source plus a minified file, and production loads the minified one.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class AssetPairsTest extends WP_UnitTestCase {

	/**
	 * @var string[]
	 */
	private $handles = array();

	/**
	 * @var mixed
	 */
	private $redsys_about;

	public function set_up() {
		parent::set_up();
		$this->redsys_about = $GLOBALS['redsys_about'] ?? null;
	}

	public function tear_down() {
		foreach ( array( 'aboutRedsys', 'redsys-css', 'redsys_notice_css' ) as $handle ) {
			wp_dequeue_style( $handle );
			wp_deregister_style( $handle );
		}
		foreach ( $this->handles as $handle ) {
			wp_deregister_script( $handle );
		}
		$this->handles = array();
		$GLOBALS['redsys_about'] = $this->redsys_about;
		set_current_screen( 'front' );
		parent::tear_down();
	}

	private function plugin_file( $url ) {
		$this->assertStringStartsWith( REDSYS_PLUGIN_URL, $url );
		return REDSYS_PLUGIN_PATH . substr( $url, strlen( REDSYS_PLUGIN_URL ) );
	}

	public function test_the_suffix_is_min_unless_script_debug_is_on() {
		// SCRIPT_DEBUG is a constant, so one run sees one branch. The suite runs as
		// production does; the readable branch is driven in the playground, where
		// .wp-env.json turns SCRIPT_DEBUG on (tests/e2e/checkout-blocks-redsys.spec.js).
		$this->assertFalse( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG );
		$this->assertSame( '.min', redsyslite_asset_suffix() );
	}

	public function test_every_stylesheet_and_script_has_both_files_of_its_pair() {
		$sources = array_merge(
			glob( REDSYS_PLUGIN_PATH . 'assets/css/*.css' ),
			glob( REDSYS_PLUGIN_PATH . 'assets/js/frontend/*.js' )
		);
		$sources = array_filter(
			$sources,
			function ( $file ) {
				return ! preg_match( '/\.min\.(css|js)$/', $file );
			}
		);

		$this->assertNotEmpty( $sources );
		foreach ( $sources as $source ) {
			$minified = preg_replace( '/\.(css|js)$/', '.min.$1', $source );
			$this->assertFileExists( $minified );
			$this->assertLessThan( filesize( $source ), filesize( $minified ), basename( $minified ) . ' is not smaller than its source.' );
		}
		$this->assertFileExists( REDSYS_PLUGIN_PATH . 'assets/js/frontend/blocks.asset.php' );
		$this->assertFileExists( REDSYS_PLUGIN_PATH . 'assets/js/frontend/blocks.min.asset.php' );
	}

	public function test_the_admin_stylesheets_load_their_minified_file() {
		$GLOBALS['redsys_about'] = 'woocommerce_page_redsys-about-page';
		redsys_styles_css( 'woocommerce_page_redsys-about-page' );
		set_current_screen( 'woocommerce_page_wc-settings' );
		redsys_css_lite();
		redsys_lite_notice_style();

		foreach ( array( 'aboutRedsys', 'redsys-css', 'redsys_notice_css' ) as $handle ) {
			$this->assertTrue( wp_style_is( $handle, 'enqueued' ), $handle );
			$src = wp_styles()->registered[ $handle ]->src;
			$this->assertStringEndsWith( '.min.css', $src );
			$this->assertFileExists( $this->plugin_file( $src ) );
		}
	}

	/**
	 * @dataProvider blocks_integrations
	 */
	public function test_the_blocks_checkout_loads_the_minified_script_and_its_own_asset_file( $class ) {
		$integration = new $class();
		$handles     = $integration->get_payment_method_script_handles();
		$this->assertCount( 1, $handles );
		$this->handles[] = $handles[0];

		$script = wp_scripts()->registered[ $handles[0] ];
		$asset  = require REDSYS_PLUGIN_PATH . 'assets/js/frontend/blocks.min.asset.php';

		$this->assertStringEndsWith( '/assets/js/frontend/blocks.min.js', $script->src );
		$this->assertSame( $asset['version'], $script->ver );
		$this->assertSame( $asset['dependencies'], $script->deps );
	}

	public function blocks_integrations() {
		return array(
			'card'       => array( 'WC_Gateway_Redsys_Lite_Support' ),
			'Bizum'      => array( 'WC_Gateway_Bizum_Lite_Support' ),
			'Google Pay' => array( 'WC_Gateway_GooglePay_Redirection_Redsys_Support' ),
			'Inespay'    => array( 'WC_Gateway_Inespay_Lite_Support' ),
		);
	}
}
