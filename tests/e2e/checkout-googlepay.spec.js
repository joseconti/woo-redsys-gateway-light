// @ts-check
const { test, expect } = require( '@playwright/test' );

/**
 * Checkout smoke test: guest checkout with the Google Pay (redirection)
 * gateway, through WordPress + WooCommerce + this plugin in the wp-env
 * playground (docs/playground.md). Mirrors checkout-redsys.spec.js —
 * Google Pay generates the same kind of self-submitting
 * Ds_MerchantParameters/Ds_Signature form as Redsys, against the same
 * *.redsys.es sandbox; only the gateway id, radio selector, thank-you copy
 * and settings option differ.
 *
 * It deliberately never lets a real request reach Redsys's servers (every
 * request to *.redsys.es is aborted) — a LOCAL smoke test, not a real
 * sandbox round trip.
 */

test( 'guest checkout with the Google Pay gateway generates a correctly-signed payment form', async ( { page } ) => {
	await page.route( '**/*redsys.es/**', ( route ) => route.abort() );

	await page.goto( '/?add-to-cart=10' );

	await page.goto( '/checkout/' );
	const gpayRadio = page.locator( '#payment_method_googlepayredirecredsys' );
	await expect( gpayRadio ).toBeAttached();
	await gpayRadio.check();
	await expect( gpayRadio ).toBeChecked();

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
	await expect( page.getByText( 'Thank you for your order, please click the button below to pay with Google Pay.' ) ).toBeVisible();

	const form = page.locator( '#redsys_payment_form' );
	await expect( form ).toBeVisible();

	const action = await form.getAttribute( 'action' );
	expect( action ).toMatch( /^https:\/\/sis-t\.redsys\.es(:25443)?\/sis\/realizarPago/ );

	await expect( form.locator( 'input[name="Ds_SignatureVersion"]' ) ).toHaveCount( 1 );
	await expect( form.locator( 'input[name="Ds_MerchantParameters"]' ) ).toHaveCount( 1 );
	await expect( form.locator( 'input[name="Ds_Signature"]' ) ).toHaveCount( 1 );

	const merchantParameters = await form.locator( 'input[name="Ds_MerchantParameters"]' ).getAttribute( 'value' );
	expect( merchantParameters ).toBeTruthy();
	const decoded = JSON.parse( Buffer.from( merchantParameters, 'base64' ).toString( 'utf8' ) );
	expect( decoded.DS_MERCHANT_MERCHANTCODE ).toBe( '999008881' );
	expect( decoded.DS_MERCHANT_TERMINAL ).toBe( '1' );
} );
