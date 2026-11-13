// @ts-check
const { defineConfig, devices } = require( '@playwright/test' );
const { execFileSync } = require( 'child_process' );

// The site under test: PLAYGROUND_URL, or the address `scripts/keel-site url`
// gives for KEEL_SITE (wrgl = floor, the default; wrgl-ceiling).
function playgroundUrl() {
	if ( process.env.PLAYGROUND_URL ) {
		return process.env.PLAYGROUND_URL;
	}
	const site = process.env.KEEL_SITE ? [ '--site', process.env.KEEL_SITE ] : [];
	return execFileSync( require( 'path' ).join( __dirname, 'scripts/keel-site' ), [ ...site, 'url' ] ).toString().trim();
}

/**
 * Browser tests against the playground site on the shared VM (docs/playground.md,
 * D-114). The site must exist (scripts/keel-site create) — this config does not
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
	// stays 1 because every spec drives the SAME playground site (one cart
	// session, one set of gateway options) — raise it with PW_WORKERS only
	// for specs known not to share state. Deliberately not `undefined` under
	// CI: a default worker count would run the specs in parallel against
	// that one shared site.
	workers: Number( process.env.PW_WORKERS ?? 1 ),
	reporter: 'list',
	use: {
		baseURL: playgroundUrl(),
		trace: 'retain-on-failure',
	},
	projects: [
		{
			name: 'chromium',
			use: { ...devices[ 'Desktop Chrome' ] },
		},
	],
} );
