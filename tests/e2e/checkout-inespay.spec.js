// @ts-check
const { test, expect } = require( '@playwright/test' );

/**
 * Checkout smoke test: guest checkout with the Inespay (bank transfer)
 * gateway, through WordPress + WooCommerce + this plugin in the wp-env
 * playground (docs/playground.md).
 *
 * Unlike the other three gateways, Inespay's process_payment() makes a real
 * SERVER-SIDE wp_remote_post() to apiflow.inespay.com during checkout,
 * rather than redirecting the browser to a self-submitting form —
 * Playwright's page.route() cannot intercept that (it never reaches the
 * browser). Instead, .wp-env-mu-plugins/inespay-http-stub.php fakes the API
 * response on the PHP side, gated behind the redsyslite_e2e_stub_inespay
 * option (off by default, never touches production code paths) — see
 * docs/playground.md and D-030 (docs/decisions.md).
 *
 * Inespay is also only offered in ES/PT/IT (is_allowed_country(),
 * class-wc-gateway-inespay-redsys.php:161) — this playground's store base
 * location is US, so the test selects Spain as the billing country and
 * waits for WooCommerce's checkout AJAX to refresh the payment method list
 * before asserting Inespay is offered.
 */

test( 'guest checkout with the Inespay gateway redirects to the (stubbed) payment link', async ( { page } ) => {
	// The stubbed API response redirects back to this site itself
	// (inespay-http-stub.php builds the link with home_url()), so no
	// external network interception is needed here — only the PHP-side
	// stub above short-circuits the real API call.

	await page.goto( '/?add-to-cart=10' );

	await page.goto( '/checkout/' );

	await page.fill( '#billing_first_name', 'Ada' );
	await page.fill( '#billing_last_name', 'Lovelace' );
	await page.fill( '#billing_address_1', 'Calle Mayor 1' );
	await page.fill( '#billing_city', 'Madrid' );
	// Selecting Spain triggers WooCommerce's checkout AJAX refresh, which is
	// what makes Inespay appear (is_allowed_country() excludes this
	// playground's US base location).
	await page.selectOption( '#billing_country', 'ES' );
	const state = page.locator( '#billing_state' );
	if ( await state.count() ) {
		const tag = await state.evaluate( ( el ) => el.tagName );
		if ( 'SELECT' === tag ) {
			await page.selectOption( '#billing_state', { index: 1 } );
		} else {
			await state.fill( 'Madrid' );
		}
	}
	await page.fill( '#billing_postcode', '28001' );
	await page.fill( '#billing_phone', '600000000' );
	await page.fill( '#billing_email', 'ada@example.com' );

	const inespayRadio = page.locator( '#payment_method_inespayredsys' );
	await expect( inespayRadio ).toBeAttached( { timeout: 15000 } );
	await inespayRadio.check();
	await expect( inespayRadio ).toBeChecked();

	await page.click( '#place_order' );

	await page.waitForURL( /inespay_e2e_return=1/ );
} );
