// @ts-check
const { test, expect } = require( '@playwright/test' );
const { execFileSync } = require( 'child_process' );

/**
 * The screens behind AC-15, AC-47, AC-49, AC-55 and AC-56 (slice S-060), in a
 * real browser session on the playground: the test-mode warning at the
 * checkout, each gateway's settings screen and what saving it stores, the
 * About Redsys page and who may open it, the payment details on the admin
 * order screen, and the transaction details on the order-received page.
 *
 * The same criteria are driven in-process, with the cases a browser cannot
 * reach (every gateway, every branch), in tests/Integration/
 * CardGatewayAsBuiltTest.php, GatewaySettingsAsBuiltTest.php,
 * AboutPageAsBuiltTest.php and OrderPaymentDetailsAsBuiltTest.php.
 *
 * As-built: nothing here changes shipped code. No request reaches
 * *.redsys.es — the paid order is made by posting a notification signed by
 * tests/e2e/fixtures/notification-fixture.php with the playground's test
 * secret.
 */

const FIXTURE = 'wp-content/plugins/woo-redsys-gateway-light/tests/e2e/fixtures/notification-fixture.php';

const WARNING = 'Warning: WooCommerce Redsys Gateway Light is in test mode. Remember to uncheck it when you go live';

// The tables of docs/usage/configuration.md, per gateway.
const FIELDS = {
	redsys: [ 'enabled', 'title', 'description', 'logo', 'customer', 'commercename', 'payoptions', 'terminal', 'not_use_https', 'lwvactive', 'orderdo', 'secretsha256', 'customtestsha256', 'redsyslanguage', 'testmode', 'debug' ],
	bizumredsys: [ 'enabled', 'title', 'description', 'logo', 'customer', 'commercename', 'terminal', 'orderdo', 'transactionlimit', 'not_use_https', 'secretsha256', 'customtestsha256', 'redsyslanguage', 'testmode', 'debug' ],
	googlepayredirecredsys: [ 'enabled', 'title', 'description', 'customer', 'commercename', 'terminal', 'not_use_https', 'secretsha256', 'customtestsha256', 'redsyslanguage', 'testmode', 'debug' ],
	inespayredsys: [ 'enabled', 'title', 'description', 'logo', 'api_key', 'api_token', 'creditor_account', 'expiration', 'orderdo', 'transactionlimit', 'testmode', 'debug' ],
};

const SHOP_MANAGER = 'e2e-shop-manager';

/**
 * Run WP-CLI in the instance under test.
 *
 * @param {...string} args WP-CLI arguments.
 * @return {string} Its output.
 */
function wp( ...args ) {
	// WP_ENV_CONFIG selects the instance under test (docs/playground.md).
	const instance = process.env.WP_ENV_CONFIG ? [ '--config', process.env.WP_ENV_CONFIG ] : [];
	return execFileSync( 'npx', [ 'wp-env', 'run', ...instance, 'cli', 'wp', ...args ], { stdio: [ 'ignore', 'pipe', 'pipe' ] } ).toString();
}

/**
 * Run the fixture in the instance under test and return its answer.
 *
 * @param {...string} args Fixture arguments.
 * @return {any} Parsed answer.
 */
function fixture( ...args ) {
	const out = wp( 'eval-file', FIXTURE, ...args );
	const line = out.split( '\n' ).find( ( l ) => l.startsWith( 'E2E-JSON:' ) );
	if ( ! line ) {
		throw new Error( `The fixture gave no answer for: ${ args.join( ' ' ) }\n${ out }` );
	}
	return JSON.parse( line.slice( 'E2E-JSON:'.length ) );
}

/**
 * The stored settings of a gateway.
 *
 * @param {string} gatewayId Gateway ID.
 * @return {Record<string, string>} Settings.
 */
function storedSettings( gatewayId ) {
	const out = wp( 'option', 'get', `woocommerce_${ gatewayId }_settings`, '--format=json' );
	const line = out.split( '\n' ).find( ( l ) => l.startsWith( '{' ) );
	if ( ! line ) {
		throw new Error( `No stored settings for ${ gatewayId }\n${ out }` );
	}
	return JSON.parse( line );
}

/**
 * Log in through the login form.
 *
 * @param {import('@playwright/test').Page} page     Page.
 * @param {string}                          user     User name.
 * @param {string}                          password Password.
 */
async function logIn( page, user, password ) {
	await page.goto( '/wp-login.php' );
	await page.fill( '#user_login', user );
	await page.fill( '#user_pass', password );
	await page.click( '#wp-submit' );
	await page.waitForURL( /\/wp-admin\// );
}

test( 'AC-15: the classic checkout shows the test-mode warning of the card gateway', async ( { page } ) => {
	const settings = storedSettings( 'redsys' );
	expect( settings.testmode, 'the playground runs the card gateway in test mode' ).toBe( 'yes' );
	expect( settings.enabled ).toBe( 'yes' );

	await page.goto( '/?add-to-cart=10' );
	await page.goto( '/checkout/' );

	await expect( page.locator( 'form.checkout' ) ).toBeVisible();
	await expect( page.locator( '.checkout-message', { hasText: WARNING } ) ).toBeVisible();
} );

test.describe( 'AC-47: settings screens', () => {
	for ( const [ gatewayId, fields ] of Object.entries( FIELDS ) ) {
		test( `the ${ gatewayId } screen shows every documented field`, async ( { page } ) => {
			await logIn( page, 'admin', 'password' );
			await page.goto( `/wp-admin/admin.php?page=wc-settings&tab=checkout&section=${ gatewayId }` );

			for ( const key of fields ) {
				await expect( page.locator( `#woocommerce_${ gatewayId }_${ key }` ), key ).toBeVisible();
			}
			await expect( page.locator( `[id^="woocommerce_${ gatewayId }_"]` ), 'no field the document does not list' ).toHaveCount( fields.length );
		} );
	}

	test( 'saving the card screen stores what was typed in woocommerce_redsys_settings, and nothing else changes', async ( { page } ) => {
		const before = storedSettings( 'redsys' );
		const typed = 'Typed On The Settings Screen';
		expect( before.commercename ).not.toBe( typed );

		await logIn( page, 'admin', 'password' );
		const screen = '/wp-admin/admin.php?page=wc-settings&tab=checkout&section=redsys';
		try {
			await page.goto( screen );
			await page.fill( '#woocommerce_redsys_commercename', typed );
			await page.click( 'button.woocommerce-save-button' );
			await expect( page.getByText( 'Your settings have been saved.' ) ).toBeVisible();

			const after = storedSettings( 'redsys' );
			expect( after.commercename ).toBe( typed );
			expect( Object.keys( after ).sort() ).toEqual( [ ...FIELDS.redsys ].sort() );
			for ( const [ key, value ] of Object.entries( before ) ) {
				if ( 'commercename' !== key ) {
					expect( after[ key ], key ).toBe( value );
				}
			}
		} finally {
			// Put the playground back the way the other specs expect it.
			await page.goto( screen );
			await page.fill( '#woocommerce_redsys_commercename', before.commercename );
			await page.click( 'button.woocommerce-save-button' );
			await expect( page.getByText( 'Your settings have been saved.' ) ).toBeVisible();
		}
		expect( storedSettings( 'redsys' ).commercename ).toBe( before.commercename );
	} );
} );

test.describe( 'AC-49: About Redsys', () => {
	test.beforeAll( () => {
		try {
			wp( 'user', 'delete', SHOP_MANAGER, '--yes' );
		} catch ( e ) {
			// Not there yet.
		}
		wp( 'user', 'create', SHOP_MANAGER, `${ SHOP_MANAGER }@example.com`, '--role=shop_manager', `--user_pass=${ SHOP_MANAGER }` );
	} );

	test.afterAll( () => {
		wp( 'user', 'delete', SHOP_MANAGER, '--yes' );
	} );

	test( 'an administrator finds the page in the WooCommerce menu and it lists the other plugins', async ( { page } ) => {
		await logIn( page, 'admin', 'password' );
		await page.goto( '/wp-admin/index.php' );

		const link = page.locator( '#toplevel_page_woocommerce a', { hasText: 'About Redsys' } );
		await expect( link ).toHaveAttribute( 'href', 'admin.php?page=redsys-about-page' );
		await page.goto( '/wp-admin/admin.php?page=redsys-about-page' );

		await expect( page.locator( '.redsys-apps h2', { hasText: 'Other plugins, Skills & APPS' } ) ).toBeVisible();
		for ( const heading of [ 'Free plugins', 'Premium plugins' ] ) {
			await expect( page.locator( '.redsys-apps h2', { hasText: heading } ) ).toBeVisible();
		}
		await expect( page.locator( '.redsys-apps .plug-card' ).first() ).toBeVisible();
	} );

	test( 'a shop manager, who cannot manage options, has no such menu entry and is refused the page', async ( { page } ) => {
		await logIn( page, SHOP_MANAGER, SHOP_MANAGER );
		await page.goto( '/wp-admin/index.php' );

		// The WooCommerce menu itself is there for this user; the entry is not.
		await expect( page.locator( '#toplevel_page_woocommerce' ) ).toBeVisible();
		await expect( page.locator( '#toplevel_page_woocommerce a', { hasText: 'About Redsys' } ) ).toHaveCount( 0 );

		const response = await page.goto( '/wp-admin/admin.php?page=redsys-about-page' );
		expect( response && response.status() ).toBe( 403 );
		await expect( page.getByText( 'Sorry, you are not allowed to access this page.' ) ).toBeVisible();
		await expect( page.locator( '.redsys-apps' ) ).toHaveCount( 0 );
	} );
} );

test.describe( 'AC-55 and AC-56: a paid card order', () => {
	/** @type {any} */
	let made;
	/** @type {any} */
	let urls;

	test.beforeAll( async ( { playwright, baseURL } ) => {
		made = fixture( 'make', 'redsys', 'pending', '0000', 'match' );
		const request = await playwright.request.newContext( { baseURL } );
		const response = await request.post( '/?wc-api=WC_Gateway_redsys', { form: made.form } );
		expect( response.status(), 'the notification passed the signature check' ).toBe( 200 );
		await request.dispose();
		expect( fixture( 'read', String( made.before.order_id ) ).status ).toBe( 'processing' );
		urls = fixture( 'urls', String( made.before.order_id ) );
	} );

	test( 'AC-55: the admin order screen shows the gateway, the Redsys order number, date, hour and authorisation code', async ( { page } ) => {
		await logIn( page, 'admin', 'password' );
		await page.goto( urls.edit );

		const details = page.locator( '#order_data' );
		await expect( details.getByRole( 'heading', { name: 'Payment Details' } ) ).toBeVisible();
		await expect( details.locator( 'p', { hasText: 'Paid with:' } ) ).toContainText( 'redsys' );
		await expect( details.locator( 'p', { hasText: 'Redsys Order Number:' } ) ).toContainText( made.ds_order );
		await expect( details.locator( 'p', { hasText: 'Redsys Date:' } ) ).toContainText( '06/10/2026' );
		await expect( details.locator( 'p', { hasText: 'Redsys Hour:' } ) ).toContainText( '21:15' );
		await expect( details.locator( 'p', { hasText: 'Redsys Authorisation Code:' } ) ).toContainText( '123456' );
	} );

	test( 'AC-56: the order-received page lists the site, merchant code, authorisation number, store name, date and hour', async ( { page } ) => {
		await page.goto( urls.received );

		// By what it says, not by its class: the classic page marks the
		// paragraph `woocommerce-thankyou-order-received`, the order
		// confirmation block of a current WooCommerce does not (S-071).
		const text = page.locator( 'p', { hasText: 'Thanks for your purchase, the details of your transaction are:' } );
		await expect( text ).toHaveCount( 1 );
		await expect( text ).toContainText( `Website: ${ urls.site }` );
		await expect( text ).toContainText( 'FUC: 999008881' );
		await expect( text ).toContainText( 'Authorization Number: 123456' );
		await expect( text ).toContainText( `Commerce Name: ${ urls.name }` );
		await expect( text ).toContainText( 'Date: 06/10/2026' );
		await expect( text ).toContainText( 'Hour: 21:15' );
	} );

	test( 'AC-56: the order-received page of an order that is not paid keeps WooCommerce\'s own sentence', async ( { page } ) => {
		const pending = fixture( 'make', 'redsys', 'pending', '0000', 'match' );
		const pendingUrls = fixture( 'urls', String( pending.before.order_id ) );

		await page.goto( pendingUrls.received );

		await expect( page.locator( 'p', { hasText: 'Your order has been received' } ) ).toBeVisible();
		const body = page.locator( 'body' );
		await expect( body ).not.toContainText( 'the details of your transaction are' );
		await expect( body ).not.toContainText( 'FUC:' );
	} );
} );
