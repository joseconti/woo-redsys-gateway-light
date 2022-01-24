<?php
/**
 * WooCommerce Redsys Gateway Ligh
 *
 * @package WooCommerce Redsys Gateway Ligh
 * @author José Conti
 * @copyright 2018-2019 José Conti
 * @license GPL-3.0+
 *
 * Plugin Name: WooCommerce Redsys Gateway Light
 * Plugin URI: https://wordpress.org/plugins/woo-redsys-gateway-light/
 * Description: Extends WooCommerce with a RedSys gateway. This is a Lite version, if you want many more, check the premium version https://woocommerce.com/products/redsys-gateway/
 * Version: 3.0.6
 * Author: José Conti
 * Author URI: https://www.joseconti.com/
 * Tested up to: 5.7
 * WC requires at least: 3.0
 * WC tested up to: 6.1
 * Text Domain: woo-redsys-gateway-light
 * Domain Path: /languages/
 * Copyright: (C) 2017 - 2021 José Conti.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

define( 'REDSYS_WOOCOMMERCE_VERSION', '3.0.6' );
define( 'REDSYS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
if ( ! defined( 'REDSYS_PLUGIN_PATH' ) ) {
	define( 'REDSYS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}
define( 'REDSYS_POST_UPDATE_URL', 'https://redsys.joseconti.com/2020/12/28/redsys-gateway-light-3-0-x-para-woocommerce/' );
define( 'REDSYS_TELEGRAM_URL', 'https://t.me/wooredsys' );
define( 'REDSYS_REVIEW', 'https://wordpress.org/support/plugin/woo-redsys-gateway-light/reviews/?rate=5#new-post' );
define( 'REDSYS_DONATION', 'https://www.joseconti.com/cursos-online/micropatrocinio/' );
define( 'REDSYS_TELEGRAM_SIGNUP', 'https://t.me/wooredsys' );

if ( ! defined( 'REDSYS_PLUGIN_DATA_PATH' ) ) {
	define( 'REDSYS_PLUGIN_DATA_PATH', REDSYS_PLUGIN_PATH . 'includes/data/' );
}
if ( ! defined( 'REDSYS_PLUGIN_DATA_URL' ) ) {
	define( 'REDSYS_PLUGIN_DATA_URL', REDSYS_PLUGIN_URL . 'includes/data/' );
}
if ( ! defined( 'REDSYS_PLUGIN_CLASS_PATH' ) ) {
	define( 'REDSYS_PLUGIN_CLASS_PATH', REDSYS_PLUGIN_PATH . 'classes/' );
}

add_action( 'plugins_loaded', 'woocommerce_gateway_redsys_init', 0 );

/**
 * Required API
 */
if ( ! class_exists( 'RedsysAPI' ) ) {
	if ( version_compare( PHP_VERSION, '7.0.0', '<' ) ) {
		require_once 'includes/apiRedsys5.php';
	} else {
		require_once 'includes/apiRedsys7.php';
	}
}
	require_once 'about-redsys.php';

/**
 * Plugin updates
 */

// Get Parent Page.
function redsys_get_parent_page() {
	$redsys_parent = basename( $_SERVER['SCRIPT_NAME'] );
	return $redsys_parent;
}

function redsys_menu() {
	global $redsys_about;

	$redsys_about = add_submenu_page( 'woocommerce', __( 'About Redsys', 'woo-redsys-gateway-light' ), __( 'About Redsys', 'woo-redsys-gateway-light' ), 'manage_options', 'redsys-about-page', 'redsys_about_page' );

}
add_action( 'admin_menu', 'redsys_menu' );

function redsys_styles_css( $hook ) {
	global $redsys_about;

	if ( $redsys_about !== $hook ) {
		return; } else {
		wp_register_style( 'aboutRedsys', REDSYS_PLUGIN_URL . 'assets/css/welcome.css', array(), '1.2.0' );
		wp_enqueue_style( 'aboutRedsys' );
		}
}
add_action( 'admin_enqueue_scripts', 'redsys_styles_css' );
// REDSYS Redirect to Welcome/About Page.
function redsys_welcome_splash() {
	$seur_parent = redsys_get_parent_page();

	if ( get_option( 'woocommerce-redsys-version' ) === REDSYS_WOOCOMMERCE_VERSION ) {
		return;
	} elseif ( 'update.php' === $seur_parent ) {
		return;
	} elseif ( 'update-core.php' === $seur_parent ) {
		return;
	} else {
		$rate = get_option( 'woocommerce-redsys-rate' );

		if ( ! $rate ) {
			update_option( 'woocommerce-redsys-rate', time() );
		}
		update_option( 'woocommerce-redsys-version', REDSYS_WOOCOMMERCE_VERSION );
		$seurredirect = esc_url( admin_url( add_query_arg( array( 'page' => 'redsys-about-page' ), 'admin.php' ) ) );
		wp_safe_redirect( $seurredirect );
		exit;
	}
}
add_action( 'admin_init', 'redsys_welcome_splash', 1 );

function redsys_css_lite() {
		global $post_type;

		$current_screen = get_current_screen();

	if ( 'woocommerce_page_wc-settings' === $current_screen->id ) {
		wp_register_style( 'redsys-css', plugins_url( 'assets/css/redsys-css.css', __FILE__ ), array(), REDSYS_WOOCOMMERCE_VERSION );
		wp_enqueue_style( 'redsys-css' );
	}

}
	add_action( 'admin_enqueue_scripts', 'redsys_css_lite' );

/**
 * Copyright: (C) 2013 - 2021 José Conti
 */
function WCRedL() {
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	require_once REDSYS_PLUGIN_CLASS_PATH . 'class-wc-gateway-redsys-global.php'; // Global class for global functions
	return new WC_Gateway_Redsys_Global_Lite();
}

/**
 * Copyright: (C) 2013 - 2021 José Conti
 */
function WCPSD2L() {
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	require_once REDSYS_PLUGIN_CLASS_PATH . 'class-wc-gateway-redsys-psd2.php'; // PSD2 class for Redsys
	return new WC_Gateway_Redsys_PSD2_Light();
}

function woocommerce_gateway_redsys_init() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}
	/**
	 * Localisation
	 */
	load_plugin_textdomain( 'woo-redsys-gateway-light', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	/**
	 * Gateway class
	 */
	class WC_Gateway_Redsys extends WC_Payment_Gateway {
		var $notify_url;
		/**
		 * Constructor for the gateway.
		 *
		 * @access public
		 * @return void
		 */
		public function __construct() {
			global $woocommerce, $checkfor254;
			$this->id = 'redsys';
			$logo_url = $this->get_option( 'logo' );
			if ( ! empty( $logo_url ) ) {
				$logo_url   = $this->get_option( 'logo' );
				$this->icon = apply_filters( 'woocommerce_redsys_icon', $logo_url );
			} else {
				$this->icon = apply_filters( 'woocommerce_redsys_icon', plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) . '/assets/images/redsys.png' );
			}
			$this->has_fields           = false;
			$this->liveurl              = 'https://sis.redsys.es/sis/realizarPago';
			$this->testurl              = 'https://sis-t.redsys.es:25443/sis/realizarPago';
			$this->testmode             = $this->get_option( 'testmode' );
			$this->method_title         = __( 'Redsys Lite (by Jose Conti)', 'woo-redsys-gateway-light' );
			$this->method_description   = __( 'Redsys Lite  works redirecting customers to Redsys.', 'woocommerce-redsys' );
			$this->not_use_https        = $this->get_option( 'not_use_https' );
			$this->notify_url           = add_query_arg( 'wc-api', 'WC_Gateway_redsys', home_url( '/' ) );
			$this->notify_url_not_https = str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'WC_Gateway_redsys', home_url( '/' ) ) );
			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();
			// Define user set variables.
			$this->psd2             = $this->get_option( 'psd2' );
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
			$this->log              = new WC_Logger();
			$this->supports         = array(
				'products',
				'refunds',
			);
			// Actions.
			add_action( 'valid_redsys_standard_ipn_request', array( $this, 'successful_request' ) );
			add_action( 'woocommerce_receipt_redsys', array( $this, 'receipt_page' ) );
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			// Payment listener/API hook.
			add_action( 'woocommerce_api_wc_gateway_redsys', array( $this, 'check_ipn_response' ) );
			add_action( 'woocommerce_before_checkout_form', array( $this, 'warning_checkout_test_mode' ) );
			if ( ! $this->is_valid_for_use() ) {
				$this->enabled = false;
			}
		}
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
		 * Check if this gateway is enabled and available in the user's country
		 *
		 * @access public
		 * @return bool
		 */
		function is_valid_for_use() {
			if ( ! in_array( get_woocommerce_currency(), array( 'ALL', 'DZD', 'AOK', 'ARS', 'MON', 'AZM', 'ARP', 'ARP', 'AUD', 'BSD', 'BHD', 'BDT', 'AMD', 'BBD', 'BMD', 'BTN', 'BOP', 'BAD', 'BWP', 'BRC', 'BZD', 'SBD', 'BND', 'BGL', 'BUK', 'BIF', 'BYB', 'KHR', 'CAD', 'CAD', 'CVE', 'LKR', 'CLP', 'CLP', 'CNY', 'CNH', 'COP', 'COP', 'KMF', 'ZRZ', 'CRC', 'CRC', 'CUP', 'CYP', 'CSK', 'CZK', 'DKK', 'DOP', 'ECS', 'SVC', 'GQE', 'ETB', 'ERN', 'FKP', 'FJD', 'DJF', 'GEL', 'GMD', 'DDM', 'GHC', 'GIP', 'GTQ', 'GNS', 'GYD', 'HTG', 'HNL', 'HKD', 'HUF', 'ISK', 'INR', 'ISK', 'IDR', 'IRR', 'IRA', 'IQD', 'ILS', 'JMD', 'JPY', 'JPY', 'KZT', 'JOD', 'KES', 'KPW', 'KRW', 'KWD', 'KGS', 'LAK', 'LBP', 'LSM', 'LVL', 'LRD', 'LYD', 'LTL', 'MOP', 'MGF', 'MWK', 'MYR', 'MVR', 'MLF', 'MTL', 'MRO', 'MUR', 'MXP', 'MXP', 'MNT', 'MDL', 'MAD', 'MZM', 'OMR', 'NAD', 'NPR', 'ANG', 'AWG', 'NTZ', 'VUV', 'NZD', 'NIC', 'NGN', 'NOK', 'PCI', 'PKR', 'PAB', 'PGK', 'PYG', 'PEI', 'PEI', 'PHP', 'PLZ', 'TPE', 'QAR', 'ROL', 'RUB', 'RWF', 'SHP', 'STD', 'SAR', 'SCR', 'SLL', 'SGD', 'SKK', 'VND', 'SIT', 'SOS', 'ZAR', 'ZWD', 'YDD', 'SSP', 'SDP', 'SDA', 'SRG', 'SZL', 'SEK', 'CHF', 'CHF', 'SYP', 'TJR', 'THB', 'TOP', 'TTD', 'AED', 'TND', 'TRL', 'PTL', 'TMM', 'UGS', 'UAK', 'MKD', 'RUR', 'EGP', 'GBP', 'TZS', 'USD', 'UYP', 'UYP', 'UZS', 'VEB', 'WST', 'YER', 'YUD', 'YUG', 'ZMK', 'TWD', 'TMT', 'GHS', 'RSD', 'MZN', 'AZN', 'RON', 'TRY', 'TRY', 'XAF', 'XCD', 'XOF', 'XPF', 'XEU', 'ZMW', 'SRD', 'MGA', '', 'TJS', 'AOA', 'BYR', 'BGN', 'CDF', 'BAM', 'EUR', 'UAH', 'GEL', 'PLN', 'BRL', 'BRL', 'ZAL', 'EEK', 'MXN' ), true ) ) {
				return false;
			}
			return true;
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
				<span class="redsysnotice__content"><?php printf( __( 'check <a href="%1$s" target="_blank" rel="noopener">FAQ page</a> for working problems, or open a <a href="%2$s" target="_blank" rel="noopener">thread on WordPress.org</a> for support. Please, add a <a href="%3$s" target="_blank" rel="noopener">review on WordPress.org</a>', 'woo-redsys-gateway-light' ), 'https://www.joseconti.com/faq-plugin-redsys-woocommerce-com/', 'https://wordpress.org/support/plugin/woo-redsys-gateway-light/', 'https://wordpress.org/support/plugin/woo-redsys-gateway-light/reviews/?rate=5#new-post' ); ?><span>
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
		function init_form_fields() {
			$this->form_fields = array(
				'enabled'          => array(
					'title'   => __( 'Enable/Disable', 'woo-redsys-gateway-light' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable Servired/RedSys', 'woo-redsys-gateway-light' ),
					'default' => 'no',
				),
				'psd2'             => array(
					'title'   => __( 'Enable PSD2', 'woocommerce-redsys' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable PSD2', 'woocommerce-redsys' ),
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
					'description' => __( 'Chose options in Redsys Gateway (by Default Credit Card + iUpay)', 'woo-redsys-gateway-light' ),
					'default'     => 'T',
					'options'     => array(
						' ' => __( 'All Methods', 'woo-redsys-gateway-light' ),
						'T' => __( 'Credit Card & iUpay', 'woo-redsys-gateway-light' ),
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
					'description' => __( 'Log Servired/RedSys events, such as notifications requests, inside <code><code>WooCommerce > Status > Logs > redsys-{date}-{number}.log</code></code>', 'woo-redsys-gateway-light' ),
				),
			);
		}
		function get_redsys_args( $order ) {
			global $woocommerce;
			$order_id         = $order->get_id();
			$currency_codes   = array(
				'ALL' => 8,
				'DZD' => 12,
				'AOK' => 24,
				'MON' => 30,
				'AZM' => 31,
				'ARS' => 32,
				'AUD' => 36,
				'BSD' => 44,
				'BHD' => 48,
				'BDT' => 50,
				'AMD' => 51,
				'BBD' => 52,
				'BMD' => 60,
				'BTN' => 64,
				'BOP' => 68,
				'BAD' => 70,
				'BWP' => 72,
				'BRC' => 76,
				'BZD' => 84,
				'SBD' => 90,
				'BND' => 96,
				'BGL' => 100,
				'BUK' => 104,
				'BIF' => 108,
				'BYB' => 112,
				'KHR' => 116,
				'CAD' => 124,
				'CAD' => 124,
				'CVE' => 132,
				'LKR' => 144,
				'CLP' => 152,
				'CLP' => 152,
				'CNY' => 156,
				'CNH' => 157,
				'COP' => 170,
				'COP' => 170,
				'KMF' => 174,
				'ZRZ' => 180,
				'CRC' => 188,
				'CRC' => 188,
				'CUP' => 192,
				'CYP' => 196,
				'CSK' => 200,
				'CZK' => 203,
				'DKK' => 208,
				'DOP' => 214,
				'ECS' => 218,
				'SVC' => 222,
				'GQE' => 226,
				'ETB' => 230,
				'ERN' => 232,
				'FKP' => 238,
				'FJD' => 242,
				'DJF' => 262,
				'GEL' => 268,
				'GMD' => 270,
				'DDM' => 278,
				'GHC' => 288,
				'GIP' => 292,
				'GTQ' => 320,
				'GNS' => 324,
				'GYD' => 328,
				'HTG' => 332,
				'HNL' => 340,
				'HKD' => 344,
				'HUF' => 348,
				'ISK' => 352,
				'INR' => 356,
				'ISK' => 356,
				'IDR' => 360,
				'IRR' => 364,
				'IRA' => 365,
				'IQD' => 368,
				'ILS' => 376,
				'JMD' => 388,
				'JPY' => 392,
				'JPY' => 392,
				'KZT' => 398,
				'JOD' => 400,
				'KES' => 404,
				'KPW' => 408,
				'KRW' => 410,
				'KWD' => 414,
				'KGS' => 417,
				'LAK' => 418,
				'LBP' => 422,
				'LSM' => 426,
				'LVL' => 428,
				'LRD' => 430,
				'LYD' => 434,
				'LTL' => 440,
				'MOP' => 446,
				'MGF' => 450,
				'MWK' => 454,
				'MYR' => 458,
				'MVR' => 462,
				'MLF' => 466,
				'MTL' => 470,
				'MRO' => 478,
				'MUR' => 480,
				'MXP' => 484,
				'MXN' => 484,
				'MNT' => 496,
				'MDL' => 498,
				'MAD' => 504,
				'MZM' => 508,
				'OMR' => 512,
				'NAD' => 516,
				'NPR' => 524,
				'ANG' => 532,
				'AWG' => 533,
				'NTZ' => 536,
				'VUV' => 548,
				'NZD' => 554,
				'NIC' => 558,
				'NGN' => 566,
				'NOK' => 578,
				'PCI' => 582,
				'PKR' => 586,
				'PAB' => 590,
				'PGK' => 598,
				'PYG' => 600,
				'PEI' => 604,
				'PEI' => 604,
				'PHP' => 608,
				'PLZ' => 616,
				'TPE' => 626,
				'QAR' => 634,
				'ROL' => 642,
				'RUB' => 643,
				'RWF' => 646,
				'SHP' => 654,
				'STD' => 678,
				'SAR' => 682,
				'SCR' => 690,
				'SLL' => 694,
				'SGD' => 702,
				'SKK' => 703,
				'VND' => 704,
				'SIT' => 705,
				'SOS' => 706,
				'ZAR' => 710,
				'ZWD' => 716,
				'YDD' => 720,
				'SSP' => 728,
				'SDP' => 736,
				'SDA' => 737,
				'SRG' => 740,
				'SZL' => 748,
				'SEK' => 752,
				'CHF' => 756,
				'CHF' => 756,
				'SYP' => 760,
				'TJR' => 762,
				'THB' => 764,
				'TOP' => 776,
				'TTD' => 780,
				'AED' => 784,
				'TND' => 788,
				'TRL' => 792,
				'PTL' => 793,
				'TMM' => 795,
				'UGS' => 800,
				'UAK' => 804,
				'MKD' => 807,
				'RUR' => 810,
				'EGP' => 818,
				'GBP' => 826,
				'TZS' => 834,
				'USD' => 840,
				'UYP' => 858,
				'UYP' => 858,
				'UZS' => 860,
				'VEB' => 862,
				'WST' => 882,
				'YER' => 886,
				'YUD' => 890,
				'YUG' => 891,
				'ZMK' => 892,
				'TWD' => 901,
				'TMT' => 934,
				'GHS' => 936,
				'RSD' => 941,
				'MZN' => 943,
				'AZN' => 944,
				'RON' => 946,
				'TRY' => 949,
				'TRY' => 949,
				'XAF' => 950,
				'XCD' => 951,
				'XOF' => 952,
				'XPF' => 953,
				'XEU' => 954,
				'ZMW' => 967,
				'SRD' => 968,
				'MGA' => 969,
				'AFN' => 971,
				'TJS' => 972,
				'AOA' => 973,
				'BYR' => 974,
				'BGN' => 975,
				'CDF' => 976,
				'BAM' => 977,
				'EUR' => 978,
				'UAH' => 980,
				'GEL' => 981,
				'PLN' => 985,
				'BRL' => 986,
				'BRL' => 986,
				'ZAL' => 991,
				'EEK' => 2333,
			);
			$transaction_id   = str_pad( $order_id, 12, '0', STR_PAD_LEFT );
			$transaction_id1  = wp_rand( 1, 999 ); // lets to create a random number.
			$transaction_id2  = substr_replace( $transaction_id, $transaction_id1, 0, -9 ); // new order number.
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
			$miobj->setParameter( 'DS_MERCHANT_PRODUCTDESCRIPTION', __( 'Order', 'woo-redsys-gateway-light' ) . ' ' . $order->get_order_number() );
			$miobj->setParameter( 'DS_MERCHANT_MERCHANTNAME', $this->commercename );
			$miobj->setParameter( 'DS_MERCHANT_MODULE', $merchant_module );
			if ( ! empty( $this->payoptions ) || ' ' !== $this->payoptions ) {
				$miobj->setParameter( 'DS_MERCHANT_PAYMETHODS', $this->payoptions );
			} else {
				$miobj->setParameter( 'DS_MERCHANT_PAYMETHODS', 'T' );
			}
			if ( 'yes' === $this->psd2 ) {
				$psd2 = WCPSD2L()->get_acctinfo( $order );
				$miobj->setParameter( 'Ds_Merchant_EMV3DS', $psd2 );

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'PSD2 activado' );
					$this->log->add( 'redsys', '$psd2: ' . $psd2 );
				}
			}
			$version = 'HMAC_SHA256_V1';
			// Se generan los parámetros de la petición.
			$request     = '';
			$params      = $miobj->createMerchantParameters();
			$signature   = $miobj->createMerchantSignature( $secretsha256 );
			$redsys_args = array(
				'Ds_SignatureVersion'   => $version,
				'Ds_MerchantParameters' => $params,
				'Ds_Signature'          => $signature,
			);
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'Generating payment form for order ' . $order->get_order_number() . '. Sent data: ' . print_r( $redsys_args, true ) );
				$this->log->add( 'redsys', 'Helping to understand the encrypted code: ' );
				$this->log->add( 'redsys', 'DS_MERCHANT_AMOUNT: ' . $order_total_sign );
				$this->log->add( 'redsys', 'DS_MERCHANT_ORDER: ' . $transaction_id2 );
				$this->log->add( 'redsys', 'DS_MERCHANT_MERCHANTCODE: ' . $this->customer );
				$this->log->add( 'redsys', 'DS_MERCHANT_CURRENCY' . $currency_codes[ get_woocommerce_currency() ] );
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
		 * @access public
		 * @param mixed $order_id
		 * @return string
		 */
		function generate_redsys_form( $order_id ) {
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
		 * @access public
		 * @param int $order_id
		 * @return array
		 */
		function process_payment( $order_id ) {
			$order = new WC_Order( $order_id );
			return array(
				'result'   => 'success',
				'redirect' => $order->get_checkout_payment_url( true ),
			);
		}
		/**
		 * Output for the order received page.
		 *
		 * @access public
		 * @return void
		 */
		function receipt_page( $order ) {
			echo '<p>' . esc_html__( 'Thank you for your order, please click the button below to pay with Credit Card via Servired/RedSys.', 'woo-redsys-gateway-light' ) . '</p>';
			echo $this->generate_redsys_form( $order );
		}
		/**
		 * Check redsys IPN validity
		 **/
		function check_ipn_request_is_valid() {
			global $woocommerce;

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'HTTP Notification received: ' . print_r( $_POST, true ) );
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

				if ( isset( $_POST['Ds_SignatureVersion'] ) ) {
					$version = sanitize_text_field( $_POST['Ds_SignatureVersion'] );
				} else {
					$version = '';
				}

				if ( isset( $_POST['Ds_MerchantParameters'] ) ) {
					$data = sanitize_text_field( $_POST['Ds_MerchantParameters'] );
				} else {
					$data = '';
				}

				if ( isset( $_POST['Ds_Signature'] ) ) {
					$remote_sign = sanitize_text_field( $_POST['Ds_Signature'] );
				} else {
					$remote_sign = '';
				}

				$miobj       = new RedsysAPI();
				$localsecret = $miobj->createMerchantSignatureNotif( $usesecretsha256, $data );

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
					$this->log->add( 'redsys', 'HTTP Notification received: ' . print_r( $_POST, true ) );
				}
				if ( $_POST['Ds_MerchantCode'] === $this->customer ) {
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
		 * @access public
		 * @return void
		 */
		function check_ipn_response() {
			@ob_clean();
			$_POST = stripslashes_deep( $_POST );
			if ( $this->check_ipn_request_is_valid() ) {
				header( 'HTTP/1.1 200 OK' );
				do_action( 'valid_redsys_standard_ipn_request', $_POST );
			} else {
				wp_die( 'Servired/RedSys Notification Request Failure' );
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

			$version           = sanitize_text_field( $_POST['Ds_SignatureVersion'] );
			$data              = sanitize_text_field( $_POST['Ds_MerchantParameters'] );
			$remote_sign       = sanitize_text_field( $_POST['Ds_Signature'] );
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
			$order1            = $ordermi;
			$order2            = substr( $order1, 3 ); // cojo los 9 digitos del final.
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
						$this->log->add( 'redsys', 'update_post_meta to "refund yes"' );
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
				// remove 0 from bigining
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
					update_post_meta( $order->get_id(), '_payment_order_number_redsys', $order1 );
				}
				if ( ! empty( $dsdate ) ) {
					update_post_meta( $order->get_id(), '_payment_date_redsys', $dsdate );
				}
				if ( ! empty( $dshour ) ) {
					update_post_meta( $order->get_id(), '_payment_hour_redsys', $dshour );
				}
				if ( ! empty( $id_trans ) ) {
					update_post_meta( $order->get_id(), '_authorisation_code_redsys', $authorisation_code );
				}
				if ( ! empty( $dscardcountry ) ) {
					update_post_meta( $order->get_id(), '_card_country_redsys', $dscardcountry );
				}
				if ( ! empty( $dscargtype ) ) {
					update_post_meta( $order->get_id(), '_card_type_redsys', 'C' === $dscargtype ? 'Credit' : 'Debit' );
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
		 * get_redsys_order function.
		 *
		 * @access public
		 * @param mixed $order_id
		 * @return void
		 */
		function get_redsys_order( $order_id ) {
			$order = new WC_Order( $order_id );
			return $order;
		}

		// refunds

		function ask_for_refund( $order_id, $transaction_id, $amount ) {

			// post code to REDSYS
			$order          = $this->get_redsys_order( $order_id );
			$terminal       = $this->terminal;
			$currency_codes = array(
				'ALL' => 8,
				'DZD' => 12,
				'AOK' => 24,
				'MON' => 30,
				'AZM' => 31,
				'ARS' => 32,
				'AUD' => 36,
				'BSD' => 44,
				'BHD' => 48,
				'BDT' => 50,
				'AMD' => 51,
				'BBD' => 52,
				'BMD' => 60,
				'BTN' => 64,
				'BOP' => 68,
				'BAD' => 70,
				'BWP' => 72,
				'BRC' => 76,
				'BZD' => 84,
				'SBD' => 90,
				'BND' => 96,
				'BGL' => 100,
				'BUK' => 104,
				'BIF' => 108,
				'BYB' => 112,
				'KHR' => 116,
				'CAD' => 124,
				'CAD' => 124,
				'CVE' => 132,
				'LKR' => 144,
				'CLP' => 152,
				'CLP' => 152,
				'CNY' => 156,
				'CNH' => 157,
				'COP' => 170,
				'COP' => 170,
				'KMF' => 174,
				'ZRZ' => 180,
				'CRC' => 188,
				'CRC' => 188,
				'CUP' => 192,
				'CYP' => 196,
				'CSK' => 200,
				'CZK' => 203,
				'DKK' => 208,
				'DOP' => 214,
				'ECS' => 218,
				'SVC' => 222,
				'GQE' => 226,
				'ETB' => 230,
				'ERN' => 232,
				'FKP' => 238,
				'FJD' => 242,
				'DJF' => 262,
				'GEL' => 268,
				'GMD' => 270,
				'DDM' => 278,
				'GHC' => 288,
				'GIP' => 292,
				'GTQ' => 320,
				'GNS' => 324,
				'GYD' => 328,
				'HTG' => 332,
				'HNL' => 340,
				'HKD' => 344,
				'HUF' => 348,
				'ISK' => 352,
				'INR' => 356,
				'ISK' => 356,
				'IDR' => 360,
				'IRR' => 364,
				'IRA' => 365,
				'IQD' => 368,
				'ILS' => 376,
				'JMD' => 388,
				'JPY' => 392,
				'JPY' => 392,
				'KZT' => 398,
				'JOD' => 400,
				'KES' => 404,
				'KPW' => 408,
				'KRW' => 410,
				'KWD' => 414,
				'KGS' => 417,
				'LAK' => 418,
				'LBP' => 422,
				'LSM' => 426,
				'LVL' => 428,
				'LRD' => 430,
				'LYD' => 434,
				'LTL' => 440,
				'MOP' => 446,
				'MGF' => 450,
				'MWK' => 454,
				'MYR' => 458,
				'MVR' => 462,
				'MLF' => 466,
				'MTL' => 470,
				'MRO' => 478,
				'MUR' => 480,
				'MXP' => 484,
				'MXN' => 484,
				'MNT' => 496,
				'MDL' => 498,
				'MAD' => 504,
				'MZM' => 508,
				'OMR' => 512,
				'NAD' => 516,
				'NPR' => 524,
				'ANG' => 532,
				'AWG' => 533,
				'NTZ' => 536,
				'VUV' => 548,
				'NZD' => 554,
				'NIC' => 558,
				'NGN' => 566,
				'NOK' => 578,
				'PCI' => 582,
				'PKR' => 586,
				'PAB' => 590,
				'PGK' => 598,
				'PYG' => 600,
				'PEI' => 604,
				'PEI' => 604,
				'PHP' => 608,
				'PLZ' => 616,
				'TPE' => 626,
				'QAR' => 634,
				'ROL' => 642,
				'RUB' => 643,
				'RWF' => 646,
				'SHP' => 654,
				'STD' => 678,
				'SAR' => 682,
				'SCR' => 690,
				'SLL' => 694,
				'SGD' => 702,
				'SKK' => 703,
				'VND' => 704,
				'SIT' => 705,
				'SOS' => 706,
				'ZAR' => 710,
				'ZWD' => 716,
				'YDD' => 720,
				'SSP' => 728,
				'SDP' => 736,
				'SDA' => 737,
				'SRG' => 740,
				'SZL' => 748,
				'SEK' => 752,
				'CHF' => 756,
				'CHF' => 756,
				'SYP' => 760,
				'TJR' => 762,
				'THB' => 764,
				'TOP' => 776,
				'TTD' => 780,
				'AED' => 784,
				'TND' => 788,
				'TRL' => 792,
				'PTL' => 793,
				'TMM' => 795,
				'UGS' => 800,
				'UAK' => 804,
				'MKD' => 807,
				'RUR' => 810,
				'EGP' => 818,
				'GBP' => 826,
				'TZS' => 834,
				'USD' => 840,
				'UYP' => 858,
				'UYP' => 858,
				'UZS' => 860,
				'VEB' => 862,
				'WST' => 882,
				'YER' => 886,
				'YUD' => 890,
				'YUG' => 891,
				'ZMK' => 892,
				'TWD' => 901,
				'TMT' => 934,
				'GHS' => 936,
				'RSD' => 941,
				'MZN' => 943,
				'AZN' => 944,
				'RON' => 946,
				'TRY' => 949,
				'TRY' => 949,
				'XAF' => 950,
				'XCD' => 951,
				'XOF' => 952,
				'XPF' => 953,
				'XEU' => 954,
				'ZMW' => 967,
				'SRD' => 968,
				'MGA' => 969,
				'AFN' => 971,
				'TJS' => 972,
				'AOA' => 973,
				'BYR' => 974,
				'BGN' => 975,
				'CDF' => 976,
				'BAM' => 977,
				'EUR' => 978,
				'UAH' => 980,
				'GEL' => 981,
				'PLN' => 985,
				'BRL' => 986,
				'BRL' => 986,
				'ZAL' => 991,
				'EEK' => 2333,
			);
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
			$secretsha256_meta = get_post_meta( $order_id, '_redsys_secretsha256', true );
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
			if ( 'yes' === $this->testmode ) :
				$redsys_adr = $this->testurl . '?';
			else :
				$redsys_adr = $this->liveurl . '?';
			endif;
			$autorization_code = get_post_meta( $order_id, '_authorisation_code_redsys', true );
			$autorization_date = get_post_meta( $order_id, '_payment_date_redsys', true );
			$currencycode      = get_post_meta( $order_id, '_corruncy_code_redsys', true );
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
			} else {
				if ( empty( $currencycode ) ) {
					$currency = $currency_codes[ get_woocommerce_currency() ];
				}
			}
			$merchant_module = 'WooCommerce_Redsys_Gateway_Light_' . REDSYS_WOOCOMMERCE_VERSION . '_WordPress.org';

			$miObj = new RedsysAPI();

			$miObj->setParameter( 'DS_MERCHANT_MODULE', $merchant_module );
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
			$miObj->setParameter( 'DS_MERCHANT_PRODUCTDESCRIPTION', __( 'Order', 'woo-redsys-gateway-light' ) . ' ' . $order->get_order_number() );
			$miObj->setParameter( 'DS_MERCHANT_MERCHANTNAME', $this->commercename );

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
				$this->log->add( 'redsys', __( 'ask_for_refund Asking por order #: ', 'woo-redsys-gateway-light' ) . $order_id );
				$this->log->add( 'redsys', ' ' );
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

		function check_redsys_refund( $order_id ) {
			// check postmeta
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

		public function process_refund( $order_id, $amount = null, $reason = '' ) {

			// Do your refund here. Refund $amount for the order with ID $order_id _transaction_id
			set_time_limit( 0 );
			$order = wc_get_order( $order_id );

			$transaction_id = get_post_meta( $order_id, '_payment_order_number_redsys', true );
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
					$this->log->add( 'redsys', __( 'check_redsys_refund Asking por order #: ', 'woo-redsys-gateway-light' ) . $order_id );
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
					$x++;
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
				echo __( 'Warning: WooCommerce Redsys Gateway Light is in test mode. Remember to uncheck it when you go live', 'woo-redsys-gateway-light' );
				echo '</div>';
			}
		}
	}

	add_action(
		'admin_notices',
		function() {
			WC_Gateway_Redsys::admin_notice_mcrypt_encrypt();
		}
	);

	include_once 'includes/persist-admin-notices-dismissal.php';
	add_action( 'admin_init', array( 'PAnD', 'init' ) );

	function redsys_help_admin_notice() {

		if ( ! PAnD::is_admin_notice_active( 'redsys-help-admin-notice-forever' ) ) {
			return;
		}

		$class   = 'notice notice-info is-dismissible';
		$message = '<a href="https://wordpress.org/support/plugin/woo-redsys-gateway-light/" target="_blank">WordPress.org</a>';

		printf( '<div data-dismissible="redsys-help-admin-notice-forever" class="%1$s"><p>', esc_attr( $class ) );
		printf( esc_attr__( 'If your orders are kept on waiting for Redsys payment, please open a thread in %s Forums, it has solution and it is not the fault of the plugin.', 'woo-redsys-gateway-light' ), $message );
		echo '</p></div>';
	}

	add_action( 'admin_notices', 'redsys_help_admin_notice' );

	function redsys_ask_for_rating() {

		if ( ! PAnD::is_admin_notice_active( 'redsys-ask-for-ratin-forever' ) ) {
			return;
		}

		$activation_date    = get_option( 'woocommerce-redsys-rate' );
		$activation_date_30 = $activation_date + ( 30 * 24 * 60 * 60 );

		if ( time() > $activation_date_30 ) {
			$class   = 'notice notice-info is-dismissible';
			$message = '<a href="https://wordpress.org/support/plugin/woo-redsys-gateway-light/reviews/?rate=5#new-post" target="_blank">WordPress.org</a>';

			printf( '<div data-dismissible="redsys-ask-for-ratin-forever" class="%1$s"><p>', esc_attr( $class ) );
			printf( esc_attr__( 'You have been using Redsys Lite plugin for more than 30 days, please, if you like, write a %s review. You will only need a moment and you will make me very happy. Thanks a lot', 'woo-redsys-gateway-light' ), $message );
			echo '</p></div>';
		}

	}
	add_action( 'admin_notices', 'redsys_ask_for_rating' );

	function redsys_lite_add_notice_new_version() {

		$version = get_option( 'hide-new-version-redsys-notice' );

		if ( $version !== REDSYS_WOOCOMMERCE_VERSION ) {
			if ( isset( $_REQUEST['redsys-hide-new-version'] ) && 'hide-new-version-redsys' === $_REQUEST['redsys-hide-new-version'] ) {
				$nonce = sanitize_text_field( $_REQUEST['_redsys_hide_new_version_nonce'] );
				if ( wp_verify_nonce( $nonce, 'redsys_hide_new_version_nonce' ) ) {
					update_option( 'hide-new-version-redsys-notice', REDSYS_WOOCOMMERCE_VERSION );
				}
			} else {
				?>
				<div id="message" class="updated woocommerce-message woocommerce-redsys-messages">
					<div class="contenido-redsys-notice">
						<a class="woocommerce-message-close notice-dismiss" style="top:0;" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'redsys-hide-new-version', 'hide-new-version-redsys' ), 'redsys_hide_new_version_nonce', '_redsys_hide_new_version_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'woo-redsys-gateway-light' ); ?></a>
						<p>
							<h3>
								<?php esc_html_e( 'WooCommerce Redsys Gateway has been updated to version ', 'woo-redsys-gateway-light' ) . ' ' . esc_html_e( REDSYS_WOOCOMMERCE_VERSION ); ?>
							</h3>
						</p>
						<p>
							<?php esc_html_e( 'Discover the improvements that have been made in this version, and how to take advantage of them ', 'woo-redsys-gateway-light' ); ?>
						</p>
						<p class="submit">
							<a href="<?php esc_html_e( REDSYS_POST_UPDATE_URL ); ?>" class="button-primary" target="_blank"><?php esc_html_e( 'Discover the improvements', 'woo-redsys-gateway-light' ); ?></a>
							<a href="<?php esc_html_e( REDSYS_DONATION ); ?>" class="button-primary" target="_blank"><?php esc_html_e( 'Make a Microsponsor', 'woo-redsys-gateway-light' ); ?></a>
							<a href="<?php esc_html_e( REDSYS_REVIEW ); ?>" class="button-primary" target="_blank"><?php esc_html_e( 'Leave a review', 'woo-redsys-gateway-light' ); ?></a>
							<a href="<?php esc_html_e( REDSYS_TELEGRAM_SIGNUP ); ?>" class="button-primary" target="_blank"><?php esc_html_e( 'Sign up for the Telegram channel', 'woo-redsys-gateway-light' ); ?></a>
						</p>
					</div>
				</div>
				<?php
			}
		}
	}
	add_action( 'admin_notices', 'redsys_lite_add_notice_new_version' );

	function redsys_lite_ask_for_telegram() {

		$status = get_option( 'telegram-redsys-notice' );

		if ( $status !== 'yes' ) {
			if ( isset( $_REQUEST['redsys-telegram'] ) && 'telegram-redsys' === $_REQUEST['redsys-telegram'] ) {
				$nonce = sanitize_text_field( $_REQUEST['_redsys_telegram_nonce'] );
				if ( wp_verify_nonce( $nonce, 'redsys_telegram_nonce' ) ) {
					update_option( 'telegram-redsys-notice', 'yes' );
				}
			} else {
				?>
				<div id="message" class="updated woocommerce-message woocommerce-redsys-messages">
					<a class="woocommerce-message-close notice-dismiss" style="top:0;" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'redsys-telegram', 'telegram-redsys' ), 'redsys_telegram_nonce', '_redsys_telegram_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'woo-redsys-gateway-light' ); ?></a>
					<p>
						<?php echo esc_html__( 'Do you want to be informed about the world of Redsys?', 'woo-redsys-gateway-light' ); ?>
					</p>
					<p>
						<?php esc_html_e( 'Sign up for the WooCommerce Redsys Gateway Telegram channel, and be the first to know everything.', 'woo-redsys-gateway-light' ); ?>
					</p>
					<p><a href="<?php esc_html_e( REDSYS_TELEGRAM_URL ); ?>" class="button" target="_blank"><?php esc_html_e( 'Don&#39;t miss a single thing!', 'woo-redsys-gateway-light' ); ?></a></p>
				</div>
				<?php
			}
		}
	}

	add_action( 'admin_notices', 'redsys_lite_ask_for_telegram' );

	function redsys_lite_notice_style() {
		wp_register_style( 'redsys_notice_css', REDSYS_PLUGIN_URL . 'assets/css/redsys-notice.css', false, REDSYS_WOOCOMMERCE_VERSION );
		wp_enqueue_style( 'redsys_notice_css' );
	}
	add_action( 'admin_enqueue_scripts', 'redsys_lite_notice_style' );

	function woocommerce_add_gateway_redsys_gateway( $methods ) {
		$methods[] = 'WC_Gateway_redsys';
		return $methods;
	}
	add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_redsys_gateway' );

	function add_redsys_meta_box() {
		$date_decoded = str_replace( '%2F', '/', get_post_meta( get_the_ID(), '_payment_date_redsys', true ) );
		$hour_decoded = str_replace( '%3A', ':', get_post_meta( get_the_ID(), '_payment_hour_redsys', true ) );
		echo '<h4>' . esc_html__( 'Payment Details', 'woo-redsys-gateway-light' ) . '</h4>';
		echo '<p><strong>' . esc_html__( 'Redsys Date', 'woo-redsys-gateway-light' ) . ': </strong><br />' . esc_html( $date_decoded ) . '</p>';
		echo '<p><strong>' . esc_html__( 'Redsys Hour', 'woo-redsys-gateway-light' ) . ': </strong><br />' . esc_html( $hour_decoded ) . '</p>';
		echo '<p><strong>' . esc_html__( 'Redsys Authorisation Code', 'woo-redsys-gateway-light' ) . ': </strong><br />' . esc_attr( get_post_meta( get_the_ID(), '_authorisation_code_redsys', true ) ) . '</p>';
	}
	add_action( 'woocommerce_admin_order_data_after_billing_address', 'add_redsys_meta_box' );

	function mostrar_numero_autentificacion( $text, $order ) {

		if ( ! empty( $order ) ) {
			$redsys          = new WC_Gateway_Redsys();
			$is_redsys_order = WCRedL()->is_redsys_order( $order->get_id() );
			if ( 'yes' === $redsys->debug ) {
				if ( $is_redsys_order ) {
					$redsys->log->add( 'redsys', '$is_redsys_order: YES' );
				} else {
					$redsys->log->add( 'redsys', '$is_redsys_order: NO' );
				}
			}
			if ( $order && $is_redsys_order ) {
				$order_id            = $order->get_id();
				$numero_autorizacion = get_post_meta( $order_id, '_authorisation_code_redsys', true );
				$text               .= '<p>' . esc_html__( 'The Redsys Authorization number is: ', 'woo-redsys-gateway-light' ) . $numero_autorizacion . '</br >';
			}
		}
		return $text;
	}
	add_filter( 'woocommerce_thankyou_order_received_text', 'mostrar_numero_autentificacion', 20, 2 );

	// Adding Bizum
	require_once REDSYS_PLUGIN_CLASS_PATH . 'class-wc-gateway-bizum-redsys.php'; // Bizum Version 3.0
}

function redsys_lite_add_head_text() {
	echo '<!-- This site is powered by WooCommerce Redsys Gateway Light v.' . REDSYS_WOOCOMMERCE_VERSION . ' - https://es.wordpress.org/plugins/woo-redsys-gateway-light/ -->';
}
add_action( 'wp_head', 'redsys_lite_add_head_text' );
