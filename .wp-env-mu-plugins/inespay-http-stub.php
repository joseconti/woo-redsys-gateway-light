<?php
/**
 * Dev/test-only Inespay API stub for this project's wp-env playground.
 *
 * Inespay's process_payment() (classes/class-wc-gateway-inespay-redsys.php)
 * makes a real server-side wp_remote_post() to apiflow.inespay.com during
 * checkout, unlike the other three gateways which just redirect the browser
 * to a self-submitting Redsys form. Playwright's page.route() cannot
 * intercept a server-side request, so this mu-plugin fakes the API response
 * on the PHP side instead — the plugin's own request-building and
 * redirect-handling code runs completely unmodified; only the outbound
 * network call is short-circuited.
 *
 * Never packaged into the shipped plugin: mapped only via .wp-env.json's
 * "mu-plugins" key, dev/test infra exactly like tests/ or
 * playwright.config.js. Inert unless the redsyslite_e2e_stub_inespay option
 * is explicitly set to 'yes' (see docs/playground.md), so it can never
 * affect a real site.
 *
 * @package WooCommerce Redsys Gateway Light
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_filter(
	'pre_http_request',
	function ( $preempt, $parsed_args, $url ) {
		if ( 'yes' !== get_option( 'redsyslite_e2e_stub_inespay' ) ) {
			return $preempt;
		}

		if ( false === strpos( $url, 'apiflow.inespay.com' ) ) {
			return $preempt;
		}

		return array(
			'headers'  => array(),
			'body'     => wp_json_encode(
				array(
					'singlePayinId'   => 'e2e-test-payin-id',
					'singlePayinLink' => add_query_arg( 'inespay_e2e_return', '1', home_url( '/' ) ),
				)
			),
			'response' => array(
				'code'    => 200,
				'message' => 'OK',
			),
			'cookies'  => array(),
			'filename' => null,
		);
	},
	10,
	3
);
