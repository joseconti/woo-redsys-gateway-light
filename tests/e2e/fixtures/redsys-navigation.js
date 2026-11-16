// @ts-check

/**
 * Keeps the order-pay page in place while recording that it submitted itself.
 *
 * The order-pay page submits #redsys_payment_form on its own (S-107). An init
 * script cancels that submit in the browser and records it in
 * window.__redsysAutoSubmitted, so the spec can read the form and then assert
 * the page really sent the customer to Redsys. As a second net, every request
 * to *.redsys.es is aborted; a regular expression is used because a
 * "redsys.es/" glob misses a URL with a port (sis-t.redsys.es:25443).
 *
 * @param {import('@playwright/test').Page} page Page under test.
 * @return {Promise<{ submitted: () => Promise<unknown> }>}
 */
async function holdRedsysNavigation( page ) {
	await page.addInitScript( () => {
		document.addEventListener(
			'submit',
			( event ) => {
				const form = /** @type {HTMLElement} */ ( event.target );
				if ( form && 'redsys_payment_form' === form.id ) {
					event.preventDefault();
					// @ts-ignore
					window.__redsysAutoSubmitted = true;
				}
			},
			true
		);
	} );
	await page.route( /^https?:\/\/([^/]+\.)?redsys\.es(:\d+)?\//, ( route ) => route.abort() );
	return {
		// @ts-ignore
		submitted: () => page.waitForFunction( () => true === window.__redsysAutoSubmitted ),
	};
}

module.exports = { holdRedsysNavigation };
