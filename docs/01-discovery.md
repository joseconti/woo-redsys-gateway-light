# 01 — Discovery

> **Adopted project — reconstructed as-built.** This document describes what the project IS today, reconstructed from the codebase and the readme.txt changelog, not a pre-build discovery process. Sections marked `as-built, unverified` are inferred from reading code, never confirmed against a test or the user.

## Problem / outcome
The plugin lets a WooCommerce store accept payments through Redsys (the payment processor used by most Spanish banks) without a full custom integration: card payments via redirection to the Redsys payment page, plus Bizum, Apple/Google Pay redirection, and Inespay (bank-transfer redirection) as additional gateways. It is the **Lite** version of a commercial "premium" plugin (`https://woocommerce.com/products/redsys-gateway/`), distributed free on WordPress.org as a funnel toward the paid product.

## Proposed v1 / scope as it exists today
Already built and released (current version 7.0.2):
- Redsys card gateway (redirection flow) — `redsys` gateway ID
- Bizum gateway — `bizumredsys` gateway ID
- Apple Pay / Google Pay via redirection — `googlepayredirecredsys` gateway ID
- Inespay (bank-transfer redirection) — `inespayredsys` gateway ID
- WooCommerce classic checkout AND WooCommerce Blocks checkout support for all four gateways
- Admin settings screens per gateway (WooCommerce Settings API): merchant code, terminal, currency, SHA-256 secret/test-secret, order status mapping, titles/descriptions/icons
- Notification (IPN-equivalent) handling per gateway via `woocommerce_api_wc_gateway_<id>`, with signature verification (`RedsysLiteAPI`, HMAC_SHA256_V1) that fails closed when no secret is configured
- Multi-language ready (English base, `es_ES` shipped translation)

**Later / not in this Lite version** *(as-built, unverified — inferred from the readme.txt description contrasting Lite vs Premium, not confirmed line-by-line against the premium plugin)*: the readme.txt states the premium version has "many more" features than Lite; the exact delta was not inventoried during adoption since the premium plugin's code is not in this repo.

## Project type
WordPress plugin / WooCommerce extension (D-001). Security profile: `references/security/wordpress.md`. Accessibility: WCAG 2.2 AA floor (D-007).

## Constraints
- Must stay compatible with WooCommerce's Settings API, classic checkout, AND Blocks checkout simultaneously (four gateway classes each implement both).
- Must interoperate with Redsys's external redirection + signature-verification protocol, which the plugin does not control.
- WordPress.org distribution rules apply: GPL-2.0-or-later compatible license (D-003), no external dependency bundling beyond what WordPress.org tooling allows.
- No runtime Composer/dependency manager — the Redsys signature logic is hand-rolled (`includes/class-redsysliteapi.php`). Composer is used for dev-only test tooling (PHPUnit), never shipped.

## Competitive scan
Not run — adoption treats the competitive scan as recommended-but-optional (per `references/adoption.md` step 3) and it was skipped for this pass. It feeds the roadmap rather than gating adoption. If useful for prioritizing new features later, it can be run then.

## Installed base
**In production, with real users and stored data.** Evidence: WordPress.org distribution, current `Stable tag: 7.0.2`, a substantial `readme.txt` changelog documenting real fixes across many prior versions, and 8 open GitHub issues from real users reporting production problems (see `docs/issues.md`). Any code change from here on must treat backward compatibility and safe upgrades as mandatory (Phase 5 rule for adopted projects with an installed base).

## i18n & accessibility (initial)
- i18n: multi-language-ready, base English, `es_ES` shipped, mechanism confirmed consistent in the sampled main gateway class (D-002).
- Accessibility: WCAG 2.2 AA floor / AAA where feasible (D-007), applies to the admin settings screens and any checkout-facing markup the plugin renders.

## License
GPL-2.0-or-later, reconciled across `woocommerce-redsys.php`, `readme.txt`, `package.json`, and a new root `LICENSE` file (D-003).

## Design system
One-off / n/a — no design system exists or is planned (D-009).

## Website intent
No (D-005).

## Client budget
No (D-010).

## Environment & test drivers (step 5a preflight)
Recorded 2026-10-06 from a real `scripts/keel-doctor --check` run on the user's Mac (Darwin 27.0.0, arm64), during the Keel v6.5.0 reconciliation. Adoption predates this step, so this is its first record. Nothing was installed and nothing was started for it.

- This session can run commands where the repo lives: **yes** — a shell on the machine holding the repository.
- Environment restrictions found: none of the measured kind (network, deletion, localhost and the repository are all reachable from one filesystem). One fact about the shell: the assistant's non-interactive shell does not have Node on its `PATH` (`node`, `npm`, `npx` live under `~/.nvm/versions/node/v24.21.0/bin`, loaded by the interactive profile only). The doctor and `scripts/keel-affected-tests` corroborate that location instead of reporting Node as missing.
- `claude` on PATH: **yes** — `~/.local/bin/claude` (card: `Chaining: start`, D-015).
- Machines in play: one — the user's machine, the repo host and the test runner are the same Mac.
- Present on the test machine:
  - Node.js 24.21.0, npm 11.19.0, npx (under `~/.nvm`, see above) — required >= 18: `OK`
  - Docker CLI 29.8.1: `OK`; Docker daemon responding: `OK`
  - PHP 8.5.11 on the host (`/opt/homebrew/bin/php`): `OK`, optional — the tests run on PHP 7.4 inside wp-env, not on this
  - python3 (`/usr/bin/python3`)
- Missing or not operational (nothing installed at this step):
  - `@wordpress/env` in `node_modules`: `MISSING`, optional — `npm install` (`node_modules/` is absent in this checkout)
  - wp-env playground of this repository: `NOT OPERATIONAL` — Docker is up but no running container mounts this repository (another project's wp-env was running on the machine at the time; it is not this project's environment) — `npx wp-env start`
  - PHPUnit in `vendor/`: `MISSING`, blocking — with the playground running, `composer install` inside the `cli` container
  - `@playwright/test` in `node_modules`: `MISSING`, blocking — `npm install`
  - Playwright Chromium: `MISSING`, blocking — a build is cached in `~/Library/Caches/ms-playwright`, but the revision this project needs is unknown until `npm install`; then `npx playwright install chromium` (a few hundred MB)
  - Consequence, stated plainly: **no test suite could be run on this machine at the moment of this record.** The doctor exits non-zero until the three blocking rows are installed.
- Advisory rows: permission mode `auto` — `OK`; browser MCP registered at user level — none, `OK`; project `.mcp.json` — none (the browser is driven by the test runner only), `OK`; Playwright browsers orphaned to PID 1 — 0, `OK`.
- Notification channel: not probed by the doctor (a script cannot probe the assistant's notification tool). The card records `PushNotification` as delivering (D-011).
- Impossible on this machine: nothing — the project has no Apple, Android or native-desktop surface.
- Screen-stealing verdict per platform: web storefront and admin → Playwright, **headless**; HTTP endpoints and PHP logic → PHPUnit / `curl` in containers, **headless**. No surface takes the screen; no mitigation is needed.
- Licence or privilege consequences flagged to the user: Docker Desktop's licence (paid for organisations of 250+ employees or over $10M revenue; Colima is the MIT alternative). No sudo and no `docker` group change is involved on macOS.
- Installing the missing pieces: **offered, not yet answered** — `scripts/keel-doctor --plan` prints the exact list (`npm install`, `npx playwright install chromium`; then, with the playground started, `composer install` in the `cli` container). The user's answer is recorded here when given.

## Preliminary estimate
Not produced — this is an adoption of an already-shipped, mature project, not a from-zero build. Estimates for specific future work (issue fixes, new features) will be produced per `references/estimation-budget.md` when that work is scoped.
