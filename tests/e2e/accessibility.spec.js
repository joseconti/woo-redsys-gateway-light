// @ts-check
const { test, expect } = require( '@playwright/test' );
const fs = require( 'fs' );
const path = require( 'path' );

/**
 * Automated accessibility pass (WCAG 2.2 AA, D-007) over the markup this
 * plugin contributes, run with axe-core inside the wp-env playground
 * (docs/playground.md, docs/accessibility.md).
 *
 * Each test scans ONE screen in ONE state and limits axe to the region the
 * plugin renders: the gateway rows of the classic and of the Blocks checkout,
 * the generated payment form, the gateway settings forms, the plugin's admin
 * notices and its About page. What WooCommerce, WordPress or the theme render
 * around those regions is theirs and is not asserted here.
 *
 * An automated scan covers part of WCAG only. Keyboard order, focus
 * visibility in use and what a screen reader announces belong to the guided
 * pass recorded in docs/accessibility.md.
 *
 * The raw result of every scan is written to test-results/a11y/ (ignored by
 * Git) so a failure can be read without re-running.
 */

const AXE_SOURCE = fs.readFileSync( require.resolve( 'axe-core/axe.min.js' ), 'utf8' );
const WCAG_TAGS = [ 'wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa' ];
const RESULTS_DIR = path.join( __dirname, '..', '..', 'test-results', 'a11y' );

// `form`: the gateway renders a payment form on the order-pay page (the
// three Redsys gateways share one form id; Inespay redirects instead).
const GATEWAYS = [
	{ id: 'redsys', form: true },
	{ id: 'bizumredsys', form: true },
	{ id: 'googlepayredirecredsys', form: true },
	{ id: 'inespayredsys', form: false },
];
const PAYMENT_FORM = '#redsys_payment_form';

/**
 * Violations found by this pass that are recorded and not fixed yet. Each
 * entry names where the decision is written; remove it when its slice lands.
 */
const KNOWN = [
	// The buttons of the two admin notices take their colours from
	// WooCommerce's own stylesheet for `.woocommerce-message .button-primary`
	// (WooCommerce 7.4 in this playground). Deferred as S-057, D-067.
	{ rule: 'color-contrast', selector: '.woocommerce-redsys-messages .button-primary' },
];

/**
 * Runs axe on the given regions of the page and returns its violations.
 *
 * @param {import('@playwright/test').Page} page    Page to scan.
 * @param {string}                          name    File name for the raw result.
 * @param {string[]}                        include CSS selectors of the regions to scan.
 */
async function scan( page, name, include ) {
	await page.addScriptTag( { content: AXE_SOURCE } );
	const results = await page.evaluate(
		async ( [ selectors, tags ] ) => {
			// @ts-ignore axe is injected above.
			return await window.axe.run( { include: selectors.map( ( s ) => [ s ] ) }, { runOnly: { type: 'tag', values: tags } } );
		},
		[ include, WCAG_TAGS ]
	);
	fs.mkdirSync( RESULTS_DIR, { recursive: true } );
	fs.writeFileSync(
		path.join( RESULTS_DIR, name + '.json' ),
		JSON.stringify( { url: results.url, include, passes: results.passes.map( ( p ) => p.id ), incomplete: results.incomplete, violations: results.violations }, null, 1 )
	);
	const known = await page.evaluate(
		( entries ) => entries.map( ( e ) => ( { rule: e.rule, nodes: Array.from( document.querySelectorAll( e.selector ) ).map( ( el ) => el.outerHTML ) } ) ),
		KNOWN
	);
	return results.violations
		.map( ( v ) => ( {
			rule: v.id,
			impact: v.impact,
			help: v.help,
			nodes: v.nodes
				.filter( ( n ) => ! known.some( ( k ) => k.rule === v.id && k.nodes.includes( n.html ) ) )
				.map( ( n ) => n.target.join( ' ' ) + ' — ' + n.html.slice( 0, 160 ) ),
		} ) )
		.filter( ( v ) => v.nodes.length > 0 );
}

async function fillClassicCheckout( page ) {
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
}

async function logIn( page ) {
	await page.goto( '/wp-login.php' );
	await page.fill( '#user_login', 'admin' );
	await page.fill( '#user_pass', 'password' );
	await page.click( '#wp-submit' );
	await page.waitForURL( /\/wp-admin\// );
}

test.describe( 'classic checkout', () => {
	for ( const gateway of GATEWAYS ) {
		test( `gateway row of ${ gateway.id }, selected, has no WCAG 2.2 AA violation`, async ( { page } ) => {
			await page.goto( '/?add-to-cart=10' );
			await page.goto( '/checkout/' );
			// Inespay is offered only once the billing country is Spain.
			await fillClassicCheckout( page );
			const radio = page.locator( '#payment_method_' + gateway.id );
			await expect( radio ).toBeAttached( { timeout: 15000 } );
			await radio.check();
			await expect( radio ).toBeChecked();
			// The description box of the selected gateway slides open.
			await page.waitForTimeout( 500 );

			const violations = await scan( page, 'classic-row-' + gateway.id, [ 'li.payment_method_' + gateway.id ] );
			expect( violations ).toEqual( [] );
		} );
	}

	for ( const gateway of GATEWAYS.filter( ( g ) => g.form ) ) {
		test( `payment form of ${ gateway.id } has no WCAG 2.2 AA violation`, async ( { page } ) => {
			// Never let a real request reach Redsys's infrastructure.
			await page.route( '**/*redsys.es/**', ( route ) => route.abort() );
			await page.goto( '/?add-to-cart=10' );
			await page.goto( '/checkout/' );
			await fillClassicCheckout( page );
			await page.locator( '#payment_method_' + gateway.id ).check();
			await page.click( '#place_order' );
			await page.waitForURL( /\/checkout\/order-pay\// );
			await expect( page.locator( PAYMENT_FORM ) ).toBeVisible();

			const violations = await scan( page, 'payment-form-' + gateway.id, [ PAYMENT_FORM ] );
			expect( violations ).toEqual( [] );
		} );
	}
} );

test( 'Blocks checkout: the payment options have no WCAG 2.2 AA violation, with each gateway selected in turn', async ( { page } ) => {
	await page.goto( '/?add-to-cart=10' );
	await page.goto( '/checkout-blocks/' );
	const options = page.locator( '.wc-block-components-radio-control-accordion-option' );
	await expect( options.first() ).toBeAttached( { timeout: 15000 } );
	const count = await options.count();
	expect( count ).toBeGreaterThanOrEqual( 3 );

	const found = [];
	for ( let i = 0; i < count; i++ ) {
		await options.nth( i ).locator( 'input[type="radio"]' ).check();
		await page.waitForTimeout( 300 );
		const violations = await scan( page, 'blocks-option-' + i, [ '.wc-block-checkout__payment-method' ] );
		found.push( ...violations );
	}
	expect( found ).toEqual( [] );
} );

test.describe( 'admin', () => {
	for ( const gateway of GATEWAYS ) {
		test( `settings form of ${ gateway.id } has no WCAG 2.2 AA violation`, async ( { page } ) => {
			await logIn( page );
			await page.goto( '/wp-admin/admin.php?page=wc-settings&tab=checkout&section=' + gateway.id );
			await expect( page.locator( '#mainform' ) ).toBeVisible();

			// The plugin notices are printed inside this form on a settings
			// screen; they have their own test below.
			await page.evaluate( () => document.querySelectorAll( '.woocommerce-redsys-messages' ).forEach( ( el ) => el.remove() ) );

			const violations = await scan( page, 'settings-' + gateway.id, [ '#mainform' ] );
			expect( violations ).toEqual( [] );
		} );
	}

	test( 'the plugin notices have no WCAG 2.2 AA violation', async ( { page } ) => {
		await logIn( page );
		await page.goto( '/wp-admin/index.php' );
		const notices = page.locator( '.woocommerce-redsys-messages' );
		// Shown until an administrator dismisses them; nothing to scan otherwise.
		test.skip( 0 === ( await notices.count() ), 'both notices are dismissed in this playground' );

		const violations = await scan( page, 'admin-notices', [ '.woocommerce-redsys-messages' ] );
		expect( violations ).toEqual( [] );
	} );

	test( 'the About page has no WCAG 2.2 AA violation', async ( { page } ) => {
		await logIn( page );
		await page.goto( '/wp-admin/admin.php?page=redsys-about-page' );
		await expect( page.locator( '.about-wrap-redsys' ) ).toBeVisible();

		const violations = await scan( page, 'about-page', [ '.about-wrap-redsys' ] );
		expect( violations ).toEqual( [] );
	} );
} );
