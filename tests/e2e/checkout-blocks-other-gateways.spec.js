// @ts-check
const { test, expect } = require( '@playwright/test' );

/**
 * AC-46 (slice S-060): Bizum, Google Pay redirection and Inespay are each
 * registered in the Blocks checkout with their title, description and icon.
 *
 * checkout-blocks-redsys.spec.js proves the card gateway there; this spec
 * proves the other three, in the wp-env playground (docs/playground.md),
 * on the "Checkout Blocks" page. What each gateway is expected to show is
 * read from the data the plugin hands the Blocks checkout for it, then
 * looked for in the rendered payment options.
 *
 * Inespay hands its data to the Blocks checkout only for a customer in ES,
 * PT or IT, and the playground's store is based elsewhere, so that test
 * first makes the customer Spanish through the classic checkout. The
 * country rule itself is covered on the server side in
 * tests/Integration/BlocksPaymentMethodsTest.php.
 */

const GATEWAYS = [
	{ id: 'bizumredsys', title: 'Bizum', description: 'Pay via Bizum you can pay with your Bizum account.' },
	{ id: 'googlepayredirecredsys', title: 'Google Pay', description: 'Pay via GPay you can pay with your Google account.' },
	{ id: 'inespayredsys', title: 'Inespay Bank Transfer', description: 'Pay via instant bank transfer.' },
];

for ( const gateway of GATEWAYS ) {
	test( `AC-46: ${ gateway.id } is offered in the Blocks checkout with its title, description and icon`, async ( { page } ) => {
		await page.goto( '/?add-to-cart=10' );
		if ( 'inespayredsys' === gateway.id ) {
			// The classic checkout saves the chosen country in the customer's
			// session; Inespay appearing there shows the server has it.
			await page.goto( '/checkout/' );
			await page.selectOption( '#billing_country', 'ES' );
			await expect( page.locator( '#payment_method_inespayredsys' ) ).toBeAttached( { timeout: 15000 } );
		}
		await page.goto( '/checkout-blocks/' );

		const radio = page.locator( `input[type="radio"][value="${ gateway.id }"]` );
		await expect( radio ).toBeAttached( { timeout: 15000 } );

		// What the plugin registered for this gateway.
		const data = await page.evaluate( ( id ) => window.wc.wcSettings.getSetting( `${ id }_data`, null ), gateway.id );
		expect( data ).toMatchObject( { title: gateway.title, description: gateway.description } );
		expect( data.icon ).toMatch( /^https?:\/\/.+\/assets\/images\/.+\.(png|svg|jpg)$/ );

		// And what the customer is shown.
		const option = page.locator( '.wc-block-components-radio-control-accordion-option' ).filter( { has: radio } );
		await expect( option ).toContainText( gateway.title );
		const icon = option.locator( `img[alt="${ gateway.title }"]` );
		await expect( icon ).toHaveAttribute( 'src', data.icon );
		const loaded = await icon.evaluate( ( /** @type {HTMLImageElement} */ img ) => img.complete && img.naturalWidth > 0 );
		expect( loaded, 'the icon is an image that loads' ).toBe( true );

		await radio.check();
		await expect( radio ).toBeChecked();
		await expect( option ).toContainText( gateway.description );
	} );
}
