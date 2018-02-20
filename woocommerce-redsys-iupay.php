<?php
	if ( !class_exists( 'WC_Payment_Gateway' ) ) return;
	/**
	* Gateway class
	*/
	class WC_Gateway_iupay extends WC_Payment_Gateway {
		var $notify_url;
		/**
		* Constructor for the gateway.
		*
		* @access public
		* @return void
		*/
		public function __construct() {
			global $woocommerce;
			$this->id           = 'iupay';
			$this->icon         = apply_filters( 'woocommerce_iupay_icon', plugins_url( basename( plugin_dir_path(__FILE__) ), basename( __FILE__ ) ) . '/assets/images/iupayLogo.jpg' );
			$this->has_fields   = false;
			$this->liveurl      = 'https://sis.redsys.es/sis/realizarPago';
			$this->testurl      = 'https://sis-t.redsys.es:25443/sis/realizarPago';
			$this->testmode		= $this->get_option( 'testmode' );
			$this->method_title = __( 'Iupay', 'woocommerce' );
			$this->notify_url   = add_query_arg( 'wc-api', 'WC_Gateway_iupay', home_url( '/' ) );
			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();
			// Define user set variables
			$this->title 			= $this->get_option( 'title' );
			$this->description 		= $this->get_option( 'description' );
			$this->customer 		= $this->get_option( 'customer' );
			$this->terminal 		= $this->get_option( 'terminal' );
			$this->secret			= $this->get_option( 'secret' );
			$this->secretsha256		= $this->get_option( 'secretsha256' );
			$this->debug			= $this->get_option( 'debug' );
			$this->hashtype			= $this->get_option( 'hashtype' );
			$this->iupaylanguage	= $this->get_option( 'iupaylanguage' );

			// Logs
			if ( 'yes' == $this->debug ) $this->log = new WC_Logger();
			// Actions
			add_action( 'valid-iupay-standard-ipn-request', array( $this, 'successful_request' ) );
			add_action( 'woocommerce_receipt_iupay', array( $this, 'receipt_page' ) );
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			// Payment listener/API hook
			add_action( 'woocommerce_api_WC_Gateway_iupay', array( $this, 'check_ipn_response' ) );
			if ( !$this->is_valid_for_use() ) $this->enabled = false;
    	}
    	function checkfor254testiupay(){
			$usesecretsha256 = $this->secretsha256;
			$iupayactive = $this->enabled;
			if( !$usesecretsha256 && $iupayactive == 'yes' ){
				$checkfor254 = true;
			} else {
				$checkfor254 = false;
			}
			return $checkfor254;
		}
		/**
		* Check if this gateway is enabled and available in the user's country
		*
		* @access public
		* @return bool
     	*/
	 	function is_valid_for_use() {
       		if ( ! in_array(get_woocommerce_currency(), array( 'EUR' , 'BRL', 'CAD', 'GBP', 'JPY', 'TRY', 'USD', 'ARS', 'CLP', 'COP', 'INR', 'MXN', 'PEN', 'CHF', 'BOB' ) ) ) return false;
	   		return true;
    	}
		/**
		* Admin Panel Options
		*
		* @since 1.0.0
	 	*/
	 	public function admin_options() {
			?>
			<h3><?php _e( 'Iupay', 'woo-redsys-gateway-light' ); ?></h3>
			<p><?php _e( 'Iupay works by sending the user to your bank TPV to enter their payment information.', 'woo-redsys-gateway-light' ); ?></p>
			<?php if( class_exists( 'SitePress' )){ ?>
				<div class="updated fade"><h4><?php _e( 'Attention! WPML detected.', 'woo-redsys-gateway-light' ); ?></h4>
				<p><?php _e( 'The Gateway will be shown in the customer language. The option "Language Gateway" is not taken into consideration', 'woo-redsys-gateway-light' ); ?></p>
				</div>
				<?php } ?>
			<?php if ( $this->is_valid_for_use() ) : ?>
				<table class="form-table">
				<?php
    				// Generate the HTML For the settings form.
					$this->generate_settings_html();
				?>
				</table><!--/.form-table-->
			<?php else : ?>
           		<div class="inline error"><p><strong><?php _e( 'Gateway Disabled', 'woo-redsys-gateway-light' ); ?></strong>: <?php _e( 'Servired/RedSys only support EUROS &euro; and BRL currency.', 'woo-redsys-gateway-light' ); ?></p></div>
		   	<?php
				endif;
			}
		/**
		* Initialise Gateway Settings Form Fields
		*
		* @access public
		* @return void
     	*/
	 	function init_form_fields() {
    		$this->form_fields = array(
				'enabled'		 => array(
									'title'			=> __( 'Enable/Disable', 'woo-redsys-gateway-light' ),
									'type'			=> 'checkbox',
									'label'			=> __( 'Enable Iupay', 'woo-redsys-gateway-light' ),
									'default'		=> 'no'
									),
				'title'			=> array(
									'title'			=> __( 'Title', 'woo-redsys-gateway-light' ),
									'type'			=> 'text',
									'description'	=> __( 'This controls the title which the user sees during checkout.', 'woo-redsys-gateway-light' ),
									'default'		=> __( 'Iupay', 'woo-redsys-gateway-light' ),
									'desc_tip'		=> true,
									),
				'description'	=> array(
									'title'			=> __( 'Description', 'woo-redsys-gateway-light' ),
									'type'			=> 'textarea',
									'description'	=> __( 'This controls the description which the user sees during checkout.', 'woo-redsys-gateway-light' ),
									'default'		=> __( 'Pay via Iupay; you can pay with your credit card.', 'woo-redsys-gateway-light' )
									),
				'customer'		=> array(
									'title'			=> __( 'Commerce number (FUC)', 'woo-redsys-gateway-light' ),
									'type'			=> 'text',
									'description'	=> __( 'Commerce number (FUC) provided by your bank.', 'woo-redsys-gateway-light' ),
									'desc_tip'      => true,
									),
				'terminal'		=> array(
									'title'			=> __( 'Terminal number', 'woo-redsys-gateway-light' ),
									'type'			=> 'text',
									'description'	=> __( 'Terminal number provided by your bank.', 'woo-redsys-gateway-light' ),
									'desc_tip'		=> true,
									),
				'secretsha256'  => array(
									'title'			=> __( 'Encryption secret passphrase SHA-256', 'woo-redsys-gateway-light' ),
									'type'			=> 'text',
									'description'	=> __( 'Encryption secret passphrase SHA-256 provided by your bank.', 'woo-redsys-gateway-light' ),
									'desc_tip'		=> true,
									),
									'secretsha256'  => array(
									'title'			=> __( 'Encryption secret passphrase SHA-256', 'woo-redsys-gateway-light' ),
									'type'			=> 'text',
									'description'	=> __( 'Encryption secret passphrase SHA-256 provided by your bank.', 'woo-redsys-gateway-light' ),
									'desc_tip'		=> true,
									),
				'iupaylanguage'=> array(
									'title'			=> __( 'Language Gateway', 'woo-redsys-gateway-light' ),
									'type'			=> 'select',
									'description'	=> __( 'Choose the language for the Gateway. Not all Banks accept all languages', 'woo-redsys-gateway-light' ),
									'default'		=> '001',
									'options'		=> array(
															'001'	=> __( 'Spanish',		'woo-redsys-gateway-light' ),
															'002'	=> __( 'English',		'woo-redsys-gateway-light' ),
															'003'	=> __( 'Catalan',		'woo-redsys-gateway-light' ),
															'004'	=> __( 'French',		'woo-redsys-gateway-light' ),
															'005'	=> __( 'German',		'woo-redsys-gateway-light' ),
															'006'	=> __( 'Dutch',			'woo-redsys-gateway-light' ),
															'007'	=> __( 'Italian',		'woo-redsys-gateway-light' ),
															'008'	=> __( 'Swedish',		'woo-redsys-gateway-light' ),
															'009'	=> __( 'Portuguese',	'woo-redsys-gateway-light' ),
															'010'	=> __( 'Valencian',		'woo-redsys-gateway-light' ),
															'011'	=> __( 'Polish',		'woo-redsys-gateway-light' ),
															'012'	=> __( 'Galician',		'woo-redsys-gateway-light' ),
															'013'	=> __( 'Basque',		'woo-redsys-gateway-light' ),
															'208'	=> __( 'Danish',		'woo-redsys-gateway-light' )
							)
						),
				'testmode'		=> array(
									'title'			=> __( 'Running in test mode', 'woo-redsys-gateway-light' ),
									'type'			=> 'checkbox',
									'label'			=> __( 'Running in test mode', 'woo-redsys-gateway-light' ),
									'default'		=> 'yes',
									'description'	=> sprintf( __( 'Select this option for the initial testing required by your bank, deselect this option once you pass the required test phase and your production environment is active.', 'woo-redsys-gateway-light' ) ),
									),
				'debug'			=> array(
									'title'			=> __( 'Debug Log', 'woo-redsys-gateway-light' ),
									'type'			=> 'checkbox',
									'label'			=> __( 'Enable logging', 'woo-redsys-gateway-light' ),
									'default'		=> 'no',
									'description'	=> __( 'Log Iupay events, such as notifications requests, inside <code>woocommerce/logs/iupay.txt</code>', 'woo-redsys-gateway-light' ),
									)
									);
    	}
		/**
		* Get redsys Args for passing to the tpv
		*
		* @access public
		* @param mixed $order
		* @return array
		*/
		function get_iupay_args( $order ) {
			global $woocommerce;
					$order_id = $order->get_id();
					$currency_codes = array(
						'EUR' => 978,
						'USD' => 840,
						'GBP' => 826,
						'JPY' => 392,
						'ARS' => 32,
						'CAD' => 124,
						'CLP' => 152,
						'COP' => 170,
						'INR' => 356,
						'MXN' => 484,
						'PEN' => 604,
						'CHF' => 756,
						'BRL' => 986,
						'BOB' => 937,
						'TRY' => 949,
					);
					$transaction_id   = str_pad( $order_id , 12 , '0' , STR_PAD_LEFT );
					$transaction_id1  = mt_rand(1, 999); // lets to create a random number
					$transaction_id2  = substr_replace($transaction_id, $transaction_id1, 0,-9); // new order number
					$order_total  	  = number_format( $order->get_total() , 2 , ',' , '' );
					$order_total_sign = number_format( $order->get_total() , 2 , '' , '' );
					$transaction_type = '0';
					$secretsha256 	  = utf8_decode(  $this->secretsha256 );
					if( class_exists( 'SitePress' )){
						if (ICL_LANGUAGE_CODE == 'es') { $gatewaylanguage = '001'; }
						elseif (ICL_LANGUAGE_CODE == 'en') { $gatewaylanguage = '002'; }
						elseif (ICL_LANGUAGE_CODE == 'ca') { $gatewaylanguage = '003'; }
						elseif (ICL_LANGUAGE_CODE == 'fr') { $gatewaylanguage = '004'; }
						elseif (ICL_LANGUAGE_CODE == 'ge') { $gatewaylanguage = '005'; }
						elseif (ICL_LANGUAGE_CODE == 'nl') { $gatewaylanguage = '006'; }
						elseif (ICL_LANGUAGE_CODE == 'it') { $gatewaylanguage = '007'; }
						elseif (ICL_LANGUAGE_CODE == 'sv') { $gatewaylanguage = '008'; }
						elseif (ICL_LANGUAGE_CODE == 'pt') { $gatewaylanguage = '009'; }
						elseif (ICL_LANGUAGE_CODE == 'pl') { $gatewaylanguage = '011'; }
						elseif (ICL_LANGUAGE_CODE == 'gl') { $gatewaylanguage = '012'; }
						elseif (ICL_LANGUAGE_CODE == 'eu') { $gatewaylanguage = '013'; }
						elseif (ICL_LANGUAGE_CODE == 'da') { $gatewaylanguage = '108'; }
						else {
							$gatewaylanguage = '002';
						}
					} elseif( $this->iupaylanguage ){
						$gatewaylanguage = $this->iupaylanguage;
					}
					else {
						$gatewaylanguage = '001';
					}
					$returnfromiupay    = $order->get_cancel_order_url();
					$DSMerchantTerminal = $this->terminal;
					// redsys Args
					$miObj = new RedsysAPI;
					$miObj->setParameter( "DS_MERCHANT_AMOUNT",				$order_total_sign															);
					$miObj->setParameter( "DS_MERCHANT_ORDER",				$transaction_id2															);
					$miObj->setParameter( "DS_MERCHANT_MERCHANTCODE",		$this->customer																);
					$miObj->setParameter( "DS_MERCHANT_CURRENCY",			$currency_codes[ get_woocommerce_currency() ]								);
					$miObj->setParameter( "DS_MERCHANT_TRANSACTIONTYPE",	$transaction_type															);
					$miObj->setParameter( "DS_MERCHANT_TERMINAL",			$DSMerchantTerminal															);
					$miObj->setParameter( "DS_MERCHANT_MERCHANTURL",		$this->notify_url															);
					$miObj->setParameter( "DS_MERCHANT_URLOK",				add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) )		);
					$miObj->setParameter( "DS_MERCHANT_URLKO",				$returnfromiupay															);
					$miObj->setParameter( "DS_CONSUMERLANGUAGE",			$gatewaylanguage															);
					$miObj->setParameter( "DS_MERCHANT_PRODUCTDESCRIPTION",	__( 'Order' , 'woo-redsys-gateway-light' ) . ' ' .  $order->get_order_number()	);
					$miObj->setParameter( "DS_MERCHANT_MERCHANTNAME",		$this->commercename															);
					$miObj->setParameter( "Ds_Merchant_PayMethods",			'O'																			);


					$version    = "HMAC_SHA256_V1";

					$request    = "";
					$params     = $miObj->createMerchantParameters();
					$signature  = $miObj->createMerchantSignature( $secretsha256 );
					$iupay_args = array(
						'Ds_SignatureVersion' => $version,
						'Ds_MerchantParameters' => $params,
						'Ds_Signature'   => $signature

					);
					if ( 'yes' == $this->debug )
						$this->log->add( 'iupay', 'Generating payment form for order ' . $order->get_order_number() . '. Sent data: ' . print_r($iupay_args, true) );
					$iupay_args = apply_filters( 'woocommerce_iupay_args', $iupay_args );
					return $iupay_args;
		}
		/**
		* Generate the redsys form
		*
		* @access public
		* @param mixed $order_id
		* @return string
    	*/
		function generate_iupay_form( $order_id ) {
			global $woocommerce;
			$usesecretsha256 = $this->secretsha256;
			if( !$usesecretsha256 ){
					$order = new WC_Order( $order_id );
					if ( $this->testmode == 'yes' ):
						$iupay_adr = $this->testurl . '?';
						else :
							$iupay_adr = $this->liveurl . '?';
					endif;
					$iupay_args = $this->get_iupay_args( $order );
					$form_inputs = '';
						foreach ($iupay_args as $key => $value) {
								$form_inputs .= '<input type="hidden" name="' . $key . '" value="' . esc_attr( $value ) . '" />';
						}
					wc_enqueue_js( '
						jQuery("body").block({
							message: "<img src=\"' . esc_url( apply_filters( 'woocommerce_ajax_loader_url', $woocommerce->plugin_url() . '/assets/images/ajax-loader.gif' ) ) . '\" alt=\"Redirecting&hellip;\" style=\"float:left; margin-right: 10px;\" />'.__( 'Thank you for your order. We are now redirecting you to Iupay to make the payment.', 'woo-redsys-gateway-light' ).'",
							overlayCSS:
							{
								background: "#fff",
								opacity: 0.6
							},
							css: {
						        padding:        20,
						        textAlign:      "center",
						        color:          "#555",
						        border:         "3px solid #aaa",
						        backgroundColor:"#fff",
						        cursor:         "wait",
						        lineHeight:		"32px"
						    }
						});
					jQuery("#submit_iupay_payment_form").click();
					' );
					return '<form action="'.esc_url( $iupay_adr ).'" method="post" id="iupay_payment_form" target="_top">
						' . $form_inputs . '
						<input type="submit" class="button-alt" id="submit_iupay_payment_form" value="'.__( 'Pay with Iupay account', 'woo-redsys-gateway-light' ).'" /> <a class="button cancel" href="'.esc_url( $order->get_cancel_order_url() ).'">'.__( 'Cancel order &amp; restore cart', 'woo-redsys-gateway-light' ).'</a>
					</form>';
			} else {
					$order = new WC_Order( $order_id );
					if ( $this->testmode == 'yes' ):
						$iupay_adr = $this->testurl . '?';
					else :
						$iupay_adr = $this->liveurl . '?';
					endif;
					$iupay_args = $this->get_iupay_args( $order );
					$form_inputs = array();
					foreach ($iupay_args as $key => $value) {
						$form_inputs[] .= '<input type="hidden" name="' . $key . '" value="' . esc_attr( $value ) . '" />';
					}
					wc_enqueue_js( '
					jQuery("body").block({
						message: "<img src=\"' . esc_url( apply_filters( 'woocommerce_ajax_loader_url', $woocommerce->plugin_url() . '/assets/images/ajax-loader.gif' ) ) . '\" alt=\"Redirecting&hellip;\" style=\"float:left; margin-right: 10px;\" />'.__( 'Thank you for your order. We are now redirecting you to Iupay to make the payment.', 'woo-redsys-gateway-light' ).'",
						overlayCSS:
						{
							background: "#fff",
							opacity: 0.6
						},
						css: {
					        padding:        20,
					        textAlign:      "center",
					        color:          "#555",
					        border:         "3px solid #aaa",
					        backgroundColor:"#fff",
					        cursor:         "wait",
					        lineHeight:		"32px"
					    }
					});
				jQuery("#submit_iupay_payment_form").click();
				' );
					return '<form action="'.esc_url( $iupay_adr ).'" method="post" id="iupay_payment_form" target="_top">
					' . implode('', $form_inputs) . '
					<input type="submit" class="button-alt" id="submit_iupay_payment_form" value="'.__( 'Pay with Iupay account', 'woo-redsys-gateway-light' ).'" /> <a class="button cancel" href="'.esc_url( $order->get_cancel_order_url() ).'">'.__( 'Cancel order &amp; restore cart', 'woo-redsys-gateway-light' ).'</a>
				</form>';

			}
		}
		/**
		* Process the payment and return the result
		*
		* @access public
		* @param int $order_id
		* @return array
		*/
		function process_payment( $order_id ) {
			$order = new WC_Order( $order_id );
			return array(
						'result'	=> 'success',
						'redirect'	=> $order->get_checkout_payment_url( true )
						);
		}
		/**
		* Output for the order received page.
		*
		* @access public
		* @return void
		*/
		function receipt_page( $order ) {
			echo '<p>'.__( 'Thank you for your order, please click the button below to pay with Iupay.', 'woo-redsys-gateway-light' ).'</p>';
			echo $this->generate_iupay_form( $order );
		}
		/**
		* Check redsys IPN validity
		**/
		function check_ipn_request_is_valid() {
			global $woocommerce;
			if ( 'yes' == $this->debug )
				$this->log->add( 'iupay', 'HTTP Notification received: ' . print_r( $_POST, true ) );
			$usesecretsha256 = $this->secretsha256;
			if( $usesecretsha256 ){
				$version     = sanitize_text_field( $_POST["Ds_SignatureVersion"] );
				$data        = sanitize_text_field( $_POST["Ds_MerchantParameters"] );
				$remote_sign = sanitize_text_field( $_POST["Ds_Signature"] );

				$miObj   = new RedsysAPI;

				$localsecret = $miObj->createMerchantSignatureNotif($usesecretsha256,$data);
				if ( $localsecret == $remote_sign) {
					if ( 'yes' == $this->debug )
						$this->log->add( 'iupay', 'Received valid notification from Iupay' );
					if ( 'yes' == $this->debug )
						$this->log->add( 'iupay', $data );
					return true;
				} else {
					if ($this->debug == 'yes') {
						$this->log->add( 'redsys', 'Received INVALID notification from Iupay' );
					}
					return false;
				}
			} else {
				if ( 'yes' == $this->debug )
					$this->log->add( 'iupay', 'HTTP Notification received: ' . print_r( $_POST, true ) );
				if ($_POST['Ds_MerchantCode'] == $this->customer) {
					if ( 'yes' == $this->debug )
						$this->log->add( 'iupay', 'Received valid notification from Iupay' );
					return true;
				} else {
					if ( 'yes' == $this->debug )
						$this->log->add( 'iupay', 'Received INVALID notification from Iupay' );
					return false;
				}
			}
    	}
    	/**
	    * Check for Servired/RedSys HTTP Notification
	    *
	    * @access public
	    * @return void
	    */
		function check_ipn_response() {
			@ob_clean();
			$_POST = stripslashes_deep( $_POST );
			if ( $this->check_ipn_request_is_valid() ) {
    			header( 'HTTP/1.1 200 OK' );
				do_action( "valid-iupay-standard-ipn-request", $_POST );
			} else {
				wp_die( "Iupay Notification Request Failure" );
   				}
		}
		/**
		* Successful Payment!
		*
		* @access public
		* @param array $posted
		* @return void
		*/
		function successful_request( $posted ) {
			global $woocommerce;
				$version			= sanitize_text_field( $_POST["Ds_SignatureVersion"] );
				$data				= sanitize_text_field( $_POST["Ds_MerchantParameters"] );
				$remote_sign		= sanitize_text_field( $_POST["Ds_Signature"] );

				$miObj				= new RedsysAPI;

				$decodedata			= $miObj->decodeMerchantParameters($data);

				$localsecret		= $miObj->createMerchantSignatureNotif($usesecretsha256,$data);

				$total				= $miObj->getParameter('Ds_Amount');
				$ordermi			= $miObj->getParameter('Ds_Order');
				$dscode				= $miObj->getParameter('Ds_MerchantCode');
				$currency_code		= $miObj->getParameter('Ds_Currency');
				$response			= $miObj->getParameter('Ds_Response');
				$id_trans			= $miObj->getParameter('Ds_AuthorisationCode');

				$dsdate				= $miObj->getParameter('Ds_Date');
				$dshour				= $miObj->getParameter('Ds_Hour');
				$dstermnal			= $miObj->getParameter('Ds_Terminal');
				$dsmerchandata		= $miObj->getParameter('Ds_MerchantData');
				$dssucurepayment	= $miObj->getParameter('Ds_SecurePayment');
				$dscardcountry		= $miObj->getParameter('Ds_Card_Country');
				$dsconsumercountry	= $miObj->getParameter('Ds_ConsumerLanguage');
				$dscargtype			= $miObj->getParameter('Ds_Card_Type');
				$order1				= $ordermi;
				$order2				= substr( $order1, 3 ); //cojo los 9 digitos del final
				$order				= $this->get_redsys_order( (int)$order2 );

				if ($this->debug == 'yes')
					$this->log->add( 'iupay', 'Ds_Amount: ' . $total . ', Ds_Order: ' . $order1 . ',  Ds_MerchantCode: '. $dscode . ', Ds_Currency: ' . $currency_code . ', Ds_Response: ' . $response . ', Ds_AuthorisationCode: ' . $id_trans . ', $order2: ' . $order2 );
				$response = intval($response);
				if ( $response  <= 99 ) {
					//authorized
					$order_total_compare = number_format( $order->get_total() , 2 , '' , '' );
					if ( $order_total_compare != $total ) {
						//amount does not match
						if ( 'yes' == $this->debug )
							$this->log->add( 'iupay', 'Payment error: Amounts do not match (order: '.$order_total_compare.' - received: ' . $total . ')' );
						// Put this order on-hold for manual checking
						$order->update_status( 'on-hold', sprintf( __( 'Validation error: Order vs. Notification amounts do not match (order: %s - received: %s).', 'woo-redsys-gateway-light' ), $order_total_compare , $total ) );
						exit;
					}
					$authorisation_code = $id_trans;
					if ( ! empty( $order1 ) )
						update_post_meta( $order->get_id(), '_payment_order_number_iupay', $order1 );
					if ( ! empty( $dsdate ) )
						update_post_meta( $order->get_id(), '_payment_date_iupay',   $dsdate );
					if ( ! empty( $dshour ) )
						update_post_meta( $order->get_id(), '_payment_hour_iupay',   $dshour );
					if ( ! empty( $id_trans ) )
						update_post_meta( $order->get_id(), '_authorisation_code_iupay', $authorisation_code );
					if ( ! empty( $dscardcountry ) )
						update_post_meta( $order->get_id(), '_card_country_iupay',   $dscardcountry );
					if ( ! empty( $dscargtype ) )
						update_post_meta( $order->get_id(), '_card_type_iupay',   $dscargtype == 'C' ? 'Credit' : 'Debit' );
					// Payment completed
					$order->add_order_note( __( 'HTTP Notification received - payment completed', 'woo-redsys-gateway-light' ) );
					$order->add_order_note( __( 'Authorisation code: ',  'woo-redsys-gateway-light' ) . $authorisation_code );
					$order->payment_complete();
					if ($this->debug == 'yes')
						$this->log->add( 'iupay', 'Payment complete.' );
				} elseif ( $response  == 101 ) {
					//Tarjeta caducada
					if ( 'yes' == $this->debug )
						$this->log->add( 'iupay', 'Pedido cancelado por Iupay: Tarjeta caducada' );
					//Order cancelled
					$order->update_status( 'cancelled', __( 'Cancelled by Iupay', 'woo-redsys-gateway-light' ) );
					$order->add_order_note( __('Pedido cancelado por Iupay: Tarjeta caducada', 'woo-redsys-gateway-light') );
					WC()->cart->empty_cart();
				}
		}
		/**
		* get_iupay_order function.
		*
		* @access public
		* @param mixed $posted
		* @return void
		*/
		function get_iupay_order( $order_id ) {
			$order = new WC_Order( $order_id );
			return $order;
		}
	}
	add_action( 'admin_notices', 'admin_notice_iupay_sha256');
	function admin_notice_iupay_sha256() {

		$sha = new WC_Gateway_iupay();
		if ( $sha->checkfor254testiupay() ) {
			$class = "error";
			$message = __( 'WARNING: You need to add Encryption secret passphrase SHA-256 to Iupay Gateway Settings.', 'woo-redsys-gateway-light' );
				echo"<div class=\"$class\"> <p>$message $prueba</p></div>";
		} else {
			return;
		}
	}
	function woocommerce_add_gateway_iupay_gateway($methods) {
		$methods[] = 'WC_Gateway_iupay';
		return $methods;
	}
	add_filter('woocommerce_payment_gateways', 'woocommerce_add_gateway_iupay_gateway' );
	function add_iupay_meta_box(){
		echo '<h4>' . __('Payment Details','woo-redsys-gateway-light') . '</h4>';
		echo '<p><strong>' . __( 'Iupay Date', 'woo-redsys-gateway-light' ) . ': </strong><br />' . get_post_meta( get_the_ID(), '_payment_date_iupay', true) . '</p>';
		echo '<p><strong>' . __( 'Iupay Hour', 'woo-redsys-gateway-light' ) . ': </strong><br />' . get_post_meta( get_the_ID(), '_payment_hour_iupay', true) . '</p>';
		echo '<p><strong>' . __( 'Iupay Authorisation Code', 'woo-redsys-gateway-light' ) . ': </strong><br />' . get_post_meta( get_the_ID(), '_authorisation_code_iupay', true) . '</p>';

	}
	add_action( 'woocommerce_admin_order_data_after_billing_address', 'add_iupay_meta_box' );

?>