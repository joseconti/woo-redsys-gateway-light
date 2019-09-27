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
 * Version: 1.3.9
 * Author: José Conti
 * Author URI: https://www.joseconti.com/
 * Tested up to: 5.2
 * WC requires at least: 3.0
 * WC tested up to: 3.7
 * Text Domain: woo-redsys-gateway-light
 * Domain Path: /languages/
 * Copyright: (C) 2017 José Conti.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

define( 'REDSYS_WOOCOMMERCE_VERSION', '1.3.9' );
define( 'REDSYS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'REDSYS_POST_UPDATE_URL', 'https://redsys.joseconti.com/2019/09/06/redsys-gateway-ligth-1-3-9-para-woocommerce/' );
define( 'REDSYS_POST_PSD2_URL', 'https://redsys.joseconti.com/2019/09/05/redsys-y-psd2-o-sca/' );

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
	require_once 'class-wc-settings-tab-redsys-sort-invoices.php';
	require_once 'class-wc-settings-tab-redsys-order-export.php';
	require_once 'about-redsys.php';

/**
 * Plugin updates
 */

// SEUR Get Parent Page.
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
// SEUR Redirect to Welcome/About Page.
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
			$this->method_title         = __( 'Servired/RedSys', 'woo-redsys-gateway-light' );
			$this->not_use_https        = $this->get_option( 'not_use_https' );
			$this->notify_url           = add_query_arg( 'wc-api', 'WC_Gateway_redsys', home_url( '/' ) );
			$this->notify_url_not_https = str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'WC_Gateway_redsys', home_url( '/' ) ) );
			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();
			// Define user set variables.
			$this->title          = $this->get_option( 'title' );
			$this->description    = $this->get_option( 'description' );
			$this->logo           = $this->get_option( 'logo' );
			$this->orderdo        = $this->get_option( 'orderdo' );
			$this->customer       = $this->get_option( 'customer' );
			$this->commercename   = $this->get_option( 'commercename' );
			$this->payoptions     = $this->get_option( 'payoptions' );
			$this->terminal       = $this->get_option( 'terminal' );
			$this->secret         = $this->get_option( 'secret' );
			$this->secretsha256   = $this->get_option( 'secretsha256' );
			$this->debug          = $this->get_option( 'debug' );
			$this->hashtype       = $this->get_option( 'hashtype' );
			$this->redsyslanguage = $this->get_option( 'redsyslanguage' );
			$this->log            = new WC_Logger();
			// Actions.
			add_action( 'valid_redsys_standard_ipn_request', array( $this, 'successful_request' ) );
			add_action( 'woocommerce_receipt_redsys', array( $this, 'receipt_page' ) );
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			// Payment listener/API hook.
			add_action( 'woocommerce_api_wc_gateway_redsys', array( $this, 'check_ipn_response' ) );
			if ( ! $this->is_valid_for_use() ) {
				$this->enabled = false;
			}
		}
		public static function admin_notice_mcrypt_encrypt() {
			if ( ! function_exists( 'mcrypt_encrypt' ) && ( version_compare( PHP_VERSION, '7.0', '!<' ) ) ) {
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
			if ( ! in_array( get_woocommerce_currency(), array( 'ALL', 'DZD', 'AOK', 'ARS', 'MON', 'AZM', 'ARP', 'ARP', 'AUD', 'BSD', 'BHD', 'BDT', 'AMD', 'BBD', 'BMD', 'BTN', 'BOP', 'BAD', 'BWP', 'BRC', 'BZD', 'SBD', 'BND', 'BGL', 'BUK', 'BIF', 'BYB', 'KHR', 'CAD', 'CAD', 'CVE', 'LKR', 'CLP', 'CLP', 'CNY', 'CNH', 'COP', 'COP', 'KMF', 'ZRZ', 'CRC', 'CRC', 'CUP', 'CYP', 'CSK', 'CZK', 'DKK', 'DOP', 'ECS', 'SVC', 'GQE', 'ETB', 'ERN', 'FKP', 'FJD', 'DJF', 'GEL', 'GMD', 'DDM', 'GHC', 'GIP', 'GTQ', 'GNS', 'GYD', 'HTG', 'HNL', 'HKD', 'HUF', 'ISK', 'INR', 'ISK', 'IDR', 'IRR', 'IRA', 'IQD', 'ILS', 'JMD', 'JPY', 'JPY', 'KZT', 'JOD', 'KES', 'KPW', 'KRW', 'KWD', 'KGS', 'LAK', 'LBP', 'LSM', 'LVL', 'LRD', 'LYD', 'LTL', 'MOP', 'MGF', 'MWK', 'MYR', 'MVR', 'MLF', 'MTL', 'MRO', 'MUR', 'MXP', 'MXP', 'MNT', 'MDL', 'MAD', 'MZM', 'OMR', 'NAD', 'NPR', 'ANG', 'AWG', 'NTZ', 'VUV', 'NZD', 'NIC', 'NGN', 'NOK', 'PCI', 'PKR', 'PAB', 'PGK', 'PYG', 'PEI', 'PEI', 'PHP', 'PLZ', 'TPE', 'QAR', 'ROL', 'RUB', 'RWF', 'SHP', 'STD', 'SAR', 'SCR', 'SLL', 'SGD', 'SKK', 'VND', 'SIT', 'SOS', 'ZAR', 'ZWD', 'YDD', 'SSP', 'SDP', 'SDA', 'SRG', 'SZL', 'SEK', 'CHF', 'CHF', 'SYP', 'TJR', 'THB', 'TOP', 'TTD', 'AED', 'TND', 'TRL', 'PTL', 'TMM', 'UGS', 'UAK', 'MKD', 'RUR', 'EGP', 'GBP', 'TZS', 'USD', 'UYP', 'UYP', 'UZS', 'VEB', 'WST', 'YER', 'YUD', 'YUG', 'ZMK', 'TWD', 'TMT', 'GHS', 'RSD', 'MZN', 'AZN', 'RON', 'TRY', 'TRY', 'XAF', 'XCD', 'XOF', 'XPF', 'XEU', 'ZMW', 'SRD', 'MGA', '', 'TJS', 'AOA', 'BYR', 'BGN', 'CDF', 'BAM', 'EUR', 'UAH', 'GEL', 'PLN', 'BRL', 'BRL', 'ZAL', 'EEK', 'MXN' ), true ) )
		return false;
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
					<a href="https://woocommerce.com/products/redsys-gateway/" target="_blank" rel="noopener"><img class="aligncenter wp-image-211 size-full" title="Consigue la versión Pro en WooCommerce.com" src="<?php echo esc_attr( REDSYS_PLUGIN_URL ) . 'assets/images/banner.png'; ?>" alt="Consigue la versión Pro en WooCommerce.com" width="800" height="150" /></a>
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
				'enabled'        => array(
					'title'   => __( 'Enable/Disable', 'woo-redsys-gateway-light' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable Servired/RedSys', 'woo-redsys-gateway-light' ),
					'default' => 'no',
				),
				'title'          => array(
					'title'       => __( 'Title', 'woo-redsys-gateway-light' ),
					'type'        => 'text',
					'description' => __( 'This controls the title which the user sees during checkout.', 'woo-redsys-gateway-light' ),
					'default'     => __( 'Servired/RedSys', 'woo-redsys-gateway-light' ),
					'desc_tip'    => true,
				),
				'description'    => array(
					'title'       => __( 'Description', 'woo-redsys-gateway-light' ),
					'type'        => 'textarea',
					'description' => __( 'This controls the description which the user sees during checkout.', 'woo-redsys-gateway-light' ),
					'default'     => __( 'Pay via Servired/RedSys; you can pay with your credit card.', 'woo-redsys-gateway-light' ),
				),
				'logo'           => array(
					'title'       => __( 'Logo', 'woo-redsys-gateway-light' ),
					'type'        => 'text',
					'description' => __( 'Add link to image logo.', 'woo-redsys-gateway-light' ),
					'desc_tip'    => true,
				),
				'customer'       => array(
					'title'       => __( 'Commerce number (FUC)', 'woo-redsys-gateway-light' ),
					'type'        => 'text',
					'description' => __( 'Commerce number (FUC) provided by your bank.', 'woo-redsys-gateway-light' ),
					'desc_tip'    => true,
				),
				'commercename'   => array(
					'title'       => __( 'Commerce Name', 'woo-redsys-gateway-light' ),
					'type'        => 'text',
					'description' => __( 'Commerce Name', 'woo-redsys-gateway-light' ),
					'desc_tip'    => true,
				),
				'payoptions'     => array(
					'title'       => __( 'Pay Options', 'woo-redsys-gateway-light' ),
					'type'        => 'select',
					'description' => __( 'Chose options in Redsys Gateway (by Default Credit Cart + iUpay)', 'woo-redsys-gateway-light' ),
					'default'     => 'T',
					'options'     => array(
						' ' => __( 'All Methods', 'woo-redsys-gateway-light' ),
						'T' => __( 'Credit Cart & iUpay', 'woo-redsys-gateway-light' ),
						'C' => __( 'Credit Cart', 'woo-redsys-gateway-light' ),
					),
				),
				'terminal'       => array(
					'title'       => __( 'Terminal number', 'woo-redsys-gateway-light' ),
					'type'        => 'text',
					'description' => __( 'Terminal number provided by your bank.', 'woo-redsys-gateway-light' ),
					'desc_tip'    => true,
				),
				'not_use_https'  => array(
					'title'       => __( 'HTTPS SNI Compatibility', 'woo-redsys-gateway-light' ),
					'type'        => 'checkbox',
					'label'       => __( 'Activate SNI Compatibility.', 'woo-redsys-gateway-light' ),
					'default'     => 'no',
					'description' => sprintf( __( 'If you are using HTTPS and Redsys don\'t support your certificate, example Lets Encrypt, you can deactivate HTTPS notifications. WARNING: If you are forcing redirection to HTTPS with htaccess, you need to add an exception for notification URL', 'woo-redsys-gateway-light' ) ),
				),
				'orderdo'     => array(
					'title'       => __( 'What to do after payment?', 'woo-redsys-gateway-light' ),
					'type'        => 'select',
					'description' => __( 'Chose what to do after the customer pay the order.', 'woo-redsys-gateway-light' ),
					'default'     => 'processing',
					'options'     => array(
						'processing' => __( 'Mark as Processing (default & recomended)', 'woo-redsys-gateway-light' ),
						'completed'  => __( 'Mark as Complete', 'woo-redsys-gateway-light' ),
					),
				),
				'secretsha256'   => array(
					'title'       => __( 'Encryption secret passphrase SHA-256', 'woo-redsys-gateway-light' ),
					'type'        => 'text',
					'description' => __( 'Encryption secret passphrase SHA-256 provided by your bank.', 'woo-redsys-gateway-light' ),
					'desc_tip'    => true,
				),
				'redsyslanguage' => array(
					'title'       => __( 'Language Gateway', 'woo-redsys-gateway-light' ),
					'type'        => 'select',
					'description' => __( 'Choose the language for the Gateway. Not all Banks accept all languages', 'woo-redsys-gateway-light' ),
					'default'     => '001',
					'options'     => array(
						'001' => __( 'Spanish', 'woo-redsys-gateway-light' ),
						'002' => __( 'English', 'woo-redsys-gateway-light' ),
						'003' => __( 'Catalan', 'woo-redsys-gateway-light' ),
						'004' => __( 'French', 'woo-redsys-gateway-light' ),
						'005' => __( 'German', 'woo-redsys-gateway-light' ),
						'006' => __( 'Dutch', 'woo-redsys-gateway-light' ),
						'007' => __( 'Italian', 'woo-redsys-gateway-light' ),
						'008' => __( 'Swedish', 'woo-redsys-gateway-light' ),
						'009' => __( 'Portuguese', 'woo-redsys-gateway-light' ),
						'010' => __( 'Valencian', 'woo-redsys-gateway-light' ),
						'011' => __( 'Polish', 'woo-redsys-gateway-light' ),
						'012' => __( 'Galician', 'woo-redsys-gateway-light' ),
						'013' => __( 'Basque', 'woo-redsys-gateway-light' ),
						'208' => __( 'Danish', 'woo-redsys-gateway-light' ),
					),
				),
				'testmode'       => array(
					'title'       => __( 'Running in test mode', 'woo-redsys-gateway-light' ),
					'type'        => 'checkbox',
					'label'       => __( 'Running in test mode', 'woo-redsys-gateway-light' ),
					'default'     => 'yes',
					'description' => sprintf( __( 'Select this option for the initial testing required by your bank, deselect this option once you pass the required test phase and your production environment is active.', 'woo-redsys-gateway-light' ) ),
				),
				'debug'          => array(
					'title'       => __( 'Debug Log', 'woo-redsys-gateway-light' ),
					'type'        => 'checkbox',
					'label'       => __( 'Enable logging', 'woo-redsys-gateway-light' ),
					'default'     => 'no',
					'description' => __( 'Log Servired/RedSys events, such as notifications requests, inside <code>woocommerce/logs/redsys.txt</code>', 'woo-redsys-gateway-light' ),
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
			$secretsha256     = utf8_decode( $this->secretsha256 );
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
			if ( 'yes' === $this->not_use_https ) {
					$final_notify_url = $this->notify_url_not_https;
			} else {
				$final_notify_url = $this->notify_url;
			}
			// redsys Args.
			$miobj = new RedsysAPI();
			$miobj->setParameter( 'DS_MERCHANT_AMOUNT', $order_total_sign );
			$miobj->setParameter( 'DS_MERCHANT_ORDER', $transaction_id2 );
			$miobj->setParameter( 'DS_MERCHANT_MERCHANTCODE', $this->customer );
			$miobj->setParameter( 'DS_MERCHANT_CURRENCY', $currency_codes[ get_woocommerce_currency() ] );
			$miobj->setParameter( 'DS_MERCHANT_PAYMETHODS', $payment_option );
			$miobj->setParameter( 'DS_MERCHANT_TRANSACTIONTYPE', $transaction_type );
			$miobj->setParameter( 'DS_MERCHANT_TERMINAL', $dsmerchantterminal );
			$miobj->setParameter( 'DS_MERCHANT_MERCHANTURL', $final_notify_url );
			$miobj->setParameter( 'DS_MERCHANT_URLOK', add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
			$miobj->setParameter( 'DS_MERCHANT_URLKO', $returnfromredsys );
			$miobj->setParameter( 'DS_MERCHANT_CONSUMERLANGUAGE', $gatewaylanguage );
			$miobj->setParameter( 'DS_MERCHANT_PRODUCTDESCRIPTION', __( 'Order', 'woo-redsys-gateway-light' ) . ' ' . $order->get_order_number() );
			$miobj->setParameter( 'DS_MERCHANT_MERCHANTNAME', $this->commercename );
			if ( ! empty( $this->payoptions ) || ' ' !== $this->payoptions ) {
				$miobj->setParameter( 'DS_MERCHANT_PAYMETHODS', $this->payoptions );
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
			$usesecretsha256 = $this->secretsha256;
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
				wc_enqueue_js( '
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
			' );
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
				wc_enqueue_js( '
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
			' );
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
			$usesecretsha256 = $this->secretsha256;
			if ( $usesecretsha256 ) {
				$version     = sanitize_text_field( $_POST['Ds_SignatureVersion'] );
				$data        = sanitize_text_field( $_POST['Ds_MerchantParameters'] );
				$remote_sign = sanitize_text_field( $_POST['Ds_Signature'] );
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
			$version           = sanitize_text_field( $_POST['Ds_SignatureVersion'] );
			$data              = sanitize_text_field( $_POST['Ds_MerchantParameters'] );
			$remote_sign       = sanitize_text_field( $_POST['Ds_Signature'] );
			$miobj             = new RedsysAPI();
			$decodedata        = $miobj->decodeMerchantParameters( $data );
			$localsecret       = $miobj->createMerchantSignatureNotif( $usesecretsha256, $data );
			$total             = $miobj->getParameter( 'Ds_Amount' );
			$ordermi           = $miobj->getParameter( 'Ds_Order' );
			$dscode            = $miobj->getParameter( 'Ds_MerchantCode' );
			$currency_code     = $miobj->getParameter( 'Ds_Currency' );
			$response          = $miobj->getParameter( 'Ds_Response' );
			$id_trans          = $miobj->getParameter( 'Ds_AuthorisationCode' );
			$dsdate            = $miobj->getParameter( 'Ds_Date' );
			$dshour            = $miobj->getParameter( 'Ds_Hour' );
			$dstermnal         = $miobj->getParameter( 'Ds_Terminal' );
			$dsmerchandata     = $miobj->getParameter( 'Ds_MerchantData' );
			$dssucurepayment   = $miobj->getParameter( 'Ds_SecurePayment' );
			$dscardcountry     = $miobj->getParameter( 'Ds_Card_Country' );
			$dsconsumercountry = $miobj->getParameter( 'Ds_ConsumerLanguage' );
			$dscargtype        = $miobj->getParameter( 'Ds_Card_Type' );
			$order1            = $ordermi;
			$order2            = substr( $order1, 3 ); // cojo los 9 digitos del final.
			$order             = $this->get_redsys_order( (int) $order2 );
			if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Ds_Amount: ' . $total . ', Ds_Order: ' . $order1 . ',	Ds_MerchantCode: ' . $dscode . ', Ds_Currency: ' . $currency_code . ', Ds_Response: ' . $response . ', Ds_AuthorisationCode: ' . $id_trans . ', $order2: ' . $order2 );
			}
			$response = intval( $response );
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
			} elseif ( 101 === $response ) {
				// Tarjeta caducada.
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Order cancelled by Redsys: Expired credit card' );
				}
				// Order cancelled.
				$order->update_status( 'cancelled', __( 'Cancelled by Redsys', 'woo-redsys-gateway-light' ) );
				$order->add_order_note( __( 'Order cancelled by Redsys: Expired credit card', 'woo-redsys-gateway-light' ) );
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
	}

	add_action( 'admin_notices', function() {
		WC_Gateway_Redsys::admin_notice_mcrypt_encrypt();
	});

	include_once 'includes/persist-admin-notices-dismissal.php';
	add_action( 'admin_init', array( 'PAnD', 'init' ) );

	function redsys_help_admin_notice() {

		if ( ! PAnD::is_admin_notice_active( 'redsys-help-admin-notice-forever' ) ) {
			return;
		}

		$class   = 'notice notice-info is-dismissible';
		$message = '<a href="https://wordpress.org/support/plugin/woo-redsys-gateway-light/" target="_blank">WordPress.org</a>';


		printf( '<div data-dismissible="redsys-help-admin-notice-forever" class="%1$s"><p>', esc_attr( $class ) );
		printf( __( 'If your orders are kept on waiting for Redsys payment, please open a thread in %s Forums, it has solution and it is not the fault of the plugin.', 'woo-redsys-gateway-light' ), $message );
		echo '</p></div>';
	}

	add_action('admin_notices', 'redsys_help_admin_notice');


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
			printf( __( 'You have been using Redsys Lite plugin for more than 30 days, please, if you like, write a %s review. You will only need a moment and you will make me very happy. Thanks a lot', 'woo-redsys-gateway-light' ), $message );
			echo '</p></div>';
		}

	}
	add_action('admin_notices', 'redsys_ask_for_rating');

	function redsys_lite_add_notice_new_version() {

	$version = get_option( 'hide-new-version-redsys-notice' );

	if ( $version !== REDSYS_WOOCOMMERCE_VERSION ) {
		if ( isset( $_REQUEST['redsys-hide-new-version'] ) &&  'hide-new-version-redsys' === $_REQUEST['redsys-hide-new-version'] ) {
			$nonce = sanitize_text_field( $_REQUEST['_redsys_hide_new_version_nonce'] );
			if ( wp_verify_nonce( $nonce, 'redsys_hide_new_version_nonce' ) ) {
				update_option( 'hide-new-version-redsys-notice', REDSYS_WOOCOMMERCE_VERSION );
			}
		} else {
			?>
			<div id="message" class="updated woocommerce-message woocommerce-redsys-messages">
				<a class="woocommerce-message-close notice-dismiss" style="top:0;" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'redsys-hide-new-version', 'hide-new-version-redsys' ), 'redsys_hide_new_version_nonce', '_redsys_hide_new_version_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'woo-redsys-gateway-light' ); ?></a>
				<p>
					<?php echo esc_html__( 'WooCommerce Redsys Gateway Light has been updated to version', 'woo-redsys-gateway-light' ) . ' ' . REDSYS_WOOCOMMERCE_VERSION; ?>
				</p>
				<p>
					<?php printf( __( 'Discover the improvements that have been made in this version, and how to take advantage of them <a href="%s" target="_blank">here</a>', 'woo-redsys-gateway-light' ), esc_url( REDSYS_POST_UPDATE_URL ) ); ?>
				</p>
			</div>
		<?php }
	}
}

	add_action( 'admin_notices', 'redsys_lite_add_notice_new_version' );
	
	function redsys_add_notice_psd2_sca_lite() {
	
		$status = get_option( 'hide-psd2-sca-redsys-notice-lite' );
		
		if ( $status !== 'yes' ) {
			if ( isset( $_REQUEST['redsys-hide-psd2-sca'] ) &&  'hide-psd2-sca-redsys' === $_REQUEST['redsys-hide-psd2-sca'] ) {
				$nonce = sanitize_text_field( $_REQUEST['_redsys_hide_psd2_sca_nonce'] );
				if ( wp_verify_nonce( $nonce, 'redsys_hide_psd2_sca_nonce' ) ) {
					update_option( 'hide-psd2-sca-redsys-notice-lite', 'yes' );
				}
			} else {
				?>
				<div id="message" class="updated woocommerce-message woocommerce-redsys-messages">
					<a class="woocommerce-message-close notice-dismiss" style="top:0;" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'redsys-hide-psd2-sca', 'hide-psd2-sca-redsys' ), 'redsys_hide_psd2_sca_nonce', '_redsys_hide_psd2_sca_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'woo-redsys-gateway-light' ); ?></a>
					<p>
						<?php echo '<h2>' . esc_html__( 'READ THIS, IT IS VERY IMPORTANT PSD2 SCA', 'woo-redsys-gateway-light' ) . '<h2>'; ?>
					</p>
					<p>
						<?php printf( __( 'Surely you are worried about the new European regulation PSD2 SCA to be implemented next September 12, 2019. Read about it in this <a href="%s" target="_blank">link</a>', 'woo-redsys-gateway-light' ), esc_url( REDSYS_POST_PSD2_URL ) ); ?>
					</p>
				</div>
			<?php }
		}
	}

	add_action( 'admin_notices', 'redsys_add_notice_psd2_sca_lite' );

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
}
