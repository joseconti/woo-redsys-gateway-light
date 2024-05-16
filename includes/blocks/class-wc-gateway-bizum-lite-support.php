<?php
/**
 * WooCommerce Redsys Gateway Lite
 *
 * @package WooCommerce Redsys Gateway Lite
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

/**
 * Bizum Lite Support
 *
 * @since 5.0.0
 */
final class WC_Gateway_Bizum_Lite_Support extends AbstractPaymentMethodType {

	/**
	 * The gateway instance.
	 *
	 * @var WC_Gateway_Bizum_Redsys
	 */
	private $gateway;

	/**
	 * Payment method name/id/slug.
	 *
	 * @var string
	 */
	protected $name = 'bizumredsys';

	/**
	 * Initializes the payment method type.
	 */
	public function initialize() {
		$this->settings = get_option( 'woocommerce_bizumredsys_settings', array() );

	}

	/**
	 * Returns if this payment method should be active. If false, the scripts will not be enqueued.
	 *
	 * @return boolean
	 */
	public function is_active() {
		return WCRedL()->is_gateway_enabled( 'bizumredsys' );
	}

	/**
	 * Returns an array of scripts/handles to be registered for this payment method.
	 *
	 * @return array
	 */
	public function get_payment_method_script_handles() {
		$script_path       = '/assets/js/frontend/blocks.js';
		$script_asset_path = plugin_abspath_redsys() . 'assets/js/frontend/blocks.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => '1.2.0',
			);
		$script_url        = plugin_url_redsys() . $script_path;

		wp_register_script(
			'wc-bizumredsys-payments-blocks',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'wc-bizumredsys-payments-blocks', 'woo-redsys-gateway-light', plugin_abspath_redsys() . 'languages/' );
		}

		return array( 'wc-bizumredsys-payments-blocks' );
	}

	/**
	 * Returns an array of key=>value pairs of data made available to the payment methods script.
	 *
	 * @return array
	 */
	public function get_payment_method_data() {
		return array(
			'title'       => WCRedL()->get_redsys_option( 'title', 'bizumredsys' ),
			'description' => WCRedL()->get_redsys_option( 'description', 'bizumredsys' ),
			'supports'    => array(
				'products',
				'refunds',
			),
		);
	}
}
