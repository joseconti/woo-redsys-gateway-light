# 03 — Technical Plan

> **Adopted project — reconstructed as-built.** Every row below is marked `[E]` (exists) since this is an adopted, already-shipped codebase; `[A]` marks anything the gap audit says still needs to be built; `[G]` marks a generated artifact.

## Stack & versions
- PHP — `Requires PHP: 7.0` declared in `readme.txt` (not declared in the main plugin file header — a gap, see `docs/04-adoption-audit.md`)
- WordPress — `Tested up to: 7.0`
- WooCommerce — `WC requires at least: 7.4`, `WC tested up to: 10.9`
- Composer — **dev-only**: `composer.json` requires `phpunit/phpunit ^9.6` and `yoast/phpunit-polyfills ^2.0` under `require-dev` (added with the first test suites, D-016/D-018). No runtime PHP dependency is installed or shipped; `vendor/` is gitignored
- Front-end build: `@wordpress/scripts` (`^36.0.0`, webpack 5 inside it), `@woocommerce/dependency-extraction-webpack-plugin` (`^5.1.0`), `cssnano` + `postcss` for the stylesheets (D-055). The Blocks script's JSX is compiled to `createElement()` from `@wordpress/element` so the built script depends on the `wp-element` handle, never on `react-jsx-runtime` (WordPress 6.6+ only).
- Coding standard: `phpcs.xml` — `WooCommerce-Core` + `WordPress-Extra` ruleset, `testVersion 5.6-`, `minimum_supported_wp_version 4.7`

## Code map (as-built)

```
woocommerce-redsys.php                                  [E] bootstrap: init, textdomain load, version-upgrade notice, admin notices
about-redsys.php                                         [E] plugin "about" admin content
index.php                                                 [E] directory-listing protection stub
wpml-config.xml                                           [E] WPML admin-text config (two settings sections)
LICENSE                                                    [E] GPL-2.0-or-later full text (added during adoption, D-003)
classes/
  class-wc-gateway-redsys.php                             [E] main Redsys card gateway (redirection), ~1363 lines
  class-wc-gateway-bizum-redsys.php                       [E] Bizum gateway
  class-wc-gateway-googlepay-redirection-redsys.php       [E] Apple/Google Pay via redirection gateway
  class-wc-gateway-inespay-redsys.php                     [E] Inespay bank-transfer redirection gateway
  class-wc-gateway-redsys-global-lite.php                 [E] shared/base utility class
  class-wc-gateway-redsys-psd2-light.php                  [E] PSD2 helper class
includes/
  class-redsysliteapi.php                                 [E] RedsysLiteAPI — signature creation/verification (HMAC_SHA256_V1)
  class-redsys-lite-apps-plugins.php                      [E] admin upsell/cross-sell widget
  data/                                                    [E] static reference tables (currencies, countries, error codes, status maps)
  blocks/                                                  [E] one *-support.php per gateway for WooCommerce Blocks checkout
assets/
  css/redsys-css.css, redsys-notice.css, welcome.css       [E] hand-written SOURCE, the only CSS files edited
  css/*.min.css                                             [G] minified pair of each stylesheet — `npm run build:css` (bin/build-assets.js); loaded unless SCRIPT_DEBUG
  images/                                                   [E] payment logos (bizum.png, GPay.svg, GPay-peque.svg, inespay.svg, ...)
  js/frontend/blocks.js + blocks.asset.php                 [G] webpack build output, readable — served under SCRIPT_DEBUG; regenerated from resources/js/frontend/index.js via `npm run build:assets`
  js/frontend/blocks.min.js + blocks.min.asset.php         [G] webpack build output, minified — what production loads; same source, same command
resources/js/frontend/index.js                             [E] webpack SOURCE for the Blocks checkout bundle
languages/                                                  [E] es_ES .po/.mo/.l10n.php/.json; .pot generated via `npm run i18n:pot`
bin/build_i18n.sh                                           [E] i18n JSON-build helper invoked from package.json
bin/build-assets.js                                         [E] CSS minifier (postcss + cssnano); `--check` reports a stale or missing minified file
docs/                                                        [E] Keel state + reconstructed docs (this adoption)
.reference/inespay-payment/                                  [E] vendored third-party reference plugin, gitignored (relocated from docs/, D-006)
.wp-env-mu-plugins/                                           [E] dev/test-only wp-env mu-plugin(s); mapped via .wp-env.json's `mappings`, never shipped (D-029)
tests/
  bootstrap.php, bootstrap-integration.php                     [E] PHPUnit bootstraps (unit: stubs wp_json_encode; integration: boots WordPress + WooCommerce + this plugin)
  Unit/, Integration/, e2e/                                    [E] PHPUnit unit suite, PHPUnit integration suite, Playwright specs
phpunit.xml.dist, phpunit-integration.xml.dist, playwright.config.js   [E] test runner configuration
scripts/
  keel-doctor                                                  [E] environment doctor, compiled from "Environment requirements" below
  keel-affected-tests                                          [E] test selection, compiled from the "Test selection" line below
  keel-verify                                                  [E] mechanical docs-vs-reality checks
.githooks/pre-push                                             [E] runs scripts/keel-affected-tests --run on every pushed branch (active once core.hooksPath = .githooks)
```

**Source plus minified pairs (D-054, S-027)** — every shipped stylesheet and script exists as a readable file and a minified file built from it. The readable CSS is the source; for the Blocks script the source is `resources/js/frontend/index.js` and both `blocks.js` and `blocks.min.js` are build output. Production loads the minified file; `redsyslite_asset_suffix()` switches to the readable one when `SCRIPT_DEBUG` is on.

## Change map

The change map — recurring change types and every artifact each one must touch — lives in [`02-functional-spec.md`](02-functional-spec.md), section "Change map (recurring change types → what must be touched)". Read its row before changing anything.

## Observed conventions
- Class file naming: `class-wc-gateway-<name>-redsys.php`, kebab-case, `class-` prefix (WordPress convention).
- Gateway IDs: lowercase, no separators (`redsys`, `bizumredsys`, `googlepayredirecredsys`, `inespayredsys`).
- Hook naming: `<action>_<gateway_id>_<event>` for filters (e.g. `woocommerce_redsys_icon`), `valid_<gateway_id>_standard_ipn_request` for the shared notification-received action.
- i18n: `__()`/`_e()`/`esc_html__()`/`esc_html_e()` consistently used with the `woo-redsys-gateway-light` text domain in the live plugin code (sampled, no hardcoded strings found in the main gateway class).
- Docblocks present at class/property level (`@since`, property docblocks); function-level docblocks less consistent — a documentation gap, not a blocking one.
- Directory-protection stub files (`index.php` at various levels) use non-standard ASCII-art comments instead of the one-line `// Silence is golden.` convention — cosmetic, harmless, left as-is (adoption doesn't impose style changes).

## Testing (real, verified commands)
PHPUnit 9.6 unit tests exist for `RedsysLiteAPI` (`tests/Unit/RedsysLiteAPITest.php`), the plugin's HMAC-SHA256 signature creation/verification class — the highest-risk code path per `docs/threat-model.md`. No `jest.config.*` or JS test suite exists — a deliberate decision (D-031), not a gap: see the "Remaining gap" paragraph below.

- **Scope decision:** these are true unit tests against `RedsysLiteAPI` in isolation, not `WP_UnitTestCase` integration tests against a booted WordPress. The class has no WordPress runtime dependency beyond `wp_json_encode()`, which `tests/bootstrap.php` stubs — booting full WP core for this class would add engineering cost with no coverage benefit. See D-016 in `docs/decisions.md`.
- **Where it runs:** inside the `wp-env` `cli` Docker container (PHP 7.4, matches `.wp-env.json`), which already has Composer 2.10 and downloads PHPUnit 9.6.35 project-locally via `composer.json`. The host is not the test runtime: when these suites were written it had no PHP at all, and the PHP it has today (8.5.11 via Homebrew, `scripts/keel-doctor` 2026-10-06) is not the 7.4 the plugin is tested against — it is used for `php -l` only.
- **Verified commands** (from the repo root, `wp-env` running):
  ```
  npx wp-env run cli bash -c "cd wp-content/plugins/woo-redsys-gateway-light && composer install"
  npx wp-env run cli bash -c "cd wp-content/plugins/woo-redsys-gateway-light && vendor/bin/phpunit"
  ```
  Real run, 2026-08-01: `OK (8 tests, 8 assertions)`. Mutation-tested for real (not just self-consistency): temporarily broke `mac256()`'s call site in `create_merchant_signature_notif()`/`_soap_request()`/`_soap_response()` → 3 tests failed as expected; reverted (`diff` confirmed byte-identical to the original) → all 8 green again.
- **Coverage:** signature matches an independent reference implementation of Redsys's documented algorithm (built from `openssl`/`hash_hmac` directly in the test, not by calling the class under test); a tampered notification payload produces a different signature; a different merchant secret produces a different signature; `sanitize_merchant_parameters()` restores space→`+` and strips out-of-alphabet/null-byte characters.
A second suite, `tests/Integration/`, covers `WC_Gateway_redsys::check_ipn_request_is_valid()` — the fail-closed gate in front of every payment notification — against a real, booted WordPress + WooCommerce.

- **Scope decision:** this class extends `WC_Payment_Gateway` and genuinely needs WordPress/WooCommerce loaded, so it runs as a `WP_UnitTestCase` integration suite, separate from the fast unit suite above. It reuses the WordPress core PHPUnit test scaffold that `wp-env` already provisions inside the `tests-cli` container at `WP_TESTS_DIR=/wordpress-phpunit` — no separate install step was needed. `yoast/phpunit-polyfills` was added as a dev dependency, required by that scaffold. See D-018 in `docs/decisions.md`.
- **Verified commands** (from the repo root, `wp-env` running):
  ```
  npx wp-env run cli bash -c "cd wp-content/plugins/woo-redsys-gateway-light && composer update -W"
  npx wp-env run tests-cli bash -c "cd wp-content/plugins/woo-redsys-gateway-light && vendor/bin/phpunit -c phpunit-integration.xml.dist"
  ```
  Real run, 2026-08-01: `OK (5 tests, 5 assertions)`. Mutation-tested for real: temporarily replaced the `$localsecret === $remote_sign` comparison with `true` (always-accept) → 3 tests failed as expected; reverted (`diff` confirmed byte-identical to the original) → all 5 green again.
- **Coverage:** rejects when no SHA-256 secret is configured (fail-closed, matches the manual playground evidence in `docs/05-test-points.md`'s "Adoption — playground verification" row); accepts a correctly-signed notification; rejects a forged signature; rejects a payload tampered with after signing (genuine signature, edited amount); rejects a signature that was valid for a different order (the key-diversification-by-order-number check).
Two further integration test classes were added the same session: `tests/Integration/GatewayBizumIpnTest.php` (`WC_Gateway_Bizum_Redsys`, needs a real `WC_Order` fixture unlike Redsys — D-019) and `tests/Integration/GatewayGooglePayIpnTest.php` (`WC_Gateway_GooglePay_Redirection_Redsys` — D-020, whose test suite also caught and fixed a real signature-bypass vulnerability, see `docs/threat-model.md`). A fifth class, `tests/Integration/GatewayInespayIpnTest.php`, covers `WC_Gateway_Inespay_Redsys::handle_callback()` — a genuinely different signature algorithm (plain `hash_hmac('sha256', dataReturn, api_key, false)` then base64 of the hex string, not `RedsysLiteAPI`) and a different method shape (`wp_die()` on every path, caught as `WPDieException`) — see D-021.

A checkout-flow smoke test was added the same session: `tests/e2e/checkout-redsys.spec.js` (Playwright/`@playwright/test`, config at `playwright.config.js`), driving a real guest checkout against the wp-env playground — product → checkout → order → the generated Redsys payment form — with every request to `*.redsys.es` intercepted and aborted, so it never depends on Redsys's live infrastructure. See D-022 in `docs/decisions.md` and `docs/playground.md`'s "Automated checkout smoke test" section (including the one-time environment setup it needs — pretty permalinks + a configured Redsys gateway, neither present in the playground by default).

All four gateways' checkout flows now have e2e coverage: `tests/e2e/checkout-bizum.spec.js` (D-027) and `tests/e2e/checkout-googlepay.spec.js` (D-027) mirror `checkout-redsys.spec.js`'s classic-checkout shape; `tests/e2e/checkout-inespay.spec.js` (D-029) needed a materially different technique, since Inespay's `process_payment()` makes a real server-side API call rather than redirecting to a self-submitting form — `.wp-env-mu-plugins/inespay-http-stub.php` (dev/test-only, mapped via `.wp-env.json`'s `mappings` key) fakes that call's response, gated behind an option that's off by default. `tests/e2e/checkout-blocks-redsys.spec.js` (D-028) covers the WooCommerce Blocks checkout for the first time (Redsys, representative of the shared `AbstractPaymentMethodType` integration pattern all four gateways use). `tests/e2e/inespay-transaction-limit.spec.js` (D-030) covers `disable_inespay()`'s fractional-total float comparison, previously code-review-verified only.

- **Remaining gap:** none of the four testability gaps recorded after the previous sprint remain open. No JS unit-test suite (Jest) was added for `resources/js/frontend/index.js` — a deliberate decision (D-031), not an oversight: the file is pure declarative `registerPaymentMethod()` config with no independent logic, and `checkout-blocks-redsys.spec.js` already exercises its real compiled output end to end.
- **Full real-run command** (all suites, from the repo root, `wp-env` running):
  ```
  npx wp-env run cli bash -c "cd wp-content/plugins/woo-redsys-gateway-light && vendor/bin/phpunit"
  npx wp-env run tests-cli bash -c "cd wp-content/plugins/woo-redsys-gateway-light && vendor/bin/phpunit -c phpunit-integration.xml.dist"
  npx playwright test
  ```
  Real run, 2026-08-01: unit `OK (8 tests, 8 assertions)`; integration `OK (31 tests, 295 assertions)`; e2e `7 passed` (`checkout-redsys`, `checkout-bizum`, `checkout-googlepay`, `checkout-inespay`, `checkout-blocks-redsys`, `inespay-transaction-limit` ×2) — 46 automated tests total, all mutation/regression-verified individually (see `docs/05-test-points.md`).

### Driver per surface

| Surface | Driver | Headless? | Evidence it produces |
|---|---|---|---|
| Signature logic (`RedsysLiteAPI`) | PHPUnit 9.6, unit suite, in the wp-env `cli` container | yes — no UI | PHPUnit output (`OK (N tests, M assertions)`), exit code |
| Notification / callback endpoints (`?wc-api=WC_Gateway_<id>`), refunds, order-number logic | PHPUnit 9.6 integration suite (`WP_UnitTestCase`) in the wp-env `tests-cli` container; `curl` against the playground for a raw POST | yes — no UI | PHPUnit output; HTTP status + body; `wp-content/debug.log` |
| Storefront checkout — classic and Blocks (the four gateways' payment options and generated payment forms) | Playwright (`@playwright/test`, Chromium) against the wp-env playground on `http://localhost:8888` | yes — Playwright's default; nothing takes the screen | list reporter output; a trace for a failed test (`test-results/`) |
| WooCommerce admin settings screens (per gateway) | Playwright against the same playground — **`TO BUILD`**: no spec drives the admin screens today | yes | — |

No surface of this project needs a non-headless driver, so there is no screen-stealing mitigation to agree.

### Run mode and recording
- **Headless is the default and the only documented mode:** `npx playwright test` (`npm run test:e2e`). A headed, slowed-down script for watching a run is **`TO BUILD`** — none exists in `package.json`; the ad-hoc equivalent is `npx playwright test --headed`, which leaves no recording behind and is never evidence.
- **Recording:** `trace: 'retain-on-failure'` (`playwright.config.js`); artifacts land in `test-results/` (gitignored). Video is **not** enabled — recording a video per test is `TO BUILD`; today a green run's evidence is the reporter output, a red run's is its trace.
- **Worker cap:** `workers: Number( process.env.PW_WORKERS ?? 1 )`. The cap is **1** locally and everywhere, because every spec drives the same wp-env site (one set of gateway options, one storefront); `PW_WORKERS=<n>` raises it for a run of specs known not to share state. Keel's reference expression leaves the count `undefined` under `CI`; this project deliberately does not, for that shared-site reason — and it has no forge CI (card: `CI runs on: n/a`).
- **Browser MCP:** none is registered for this project (no `.mcp.json`); the browser is driven by the test runner only. If one is ever added it goes in the repo-level `.mcp.json` with `--headless --isolated` (or `--cdp-endpoint` to one shared browser) and this line records which. `scripts/keel-doctor` checks the three advisory rows (user-level registration, flags, browsers orphaned to PID 1) on every run.
- Several sessions on one machine take turns running the browser suite — one executing verifier per environment; the playground is one environment.

### Element addressability
The checkout markup this plugin's tests bind to is rendered by WooCommerce, not by the plugin: specs address the stable element IDs WooCommerce derives from the gateway ID (`#payment_method_redsys`, `#payment_method_bizumredsys`, `#payment_method_googlepayredirecredsys`, `#payment_method_inespayredsys`) and the `name` attributes of the generated Redsys form fields (`Ds_MerchantParameters`, `Ds_Signature`, `Ds_SignatureVersion`) — identifiers, never localized visible text. The plugin adds no `data-testid` of its own today. Convention for any interactive element the plugin itself renders from now on: `data-testid="redsys-lite-<screen>-<element>"`, kebab-case. No accessibility label is ever invented to make an element findable.

### Division of labour
The assistant drives every suite above end to end; every row of `docs/05-test-points.md` is `driven`. The legs it cannot drive, each with the tag a slice takes when it needs that leg — the tag covers only the leg, never the whole flow:

| Leg | Tag | Who runs it, how |
|---|---|---|
| A payment completed on Redsys's own hosted page (the e2e specs stop at the generated, signed form and abort every request to `*.redsys.es`, D-022) with a merchant's real terminal and secret | `CREDENTIAL` | the merchant/user, with their own Redsys test-environment credentials, following `docs/playground.md` "Try it yourself" |
| A Bizum payment confirmed on a phone | `HARDWARE` | the user, on a device with a Bizum-enabled banking app |
| An Inespay transfer authorised at a real bank (the playground fakes Inespay's API response, D-029) | `CREDENTIAL` | the user, with a real Inespay API key and bank login |
| A real charge against a live merchant account | `PRODUCTION-RISK` | never automated; the user, deliberately |
| The screen-reader pass on the checkout and settings screens (`docs/accessibility.md`) | `ASSISTIVE-TECH` | a person with VoiceOver/NVDA |
| Publishing to WordPress.org (SVN commit, plugin review) | `EXTERNAL-APPROVAL` | the user, with their WordPress.org account |

### Static analysis and sniffers
Run at every test point on the changed files, and over the whole tree at the release gate:

- `php -l <file>` on every touched PHP file — on the host (`/opt/homebrew/bin/php`), or `npx wp-env run cli php -l wp-content/plugins/woo-redsys-gateway-light/<file>` for the PHP 7.4 the plugin targets. **Available today.**
- PHP_CodeSniffer with `phpcs.xml` (`WooCommerce-Core` + `WordPress-Extra`) — **`TO BUILD`**: the ruleset is committed, but `phpcs` and the WooCommerce/WordPress standards are not a `require-dev` dependency and are not installed anywhere this project can call (`vendor/bin/phpcs` is absent). No phpcs run is claimed until that exists.
- PHPStan — **absent**: no `phpstan.neon`, not installed.
- WordPress Plugin Check — **absent**: not installed in the playground.
- JavaScript: `@wordpress/scripts` ships ESLint (`npx wp-scripts lint-js resources/js`), but no lint script is defined in `package.json` and no run has been recorded — **`TO BUILD`**.
- `scripts/keel-verify` — cheap, runs whole, always.

### Accessibility automation
**Built (S-029, D-067)** — `tests/e2e/accessibility.spec.js` injects `axe-core` (a development dependency; `@axe-core/playwright` is not used) into the page through Playwright and scans, per screen and per state, only the region this plugin renders: each gateway's row on the classic checkout with that gateway selected, the generated payment form of the three Redsys gateways, the payment options of the Blocks checkout with each selected in turn, the four settings forms, the plugin's admin notices and its About page. Rules tagged WCAG 2.0, 2.1 and 2.2, levels A and AA (D-007). Raw results go to `test-results/a11y/`. One recorded exception is listed in the spec by rule and selector (S-057). **`TO BUILD`**: a driven keyboard and focus-order pass; the states the spec does not reach are listed in `docs/accessibility.md`, which also holds the results and the script of the guided pass.

### Read-back duty
- **WordPress log — in place:** `.wp-env.json` sets `WP_DEBUG` and `WP_DEBUG_LOG` for both environments; the log is read at every test point with `npx wp-env run cli tail -n 100 wp-content/debug.log` (`docs/playground.md`, "Reading the WordPress debug log"). A flow that passes while the log gained a fatal or a notice has not passed.
- **Browser — `TO BUILD`:** the specs do not yet subscribe to `console`, `pageerror`, `requestfailed` or 5xx `response` events, so a page that renders correctly while throwing does not fail its test today.
- **Gateway log:** the plugin's own `WC_Logger` output (WooCommerce → Status → Logs), switched by each gateway's debug setting.

### Test selection (the source of `scripts/keel-affected-tests`)
Every test point, every push (`.githooks/pre-push`) and every sprint close runs the affected selection; the entire suite runs only at the Phase 7 gate on the release candidate (`scripts/keel-affected-tests --full --run`, recorded as `scope: full — M of M tests`). Card: `Push test scope: affected`.

- **Impact tool:** none exists for PHPUnit, so the path tables written into `scripts/keel-affected-tests` plus a reverse-dependency grep ARE the tool:
  - *source → tests* — the script's `DIRECT` table (e.g. `includes/class-redsysliteapi.php` → `tests/Unit/RedsysLiteAPITest.php` and the three IPN tests that sign their fixtures with it; `classes/class-wc-gateway-<name>-redsys.php` → `tests/Integration/Gateway<Name>IpnTest.php` + `tests/e2e/checkout-<name>.spec.js`), plus, at run time, a grep of `tests/` for every class (or, in a classless file, function) the changed file declares — which is what selects a new test before its row is added;
  - *reverse dependencies* — the script's `DEPENDENTS` table, by symbol use (`new RedsysLiteAPI`, `WCRedL()`, `WCPSD2L()`, `include_once REDSYS_PLUGIN_DATA_PATH`), followed transitively: a change to `classes/class-wc-gateway-redsys-global-lite.php` reaches every gateway's tests. `woocommerce-redsys.php`'s `require_once` list is deliberately not an edge (it loads everything; counting it would select everything on every change);
  - every test file added or modified in the diff.
- **Always-run smoke set:** none.
- **Widening list, this project's concrete paths → the entire suite:** `composer.json`, `composer.lock`, `package.json`, `package-lock.json` (manifests and lockfiles); `phpunit.xml.dist`, `phpunit-integration.xml.dist`, `playwright.config.js` (runner configuration); `tests/bootstrap.php`, `tests/bootstrap-integration.php` (bootstraps); any other non-test file under `tests/` (shared fixture or helper — none exists yet); `webpack.config.js`, `bin/*`, `phpcs.xml` (build configuration); `.wp-env.json`, `.wp-env.override.json`, `.wp-env-mu-plugins/*` (playground configuration); `.github/workflows/*` (CI — none exists); `scripts/keel-affected-tests` itself.
- **Database schema / migrations:** none — the plugin creates no table and ships no migration, so that widening row has nothing to match.
- **Uncovered source:** a source file with no test and no tested dependent gets the tests of its enclosing module (the script's `MODULES` table — e.g. the Bizum/Google Pay/Inespay Blocks support classes → that gateway's tests; anything else → the plugin-bootstrap pair) and is printed as a coverage gap, never passed over.
- **Docs only:** a diff touching only `docs/`, `*.md`, `readme.txt`, `LICENSE`, `scripts/keel-*` (other than the selector), `.githooks/`, `.claude/`, `.agents/`, `.codex/` or repository metadata selects nothing — `scope: none — docs only`.
- **Failure modes:** a source diff with an empty selection exits non-zero; a stale table or an unresolvable base falls back to the entire suite and says why; `--run` with the playground down exits non-zero naming `npx wp-env start` — it never passes silently.
- **Keeping it true:** adding a source or test file means adding its row; `scripts/keel-affected-tests --check-map` fails when a test file is in no row or a row names a path that does not exist.
- **Test counts in the `scope:` line are static** (PHPUnit test methods + Playwright `test()` calls); data-provider rows are not expanded, so PHPUnit's own total can be higher.

### Regression rule
Every bug fixed gets a test pinning the fix, **written and failing before the fix** (linked from `docs/lessons-learned.md`) — on every policy value below, hotfixes included.

### Test-first policy: pure-logic
Card value `pure-logic` (D-036). Pure logic gets its test written and **seen failing** before its code: here that is signature creation/verification (`RedsysLiteAPI`, the Inespay HMAC), order-number preparation and its transients, amount formatting, and response/status-code mapping. Not applied to gateway settings markup, hook registration and bootstrap glue, or exploratory work against Redsys/Inespay behaviour that is not yet known (a spike, closed by a test once the shape is known). No acceptance-level scope is in force. A test derived from an `AC-nn` or a reproduced bug is never edited to make it pass. The policy is not retroactive: rows already in `docs/05-test-points.md` carry `Red first: n/a — predates`.

## Environment requirements (the source of `scripts/keel-doctor`)

One machine plays all three roles here — the user's Mac holds the repository and runs the tests. PHP, Composer, MySQL, WordPress and WooCommerce are **not** host requirements: they run inside the wp-env containers.

| Requirement | Required version/state | Severity | How it is installed on macOS / Windows / Linux |
|---|---|---|---|
| Node.js | `^22.22.2 \|\| ^24.15.0 \|\| >=26.0.0` (what `@wordpress/scripts` 36 declares; `package.json` `engines`) | blocking | `nvm install --lts` or `mise use node@lts` (macOS/Linux); `fnm`/`volta` (Windows). Never a silent global version change |
| npm, npx | any (ship with Node.js) | blocking | with Node.js |
| Docker CLI | any | blocking | Docker Desktop (macOS/Windows) or Docker Engine (Linux). **Licence:** Docker Desktop needs a paid subscription for organisations with 250+ employees or over $10M revenue — Colima (MIT) on macOS/Linux is the drop-in alternative. **Privilege:** adding a user to the `docker` group on Linux is effectively granting root |
| Docker daemon | running — "installed but stopped" is `NOT OPERATIONAL`, not `MISSING` | blocking | start Docker Desktop, `colima start`, or `systemctl start docker` |
| `@wordpress/env` (wp-env) | `^11.16.0` (D-051), project-local | optional (npx fetches it on first run) | `npm install` |
| wp-env playground of THIS repository | running, for any test run — a state, not an install; another project's wp-env is not it | optional | `npx wp-env start` |
| PHPUnit + `yoast/phpunit-polyfills` | `^9.6` / `^2.0`, in `vendor/` | blocking | with the playground running: `npx wp-env run cli bash -c "cd wp-content/plugins/woo-redsys-gateway-light && composer install"` |
| `@playwright/test` | `^1.62.1`, project-local | blocking | `npm install` |
| `axe-core` | `^4.14`, project-local | blocking | `npm install` |
| Playwright Chromium | the revision the installed `@playwright/test` pins | blocking | `npx playwright install chromium` — a download of a few hundred MB |
| PHP on the host | >= 7.4 recommended | optional — only for `php -l` outside Docker | Homebrew / the OS package manager |
| python3 | any | optional — the doctor reads the MCP and settings JSON with it | Xcode Command Line Tools / the OS package manager |
| Permission mode | not `manual` | advisory | `.claude/settings.local.json` (`permissions.defaultMode: "auto"`), or `claude --permission-mode auto` |
| Browser MCP scope | no user-level registration | advisory | `claude mcp remove -s user playwright`; register in the project's `.mcp.json` |
| Browser MCP flags | `--headless` + `--isolated`, or `--cdp-endpoint` | advisory | add the flags in `.mcp.json` |
| Orphaned Playwright browsers | 0 with parent PID 1 | advisory | `kill <the listed PIDs>` — never `pkill -f ms-playwright` |

**Not in the table because they do not exist yet** (see "Static analysis and sniffers"): phpcs with the WooCommerce/WordPress standards, PHPStan, Plugin Check. Each gets a row here — and so a row in the doctor — in the slice that installs it.

**Nothing in this project is impossible on this machine:** no Apple, Android or native-desktop surface exists.

**Not probed by the doctor:** the notification channel (card `Notify:`). A shell script cannot probe the assistant's own notification tool, so the session records that probe; the doctor does not claim it.

## Build/lint commands (verified from `package.json`)
- `npm run build:assets` — `wp-scripts build` (compiles `resources/js/frontend/index.js` → `assets/js/frontend/blocks.js`, `blocks.min.js` and their `.asset.php` files), then `npm run build:css` (`node bin/build-assets.js`: every `assets/css/<name>.css` → `<name>.min.css`).
- `npm run check:css` — exits 1 when a minified stylesheet is missing or stale; `scripts/keel-verify` check 11 runs it and also rebuilds the script into a temporary directory to compare.
- `npm run build` — `npm run build:assets`, then `npm run i18n:build`
- `npm run start` — `wp-scripts start` (watch mode)
- `npm run i18n:pot` — generates the `.pot` via `wp i18n make-pot` (WP-CLI, not verified runnable in this environment — requires WP-CLI + a WordPress install)
- `npm run test:e2e` (`playwright test`) — runs `tests/e2e/`; needs `npx wp-env start` and the one-time environment setup in `docs/playground.md`
- `phpcs.xml` — `WooCommerce-Core` + `WordPress-Extra` ruleset; never run for this project: no `phpcs` binary with those standards is installed or declared as a dependency (`TO BUILD`, see "Static analysis and sniffers")
- `scripts/keel-affected-tests [--base <ref>] [--head <ref>] [--run] [--full]` — prints (and with `--run`, runs) the tests the diff reaches; `scripts/keel-doctor [--check|--plan|--fix]` — environment table

## Version touchpoints (verified, and their current agreement)
| Location | Value | Agrees with Stable tag? |
|---|---|---|
| `woocommerce-redsys.php` header `Version:` | 7.0.2 | yes |
| `woocommerce-redsys.php` `REDSYS_WOOCOMMERCE_VERSION` constant | 7.0.2 | yes |
| `readme.txt` `Stable tag:` | 7.0.2 | — (reference) |
| `readme.txt` changelog top entry | `== 7.0.2 ==` | yes |
| `package.json` `version` | 4.0.0 | **no — drifted, recorded as a deferred item in PROGRESS.md** |

## License compatibility
GPL-2.0-or-later (D-003). No Composer/npm runtime dependencies are bundled into the shipped plugin (`package.json` deps are dev-only build tooling); no license-compatibility conflict identified.

## Front-end asset build contract
Keel's "source first, minified for production" contract (`SKILL.md`) is applied since S-027 (D-054): edit the source, run `npm run build:assets` locally before committing, commit source and output together. No CI or forge action builds them. `scripts/keel-verify` check 11 fails on a missing pair, a stale minified stylesheet, or built script files that differ from a fresh build. The minifier (`cssnano`, `postcss`) is a declared dev dependency (D-055).
