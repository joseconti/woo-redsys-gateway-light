<?php
/**
 * Bizum Lite for WooCommerce
 *
 * @package Bizum Lite for WooCommerce
 * @subpackage Bizum Lite for WooCommerce
 * @version 6.0.0
 * @since 6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Bizum Lite for WooCommerce
 */
class WC_Gateway_Bizum_Redsys extends WC_Payment_Gateway {

	/**
	 * Public $id
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Public $icon
	 *
	 * @var string
	 */
	public $icon;

	/**
	 * Public $has_fields
	 *
	 * @var bool
	 */
	public $has_fields;

	/**
	 * Public $liveurl
	 *
	 * @var string
	 */
	public $liveurl;

	/**
	 * Public $testurl
	 *
	 * @var string
	 */
	public $testurl;

	/**
	 * Public $liveurlws
	 *
	 * @var string
	 */
	public $liveurlws;

	/**
	 * Public $testurlws
	 *
	 * @var string
	 */
	public $testurlws;

	/**
	 * Public $testsha256
	 *
	 * @var string|null
	 */
	public $testsha256;

	/**
	 * Public $testmode
	 *
	 * @var string|bool
	 */
	public $testmode;

	/**
	 * Public $method_title
	 *
	 * @var string
	 */
	public $method_title;

	/**
	 * Public $method_description
	 *
	 * @var string
	 */
	public $method_description;

	/**
	 * Public $not_use_https
	 *
	 * @var string|bool
	 */
	public $not_use_https;

	/**
	 * Public $notify_url
	 *
	 * @var string
	 */
	public $notify_url;

	/**
	 * Public $notify_url_not_https
	 *
	 * @var string
	 */
	public $notify_url_not_https;

	/**
	 * Public $log
	 *
	 * @var WC_Logger|null
	 */
	public $log;

	/**
	 * Public $supports
	 *
	 * @var array
	 */
	public $supports;

	/**
	 * Public $debug
	 *
	 * @var string|bool
	 */
	public $debug;

	/**
	 * Public $testforuser
	 *
	 * @var string|null
	 */
	public $testforuser;

	/**
	 * Public $testforuserid
	 *
	 * @var string|null
	 */
	public $testforuserid;

	/**
	 * Public $buttoncheckout
	 *
	 * @var string|null
	 */
	public $buttoncheckout;

	/**
	 * Public $butonbgcolor
	 *
	 * @var string|null
	 */
	public $butonbgcolor;

	/**
	 * Public $butontextcolor
	 *
	 * @var string|null
	 */
	public $butontextcolor;

	/**
	 * Public $orderdo
	 *
	 * @var string|null
	 */
	public $orderdo;

	/**
	 * Public $redsyslanguage
	 *
	 * @var string|null
	 */
	public $redsyslanguage;

	/**
	 * Public $title
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Public $description
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Public $logo
	 *
	 * @var string|null
	 */
	public $logo;

	/**
	 * Public $customer
	 *
	 * @var string|null
	 */
	public $customer;

	/**
	 * Public $transactionlimit
	 *
	 * @var string|null
	 */
	public $transactionlimit;

	/**
	 * Public $commercename
	 *
	 * @var string|null
	 */
	public $commercename;

	/**
	 * Public $terminal
	 *
	 * @var string|null
	 */
	public $terminal;

	/**
	 * Public $secretsha256
	 *
	 * @var string|null
	 */
	public $secretsha256;

	/**
	 * Public $customtestsha256
	 *
	 * @var string|null
	 */
	public $customtestsha256;

	/**
	 * Public enabled
	 *
	 * @var bool
	 */
	public $enabled;
	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	public function __construct() {

		$this->id = 'bizumredsys';
		$logo_url = $this->get_option( 'logo' );
		if ( ! empty( $logo_url ) ) {
			$logo_url = $this->get_option( 'logo' );
			/**
			 * Filter the icon for the WooCommerce Bizum gateway.
			 *
			 * @param string $icon_url The URL of the icon.
			 * @return string The filtered icon URL.
			 * @since 1.0.0
			 */
			$this->icon = apply_filters( 'woocommerce_' . $this->id . '_icon', $logo_url );
		} else {
			/**
			 * Filter the icon for the WooCommerce Bizum gateway.
			 *
			 * @param string $icon_url The URL of the icon.
			 * @return string The filtered icon URL.
			 * @since 1.0.0
			 */
			$this->icon = apply_filters( 'woocommerce_' . $this->id . '_icon', REDSYS_PLUGIN_URL . 'assets/images/bizum.png' );
		}
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
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		// Define user set variables.
		$this->title            = $this->get_option( 'title' );
		$this->description      = $this->get_option( 'description' );
		$this->logo             = $this->get_option( 'logo' );
		$this->customer         = $this->get_option( 'customer' );
		$this->transactionlimit = $this->get_option( 'transactionlimit' );
		$this->commercename     = $this->get_option( 'commercename' );
		$this->terminal         = $this->get_option( 'terminal' );
		$this->secretsha256     = $this->get_option( 'secretsha256' );
		$this->customtestsha256 = $this->get_option( 'customtestsha256' );
		$this->redsyslanguage   = $this->get_option( 'redsyslanguage' );
		$this->debug            = $this->get_option( 'debug' );
		$this->testforuser      = $this->get_option( 'testforuser' );
		$this->testforuserid    = $this->get_option( 'testforuserid' );
		$this->buttoncheckout   = $this->get_option( 'buttoncheckout' );
		$this->butonbgcolor     = $this->get_option( 'butonbgcolor' );
		$this->butontextcolor   = $this->get_option( 'butontextcolor' );
		$this->orderdo          = $this->get_option( 'orderdo' );
		$this->log              = new WC_Logger();
		$this->supports         = array(
			'products',
			'refunds',
		);
		// Actions.
		add_action( 'valid_' . $this->id . '_standard_ipn_request', array( $this, 'successful_request' ) );
		add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'warning_checkout_test_mode_bizum' ) );
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'disable_bizum' ) );

		// Payment listener/API hook.
		add_action( 'woocommerce_api_wc_gateway_' . $this->id, array( $this, 'check_ipn_response' ) );

		if ( ! $this->is_valid_for_use() ) {
			$this->enabled = false;
		}
	}
	/**
	 * Check if this gateway is valid for use.
	 *
	 * @since 1.0.0
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
			<h3><?php esc_html_e( 'Bizum', 'woo-redsys-gateway-light' ); ?></h3>
			<p><?php esc_html_e( 'Bizum works by sending the user to Bizum Gateway', 'woo-redsys-gateway-light' ); ?></p>
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
	 * @return void
	 */
	public function init_form_fields() {

		$options    = array();
		$selections = (array) $this->get_option( 'testforuserid' );

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
			'enabled'          => array(
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
			'terminal'         => array(
				'title'       => __( 'Terminal number', 'woo-redsys-gateway-light' ),
				'type'        => 'text',
				'description' => __( 'Terminal number provided by your bank.', 'woo-redsys-gateway-light' ),
				'desc_tip'    => true,
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
				'label'       => __( 'Enable logging', 'woo-redsys-gateway-light' ),
				'default'     => 'no',
				'description' => __( 'Log Bizum events, such as notifications requests, inside <code>WooCommerce > Status > Logs > bizum-{date}-{number}.log</code>', 'woo-redsys-gateway-light' ),
			),
		);
		$redsyslanguages   = WCRedL()->get_redsys_languages();

		foreach ( $redsyslanguages as $redsyslanguage => $valor ) {
			$this->form_fields['redsyslanguage']['options'][ $redsyslanguage ] = $valor;
		}
	}
	/**
	 * Check user test mode
	 *
	 * @param string $userid User ID.
	 * @return boolean
	 */
	public function check_user_test_mode( $userid ) {

		$usertest_active = $this->testforuser;
		$selections      = (array) $this->get_option( 'testforuserid' );
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
						$this->log->add( 'bizumredsys', '   Checking user ' . $userid );
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
	 * Disable bizum
	 *
	 * @param array $available_gateways Available gateways.
	 *
	 * @return array
	 */
	public function disable_bizum( $available_gateways ) {

		if ( ! is_admin() && is_checkout() ) {
			$total = (int) WC()->cart->total;
			$limit = (int) $this->transactionlimit;
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
	 * Get the URL for the gateway
	 *
	 * @param int    $user_id User ID for the order.
	 * @param string $type Type of gateway.
	 */
	public function get_redsys_url_gateway( $user_id, $type = 'rd' ) {

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
			} elseif ( 'rd' === $type ) {
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
		return $url;
	}
	/**
	 * Get the SHA256 for the user
	 *
	 * @param int $user_id User ID for the order.
	 * @return string
	 */
	public function get_redsys_sha256( $user_id ) {

		if ( 'yes' === $this->testmode ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '/****************************/' );
				$this->log->add( 'bizumredsys', '         SHA256 Test.         ' );
				$this->log->add( 'bizumredsys', '/****************************/' );
				$this->log->add( 'bizumredsys', ' ' );
			}
			$customtestsha256 = mb_convert_encoding( $this->customtestsha256, 'ISO-8859-1', 'UTF-8' );
			if ( ! empty( $customtestsha256 ) ) {
				$sha256 = $customtestsha256;
			} else {
				$sha256 = mb_convert_encoding( $this->testsha256, 'ISO-8859-1', 'UTF-8' );
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
				$customtestsha256 = mb_convert_encoding( $this->customtestsha256, 'ISO-8859-1', 'UTF-8' );
				if ( ! empty( $customtestsha256 ) ) {
					$sha256 = $customtestsha256;
				} else {
					$sha256 = mb_convert_encoding( $this->testsha256, 'ISO-8859-1', 'UTF-8' );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', '     USER SHA256 NOT Test.    ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', ' ' );
				}
				$sha256 = mb_convert_encoding( $this->secretsha256, 'ISO-8859-1', 'UTF-8' );
			}
		}
		return $sha256;
	}
	/**
	 * Get the redsys args
	 *
	 * @param object $order Order object.
	 * @return array
	 */
	public function get_redsys_args( $order ) {

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
		$nombr_apellidos = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
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
		$mi_obj->set_parameter( 'DS_MERCHANT_PRODUCTDESCRIPTION', WCRedL()->product_description( $order, $this->id ) );
		$mi_obj->set_parameter( 'DS_MERCHANT_MERCHANTNAME', $this->commercename );
		$mi_obj->set_parameter( 'DS_MERCHANT_PAYMETHODS', 'z' );

		$version = 'HMAC_SHA256_V1';
		// Se generan los parámetros de la petición.
		$request      = '';
		$params       = $mi_obj->create_merchant_parameters();
		$signature    = $mi_obj->create_merchant_signature( $secretsha256 );
		$order_id_set = $transaction_id2;
		set_transient( 'redsys_signature_' . sanitize_title( $order_id_set ), $secretsha256, 600 );
		$redsys_args = array(
			'Ds_SignatureVersion'   => $version,
			'Ds_MerchantParameters' => $params,
			'Ds_Signature'          => $signature,
		);
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', 'Generating payment form for order ' . $order->get_order_number() . '. Sent data: ' . print_r( $redsys_args, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$this->log->add( 'bizumredsys', 'Helping to understand the encrypted code: ' );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_AMOUNT: ' . $order_total_sign );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_ORDER: ' . $transaction_id2 );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_MERCHANTCODE: ' . $this->customer );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_CURRENCY: ' . $currency_codes[ get_woocommerce_currency() ] );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_TRANSACTIONTYPE: ' . $transaction_type );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_TERMINAL: ' . $dsmerchantterminal );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_MERCHANTURL: ' . $final_notify_url );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_URLOK: ' . add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_URLKO: ' . $returnfromredsys );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_CONSUMERLANGUAGE: ' . $gatewaylanguage );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_PRODUCTDESCRIPTION: ' . WCRedL()->product_description( $order, $this->id ) );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_PAYMETHODS: z' );
		}
		/**
		 * Filter the redsys args.
		 *
		 * @since 2.0.0
		 */
		$redsys_args = apply_filters( 'woocommerce_redsys_args', $redsys_args );
		return $redsys_args;
	}

	/**
	 * Generate the redsys form
	 *
	 * @param mixed $order_id Order ID.
	 * @return string
	 */
	public function generate_redsys_form( $order_id ) {
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
		jQuery("#submit_redsys_payment_form").click();
		'
		);
		return '<form action="' . esc_url( $redsys_adr ) . '" method="post" id="redsys_payment_form" target="_top">
		' . implode( '', $form_inputs ) . '
		<input type="submit" class="button-alt" id="submit_redsys_payment_form" value="' . __( 'Pay with Bizum', 'woo-redsys-gateway-light' ) . '" /> <a class="button cancel" href="' . esc_url( $order->get_cancel_order_url() ) . '">' . __( 'Cancel order &amp; restore cart', 'woo-redsys-gateway-light' ) . '</a>
		</form>';
	}
	/**
	 * Process the payment and return the result
	 *
	 * @param int $order_id Order ID.
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = WCRedL()->get_order( $order_id );
		return array(
			'result'   => 'success',
			'redirect' => $order->get_checkout_payment_url( true ),
		);
	}

	/**
	 * Output for the order received page.
	 *
	 * @param object $order Order object.
	 *
	 * @return void
	 */
	public function receipt_page( $order ) {
		$allowed_html = array(
			'form'  => array(
				'action' => array(),
				'method' => array(),
				'id'     => array(),
				'target' => array(),
			),
			'input' => array(
				'type'  => array(),
				'class' => array(),
				'id'    => array(),
				'value' => array(),
				'name'  => array(),
			),
			'a'     => array(
				'class' => array(),
				'href'  => array(),
			),
		);
		echo '<p>' . esc_html__( 'Thank you for your order, please click the button below to pay with Bizum.', 'woo-redsys-gateway-light' ) . '</p>';
		echo wp_kses( $this->generate_redsys_form( $order ), $allowed_html );
	}

	/**
	 * Check redsys IPN validity
	 **/
	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	public function check_ipn_request_is_valid() {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', 'HTTP Notification received: ' . print_r( $_POST, true ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.PHP.DevelopmentFunctions.error_log_print_r
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
				// Sanitize and process the data.

				if ( isset( $_POST['Ds_MerchantParameters'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
					$data = sanitize_text_field( wp_unslash( $_POST['Ds_MerchantParameters'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
					// Sanitize and process the data.
				}

				if ( isset( $_POST['Ds_Signature'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
					$remote_sign = sanitize_text_field( wp_unslash( $_POST['Ds_Signature'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
				}
				// Sanitize and process the data.
			}
			$mi_obj            = new RedsysAPI();
			$decodec           = $mi_obj->decode_merchant_parameters( $data );
			$order_id          = $mi_obj->get_parameter( 'Ds_Order' );
			$secretsha256      = get_transient( 'redsys_signature_' . sanitize_title( $order_id ) );
			$order1            = $order_id;
			$order2            = WCRedL()->clean_order_number( $order1 );
			$secretsha256_meta = WCRedL()->get_order_meta( $order2, '_redsys_secretsha256', true );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', 'Signature from Redsys: ' . $remote_sign );
				$this->log->add( 'bizumredsys', 'Name transient remote: redsys_signature_' . sanitize_title( $order_id ) );
				$this->log->add( 'bizumredsys', 'Secret SHA256 transcient: ' . $secretsha256 );
				$this->log->add( 'bizumredsys', ' ' );
			}

			if ( 'yes' === $this->debug ) {
				$order_id = $mi_obj->get_parameter( 'Ds_Order' );
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
			$localsecret = $mi_obj->create_merchant_signature_notif( $usesecretsha256, $data );
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
				$this->log->add( 'bizumredsys', 'Received INVALID notification from Servired/RedSys' );
				$this->log->add( 'bizumredsys', '$remote_sign: ' . $remote_sign );
				$this->log->add( 'bizumredsys', '$localsecret: ' . $localsecret );
			}
			return false;
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
	public function check_ipn_response() {
		@ob_clean(); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$post = stripslashes_deep( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( $this->check_ipn_request_is_valid() ) {
			header( 'HTTP/1.1 200 OK' );
			/**
			 * Action hook to validate the Bizum HTTP Notification
			 *
			 * @param array $post
			 * @return void
			 * @since 2.0.0
			 */
			do_action( 'valid_' . $this->id . '_standard_ipn_request', $post );
		} else {
			wp_die( 'Do not access this page directly (Bizum Lite)' );
		}
	}
	/**
	 * Successful Payment!
	 *
	 * @param array $posted Post data successsful request.
	 * @return void
	 */
	public function successful_request( $posted ) {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', '/****************************/' );
			$this->log->add( 'bizumredsys', '      successful_request      ' );
			$this->log->add( 'bizumredsys', '/****************************/' );
			$this->log->add( 'bizumredsys', ' ' );
		}

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

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', '$version: ' . $version );
			$this->log->add( 'bizumredsys', '$data: ' . $data );
			$this->log->add( 'bizumredsys', '$remote_sign: ' . $remote_sign );
			$this->log->add( 'bizumredsys', ' ' );
		}

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
		$dsmerchandata     = $mi_obj->get_parameter( 'Ds_MerchantData' );
		$dssucurepayment   = $mi_obj->get_parameter( 'Ds_SecurePayment' );
		$dscardcountry     = $mi_obj->get_parameter( 'Ds_Card_Country' );
		$dsconsumercountry = $mi_obj->get_parameter( 'Ds_ConsumerLanguage' );
		$dstransactiontype = $mi_obj->get_parameter( 'Ds_TransactionType' );
		$dsmerchantidenti  = $mi_obj->get_parameter( 'Ds_Merchant_Identifier' );
		$dscardbrand       = $mi_obj->get_parameter( 'Ds_Card_Brand' );
		$dsmechandata      = $mi_obj->get_parameter( 'Ds_MerchantData' );
		$dscargtype        = $mi_obj->get_parameter( 'Ds_Card_Type' );
		$dserrorcode       = $mi_obj->get_parameter( 'Ds_ErrorCode' );
		$dpaymethod        = $mi_obj->get_parameter( 'Ds_PayMethod' ); // D o R, D: Domiciliacion, R: Transferencia.
		$response          = intval( $response );
		$secretsha256      = get_transient( 'redsys_signature_' . sanitize_title( $ordermi ) );
		$order1            = $ordermi;
		$order2            = WCRedL()->clean_order_number( $order1 );
		$order             = WCRedL()->get_order( (int) $order2 );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', 'SHA256 Settings: ' . $usesecretsha256 );
			$this->log->add( 'bizumredsys', 'SHA256 Transcient: ' . $secretsha256 );
			$this->log->add( 'bizumredsys', 'decode_merchant_parameters: ' . $decodedata );
			$this->log->add( 'bizumredsys', 'create_merchant_signature_notif: ' . $localsecret );
			$this->log->add( 'bizumredsys', 'Ds_Amount: ' . $total );
			$this->log->add( 'bizumredsys', 'Ds_Order: ' . $ordermi );
			$this->log->add( 'bizumredsys', '$order_id: ' . $order2 );
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
					$this->log->add( 'bizumredsys', 'WCRedL()->update_order_meta to "refund yes"' );
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
				WCRedL()->update_order_meta( $order->get_id(), '_payment_order_number_redsys', $order1 );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_payment_order_number_redsys saved: ' . $order1 );
				}
			} elseif ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '_payment_order_number_redsys NOT SAVED!!!' );
				$this->log->add( 'bizumredsys', ' ' );
			}
			if ( ! empty( $dsdate ) ) {
				WCRedL()->update_order_meta( $order->get_id(), '_payment_date_redsys', $dsdate );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_payment_date_redsys saved: ' . $dsdate );
				}
			} elseif ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '_payment_date_redsys NOT SAVED!!!' );
				$this->log->add( 'bizumredsys', ' ' );
			}
			if ( ! empty( $dstermnal ) ) {
				WCRedL()->update_order_meta( $order->get_id(), '_payment_terminal_redsys', $dstermnal );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_payment_terminal_redsys saved: ' . $dstermnal );
				}
			} elseif ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '_payment_terminal_redsys NOT SAVED!!!' );
				$this->log->add( 'bizumredsys', ' ' );
			}
			if ( ! empty( $dshour ) ) {
				WCRedL()->update_order_meta( $order->get_id(), '_payment_hour_redsys', $dshour );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_payment_hour_redsys saved: ' . $dshour );
				}
			} elseif ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '_payment_hour_redsys NOT SAVED!!!' );
				$this->log->add( 'bizumredsys', ' ' );
			}
			if ( ! empty( $id_trans ) ) {
				WCRedL()->update_order_meta( $order->get_id(), '_authorisation_code_redsys', $authorisation_code );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_authorisation_code_redsys saved: ' . $authorisation_code );
				}
			} elseif ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '_authorisation_code_redsys NOT SAVED!!!' );
				$this->log->add( 'bizumredsys', ' ' );
			}
			if ( ! empty( $currency_code ) ) {
				WCRedL()->update_order_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_corruncy_code_redsys saved: ' . $currency_code );
				}
			} elseif ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '_corruncy_code_redsys NOT SAVED!!!' );
				$this->log->add( 'bizumredsys', ' ' );
			}
			if ( ! empty( $dscardcountry ) ) {
				WCRedL()->update_order_meta( $order->get_id(), '_card_country_redsys', $dscardcountry );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_card_country_redsys saved: ' . $dscardcountry );
				}
			} elseif ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '_card_country_redsys NOT SAVED!!!' );
				$this->log->add( 'bizumredsys', ' ' );
			}
			if ( ! empty( $dscargtype ) ) {
				WCRedL()->update_order_meta( $order->get_id(), '_card_type_redsys', 'C' === $dscargtype ? 'Credit' : 'Debit' );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_card_type_redsys saved: ' . $dscargtype );
				}
			} elseif ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '_card_type_redsys NOT SAVED!!!' );
				$this->log->add( 'bizumredsys', ' ' );
			}
			// This meta is essential for later use.
			if ( ! empty( $secretsha256 ) ) {
				WCRedL()->update_order_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
				}
			} elseif ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '_redsys_secretsha256 NOT SAVED!!!' );
				$this->log->add( 'bizumredsys', ' ' );
			}
			// Payment completed.

			$order->add_order_note( __( 'HTTP Notification received - payment completed', 'woo-redsys-gateway-light' ) );
			$order->add_order_note( __( 'Authorization code: ', 'woo-redsys-gateway-light' ) . $authorisation_code );
			if ( 'completed' === $this->orderdo ) {
				$order->update_status( 'completed', __( 'Order Completed by Bizum', 'woo-redsys-gateway-light' ) );
			} else {
				$order->payment_complete();
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
				WCRedL()->update_order_meta( $order->get_id(), '_redsys_error_payment_ds_response_value', $ds_response_value );
			}

			if ( $ds_error_value ) {
				$order->add_order_note( __( 'Order cancelled by Redsys: ', 'woo-redsys-gateway-light' ) . $ds_error_value );
				WCRedL()->update_order_meta( $order->get_id(), '_redsys_error_payment_ds_response_value', $ds_error_value );
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
	 * Ask for Refund
	 *
	 * @param int $order_id Order ID.
	 * @param int $transaction_id Transaction ID.
	 * @param int $amount Amount.
	 */
	public function ask_for_refund( $order_id, $transaction_id, $amount ) {

		// post code to REDSYS.
		$order          = WCRedL()->get_order( $order_id );
		$terminal       = WCRedL()->get_order_meta( $order_id, '_payment_terminal_redsys', true );
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
		$secretsha256_meta = WCRedL()->get_order_meta( $order_id, '_redsys_secretsha256', true );
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
		$autorization_code = WCRedL()->get_order_meta( $order_id, '_authorisation_code_redsys', true );
		$autorization_date = WCRedL()->get_order_meta( $order_id, '_payment_date_redsys', true );
		$currencycode      = WCRedL()->get_order_meta( $order_id, '_corruncy_code_redsys', true );

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
		} elseif ( ! empty( $currency_codes ) ) {
			$currency = $currency_codes[ get_woocommerce_currency() ];
		}

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
		$mi_obj->set_parameter( 'DS_MERCHANT_PRODUCTDESCRIPTION', WCRedL()->product_description( $order, $this->id ) );
		$mi_obj->set_parameter( 'DS_MERCHANT_MERCHANTNAME', $this->commercename );

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
			$this->log->add( 'bizumredsys', __( 'ask_for_refund Asking for order #: ', 'woo-redsys-gateway-light' ) . $order_id );
			$this->log->add( 'bizumredsys', ' ' );
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
	 * Check Redsys Refund
	 *
	 * @param int $order_id Order ID.
	 */
	public function check_redsys_refund( $order_id ) {
		// check postmeta.
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
	 * Process the refund.
	 *
	 * @param int    $order_id Order ID.
	 * @param float  $amount Refund amount.
	 * @param string $reason Refund reason.
	 *
	 * @return bool|WP_Error
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		// Do your refund here. Refund $amount for the order with ID $order_id _transaction_id.
		set_time_limit( 0 );
		$order = wc_get_order( $order_id );

		$transaction_id = WCRedL()->get_order_meta( $order_id, '_payment_order_number_redsys', true );
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
				++$x;
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
			esc_html_e( 'Warning: WooCommerce Redsys Gateway Bizum is in test mode. Remember to uncheck it when you go live', 'woo-redsys-gateway-light' );
			echo '</div>';
		}
	}
}
