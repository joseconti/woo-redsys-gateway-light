<?php
/**
 * Class PSD2
 *
 * @package WooCommerce Redsys Gateway WooCommerce.com > https://woocommerce.com/products/redsys-gateway/
 * @since 13.0.0
 * Copyright: (C) 2013 - 2021 José Conti
 **/

/**
 * Copyright: (C) 2013 - 2021 José Conti
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Gateway class
 */
/**
 * Copyright: (C) 2013 - 2021 José Conti
 */
class WC_Gateway_Redsys_PSD2_Light {
	/**
	 * Clean data
	 *
	 * @param string $out String to clean.
	 */
	public function clean_data( $out ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/

		$out = str_replace( 'Á', 'A', $out );
		$out = str_replace( 'À', 'A', $out );
		$out = str_replace( 'Ä', 'A', $out );
		$out = str_replace( 'É', 'E', $out );
		$out = str_replace( 'È', 'E', $out );
		$out = str_replace( 'Ë', 'E', $out );
		$out = str_replace( 'Í', 'I', $out );
		$out = str_replace( 'Ì', 'I', $out );
		$out = str_replace( 'Ï', 'I', $out );
		$out = str_replace( 'Ó', 'O', $out );
		$out = str_replace( 'Ò', 'O', $out );
		$out = str_replace( 'Ö', 'O', $out );
		$out = str_replace( 'Ú', 'U', $out );
		$out = str_replace( 'Ù', 'U', $out );
		$out = str_replace( 'Ü', 'U', $out );
		$out = str_replace( 'á', 'a', $out );
		$out = str_replace( 'à', 'a', $out );
		$out = str_replace( 'ä', 'a', $out );
		$out = str_replace( 'é', 'e', $out );
		$out = str_replace( 'è', 'e', $out );
		$out = str_replace( 'ë', 'e', $out );
		$out = str_replace( 'í', 'i', $out );
		$out = str_replace( 'ì', 'i', $out );
		$out = str_replace( 'ï', 'i', $out );
		$out = str_replace( 'ó', 'o', $out );
		$out = str_replace( 'ò', 'o', $out );
		$out = str_replace( 'ö', 'o', $out );
		$out = str_replace( 'ú', 'u', $out );
		$out = str_replace( 'ù', 'u', $out );
		$out = str_replace( 'ü', 'u', $out );
		$out = str_replace( 'Ñ', 'N', $out );
		$out = str_replace( 'ñ', 'n', $out );
		$out = str_replace( '&', '-', $out );
		$out = str_replace( '<', ' ', $out );
		$out = str_replace( '>', ' ', $out );
		$out = str_replace( '/', ' ', $out );
		$out = str_replace( '"', ' ', $out );
		$out = str_replace( "'", ' ', $out );
		$out = str_replace( '"', ' ', $out );
		$out = str_replace( '?', ' ', $out );
		$out = str_replace( '¿', ' ', $out );
		$out = str_replace( 'º', ' ', $out );
		$out = str_replace( 'ª', ' ', $out );
		$out = str_replace( '#', ' ', $out );
		$out = str_replace( '&', ' ', $out );
		$out = str_replace( '@', ' ', $out );

		return $out;
	}
	/**
	 * Get redsys option
	 *
	 * @param string $option Option.
	 * @param string $gateway Gateway ID.
	 */
	public function get_redsys_option( $option, $gateway ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
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
	 * Get Card Holder Name.
	 *
	 * @param obj $order Object WooCommerce Order.
	 */
	public function get_cardholdername( $order ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		// cardholderName.
	}

	/**
	 * Get email
	 *
	 * @param obj $order Object WooCommerce Order.
	 */
	public function get_email( $order ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		return $order->get_billing_email();
	}

	/**
	 * Shipname indicator
	 *
	 * @param obj $order Object WooCommerce Order.
	 */
	public function get_homephone( $order ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		return $order->get_billing_phone();
	}

	/**
	 * Get mobile phone
	 *
	 * @param obj $order Object WooCommerce Order.
	 */
	public function get_mobile_phone( $order ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		// mobilePhone.
	}

	/**
	 * Get Work
	 *
	 * @param obj $order Object WooCommerce Order.
	 */
	public function get_work( $order ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		return $order->get_billing_phone();
	}

	/**
	 * Get Adress Ship
	 *
	 * @param obj $order Object WooCommerce Order.
	 */
	public function get_adress_ship( $order ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$adress_ship                     = array();
		$adress_ship['shipAddrLine1']    = $order->get_billing_address_1();
		$adress_ship['shipAddrLine2']    = $order->get_billing_address_2();
		$adress_ship['shipAddrCity']     = $order->get_billing_city();
		$adress_ship['shipAddrState']    = strtolower( $order->get_billing_state() );
		$adress_ship['shipAddrPostCode'] = $order->get_billing_postcode();
		$adress_ship['shipAddrCountry']  = strtolower( $order->get_billing_country() );
		return $adress_ship;
	}

	/**
	 * Addr Match
	 *
	 * @param obj $order Object WooCommerce Order.
	 */
	public function addr_match( $order ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/

		$adress_ship_ship_addr_line1     = $order->get_billing_address_1();
		$adress_ship_ship_addr_line2     = $order->get_billing_address_2();
		$adress_ship_ship_addr_city      = $order->get_billing_city();
		$adress_ship_ship_addr_state     = strtolower( $order->get_billing_state() );
		$adress_ship_ship_addr_post_code = $order->get_billing_postcode();
		$adress_ship_ship_addr_country   = strtolower( $order->get_billing_country() );

		if ( $order->has_shipping_address() ) {
			$adress_bill_bill_addr_line1     = $order->get_shipping_address_1();
			$adress_bill_bill_addr_line2     = $order->get_shipping_address_2();
			$adress_bill_bill_addr_city      = $order->get_shipping_city();
			$adress_bill_bill_addr_post_code = $order->get_shipping_postcode();
			$adress_bill_bill_addr_state     = strtolower( $order->get_shipping_state() );
			$adress_bill_bill_addr_countr    = strtolower( $order->get_shipping_country() );
		} else {
			return 'Y';
		}

		if (
			$adress_ship_ship_addr_line1 === $adress_bill_bill_addr_line1 &&
			$adress_ship_ship_addr_line2 === $adress_bill_bill_addr_line2 &&
			$adress_ship_ship_addr_city === $adress_bill_bill_addr_city &&
			$adress_ship_ship_addr_state === $adress_bill_bill_addr_state &&
			$adress_ship_ship_addr_post_code === $adress_bill_bill_addr_post_code &&
			$adress_ship_ship_addr_country === $adress_bill_bill_addr_countr
		) {
			return 'Y';
		} else {
			return 'N';
		}
	}

	/**
	 * Get Challenge.
	 *
	 * @param obj $order Object WooCommerce Order.
	 */
	public function get_challenge_wwndow_size( $order ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		/**
		 * 01 = 250x 400
		 * 02 = 390x 400
		 * 03 = 500x 600
		 * 04 = 600x 400
		 * 05 = Pantalla completa (valor por defecto).
		 */

		$redsys = $this->get_redsys_option( 'windowssize', 'redsys' );

		if ( ! empty( $redsys ) ) {
			$windows_size = $redsys;
		} else {
			$windows_size = '05';
		}
		return $windows_size;
	}

	/**
	 * Get Acctid.
	 *
	 * @param obj $order Object WooCommerce Order.
	 */
	public function get_acctid( $order ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		// acctID.
	}
	/**
	 * Days
	 *
	 * @param string $start_time Time.
	 */
	public function days( $start_time ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/

		$current_time    = time();
		$unix_start_time = date( 'U', strtotime( $start_time ) ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
		$diff            = (int) abs( $current_time - $unix_start_time );

		// Now, we change seconds for days.
		if ( $diff >= DAY_IN_SECONDS ) {
			$days = round( $diff / DAY_IN_SECONDS );
		}
		return $days;
	}

	/**
	 * Get post num
	 *
	 * @param array $post_status Post Status Array.
	 * @param array $date_query Data Query.
	 */
	public function get_post_num( $post_status = array(), $date_query = array() ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/

		$args   = array(
			'customer_id'  => get_current_user_id(),
			'limit'        => -1, // to retrieve _all_ orders by this user
			'date_created' => $date_query,
			'status'       => $post_status,
		);
		$orders = wc_get_orders( $args );
		return $orders->total;
	}

	/**
	 * Get accept header
	 *
	 * @param int $order_id Order ID.
	 */
	public function get_accept_headers( $order_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		return WCRedL()->get_order_meta( $order_id, '_accept_haders', true );
	}

	/**
	 * Get Agente navegador
	 *
	 * @param int $order_id Order ID.
	 */
	public function get_agente_navegador( $order_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = WCRedL()->get_order_meta( $order_id, '_billing_agente_navegador_field', true );

		if ( $data ) {
			return $data;
		} else {
			return '';
		}
	}

	/**
	 * Get idioma navegador
	 *
	 * @param int $order_id Order ID.
	 */
	public function get_idioma_navegador( $order_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = WCRedL()->get_order_meta( $order_id, '_billing_idioma_navegador_field', true );

		if ( $data ) {
			return $data;
		} else {
			return '';
		}
	}

	/**
	 * Get altura pantalla
	 *
	 * @param int $order_id Order ID.
	 */
	public function get_altura_pantalla( $order_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = WCRedL()->get_order_meta( $order_id, '_billing_altura_pantalla_field', true );

		if ( $data ) {
			return $data;
		} else {
			return '0';
		}
	}

	/**
	 * Get anchura pantalla
	 *
	 * @param int $order_id Order ID.
	 */
	public function get_anchura_pantalla( $order_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = WCRedL()->get_order_meta( $order_id, '_billing_anchura_pantalla_field', true );

		if ( $data ) {
			return $data;
		} else {
			return '0';
		}
	}

	/**
	 * Get profundidad color
	 *
	 * @param int $order_id Order ID.
	 */
	public function get_profundidad_color( $order_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = WCRedL()->get_order_meta( $order_id, '_billing_profundidad_color_field', true );

		if ( $data ) {
			return $data;
		} else {
			return '1';
		}
	}

	/**
	 * Get diferencia horaria
	 *
	 * @param int $order_id Order ID.
	 */
	public function get_diferencia_horaria( $order_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = WCRedL()->get_order_meta( $order_id, '_billing_diferencia_horaria_field', true );

		if ( $data ) {
			return $data;
		} else {
			return '0';
		}
	}

	/**
	 * Get Browser Java Enabled
	 *
	 * @param int $order_id Order ID.
	 */
	public function get_browserjavaenabled( $order_id ) {
		$data = $this->get_idioma_navegador( $order_id );
		if ( '' !== $data ) {
			return '1';
		} else {
			return 'false';
		}
	}

	/**
	 * Get accept header
	 *
	 * @param int $user_id User ID.
	 */
	public function get_accept_headers_user( $user_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		return get_user_meta( $user_id, '_accept_haders', true );
	}

	/**
	 * Get Agente navegador
	 *
	 * @param int $user_id User ID.
	 */
	public function get_agente_navegador_user( $user_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = get_user_meta( $user_id, '_billing_agente_navegador_field', true );

		if ( $data ) {
			return $data;
		} else {
			return '';
		}
	}

	/**
	 * Get idioma navegador
	 *
	 * @param int $user_id User ID.
	 */
	public function get_idioma_navegador_user( $user_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = get_user_meta( $user_id, '_billing_idioma_navegador_field', true );

		if ( $data ) {
			return $data;
		} else {
			return '';
		}
	}

	/**
	 * Get altura pantalla usuario
	 *
	 * @param int $user_id User ID.
	 */
	public function get_altura_pantalla_user( $user_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = get_user_meta( $user_id, '_billing_altura_pantalla_field', true );

		if ( $data ) {
			return $data;
		} else {
			return '0';
		}
	}

	/**
	 * Get acgura pantlla usuario
	 *
	 * @param int $user_id User ID.
	 */
	public function get_anchura_pantalla_user( $user_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = get_user_meta( $user_id, '_billing_anchura_pantalla_field', true );

		if ( $data ) {
			return $data;
		} else {
			return '0';
		}
	}

	/**
	 * Get profundidad color usuario
	 *
	 * @param int $user_id User ID.
	 */
	public function get_profundidad_color_user( $user_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = get_user_meta( $user_id, '_billing_profundidad_color_field', true );

		if ( $data ) {
			return $data;
		} else {
			return '1';
		}
	}

	/**
	 * Get diferencia horaria
	 *
	 * @param int $user_id User ID.
	 */
	public function get_diferencia_horaria_user( $user_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = get_user_meta( $user_id, '_billing_diferencia_horaria_field', true );

		if ( $data ) {
			return $data;
		} else {
			return '0';
		}
	}

	/**
	 * Get browser Java
	 *
	 * @param int $user_id User ID.
	 */
	public function get_browserjavaenabled_user( $user_id ) {
		$data = $this->get_idioma_navegador_user( $user_id );
		if ( '' !== $data ) {
			return '1';
		} else {
			return 'false';
		}
	}
	/**
	 * Shipname indicator
	 *
	 * @param obj $order Object WooCommerce Order.
	 */
	public function shipnameindicator( $order ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/

		if ( $order->has_shipping_address() ) {
			$billing_first_name  = $order->get_billing_first_name();
			$billing_last_name   = $order->get_billing_last_name();
			$shipping_first_name = $order->get_shipping_first_name();
			$shipping_last_name  = $order->get_shipping_last_name();

			if (
				$billing_first_name === $shipping_first_name &&
				$billing_last_name === $shipping_last_name
			) {
				$shipnameindicator = '01';
			} else {
				$shipnameindicator = '02';
			}
		} else {
			$shipnameindicator = '01';
		}
		return $shipnameindicator;
	}

	/**
	 * Get Acct Info
	 *
	 * @param obj   $order Order Object.
	 * @param array $user_data_3ds User data 3DS array.
	 * @param int   $user_id User ID can be false.
	 */
	public function get_acctinfo( $order, $user_data_3ds = false, $user_id = false ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/

		// if ( $this->get_redsys_option( 'psd2', 'redsys' ) === 'yes' ) {

			/**
			 * 1569057946
			 * 01 = Sin cuenta (invitado)
			 * 02 = Recién creada
			 * 03 = Menos de 30 días
			 * 04 = Entre 30 y 60días
			 * 05 = Más de 60 días
			 */
		if ( is_user_logged_in() || $user_id ) {

			if ( is_user_logged_in() ) {
				$user_id = get_current_user_id();
			} else {
				$user_id = $user_id;
			}
			$usr_data         = get_userdata( $user_id );
			$usr_registered   = $usr_data->user_registered;
			$dt               = new DateTime( $usr_registered );
			$usr_registered   = $dt->format( 'Ymd' );
			$last_update      = get_user_meta( $user_id, 'last_update', true );
			$minu_registered  = intval( ( strtotime( 'now' ) - strtotime( $usr_registered ) ) / 60 );
			$days_registered  = intval( $minu_registered / 1440 );
			$account_modified = intval( ( ( strtotime( 'now' ) - $last_update ) ) / DAY_IN_SECONDS );

			if ( $minu_registered < 20 ) {
				$ch_acc_age_ind = '02';
			} elseif ( $days_registered < 30 ) {
				$ch_acc_age_ind = '03';
			} elseif ( $days_registered >= 30 && $days_registered <= 60 ) {
				$ch_acc_age_ind = '04';
			} else {
				$ch_acc_age_ind = '05';
			}

			$customer         = new WC_Customer( $user_id );
			$dt               = new DateTime( $customer->get_date_modified() );
			$ch_acc_change    = $dt->format( 'Ymd' );
			$account_modified = intval( ( strtotime( 'now' ) - strtotime( $customer->get_date_modified() ) ) / 60 );
			$n_days           = intval( $account_modified / 1440 );

			if ( $account_modified < 20 ) {
				$ch_acc_change_ind = '01';
			} elseif ( $n_days < 30 ) {
				$ch_acc_change_ind = '02';
			} elseif ( $n_days >= 30 && $n_days <= 60 ) {
				$ch_acc_change_ind = '03';
			} else {
				$ch_acc_change_ind = '04';
			}

			$nb_purchase_account = $this->get_post_num( array( 'wc-completed' ), '>' . ( time() - 6 * MONTH_IN_SECONDS ) );
			$txn_activity_day    = $this->get_post_num( array( 'wc-completed', 'wc-pending' ), '>' . ( time() - DAY_IN_SECONDS ) );
			$txn_activity_year   = $this->get_post_num( array( 'wc-completed', 'wc-pending' ), '>' . ( time() - YEAR_IN_SECONDS ) );

			if ( $order->has_shipping_address() ) {
				$args   = array(
					'shipping_address_1' => $order->get_shipping_address_1(),
					'shipping_address_2' => $order->get_shipping_address_2(),
					'shipping_city'      => $order->get_shipping_city(),
					'shipping_postcode'  => $order->get_shipping_postcode(),
					'shipping_country'   => $order->get_shipping_country(),
					'order'              => 'ASC',
				);
				$orders = wc_get_orders( $args );

				if ( $orders->total > 0 ) {
					$order_data         = $orders->orders[0]->get_data();
					$ship_address_usage = $order_data['date_created']->date( 'Ymd' );
					$days               = intval( ( ( strtotime( 'now' ) - strtotime( $orders->orders[0]->get_date_created() ) ) / MINUTE_IN_SECONDS ) / HOUR_IN_SECONDS );
					if ( $days < 30 ) {
						$ship_address_usage_ind = '02';
					} elseif ( $days >= 30 && $days <= 60 ) {
						$ship_address_usage_ind = '03';
					} else {
						$ship_address_usage_ind = '04';
					}
				} else {
					$ship_address_usage     = date( 'Ymd' ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
					$ship_address_usage_ind = '01';
				}
			}
		} else {
			$ch_acc_age_ind = '01';
		}

		$acct_info = array(
			'chAccAgeInd' => $ch_acc_age_ind,
		);
		if ( $order->has_shipping_address() ) {
				$acct_info['shipAddressUsage']    = $ship_address_usage;
				$acct_info['shipAddressUsageInd'] = $ship_address_usage_ind;
		}
		if ( is_user_logged_in() ) {
			$acct_info['chAccDate']         = $usr_registered;
			$acct_info['chAccChange']       = $ch_acc_change;
			$acct_info['chAccChangeInd']    = $ch_acc_change_ind;
			$acct_info['nbPurchaseAccount'] = (string) $nb_purchase_account;
			$acct_info['txnActivityDay']    = (string) $txn_activity_day;
			$acct_info['txnActivityYear']   = (string) $txn_activity_year;
		}

			$ds_merchant_emv3ds = array();
		if ( $user_data_3ds ) {
			foreach ( $user_data_3ds as $data => $valor ) {
				$ds_merchant_emv3ds[ $data ] = $valor;
			}
		}
			$ds_merchant_emv3ds['addrMatch']        = $this->addr_match( $order );
			$ds_merchant_emv3ds['billAddrCity']     = $this->clean_data( $order->get_billing_city() );
			$ds_merchant_emv3ds['billAddrLine1']    = $this->clean_data( $order->get_billing_address_1() );
			$ds_merchant_emv3ds['billAddrPostCode'] = $this->clean_data( $order->get_billing_postcode() );
			$ds_merchant_emv3ds['billAddrState']    = strtolower( $this->clean_data( $order->get_billing_state() ) );
			$ds_merchant_emv3ds['billAddrCountry']  = strtolower( $this->clean_data( $order->get_billing_country() ) );
			$ds_merchant_emv3ds['Email']            = $this->get_email( $order );
			$ds_merchant_emv3ds['acctInfo']         = $acct_info;
			$ds_merchant_emv3ds['homePhone']        = array( 'subscriber' => $this->get_homephone( $order ) );

			/**
			 * TO-DO: suspiciousAccActivity, en una futura versión añadiré un meta a los usuarios para que el admistrador pueda marcar alguna cuenta fraudulenta o que ha habido algún problema.
			 */

		if ( $order->get_shipping_address_2() !== '' ) {
			$ds_merchant_emv3ds['billAddrLine2'] = $this->clean_data( $order->get_shipping_address_2() );
		}
		if ( $order->has_shipping_address() ) {
			$ds_merchant_emv3ds['shipAddrCity']     = $this->clean_data( $order->get_shipping_city() );
			$ds_merchant_emv3ds['shipAddrLine1']    = $this->clean_data( $order->get_shipping_address_1() );
			$ds_merchant_emv3ds['shipAddrPostCode'] = $this->clean_data( $order->get_shipping_postcode() );
			$ds_merchant_emv3ds['shipAddrState']    = strtolower( $this->clean_data( $order->get_shipping_state() ) );
			$ds_merchant_emv3ds['shipAddrCountry']  = strtolower( $this->clean_data( $order->get_shipping_country() ) );
			if ( $order->get_shipping_address_2() !== '' ) {
				$ds_merchant_emv3ds['shipAddrLine2'] = $this->clean_data( $order->get_shipping_address_2() );
			}
		}
		$ds_merchant_emv3ds = wp_json_encode( $ds_merchant_emv3ds );
		return $ds_merchant_emv3ds;
	}
}
