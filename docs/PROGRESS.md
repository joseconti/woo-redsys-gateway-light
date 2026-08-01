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
| 5 Development | scaffold complete (D-014), playground verified for real; slices done: #93 fix, RedsysLiteAPI unit tests (D-016), WC_Gateway_redsys IPN integration tests (D-018) | docs/issues.md, docs/playground.md, scripts/keel-doctor, scripts/keel-verify, scripts/keel-handoff-verify, docs/sprints/, docs/05-test-points.md, composer.json, phpunit.xml.dist, phpunit-integration.xml.dist, tests/ |
| 6 Documentation | in progress — `docs/api/INDEX.md` created; per-surface docs backfilled progressively | docs/api/INDEX.md |
| 7 Release | not started (next real release runs the full gate) | — |
| 8 Website | n/a — no website intent (D-005) | — |

## Current position
- Phase: Adoption complete, Phase 5 scaffold built and playground verified for real (D-014); first real slice done (issue #93 fix); second slice done — `RedsysLiteAPI` unit tests (D-016/D-017); third slice done — `WC_Gateway_redsys` IPN validation integration tests (D-018)
- Next action: bump the plugin version and cut a release (Phase 7) so the #93 fix reaches users and the reporter can confirm it — or continue picking up further work first (e.g. extend the same integration-test pattern to the other three gateway classes, or a Playwright smoke test for the checkout flow) and batch it into the same release. All 8 open GitHub issues have now been triaged for real (see `docs/issues.md`): 7 turned out to be stale/already-resolved (commented on GitHub, left open for the reporter/maintainer to close), 1 (#93) got a real fix this session. GitHub also reported 55 Dependabot alerts on the default branch after this session's push — not investigated yet, unrelated to any dependency this session touched.

## Open items
- Unresolved user questions: none
- Open Design Requests: none — no design system in place
- Unverified external steps/assets: none
- Forge issues in progress: see `docs/issues.md` — E-001 (#93) fix landed, unreleased; awaiting a version bump + release before the reporter can test it. 7 stale issues commented on, left open (never closed by Keel on its own reading of the code, per protocol).

### Deferred items (consciously postponed work)
- Automated test coverage: `RedsysLiteAPI` (unit tests, D-016) and `WC_Gateway_redsys`'s IPN validation (integration tests, D-018) are covered. **Correction to an earlier note in this file:** `class-wc-gateway-bizum-redsys.php`'s `check_ipn_request_is_valid()` was assumed to share the exact same shape and was attempted as a direct copy of the Redsys test — it does not: after a signature match it calls `WCRedL()->get_order( $order2 )` on a real order (throws "Invalid order" without one) and derives the secret per-order from a transient/`_redsys_secretsha256` order meta rather than only the gateway setting. The attempted test was removed rather than committed with a wrong fixture. Testing Bizum (and, unverified, `class-wc-gateway-googlepay-redirection-redsys.php`, which also calls `WCRedL()->get_order()`) needs a real `WC_Order` fixture (`wc_create_order()` + matching order-number cleaning + the meta/transient) — a genuinely separate, larger slice, not a copy-paste of the Redsys pattern. `class-wc-gateway-inespay-redsys.php` uses a different signature algorithm entirely (plain `hash_hmac('sha256', dataReturn, api_key)` compared with `hash_equals()`, not `RedsysLiteAPI`) and needs its own fixture builder. No JS test suite exists. Flagged in `docs/04-adoption-audit.md` (Testability), severity low-medium (was high — the two highest-risk paths are now covered), review trigger "before the next release, or the next time one of the other three gateway classes' notification handler is touched"
- GitHub reported 55 Dependabot vulnerability alerts (1 critical, 32 high, 19 moderate, 3 low) on the default branch after this session's `develop` push — not investigated, unrelated to any dependency change made this session (likely `node_modules`/`package-lock.json`, which this session did not touch). Review trigger: next time the user wants a dependency/security pass, or before the next release's Phase 7 gate.
- `package.json` carries a stale `wp-scripts ^0.0.1-security` dependency (looks like a squatted/placeholder package name, distinct from `@wordpress/scripts`) — severity medium, review trigger "next time package.json dependencies are touched"
- `package.json` version (`4.0.0`) is out of sync with the plugin's real version (`7.0.2`) — severity low, review trigger "next release"

Last updated: 2026-08-01 — Phase 5 sprint (RedsysLiteAPI unit tests + WC_Gateway_redsys IPN integration tests)
