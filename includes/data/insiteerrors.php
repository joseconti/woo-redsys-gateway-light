<?php

/*
* Copyright: (C) 2013 - 2021 José Conti
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
* Copyright: (C) 2013 - 2021 José Conti
*/
function redsys_return_insiteerrors() {
	return array(
		'msg1'  => esc_html( 'You have to fill in the data of the card' , 'woo-redsys-gateway-light' ),
		'msg2'  => esc_html( 'The credit card is required' , 'woo-redsys-gateway-light' ),
		'msg3'  => esc_html( 'The credit card must be numerical' , 'woo-redsys-gateway-light' ),
		'msg4'  => esc_html( 'The credit card cannot be negative' , 'woo-redsys-gateway-light' ),
		'msg5'  => esc_html( 'The expiration month of the card is required.' , 'woo-redsys-gateway-light' ),
		'msg6'  => esc_html( 'The expiration month of the credit card must be numerical' , 'woo-redsys-gateway-light' ),
		'msg7'  => esc_html( 'The credit card\'s expiration month is incorrect' , 'woo-redsys-gateway-light' ),
		'msg8'  => esc_html( 'The year of expiry of the card is mandatory.' , 'woo-redsys-gateway-light' ),
		'msg9'  => esc_html( 'The year of expiry of the card must be numerical' , 'woo-redsys-gateway-light' ),
		'msg10' => esc_html( 'The year of expiry of the card cannot be negative' , 'woo-redsys-gateway-light' ),
		'msg11' => esc_html( 'The security code on the card is not the correct length' , 'woo-redsys-gateway-light' ),
		'msg12' => esc_html( 'The security code on the credit card must be numerical' , 'woo-redsys-gateway-light' ),
		'msg13' => esc_html( 'The security code on the credit card cannot be negative' , 'woo-redsys-gateway-light' ),
		'msg14' => esc_html( 'The security code is not required for your card' , 'woo-redsys-gateway-light' ),
		'msg15' => esc_html( 'The length of the credit card is not correct' , 'woo-redsys-gateway-light' ),
		'msg16' => esc_html( 'You must enter a valid credit card number (without spaces or dashes).' , 'woo-redsys-gateway-light' ),
		'msg17' => esc_html( 'Incorrect validation by the commerce' , 'woo-redsys-gateway-light' ),
	);
}
