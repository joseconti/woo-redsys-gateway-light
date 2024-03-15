<?php
/**
 * Devuelve los tipos de orden
 *
 * @package WooCommerce Redsys Gateway Ligth
 *
 * @return array
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Devuelve los tipos de orden
 *
 * @return array
 */
function redsys_return_number_order_type() {

	return array(
		'threepluszeros'        => __( '3 random numbers followed by zeros (Standard and default). Ex: 734000008934', 'woo-redsys-gateway-light' ),
		'endoneletter'          => __( 'One random lowercase letter at the end, with zeros. Ex: 00000008934i', 'woo-redsys-gateway-light' ),
		'endtwoletters'         => __( 'Two random lowercase letter at the end, with zeros. Ex: 000008934iz', 'woo-redsys-gateway-light' ),
		'endthreeletters'       => __( 'Three random lowercase letter at the end, with zeros. Ex: 000008934izq', 'woo-redsys-gateway-light' ),
		'endoneletterup'        => __( 'One random capital letter at the end, with zeros. Ex: 00000008934Z', 'woo-redsys-gateway-light' ),
		'endtwolettersup'       => __( 'Two random lowercase letter at the end, with zeros. Ex: 000008934IZ', 'woo-redsys-gateway-light' ),
		'endthreelettersup'     => __( 'Three random capital letter at the end, with zeros. Ex: 000008934ZYA', 'woo-redsys-gateway-light' ),
		'endoneletterdash'      => __( 'Dash One random lowercase letter at the end, with zeros. Ex: 00000008934-i', 'woo-redsys-gateway-light' ),
		'endtwolettersdash'     => __( 'Dash two random lowercase letter at the end, with zeros. Ex: 000008934-iz', 'woo-redsys-gateway-light' ),
		'endthreelettersdash'   => __( 'DashThree random lowercase letter at the end, with zeros. Ex: 000008934-izq', 'woo-redsys-gateway-light' ),
		'endoneletterupdash'    => __( 'Dash One random capital letter at the end, with zeros. Ex: 00000008934-Z', 'woo-redsys-gateway-light' ),
		'endtwolettersupdash'   => __( 'Dash two random lowercase letter at the end, with zeros. Ex: 000008934-IZ', 'woo-redsys-gateway-light' ),
		'endthreelettersupdash' => __( 'Dash Three random capital letter at the end, with zeros. Ex: 000008934-ZYA', 'woo-redsys-gateway-light' ),
		'simpleorder'           => __( 'Number created by WooCommerce only with zeros (it gives problems, not recommended) Ex: 000000008934', 'woo-redsys-gateway-light' ),
	);
}
