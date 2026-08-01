// @ts-check
const { test, expect } = require( '@playwright/test' );

/**
 * Covers disable_inespay()'s transaction-limit check
 * (classes/class-wc-gateway-inespay-redsys.php:199-213), fixed in D-026 to
 * compare the cart total and the configured limit as floats instead of
 * truncating both to (int) — a pre-fix `(int)` cast let a cart total like
 * 200.50 incorrectly pass a 200 limit. D-026 named "a Playwright checkout
 * test for a fractional-total cart" as this fix's own review trigger; this
 * is that test.
 *
 * This playground's Inespay gateway (docs/playground.md) is configured with
 * transactionlimit: "200". The "E2E Transaction Limit Product" created for
 * this test (docs/playground.md) is priced at 200.50 — exactly the pre-fix
 * bug's failure case. Only the gateway list needs to be checked; no payment
 * needs to complete. Added to the cart via its product page (not a
 * hardcoded post ID + "?add-to-cart="), since a fresh `wp-env clean all`
 * install won't necessarily reassign the same numeric ID this product got
 * when it was first created, but its slug is deterministic from its name.
 */

test( 'Inespay is hidden when the cart total exceeds the transaction limit (fractional total)', async ( { page } ) => {
	await page.goto( '/product/e2e-transaction-limit-product/' );
	await page.getByRole( 'button', { name: 'Add to cart' } ).click();

	await page.goto( '/checkout/' );

	// Selecting the country triggers WooCommerce's checkout-fragment AJAX
	// refresh (wc-ajax=update_order_review), which is what actually
	// re-evaluates disable_inespay() and the is_allowed_country() gate.
	// Wait for that specific response before asserting on the gateway list —
	// otherwise the assertion below could trivially pass before the AJAX
	// call has even started (Inespay isn't available at initial page load
	// either, since this playground's store base location is US).
	const [ response ] = await Promise.all( [
		page.waitForResponse( ( r ) => r.url().includes( 'wc-ajax=update_order_review' ) && r.status() === 200 ),
		page.selectOption( '#billing_country', 'ES' ),
	] );
	expect( response.ok() ).toBeTruthy();

	await expect( page.locator( '#payment_method_inespayredsys' ) ).toHaveCount( 0 );
} );

test( 'Inespay is still offered when the cart total is under the transaction limit', async ( { page } ) => {
	await page.goto( '/?add-to-cart=10' );

	await page.goto( '/checkout/' );

	const [ response ] = await Promise.all( [
		page.waitForResponse( ( r ) => r.url().includes( 'wc-ajax=update_order_review' ) && r.status() === 200 ),
		page.selectOption( '#billing_country', 'ES' ),
	] );
	expect( response.ok() ).toBeTruthy();

	const inespayRadio = page.locator( '#payment_method_inespayredsys' );
	await expect( inespayRadio ).toBeAttached( { timeout: 15000 } );
} );
