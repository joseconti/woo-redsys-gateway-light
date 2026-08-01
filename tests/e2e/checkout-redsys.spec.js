// @ts-check
const { test, expect } = require( '@playwright/test' );

/**
 * Checkout smoke test: guest checkout with the Redsys (card redirection)
 * gateway, through WordPress + WooCommerce + this plugin in the wp-env
 * playground (docs/playground.md).
 *
 * Scope: proves the classic (shortcode) checkout → gateway selection →
 * order creation → Redsys payment-form generation chain works end to end,
 * with the exact hidden fields (Ds_MerchantParameters/Ds_Signature) a real
 * notification will later need to match. It deliberately never lets a real
 * request reach Redsys's servers (every request to *.redsys.es is aborted)
 * — this is a LOCAL smoke test, not a real Redsys sandbox round trip
 * (docs/playground.md already documents that as CREDENTIAL/unverified: no
 * Redsys test merchant credentials exist for this project beyond Redsys's
 * own published test FUC/secret, used here only to exercise this plugin's
 * own form-generation code).
 *
 * Not covered: the WooCommerce Blocks checkout (this page uses the
 * classic [woocommerce_checkout] shortcode) and the other three gateways —
 * see docs/PROGRESS.md deferred items.
 */

test( 'guest checkout with the Redsys gateway generates a correctly-signed payment form', async ( { page } ) => {
	// Never let a real request reach Redsys's infrastructure.
	await page.route( '**/*redsys.es/**', ( route ) => route.abort() );

	await page.goto( '/?add-to-cart=10' );

	await page.goto( '/checkout/' );
	// Redsys is the only enabled gateway, so WooCommerce auto-selects it and
	// hides the radio input (its standard "only one payment method" CSS) —
	// assert it's attached and already checked, rather than visible/clicked.
	const redsysRadio = page.locator( '#payment_method_redsys' );
	await expect( redsysRadio ).toBeAttached();
	await expect( redsysRadio ).toBeChecked();

	await page.fill( '#billing_first_name', 'Ada' );
	await page.fill( '#billing_last_name', 'Lovelace' );
	await page.fill( '#billing_address_1', 'Calle Mayor 1' );
	await page.fill( '#billing_city', 'Madrid' );
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

	await page.click( '#place_order' );

	await page.waitForURL( /\/checkout\/order-pay\// );
	await expect( page.getByText( 'Thank you for your order, please click the button below to pay with Credit Card via Servired/RedSys.' ) ).toBeVisible();

	const form = page.locator( '#redsys_payment_form' );
	await expect( form ).toBeVisible();

	const action = await form.getAttribute( 'action' );
	expect( action ).toMatch( /^https:\/\/sis-t\.redsys\.es(:25443)?\/sis\/realizarPago/ );

	// The exact fields a real Redsys notification's signature depends on.
	await expect( form.locator( 'input[name="Ds_SignatureVersion"]' ) ).toHaveCount( 1 );
	await expect( form.locator( 'input[name="Ds_MerchantParameters"]' ) ).toHaveCount( 1 );
	await expect( form.locator( 'input[name="Ds_Signature"]' ) ).toHaveCount( 1 );

	const merchantParameters = await form.locator( 'input[name="Ds_MerchantParameters"]' ).getAttribute( 'value' );
	expect( merchantParameters ).toBeTruthy();
	const decoded = JSON.parse( Buffer.from( merchantParameters, 'base64' ).toString( 'utf8' ) );
	expect( decoded.DS_MERCHANT_MERCHANTCODE ).toBe( '999008881' );
	expect( decoded.DS_MERCHANT_TERMINAL ).toBe( '1' );
} );
