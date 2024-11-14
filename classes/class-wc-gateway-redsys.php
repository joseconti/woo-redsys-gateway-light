<?php
/**
 * WooCommerce Redsys Gateway Class.
 *
 * Built the Redsys method.
 *
 * @class       WC_Gateway_Redsys
 * @extends     WC_Payment_Gateway
 * @version     2.0.0
 * @package     WooCommerce/Classes/Payment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Redsys Payment Gateway class.
 */
class WC_Gateway_Redsys extends WC_Payment_Gateway {
	/**
	 * $notify_url
	 *
	 * @var string
	 */
	public $notify_url;
	/**
	 * $id
	 *
	 * @var string
	 */
	public $id;
	/**
	 * $icon
	 *
	 * @var string
	 */
	public $icon;
	/**
	 * $has_fields
	 *
	 * @var bool
	 */
	public $has_fields;
	/**
	 * $liveurl
	 *
	 * @var string
	 */
	public $liveurl;
	/**
	 * $testurl
	 *
	 * @var string
	 */
	public $testurl;
	/**
	 * $liveurlws
	 *
	 * @var string
	 */
	public $liveurlws;
	/**
	 * $testurlws
	 *
	 * @var string
	 */
	public $testurlws;
	/**
	 * $testsha256
	 *
	 * @var string|null
	 */
	public $testsha256;
	/**
	 * $testmode
	 *
	 * @var string|bool
	 */
	public $testmode;
	/**
	 * $method_title
	 *
	 * @var string
	 */
	public $method_title;
	/**
	 * $method_description
	 *
	 * @var string
	 */
	public $method_description;
	/**
	 * $not_use_https
	 *
	 * @var string|bool|null
	 */
	public $not_use_https;
	/**
	 * $notify_url_not_https
	 *
	 * @var string
	 */
	public $notify_url_not_https;
	/**
	 * $log
	 *
	 * @var WC_Logger|null
	 */
	public $log;
	/**
	 * $supports
	 *
	 * @var array
	 */
	public $supports;
	/**
	 * $title
	 *
	 * @var string
	 */
	public $title;
	/**
	 * $description
	 *
	 * @var string
	 */
	public $description;
	/**
	 * $customer
	 *
	 * @var string|null
	 */
	public $customer;
	/**
	 * $commercename
	 *
	 * @var string|null
	 */
	public $commercename;
	/**
	 * $terminal
	 *
	 * @var string|null
	 */
	public $terminal;
	/**
	 * $secretsha256
	 *
	 * @var string|null
	 */
	public $secretsha256;
	/**
	 * $customtestsha256
	 *
	 * @var string|null
	 */
	public $customtestsha256;
	/**
	 * $redsyslanguage
	 *
	 * @var string|null
	 */
	public $redsyslanguage;
	/**
	 * $debug
	 *
	 * @var string|bool
	 */
	public $debug;
	/**
	 * $enabled
	 *
	 * @var bool
	 */
	public $enabled;
	/**
	 * $hashtype
	 *
	 * @var string|null
	 */
	public $hashtype;
	/**
	 * $lwvactive
	 *
	 * @var string|bool|null
	 */
	public $lwvactive;
	/**
	 * $psd2
	 *
	 * @var string|bool|null
	 */
	public $psd2;
	/**
	 * $orderdo
	 *
	 * @var string|null
	 */
	public $orderdo;
	/**
	 * $secret
	 *
	 * @var string|null
	 */
	public $secret;
	/**
	 * $payoptions
	 *
	 * @var string|null
	 */
	public $payoptions;
	/**
	 * $logo
	 *
	 * @var string|null
	 */
	public $logo;
	/**
	 * The live URL for the gateway.
	 *
	 * @var string
	 */
	public $liveurlrest;

	/**
	 * The test URL for the gateway.
	 *
	 * @var string
	 */
	public $testurlrest;

	/**
	 * Constructor for the gateway.
	 *
	 * @return void
	 */
	public function __construct() {
		global $woocommerce, $checkfor254;
		$this->id = 'redsys';
		$logo_url = $this->get_option( 'logo' );
		if ( ! empty( $logo_url ) ) {
			$logo_url   = $this->get_option( 'logo' );
			$this->icon = apply_filters( 'woocommerce_' . $this->id . '_icon', $logo_url );
		} else {
			$this->icon = apply_filters( 'woocommerce_' . $this->id . '_icon', REDSYS_PLUGIN_URL . 'assets/images/redsys.png' );
		}
		$this->has_fields           = false;
		$this->liveurl              = 'https://sis.redsys.es/sis/realizarPago';
		$this->testurl              = 'https://sis-t.redsys.es:25443/sis/realizarPago';
		$this->liveurlws            = 'https://sis.redsys.es/sis/services/SerClsWSEntrada?wsdl';
		$this->testurlws            = 'https://sis-t.redsys.es:25443/sis/services/SerClsWSEntrada?wsdl';
		$this->liveurlrest          = 'https://sis.redsys.es/sis/rest/trataPeticionREST';
		$this->testurlrest          = 'https://sis-t.redsys.es:25443/sis/rest/trataPeticionREST';
		$this->testmode             = $this->get_option( 'testmode' );
		$this->method_title         = __( 'Redsys Lite (by Jose Conti)', 'woo-redsys-gateway-light' );
		$this->method_description   = __( 'Redsys Lite  works redirecting customers to Redsys.', 'woo-redsys-gateway-light' );
		$this->not_use_https        = $this->get_option( 'not_use_https' );
		$this->notify_url           = add_query_arg( 'wc-api', 'WC_Gateway_redsys', home_url( '/' ) );
		$this->notify_url_not_https = str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'WC_Gateway_redsys', home_url( '/' ) ) );
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		// Define user set variables.
		$this->psd2             = 'yes';
		$this->title            = $this->get_option( 'title' );
		$this->description      = $this->get_option( 'description' );
		$this->logo             = $this->get_option( 'logo' );
		$this->orderdo          = $this->get_option( 'orderdo' );
		$this->customer         = $this->get_option( 'customer' );
		$this->commercename     = $this->get_option( 'commercename' );
		$this->payoptions       = $this->get_option( 'payoptions' );
		$this->terminal         = $this->get_option( 'terminal' );
		$this->secret           = $this->get_option( 'secret' );
		$this->secretsha256     = $this->get_option( 'secretsha256' );
		$this->customtestsha256 = $this->get_option( 'customtestsha256' );
		$this->debug            = $this->get_option( 'debug' );
		$this->hashtype         = $this->get_option( 'hashtype' );
		$this->redsyslanguage   = $this->get_option( 'redsyslanguage' );
		$this->lwvactive        = $this->get_option( 'lwvactive' );
		$this->log              = new WC_Logger();
		$this->supports         = array(
			'products',
			'refunds',
		);
		// Actions.
		add_action( 'valid_' . $this->id . '_standard_ipn_request', array( $this, 'successful_request' ) );
		add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		// Payment listener/API hook.
		add_action( 'woocommerce_api_wc_gateway_' . $this->id, array( $this, 'check_ipn_response' ) );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'warning_checkout_test_mode' ) );
		if ( ! $this->is_valid_for_use() ) {
			$this->enabled = false;
		}
	}
	/**
	 * Display an admin notice if the PHP mcrypt_encrypt module is not installed.
	 *
	 * @return void
	 */
	public static function admin_notice_mcrypt_encrypt() {
		if ( ! function_exists( 'mcrypt_encrypt' ) && ( version_compare( PHP_VERSION, '7.0', '<' ) ) ) {
			$class   = 'error';
			$message = __( 'WARNING: The PHP mcrypt_encrypt module is not installed on your server. The API Redsys SHA-256 needs this module in order to work.	Please contact your hosting provider and ask them to install it. Otherwise, your shop will stop working.', 'woo-redsys-gateway-light' );
			echo '<div class=' . esc_attr( $class ) . '> <p>' . esc_attr( $message ) . '</p></div>';
		} else {
			return;
		}
	}
	/**
	 * Get Redsys URL Gateway
	 *
	 * @return string
	 */
	public function get_redsys_url_gateway_rest() {

		if ( 'yes' === $this->testmode ) {
			return $this->testurlrest;
		} else {
			return $this->liveurlrest;
		}
	}
	/**
	 * Check if this gateway is enabled and available in the user's country
	 *
	 * @return bool
	 */
	public function is_valid_for_use() {

		if ( ! in_array( get_woocommerce_currency(), WCRedL()->allowed_currencies(), true ) ) {
			return false;
		} else {
			return true;
		}
	}
	/**
	 * Admin Panel Options
	 *
	 * @since 1.0.0
	 */
	public function admin_options() {
		?>
			<h3><?php esc_html_e( 'Servired/RedSys Spain', 'woo-redsys-gateway-light' ); ?></h3>
			<div class="updated woocommerce-message inline">
				<p>
					<a href="https://woocommerce.com/products/redsys-gateway/" target="_blank" rel="noopener"><img class="aligncenter wp-image-211 size-full" title="Consigue la versión Pro en WooCommerce.com" src="<?php echo esc_url( REDSYS_PLUGIN_URL ) . 'assets/images/banner.png'; ?>" alt="Consigue la versión Pro en WooCommerce.com" width="800" height="150" /></a>
				</p>
			</div>
			<div class="redsysnotice">
				<span class="dashicons dashicons-welcome-learn-more redsysnotice-dash"></span>
				<span class="redsysnotice__content">
				<?php
				// Define allowed HTML tags.
				$allowed_html = array(
					'a' => array(
						'href'   => array(),
						'target' => array(),
						'rel'    => array(),
					),
				);

				// Define the URLs.
				$faq_url     = esc_url( 'https://plugins.joseconti.com/redsys-for-woocommerce/' );
				$support_url = esc_url( 'https://wordpress.org/support/plugin/woo-redsys-gateway-light/' );
				$review_url  = esc_url( 'https://wordpress.org/support/plugin/woo-redsys-gateway-light/reviews/?rate=5#new-post' );

				$raw_html = sprintf(
					// Translators: %1$s is the FAQ page URL, %2$s is the support thread URL, %3$s is the review URL.
					__( 'Check <a href="%1$s" target="_blank" rel="noopener">FAQ page</a> for working problems, or open a <a href="%2$s" target="_blank" rel="noopener">thread on WordPress.org</a> for support. Please, add a <a href="%3$s" target="_blank" rel="noopener">review on WordPress.org</a>', 'woo-redsys-gateway-light' ),
					$faq_url,
					$support_url,
					$review_url
				);
				echo wp_kses( $raw_html, $allowed_html );
				?>
				<span>
			</div>
			<p><?php esc_html_e( 'Servired/RedSys works by sending the user to your bank TPV to enter their payment information.', 'woo-redsys-gateway-light' ); ?></p>
			<?php
			if ( class_exists( 'SitePress' ) ) {
				?>
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
			<?php else : ?>
				<div class="inline error"><p><strong><?php esc_html_e( 'Gateway Disabled', 'woo-redsys-gateway-light' ); ?></strong>: <?php esc_html_e( 'Servired/RedSys only support EUROS &euro; and BRL currency.', 'woo-redsys-gateway-light' ); ?></p></div>
				<?php
			endif;
	}
	/**
	 * Initialise Gateway Settings Form Fields
	 *
	 * @access public
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'          => array(
				'title'   => __( 'Enable/Disable', 'woo-redsys-gateway-light' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Servired/RedSys', 'woo-redsys-gateway-light' ),
				'default' => 'no',
			),
			'title'            => array(
				'title'       => __( 'Title', 'woo-redsys-gateway-light' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woo-redsys-gateway-light' ),
				'default'     => __( 'Servired/RedSys', 'woo-redsys-gateway-light' ),
				'desc_tip'    => true,
			),
			'description'      => array(
				'title'       => __( 'Description', 'woo-redsys-gateway-light' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the description which the user sees during checkout.', 'woo-redsys-gateway-light' ),
				'default'     => __( 'Pay via Servired/RedSys; you can pay with your credit card.', 'woo-redsys-gateway-light' ),
			),
			'logo'             => array(
				'title'       => __( 'Logo', 'woo-redsys-gateway-light' ),
				'type'        => 'text',
				'description' => __( 'Add link to image logo.', 'woo-redsys-gateway-light' ),
				'desc_tip'    => true,
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
			'payoptions'       => array(
				'title'       => __( 'Pay Options', 'woo-redsys-gateway-light' ),
				'type'        => 'select',
				'description' => __( 'Chose options in Redsys Gateway (by Default Credit Card)', 'woo-redsys-gateway-light' ),
				'default'     => 'T',
				'options'     => array(
					' ' => __( 'All Methods', 'woo-redsys-gateway-light' ),
					'T' => __( 'Credit Card', 'woo-redsys-gateway-light' ),
					'C' => __( 'Credit Card', 'woo-redsys-gateway-light' ),
				),
			),
			'terminal'         => array(
				'title'       => __( 'Terminal number', 'woo-redsys-gateway-light' ),
				'type'        => 'text',
				'description' => __( 'Terminal number provided by your bank.', 'woo-redsys-gateway-light' ),
				'desc_tip'    => true,
			),
			'not_use_https'    => array(
				'title'       => __( 'HTTPS SNI Compatibility', 'woo-redsys-gateway-light' ),
				'type'        => 'checkbox',
				'label'       => __( 'Activate SNI Compatibility.', 'woo-redsys-gateway-light' ),
				'default'     => 'no',
				'description' => sprintf( __( 'If you are using HTTPS and Redsys don\'t support your certificate, example Lets Encrypt, you can deactivate HTTPS notifications. WARNING: If you are forcing redirection to HTTPS with htaccess, you need to add an exception for notification URL', 'woo-redsys-gateway-light' ) ),
			),
			'lwvactive'        => array(
				'title'   => __( 'Enable LWV', 'woo-redsys-gateway-light' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable LWV. WARNING, your bank has to enable it before you use it.', 'woo-redsys-gateway-light' ),
				'default' => 'no',
			),
			'orderdo'          => array(
				'title'       => __( 'What to do after payment?', 'woo-redsys-gateway-light' ),
				'type'        => 'select',
				'description' => __( 'Chose what to do after the customer pay the order.', 'woo-redsys-gateway-light' ),
				'default'     => 'processing',
				'options'     => array(
					'processing' => __( 'Mark as Processing (default & recomended)', 'woo-redsys-gateway-light' ),
					'completed'  => __( 'Mark as Complete', 'woo-redsys-gateway-light' ),
				),
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
				'options'     => array(
					'001' => 'Español',
					'002' => 'English - Inglés',
					'003' => 'Català',
					'004' => 'Français - Frances',
					'005' => 'Deutsch - Aleman',
					'006' => 'Nederlands - Holandes',
					'007' => 'Italiano',
					'008' => 'Svenska - Sueco',
					'009' => 'Português',
					'010' => 'Valencià',
					'011' => 'Polski - Polaco',
					'012' => 'Galego',
					'013' => 'Euskara',
					'100' => 'български език - Bulgaro',
					'156' => 'Chino',
					'191' => 'Hrvatski - Croata',
					'203' => 'Čeština - Checo',
					'208' => 'Dansk - Danes',
					'233' => 'Eesti keel - Estonio',
					'246' => 'Suomi - Finlandes',
					'300' => 'ελληνικά - Griego',
					'348' => 'Magyar - Hungaro',
					'392' => 'Japonés',
					'428' => 'Latviešu valoda - Leton',
					'440' => 'Lietuvių kalba - Lituano',
					'470' => 'Malti - Maltés',
					'642' => 'Română - Rumano',
					'643' => 'ру́сский язы́к – Ruso',
					'703' => 'Slovenský jazyk - Eslovaco',
					'705' => 'Slovenski jezik - Esloveno',
					'792' => 'Türkçe - Turco',
				),
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
				'label'       => __( 'Enable logging', 'woo-redsys-gateway-light' ),
				'default'     => 'no',
				'description' => __( 'Log Servired/RedSys events, such as notifications requests, inside <code>WooCommerce > Status > Logs > redsys-{date}-{number}.log</code>', 'woo-redsys-gateway-light' ),
			),
		);
	}
	/**
	 * Redsys args.
	 *
	 * @param mixed $order order object.
	 */
	public function get_redsys_args( $order ) {
		global $woocommerce;
		$order_id         = $order->get_id();
		$currency_codes   = WCRedL()->get_currencies();
		$transaction_id2  = WCRedL()->prepare_order_number( $order_id );
		$order_total      = number_format( $order->get_total(), 2, ',', '' );
		$order_total_sign = number_format( $order->get_total(), 2, '', '' );
		$transaction_type = '0';
		if ( 'yes' === $this->testmode ) {
			$secretsha256 = $this->customtestsha256;
			if ( ! empty( $secretsha256 ) ) {
				$secretsha256 = $this->customtestsha256;
			} else {
				$secretsha256 = $this->secretsha256;
			}
		} else {
			$secretsha256 = $this->secretsha256;
		}
		if ( class_exists( 'SitePress' ) ) {
			if ( ICL_LANGUAGE_CODE === 'es' ) {
				$gatewaylanguage = '001';
			} elseif ( ICL_LANGUAGE_CODE === 'en' ) {
				$gatewaylanguage = '002';
			} elseif ( ICL_LANGUAGE_CODE === 'ca' ) {
				$gatewaylanguage = '003';
			} elseif ( ICL_LANGUAGE_CODE === 'fr' ) {
				$gatewaylanguage = '004';
			} elseif ( ICL_LANGUAGE_CODE === 'ge' ) {
				$gatewaylanguage = '005';
			} elseif ( ICL_LANGUAGE_CODE === 'nl' ) {
				$gatewaylanguage = '006';
			} elseif ( ICL_LANGUAGE_CODE === 'it' ) {
				$gatewaylanguage = '007';
			} elseif ( ICL_LANGUAGE_CODE === 'sv' ) {
				$gatewaylanguage = '008';
			} elseif ( ICL_LANGUAGE_CODE === 'pt' ) {
				$gatewaylanguage = '009';
			} elseif ( ICL_LANGUAGE_CODE === 'pl' ) {
				$gatewaylanguage = '011';
			} elseif ( ICL_LANGUAGE_CODE === 'gl' ) {
				$gatewaylanguage = '012';
			} elseif ( ICL_LANGUAGE_CODE === 'eu' ) {
				$gatewaylanguage = '013';
			} elseif ( ICL_LANGUAGE_CODE === 'da' ) {
				$gatewaylanguage = '108';
			} else {
					$gatewaylanguage = '002';
			}
		} elseif ( $this->redsyslanguage ) {
				$gatewaylanguage = $this->redsyslanguage;
		} else {
				$gatewaylanguage = '001';
		}
		$returnfromredsys   = $order->get_cancel_order_url();
		$dsmerchantterminal = $this->terminal;
		$nombr_apellidos    = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
		if ( 'yes' === $this->not_use_https ) {
				$final_notify_url = $this->notify_url_not_https;
		} else {
			$final_notify_url = $this->notify_url;
		}
		$merchant_module = 'WooCommerce_Redsys_Gateway_Light_' . REDSYS_WOOCOMMERCE_VERSION . '_WordPress.org';
		// redsys Args.
		$mi_obj = new RedsysAPI();
		$mi_obj->set_parameter( 'DS_MERCHANT_AMOUNT', $order_total_sign );
		$mi_obj->set_parameter( 'DS_MERCHANT_ORDER', $transaction_id2 );
		$mi_obj->set_parameter( 'DS_MERCHANT_MERCHANTCODE', $this->customer );
		$mi_obj->set_parameter( 'DS_MERCHANT_CURRENCY', $currency_codes[ get_woocommerce_currency() ] );
		$mi_obj->set_parameter( 'DS_MERCHANT_TRANSACTIONTYPE', $transaction_type );
		$mi_obj->set_parameter( 'DS_MERCHANT_TERMINAL', $dsmerchantterminal );
		$mi_obj->set_parameter( 'DS_MERCHANT_MERCHANTURL', $final_notify_url );
		$mi_obj->set_parameter( 'DS_MERCHANT_TITULAR', $nombr_apellidos );
		$mi_obj->set_parameter( 'DS_MERCHANT_URLOK', add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
		$mi_obj->set_parameter( 'DS_MERCHANT_URLKO', $returnfromredsys );
		$mi_obj->set_parameter( 'DS_MERCHANT_CONSUMERLANGUAGE', $gatewaylanguage );
		$mi_obj->set_parameter( 'DS_MERCHANT_PRODUCTDESCRIPTION', __( 'Order', 'woo-redsys-gateway-light' ) . ' ' . $order->get_order_number() );
		$mi_obj->set_parameter( 'DS_MERCHANT_MERCHANTNAME', $this->commercename );
		if ( $order_total_sign <= 3000 && 'yes' === $this->lwvactive ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', 'Using LWV' );
				$this->log->add( 'redsys', ' ' );
			}
			$mi_obj->set_parameter( 'DS_MERCHANT_EXCEP_SCA', 'LWV' );
		} elseif ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', 'NOT Using LWV' );
			$this->log->add( 'redsys', ' ' );
		}
		if ( ! empty( $this->payoptions ) || ' ' !== $this->payoptions ) {
			$mi_obj->set_parameter( 'DS_MERCHANT_PAYMETHODS', $this->payoptions );
		} else {
			$mi_obj->set_parameter( 'DS_MERCHANT_PAYMETHODS', 'T' );
		}
		if ( 'yes' === $this->psd2 ) {
			$psd2 = WCPSD2L()->get_acctinfo( $order );
			$mi_obj->set_parameter( 'Ds_Merchant_EMV3DS', $psd2 );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'PSD2 activado' );
				$this->log->add( 'redsys', '$psd2: ' . $psd2 );
			}
		}
		$version = 'HMAC_SHA256_V1';
		// Se generan los parámetros de la petición.
		$request     = '';
		$params      = $mi_obj->create_merchant_parameters();
		$signature   = $mi_obj->create_merchant_signature( $secretsha256 );
		$redsys_args = array(
			'Ds_SignatureVersion'   => $version,
			'Ds_MerchantParameters' => $params,
			'Ds_Signature'          => $signature,
		);
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', 'Generating payment form for order ' . $order->get_order_number() . '. Sent data: ' . print_r( $redsys_args, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$this->log->add( 'redsys', 'Helping to understand the encrypted code: ' );
			$this->log->add( 'redsys', 'DS_MERCHANT_AMOUNT: ' . $order_total_sign );
			$this->log->add( 'redsys', 'DS_MERCHANT_ORDER: ' . $transaction_id2 );
			$this->log->add( 'redsys', 'DS_MERCHANT_MERCHANTCODE: ' . $this->customer );
			$this->log->add( 'redsys', 'DS_MERCHANT_CURRENCY: ' . $currency_codes[ get_woocommerce_currency() ] );
			$this->log->add( 'redsys', 'DS_MERCHANT_TRANSACTIONTYPE: ' . $transaction_type );
			$this->log->add( 'redsys', 'DS_MERCHANT_TERMINAL: ' . $dsmerchantterminal );
			$this->log->add( 'redsys', 'DS_MERCHANT_MERCHANTURL: ' . $final_notify_url );
			$this->log->add( 'redsys', 'DS_MERCHANT_URLOK: ' . add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
			$this->log->add( 'redsys', 'DS_MERCHANT_URLKO: ' . $returnfromredsys );
			$this->log->add( 'redsys', 'DS_MERCHANT_CONSUMERLANGUAGE: ' . $gatewaylanguage );
			$this->log->add( 'redsys', 'DS_MERCHANT_PRODUCTDESCRIPTION: ' . __( 'Order', 'woo-redsys-gateway-light' ) . ' ' . $order->get_order_number() );
			$this->log->add( 'redsys', 'DS_MERCHANT_PAYMETHODS: ' . $this->payoptions );
			$this->log->add( 'redsys', 'DS_MERCHANT_MODULE: ' . $merchant_module );
		}
			$redsys_args = apply_filters( 'woocommerce_redsys_args', $redsys_args );
			return $redsys_args;
	}
	/**
	 * Generate the redsys form
	 *
	 * @param mixed $order_id order id.
	 * @return string
	 */
	public function generate_redsys_form( $order_id ) {
		global $woocommerce;

		if ( 'yes' === $this->testmode ) {
			$usesecretsha256 = $this->customtestsha256;
			if ( ! empty( $usesecretsha256 ) ) {
				$usesecretsha256 = $this->customtestsha256;
			} else {
				$usesecretsha256 = $this->secretsha256;
			}
		} else {
			$usesecretsha256 = $this->secretsha256;
		}

		if ( ! $usesecretsha256 ) {
			$order = new WC_Order( $order_id );
			if ( 'yes' === $this->testmode ) :
				$redsys_adr = $this->testurl . '?';
				else :
					$redsys_adr = $this->liveurl . '?';
				endif;
				$redsys_args = $this->get_redsys_args( $order );
				$form_inputs = '';
				foreach ( $redsys_args as $key => $value ) {
					$form_inputs .= '<input type="hidden" name="' . $key . '" value="' . esc_attr( $value ) . '" />';
				}
				wc_enqueue_js(
					'
				$("body").block({
					message: "<img src=\"' . esc_url( apply_filters( 'woocommerce_ajax_loader_url', $woocommerce->plugin_url() . '/assets/images/select2-spinner.gif' ) ) . '\" alt=\"Redirecting&hellip;\" style=\"float:left; margin-right: 10px;\" />' . __( 'Thank you for your order. We are now redirecting you to Servired/RedSys to make the payment.', 'woo-redsys-gateway-light' ) . '",
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
			jQuery("#submit_redsys_payment_form").click();
			'
				);
			return '<form action="' . esc_url( $redsys_adr ) . '" method="post" id="redsys_payment_form" target="_top">
				' . $form_inputs . '
				<input type="submit" class="button-alt" id="submit_redsys_payment_form" value="' . __( 'Pay with Credit Card via Servired/RedSys', 'woo-redsys-gateway-light' ) . '" /> <a class="button cancel" href="' . esc_url( $order->get_cancel_order_url() ) . '">' . __( 'Cancel order &amp; restore cart', 'woo-redsys-gateway-light' ) . '</a>
			</form>';
		} else {
			$order = new WC_Order( $order_id );
			if ( 'yes' === $this->testmode ) :
				$redsys_adr = $this->testurl . '?';
				else :
					$redsys_adr = $this->liveurl . '?';
				endif;
				$redsys_args = $this->get_redsys_args( $order );
				$form_inputs = array();
				foreach ( $redsys_args as $key => $value ) {
					$form_inputs[] .= '<input type="hidden" name="' . $key . '" value="' . esc_attr( $value ) . '" />';
				}
				wc_enqueue_js(
					'
				$("body").block({
					message: "<img src=\"' . esc_url( apply_filters( 'woocommerce_ajax_loader_url', $woocommerce->plugin_url() . '/assets/images/select2-spinner.gif' ) ) . '\" alt=\"Redirecting&hellip;\" style=\"float:left; margin-right: 10px;\" />' . __( 'Thank you for your order. We are now redirecting you to Servired/RedSys to make the payment.', 'woo-redsys-gateway-light' ) . '",
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
			jQuery("#submit_redsys_payment_form").click();
			'
				);
			return '<form action="' . esc_url( $redsys_adr ) . '" method="post" id="redsys_payment_form" target="_top">
				' . implode( '', $form_inputs ) . '
				<input type="submit" class="button-alt" id="submit_redsys_payment_form" value="' . __( 'Pay with Credit Card via Servired/RedSys', 'woo-redsys-gateway-light' ) . '" /> <a class="button cancel" href="' . esc_url( $order->get_cancel_order_url() ) . '">' . __( 'Cancel order &amp; restore cart', 'woo-redsys-gateway-light' ) . '</a>
			</form>';
		}
	}
	/**
	 * Process the payment and return the result
	 *
	 * @param int $order_id order id.
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = new WC_Order( $order_id );
		return array(
			'result'   => 'success',
			'redirect' => $order->get_checkout_payment_url( true ),
		);
	}
	/**
	 * Display the receipt page for the order.
	 *
	 * @param WC_Order $order The order object.
	 */
	public function receipt_page( $order ) {

		// Display thank you message.
		echo '<p>' . esc_html__( 'Thank you for your order, please click the button below to pay with Credit Card via Servired/RedSys.', 'woo-redsys-gateway-light' ) . '</p>';

		// Generate and display the Redsys form.
		$allowed_html = array(
			'form'  => array(
				'action' => array(),
				'method' => array(),
				'id'     => array(),
				'target' => array(),
			),
			'input' => array(
				'type'  => array(),
				'name'  => array(),
				'value' => array(),
				'class' => array(),
				'id'    => array(),
			),
			'a'     => array(
				'class' => array(),
				'href'  => array(),
			),
		);
		echo wp_kses( $this->generate_redsys_form( $order ), $allowed_html );
	}
	/**
	 * Check redsys IPN validity
	 **/
	public function check_ipn_request_is_valid() {
		global $woocommerce;

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', 'HTTP Notification received: ' . print_r( $_POST, true ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.PHP.DevelopmentFunctions.error_log_print_r
		}

		if ( 'yes' === $this->testmode ) {
			$usesecretsha256 = $this->customtestsha256;
			if ( ! empty( $usesecretsha256 ) ) {
				$usesecretsha256 = $this->customtestsha256;
			} else {
				$usesecretsha256 = $this->secretsha256;
			}
		} else {
			$usesecretsha256 = $this->secretsha256;
		}

		if ( $usesecretsha256 ) {

			if ( isset( $_POST['Ds_SignatureVersion'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				$version = sanitize_text_field( wp_unslash( $_POST['Ds_SignatureVersion'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			} else {
				$version = '';
			}

			if ( isset( $_POST['Ds_MerchantParameters'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				$data = sanitize_text_field( wp_unslash( $_POST['Ds_MerchantParameters'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			} else {
				$data = '';
			}

			if ( isset( $_POST['Ds_Signature'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				$remote_sign = sanitize_text_field( wp_unslash( $_POST['Ds_Signature'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			} else {
				$remote_sign = '';
			}

			$mi_obj      = new RedsysAPI();
			$localsecret = $mi_obj->create_merchant_signature_notif( $usesecretsha256, $data );

			if ( $localsecret === $remote_sign ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Received valid notification from Servired/RedSys' );
					$this->log->add( 'redsys', $data );
				}
				return true;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Received INVALID notification from Servired/RedSys' );
				}
				return false;
			}
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'HTTP Notification received: ' . print_r( $_POST, true ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}
			if ( sanitize_text_field( wp_unslash( $_POST['Ds_MerchantCode'] ) ) === $this->customer ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Received valid notification from Servired/RedSys' );
				}
				return true;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Received INVALID notification from Servired/RedSys' );
				}
				return false;
			}
		}
	}
	/**
	 * Check for Servired/RedSys HTTP Notification
	 *
	 * @return void
	 */
	public function check_ipn_response() {
		@ob_clean(); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$post = stripslashes_deep( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( $this->check_ipn_request_is_valid() ) {
			header( 'HTTP/1.1 200 OK' );
			do_action( 'valid_' . $this->id . '_standard_ipn_request', $post );
		} else {
			wp_die( 'Do not access this page directly (Redsys redirección Lite)' );
		}
	}
	/**
	 * Successful Payment!
	 *
	 * @param array $posted posted data.
	 * @return void
	 */
	public function successful_request( $posted ) {
		global $woocommerce;

		if ( 'yes' === $this->testmode ) {
			$usesecretsha256 = $this->customtestsha256;
			if ( ! empty( $usesecretsha256 ) ) {
				$usesecretsha256 = $this->customtestsha256;
			} else {
				$usesecretsha256 = $this->secretsha256;
			}
		} else {
			$usesecretsha256 = $this->secretsha256;
		}

		$version           = sanitize_text_field( wp_unslash( $_POST['Ds_SignatureVersion'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$data              = sanitize_text_field( wp_unslash( $_POST['Ds_MerchantParameters'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$remote_sign       = sanitize_text_field( wp_unslash( $_POST['Ds_Signature'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$mi_obj            = new RedsysAPI();
		$usesecretsha256   = $this->secretsha256;
		$dscardnumbercompl = '';
		$dsexpiration      = '';
		$dsmerchantidenti  = '';
		$dscardnumber4     = '';
		$dsexpiryyear      = '';
		$dsexpirymonth     = '';
		$decodedata        = $mi_obj->decode_merchant_parameters( $data );
		$localsecret       = $mi_obj->create_merchant_signature_notif( $usesecretsha256, $data );
		$total             = $mi_obj->get_parameter( 'Ds_Amount' );
		$ordermi           = $mi_obj->get_parameter( 'Ds_Order' );
		$dscode            = $mi_obj->get_parameter( 'Ds_MerchantCode' );
		$currency_code     = $mi_obj->get_parameter( 'Ds_Currency' );
		$response          = $mi_obj->get_parameter( 'Ds_Response' );
		$id_trans          = $mi_obj->get_parameter( 'Ds_AuthorisationCode' );
		$dsdate            = htmlspecialchars_decode( $mi_obj->get_parameter( 'Ds_Date' ) );
		$dshour            = htmlspecialchars_decode( $mi_obj->get_parameter( 'Ds_Hour' ) );
		$dstermnal         = $mi_obj->get_parameter( 'Ds_Terminal' );
		$dssucurepayment   = $mi_obj->get_parameter( 'Ds_SecurePayment' );
		$dscardcountry     = $mi_obj->get_parameter( 'Ds_Card_Country' );
		$dsconsumercountry = $mi_obj->get_parameter( 'Ds_ConsumerLanguage' );
		$dstransactiontype = $mi_obj->get_parameter( 'Ds_TransactionType' );
		$dsmerchantidenti  = $mi_obj->get_parameter( 'Ds_Merchant_Identifier' );
		$dscardbrand       = $mi_obj->get_parameter( 'Ds_Card_Brand' );
		$dscargtype        = $mi_obj->get_parameter( 'Ds_Card_Type' );
		$dserrorcode       = $mi_obj->get_parameter( 'Ds_ErrorCode' );
		$dpaymethod        = $mi_obj->get_parameter( 'Ds_PayMethod' ); // D o R, D: Domiciliacion, R: Transferencia.
		$order1            = $ordermi;
		$order2            = WCRedL()->clean_order_number( $ordermi );
		$order             = $this->get_redsys_order( (int) $order2 );
		$is_paid           = WCRedL()->is_paid( $order->get_id() );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', 'Ds_Amount: ' . $total . ', Ds_Order: ' . $order1 . ',	Ds_MerchantCode: ' . $dscode . ', Ds_Currency: ' . $currency_code . ', Ds_Response: ' . $response . ', Ds_AuthorisationCode: ' . $id_trans . ', $order2: ' . $order2 );
		}

		$response = intval( $response );

		// refund.

		if ( '3' === $dstransactiontype ) {
			if ( 900 === $response ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Response 900 (refund)' );
				}
				set_transient( $order->get_id() . '_redsys_refund', 'yes' );

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'WCRedL()->update_order_meta to "refund yes"' );
				}
				$status = $order->get_status();
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'New Status in request: ' . $status );
				}
				$order->add_order_note( __( 'Order Payment refunded', 'woo-redsys-gateway-light' ) );
				return;
			}
			$order->add_order_note( __( 'There was an error refunding', 'woo-redsys-gateway-light' ) );
			exit;
		}
		if ( 'yes' === $this->debug ) {
			if ( $is_paid ) {
				$this->log->add( 'redsys', 'Order is Paid: TRUE' );
			} else {
				$this->log->add( 'redsys', 'Order is Paid: FALSE' );
			}
		}
		if ( $is_paid ) {
			exit();
		}
		if ( $response <= 99 ) {
			// authorized.
			$order_total_compare = number_format( $order->get_total(), 2, '', '' );
			// remove 0 from bigining.
			$order_total_compare = ltrim( $order_total_compare, '0' );
			$total               = ltrim( $total, '0' );
			if ( $order_total_compare !== $total ) {
				// amount does not match.
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Payment error: Amounts do not match (order: ' . $order_total_compare . ' - received: ' . $total . ')' );
				}
				// Put this order on-hold for manual checking.
				/* translators: order an received are the amount */
				$order->update_status( 'on-hold', sprintf( __( 'Validation error: Order vs. Notification amounts do not match (order: %1$s - received: %2&s).', 'woo-redsys-gateway-light' ), $order_total_compare, $total ) );
				exit;
			}
			$authorisation_code = $id_trans;
			if ( ! empty( $order1 ) ) {
				WCRedL()->update_order_meta( $order->get_id(), '_payment_order_number_redsys', $order1 );
			}
			if ( ! empty( $dsdate ) ) {
				WCRedL()->update_order_meta( $order->get_id(), '_payment_date_redsys', $dsdate );
			}
			if ( ! empty( $dshour ) ) {
				WCRedL()->update_order_meta( $order->get_id(), '_payment_hour_redsys', $dshour );
			}
			if ( ! empty( $id_trans ) ) {
				WCRedL()->update_order_meta( $order->get_id(), '_authorisation_code_redsys', $authorisation_code );
			}
			if ( ! empty( $dscardcountry ) ) {
				WCRedL()->update_order_meta( $order->get_id(), '_card_country_redsys', $dscardcountry );
			}
			if ( ! empty( $dscargtype ) ) {
				WCRedL()->update_order_meta( $order->get_id(), '_card_type_redsys', 'C' === $dscargtype ? 'Credit' : 'Debit' );
			}
			// Payment completed.
			$order->add_order_note( __( 'HTTP Notification received - payment completed', 'woo-redsys-gateway-light' ) );
			$order->add_order_note( __( 'Authorization code: ', 'woo-redsys-gateway-light' ) . $authorisation_code );
			$order->payment_complete();
			if ( 'completed' === $this->orderdo ) {
				$order->update_status( 'completed', __( 'Order Completed by Redsys', 'woo-redsys-gateway-light' ) );
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'Payment complete.' );
			}
		} else {
			// Tarjeta caducada.
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'Order cancelled by Redsys' );
			}
			// Order cancelled.
			$order->update_status( 'cancelled', __( 'Cancelled by Redsys', 'woo-redsys-gateway-light' ) );
			$order->add_order_note( __( 'Order cancelled by Redsys', 'woo-redsys-gateway-light' ) );
			WC()->cart->empty_cart();
		}
	}
	/**
	 * Get_redsys_order function.
	 *
	 * @param int $order_id order id.
	 */
	public function get_redsys_order( $order_id ) {
		$order = new WC_Order( $order_id );
		return $order;
	}
	/**
	 * Ask for refund
	 *
	 * @param int $order_id order id.
	 * @param int $transaction_id transaction id.
	 * @param int $amount amount.
	 */
	public function ask_for_refund( $order_id, $transaction_id, $amount ) {

		// post code to REDSYS.
		$order          = $this->get_redsys_order( $order_id );
		$terminal       = $this->terminal;
		$currency_codes = WCRedL()->get_currencies();
		$currencycode   = $currency_codes[ get_woocommerce_currency() ];
		$user_id        = $order->get_user_id();
		if ( 'yes' === $this->testmode ) {
			$secretsha256 = $this->customtestsha256;
			if ( ! empty( $secretsha256 ) ) {
				$secretsha256 = $this->customtestsha256;
			} else {
				$secretsha256 = $this->secretsha256;
			}
		} else {
			$secretsha256 = $this->secretsha256;
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/**************************/' );
			$this->log->add( 'redsys', __( 'Starting asking for Refund', 'woo-redsys-gateway-light' ) );
			$this->log->add( 'redsys', '/**************************/' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', __( 'Terminal : ', 'woo-redsys-gateway-light' ) . $terminal );
		}
		$transaction_type  = '3';
		$secretsha256_meta = WCRedL()->get_order_meta( $order_id, '_redsys_secretsha256', true );
		if ( 'yes' === $this->testmode ) {
			$usesecretsha256 = $this->customtestsha256;
			if ( ! empty( $usesecretsha256 ) ) {
				$usesecretsha256 = $this->customtestsha256;
			} else {
				$usesecretsha256 = $this->secretsha256;
			}
		} else {
			$usesecretsha256 = $this->secretsha256;
		}
		if ( 'yes' === $this->not_use_https ) {
			$final_notify_url = $this->notify_url_not_https;
		} else {
			$final_notify_url = $this->notify_url;
		}

			$redsys_adr        = $this->get_redsys_url_gateway_rest();
			$autorization_code = WCRedL()->get_order_meta( $order_id, '_authorisation_code_redsys', true );
			$autorization_date = WCRedL()->get_order_meta( $order_id, '_payment_date_redsys', true );
			$currencycode      = WCRedL()->get_order_meta( $order_id, '_corruncy_code_redsys', true );
			$merchant_module   = 'WooCommerce_Redsys_Gateway_Light_' . REDSYS_WOOCOMMERCE_VERSION . '_WordPress.org';

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', __( 'All data from meta', 'woo-redsys-gateway-light' ) );
			$this->log->add( 'redsys', '**********************' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', __( 'If something is empty, the data was not saved', 'woo-redsys-gateway-light' ) );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', __( 'All data from meta', 'woo-redsys-gateway-light' ) );
			$this->log->add( 'redsys', __( 'Authorization Code : ', 'woo-redsys-gateway-light' ) . $autorization_code );
			$this->log->add( 'redsys', __( 'Authorization Date : ', 'woo-redsys-gateway-light' ) . $autorization_date );
			$this->log->add( 'redsys', __( 'Currency Codey : ', 'woo-redsys-gateway-light' ) . $currencycode );
			$this->log->add( 'redsys', __( 'Terminal : ', 'woo-redsys-gateway-light' ) . $terminal );
			$this->log->add( 'redsys', __( 'SHA256 : ', 'woo-redsys-gateway-light' ) . $secretsha256_meta );
		}

		if ( ! empty( $currencycode ) ) {
			$currency = $currencycode;
		} elseif ( empty( $currencycode ) ) {
			$currency = $currency_codes[ get_woocommerce_currency() ];
		}
		$merchant_module = 'WooCommerce_Redsys_Gateway_Light_' . REDSYS_WOOCOMMERCE_VERSION;

		$mi_obj = new RedsysAPI();

		$mi_obj->set_parameter( 'DS_MERCHANT_AMOUNT', $amount );
		$mi_obj->set_parameter( 'DS_MERCHANT_ORDER', $transaction_id );
		$mi_obj->set_parameter( 'DS_MERCHANT_MERCHANTCODE', $this->customer );
		$mi_obj->set_parameter( 'DS_MERCHANT_CURRENCY', $currency );
		$mi_obj->set_parameter( 'DS_MERCHANT_TRANSACTIONTYPE', $transaction_type );
		$mi_obj->set_parameter( 'DS_MERCHANT_TERMINAL', $terminal );
		$mi_obj->set_parameter( 'DS_MERCHANT_MERCHANTURL', $final_notify_url );
		$mi_obj->set_parameter( 'DS_MERCHANT_URLOK', add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
		$mi_obj->set_parameter( 'DS_MERCHANT_URLKO', $order->get_cancel_order_url() );
		$mi_obj->set_parameter( 'DS_MERCHANT_CONSUMERLANGUAGE', '001' );
		$mi_obj->set_parameter( 'DS_MERCHANT_PRODUCTDESCRIPTION', __( 'Order', 'woo-redsys-gateway-light' ) . ' ' . $order->get_order_number() );
		$mi_obj->set_parameter( 'DS_MERCHANT_MERCHANTNAME', $this->commercename );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', __( 'Data sent to Redsys for refund', 'woo-redsys-gateway-light' ) );
			$this->log->add( 'redsys', '*********************************' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', __( 'URL to Redsys : ', 'woo-redsys-gateway-light' ) . $redsys_adr );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_AMOUNT : ', 'woo-redsys-gateway-light' ) . $amount );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_ORDER : ', 'woo-redsys-gateway-light' ) . $transaction_id );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_MERCHANTCODE : ', 'woo-redsys-gateway-light' ) . $this->customer );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_CURRENCY : ', 'woo-redsys-gateway-light' ) . $currency );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_TRANSACTIONTYPE : ', 'woo-redsys-gateway-light' ) . $transaction_type );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_TERMINAL : ', 'woo-redsys-gateway-light' ) . $terminal );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_MERCHANTURL : ', 'woo-redsys-gateway-light' ) . $final_notify_url );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_URLOK : ', 'woo-redsys-gateway-light' ) . add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_URLKO : ', 'woo-redsys-gateway-light' ) . $order->get_cancel_order_url() );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_CONSUMERLANGUAGE : 001', 'woo-redsys-gateway-light' ) );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_PRODUCTDESCRIPTION : ', 'woo-redsys-gateway-light' ) . __( 'Order', 'woo-redsys-gateway-light' ) . ' ' . $order->get_order_number() );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_MERCHANTNAME : ', 'woo-redsys-gateway-light' ) . $this->commercename );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_AUTHORISATIONCODE : ', 'woo-redsys-gateway-light' ) . $autorization_code );
			$this->log->add( 'redsys', __( 'Ds_Merchant_TransactionDate : ', 'woo-redsys-gateway-light' ) . $autorization_date );
			$this->log->add( 'redsys', __( 'ask_for_refund Asking for order #: ', 'woo-redsys-gateway-light' ) . $order_id );
			$this->log->add( 'redsys', ' ' );
		}

		$version   = 'HMAC_SHA256_V1';
		$request   = '';
		$params    = $mi_obj->create_merchant_parameters();
		$signature = $mi_obj->create_merchant_signature( $secretsha256 );

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
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', '$post_arg: ' . print_r( $post_arg, true ) );
		}
		if ( is_wp_error( $post_arg ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', __( 'There is an error', 'woo-redsys-gateway-light' ) );
				$this->log->add( 'redsys', '*********************************' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', __( 'The error is : ', 'woo-redsys-gateway-light' ) . $post_arg );
			}
			return $post_arg;
		}
		return true;
	}
	/**
	 * Check for Redsys Refund
	 *
	 * @param int $order_id order id.
	 */
	public function check_redsys_refund( $order_id ) {
		// check postmeta.
		$order        = wc_get_order( (int) $order_id );
		$order_refund = get_transient( $order->get_id() . '_redsys_refund' );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', __( 'Checking and waiting ping from Redsys', 'woo-redsys-gateway-light' ) );
			$this->log->add( 'redsys', '*****************************************' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', __( 'Check order status #: ', 'woo-redsys-gateway-light' ) . $order->get_id() );
			$this->log->add( 'redsys', __( 'Check order status with get_transient: ', 'woo-redsys-gateway-light' ) . $order_refund );
		}
		if ( 'yes' === $order_refund ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Process the refund and return the result
	 *
	 * @param int    $order_id order id.
	 * @param int    $amount amount.
	 * @param string $reason reason.
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {

		// Do your refund here. Refund $amount for the order with ID $order_id _transaction_id.
		set_time_limit( 0 );
		$order = wc_get_order( $order_id );

		$transaction_id = WCRedL()->get_order_meta( $order_id, '_payment_order_number_redsys', true );
		if ( ! $amount ) {
			$order_total      = number_format( $order->get_total(), 2, ',', '' );
			$order_total_sign = number_format( $order->get_total(), 2, '', '' );
		} else {
			$order_total_sign = number_format( $amount, 2, '', '' );
		}

		if ( ! empty( $transaction_id ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '       Once upon a time       ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', __( 'check_redsys_refund Asking for order #: ', 'woo-redsys-gateway-light' ) . $order_id );
			}

			$refund_asked = $this->ask_for_refund( $order_id, $transaction_id, $order_total_sign );

			if ( is_wp_error( $refund_asked ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', __( 'Refund Failed: ', 'woo-redsys-gateway-light' ) . $refund_asked->get_error_message() );
				}
				return new WP_Error( 'error', $refund_asked->get_error_message() );
			}
			$x = 0;
			do {
				sleep( 5 );
				$result = $this->check_redsys_refund( $order_id );
				++$x;
			} while ( $x <= 20 && false === $result );
			if ( 'yes' === $this->debug && $result ) {
				$this->log->add( 'redsys', __( 'check_redsys_refund = true ', 'woo-redsys-gateway-light' ) . $result );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/********************************/' );
				$this->log->add( 'redsys', '  Refund complete by Redsys   ' );
				$this->log->add( 'redsys', '/********************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/******************************************/' );
				$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'redsys', '/******************************************/' );
				$this->log->add( 'redsys', ' ' );
			}
			if ( 'yes' === $this->debug && ! $result ) {
				$this->log->add( 'redsys', __( 'check_redsys_refund = false ', 'woo-redsys-gateway-light' ) . $result );
			}
			if ( $result ) {
				delete_transient( $order->get_id() . '_redsys_refund' );
				return true;
			} else {
				if ( 'yes' === $this->debug && $result ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
					$this->log->add( 'redsys', __( '!!!!Refund Failed, please try again!!!!', 'woo-redsys-gateway-light' ) );
					$this->log->add( 'redsys', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/******************************************/' );
					$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
					$this->log->add( 'redsys', '/******************************************/' );
					$this->log->add( 'redsys', ' ' );
				}
				return false;
			}
		} else {
			if ( 'yes' === $this->debug && $result ) {
				$this->log->add( 'redsys', __( 'Refund Failed: No transaction ID', 'woo-redsys-gateway-light' ) );
			}
			return new WP_Error( 'error', __( 'Refund Failed: No transaction ID', 'woo-redsys-gateway-light' ) );
		}
	}
	/**
	 * Check if this gateway is enabled and available in the user's country
	 */
	public function warning_checkout_test_mode() {
		if ( 'yes' === $this->testmode && WCRedL()->is_gateway_enabled( $this->id ) ) {
			echo '<div class="checkout-message" style="
				background-color: #f39c12;
				padding: 1em 1.618em;
				margin-bottom: 2.617924em;
				margin-left: 0;
				border-radius: 2px;
				color: #fff;
				clear: both;
				border-left: 0.6180469716em solid rgb(228, 120, 51);
				">';
			echo esc_html__( 'Warning: WooCommerce Redsys Gateway Light is in test mode. Remember to uncheck it when you go live', 'woo-redsys-gateway-light' );
			echo '</div>';
		}
	}
}
