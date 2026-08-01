# Threat Model — Payment Gateway for Redsys & WooCommerce Lite

> Produced during adoption (2026-08-01) from `references/security/wordpress.md` and a reading of the real code (no re-run of dynamic tests). Every control below carries its HONEST delivery state — only `IN PLACE` is written in the present tense.

## Assumptions
- Runs inside a WordPress + WooCommerce install the site owner administers; a compromised WordPress admin account can already execute arbitrary PHP and is out of scope for plugin-level defenses.
- Redsys (and Inespay) are trusted external payment processors reached over HTTPS; the plugin does not control their infrastructure.
- The merchant's Redsys SHA-256 secret is entered by the site owner into the gateway's WooCommerce settings and stored as a WordPress option — never committed to the repository (verified during adoption inventory: no secret-shaped strings found in the codebase).
- This plugin handles no card data directly (redirection-based flow to Redsys's own hosted payment page) — PCI scope for card data stays with Redsys, not this plugin.

## Defended controls

| Control | Delivery state | Evidence / where |
|---|---|---|
| Notification signature verification (HMAC_SHA256_V1) before trusting any Redsys/Inespay notification | `IN PLACE` | `includes/class-redsysliteapi.php` (`create_merchant_signature_notif`), invoked from each gateway's `check_ipn_response`/`handle_callback` handler |
| Fail closed when no SHA-256 secret is configured (reject rather than trust an unauthenticated notification) | `IN PLACE` | `classes/class-wc-gateway-redsys.php` ~lines 868–943; confirmed by the 7.0.1/7.0.2 `readme.txt` changelog entries describing this exact hardening; **driven-verified 2026-08-01** in the wp-env playground — two fabricated POSTs to `?wc-api=WC_Gateway_redsys` with no configured secret were rejected and the target order stayed `wc-pending`, see `docs/playground.md` |
| Order-amount cross-check against the notification total before marking an order paid | `IN PLACE` | `classes/class-wc-gateway-redsys.php` ~line 1026 (mismatch → `on-hold`, never silently trusted) |
| Text domain / i18n consistency (no obvious string-injection surface via translations) | `IN PLACE` | sampled `class-wc-gateway-redsys.php`, consistent `__()`/`_e()` usage with escaping functions (`esc_html__`, `esc_html_e`) present in the sample |
| No secrets committed to the repository | `IN PLACE` | verified during adoption's confidential-data scan (no `.env`, no literal keys found; SHA-256 secret is a runtime WooCommerce option) |
| WooCommerce Settings API used for all gateway configuration (no custom unauthenticated admin endpoints) | `IN PLACE` | all four gateway classes extend `WC_Payment_Gateway` and use `init_form_fields()` |
| CSRF/nonce protection on admin settings forms | `VERIFY` | WooCommerce's Settings API handles this by default for standard fields; not independently re-verified against this plugin's specific field types during adoption (read-only pass) |
| Output escaping across all admin/checkout-rendered strings | `VERIFY` | spot-checked in the main gateway class only; the full tree was not exhaustively scanned for every `echo`/`printf` — flagged as a gap-audit item (see `docs/04-adoption-audit.md`, Security dimension) |
| Capability checks on settings pages beyond WooCommerce's own gating | `VERIFY` | relies on WooCommerce's default `manage_woocommerce` gating for its Settings API pages; not independently re-verified for this plugin |
| Rate limiting / replay protection on the notification endpoint (`?wc-api=WC_Gateway_<id>`) | `TO BUILD` or `MANUAL` (undetermined) | no rate limiting or nonce/replay-window logic observed in the notification handlers beyond signature verification — a valid, correctly-signed notification could in principle be replayed; not confirmed whether Redsys's own protocol prevents this at their end |
| Automated dependency/CVE scanning (`composer audit`/`npm audit`) | `TO BUILD` | no CI, no `composer.json` (no PHP deps to scan); `npm audit` has never been run against `package.json`'s devDependencies as part of this project's process |
| Automated security test coverage (signature verification, fail-closed paths) | `TO BUILD` | no test suite exists at all (confirmed in `docs/03-technical-plan.md`) |

## Not defended
| Not defended | Consequence | If you need it |
|---|---|---|
| A compromised or malicious site administrator | Can already execute PHP; no plugin-level control survives that | Nothing at the plugin level — hosting/account security |
| Other plugins and the active theme | Share the process, database and global scope; a hostile or broken one can alter this plugin's behavior | Defensive prefixing (already used: `redsys`/`bizumredsys`/etc. IDs), capability re-checks at each entry point |
| Data at rest in the database | The SHA-256 secret and other settings are stored as WordPress options, unencrypted, as the platform provides | Encrypt the specific field before storing and own key handling — not currently done, not currently required by the plugin's threat profile (a compromised DB with admin access already exposes more) |
| Brute force against `wp-login` and the WooCommerce REST API | Out of this plugin's scope | A dedicated security plugin, host-level rate limiting, or a WAF |
| Replay of a validly-signed Redsys/Inespay notification within its (unknown) validity window | Same order could in principle be re-processed if Redsys's own protocol doesn't prevent it | Would need explicit replay protection (nonce/timestamp tracking) at the plugin level if Redsys doesn't guarantee it — flagged as `TO BUILD or MANUAL (undetermined)` above, worth clarifying with Redsys's own protocol documentation before building anything |
| Supply chain of build-time devDependencies (`@wordpress/scripts`, webpack, etc.) beyond a manual read | `npm audit` has not been run as part of this project's process | Run `npm audit`, review advisories, pin versions |
| PII in debug/error logs | This plugin has no dedicated debug-log-with-a-switch (Keel's own contract) yet | Add one per Keel's debug-logging contract when Phase 5 scaffold work happens; redact at the log call |
