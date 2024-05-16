<?php
/**
 * Google Pay Redirection Redsys Support
 *
 * @package WooCommerce\GooglePayRedireRedsys
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

/**
 * Google Pay Redirection Redsys Support
 *
 * @since 6.0.0
 */
final class WC_Gateway_GooglePay_Redirection_Redsys_Support extends AbstractPaymentMethodType {

	/**
	 * The gateway instance.
	 *
	 * @var WC_Gateway_GooglePay_Redirection_Redsys
	 */
	private $gateway;

	/**
	 * Payment method name/id/slug.
	 *
	 * @var string
	 */
	protected $name = 'googlepayredirecredsys';

	/**
	 * Initializes the payment method type.
	 */
	public function initialize() {
		$this->settings = get_option( 'woocommerce_googlepayredirecredsys_settings', array() );
	}

	/**
	 * Returns if this payment method should be active. If false, the scripts will not be enqueued.
	 *
	 * @return boolean
	 */
	public function is_active() {
		return WCRedL()->is_gateway_enabled( 'googlepayredirecredsys' );
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
			'wc-googlepayredirecredsys-payments-blocks',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'wc-googlepayredirecredsyss-payments-blocks', 'woo-redsys-gateway-light', plugin_abspath_redsys() . 'languages/' );
		}

		return array( 'wc-googlepayredirecredsys-payments-blocks' );
	}

	/**
	 * Returns an array of key=>value pairs of data made available to the payment methods script.
	 *
	 * @return array
	 */
	public function get_payment_method_data() {
		return array(
			'title'       => WCRedL()->get_redsys_option( 'title', 'googlepayredirecredsys' ),
			'description' => WCRedL()->get_redsys_option( 'description', 'googlepayredirecredsys' ),
			'supports'    => array(
				'products',
				'refunds',
			),
		);
	}
}
