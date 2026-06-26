<?php
/**
 * About page
 *
 * @package WooCommerce_Redsys/Classes
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * About page
 */
function redsys_about_page() {
	?>
	<div class="wrap about-wrap-redsys">
		<h1>
			<?php
			/* translators: plugin version number */
			printf( esc_html__( 'Welcome to WooCommerce Redsys %s', 'woo-redsys-gateway-light' ), esc_attr( REDSYS_WOOCOMMERCE_VERSION ) );
			?>
		</h1>
		<p class="about-text"><?php esc_html_e( 'Thank you for install the latest version! WooCommerce Redsys Gateway light, democratizing ecommerce.', 'woo-redsys-gateway-light' ); ?></p>
		<div class="wp-badge">
			<?php
			/* translators: plugin version number */
			printf( esc_html__( 'Version %s', 'woo-redsys-gateway-light' ), esc_attr( REDSYS_WOOCOMMERCE_VERSION ) );
			?>
		</div>
		<h2 class="nav-tab-wrapper wp-clearfix">
			<a href="about.php" class="nav-tab nav-tab-active"><?php esc_html_e( 'Other plugins, Skills & APPS', 'woo-redsys-gateway-light' ); ?></a>
		</h2>

		<?php
		require_once REDSYS_PLUGIN_PATH . 'includes/class-redsys-lite-apps-plugins.php';
		Redsys_Lite_Apps_Plugins::render();
		?>

	</div>
	<?php
}
