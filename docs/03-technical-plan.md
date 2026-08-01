# 03 — Technical Plan

> **Adopted project — reconstructed as-built.** Every row below is marked `[E]` (exists) since this is an adopted, already-shipped codebase; `[A]` marks anything the gap audit says still needs to be built; `[G]` marks a generated artifact.

## Stack & versions
- PHP — `Requires PHP: 7.0` declared in `readme.txt` (not declared in the main plugin file header — a gap, see `docs/04-adoption-audit.md`)
- WordPress — `Tested up to: 7.0`
- WooCommerce — `WC requires at least: 7.4`, `WC tested up to: 10.9`
- No Composer / PHP dependency manager
- Front-end build: `@wordpress/scripts` (`^30.20.0`) + webpack (`webpack-cli ^4.10.0`), `@woocommerce/dependency-extraction-webpack-plugin ^1.7.0`, `cross-env`
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
  css/redsys-css.css, redsys-notice.css, welcome.css       [E] hand-written, unminified — no minified pairs exist [gap, see audit]
  images/                                                   [E] payment logos (bizum.png, GPay.svg, GPay-peque.svg, inespay.svg, ...)
  js/frontend/blocks.js + blocks.asset.php                 [G] webpack build output — regenerated from resources/js/frontend/index.js via `npm run build`
resources/js/frontend/index.js                             [E] webpack SOURCE for the Blocks checkout bundle
languages/                                                  [E] es_ES .po/.mo/.l10n.php/.json; .pot generated via `npm run i18n:pot`
bin/build_i18n.sh                                           [E] i18n JSON-build helper invoked from package.json
docs/                                                        [E] Keel state + reconstructed docs (this adoption)
.reference/inespay-payment/                                  [E] vendored third-party reference plugin, gitignored (relocated from docs/, D-006)
```

**Not shipped as source-controlled minified pairs** — `assets/js/frontend/blocks.js` is a build OUTPUT (correctly `[G]`), but the CSS files have no `*.min.css` counterpart at all: the project has never adopted Keel's "source first, minified for production" contract. This is a real gap, recorded in `docs/04-adoption-audit.md` and NOT silently fixed during adoption (adoption changes no code beyond the user-approved license reconciliation, D-003/D-006).

## Observed conventions
- Class file naming: `class-wc-gateway-<name>-redsys.php`, kebab-case, `class-` prefix (WordPress convention).
- Gateway IDs: lowercase, no separators (`redsys`, `bizumredsys`, `googlepayredirecredsys`, `inespayredsys`).
- Hook naming: `<action>_<gateway_id>_<event>` for filters (e.g. `woocommerce_redsys_icon`), `valid_<gateway_id>_standard_ipn_request` for the shared notification-received action.
- i18n: `__()`/`_e()`/`esc_html__()`/`esc_html_e()` consistently used with the `woo-redsys-gateway-light` text domain in the live plugin code (sampled, no hardcoded strings found in the main gateway class).
- Docblocks present at class/property level (`@since`, property docblocks); function-level docblocks less consistent — a documentation gap, not a blocking one.
- Directory-protection stub files (`index.php` at various levels) use non-standard ASCII-art comments instead of the one-line `// Silence is golden.` convention — cosmetic, harmless, left as-is (adoption doesn't impose style changes).

## Testing (real, verified commands)
PHPUnit 9.6 unit tests exist for `RedsysLiteAPI` (`tests/Unit/RedsysLiteAPITest.php`), the plugin's HMAC-SHA256 signature creation/verification class — the highest-risk code path per `docs/threat-model.md`. No `jest.config.*` or JS test suite exists yet.

- **Scope decision:** these are true unit tests against `RedsysLiteAPI` in isolation, not `WP_UnitTestCase` integration tests against a booted WordPress. The class has no WordPress runtime dependency beyond `wp_json_encode()`, which `tests/bootstrap.php` stubs — booting full WP core for this class would add engineering cost with no coverage benefit. See D-016 in `docs/decisions.md`.
- **Where it runs:** the host machine has no local PHP/Composer; tests run inside the `wp-env` `cli` Docker container (PHP 7.4, matches `.wp-env.json`), which already has Composer 2.10 and downloads PHPUnit 9.6.35 project-locally via `composer.json`.
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

- **Remaining gap:** all four gateway classes have IPN/callback test coverage, and the classic (shortcode) checkout flow has a real, mutation-tested smoke test. Not covered: the WooCommerce Blocks checkout (this playground's Checkout page uses the classic `[woocommerce_checkout]` shortcode), the other three gateways' checkout flows, and any JS unit-test suite for `resources/js/frontend/index.js`.
- **Full real-run command** (all suites, from the repo root, `wp-env` running):
  ```
  npx wp-env run cli bash -c "cd wp-content/plugins/woo-redsys-gateway-light && vendor/bin/phpunit"
  npx wp-env run tests-cli bash -c "cd wp-content/plugins/woo-redsys-gateway-light && vendor/bin/phpunit -c phpunit-integration.xml.dist"
  ```
  Real run, 2026-08-01: unit `OK (8 tests, 8 assertions)`; integration `OK (20 tests, 29 assertions)` — 28 tests total, all mutation/regression-verified individually (see `docs/05-test-points.md`).

## Build/lint commands (verified from `package.json`)
- `npm run build` — `wp-scripts build`, compiles `resources/js/frontend/index.js` → `assets/js/frontend/blocks.js` + `.asset.php`
- `npm run start` — `wp-scripts start` (watch mode)
- `npm run i18n:pot` — generates the `.pot` via `wp i18n make-pot` (WP-CLI, not verified runnable in this environment — requires WP-CLI + a WordPress install)
- `npm run test:e2e` (`playwright test`) — runs `tests/e2e/`; needs `npx wp-env start` and the one-time environment setup in `docs/playground.md`
- `phpcs.xml` — `WooCommerce-Core` + `WordPress-Extra` ruleset; not verified to run cleanly during adoption (adoption is read-only; running phpcs and recording its output is a Phase 5/gap-audit follow-up, not repeated here to avoid a stale claim)

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
Keel's "source first, minified for production" contract (`SKILL.md`) is **not yet applied** to this project — CSS ships unminified with no source/minified pairing, and there is no dedicated minify build step for CSS. Recorded as a gap (`docs/04-adoption-audit.md`), not retrofitted during adoption. When next touched, the fix is: add a CSS minify step to the existing webpack/`@wordpress/scripts` build (or a small dedicated script), rename sources to the `name.css` + `name.min.css` pairing, and load the minified form in production per WordPress `SCRIPT_DEBUG` convention.
