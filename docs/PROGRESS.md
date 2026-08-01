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
| 5 Development | scaffold complete (D-014), playground verified for real; slices done: #93 fix, RedsysLiteAPI unit tests (D-016), IPN/callback integration tests for all four gateways — redsys (D-018), Bizum (D-019), GooglePay incl. security fix (D-020), Inespay (D-021) — a Playwright checkout smoke test (D-022), a full-plugin review that fixed 9 real bugs total (D-023, D-026) and deferred 1 by the user's choice (D-025), and closing all 4 remaining testability gaps (D-027–D-031: Bizum/Google Pay/Inespay checkout e2e, the WooCommerce Blocks checkout, `disable_inespay()` fractional-total coverage, and a recorded decision against JS unit-test tooling) | docs/issues.md, docs/playground.md, scripts/keel-doctor, scripts/keel-verify, scripts/keel-handoff-verify, docs/sprints/, docs/05-test-points.md, docs/threat-model.md, composer.json, phpunit.xml.dist, phpunit-integration.xml.dist, playwright.config.js, .wp-env-mu-plugins/, tests/, classes/, woocommerce-redsys.php |
| 6 Documentation | in progress — `docs/api/INDEX.md` created; per-surface docs backfilled progressively | docs/api/INDEX.md |
| 7 Release | not started (next real release runs the full gate) | — |
| 8 Website | n/a — no website intent (D-005) | — |

## Current position
- Phase: Adoption complete, Phase 5 scaffold built and playground verified for real (D-014); slices done: #93 fix, `RedsysLiteAPI` unit tests (D-016/D-017), IPN/callback integration tests for all four gateway classes — `WC_Gateway_redsys` (D-018), `WC_Gateway_Bizum_Redsys` (D-019), `WC_Gateway_GooglePay_Redirection_Redsys` including a real security fix (D-020), `WC_Gateway_Inespay_Redsys` (D-021) — a Playwright checkout smoke test for the Redsys gateway (D-022), a full-plugin review (4 parallel agents, every finding independently re-verified by reading the actual code) that found 9 real bugs and fixed all of them (D-023 wrong-secret bug; D-026's 7 findings — a DoS, a pre-auth crash risk in two gateways, a ~1-in-5 wrong-order bug, non-constant-time comparisons, an Inespay refund/payment mixup, and two Inespay amount bugs), deferring only 1 by the user's explicit choice (D-025, plaintext secret in order meta), and — at the user's follow-up request to close remaining testability gaps — Bizum + Google Pay checkout e2e tests (D-027, which also surfaced and fixed a real test-order dependency in the existing Redsys e2e test), a WooCommerce Blocks checkout e2e test (D-028, the first test of any kind for that pre-existing integration), an Inespay checkout e2e test via a dev-only `pre_http_request` stub since Inespay's `process_payment()` makes a real server-side API call (D-029), `disable_inespay()`'s fractional-total float-comparison coverage (D-030, whose own first draft couldn't have caught the bug it targeted until mutation-testing caught that — see L-005), and a recorded decision against building JS unit-test tooling for `resources/js/frontend/index.js` (D-031, closed instead by the Blocks e2e test exercising the real compiled bundle). 46 automated tests total (8 unit + 31 integration + 7 e2e), all but one (D-026's `disable_inespay()` FIX, code-review-verified only — its float-vs-int BEHAVIOR is now separately e2e-covered by D-030) mutation- or regression-verified against the pre-fix code.
- Next action: this session found and fixed real security/correctness bugs (D-020, D-023, D-026 — 9 total across the full-plugin review) that are unreleased — a version bump + Phase 7 release should now be a priority so they reach production sites, rather than sitting fixed-but-unshipped on `develop`. Only D-025 (the plaintext-secret-in-order-meta finding) remains deliberately deferred, by the user's explicit choice; all four testability gaps recorded after the previous sprint are now closed (D-027–D-031). All 8 open GitHub issues have now been triaged for real (see `docs/issues.md`): 7 turned out to be stale/already-resolved (commented on GitHub, left open for the reporter/maintainer to close), 1 (#93) got a real fix this session. GitHub also reported 55 Dependabot alerts on the default branch after this session's push — not investigated yet, unrelated to any dependency this session touched.

## Open items
- Unresolved user questions: none.
- Open Design Requests: none — no design system in place
- Unverified external steps/assets: none
- Forge issues in progress: see `docs/issues.md` — E-001 (#93) fix landed, unreleased; awaiting a version bump + release before the reporter can test it. 7 stale issues commented on, left open (never closed by Keel on its own reading of the code, per protocol).

### Deferred items (consciously postponed work)
- Automated test coverage: `RedsysLiteAPI` (D-016), all four gateway classes' IPN/callback validation (D-018/D-019/D-020/D-021), all four gateways' checkout flows (D-022 Redsys, D-027 Bizum/Google Pay, D-029 Inespay via a dev-only HTTP stub), the WooCommerce Blocks checkout (D-028), and `disable_inespay()`'s fractional-total behavior (D-030) are all covered — this closes the testability gap flagged in `docs/04-adoption-audit.md`. JS unit-test tooling (Jest) was deliberately NOT added (D-031): `resources/js/frontend/index.js` has no independent logic to unit-test in isolation, and D-028's e2e test already exercises its real compiled output end to end. Review trigger: revisit D-031 if that file grows real conditional logic (e.g. a `canMakePayment()` beyond the current `() => true`).
- `class-wc-gateway-inespay-redsys.php`'s `handle_callback()` (success path) writes WooCommerce's internal `_payment_method` meta key via the generic meta API instead of `$order->set_payment_method()` — surfaced while writing its test (L-004, `docs/lessons-learned.md`), not fixed (low severity, works today, out of scope for a test-writing slice). Review trigger: next time `handle_callback()`'s success path is touched.
- **The plaintext-secret-in-order-meta finding (D-025)** — Bizum and GooglePay persist the real Redsys signing secret, unencrypted and permanently, to order postmeta. Documented in `docs/threat-model.md`; user was offered three fix approaches and chose to leave it documented and decide later rather than guess at a fix that could break the per-user test-mode feature it's load-bearing for. Review trigger: whenever the user is ready to decide. **This is the only remaining open finding from the full-plugin review** — all 7 others (D-024) were fixed in the same session (D-026).
- GitHub reported 55 Dependabot vulnerability alerts (1 critical, 32 high, 19 moderate, 3 low) on the default branch after this session's `develop` push — not investigated, unrelated to any dependency change made this session (likely `node_modules`/`package-lock.json`, which this session did not touch). Review trigger: next time the user wants a dependency/security pass, or before the next release's Phase 7 gate.
- `package.json` carries a stale `wp-scripts ^0.0.1-security` dependency (looks like a squatted/placeholder package name, distinct from `@wordpress/scripts`) — severity medium, review trigger "next time package.json dependencies are touched"
- `package.json` version (`4.0.0`) is out of sync with the plugin's real version (`7.0.2`) — severity low, review trigger "next release"

Last updated: 2026-08-01 — Phase 5 sprint (test coverage for RedsysLiteAPI, all four gateway classes, full-plugin review — 9 real bugs found and fixed, 1 deliberately deferred — plus closing all 4 remaining testability gaps: e2e coverage for all 4 gateways' checkout flows, the WooCommerce Blocks checkout, and `disable_inespay()`'s fractional-total behavior, with a recorded decision against JS unit-test tooling — 46 tests total)
