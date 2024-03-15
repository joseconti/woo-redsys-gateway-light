<?php
/**
 * Devuelve los estados de pedido pagados
 *
 * @package WooCommerce Redsys Gateway Ligth
 *
 * @return array
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Devuelve los estados de pedido pagados
 *
 * @return array
 */
function redsys_return_status_paid() {

	$status = array();
	$status = array(
		'pending',
		'redsys-pbankt',
		'cancelled',
		'pending-deposit',
	);
	return $status;
}
