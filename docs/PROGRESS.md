# PROGRESS — Payment Gateway for Redsys & WooCommerce Lite

> Living state. Read this FIRST in every session. Keep current and compact.

## Project card
- Name / one-line purpose: `woo-redsys-gateway-light` — WooCommerce payment gateway plugin for Redsys (card via redirection, Bizum, Apple/Google Pay redirection, Inespay), Lite version of a commercial premium plugin.
- Project type: WordPress plugin / WooCommerce extension
- Stack & target platform(s): PHP (Requires PHP 7.0), WordPress (tested up to 7.0), WooCommerce (requires ≥7.4, tested up to 10.9); front-end build via `@wordpress/scripts`/webpack for the WooCommerce Blocks checkout integration
- License: GPL-2.0-or-later (reconciled 2026-08-01 — D-003)
- Docs language: English (D-004)
- Security profile: `references/security/wordpress.md`
- Accessibility: WCAG 2.2 AA floor, AAA where feasible (D-007)
- i18n: multi — base English, shipped locale `es_ES`, mechanism `load_plugin_textdomain()` + `__()`/`_e()` family, text domain `woo-redsys-gateway-light`
- Installed base: **in production with real users** — distributed on WordPress.org, current version 7.0.2, active changelog with real security fixes (signature/HMAC hardening in 7.0.1/7.0.2)
- Design system: one-off / n/a — plain hand-written CSS, no design system (D-009)
- Keel portability: lock + embedded v5.9.0
- Assistant config: none beyond the lock + embedded skill (tools: claude) (D-008)
- Models: n/a — no subagent role→model map configured
- Keel baseline: v5.9.0
- Website intent: no (D-005)
- Client budget: no (D-010)
- User guide: declined for now (D-013) — readme.txt covers it
- Docs theme: n/a — no guide
- Durability: git remote `origin` → `https://github.com/joseconti/woo-redsys-gateway-light.git` — satisfied
- Autonomy: automatic — Keel does not ask before pushing to `develop`, and performs every merge/push itself; never merges to `master` or tags without explicit instruction / issues: after-sprint / Issue sweep interval: 24h / Issue capture: off (D-011)
- Branches: integration branch `develop` (created 2026-07-31 from `master`, published to `origin`); current work: adoption on `develop`; nothing yet queued for `master`
- Notify: `PushNotification` tool (desktop + phone via Remote Control) — delivers (D-011)
- Chaining: start (D-015) — a clean close-out opens and submits a fresh Claude Code CLI session automatically via `osascript`/Terminal.app; verified end-to-end on macOS with `claude` on PATH at `~/.local/bin/claude`

## Phase status
| Phase | Status | Key artifacts |
|-------|--------|---------------|
| 1 Discovery | adopted (as-built) | docs/01-discovery.md |
| 2 Functional spec | adopted (as-built) | docs/02-functional-spec.md, docs/03-technical-plan.md |
| 3 Design handoff | n/a — pre-existing UI, no design contract (see docs/03-technical-plan.md) | — |
| 4 Faithful build | n/a — same as above | — |
| 5 Development | scaffold complete (D-014), playground verified for real; slices done: #93 fix, RedsysLiteAPI unit tests (D-016), IPN/callback integration tests for all four gateways — redsys (D-018), Bizum (D-019), GooglePay incl. security fix (D-020), Inespay (D-021) — and a Playwright checkout smoke test (D-022) | docs/issues.md, docs/playground.md, scripts/keel-doctor, scripts/keel-verify, scripts/keel-handoff-verify, docs/sprints/, docs/05-test-points.md, docs/threat-model.md, composer.json, phpunit.xml.dist, phpunit-integration.xml.dist, playwright.config.js, tests/, classes/class-wc-gateway-googlepay-redirection-redsys.php |
| 6 Documentation | in progress — `docs/api/INDEX.md` created; per-surface docs backfilled progressively | docs/api/INDEX.md |
| 7 Release | not started (next real release runs the full gate) | — |
| 8 Website | n/a — no website intent (D-005) | — |

## Current position
- Phase: Adoption complete, Phase 5 scaffold built and playground verified for real (D-014); slices done: #93 fix, `RedsysLiteAPI` unit tests (D-016/D-017), IPN/callback integration tests for all four gateway classes — `WC_Gateway_redsys` (D-018), `WC_Gateway_Bizum_Redsys` (D-019), `WC_Gateway_GooglePay_Redirection_Redsys` including a real security fix (D-020), `WC_Gateway_Inespay_Redsys` (D-021) — a Playwright checkout smoke test for the Redsys gateway (D-022), and a full-plugin review (4 parallel agents, findings independently re-verified) that found and fixed a wrong-secret bug in `successful_request()` for Bizum + GooglePay (D-023). 31 automated tests total (8 unit + 22 integration + 1 e2e), every one mutation- or regression-verified.
- **Blocked, awaiting the user:** the review's second "Alto" finding — Bizum and GooglePay persist the actual Redsys signing secret in plaintext, permanently, to order postmeta (`_redsys_secretsha256`) — needs the user's decision on HOW to fix it (it's load-bearing for a real per-user test-mode feature; a wrong fix could break that feature or leave the exposure). See D-024 in `docs/decisions.md` for detail.
- Next action: this session found and fixed two real security/correctness bugs (D-020, D-023) that are unreleased — a version bump + Phase 7 release should now be a priority so they reach production sites, rather than sitting fixed-but-unshipped on `develop`. 7 further findings from the review (D-024) are documented and deferred, not fixed this session — user scoped this session to the 2 "Alto" findings only. All 8 open GitHub issues have now been triaged for real (see `docs/issues.md`): 7 turned out to be stale/already-resolved (commented on GitHub, left open for the reporter/maintainer to close), 1 (#93) got a real fix this session. GitHub also reported 55 Dependabot alerts on the default branch after this session's push — not investigated yet, unrelated to any dependency this session touched.

## Open items
- **Unresolved user question:** how to fix the plaintext-secret-in-order-meta finding (D-024's second "Alto" item) without breaking Bizum/GooglePay's per-user test-mode feature, which currently relies on it. Asked; awaiting the answer.
- Open Design Requests: none — no design system in place
- Unverified external steps/assets: none
- Forge issues in progress: see `docs/issues.md` — E-001 (#93) fix landed, unreleased; awaiting a version bump + release before the reporter can test it. 7 stale issues commented on, left open (never closed by Keel on its own reading of the code, per protocol).

### Deferred items (consciously postponed work)
- Automated test coverage: `RedsysLiteAPI` (D-016), all four gateway classes' IPN/callback validation (D-018/D-019/D-020/D-021), a Redsys checkout smoke test (D-022, `tests/e2e/checkout-redsys.spec.js`), and both `successful_request()` regression tests (D-023) are covered. Not covered: WooCommerce Blocks checkout (only the classic shortcode checkout is tested), the other three gateways' checkout flows, and any JS unit-test suite for `resources/js/frontend/index.js`. Flagged in `docs/04-adoption-audit.md` (Testability), severity low, review trigger "before the next release, or when the Blocks checkout integration is next touched"
- `class-wc-gateway-inespay-redsys.php`'s `handle_callback()` (success path) writes WooCommerce's internal `_payment_method` meta key via the generic meta API instead of `$order->set_payment_method()` — surfaced while writing its test (L-004, `docs/lessons-learned.md`), not fixed (low severity, works today, out of scope for a test-writing slice). Review trigger: next time `handle_callback()`'s success path is touched.
- **7 findings from the full-plugin review (D-024), documented, not fixed this session** — user scoped the session to the 2 "Alto" findings only:
  - Medium: unauthenticated blocking `sleep(5)` on the order-received page before any signature check (`woocommerce-redsys.php:385`).
  - Medium: GooglePay's `check_ipn_request_is_valid()` looks up a real order before verifying the signature (unauthenticated probing/500 risk).
  - Medium/correctness: `prepare_order_number()`/`clean_order_number()`'s post-transient-expiry fallback can resolve the wrong order (~1 in 5 times it triggers).
  - Low: `===`/`!==` instead of `hash_equals()` for signature comparison (all HMAC gateways except Inespay).
  - Low-medium: Inespay refund callbacks can land in the "payment completed" branch (misleading note/meta/hook, not exploitable to un-refund).
  - Low: Inespay's `disable_inespay()` truncates the cart total to `(int)` before comparing to the transaction limit.
  - Low: Inespay's `process_refund()` treats an explicit `0` refund amount as "not given," refunding the full total instead.
  Review trigger: next security-focused session, or before a release that the user wants a clean threat-model for.
- GitHub reported 55 Dependabot vulnerability alerts (1 critical, 32 high, 19 moderate, 3 low) on the default branch after this session's `develop` push — not investigated, unrelated to any dependency change made this session (likely `node_modules`/`package-lock.json`, which this session did not touch). Review trigger: next time the user wants a dependency/security pass, or before the next release's Phase 7 gate.
- `package.json` carries a stale `wp-scripts ^0.0.1-security` dependency (looks like a squatted/placeholder package name, distinct from `@wordpress/scripts`) — severity medium, review trigger "next time package.json dependencies are touched"
- `package.json` version (`4.0.0`) is out of sync with the plugin's real version (`7.0.2`) — severity low, review trigger "next release"

Last updated: 2026-08-01 — Phase 5 sprint (test coverage for RedsysLiteAPI, all four gateway classes, checkout smoke test, full-plugin review; 2 real bugs found and fixed, 7 more documented — 31 tests total)
