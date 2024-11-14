<?php
/**
 * WooCommerce Redsys Gateway Light
 *
 * @package WooCommerce Redsys Gateway Ligh
 *
 * Plugin Name: WooCommerce Redsys Gateway Light
 * Requires Plugins: woocommerce
 * Plugin URI: https://wordpress.org/plugins/woo-redsys-gateway-light/
 * Description: Extends WooCommerce with a RedSys gateway. This is a Lite version, if you want many more, check the premium version https://woocommerce.com/products/redsys-gateway/
 * Version: 6.2.2
 * Author: José Conti
 * Author URI: https://plugins.joseconti.com/
 * Tested up to: 6.6
 * WC requires at least: 7.4
 * WC tested up to: 9.5
 * Text Domain: woo-redsys-gateway-light
 * Domain Path: /languages/
 * Copyright: (C) 2017 - 2024 José Conti.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

define( 'REDSYS_WOOCOMMERCE_VERSION', '6.2.2' );
define( 'REDSYS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
if ( ! defined( 'REDSYS_PLUGIN_PATH' ) ) {
	define( 'REDSYS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}
define( 'REDSYS_POST_UPDATE_URL', 'https://plugins.joseconti.com/2024/10/04/woocommerce-redsys-gateway-light-6-2-x/' );
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

add_action( 'plugins_loaded', 'woocommerce_gateway_redsys_init', 11 );

add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);
/**
 * Required API
 */
if ( ! class_exists( 'RedsysAPI' ) ) {
	require_once 'includes/class-redsysapi.php';
}
require_once 'about-redsys.php';

/**
 * Plugin updates
 */
function redsys_get_parent_page() {
	$redsys_parent = basename( $_SERVER['SCRIPT_NAME'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
	return $redsys_parent;
}
/**
 * Redsys menu
 */
function redsys_menu() {
	global $redsys_about;

	$redsys_about = add_submenu_page( 'woocommerce', __( 'About Redsys', 'woo-redsys-gateway-light' ), __( 'About Redsys', 'woo-redsys-gateway-light' ), 'manage_options', 'redsys-about-page', 'redsys_about_page' );
}
add_action( 'admin_menu', 'redsys_menu' );

/**
 * Redsys hook
 *
 * @param string $hook page hook.
 */
function redsys_styles_css( $hook ) {
	global $redsys_about;

	if ( $redsys_about !== $hook ) {
		return;
	} else {
		wp_register_style( 'aboutRedsys', REDSYS_PLUGIN_URL . 'assets/css/welcome.css', array(), '1.2.0' );
		wp_enqueue_style( 'aboutRedsys' );
	}
}
add_action( 'admin_enqueue_scripts', 'redsys_styles_css' );

/**
 * Redsys Redirect to Welcome/About Page.
 */
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

/**
 * Redsys CSS.
 */
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
 * WCRedL magic funcuton.
 * Copyright: (C) 2013 - 2021 José Conti
 */
function WCRedL() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	require_once REDSYS_PLUGIN_CLASS_PATH . 'class-wc-gateway-redsys-global-lite.php'; // Global class for global functions.
	return new WC_Gateway_Redsys_Global_Lite();
}

/**
 * WCPSD2L magic funcuton.
 * Copyright: (C) 2013 - 2021 José Conti
 */
function WCPSD2L() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	require_once REDSYS_PLUGIN_CLASS_PATH . 'class-wc-gateway-redsys-psd2-light.php'; // PSD2 class for Redsys.
	return new WC_Gateway_Redsys_PSD2_Light();
}
/**
 * Redsys init.
 * Copyright: (C) 2013 - 2021 José Conti
 */
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

	add_action(
		'admin_notices',
		function () {
			WC_Gateway_Redsys::admin_notice_mcrypt_encrypt();
		}
	);
	/**
	 * Redsys Notice version.
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function redsys_lite_add_notice_new_version() {

		$version = get_option( 'hide-new-version-redsys-notice' );

		if ( REDSYS_WOOCOMMERCE_VERSION !== $version ) {
			if ( isset( $_REQUEST['redsys-hide-new-version'] ) && 'hide-new-version-redsys' === $_REQUEST['redsys-hide-new-version'] ) {
				$nonce = sanitize_text_field( $_REQUEST['_redsys_hide_new_version_nonce'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
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
							<?php echo esc_html__( 'WooCommerce Redsys Gateway has been updated to version ', 'woo-redsys-gateway-light' ) . ' ' . esc_html( REDSYS_WOOCOMMERCE_VERSION ); ?>
							</h3>
						</p>
						<p>
						<?php esc_html_e( 'Discover the improvements that have been made in this version, and how to take advantage of them ', 'woo-redsys-gateway-light' ); ?>
						</p>
						<p class="submit">
							<a href="<?php echo esc_url( REDSYS_POST_UPDATE_URL ); ?>" class="button-primary" target="_blank"><?php esc_html_e( 'Discover the improvements', 'woo-redsys-gateway-light' ); ?></a>
							<a href="<?php echo esc_url( REDSYS_DONATION ); ?>" class="button-primary" target="_blank"><?php esc_html_e( 'Make a Microsponsor', 'woo-redsys-gateway-light' ); ?></a>
							<a href="<?php echo esc_url( REDSYS_REVIEW ); ?>" class="button-primary" target="_blank"><?php esc_html_e( 'Leave a review', 'woo-redsys-gateway-light' ); ?></a>
							<a href="<?php echo esc_url( REDSYS_TELEGRAM_SIGNUP ); ?>" class="button-primary" target="_blank"><?php esc_html_e( 'Sign up for the Telegram channel', 'woo-redsys-gateway-light' ); ?></a>
						</p>
					</div>
				</div>
				<?php
			}
		}
	}
	add_action( 'admin_notices', 'redsys_lite_add_notice_new_version' );

	/**
	 * Redsys ask for Telegram.
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function redsys_lite_ask_for_telegram() {

		$status = get_option( 'telegram-redsys-notice' );

		if ( 'yes' !== $status ) {
			if ( isset( $_REQUEST['redsys-telegram'] ) && 'telegram-redsys' === $_REQUEST['redsys-telegram'] ) {
				$nonce = sanitize_text_field( $_REQUEST['_redsys_telegram_nonce'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
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
					<p><a href="<?php esc_url( REDSYS_TELEGRAM_URL ); ?>" class="button" target="_blank"><?php esc_html_e( 'Don&#39;t miss a single thing!', 'woo-redsys-gateway-light' ); ?></a></p>
				</div>
				<?php
			}
		}
	}

	add_action( 'admin_notices', 'redsys_lite_ask_for_telegram' );

	/**
	 * Redsys notice CSS.
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function redsys_lite_notice_style() {
		wp_register_style( 'redsys_notice_css', REDSYS_PLUGIN_URL . 'assets/css/redsys-notice.css', false, REDSYS_WOOCOMMERCE_VERSION );
		wp_enqueue_style( 'redsys_notice_css' );
	}
	add_action( 'admin_enqueue_scripts', 'redsys_lite_notice_style' );

	/**
	 * Redsys add method.
	 *
	 * @param array $methods all WooCommerce methods.
	 */
	function woocommerce_add_gateway_redsys_gateway( $methods ) {
		$methods[] = 'WC_Gateway_Bizum_Redsys';
		$methods[] = 'WC_Gateway_redsys';
		$methods[] = 'WC_Gateway_GooglePay_Redirection_Redsys';
		return $methods;
	}
	add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_redsys_gateway' );

	/**
	 * Redsys add metabox.
	 *
	 * @param obj $post_or_order_object Order object.
	 */
	function add_redsys_meta_box( $post_or_order_object ) {

		$order_id = $post_or_order_object->get_id();
		if ( WCRedL()->is_redsys_order( $order_id ) ) {

			$date   = WCRedL()->get_order_date( $order_id );
			$hour   = WCRedL()->get_order_hour( $order_id );
			$auth   = WCRedL()->get_order_auth( $order_id );
			$number = WCRedL()->get_order_mumber( $order_id );

			echo '<h4 style="display: inline-block">' . esc_html__( 'Payment Details', 'woo-redsys-gateway-light' ) . '</h4>';
			echo '<p><strong>' . esc_html__( 'Paid with', 'woo-redsys-gateway-light' ) . ': </strong><br />' . esc_html( WCRedL()->get_gateway( $order_id ) ) . '</p>';
			if ( $number ) {
				echo '<p><strong>' . esc_html__( 'Redsys Order Number', 'woo-redsys-gateway-light' ) . ': </strong><br />' . esc_html( $number ) . '</p>';
			}
			if ( $date ) {
				echo '<p><strong>' . esc_html__( 'Redsys Date', 'woo-redsys-gateway-light' ) . ': </strong><br />' . esc_html( $date ) . '</p>';
			}

			if ( $hour ) {
				echo '<p><strong>' . esc_html__( 'Redsys Hour', 'woo-redsys-gateway-light' ) . ': </strong><br />' . esc_html( $hour ) . '</p>';
			}

			if ( $auth ) {
				echo '<p><strong>' . esc_html__( 'Redsys Authorisation Code', 'woo-redsys-gateway-light' ) . ': </strong><br />' . esc_html( $auth ) . '</p>';
			}
		}
	}
	add_action( 'woocommerce_admin_order_data_after_billing_address', 'add_redsys_meta_box' );

	/**
	 * Redsys add method.
	 *
	 * @param string $text Text in order.
	 * @param obj    $order Order information.
	 */
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
				$numero_autorizacion = WCRedL()->get_order_meta( $order_id, '_authorisation_code_redsys', true );
				$text               .= '<p>' . esc_html__( 'The Redsys Authorization number is: ', 'woo-redsys-gateway-light' ) . $numero_autorizacion . '</br >';
			}
		}
		return $text;
	}
	add_filter( 'woocommerce_thankyou_order_received_text', 'mostrar_numero_autentificacion', 20, 2 );

	/**
	 * Redsys head text.
	 */
	function redsys_lite_add_head_text() {
		echo '<!-- This site is powered by WooCommerce Redsys Gateway Light v.' . esc_html( REDSYS_WOOCOMMERCE_VERSION ) . ' - https://es.wordpress.org/plugins/woo-redsys-gateway-light/ -->';
	}
	add_action( 'wp_head', 'redsys_lite_add_head_text' );
	/**
	 * Phugin absolute path.
	 */
	function plugin_abspath_redsys() {
		return trailingslashit( plugin_dir_path( __FILE__ ) );
	}
	/**
	 * Plugin URL.
	 */
	function plugin_url_redsys() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}
	require_once REDSYS_PLUGIN_CLASS_PATH . 'class-wc-gateway-redsys.php'; // Redsys redirection 1.0.
	require_once REDSYS_PLUGIN_CLASS_PATH . 'class-wc-gateway-bizum-redsys.php'; // Bizum Version 3.0.
	require_once REDSYS_PLUGIN_CLASS_PATH . 'class-wc-gateway-googlepay-redirection-redsys.php'; // Google Pay redirection 6.0.
}
/**
 * Redsys block support.
 */
function woocommerce_gateway_redsys_lite_block_support() {
	if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
		require_once 'includes/blocks/class-wc-gateway-redsys-lite-support.php';
		require_once 'includes/blocks/class-wc-gateway-bizum-lite-support.php';
		require_once 'includes/blocks/class-wc-gateway-googlepay-redirection-redsys-support.php';
		add_action(
			'woocommerce_blocks_payment_method_type_registration',
			function ( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
				$payment_method_registry->register( new WC_Gateway_Redsys_Lite_Support() );
			}
		);
		add_action(
			'woocommerce_blocks_payment_method_type_registration',
			function ( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
				$payment_method_registry->register( new WC_Gateway_Bizum_Lite_Support() );
			}
		);
		add_action(
			'woocommerce_blocks_payment_method_type_registration',
			function ( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
				$payment_method_registry->register( new WC_Gateway_GooglePay_Redirection_Redsys_Support() );
			}
		);
	}
}
add_action( 'woocommerce_blocks_loaded', 'woocommerce_gateway_redsys_lite_block_support' );
