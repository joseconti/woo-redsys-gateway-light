<?php
/**
 * Uninstalling removes the plugin's own bookkeeping only (owner, D-085):
 * its version and notice options and its transients go; the gateway
 * settings, signing secrets included, the payment tokens and the order
 * data stay, so a reinstall needs no bank data. Slice S-081.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class UninstallTest extends WP_UnitTestCase {

	private function run_uninstall() {
		if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
			define( 'WP_UNINSTALL_PLUGIN', 'woo-redsys-gateway-light/woocommerce-redsys.php' );
		}
		include dirname( __DIR__, 2 ) . '/uninstall.php';
	}

	public function test_uninstalling_removes_the_internal_data_and_keeps_the_merchant_configuration() {
		foreach ( array( 'woocommerce-redsys-version', 'woocommerce-redsys-rate', 'hide-new-version-redsys-notice', 'telegram-redsys-notice' ) as $option ) {
			update_option( $option, 'x' );
		}
		$transients = array( 'redsys_signature_123000000001', 'redys_order_temp_123000000001', '42_redsys_refund', 'redsyslite_mark_paid_attempt_42' );
		foreach ( $transients as $transient ) {
			set_transient( $transient, 'x', HOUR_IN_SECONDS );
		}
		update_option( 'woocommerce_redsys_settings', array( 'secretsha256' => 'kept' ) );
		update_option( 'woocommerce_bizumredsys_settings', array( 'secretsha256' => 'kept' ) );
		update_option( 'txnid_7', 'kept' );
		set_transient( 'unrelated_plugin_transient', 'kept', HOUR_IN_SECONDS );
		set_transient( 'abc_redsys_refund', 'kept', HOUR_IN_SECONDS );
		$order = wc_create_order();
		$order->update_meta_data( '_payment_order_number_redsys', '123000000001' );
		$order->save();

		$this->run_uninstall();
		wp_cache_flush();

		foreach ( array( 'woocommerce-redsys-version', 'woocommerce-redsys-rate', 'hide-new-version-redsys-notice', 'telegram-redsys-notice' ) as $option ) {
			$this->assertFalse( get_option( $option ), $option . ' is removed.' );
		}
		foreach ( $transients as $transient ) {
			$this->assertFalse( get_transient( $transient ), $transient . ' is removed.' );
		}
		$this->assertSame( array( 'secretsha256' => 'kept' ), get_option( 'woocommerce_redsys_settings' ) );
		$this->assertSame( array( 'secretsha256' => 'kept' ), get_option( 'woocommerce_bizumredsys_settings' ) );
		$this->assertSame( 'kept', get_option( 'txnid_7' ) );
		$this->assertSame( 'kept', get_transient( 'unrelated_plugin_transient' ) );
		$this->assertSame( 'kept', get_transient( 'abc_redsys_refund' ), 'Only "<order id>_redsys_refund" is the plugin\'s.' );
		$this->assertSame( '123000000001', wc_get_order( $order->get_id() )->get_meta( '_payment_order_number_redsys' ) );
	}

	/**
	 * The premium plugin uses the same names; with it installed, deleting
	 * this plugin removes only what this plugin alone uses (D-087).
	 */
	public function test_with_the_premium_plugin_installed_only_the_names_of_this_plugin_go() {
		$shared = array( 'redsys_signature_123000000001', 'redys_order_temp_123000000001', '42_redsys_refund' );
		update_option( 'woocommerce-redsys-version', '32.2.1' );
		foreach ( $shared as $transient ) {
			set_transient( $transient, 'x', HOUR_IN_SECONDS );
		}
		set_transient( 'redsyslite_mark_paid_attempt_42', 1, HOUR_IN_SECONDS );
		wp_cache_set(
			'plugins',
			array(
				'' => array(
					'woocommerce-gateway-redsys/woocommerce-gateway-redsys.php' => array(
						'Name'       => 'WooCommerce Servired/RedSys Spain Gateway',
						'TextDomain' => 'woocommerce-gateway-redsys',
					),
				),
			),
			'plugins'
		);

		$this->run_uninstall();
		wp_cache_delete( 'plugins', 'plugins' );

		$this->assertSame( '32.2.1', get_option( 'woocommerce-redsys-version' ) );
		foreach ( $shared as $transient ) {
			$this->assertSame( 'x', get_transient( $transient ), $transient . ' belongs to the premium plugin too.' );
		}
		$this->assertFalse( get_transient( 'redsyslite_mark_paid_attempt_42' ) );
	}
}
