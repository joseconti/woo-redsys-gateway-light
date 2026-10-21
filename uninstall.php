<?php
/**
 * Uninstall: removes the plugin's own bookkeeping only.
 *
 * The gateway settings (signing secrets and Inespay credentials included),
 * the payment tokens and the order data are kept on purpose, so a merchant
 * who reinstalls does not have to enter the bank data again (D-085).
 * Transients kept in a persistent object cache cannot be listed; they expire
 * by themselves within a day.
 *
 * The premium plugin (WooCommerce Servired/RedSys Spain Gateway) uses the
 * same option and transient names. When it is installed — the usual reason
 * to delete this plugin is moving to it — only the names this plugin alone
 * uses are removed, so its version, notices and in-flight payments are left
 * intact (D-087).
 *
 * @package WooCommerce Redsys Gateway Light
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( ! function_exists( 'redsyslite_premium_is_installed' ) ) {
	/**
	 * Whether the premium plugin is installed on this site, active or not.
	 *
	 * @since 7.1.0
	 * @return bool
	 */
	function redsyslite_premium_is_installed() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		foreach ( get_plugins() as $file => $data ) {
			if ( 'woocommerce-gateway-redsys.php' === basename( $file ) || ( isset( $data['TextDomain'] ) && 'woocommerce-gateway-redsys' === $data['TextDomain'] ) ) {
				return true;
			}
		}
		return false;
	}
}

if ( ! function_exists( 'redsyslite_delete_transients_with_prefix' ) ) {
	/**
	 * Delete every transient of the current site whose name starts with a prefix.
	 *
	 * @since 7.1.0
	 * @param string $prefix Transient name prefix.
	 */
	function redsyslite_delete_transients_with_prefix( $prefix ) {
		global $wpdb;

		$names = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
				$wpdb->esc_like( '_transient_' . $prefix ) . '%'
			)
		);
		foreach ( $names as $name ) {
			delete_transient( substr( $name, strlen( '_transient_' ) ) );
		}
	}
}

if ( ! function_exists( 'redsyslite_uninstall_site' ) ) {
	/**
	 * Remove the plugin's options and transients from the current site.
	 *
	 * @since 7.1.0
	 * @param bool $shared_too Also remove the names shared with the premium plugin.
	 */
	function redsyslite_uninstall_site( $shared_too = true ) {
		global $wpdb;

		// Used by this plugin only.
		redsyslite_delete_transients_with_prefix( 'redsyslite_mark_paid_attempt_' );

		if ( ! $shared_too ) {
			return;
		}

		foreach ( array( 'woocommerce-redsys-version', 'woocommerce-redsys-rate', 'hide-new-version-redsys-notice', 'telegram-redsys-notice' ) as $option ) {
			delete_option( $option );
		}
		redsyslite_delete_transients_with_prefix( 'redsys_signature_' );
		redsyslite_delete_transients_with_prefix( 'redys_order_temp_' );

		// Refund flags are named after the order: "<order id>_redsys_refund".
		$names = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
				$wpdb->esc_like( '_transient_' ) . '%' . $wpdb->esc_like( '_redsys_refund' )
			)
		);
		foreach ( $names as $name ) {
			$transient = substr( $name, strlen( '_transient_' ) );
			if ( preg_match( '/^\d+_redsys_refund$/', $transient ) ) {
				delete_transient( $transient );
			}
		}
	}
}

$redsyslite_shared_too = ! redsyslite_premium_is_installed();

if ( is_multisite() ) {
	foreach ( get_sites(
		array(
			'fields' => 'ids',
			'number' => 0,
		)
	) as $redsyslite_site_id ) {
		switch_to_blog( (int) $redsyslite_site_id );
		redsyslite_uninstall_site( $redsyslite_shared_too );
		restore_current_blog();
	}
} else {
	redsyslite_uninstall_site( $redsyslite_shared_too );
}
