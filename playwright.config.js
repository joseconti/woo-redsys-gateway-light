// @ts-check
const { defineConfig, devices } = require( '@playwright/test' );

/**
 * Checkout smoke test against the wp-env playground (docs/playground.md).
 * Requires `npx wp-env start` to be running first — this config does not
 * start it itself, since these tests share the same environment the
 * PHPUnit integration suite and manual playground use.
 */
module.exports = defineConfig( {
	testDir: './tests/e2e',
	fullyParallel: false,
	forbidOnly: !! process.env.CI,
	retries: 0,
	// Local worker cap (Keel test-automation.md, "A browser MCP costs one
	// browser per session"): every worker is a whole browser. The default
	// stays 1 because every spec drives the SAME wp-env site (one cart
	// session, one set of gateway options) — raise it with PW_WORKERS only
	// for specs known not to share state. Deliberately not `undefined` under
	// CI: a default worker count would run the specs in parallel against
	// that one shared site.
	workers: Number( process.env.PW_WORKERS ?? 1 ),
	reporter: 'list',
	use: {
		baseURL: 'http://localhost:8888',
		trace: 'retain-on-failure',
	},
	projects: [
		{
			name: 'chromium',
			use: { ...devices[ 'Desktop Chrome' ] },
		},
	],
} );
