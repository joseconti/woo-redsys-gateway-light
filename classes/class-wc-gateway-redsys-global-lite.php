<?php
/**
 * Copyright: (C) 2013 - 2021 José Conti
 *
 * @package WooCommerce Redsys Gateway Ligh
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Global class Redsys
 * This class is used to get the options of the plugin and to get the data of the plugin.
 *
 * @since 1.0.0
 */
class WC_Gateway_Redsys_Global_Lite {

	/**
	 * Log
	 *
	 * @var WC_Logger
	 */
	public $log;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->log = new WC_Logger();
	}
	/**
	 * Debug function.
	 *
	 * @param mixed $log The log to debug.
	 */
	public function debug( $log ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$debug = new WC_Logger();
			$debug->add( 'redsys-global', $log );
		}
	}
	/**
	 * Get Redsys option.
	 *
	 * @param string $option The option to retrieve.
	 * @param string $gateway The gateway name.
	 * @return mixed The option value.
	 */
	public function get_redsys_option( $option, $gateway ) {

		$options = get_option( 'woocommerce_' . $gateway . '_settings' );

		if ( ! empty( $options ) ) {
			$redsys_options = maybe_unserialize( $options );
			if ( array_key_exists( $option, $redsys_options ) ) {
				$option_value = $redsys_options[ $option ];
				return $option_value;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	/**
	 * Clean data.
	 *
	 * @param mixed $out The data to clean.
	 * @return mixed The cleaned data.
	 */
	public function clean_data( $out ) {
		// Arrays con los caracteres a reemplazar y sus reemplazos.
		$search  = array(
			'Á',
			'À',
			'Ä',
			'É',
			'È',
			'Ë',
			'Í',
			'Ì',
			'Ï',
			'Ó',
			'Ò',
			'Ö',
			'Ú',
			'Ù',
			'Ü',
			'á',
			'à',
			'ä',
			'é',
			'è',
			'ë',
			'í',
			'ì',
			'ï',
			'ó',
			'ò',
			'ö',
			'ú',
			'ù',
			'ü',
			'Ñ',
			'ñ',
			'&',
			'<',
			'>',
			'/',
			'"',
			"'",
			'?',
			'¿',
			'º',
			'ª',
			'#',
			'@',
		);
		$replace = array(
			'A',
			'A',
			'A',
			'E',
			'E',
			'E',
			'I',
			'I',
			'I',
			'O',
			'O',
			'O',
			'U',
			'U',
			'U',
			'a',
			'a',
			'a',
			'e',
			'e',
			'e',
			'i',
			'i',
			'i',
			'o',
			'o',
			'o',
			'u',
			'u',
			'u',
			'N',
			'n',
			'-',
			' ',
			' ',
			' ',
			' ',
			' ',
			' ',
			' ',
			' ',
			'',
			'',
			' ',
		);

		// Realizar todas las sustituciones en una sola llamada.
		return str_replace( $search, $replace, $out );
	}
	/**
	 * Get the order object by order ID.
	 *
	 * @param int $order_id The ID of the order.
	 * @return WC_Order|false The order object if found, false otherwise.
	 */
	public function get_order( $order_id ) {
		$order = new WC_Order( $order_id );
		return $order;
	}
	/**
	 * Get the meta value for a specific key from the order.
	 *
	 * @param int    $order_id The ID of the order.
	 * @param string $key      The meta key.
	 * @param bool   $single   Optional. Whether to return a single value or an array of values. Default true.
	 * @return mixed The meta value if found, false otherwise.
	 */
	public function get_order_meta( $order_id, $key, $single = true ) {
		$order = wc_get_order( $order_id );
		if ( $order ) {
			$context  = 'view';
			$order_id = $order->get_meta( $key, $single, $context );
			if ( $order_id ) {
				$post_id = $order_id;
			}
			$meta = $order->get_meta( $key, $single, $context );
			return $meta;
		} else {
			return false;
		}
	}
	/**
	 * Update the meta data for an order.
	 *
	 * @param int          $post_id        The ID of the order.
	 * @param array|string $meta_key_array The meta key(s) to update.
	 * @param mixed        $meta_value     Optional. The new meta value. Default false.
	 */
	public function update_order_meta( $post_id, $meta_key_array, $meta_value = false ) {
		// Si $meta_key_array no es un array, se convierte en un array asociativo.
		if ( ! is_array( $meta_key_array ) ) {
			$meta_key_array = array( $meta_key_array => $meta_value );
		}

		// Obtener el ID del pedido asociado al post_id.
		$order_id = $this->get_order_meta( $post_id, 'post_id', true );

		// Si existe un ID de pedido asociado, usarlo, de lo contrario, usar el post_id.
		if ( $order_id ) {
			$post_id = $order_id;
		} else {
			$post_id = $post_id;
		}

		// Obtener el pedido de WooCommerce.
		$order = wc_get_order( $post_id );

		// Actualizar los metadatos del pedido.
		foreach ( $meta_key_array as $meta_key => $meta_value ) {
			$order->update_meta_data( $meta_key, $meta_value );
		}

		// Guardar los cambios en el pedido.
		$order->save();
	}
	/**
	 * Set the transaction ID for a token.
	 *
	 * @param int    $token_num      The token number.
	 * @param string $redsys_txnid   The transaction ID.
	 */
	public function set_txnid( $token_num, $redsys_txnid ) {
		if ( $redsys_txnid ) {
			update_option( 'txnid_' . $token_num, $redsys_txnid );
		}
	}
	/**
	 * Set the token type for a token.
	 *
	 * @param int    $token_num      The token number.
	 * @param string $type           The token type.
	 */
	public function set_token_type( $token_num, $type ) {
		if ( $token_num && $type ) {
			update_option( 'token_type_' . $token_num, $type );
		}
	}
	/**
	 * Get the transaction ID for a token.
	 *
	 * @param int $token_num The token number.
	 * @return mixed The transaction ID if found, false otherwise.
	 */
	public function get_txnid( $token_num ) {
		if ( $token_num ) {
			$redsys_txnid = get_option( 'txnid_' . $token_num, true );
			if ( $redsys_txnid ) {
				return $redsys_txnid;
			} else {
				return '999999999999999'; // Temporal return for old tokens.
			}
		}
		return false;
	}
	/**
	 * Get the token type for a token.
	 *
	 * @param int $token_num The token number.
	 * @return mixed The token type if found, false otherwise.
	 */
	public function get_token_type( $token_num ) {
		if ( $token_num ) {
			$redsys_token_type = get_option( 'token_type_' . $token_num, true );
			if ( $redsys_token_type ) {
				return $redsys_token_type;
			} else {
				return 'R'; // Temporal return for old tokens.
			}
		}
		return false;
	}
	/**
	 * Get the DS error.
	 *
	 * @return array The DS error.
	 */
	public function get_ds_error() {

		include_once REDSYS_PLUGIN_DATA_PATH . 'dserrors.php';

		$dserrors = array();
		$dserrors = redsys_return_dserrors();
		return $dserrors;
	}
	/**
	 * Get the DS response.
	 *
	 * @return array The DS response.
	 */
	public function get_ds_response() {

		include_once REDSYS_PLUGIN_DATA_PATH . 'dsresponse.php';

		$dsresponse = array();
		$dsresponse = redsys_return_dsresponse();
		return $dsresponse;
	}
	/**
	 * Get the error message.
	 *
	 * @return array The error message.
	 */
	public function get_msg_error() {

		include_once REDSYS_PLUGIN_DATA_PATH . 'insiteerrors.php';

		$msgerrors = array();
		$msgerrors = redsys_return_insiteerrors();
		return $msgerrors;
	}
	/**
	 * Get country codes phone.
	 *
	 * @return array The country codes phone.
	 */
	public function get_country_codes_phone() {

		include_once REDSYS_PLUGIN_DATA_PATH . 'countries-2.php';

		$countries = array();
		$countries = redsys_get_country_code_2();
		return $countries;
	}
	/**
	 * Get country codes 2
	 *
	 * @param string $country_code_2 Country Code 2.
	 */
	public function get_country_codes_2( $country_code_2 ) {

		$countries = array();
		$countries = $this->get_country_codes_phone();

		if ( $countries ) {
			foreach ( $countries as $country => $valor ) {
				$country_2_up = strtoupper( $country_code_2 );
				if ( $country_2_up === $country ) {
					return $valor;
				} else {
					continue;
				}
			}
		}
		return false;
	}
	/**
	 * Get country codes
	 */
	public function get_country_codes() {

		include_once REDSYS_PLUGIN_DATA_PATH . 'countries.php';

		$countries = array();
		$countries = redsys_get_country_code();
		return $countries;
	}
	/**
	 * Get country codes 3
	 *
	 * @param string $country_code_2 Country code 2.
	 */
	public function get_country_codes_3( $country_code_2 ) {

		$countries = array();
		$countries = $this->get_country_codes();

		if ( $countries ) {
			foreach ( $countries as $country => $valor ) {
				$country_2_up = strtoupper( $country_code_2 );
				if ( $country_2_up === $country ) {
					return $valor;
				} else {
					continue;
				}
			}
		}
		return false;
	}
	/**
	 * Check if the given error code is a DS error.
	 *
	 * @param mixed $error_code The error code to check.
	 * @return bool True if the error code is a DS error, false otherwise.
	 */
	public function is_ds_error( $error_code = null ) {

		$ds_errors = array();
		$ds_errors = $this->get_ds_error();

		if ( $error_code ) {
			foreach ( $ds_errors as $ds_error => $value ) {
				if ( (string) $ds_error === (string) $error_code ) {
					return true;
				} else {
					continue;
				}
			}
			return false;
		}
		return false;
	}
	/**
	 * Check if the given error code is a DS response.
	 *
	 * @param mixed $error_code The error code to check.
	 * @return bool True if the error code is a DS response, false otherwise.
	 */
	public function is_ds_response( $error_code = null ) {

		$ds_response  = array();
		$ds_responses = $this->get_ds_response();

		if ( $error_code ) {
			foreach ( $ds_responses as $ds_response => $value ) {
				if ( (string) $ds_response === (string) $error_code ) {
					return true;
				}
				continue;
			}
			return false;
		}
		return false;
	}
	/**
	 * Check if the given error code is a message error.
	 *
	 * @param mixed $error_code The error code to check.
	 * @return bool True if the error code is a message error, false otherwise.
	 */
	public function is_msg_error( $error_code = null ) {

		$msg_errors = array();
		$msg_errors = $this->get_msg_error();

		if ( $error_code ) {
			foreach ( $msg_errors as $msg_error => $value ) {
				if ( (string) $msg_error === (string) $error_code ) {
					return true;
				} else {
					continue;
				}
			}
			return false;
		}
		return false;
	}
	/**
	 * Get the message error by error code.
	 *
	 * @param mixed $error_code The error code to check.
	 * @return mixed The message error if found, false otherwise.
	 */
	public function get_msg_error_by_code( $error_code = null ) {

		$smg_errors = array();
		$smg_errors = $this->get_msg_error();

		if ( $error_code ) {
			if ( ! empty( $error_code ) ) {
				if ( ! empty( $smg_errors ) ) {
					foreach ( $smg_errors as $msg_error => $value ) {
						if ( (string) $msg_error === (string) $error_code ) {
							return $value;
						} else {
							continue;
						}
					}
				}
			} else {
				return false;
			}
		}
		return false;
	}
	/**
	 * Get the error message by error code.
	 *
	 * @param mixed $error_code The error code to check.
	 * @return mixed The error message if found, false otherwise.
	 */
	public function get_error_by_code( $error_code = null ) {

		$ds_errors = array();
		$ds_errors = $this->get_ds_error();

		if ( $error_code ) {
			if ( ! empty( $error_code ) ) {
				if ( ! empty( $ds_errors ) ) {
					foreach ( $ds_errors as $ds_error => $value ) {
						if ( (string) $ds_error === (string) $error_code ) {
							return $value;
						} else {
							continue;
						}
					}
				}
			} else {
				return false;
			}
		}
		return false;
	}
	/**
	 * Get the response by error code.
	 *
	 * @param mixed $error_code The error code to check.
	 * @return mixed The response if found, false otherwise.
	 */
	public function get_response_by_code( $error_code = null ) {

		$ds_responses = array();
		$ds_responses = $this->get_ds_response();

		if ( $error_code ) {
			if ( ! empty( $error_code ) ) {
				if ( ! empty( $ds_responses ) ) {
					foreach ( $ds_responses as $ds_response => $value ) {
						if ( (string) $ds_response === (string) $error_code ) {
							return $value;
						} else {
							continue;
						}
					}
				}
			} else {
				return false;
			}
		}
		return false;
	}
	/**
	 * Check if the given error code is a Redsys error.
	 *
	 * @param mixed $error_code The error code to check.
	 * @return bool True if the error code is a Redsys error, false otherwise.
	 */
	public function is_redsys_error( $error_code = null ) {

		if ( $error_code ) {
			if ( $this->is_ds_error( $error_code ) ) {
				return true;
			} elseif ( $this->is_ds_response( $error_code ) ) {
				return true;
			} elseif ( $this->is_msg_error( $error_code ) ) {
				return true;
			} else {
				return false;
			}
		}
		return false;
	}
	/**
	 * Get the error message by error code.
	 *
	 * @param mixed $error_code The error code to check.
	 * @return mixed The error message if found, false otherwise.
	 */
	public function get_error( $error_code = null ) {

		if ( $error_code ) {
			if ( $this->is_ds_error( $error_code ) ) {
				return $this->get_error_by_code( $error_code );
			} elseif ( $this->is_ds_response( $error_code ) ) {
				return $this->get_response_by_code( $error_code );
			} elseif ( $this->is_msg_error( $error_code ) ) {
				return $this->get_msg_error_by_code( $error_code );
			} else {
				return false;
			}
		}
		return false;
	}
	/**
	 * Get the error type by error code.
	 *
	 * @param mixed $error_code The error code to check.
	 * @return mixed The error type if found, false otherwise.
	 */
	public function get_error_type( $error_code = null ) {

		if ( $error_code ) {
			if ( $this->is_ds_error( $error_code ) ) {
				return 'ds_error';
			} elseif ( $this->is_ds_response( $error_code ) ) {
				return 'ds_response';
			} elseif ( $this->is_msg_error( $error_code ) ) {
				return 'msg_error';
			} else {
				return false;
			}
		}
		return false;
	}
	/**
	 * Get the currencies.
	 *
	 * @return array The currencies.
	 */
	public function get_currencies() {

		include_once REDSYS_PLUGIN_DATA_PATH . 'currencies.php';

		$currencies = array();
		$currencies = redsys_return_currencies();
		return $currencies;
	}
	/**
	 * Get the allowed currencies.
	 *
	 * @return array The allowed currencies.
	 */
	public function allowed_currencies() {

		include_once REDSYS_PLUGIN_DATA_PATH . 'allowed-currencies.php';

		$currencies = array();
		$currencies = redsys_return_allowed_currencies();
		return $currencies;
	}
	/**
	 * Get the Redsys languages.
	 *
	 * @return array The Redsys languages.
	 */
	public function get_redsys_languages() {

		include_once REDSYS_PLUGIN_DATA_PATH . 'languages.php';

		$languages = array();
		$languages = redsys_return_languages();
		return $languages;
	}
	/**
	 * Get the Redsys WP languages.
	 *
	 * @return array The Redsys WP languages.
	 */
	public function get_redsys_wp_languages() {

		include_once REDSYS_PLUGIN_DATA_PATH . 'wplanguages.php';

		$languages = array();
		$languages = redsys_return_all_languages_code();
		return $languages;
	}
	/**
	 * Get the orders type.
	 *
	 * @return mixed The orders type.
	 */
	public function get_orders_type() {

		include_once REDSYS_PLUGIN_DATA_PATH . 'redsys-types.php';

		$types = array();
		$types = redsys_return_types();
		return $types;
	}
	/**
	 * Get the language code.
	 *
	 * @param string $lang The language code.
	 * @return string The language code.
	 */
	public function get_lang_code( $lang = 'en' ) {

		$lang = trim( $lang );

		if ( 'yes' === $this->get_redsys_option( 'debug', 'redsys' ) ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '     Is Global Class       ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', 'Asking for language: ' . $lang );
			$this->log->add( 'redsys', ' ' );
		}

		$languages = array();
		$languages = $this->get_redsys_wp_languages();

		if ( 'yes' === $this->get_redsys_option( 'debug', 'redsys' ) ) {

			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', 'Asking for language: ' . $lang );
			$this->log->add( 'redsys', ' All Languages ($languages): ' . print_r( $languages, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		}

		if ( ! empty( $languages ) ) {
			foreach ( $languages as $language => $value ) {
				if ( 'yes' === $this->get_redsys_option( 'debug', 'redsys' ) ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '$language: ' . $language );
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', 'Checking if ' . $language . ' is like ' . $lang );
				}
				if ( (string) $language === (string) $lang ) {
					if ( 'yes' === $this->get_redsys_option( 'debug', 'redsys' ) ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '$value: ' . $value );
						$this->log->add( 'redsys', ' ' );
					}
					return $value;
				} else {
					continue;
				}
			}
		} else {
			return '2';
		}
	}
	/**
	 * Check if an order exists.
	 *
	 * @param int $order_id The order ID.
	 * @return bool True if the order exists, false otherwise.
	 */
	public function order_exist( $order_id ) {

		$order = wc_get_order( $order_id );

		if ( empty( $order ) ) {
			return false;
		}
		return true;
	}
	/**
	 * Check if a post exists.
	 *
	 * @param int $order_id The order ID.
	 * @return bool True if the post exists, false otherwise.
	 */
	public function post_exist( $order_id ) {
		$post_status = get_post_status( $order_id );

		if ( false === $post_status ) {
			return false;
		} else {
			return true;
		}
	}
	/**
	 * Check if an order is a Redsys order.
	 *
	 * @param int    $order_id The order ID.
	 * @param string $type     The payment method type.
	 * @return bool True if the order is a Redsys order, false otherwise.
	 */
	public function is_redsys_order( $order_id, $type = null ) {

		$post_status = $this->order_exist( $order_id );

		if ( $post_status ) {
			$order        = new WC_Order( $order_id );
			$gateway      = $order->get_payment_method();
			$redsys_types = array();
			$redsys_types = $this->get_orders_type();
			if ( ! empty( $redsys_types ) ) {
				if ( ! $type ) {
					foreach ( $redsys_types as $redsys_type ) {
						if ( (string) $redsys_type === (string) $gateway ) {
							return true;
						}
						continue;
					}
					return false;
				} elseif ( $gateway === $type ) {
						return true;
				} else {
					return false;
				}
			}
			return false;
		}
		return false;
	}
	/**
	 * Get the payment gateway for an order.
	 *
	 * @param int $order_id The order ID.
	 * @return string|bool The payment gateway if found, false otherwise.
	 */
	public function get_gateway( $order_id ) {

		$post_status = $this->order_exist( $order_id );

		if ( $post_status ) {
			$order   = new WC_Order( $order_id );
			$gateway = $order->get_payment_method();
			return $gateway;
		} else {
			return false;
		}
	}
	/**
	 * Get the order number for an order.
	 *
	 * @param int $order_id The order ID.
	 * @return string|bool The order number if found, false otherwise.
	 */
	public function get_order_mumber( $order_id ) {
		$number = $this->get_order_meta( $order_id, '_payment_order_number_redsys', true );
		if ( ! $number ) {
			return false;
		}
		return $number;
	}
	/**
	 * Get the order date for an order.
	 *
	 * @param int $order_id The order ID.
	 * @return string|bool The order date if found, false otherwise.
	 */
	public function get_order_date( $order_id ) {

		$date_decoded = str_replace( '%2F', '/', $this->get_order_meta( $order_id, '_payment_date_redsys', true ) );
		if ( ! $date_decoded ) {
			return false;
		}
		return $date_decoded;
	}
	/**
	 * Get the order hour for an order.
	 *
	 * @param int $order_id The order ID.
	 * @return string|bool The order hour if found, false otherwise.
	 */
	public function get_order_hour( $order_id ) {
		$hour_decoded = str_replace( '%3A', ':', $this->get_order_meta( $order_id, '_payment_hour_redsys', true ) );
		if ( ! $hour_decoded ) {
			return false;
		}
		return $hour_decoded;
	}
	/**
	 * Get the authorization code for an order.
	 *
	 * @param int $order_id The order ID.
	 * @return string|bool The authorization code if found, false otherwise.
	 */
	public function get_order_auth( $order_id ) {
		$auth = $this->get_order_meta( $order_id, '_authorisation_code_redsys', true );
		if ( ! $auth ) {
			return false;
		}
		return $auth;
	}
	/**
	 * Get the pending status for an order.
	 *
	 * @return string The pending status.
	 */
	public function get_status_pending() {

		include_once REDSYS_PLUGIN_DATA_PATH . 'redsys-status-paid.php';

		$status = array();
		$status = redsys_return_status_paid();
		return apply_filters( 'redsys_status_pending', $status );
	}
	/**
	 * Check if an order is paid.
	 *
	 * @param int $order_id The order ID.
	 * @return bool True if the order is paid, false otherwise.
	 */
	public function is_paid( $order_id ) {

		if ( 'yes' === $this->get_redsys_option( 'debug', 'redsys' ) ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', 'Checking order $order_id: ' . $order_id );
			$this->log->add( 'redsys', ' ' );
		}

		if ( $this->order_exist( $order_id ) ) {
			if ( 'yes' === $this->get_redsys_option( 'debug', 'redsys' ) ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', 'Order Exist: ' . $order_id );
				$this->log->add( 'redsys', ' ' );
			}
			$order       = $this->get_order( $order_id );
			$status      = $order->get_status();
			$status_paid = array();
			if ( 'yes' === $this->get_redsys_option( 'debug', 'redsys' ) ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', 'Order Status: ' . $status );
				$this->log->add( 'redsys', ' ' );
			}
			$status_paid = $this->get_status_pending();
			if ( $status_paid ) {
				foreach ( $status_paid as $spaid ) {
					if ( 'yes' === $this->get_redsys_option( 'debug', 'redsys' ) ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '$spaid: ' . $spaid );
						$this->log->add( 'redsys', '$status: ' . $status );
						$this->log->add( 'redsys', ' ' );
					}
					if ( (string) $status === (string) $spaid ) {
						return false;
					}
					continue;
				}
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	/**
	 * Check if a gateway is enabled.
	 *
	 * @param string $gateway The gateway name.
	 * @return bool True if the gateway is enabled, false otherwise.
	 */
	public function is_gateway_enabled( $gateway ) {
		$is_enabled = $this->get_redsys_option( 'enabled', $gateway );

		if ( 'yes' === $is_enabled ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Check if a token is valid.
	 *
	 * @param int $token_id The token ID.
	 * @return bool True if the token is valid, false otherwise.
	 */
	public function check_if_token_is_valid( $token_id ) {

		$token     = WC_Payment_Tokens::get( $token_id );
		$year      = $token->get_expiry_year();
		$month     = $token->get_expiry_month();
		$act_year  = gmdate( 'Y' );
		$act_month = gmdate( 'm' );
		if ( $year >= $act_year ) {
			if ( $year > $act_year ) {
				return true;
			}
			if ( $month >= $act_month ) {
				return true;
			} else {
				WC_Payment_Tokens::delete( $token_id );
				return false;
			}
		} else {
			WC_Payment_Tokens::delete( $token_id );
			return false;
		}
	}
	/**
	 * Check if a specific type exists in the given tokens.
	 *
	 * @param array  $tokens The tokens to check.
	 * @param string $type   The type to search for.
	 * @return bool True if the type exists in the tokens, false otherwise.
	 */
	public function check_type_exist_in_tokens( $tokens, $type ) {
		foreach ( $tokens as $token ) {
			$token_num  = $token->get_token();
			$token_type = $this->get_token_type( $token_num );
			if ( $token_type === $type ) {
				if ( $token->get_gateway_id() === 'redsys' ) {
					$valid_token = $this->check_if_token_is_valid( $token->get_id() );
					if ( $valid_token ) {
						return true;
					}
					break;
				} else {
					continue;
				}
			}
			return false;
		}
	}
	/**
	 * Get the Redsys users token.
	 *
	 * @param bool $type The type of token.
	 * @return string|bool The Redsys users token if found, false otherwise.
	 */
	public function get_redsys_users_token( $type = false ) {
		// $type puede ser R (suscripción) o C (principalmente pago con 1 clic) en estos momentos.
		$customer_token = false;
		if ( is_user_logged_in() ) {
			if ( ! $type ) {
				$tokens = WC_Payment_Tokens::get_customer_tokens( get_current_user_id(), 'redsys' );
				foreach ( $tokens as $token ) {
					if ( $token->get_gateway_id() === 'redsys' ) {
						$valid_token = $this->check_if_token_is_valid( $token->get_id() );
						if ( $valid_token ) {
							$customer_token = $token->get_token();
						}
						break;
					} else {
						continue;
					}
				}
			} else {
				$tokens = WC_Payment_Tokens::get_customer_tokens( get_current_user_id(), 'redsys' );
				foreach ( $tokens as $token ) {
					$token_id  = $token->get_token();
					$type_type = $this->get_token_type( $token_id );
					if ( $type === $type_type ) {
						if ( $token->get_gateway_id() === 'redsys' ) {
							$valid_token = $this->check_if_token_is_valid( $token->get_id() );
							if ( $valid_token ) {
								$customer_token = $token_id;
								break;
							}
						}
						continue;
					} else {
						continue;
					}
				}
			}
		}
		return $customer_token;
	}
	/**
	 * Get the tokens for a specific user.
	 *
	 * @param int    $user_id The ID of the user.
	 * @param string $type    The type of token.
	 * @return string|bool The user's token if found, false otherwise.
	 */
	public function get_users_token_bulk( $user_id, $type = false ) {
		$customer_token = false;
		$tokens         = WC_Payment_Tokens::get_customer_tokens( $user_id, 'redsys' );
		if ( ! $type ) {
			foreach ( $tokens as $token ) {
				if ( $token->get_gateway_id() === 'redsys' ) {
					$valid_token = $this->check_if_token_is_valid( $token->get_id() );
					if ( $valid_token ) {
						$customer_token = $token->get_token();
					}
					break;
				} else {
					continue;
				}
			}
		} else {
			foreach ( $tokens as $token ) {
				$token_id  = $token->get_token();
				$type_type = $this->get_token_type( $token_id );
				if ( $type === $type_type ) {
					if ( $token->get_gateway_id() === 'redsys' ) {
						$valid_token = $this->check_if_token_is_valid( $token->get_id() );
						if ( $valid_token ) {
							$customer_token = $token->get_token();
						}
						break;
					} else {
						continue;
					}
				} else {
					continue;
				}
			}
		}
		return $customer_token;
	}
	/**
	 * Clean the order number.
	 *
	 * @param string $ordernumber The order number to clean.
	 * @return string The cleaned order number.
	 */
	public function clean_order_number( $ordernumber ) {
		$real_order = get_transient( 'redys_order_temp_' . $ordernumber );
		if ( $real_order ) {
			return $real_order;
		} else {
			return ltrim( substr( $ordernumber, 3 ), '0' );
		}
	}
	/**
	 * Prepare the order number.
	 *
	 * @param int $order_id The ID of the order.
	 * @return string The prepared order number.
	 */
	public function prepare_order_number( $order_id ) {
		$transaction_id  = str_pad( $order_id, 12, '0', STR_PAD_LEFT );
		$transaction_id1 = wp_rand( 1, 999 ); // lets to create a random number.
		$transaction_id2 = substr_replace( $transaction_id, $transaction_id1, 0, -9 ); // new order number.
		set_transient( 'redys_order_temp_' . $transaction_id2, $order_id, 3600 );
		return $transaction_id2;
	}
	/**
	 * Format the redsys amount.
	 *
	 * @param float $total The total amount.
	 * @return string The formatted amount.
	 */
	public function redsys_amount_format( $total ) {
		$order_total_sign = number_format( $total, 2, '', '' );
		return $order_total_sign;
	}
	/**
	 * Get the product description for the Redsys payment gateway.
	 *
	 * @param WC_Order $order The order object.
	 * @param string   $gateway The gateway ID.
	 * @return string The product description.
	 */
	public function product_description( $order, $gateway ) {
		if ( ! $this->is_redsys_order( $order->get_id() ) ) {
			return;
		}
		$product_id = '';
		$name       = '';
		$sku        = '';
		foreach ( $order->get_items() as $item ) {
			$product_id .= $item->get_product_id() . ', ';
			$name       .= $item->get_name() . ', ';
			$sku        .= get_post_meta( $item->get_product_id(), '_sku', true ) . ', ';
		}
		// Can be order, id, name or sku.
		$description_type = $this->get_redsys_option( 'descripredsys', $gateway );

		if ( 'id' === $description_type ) {
			$description = $product_id;
		} elseif ( 'name' === $description_type ) {
			$description = $name;
		} elseif ( 'sku' === $description_type ) {
			$description = $sku;
		} else {
			$description = __( 'Order', 'woo-redsys-gateway-light' ) . ' ' . $order->get_order_number();
		}
		return $description;
	}
	/**
	 * Get the PSD2 argument for the Redsys payment gateway.
	 *
	 * @param WC_Order $order The order object.
	 * @param string   $gateway The gateway ID.
	 * @return string The PSD2 argument.
	 */
	public function get_psd2_arg( $order, $gateway ) {
		if ( 'yes' === $this->get_redsys_option( 'psd2', $gateway ) ) {
			return $arg;
		} else {
			return '';
		}
	}
}
