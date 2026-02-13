<?php
/**
 * Plugins to deactivate
 *
 * @package WooCommerce Redsys Gateway Light
 *
 * @return array
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Plugins to deactivate.
 */
function redsyslite_plugins_to_deactivate() {

	return array(
		'/redsysoficial/class-wc-redsys.php',
		'/redsys/class-wc-redsys.php',
		'/bizum/class-wc-bizum.php',
		'/woocommerce-sermepa-payment-gateway/wc_redsys_payment_gateway.php',
	);
}
