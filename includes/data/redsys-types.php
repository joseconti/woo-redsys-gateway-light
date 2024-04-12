<?php
/**
 * Devuelve los tipos de retorno
 *
 * @package WooCommerce Redsys Gateway Ligth
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Devuelve los tipos de retorno
 *
 * @return array
 */
function redsys_return_types() {

	return array(
		'redsys',
		'bizumredsys',
		'googlepayredirecredsys',
	);
}
