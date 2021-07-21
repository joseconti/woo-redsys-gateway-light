<?php
/**
* Copyright: (C) 2013 - 2021 José Conti
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
* Copyright: (C) 2013 - 2021 José Conti
*/
class WC_Gateway_Bizum_Redsys extends WC_Payment_Gateway {
	var $notify_url;
	
	/**
	* Constructor for the gateway.
	*
	* @access public
	* @return void
	*/
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	public function __construct() {
		
		$this->id                   = 'bizumredsys';
		$this->icon                 = apply_filters( 'woocommerce_' . $this->id . '_icon', REDSYS_PLUGIN_URL . 'assets/images/bizum.png' );
		$this->has_fields           = false;
		$this->liveurl              = 'https://sis.redsys.es/sis/realizarPago';
		$this->testurl              = 'https://sis-t.redsys.es:25443/sis/realizarPago';
		$this->liveurlws            = 'https://sis.redsys.es/sis/services/SerClsWSEntrada?wsdl';
		$this->testurlws            = 'https://sis-t.redsys.es:25443/sis/services/SerClsWSEntrada?wsdl';
		$this->testsha256           = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7';
		$this->testmode             = $this->get_option( 'testmode' );
		$this->method_title         = __( 'Bizum Lite (by José Conti)', 'woo-redsys-gateway-light' );
		$this->method_description   = __( 'Bizum Lite  works redirecting customers to Bizum.', 'woo-redsys-gateway-light' );
		$this->not_use_https        = $this->get_option( 'not_use_https' );
		$this->notify_url           = add_query_arg( 'wc-api', 'WC_Gateway_' . $this->id, home_url( '/' ) );
		$this->notify_url_not_https = str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'WC_Gateway_' . $this->id, home_url( '/' ) ) );
		// Load the settings
		$this->init_form_fields();
		$this->init_settings();
		// Define user set variables
		$this->title                = $this->get_option( 'title' );
		$this->description          = $this->get_option( 'description' );
		$this->customer             = $this->get_option( 'customer' );
		$this->transactionlimit     = $this->get_option( 'transactionlimit' );
		$this->commercename         = $this->get_option( 'commercename' );
		$this->terminal             = $this->get_option( 'terminal' );
		$this->secretsha256         = $this->get_option( 'secretsha256' );
		$this->customtestsha256     = $this->get_option( 'customtestsha256' );
		$this->redsyslanguage       = $this->get_option( 'redsyslanguage' );
		$this->debug                = $this->get_option( 'debug' );
		$this->testforuser          = $this->get_option( 'testforuser' );
		$this->testforuserid        = $this->get_option( 'testforuserid' );
		$this->buttoncheckout       = $this->get_option( 'buttoncheckout' );
		$this->butonbgcolor         = $this->get_option( 'butonbgcolor' );
		$this->butontextcolor       = $this->get_option( 'butontextcolor' );
		$this->log                  = new WC_Logger();
		$this->supports             = array(
			'products',
			'refunds',
		);
		// Actions
		add_action( 'valid-' . $this->id . '-standard-ipn-request', array( $this, 'successful_request' ) );
		add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'warning_checkout_test_mode_bizum' ) );
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'disable_bizum' ) );
	
		// Payment listener/API hook
		add_action( 'woocommerce_api_wc_gateway_' . $this->id, array( $this, 'check_ipn_response' ) );
		
		if ( ! $this->is_valid_for_use() ) {
			$this->enabled = false;
		}
	}
	
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	function is_valid_for_use() {
		if ( ! in_array( get_woocommerce_currency(), WCRedL()->allowed_currencies(), true ) ) {
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * Admin Panel Options
	 *
	 * @since 6.0.0
	 */
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	public function admin_options() {
		?>
		<h3><?php esc_html_e( 'Bizum', 'woo-redsys-gateway-light' ); ?></h3>
		<p><?php esc_html_e( 'Bizum works by sending the user to Bizum Gateway', 'woo-redsys-gateway-light' ); ?></p>
		<div class="redsysnotice">
				<span class="dashicons dashicons-welcome-learn-more redsysnotice-dash"></span>
				<span class="redsysnotice__content"><?php printf( __( 'check <a href="%1$s" target="_blank" rel="noopener">FAQ page</a> for working problems, or open a <a href="%2$s" target="_blank" rel="noopener">thread on WordPress.org</a> for support. Please, add a <a href="%3$s" target="_blank" rel="noopener">review on WordPress.org</a>', 'woo-redsys-gateway-light' ), 'https://www.joseconti.com/faq-plugin-redsys-woocommerce-com/', 'https://wordpress.org/support/plugin/woo-redsys-gateway-light/', 'https://wordpress.org/support/plugin/woo-redsys-gateway-light/reviews/?rate=5#new-post' ); ?><span>
			</div>
		<?php if ( class_exists( 'SitePress' ) ) { ?>
			<div class="updated fade"><h4><?php esc_html_e( 'Attention! WPML detected.', 'woo-redsys-gateway-light' ); ?></h4>
				<p><?php esc_html_e( 'The Gateway will be shown in the customer language. The option "Language Gateway" is not taken into consideration', 'woo-redsys-gateway-light' ); ?></p>
			</div>
		<?php } ?>
		<?php if ( $this->is_valid_for_use() ) : ?>
			<table class="form-table">
				<?php
				// Generate the HTML For the settings form.
				$this->generate_settings_html();
				?>
			</table><!--/.form-table-->
		<?php else :
			$currencies = WCRedL()->allowed_currencies();
			$formated_currencies = '';

			foreach ( $currencies as $currency ) {
				$formated_currencies .= $currency . ', ';
			}
		?>
	<div class="inline error"><p><strong><?php esc_html_e( 'Gateway Disabled', 'woo-redsys-gateway-light' ); ?></strong>: <?php esc_html_e( 'Servired/RedSys only support ', 'woo-redsys-gateway-light' );
		echo esc_html( $formated_currencies ); ?></p></div>
			<?php
		endif;
	}
	
	/**
	 * Initialise Gateway Settings Form Fields
	 *
	 * @access public
	 * @return void
	 */
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	function init_form_fields() {
		
		$options    = array();
		$selections = (array)$this->get_option( 'testforuserid' );

		if ( count( $selections ) !== 0 ) {
			foreach ( $selections as $user_id ) {
				if ( ! empty( $user_id ) ) {
					$user_data  = get_userdata( $user_id );
					$user_email = $user_data->user_email;
					if ( ! empty( esc_html( $user_email ) ) ) {
						$options[ esc_html( $user_id ) ] = esc_html( $user_email );
					}
				}
			}
		}
		
		$this->form_fields = array(
			'enabled'              => array(
				'title'   => __( 'Enable/Disable', 'woo-redsys-gateway-light' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Bizum', 'woo-redsys-gateway-light' ),
				'default' => 'no',
			),
			'title'            => array(
				'title'       => __( 'Title', 'woo-redsys-gateway-light' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woo-redsys-gateway-light' ),
				'default'     => __( 'Bizum', 'woo-redsys-gateway-light' ),
				'desc_tip'    => true,
			),
			'description'      => array(
				'title'       => __( 'Description', 'woo-redsys-gateway-light' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the description which the user sees during checkout.', 'woo-redsys-gateway-light' ),
				'default'     => __( 'Pay via Bizum you can pay with your Bizum account.', 'woo-redsys-gateway-light' ),
			),
			'customer'         => array(
				'title'       => __( 'Commerce number (FUC)', 'woo-redsys-gateway-light' ),
				'type'        => 'text',
				'description' => __( 'Commerce number (FUC) provided by your bank.', 'woo-redsys-gateway-light' ),
				'desc_tip'    => true,
			),
			'commercename'     => array(
				'title'       => __( 'Commerce Name', 'woo-redsys-gateway-light' ),
				'type'        => 'text',
				'description' => __( 'Commerce Name', 'woo-redsys-gateway-light' ),
				'desc_tip'    => true,
			),
			'terminal'         => array(
				'title'       => __( 'Terminal number', 'woo-redsys-gateway-light' ),
				'type'        => 'text',
				'description' => __( 'Terminal number provided by your bank.', 'woo-redsys-gateway-light' ),
				'desc_tip'    => true,
			),
			'transactionlimit' => array(
				'title'       => __( 'Transaction Limit', 'woo-redsys-gateway-light' ),
				'type'        => 'text',
				'description' => __( 'Maximum transaction price for the cart.', 'woo-redsys-gateway-light' ),
				'desc_tip'    => true,
			),
			'not_use_https'    => array(
				'title'       => __( 'HTTPS SNI Compatibility', 'woo-redsys-gateway-light' ),
				'type'        => 'checkbox',
				'label'       => __( 'Activate SNI Compatibility.', 'woo-redsys-gateway-light' ),
				'default'     => 'no',
				'description' => sprintf( __( 'If you are using HTTPS and Redsys don\'t support your certificate, example Lets Encrypt, you can deactivate HTTPS notifications. WARNING: If you are forcing redirection to HTTPS with htaccess, you need to add an exception for notification URL', 'woo-redsys-gateway-light' ) ),
			),
			'secretsha256'     => array(
				'title'       => __( 'Encryption secret passphrase SHA-256', 'woo-redsys-gateway-light' ),
				'type'        => 'text',
				'description' => __( 'Encryption secret passphrase SHA-256 provided by your bank.', 'woo-redsys-gateway-light' ),
				'desc_tip'    => true,
			),
			'customtestsha256' => array(
				'title'       => __( 'TEST MODE: Encryption secret passphrase SHA-256', 'woo-redsys-gateway-light' ),
				'type'        => 'text',
				'description' => __( 'Encryption secret passphrase SHA-256 provided by your bank for test mode.', 'woo-redsys-gateway-light' ),
				'desc_tip'    => true,
			),
			'redsyslanguage'   => array(
				'title'       => __( 'Language Gateway', 'woo-redsys-gateway-light' ),
				'type'        => 'select',
				'description' => __( 'Choose the language for the Gateway. Not all Banks accept all languages', 'woo-redsys-gateway-light' ),
				'default'     => '001',
				'options'     => array(),
			),
			'testmode'         => array(
				'title'       => __( 'Running in test mode', 'woo-redsys-gateway-light' ),
				'type'        => 'checkbox',
				'label'       => __( 'Running in test mode', 'woo-redsys-gateway-light' ),
				'default'     => 'yes',
				'description' => sprintf( __( 'Select this option for the initial testing required by your bank, deselect this option once you pass the required test phase and your production environment is active.', 'woo-redsys-gateway-light' ) ),
			),
			'debug'            => array(
				'title'       => __( 'Debug Log', 'woo-redsys-gateway-light' ),
				'type'        => 'checkbox',
				'label'       => __( 'Running in test mode', 'woo-redsys-gateway-light' ),
				'label'       => __( 'Enable logging', 'woo-redsys-gateway-light' ),
				'default'     => 'no',
				'description' => __( 'Log Bizum events, such as notifications requests, inside <code>WooCommerce > Status > Logs > bizum-{date}-{number}.log</code>', 'woo-redsys-gateway-light' ),
			),
		);
		$redsyslanguages = WCRedL()->get_redsys_languages();
		
		foreach( $redsyslanguages as $redsyslanguage => $valor ) {
			$this->form_fields['redsyslanguage']['options'][$redsyslanguage] = $valor;
		}
	}

	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	function check_user_test_mode( $userid ) {

		$usertest_active = $this->testforuser;
		$selections = (array)$this->get_option( 'testforuserid' );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', '/****************************/' );
			$this->log->add( 'bizumredsys', '     Checking user test       ' );
			$this->log->add( 'bizumredsys', '/****************************/' );
			$this->log->add( 'bizumredsys', ' ' );
		}
		
		if ( 'yes' === $usertest_active ) {
		
			if ( ! empty( $selections ) ) {
				foreach ( $selections as $user_id ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'bizumredsys', ' ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', '   Checking user ' . $userid    );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', ' ' );
						$this->log->add( 'bizumredsys', ' ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', '  User in forach ' . $user_id );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', ' ' );
					}
					if ( (string) $user_id === (string) $userid ) {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'bizumredsys', ' ' );
							$this->log->add( 'bizumredsys', '/****************************/' );
							$this->log->add( 'bizumredsys', '   Checking user test TRUE    ' );
							$this->log->add( 'bizumredsys', '/****************************/' );
							$this->log->add( 'bizumredsys', ' ' );
							$this->log->add( 'bizumredsys', ' ' );
							$this->log->add( 'bizumredsys', '/********************************************/' );
							$this->log->add( 'bizumredsys', '  User ' . $userid . ' is equal to ' . $user_id );
							$this->log->add( 'bizumredsys', '/********************************************/' );
							$this->log->add( 'bizumredsys', ' ' );
						}
						return true;
					}
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'bizumredsys', ' ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', '  Checking user test continue ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', ' ' );
					}
					continue;
				}
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', '  Checking user test FALSE    ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', ' ' );
				}
				return false;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', '  Checking user test FALSE    ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', ' ' );
				}
				return false;
			}
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '/****************************/' );
				$this->log->add( 'bizumredsys', '     User test Disabled.      ' );
				$this->log->add( 'bizumredsys', '/****************************/' );
				$this->log->add( 'bizumredsys', ' ' );
			}
			return false;
		}
	}
	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function disable_bizum( $available_gateways ) {

		if ( ! is_admin() ) {
			$total  = (int) WC()->cart->total;
			$limit  = (int) $this->transactionlimit;
			if ( ! empty( $limit ) && $limit > 0 ) {
				$result = $limit - $total;
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '$total: ' . $total );
					$this->log->add( 'bizumredsys', '$limit: ' . $limit );
					$this->log->add( 'bizumredsys', '$result: ' . $result );
					$this->log->add( 'bizumredsys', ' ' );
				}
				if ( $result > 0 ) {
					return $available_gateways;
				} else {
					unset( $available_gateways['bizumredsys'] );
				}
			}
		}
		return $available_gateways;
	}
	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_redsys_url_gateway( $user_id, $type = 'rd' ) {

		if ( 'yes' === $this->testmode ) {
			if ( 'rd' === $type ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', '          URL Test RD         ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', ' ' );
				}
				$url = $this->testurl;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', '          URL Test WS         ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', ' ' );
				}
				$url = $this->testurlws;
			}
		} else {
			$user_test = $this->check_user_test_mode( $user_id );
			if ( $user_test ) {
				if ( 'rd' === $type ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'bizumredsys', ' ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', '          URL Test RD         ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', ' ' );
					}
					$url = $this->testurl;
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'bizumredsys', ' ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', '          URL Test WS         ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', ' ' );
					}
					$url = $this->testurlws;
				}
			} else {
				if ( 'rd' === $type ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'bizumredsys', ' ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', '          URL Live RD         ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', ' ' );
					}
					$url = $this->liveurl;
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'bizumredsys', ' ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', '          URL Live WS         ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', ' ' );
					}
					$url = $this->liveurlws;
				}
			}
		}
		return $url;
	}

	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_redsys_sha256( $user_id ) {

		if ( 'yes' === $this->testmode ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '/****************************/' );
				$this->log->add( 'bizumredsys', '         SHA256 Test.         ' );
				$this->log->add( 'bizumredsys', '/****************************/' );
				$this->log->add( 'bizumredsys', ' ' );
			}
			$customtestsha256 = utf8_decode( $this->customtestsha256 );
			if ( ! empty( $customtestsha256 ) ) {
				$sha256 = $customtestsha256;
			} else {
				$sha256 = utf8_decode( $this->testsha256 );
			}
		} else {
			$user_test = $this->check_user_test_mode( $user_id );
			if ( $user_test ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', '      USER SHA256 Test.       ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', ' ' );
				}
				$customtestsha256 = utf8_decode( $this->customtestsha256 );
				if ( ! empty( $customtestsha256 ) ) {
					$sha256 = $customtestsha256;
				} else {
					$sha256 = utf8_decode( $this->testsha256 );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', '     USER SHA256 NOT Test.    ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', ' ' );
				}
				$sha256 = utf8_decode( $this->secretsha256 );
			}
		}
		return $sha256;
	}

	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_redsys_args( $order ) {

		$order_id         = $order->get_id();
		$currency_codes   = WCRedL()->get_currencies();
		$transaction_id2  = WCRedL()->prepare_order_number( $order_id );
		$order_total_sign = WCRedL()->redsys_amount_format( $order->get_total() );
		$transaction_type = '0';
		$user_id          = $order->get_user_id();
		$secretsha256     = $this->get_redsys_sha256( $user_id );
		if ( class_exists( 'SitePress' ) ) {
			$gatewaylanguage = WCRedL()->get_lang_code( ICL_LANGUAGE_CODE );
		} elseif ( $this->redsyslanguage ) {
			$gatewaylanguage = $this->redsyslanguage;
		} else {
			$gatewaylanguage = '001';
		}
		$returnfromredsys   = $order->get_cancel_order_url();
		$dsmerchantterminal = $this->terminal;
		if ( 'yes' === $this->not_use_https ) {
				$final_notify_url = $this->notify_url_not_https;
		} else {
			$final_notify_url = $this->notify_url;
		}
		$nombr_apellidos    = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
		// redsys Args.
		$miobj = new RedsysAPI();
		$miobj->setParameter( 'DS_MERCHANT_AMOUNT', $order_total_sign );
		$miobj->setParameter( 'DS_MERCHANT_ORDER', $transaction_id2 );
		$miobj->setParameter( 'DS_MERCHANT_MERCHANTCODE', $this->customer );
		$miobj->setParameter( 'DS_MERCHANT_CURRENCY', $currency_codes[ get_woocommerce_currency() ] );
		$miobj->setParameter( 'DS_MERCHANT_TRANSACTIONTYPE', $transaction_type );
		$miobj->setParameter( 'DS_MERCHANT_TERMINAL', $dsmerchantterminal );
		$miobj->setParameter( 'DS_MERCHANT_MERCHANTURL', $final_notify_url );
		$miobj->setParameter( 'DS_MERCHANT_TITULAR', $nombr_apellidos );
		$miobj->setParameter( 'DS_MERCHANT_URLOK', add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
		$miobj->setParameter( 'DS_MERCHANT_URLKO', $returnfromredsys );
		$miobj->setParameter( 'DS_MERCHANT_CONSUMERLANGUAGE', $gatewaylanguage );
		$miobj->setParameter( 'DS_MERCHANT_PRODUCTDESCRIPTION', WCRedL()->product_description( $order, $this->id ) );
		$miobj->setParameter( 'DS_MERCHANT_MERCHANTNAME', $this->commercename );
		$miobj->setParameter( 'DS_MERCHANT_PAYMETHODS', 'z' );

		$version      = 'HMAC_SHA256_V1';
		// Se generan los parámetros de la petición.
		$request      = '';
		$params       = $miobj->createMerchantParameters();
		$signature    = $miobj->createMerchantSignature( $secretsha256 );
		$order_id_set = $transaction_id2;
		set_transient( 'redsys_signature_' . sanitize_title( $order_id_set ), $secretsha256, 600 );
		$redsys_args = array(
			'Ds_SignatureVersion'   => $version,
			'Ds_MerchantParameters' => $params,
			'Ds_Signature'          => $signature,
		);
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', 'Generating payment form for order ' . $order->get_order_number() . '. Sent data: ' . print_r( $redsys_args, true ) );
			$this->log->add( 'bizumredsys', 'Helping to understand the encrypted code: ' );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_AMOUNT: ' . $order_total_sign );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_ORDER: ' . $transaction_id2 );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_MERCHANTCODE: ' . $this->customer );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_CURRENCY' . $currency_codes[ get_woocommerce_currency() ] );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_TRANSACTIONTYPE: ' . $transaction_type );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_TERMINAL: ' . $dsmerchantterminal );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_MERCHANTURL: ' . $final_notify_url );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_URLOK: ' . add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_URLKO: ' . $returnfromredsys );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_CONSUMERLANGUAGE: ' . $gatewaylanguage );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_PRODUCTDESCRIPTION: ' . WCRedL()->product_description( $order, $this->id ) );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_PAYMETHODS: z' );
		}
		$redsys_args = apply_filters( 'woocommerce_redsys_args', $redsys_args );
		return $redsys_args;
	}

	/**
	 * Generate the redsys form
	 *
	 * @param mixed $order_id
	 * @return string
	 */
	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function generate_redsys_form( $order_id ) {
		global $woocommerce;

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', '/****************************/' );
			$this->log->add( 'bizumredsys', '   Generating Redsys Form     ' );
			$this->log->add( 'bizumredsys', '/****************************/' );
			$this->log->add( 'bizumredsys', ' ' );
		}

		$order           = WCRedL()->get_order( $order_id );
		$user_id         = $order->get_user_id();
		$usesecretsha256 = $this->get_redsys_sha256( $user_id );
		$redsys_adr      = $this->get_redsys_url_gateway( $user_id );
		$redsys_args     = $this->get_redsys_args( $order );
		$form_inputs     = array();

		foreach ( $redsys_args as $key => $value ) {
			$form_inputs[] .= '<input type="hidden" name="' . $key . '" value="' . esc_attr( $value ) . '" />';
		}
		wc_enqueue_js(
			'$("body").block({
			message: "<img src=\"' . esc_url( apply_filters( 'woocommerce_ajax_loader_url', $woocommerce->plugin_url() . '/assets/images/select2-spinner.gif' ) ) . '\" alt=\"Redirecting&hellip;\" style=\"float:left; margin-right: 10px;\" />' . __( 'Thank you for your order. We are now redirecting you to Bizum to make the payment.', 'woo-redsys-gateway-light' ) . '",
			overlayCSS:
			{
				background: "#fff",
				opacity: 0.6
			},
			css: {
				padding:		20,
				textAlign:		"center",
				color:			"#555",
				border:			"3px solid #aaa",
				backgroundColor:"#fff",
				cursor:			"wait",
				lineHeight:		"32px"
			}
		});
		jQuery("#submit_redsys_payment_form").click();'
		);
		return '<form action="' . esc_url( $redsys_adr ) . '" method="post" id="redsys_payment_form" target="_top">
		' . implode( '', $form_inputs ) . '
		<input type="submit" class="button-alt" id="submit_redsys_payment_form" value="' . __( 'Pay with Bizum', 'woo-redsys-gateway-light' ) . '" /> <a class="button cancel" href="' . esc_url( $order->get_cancel_order_url() ) . '">' . __( 'Cancel order &amp; restore cart', 'woo-redsys-gateway-light' ) . '</a>
	</form>';
	}

	/**
	 * Process the payment and return the result
	 *
	 * @param int $order_id
	 * @return array
	 */
	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function process_payment( $order_id ) {
		$order = WCRedL()->get_order( $order_id );
		return array(
			'result'   => 'success',
			'redirect' => $order->get_checkout_payment_url( true ),
		);
	}

	/**
	 * Output for the order received page.
	 *
	 * @return void
	 */
	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function receipt_page( $order ) {
		echo '<p>' . esc_html__( 'Thank you for your order, please click the button below to pay with Bizum.', 'woo-redsys-gateway-light' ) . '</p>';
		echo $this->generate_redsys_form( $order );
	}

	/**
	 * Check redsys IPN validity
	 **/
	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function check_ipn_request_is_valid() {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', 'HTTP Notification received: ' . print_r( $_POST, true ) );
		}
		$usesecretsha256 = $this->secretsha256;
		if ( $usesecretsha256 ) {
			$version           = $_POST['Ds_SignatureVersion'];
			$data              = $_POST['Ds_MerchantParameters'];
			$remote_sign       = $_POST['Ds_Signature'];
			$miObj             = new RedsysAPI();
			$decodec           = $miObj->decodeMerchantParameters( $data );
			$order_id          = $miObj->getParameter( 'Ds_Order' );
			$secretsha256      = get_transient( 'redsys_signature_' . sanitize_title( $order_id ) );
			$order1            = $order_id;
			$order2            = WCRedL()->clean_order_number( $order1 );
			$secretsha256_meta = get_post_meta( $order2, '_redsys_secretsha256', true );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', 'Signature from Redsys: ' . $remote_sign );
				$this->log->add( 'bizumredsys', 'Name transient remote: redsys_signature_' . sanitize_title( $order_id ) );
				$this->log->add( 'bizumredsys', 'Secret SHA256 transcient: ' . $secretsha256 );
				$this->log->add( 'bizumredsys', ' ' );
			}

			if ( 'yes' === $this->debug ) {
				$order_id = $miObj->getParameter( 'Ds_Order' );
				$this->log->add( 'bizumredsys', 'Order ID: ' . $order_id );
			}
			$order           = WCRedL()->get_order( $order2 );
			$user_id         = $order->get_user_id();
			$usesecretsha256 = $this->get_redsys_sha256( $user_id );
			if ( empty( $secretsha256 ) && ! $secretsha256_meta ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', 'Using $usesecretsha256 Settings' );
					$this->log->add( 'bizumredsys', 'Secret SHA256 Settings: ' . $usesecretsha256 );
					$this->log->add( 'bizumredsys', ' ' );
				}
				$usesecretsha256 = $usesecretsha256;
			} elseif ( $secretsha256_meta ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', 'Using $secretsha256_meta Meta' );
					$this->log->add( 'bizumredsys', 'Secret SHA256 Meta: ' . $secretsha256_meta );
					$this->log->add( 'bizumredsys', ' ' );
				}
				$usesecretsha256 = $secretsha256_meta;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', 'Using $secretsha256 Transcient' );
					$this->log->add( 'bizumredsys', 'Secret SHA256 Transcient: ' . $secretsha256 );
					$this->log->add( 'bizumredsys', ' ' );
				}
				$usesecretsha256 = $secretsha256;
			}
			$localsecret     = $miObj->createMerchantSignatureNotif( $usesecretsha256, $data );
			if ( $localsecret === $remote_sign ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', 'Received valid notification from Servired/RedSys' );
					$this->log->add( 'bizumredsys', $data );
				}
				return true;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', 'Received INVALID notification from Servired/RedSys' );
				}
				delete_transient( 'redsys_signature_' . sanitize_title( $order_id ) );
				return false;
			}
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', 'HTTP Notification received: ' . print_r( $_POST, true ) );
			}
			if ( $_POST['Ds_MerchantCode'] === $this->customer ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', 'Received valid notification from Servired/RedSys' );
				}
				return true;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', 'Received INVALID notification from Servired/RedSys' );
					$this->log->add( 'bizumredsys', '$remote_sign: ' . $remote_sign );
					$this->log->add( 'bizumredsys', '$localsecret: ' . $localsecret );
				}
				return false;
			}
		}
	}

	/**
	 * Check for Bizum HTTP Notification
	 * 
	 * @return void
	 */
	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function check_ipn_response() {
		@ob_clean();
		$_POST = stripslashes_deep( $_POST );
		if ( $this->check_ipn_request_is_valid() ) {
			header( 'HTTP/1.1 200 OK' );
			do_action( 'valid-' . $this->id . '-standard-ipn-request', $_POST );
		} else {
			wp_die( 'Bizum Notification Request Failure' );
		}
	}
	
	/**
	 * Successful Payment!
	 *
	 * @access public
	 * @param array $posted
	 * @return void
	 */
	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function successful_request( $posted ) {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', '/****************************/' );
			$this->log->add( 'bizumredsys', '      successful_request      ' );
			$this->log->add( 'bizumredsys', '/****************************/' );
			$this->log->add( 'bizumredsys', ' ' );
		}

		$version           = $_POST['Ds_SignatureVersion'];
		$data              = $_POST['Ds_MerchantParameters'];
		$remote_sign       = $_POST['Ds_Signature'];

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', '$version: ' . $version );
			$this->log->add( 'bizumredsys', '$data: ' . $data );
			$this->log->add( 'bizumredsys', '$remote_sign: ' . $remote_sign );
			$this->log->add( 'bizumredsys', ' ' );
		}

		$miObj             = new RedsysAPI();
		$usesecretsha256   = $this->secretsha256;
		$dscardnumbercompl = '';
		$dsexpiration      = '';
		$dsmerchantidenti  = '';
		$dscardnumber4     = '';
		$dsexpiryyear      = '';
		$dsexpirymonth     = '';
		$decodedata        = $miObj->decodeMerchantParameters( $data );
		$localsecret       = $miObj->createMerchantSignatureNotif( $usesecretsha256, $data );
		$total             = $miObj->getParameter( 'Ds_Amount' );
		$ordermi           = $miObj->getParameter( 'Ds_Order' );
		$dscode            = $miObj->getParameter( 'Ds_MerchantCode' );
		$currency_code     = $miObj->getParameter( 'Ds_Currency' );
		$response          = $miObj->getParameter( 'Ds_Response' );
		$id_trans          = $miObj->getParameter( 'Ds_AuthorisationCode' );
		$dsdate            = htmlspecialchars_decode( $miObj->getParameter( 'Ds_Date' ) );
		$dshour            = htmlspecialchars_decode( $miObj->getParameter( 'Ds_Hour' ) );
		$dstermnal         = $miObj->getParameter( 'Ds_Terminal' );
		$dsmerchandata     = $miObj->getParameter( 'Ds_MerchantData' );
		$dssucurepayment   = $miObj->getParameter( 'Ds_SecurePayment' );
		$dscardcountry     = $miObj->getParameter( 'Ds_Card_Country' );
		$dsconsumercountry = $miObj->getParameter( 'Ds_ConsumerLanguage' );
		$dstransactiontype = $miObj->getParameter( 'Ds_TransactionType' );
		$dsmerchantidenti  = $miObj->getParameter( 'Ds_Merchant_Identifier' );
		$dscardbrand       = $miObj->getParameter( 'Ds_Card_Brand' );
		$dsmechandata      = $miObj->getParameter( 'Ds_MerchantData' );
		$dscargtype        = $miObj->getParameter( 'Ds_Card_Type' );
		$dserrorcode       = $miObj->getParameter( 'Ds_ErrorCode' );
		$dpaymethod        = $miObj->getParameter( 'Ds_PayMethod' ); // D o R, D: Domiciliacion, R: Transferencia. Si se paga por Iupay o TC, no se utiliza.
		$response          = intval( $response );
		$secretsha256      = get_transient( 'redsys_signature_' . sanitize_title( $ordermi ) );
		$order1            = $ordermi;
		$order2            = WCRedL()->clean_order_number( $order1 );
		$order             = WCRedL()->get_order( (int) $order2 );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', 'SHA256 Settings: ' . $usesecretsha256 );
			$this->log->add( 'bizumredsys', 'SHA256 Transcient: ' . $secretsha256 );
			$this->log->add( 'bizumredsys', 'decodeMerchantParameters: ' . $decodedata );
			$this->log->add( 'bizumredsys', 'createMerchantSignatureNotif: ' . $localsecret );
			$this->log->add( 'bizumredsys', 'Ds_Amount: ' . $total );
			$this->log->add( 'bizumredsys', 'Ds_Order: ' . $ordermi );
			$this->log->add( 'bizumredsys', 'Ds_MerchantCode: ' . $dscode );
			$this->log->add( 'bizumredsys', 'Ds_Currency: ' . $currency_code );
			$this->log->add( 'bizumredsys', 'Ds_Response: ' . $response );
			$this->log->add( 'bizumredsys', 'Ds_AuthorisationCode: ' . $id_trans );
			$this->log->add( 'bizumredsys', 'Ds_Date: ' . $dsdate );
			$this->log->add( 'bizumredsys', 'Ds_Hour: ' . $dshour );
			$this->log->add( 'bizumredsys', 'Ds_Terminal: ' . $dstermnal );
			$this->log->add( 'bizumredsys', 'Ds_MerchantData: ' . $dsmerchandata );
			$this->log->add( 'bizumredsys', 'Ds_SecurePayment: ' . $dssucurepayment );
			$this->log->add( 'bizumredsys', 'Ds_Card_Country: ' . $dscardcountry );
			$this->log->add( 'bizumredsys', 'Ds_ConsumerLanguage: ' . $dsconsumercountry );
			$this->log->add( 'bizumredsys', 'Ds_Card_Type: ' . $dscargtype );
			$this->log->add( 'bizumredsys', 'Ds_TransactionType: ' . $dstransactiontype );
			$this->log->add( 'bizumredsys', 'Ds_Merchant_Identifiers_Amount: ' . $response );
			$this->log->add( 'bizumredsys', 'Ds_Card_Brand: ' . $dscardbrand );
			$this->log->add( 'bizumredsys', 'Ds_MerchantData: ' . $dsmechandata );
			$this->log->add( 'bizumredsys', 'Ds_ErrorCode: ' . $dserrorcode );
			$this->log->add( 'bizumredsys', 'Ds_PayMethod: ' . $dpaymethod );
		}

		// refund.
		if ( '3' === $dstransactiontype ) {
			if ( 900 === $response ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', 'Response 900 (refund)' );
				}
				set_transient( $order->get_id() . '_redsys_refund', 'yes' );

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', 'update_post_meta to "refund yes"' );
				}
				$status = $order->get_status();
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', 'New Status in request: ' . $status );
				}
				$order->add_order_note( __( 'Order Payment refunded', 'woo-redsys-gateway-light' ) );
				return;
			}
			$order->add_order_note( __( 'There was an error refunding', 'woo-redsys-gateway-light' ) );
			exit;
		}

		$response = intval( $response );
		if ( $response <= 99 ) {
			// authorized.
			$order_total_compare = number_format( $order->get_total(), 2, '', '' );
			// remove 0 from bigining.
			$order_total_compare = ltrim( $order_total_compare, '0' );
			$total               = ltrim( $total, '0' );
			if ( $order_total_compare !== $total ) {
				// amount does not match.
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', 'Payment error: Amounts do not match (order: ' . $order_total_compare . ' - received: ' . $total . ')' );
				}
				// Put this order on-hold for manual checking.
				/* translators: order an received are the amount */
				$order->update_status( 'on-hold', sprintf( __( 'Validation error: Order vs. Notification amounts do not match (order: %1$s - received: %2&s).', 'woo-redsys-gateway-light' ), $order_total_compare, $total ) );
				exit;
			}
			$authorisation_code = $id_trans;

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '/****************************/' );
				$this->log->add( 'bizumredsys', '      Saving Order Meta       ' );
				$this->log->add( 'bizumredsys', '/****************************/' );
				$this->log->add( 'bizumredsys', ' ' );
			}
			if ( ! empty( $order1 ) ) {
				update_post_meta( $order->get_id(), '_payment_order_number_redsys', $order1 );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_payment_order_number_redsys saved: ' . $order1 );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '_payment_order_number_redsys NOT SAVED!!!' );
					$this->log->add( 'bizumredsys', ' ' );
				}
			}
			if ( ! empty( $dsdate ) ) {
				update_post_meta( $order->get_id(), '_payment_date_redsys', $dsdate );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_payment_date_redsys saved: ' . $dsdate );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '_payment_date_redsys NOT SAVED!!!' );
					$this->log->add( 'bizumredsys', ' ' );
				}
			}
			if ( ! empty( $dsdate ) ) {
				update_post_meta( $order->get_id(), '_payment_terminal_redsys', $dstermnal );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_payment_terminal_redsys saved: ' . $dstermnal );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '_payment_terminal_redsys NOT SAVED!!!' );
					$this->log->add( 'bizumredsys', ' ' );
				}
			}
			if ( ! empty( $dshour ) ) {
				update_post_meta( $order->get_id(), '_payment_hour_redsys', $dshour );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_payment_hour_redsys saved: ' . $dshour );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '_payment_hour_redsys NOT SAVED!!!' );
					$this->log->add( 'bizumredsys', ' ' );
				}
			}
			if ( ! empty( $id_trans ) ) {
				update_post_meta( $order->get_id(), '_authorisation_code_redsys', $authorisation_code );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_authorisation_code_redsys saved: ' . $authorisation_code );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '_authorisation_code_redsys NOT SAVED!!!' );
					$this->log->add( 'bizumredsys', ' ' );
				}
			}
			if ( ! empty( $currency_code ) ) {
				update_post_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_corruncy_code_redsys saved: ' . $currency_code );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '_corruncy_code_redsys NOT SAVED!!!' );
					$this->log->add( 'bizumredsys', ' ' );
				}
			}
			if ( ! empty( $dscardcountry ) ) {
				update_post_meta( $order->get_id(), '_card_country_redsys', $dscardcountry );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_card_country_redsys saved: ' . $dscardcountry );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '_card_country_redsys NOT SAVED!!!' );
					$this->log->add( 'bizumredsys', ' ' );
				}
			}
			if ( ! empty( $dscargtype ) ) {
				update_post_meta( $order->get_id(), '_card_type_redsys', 'C' === $dscargtype ? 'Credit' : 'Debit' );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_card_type_redsys saved: ' . $dscargtype );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '_card_type_redsys NOT SAVED!!!' );
					$this->log->add( 'bizumredsys', ' ' );
				}
			}
			// This meta is essential for later use.
			if ( ! empty( $secretsha256 ) ) {
				update_post_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '_redsys_secretsha256 NOT SAVED!!!' );
					$this->log->add( 'bizumredsys', ' ' );
				}
			}
			// Payment completed.

			$order->add_order_note( __( 'HTTP Notification received - payment completed', 'woo-redsys-gateway-light' ) );
			$order->add_order_note( __( 'Authorization code: ', 'woo-redsys-gateway-light' ) . $authorisation_code );
			$order->payment_complete();
			if ( 'completed' === $this->orderdo ) {
				$order->update_status( 'completed', __( 'Order Completed by Bizum', 'woo-redsys-gateway-light' ) );
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', 'Payment complete.' );
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '/******************************************/' );
				$this->log->add( 'bizumredsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'bizumredsys', '/******************************************/' );
				$this->log->add( 'bizumredsys', ' ' );
			}
		} else {

			$ds_response_value = WCRedL()->get_error( $response );
			$ds_error_value    = WCRedL()->get_error( $dserrorcode );

			if ( $ds_response_value ) {
				$order->add_order_note( __( 'Order cancelled by Redsys: ', 'woo-redsys-gateway-light' ) . $ds_response_value );
				update_post_meta( $order_id, '_redsys_error_payment_ds_response_value', $ds_response_value );
			}

			if ( $ds_error_value ) {
				$order->add_order_note( __( 'Order cancelled by Redsys: ', 'woo-redsys-gateway-light' ) . $ds_error_value );
				update_post_meta( $order_id, '_redsys_error_payment_ds_response_value', $ds_error_value );
			}

			if ( 'yes' === $this->debug ) {
				if ( $ds_response_value ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', $ds_response_value );
				}
				if ( $ds_error_value ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', $ds_error_value );
				}
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '/******************************************/' );
				$this->log->add( 'bizumredsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'bizumredsys', '/******************************************/' );
				$this->log->add( 'bizumredsys', ' ' );
			}
			// Order cancelled.
			$order->update_status( 'cancelled', __( 'Order cancelled by Redsys Bizum', 'woo-redsys-gateway-light' ) );
			$order->add_order_note( __( 'Order cancelled by Redsys Bizum', 'woo-redsys-gateway-light' ) );
			WC()->cart->empty_cart();
		}
	}

	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function ask_for_refund( $order_id, $transaction_id, $amount ) {

		//post code to REDSYS
		$order          = WCRedL()->get_order( $order_id );
		$terminal       = get_post_meta( $order_id, '_payment_terminal_redsys', true );
		$currency_codes = WCRedL()->get_currencies();
		$user_id        = $order->get_user_id();
		$secretsha256   = $this->get_redsys_sha256( $user_id );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', '/**************************/' );
			$this->log->add( 'bizumredsys', __( 'Starting asking for Refund', 'woo-redsys-gateway-light' ) );
			$this->log->add( 'bizumredsys', '/**************************/' );
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', __( 'Terminal : ', 'woo-redsys-gateway-light' ) . $terminal );
		}
		$transaction_type  = '3';
		$secretsha256_meta = get_post_meta( $order_id, '_redsys_secretsha256', true );
		if ( $secretsha256_meta ) {
			$secretsha256 = $secretsha256_meta;
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', __( 'Using meta for SHA256', 'woo-redsys-gateway-light' ) );
				$this->log->add( 'bizumredsys', __( 'The SHA256 Meta is: ', 'woo-redsys-gateway-light' ) . $secretsha256 );
				}
		} else {
			$secretsha256 = $secretsha256;
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', __( 'Using settings for SHA256', 'woo-redsys-gateway-light' ) );
				$this->log->add( 'bizumredsys', __( 'The SHA256 settings is: ', 'woo-redsys-gateway-light' ) . $secretsha256 );
			}
		}
		if ( 'yes' === $this->not_use_https ) {
			$final_notify_url = $this->notify_url_not_https;
		} else {
			$final_notify_url = $this->notify_url;
		}
		$redsys_adr        = $this->get_redsys_url_gateway( $user_id );
		$autorization_code = get_post_meta( $order_id, '_authorisation_code_redsys', true );
		$autorization_date = get_post_meta( $order_id, '_payment_date_redsys', true );
		$currencycode      = get_post_meta( $order_id, '_corruncy_code_redsys', true );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', __( 'All data from meta', 'woo-redsys-gateway-light' ) );
			$this->log->add( 'bizumredsys', '**********************' );
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', __( 'If something is empty, the data was not saved', 'woo-redsys-gateway-light' ) );
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', __( 'All data from meta', 'woo-redsys-gateway-light' ) );
			$this->log->add( 'bizumredsys', __( 'Authorization Code : ', 'woo-redsys-gateway-light' ) . $autorization_code );
			$this->log->add( 'bizumredsys', __( 'Authorization Date : ', 'woo-redsys-gateway-light' ) . $autorization_date );
			$this->log->add( 'bizumredsys', __( 'Currency Codey : ', 'woo-redsys-gateway-light' ) . $currencycode );
			$this->log->add( 'bizumredsys', __( 'Terminal : ', 'woo-redsys-gateway-light' ) . $terminal );
			$this->log->add( 'bizumredsys', __( 'SHA256 : ', 'woo-redsys-gateway-light' ) . $secretsha256_meta );

		}

		if ( ! empty( $currencycode ) ) {
			$currency = $currencycode;
		} else {
			if ( ! empty( $currency_codes ) ) {
				$currency = $currency_codes[ get_woocommerce_currency() ];
			}
		}

		$miObj = new RedsysAPI();
		$miObj->setParameter( 'DS_MERCHANT_AMOUNT', $amount );
		$miObj->setParameter( 'DS_MERCHANT_ORDER', $transaction_id );
		$miObj->setParameter( 'DS_MERCHANT_MERCHANTCODE', $this->customer );
		$miObj->setParameter( 'DS_MERCHANT_CURRENCY', $currency );
		$miObj->setParameter( 'DS_MERCHANT_TRANSACTIONTYPE', $transaction_type );
		$miObj->setParameter( 'DS_MERCHANT_TERMINAL', $terminal );
		$miObj->setParameter( 'DS_MERCHANT_MERCHANTURL', $final_notify_url );
		$miObj->setParameter( 'DS_MERCHANT_URLOK', add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
		$miObj->setParameter( 'DS_MERCHANT_URLKO', $order->get_cancel_order_url() );
		$miObj->setParameter( 'DS_MERCHANT_CONSUMERLANGUAGE', '001' );
		$miObj->setParameter( 'DS_MERCHANT_PRODUCTDESCRIPTION', WCRedL()->product_description( $order, $this->id ) );
		$miObj->setParameter( 'DS_MERCHANT_MERCHANTNAME', $this->commercename );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', __( 'Data sent to Redsys for refund', 'woo-redsys-gateway-light' ) );
			$this->log->add( 'bizumredsys', '*********************************' );
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', __( 'URL to Redsys : ', 'woo-redsys-gateway-light' ) . $redsys_adr );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_AMOUNT : ', 'woo-redsys-gateway-light' ) . $amount );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_ORDER : ', 'woo-redsys-gateway-light' ) . $transaction_id );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_MERCHANTCODE : ', 'woo-redsys-gateway-light' ) . $this->customer );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_CURRENCY : ', 'woo-redsys-gateway-light' ) . $currency );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_TRANSACTIONTYPE : ', 'woo-redsys-gateway-light' ) . $transaction_type );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_TERMINAL : ', 'woo-redsys-gateway-light' ) . $terminal );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_MERCHANTURL : ', 'woo-redsys-gateway-light' ) . $final_notify_url );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_URLOK : ', 'woo-redsys-gateway-light' ) . add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_URLKO : ', 'woo-redsys-gateway-light' ) . $order->get_cancel_order_url() );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_CONSUMERLANGUAGE : 001', 'woo-redsys-gateway-light' ) );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_PRODUCTDESCRIPTION : ', 'woo-redsys-gateway-light' ) . WCRedL()->product_description( $order, $this->id ) );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_MERCHANTNAME : ', 'woo-redsys-gateway-light' ) . $this->commercename );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_AUTHORISATIONCODE : ', 'woo-redsys-gateway-light' ) . $autorization_code );
			$this->log->add( 'bizumredsys', __( 'Ds_Merchant_TransactionDate : ', 'woo-redsys-gateway-light' ) . $autorization_date );
			$this->log->add( 'bizumredsys', __( 'ask_for_refund Asking por order #: ', 'woo-redsys-gateway-light' ) . $order_id );
			$this->log->add( 'bizumredsys', ' ' );
		}

		$version   = 'HMAC_SHA256_V1';
		$request   = '';
		$params    = $miObj->createMerchantParameters();
		$signature = $miObj->createMerchantSignature( $secretsha256 );

		$post_arg = wp_remote_post(
			$redsys_adr,
			array(
				'method'      => 'POST',
				'timeout'     => 45,
				'httpversion' => '1.0',
				'user-agent'  => 'WooCommerce',
				'body'        => array(
					'Ds_SignatureVersion'   => $version,
					'Ds_MerchantParameters' => $params,
					'Ds_Signature'          => $signature,
				),
			)
		);
		if ( is_wp_error( $post_arg ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', __( 'There is an error', 'woo-redsys-gateway-light' ) );
				$this->log->add( 'bizumredsys', '*********************************' );
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', __( 'The error is : ', 'woo-redsys-gateway-light' ) . $post_arg );
				}
			return $post_arg;
		}
		return true;
	}

	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	function check_redsys_refund( $order_id ) {
		// check postmeta
		$order        = WCRedL()->get_order( (int) $order_id );
		$order_refund = get_transient( $order->get_id() . '_redsys_refund' );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', __( 'Checking and waiting ping from Redsys', 'woo-redsys-gateway-light' ) );
			$this->log->add( 'bizumredsys', '*****************************************' );
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', __( 'Check order status #: ', 'woo-redsys-gateway-light' ) . $order->get_id() );
			$this->log->add( 'bizumredsys', __( 'Check order status with get_transient: ', 'woo-redsys-gateway-light' ) . $order_refund );
		}
		if ( 'yes' === $order_refund ) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		// Do your refund here. Refund $amount for the order with ID $order_id _transaction_id
		set_time_limit( 0 );
		$order = wc_get_order( $order_id );

		$transaction_id = get_post_meta( $order_id, '_payment_order_number_redsys', true );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', __( '$order_id#: ', 'woo-redsys-gateway-light' ) . $transaction_id );
		}
		if ( ! $amount ) {
			$order_total_sign = WCRedL()->redsys_amount_format( $order->get_total() );
		} else {
			$order_total_sign = number_format( $amount, 2, '', '' );
		}

		if ( ! empty( $transaction_id ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', __( 'check_redsys_refund Asking for order #: ', 'woo-redsys-gateway-light' ) . $order_id );
			}

			$refund_asked = $this->ask_for_refund( $order_id, $transaction_id, $order_total_sign );

			if ( is_wp_error( $refund_asked ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', __( 'Refund Failed: ', 'woo-redsys-gateway-light' ) . $refund_asked->get_error_message() );
				}
				return new WP_Error( 'error', $refund_asked->get_error_message() );
			}
			$x = 0;
			do {
				sleep( 5 );
				$result = $this->check_redsys_refund( $order_id );
				$x++;
			} while ( $x <= 20 && false === $result );
			if ( 'yes' === $this->debug && $result ) {
				$this->log->add( 'bizumredsys', __( 'check_redsys_refund = true ', 'woo-redsys-gateway-light' ) . $result );
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '/********************************/' );
				$this->log->add( 'bizumredsys', '  Refund complete by Redsys   ' );
				$this->log->add( 'bizumredsys', '/********************************/' );
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '/******************************************/' );
				$this->log->add( 'bizumredsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'bizumredsys', '/******************************************/' );
				$this->log->add( 'bizumredsys', ' ' );
			}
			if ( 'yes' === $this->debug && ! $result ) {
				$this->log->add( 'bizumredsys', __( 'check_redsys_refund = false ', 'woo-redsys-gateway-light' ) . $result );
			}
			if ( $result ) {
				delete_transient( $order->get_id() . '_redsys_refund' );
				return true;
			} else {
				if ( 'yes' === $this->debug && $result ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
					$this->log->add( 'bizumredsys', __( '!!!!Refund Failed, please try again!!!!', 'woo-redsys-gateway-light' ) );
					$this->log->add( 'bizumredsys', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '/******************************************/' );
					$this->log->add( 'bizumredsys', '  The final has come, this story has ended  ' );
					$this->log->add( 'bizumredsys', '/******************************************/' );
					$this->log->add( 'bizumredsys', ' ' );
				}
				return false;
			}
		} else {
			if ( 'yes' === $this->debug && $result ) {
				$this->log->add( 'bizumredsys', __( 'Refund Failed: No transaction ID', 'woo-redsys-gateway-light' ) );
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '/******************************************/' );
				$this->log->add( 'bizumredsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'bizumredsys', '/******************************************/' );
				$this->log->add( 'bizumredsys', ' ' );
			}
			return new WP_Error( 'error', __( 'Refund Failed: No transaction ID', 'woo-redsys-gateway-light' ) );
		}
	}
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	public function warning_checkout_test_mode_bizum() {
		if ( 'yes' === $this->testmode && WCRedL()->is_gateway_enabled( $this->id ) ) {
			echo '<div class="checkout-message" style="
			background-color: rgb(3, 166, 120);
			padding: 1em 1.618em;
			margin-bottom: 2.617924em;
			margin-left: 0;
			border-radius: 2px;
			color: #fff;
			clear: both;
			border-left: 0.6180469716em solid rgb(1, 152, 117);
			">';
			echo __( 'Warning: WooCommerce Redsys Gateway Bizum is in test mode. Remember to uncheck it when you go live', 'woo-redsys-gateway-light' );
			echo '</div>';
		}
	}
}
/**
* Copyright: (C) 2013 - 2021 José Conti
*/
function woocommerce_add_gateway_bizum_redsys( $methods ) {
		$methods[] = 'WC_Gateway_Bizum_Redsys';
		return $methods;
}
add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_bizum_redsys' );
