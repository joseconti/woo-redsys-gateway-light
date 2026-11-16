<?php
/**
 * The order-pay page of the card, Bizum and Google Pay gateways submits the
 * payment form to Redsys on its own. With a block theme the page content is
 * rendered before wp_enqueue_scripts, so WooCommerce's own script handle does
 * not exist yet when receipt_page() runs: an inline script attached to it is
 * silently dropped and the customer is left on the page with a button to
 * click. Reproduction of the 7.1.0 defect, slice S-107 (D-116).
 *
 * Runs only on a playground site (scripts/keel-site test integration) — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class RedirectAutoSubmitTest extends WP_UnitTestCase {

	/**
	 * The scripts registry of the test run, restored after each test.
	 *
	 * @var WP_Scripts|null
	 */
	private $saved_scripts;

	public function set_up() {
		parent::set_up();
		$this->saved_scripts   = isset( $GLOBALS['wp_scripts'] ) ? $GLOBALS['wp_scripts'] : null;
		$GLOBALS['wp_scripts'] = new WP_Scripts();
	}

	public function tear_down() {
		$GLOBALS['wp_scripts'] = $this->saved_scripts;
		parent::tear_down();
	}

	/**
	 * Gateway class and whether the merchant key is set (the card gateway has
	 * two branches of the form, with and without it).
	 *
	 * @return array<string, array{0: string, 1: bool}>
	 */
	public function gateways() {
		return array(
			'card'               => array( 'WC_Gateway_redsys', true ),
			'card, no key branch' => array( 'WC_Gateway_redsys', false ),
			'Bizum'              => array( 'WC_Gateway_Bizum_Redsys', true ),
			'Google Pay'         => array( 'WC_Gateway_GooglePay_Redirection_Redsys', true ),
		);
	}

	/**
	 * Renders the receipt page with the scripts registry in the state a theme
	 * leaves it in, then lets WooCommerce enqueue its scripts and prints every
	 * queued script, as the page would.
	 *
	 * @param string $class            Gateway class name.
	 * @param bool   $with_key         Whether the merchant key is set.
	 * @param bool   $wc_registered_first Whether WooCommerce's handle exists before the page renders (classic theme) or not (block theme).
	 * @return string Every script printed for the page.
	 */
	private function printed_scripts( $class, $with_key, $wc_registered_first ) {
		$register_wc = static function () {
			wp_register_script( 'woocommerce', 'https://example.invalid/woocommerce.js', array( 'jquery' ), '1', array( 'strategy' => 'defer' ) );
			wp_enqueue_script( 'woocommerce' );
		};
		if ( $wc_registered_first ) {
			$register_wc();
		}

		$order = wc_create_order();
		$order->set_total( 12.34 );
		$order->save();
		$gateway               = new $class();
		$gateway->secretsha256 = $with_key ? base64_encode( str_repeat( 'S', 24 ) ) : ''; // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$gateway->testmode     = 'no';
		$gateway->debug        = 'no';

		ob_start();
		$gateway->receipt_page( $order->get_id() );
		$page = ob_get_clean();
		$this->assertStringContainsString( 'id="redsys_payment_form"', $page, 'The form is printed.' );

		if ( ! $wc_registered_first ) {
			$register_wc();
		}

		ob_start();
		wp_scripts()->do_items();
		return ob_get_clean();
	}

	/**
	 * @dataProvider gateways
	 * @param string $class    Gateway class name.
	 * @param bool   $with_key Whether the merchant key is set.
	 */
	public function test_block_theme_the_form_is_submitted_without_a_click( $class, $with_key ) {
		$scripts = $this->printed_scripts( $class, $with_key, false );
		$this->assertMatchesRegularExpression( '#<script[^>]*>(?:(?!</script>).)*redsys_payment_form(?:(?!</script>).)*</script>#s', $scripts, 'A printed script submits the payment form.' );
	}

	/**
	 * @dataProvider gateways
	 * @param string $class    Gateway class name.
	 * @param bool   $with_key Whether the merchant key is set.
	 */
	public function test_classic_theme_the_form_is_submitted_without_a_click( $class, $with_key ) {
		$scripts = $this->printed_scripts( $class, $with_key, true );
		$this->assertMatchesRegularExpression( '#<script[^>]*>(?:(?!</script>).)*redsys_payment_form(?:(?!</script>).)*</script>#s', $scripts, 'A printed script submits the payment form.' );
	}
}
