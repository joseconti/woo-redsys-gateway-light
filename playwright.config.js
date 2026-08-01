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
	workers: 1,
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
