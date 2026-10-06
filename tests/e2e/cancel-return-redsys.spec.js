// @ts-check
const { test, expect } = require( '@playwright/test' );
const { execFileSync } = require( 'child_process' );

/**
 * Forge issue 112 (slice S-038), driven end to end in the wp-env playground
 * (docs/playground.md): a guest starts a Redsys payment, Redsys cancels the
 * order server to server, and the customer comes back through the exact
 * DS_MERCHANT_URLKO the plugin signed.
 *
 * Scope: proves the cancel URL in the signed payload is a real URL (literal
 * `&`), that WooCommerce's cancel handler runs on it, and that the customer
 * is told the order was cancelled instead of being shown "Your order can no
 * longer be cancelled".
 *
 * Not a real Redsys round trip: no request reaches *.redsys.es, and the
 * notification's effect (order → cancelled) is applied with WP-CLI, which is
 * what a refused or abandoned payment does through the notification handler.
 */

test( 'a customer returning from a payment Redsys cancelled is told the order was cancelled', async ( { page } ) => {
	// Never let a real request reach Redsys's infrastructure.
	await page.route( '**/*redsys.es/**', ( route ) => route.abort() );

	await page.goto( '/?add-to-cart=10' );
	await page.goto( '/checkout/' );
	const redsysRadio = page.locator( '#payment_method_redsys' );
	await expect( redsysRadio ).toBeAttached();
	await redsysRadio.check();

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
	const orderId = page.url().match( /order-pay\/(\d+)/ )?.[ 1 ];
	expect( orderId ).toBeTruthy();

	const merchantParameters = await page.locator( '#redsys_payment_form input[name="Ds_MerchantParameters"]' ).getAttribute( 'value' );
	const decoded = JSON.parse( Buffer.from( merchantParameters ?? '', 'base64' ).toString( 'utf8' ) );
	const urlKo = decoded.DS_MERCHANT_URLKO;
	expect( urlKo ).not.toContain( '&amp;' );
	expect( new URL( urlKo ).searchParams.get( 'order_id' ) ).toBe( orderId );

	// What the notification handler does when Redsys reports the payment refused or abandoned.
	execFileSync( 'npx', [ 'wp-env', 'run', 'cli', 'wp', 'eval', `wc_get_order( ${ Number( orderId ) } )->update_status( 'cancelled', 'Cancelled by Redsys' );` ], { stdio: 'pipe' } );

	await page.goto( urlKo );

	await expect( page.getByText( 'Your order was cancelled.' ) ).toBeVisible();
	await expect( page.getByText( 'Your order can no longer be cancelled' ) ).toHaveCount( 0 );
} );
