<?php
/**
 * The translated strings of the three redirect forms — the button label, the
 * cancel link and the redirect message inside the inline script — are escaped
 * for where they are printed, so a translation file cannot add markup or break
 * out of the script. The Google Pay page passes its form through wp_kses() like
 * its siblings. Slice S-073 (D-078).
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class RedirectFormEscapingTest extends WP_UnitTestCase {

	const HOSTILE = '"><b id="redsyslite-injected">x</b><script>alert(1)</script>';

	public function set_up() {
		parent::set_up();
		if ( ! wp_script_is( 'woocommerce', 'registered' ) ) {
			wp_register_script( 'woocommerce', 'https://example.invalid/woocommerce.js', array(), '1', true );
		}
		wp_scripts()->registered['woocommerce']->extra['after'] = array();
		add_filter( 'gettext', array( $this, 'hostile_translation' ), 10, 3 );
	}

	public function tear_down() {
		remove_filter( 'gettext', array( $this, 'hostile_translation' ), 10 );
		parent::tear_down();
	}

	/**
	 * Every string of the forms is "translated" into markup and a quote.
	 *
	 * @param string $translation Translation.
	 * @param string $text        Original.
	 * @param string $domain      Text domain.
	 * @return string
	 */
	public function hostile_translation( $translation, $text, $domain ) {
		if ( 'woo-redsys-gateway-light' !== $domain ) {
			return $translation;
		}
		if ( 0 === strpos( $text, 'Pay with' ) || 0 === strpos( $text, 'Cancel order' ) || 0 === strpos( $text, 'Thank you for your order. We are now redirecting' ) ) {
			return $text . self::HOSTILE;
		}
		return $translation;
	}

	/**
	 * @return array<string, array{0: string}>
	 */
	public function gateways() {
		return array(
			'card'       => array( 'WC_Gateway_redsys' ),
			'Bizum'      => array( 'WC_Gateway_Bizum_Redsys' ),
			'Google Pay' => array( 'WC_Gateway_GooglePay_Redirection_Redsys' ),
		);
	}

	/**
	 * @dataProvider gateways
	 * @param string $class Gateway class name.
	 */
	public function test_a_hostile_translation_adds_no_markup_and_does_not_break_the_script( $class ) {
		if ( 'WC_Gateway_GooglePay_Redirection_Redsys' === $class ) {
			// Pre-existing and deferred (S-042): this class reads the billing name through the generic meta API.
			$this->setExpectedIncorrectUsage( 'is_internal_meta_key' );
		}
		$order = wc_create_order();
		$order->set_total( 12.34 );
		$order->save();
		$gateway               = new $class();
		$gateway->secretsha256 = base64_encode( str_repeat( 'S', 24 ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$gateway->testmode     = 'no';
		$gateway->debug        = 'no';

		ob_start();
		$gateway->receipt_page( $order->get_id() );
		$page   = ob_get_clean();
		$script = implode( "\n", (array) wp_scripts()->get_data( 'woocommerce', 'after' ) );

		$this->assertStringContainsString( 'id="redsys_payment_form"', $page, 'The form is printed.' );
		$this->assertStringNotContainsString( 'redsyslite-injected"', $page, 'No element from a translation reaches the page.' );
		$this->assertStringNotContainsString( '<script>alert(1)', $page );
		$this->assertStringContainsString( 'Cancel order &amp; restore cart', $page, 'The intended entity of the cancel label is kept, not double-encoded.' );
		$this->assertStringContainsString( 'We are now redirecting', $script, 'The redirect message is in the inline script.' );
		$this->assertStringNotContainsString( '"><b id="redsyslite-injected"', $script, 'The translation cannot close the script string.' );
	}

	/**
	 * The documented woocommerce_ajax_loader_url filter sets the spinner image
	 * of the redirect message; without a listener WooCommerce's own spinner is
	 * used (the counterpart). Self-audit answer 5 at the 7.1.0 gate (D-100).
	 *
	 * @dataProvider gateways
	 * @param string $class Gateway class name.
	 */
	public function test_the_ajax_loader_url_filter_sets_the_spinner_of_the_redirect_message( $class ) {
		if ( 'WC_Gateway_GooglePay_Redirection_Redsys' === $class ) {
			// Pre-existing and deferred (S-042): this class reads the billing name through the generic meta API.
			$this->setExpectedIncorrectUsage( 'is_internal_meta_key' );
		}
		remove_filter( 'gettext', array( $this, 'hostile_translation' ), 10 );
		$order = wc_create_order();
		$order->set_total( 12.34 );
		$order->save();
		$gateway               = new $class();
		$gateway->secretsha256 = base64_encode( str_repeat( 'S', 24 ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$gateway->testmode     = 'no';
		$gateway->debug        = 'no';

		ob_start();
		$gateway->receipt_page( $order->get_id() );
		ob_end_clean();
		$default = implode( "\n", (array) wp_scripts()->get_data( 'woocommerce', 'after' ) );

		wp_scripts()->registered['woocommerce']->extra['after'] = array();
		$loader = static function () {
			return 'https://example.invalid/redsyslite-loader.gif';
		};
		add_filter( 'woocommerce_ajax_loader_url', $loader );
		ob_start();
		$gateway->receipt_page( $order->get_id() );
		ob_end_clean();
		remove_filter( 'woocommerce_ajax_loader_url', $loader );
		$filtered = implode( "\n", (array) wp_scripts()->get_data( 'woocommerce', 'after' ) );

		$this->assertStringContainsString( 'select2-spinner.gif', $default, 'Without a listener the spinner is WooCommerce\'s own.' );
		$this->assertStringNotContainsString( 'redsyslite-loader.gif', $default );
		$this->assertStringContainsString( 'https://example.invalid/redsyslite-loader.gif', $filtered, 'The listener\'s URL becomes the spinner.' );
		$this->assertStringNotContainsString( 'select2-spinner.gif', $filtered );
	}
}
