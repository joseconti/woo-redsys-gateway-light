// @ts-check
const { test, expect } = require( '@playwright/test' );
const { execFileSync } = require( 'child_process' );

/**
 * What the card, Bizum and Google Pay gateways do with a notification that
 * passes the signature check (slice S-060): AC-09 to AC-12, AC-23 and AC-31.
 *
 * Driven over HTTP on purpose. The handlers end in a bare `exit` on the
 * amount-mismatch and already-paid paths, so inside PHPUnit they end the
 * test runner (L-012). Here a signed notification is POSTed to the real
 * `?wc-api=WC_Gateway_<id>` endpoint of the playground and the order is read
 * back with WP-CLI.
 *
 * Not a real Redsys round trip: the notification is built and signed by
 * tests/e2e/fixtures/notification-fixture.php with the playground's test
 * secret, the way Redsys signs one. No request reaches *.redsys.es.
 */

const FIXTURE = 'wp-content/plugins/woo-redsys-gateway-light/tests/e2e/fixtures/notification-fixture.php';

const GATEWAYS = {
	redsys: 'WC_Gateway_redsys',
	bizumredsys: 'WC_Gateway_bizumredsys',
	googlepayredirecredsys: 'WC_Gateway_googlepayredirecredsys',
};

/**
 * Run the fixture in the instance under test and return its answer.
 *
 * @param {...string} args Fixture arguments.
 * @return {any} Parsed answer.
 */
function fixture( ...args ) {
	// WP_ENV_CONFIG selects the instance under test (docs/playground.md).
	const instance = process.env.WP_ENV_CONFIG ? [ '--config', process.env.WP_ENV_CONFIG ] : [];
	const out = execFileSync( 'npx', [ 'wp-env', 'run', ...instance, 'cli', 'wp', 'eval-file', FIXTURE, ...args ], { stdio: [ 'ignore', 'pipe', 'pipe' ] } ).toString();
	const line = out.split( '\n' ).find( ( l ) => l.startsWith( 'E2E-JSON:' ) );
	if ( ! line ) {
		throw new Error( `The fixture gave no answer for: ${ args.join( ' ' ) }\n${ out }` );
	}
	return JSON.parse( line.slice( 'E2E-JSON:'.length ) );
}

/**
 * POST a signed notification the way Redsys does and read the order back.
 *
 * @param {import('@playwright/test').APIRequestContext} request Request context to post with.
 * @param {string} gatewayId Gateway ID.
 * @param {any}    made      Answer of the fixture's `make`.
 * @return {Promise<any>} The order after the notification.
 */
async function notify( request, gatewayId, made ) {
	const response = await request.post( `/?wc-api=${ GATEWAYS[ gatewayId ] }`, { form: made.form } );
	// Every path of the handler past the signature check answers 200; a
	// rejected signature is a wp_die() 500 and would prove nothing below.
	expect( response.status(), 'the notification passed the signature check' ).toBe( 200 );
	return fixture( 'read', String( made.before.order_id ) );
}

/**
 * Put the test product in the cart of the page's session and confirm it is there.
 *
 * @param {import('@playwright/test').Page} page Page.
 */
async function fillCart( page ) {
	await page.goto( '/?add-to-cart=10' );
	await page.goto( '/cart/' );
	await expect( page.getByText( 'Your cart is currently empty.' ) ).toHaveCount( 0 );
	await expect( page.locator( '.woocommerce-cart-form, .wc-block-cart' ).first() ).toBeVisible();
}

test.describe( 'card gateway (redsys)', () => {
	test( 'AC-09: an authorised notification for the order total stores the Redsys data, adds both notes and completes the payment', async ( { request } ) => {
		const made = fixture( 'make', 'redsys', 'pending', '0000', 'match' );
		const after = await notify( request, 'redsys', made );

		expect( after.meta ).toMatchObject( {
			_payment_order_number_redsys: made.ds_order,
			_payment_date_redsys: '06%2F10%2F2026',
			_payment_hour_redsys: '21%3A15',
			_order_fuc_redsys: '999008881',
			_authorisation_code_redsys: '123456',
			_card_country_redsys: '724',
			_card_type_redsys: 'Credit',
		} );
		expect( after.notes ).toContain( 'HTTP Notification received - payment completed' );
		expect( after.notes ).toContain( 'Authorization code: 123456' );
		expect( after.date_paid, 'payment_complete() ran' ).not.toBeNull();
		// `orderdo` is `processing` in the playground: the order is paid, not completed.
		expect( after.status ).toBe( 'processing' );
	} );

	test( 'AC-09: the order is set to completed only when `orderdo` is `completed`', async ( { request } ) => {
		const { previous } = fixture( 'orderdo', 'redsys', 'completed' );
		try {
			const made = fixture( 'make', 'redsys', 'pending', '0000', 'match' );
			const after = await notify( request, 'redsys', made );
			expect( after.status ).toBe( 'completed' );
			expect( after.notes.join( '\n' ) ).toContain( 'Order Completed by Redsys' );
		} finally {
			fixture( 'orderdo', 'redsys', previous || 'processing' );
		}
	} );

	test( 'AC-10: an authorised notification for another amount puts the order on hold with both amounts and does not complete the payment', async ( { request } ) => {
		const made = fixture( 'make', 'redsys', 'pending', '0000', '999' );
		const after = await notify( request, 'redsys', made );

		expect( after.status ).toBe( 'on-hold' );
		expect( after.notes.join( '\n' ) ).toContain( `Validation error: Order vs. Notification amounts do not match (order: ${ made.match } - received: 999).` );
		expect( after.date_paid ).toBeNull();
		expect( after.meta._authorisation_code_redsys ).toBe( '' );
		expect( after.notes ).not.toContain( 'HTTP Notification received - payment completed' );
	} );

	test( 'AC-11: a refused notification cancels the order, adds a note and empties the cart', async ( { page } ) => {
		await fillCart( page );
		const made = fixture( 'make', 'redsys', 'pending', '0190', 'match' );
		// Posted with the customer's own session: the handler empties the
		// cart of the session the request carries.
		const after = await notify( page.request, 'redsys', made );

		expect( after.status ).toBe( 'cancelled' );
		expect( after.notes ).toContain( 'Order cancelled by Redsys' );
		expect( after.date_paid ).toBeNull();
		await page.goto( '/cart/' );
		await expect( page.getByText( 'Your cart is currently empty.' ) ).toBeVisible();
	} );

	test( 'AC-11: the boundary — response 99 is authorised, response 100 is refused', async ( { request } ) => {
		const authorised = await notify( request, 'redsys', fixture( 'make', 'redsys', 'pending', '0099', 'match' ) );
		expect( authorised.status ).toBe( 'processing' );
		const refused = await notify( request, 'redsys', fixture( 'make', 'redsys', 'pending', '0100', 'match' ) );
		expect( refused.status ).toBe( 'cancelled' );
	} );

	test( 'AC-12: a notification for an order that is already paid changes nothing', async ( { request } ) => {
		// Refused and for another amount: either would change a pending order.
		const made = fixture( 'make', 'redsys', 'processing', '0190', '999' );
		const after = await notify( request, 'redsys', made );
		expect( after ).toEqual( made.before );
	} );
} );

for ( const [ gatewayId, criterion, cancelNote ] of [
	[ 'bizumredsys', 'AC-23', 'Order cancelled by Redsys Bizum' ],
	[ 'googlepayredirecredsys', 'AC-31', 'Order cancelled by Redsys Gpay' ],
] ) {
	test.describe( `${ gatewayId }`, () => {
		test( `${ criterion }: an amount mismatch puts the order on hold`, async ( { request } ) => {
			const made = fixture( 'make', gatewayId, 'pending', '0000', '999' );
			const after = await notify( request, gatewayId, made );

			expect( after.status ).toBe( 'on-hold' );
			expect( after.notes.join( '\n' ) ).toContain( `Validation error: Order vs. Notification amounts do not match (order: ${ made.match } - received: 999).` );
			expect( after.date_paid ).toBeNull();
		} );

		test( `${ criterion }: a refused notification cancels the order, stores the Redsys error text and empties the cart`, async ( { page } ) => {
			await fillCart( page );
			const made = fixture( 'make', gatewayId, 'pending', '0190', 'match' );
			const after = await notify( page.request, gatewayId, made );

			expect( after.status ).toBe( 'cancelled' );
			expect( after.meta._redsys_error_payment_ds_response_value ).toBe( 'Denegación emisor' );
			expect( after.notes ).toContain( 'Order cancelled by Redsys: Denegación emisor' );
			expect( after.notes ).toContain( cancelNote );
			expect( after.date_paid ).toBeNull();
			await page.goto( '/cart/' );
			await expect( page.getByText( 'Your cart is currently empty.' ) ).toBeVisible();
		} );

		if ( 'bizumredsys' === gatewayId ) {
			test( 'AC-23: a notification for an order that is already paid changes nothing', async ( { request } ) => {
				const made = fixture( 'make', gatewayId, 'processing', '0190', '999' );
				const after = await notify( request, gatewayId, made );
				expect( after ).toEqual( made.before );
			} );
		}
	} );
}
