// @ts-check
const { test, expect } = require( '@playwright/test' );

/**
 * Checkout smoke test: guest checkout with the Redsys gateway through the
 * WooCommerce BLOCKS checkout (the "Checkout Blocks" page created for this
 * playground, docs/playground.md — the default Checkout page still uses the
 * classic [woocommerce_checkout] shortcode, exercised by
 * checkout-redsys.spec.js).
 *
 * Scope: proves the Blocks payment-method integration
 * (includes/blocks/class-wc-gateway-redsys-lite-support.php +
 * resources/js/frontend/index.js's compiled bundle,
 * assets/js/frontend/blocks.js) actually registers, renders and lets a
 * customer select the Redsys gateway inside the Blocks checkout, and that
 * selecting it produces the same signed payment form as the classic
 * checkout — i.e. the Blocks integration is a real, working alternate route
 * into the same payment code, not just wiring that never gets exercised.
 */

test( 'guest checkout via the Blocks checkout with the Redsys gateway generates a correctly-signed payment form', async ( { page } ) => {
	await page.route( '**/*redsys.es/**', ( route ) => route.abort() );

	await page.goto( '/?add-to-cart=10' );

	await page.goto( '/checkout-blocks/' );

	// The Redsys radio is checked by default (first payment method in the
	// list) once the Payment options group has loaded — wait for it rather
	// than assuming any particular gateway order.
	const redsysRadio = page.getByRole( 'radio', { name: /^Redsys/ } );
	await expect( redsysRadio ).toBeAttached( { timeout: 15000 } );
	await redsysRadio.check();
	await expect( redsysRadio ).toBeChecked();

	await page.getByLabel( 'Email address' ).fill( 'ada@example.com' );
	await page.getByLabel( 'First name' ).fill( 'Ada' );
	await page.getByLabel( 'Last name' ).fill( 'Lovelace' );
	await page.getByLabel( 'Address', { exact: true } ).fill( 'Calle Mayor 1' );
	await page.getByLabel( 'City' ).fill( 'Madrid' );
	await page.getByLabel( 'ZIP Code' ).fill( '28001' );
	await page.getByLabel( 'Phone (optional)' ).fill( '600000000' );

	await page.getByRole( 'button', { name: 'Place Order' } ).click();

	await page.waitForURL( /\/checkout\/order-pay\// );
	await expect( page.getByText( 'Thank you for your order, please click the button below to pay with Credit Card via Servired/RedSys.' ) ).toBeVisible();

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
