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
- Keel portability: lock + embedded v7.3.2 (both trees replaced and verified file-for-file 2026-10-21, lock block refreshed v7.3.2 — D-093)
- Assistant config: full (tools: claude) — rules (`.claude/rules/`) + agents (`.claude/agents/`) + confidential-data pre-commit gate (`.githooks/pre-commit`, active via `core.hooksPath`) + committed allow-list and Stop hook (`.claude/settings.json`, confirmed by the user); MCP n/a (the plan defines no development MCP servers); CI not accepted (D-039, D-043)
- Models: orchestrator=session model (chained chats launch with opus, D-037) / reviewer=sonnet / mechanical=haiku (tools: claude) (D-043)
- Keel baseline: v7.3.2 (reconciled 2026-10-21, D-097)
- Website intent: no (D-005)
- Client budget: no (D-010)
- User guide: declined for now (D-013) — readme.txt covers it
- Docs theme: n/a — no guide
- Durability: git remote `origin` → `https://github.com/joseconti/woo-redsys-gateway-light.git` — satisfied
- Autonomy: automatic — Keel does not ask before pushing to `develop`, and performs every merge/push itself; never merges to `master` or tags without explicit instruction / issues: after-sprint / Issue sweep interval: 24h / Issue capture: off (D-011)
- Branches: integration branch `develop` (created 2026-07-31 from `master`, published to `origin`); bookkeeping on `develop`, sprint 5 product work on `work/after-7.1.0` (merge into `develop` after the `v7.1.0` tag, D-103); **queued for `master`: release 7.1.0** (the owner merges and tags)
- Notify: `PushNotification` tool (desktop + phone via Remote Control) — delivers (D-011)
- Chaining: start (D-015) — a clean close-out opens and submits a fresh Claude Code CLI session automatically via `osascript`/Terminal.app; verified end-to-end on macOS with `claude` on PATH at `~/.local/bin/claude`
- Chaining model: opus (D-037) — every chained chat launches with `claude --model opus`
- Chain verified: 2026-10-06, tier start, Keel 6.5.0, keel-continue b8796f66873c0ff2, row claude:0a2731fe91ba319a
- Test-first policy: pure-logic (D-036)
- Push test scope: affected — the default, never asked
- Sprints: on — the default, never asked
- Parallel development: auto; max_workers: max (owner, D-093)
- Security audit: required — money moves, and the IPN/callback endpoints (`?wc-api=WC_Gateway_<id>`) are reachable from outside (derived, D-040)
- CI runs on: n/a — no forge CI

## Phase status
| Phase | Status | Key artifacts |
|-------|--------|---------------|
| 1 Discovery | adopted (as-built) | docs/01-discovery.md |
| 2 Functional spec | adopted (as-built) | docs/02-functional-spec.md, docs/03-technical-plan.md |
| 3 Design handoff | n/a — pre-existing UI, no design contract (see docs/03-technical-plan.md) | — |
| 4 Faithful build | n/a — same as above | — |
| 5 Development | scaffold complete (D-014), playground verified for real; slices done: #93 fix, RedsysLiteAPI unit tests (D-016), IPN/callback integration tests for all four gateways — redsys (D-018), Bizum (D-019), GooglePay incl. security fix (D-020), Inespay (D-021) — a Playwright checkout smoke test (D-022), a full-plugin review that fixed 9 real bugs total (D-023, D-026) and deferred 1 by the user's choice (D-025), closing all 4 remaining testability gaps (D-027–D-031), and fixing the "1 billion bug" — high-order digits lost for 10+ digit order IDs, plus an unrenewed refund transient (D-033) | docs/issues.md, docs/playground.md, scripts/keel-doctor, scripts/keel-verify, scripts/keel-handoff-verify, docs/sprints/, docs/05-test-points.md, docs/threat-model.md, composer.json, phpunit.xml.dist, phpunit-integration.xml.dist, playwright.config.js, .wp-env-mu-plugins/, tests/, classes/, woocommerce-redsys.php |
| 6 Documentation | in progress — `docs/api/INDEX.md` created; per-surface docs backfilled progressively | docs/api/INDEX.md |
| 7 Release | **7.1.0 candidate ready on `develop`** — version approved (D-099), gate run on the candidate (D-100, D-101); waiting on the owner's two acceptances, the merge to `master`, the tag and the WordPress.org upload | docs/07-release.md |
| 8 Website | n/a — no website intent (D-005) | — |

## Current position
- Slice: **2026-10-21, session from 15:55 (sprint 5, D-103): next-version defect fixes S-092, S-065, S-034, S-042 on the work branch `work/after-7.1.0` — merged into `develop` only after the owner tags `v7.1.0`; `develop` stays the unchanged 7.1.0 candidate.** Before it, sprint 4 (S-091, D-102: analysers run once outside the repository) and sprint 3 (the 7.1.0 candidate, D-098 to D-101). The release record is `docs/07-release.md`; its last sections are the list of what is left. The plan lives in `docs/sprints/README.md` (index) and `docs/.keel/plan.json` (derived).
- Phase: Adoption complete, Phase 5 scaffold built and playground verified for real (D-014); slices done: #93 fix, `RedsysLiteAPI` unit tests (D-016/D-017), IPN/callback integration tests for all four gateway classes — `WC_Gateway_redsys` (D-018), `WC_Gateway_Bizum_Redsys` (D-019), `WC_Gateway_GooglePay_Redirection_Redsys` including a real security fix (D-020), `WC_Gateway_Inespay_Redsys` (D-021) — a Playwright checkout smoke test for the Redsys gateway (D-022), a full-plugin review (4 parallel agents, every finding independently re-verified by reading the actual code) that found 9 real bugs and fixed all of them (D-023 wrong-secret bug; D-026's 7 findings — a DoS, a pre-auth crash risk in two gateways, a ~1-in-5 wrong-order bug, non-constant-time comparisons, an Inespay refund/payment mixup, and two Inespay amount bugs), deferring only 1 by the user's explicit choice (D-025, plaintext secret in order meta), closing all 4 remaining testability gaps (D-027–D-031), and — triggered by a real customer support ticket — fixing the "1 billion bug" (D-033): order IDs of 10+ digits lost their high-order digits in `prepare_order_number()`/`clean_order_number()` independent of D-026's earlier fix, and the refund path never renewed the order-number transient, making it hit the lossy fallback almost every time. Ported the fix from the premium plugin (`woocommerce-gateway-redsys` v31.0.3): a persistent postmeta reverse-lookup in `clean_order_number()`, plus a 24h transient renewal in `process_refund()` for Redsys, Bizum and Google Pay. 51 automated tests total (8 unit + 36 integration + 7 e2e), all mutation- or regression-verified against the pre-fix code except the one already-noted D-026 exception.
- Next action: **the owner's** — (1) accept, or not, self-audit answers 1 (analysers run once outside the repository, not installed — D-102) and 6 (S-065, the Blocks script's Spanish JSON) for 7.1.0; (1b) take or leave the shorter 7.1.0 upgrade notice (`docs/07-release.md`; if taken, the assistant applies it before the tag); (2) merge `develop` into `master`, tag `v7.1.0`, build `git archive --format=zip --prefix=woo-redsys-gateway-light/ v7.1.0` and upload it to WordPress.org SVN. **Then the assistant's:** post the replies on #93, #112 and the two WordPress.org forum threads saying the fix ships in 7.1.0 (D-099), update `docs/issues.md`, mark Phase 7 done and move the project to maintenance (`references/maintenance.md`), and merge `work/after-7.1.0` (sprint 5) into `develop`, and plan the next version from it plus `docs/sprints/deferred.md` (S-030, S-090, S-033, S-040, S-041, S-053, S-055, S-057, S-093).

## Open items
- Unresolved user questions: for 7.1.0, the two acceptances in "Next action" (D-100) and the upgrade-notice wording (D-102). Closed by D-099 on 2026-10-21: the version, the 7.0.3-hotfix choice (moot), the accessibility pass (accepted shortfall), SA-06/SA-13/SA-20 and the tunnel payment (acknowledged; S-090 after the release), the changelog wording, D-053's choice, how the package is built, and the forum replies (posted after publication). Offered, not required: a second playground on a current PHP 8.x is moot (the ceiling runs PHP 8.4); adding `scripts/playground-setup` to the committed allow-list (D-043).
- Ready for `master` (parked — waiting on the owner): **`develop` at the close of this session — release 7.1.0** (candidate `704730a`; later commits change no shipped file). The merge and the tag are the owner's acts.
- Open Design Requests: none — no design system in place
- Unverified external steps/assets: none
- Forge issues in progress (parked on the user's release): see `docs/issues.md` — E-001 (#93) and E-002 (#112) fixes landed, unreleased; both await a version bump + release before the reporters can test them. 7 stale issues commented on, left open (never closed by Keel on its own reading of the code, per protocol).

### Deferred items (consciously postponed work)
- Automated test coverage: `RedsysLiteAPI` (D-016), all four gateway classes' IPN/callback validation (D-018/D-019/D-020/D-021), all four gateways' checkout flows (D-022 Redsys, D-027 Bizum/Google Pay, D-029 Inespay via a dev-only HTTP stub), the WooCommerce Blocks checkout (D-028), and `disable_inespay()`'s fractional-total behavior (D-030) are all covered — this closes the testability gap flagged in `docs/04-adoption-audit.md`. JS unit-test tooling (Jest) was deliberately NOT added (D-031): `resources/js/frontend/index.js` has no independent logic to unit-test in isolation, and D-028's e2e test already exercises its real compiled output end to end. Review trigger: revisit D-031 if that file grows real conditional logic (e.g. a `canMakePayment()` beyond the current `() => true`).
- L-004 (`handle_callback()` of Inespay writing `_payment_method` as generic meta) and D-053's twin in Google Pay are fixed on `work/after-7.1.0` (S-034, S-042 — sprint 5); they reach `develop` with that branch after the `v7.1.0` tag.
- **The plaintext-secret-in-order-meta finding (D-025)** — Bizum and GooglePay persist the real Redsys signing secret, unencrypted and permanently, to order postmeta. Documented in `docs/threat-model.md`; user was offered three fix approaches and chose to leave it documented and decide later rather than guess at a fix that could break the per-user test-mode feature it's load-bearing for. Review trigger: whenever the user is ready to decide. **This is the only remaining open finding from the full-plugin review** — all 7 others (D-024) were fixed in the same session (D-026).

Last updated: 2026-10-21 — sprint 5 opened (D-103) on `work/after-7.1.0`; 7.1.0 candidate unchanged on `develop`; the owner merges, tags and publishes.
