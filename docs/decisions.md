# Decisions — Payment Gateway for Redsys & WooCommerce Lite

> Append-only. A session NEVER re-opens a decision recorded here on its own initiative;
> only the user reverses a decision (append the reversal as a new entry).

## D-001 — Project type and security profile
- Date / phase: 2026-08-01 / Adoption
- Decision: Project type is WordPress plugin / WooCommerce extension. Security profile loaded: `references/security/wordpress.md`.
- Why: the codebase is a WooCommerce payment gateway plugin distributed on WordPress.org.
- Alternatives rejected (and why): n/a — unambiguous from the codebase.
- Supersedes: none

## D-002 — Stack, conventions, license (adopted as-is)
- Date / phase: 2026-08-01 / Adoption
- Decision: PHP plugin targeting `Requires PHP: 7.0` (readme.txt) / WordPress `Tested up to: 7.0` / WooCommerce `7.4`–`10.9`. No Composer dependency manager (hand-rolled signature logic). Front-end build via `@wordpress/scripts` + webpack, source at `resources/js/frontend/index.js` compiling to `assets/js/frontend/blocks.js`. Text domain `woo-redsys-gateway-light`.
- Why: this is what the code already does; conventions are observed, not imposed on adoption.
- Alternatives rejected (and why): n/a — de facto state.
- Supersedes: none

## D-003 — License reconciled to GPL-2.0-or-later
- Date / phase: 2026-08-01 / Adoption
- Decision: the single declared license is now **GPL-2.0-or-later** everywhere: `woocommerce-redsys.php` header, `package.json`, `readme.txt` (already correct), plus a new root `LICENSE` file with the official GNU GPLv2 text.
- Why: the three files previously disagreed (header said GPL-3.0, readme.txt said GPLv2-or-later, package.json said GPL-3.0+), and WordPress.org requires GPLv2-or-later compatibility. User chose GPL-2.0-or-later explicitly when asked.
- Alternatives rejected: keeping GPL-3.0 everywhere — rejected by the user in favor of matching WordPress.org's expectation and the already-correct readme.txt.
- Supersedes: none

## D-004 — Docs language: English
- Date / phase: 2026-08-01 / Adoption
- Decision: every `docs/` artifact Keel creates for this project is written in English (token economy default).
- Why: user confirmed the recommended default; the plugin's own code is already English-based with an `es_ES` translation, so this only affects Keel's internal documentation.
- Alternatives rejected: Spanish docs — user declined the extra token cost.
- Supersedes: none

## D-005 — No project website (Phase 8 not activated)
- Date / phase: 2026-08-01 / Adoption
- Decision: this project does not get its own Keel-managed website; Phase 8 is not run.
- Why: user has a general site (plugins.joseconti.com) and the WordPress.org plugin page already serves as the product page.
- Alternatives rejected: dedicated landing page — declined by user, may be revisited later (a later explicit request supersedes this).
- Supersedes: none

## D-006 — `docs/inespay-payment/` vendored reference material relocated
- Date / phase: 2026-08-01 / Adoption
- Decision: moved the full vendored third-party Inespay plugin copy from `docs/inespay-payment/` to `.reference/inespay-payment/` and added `.reference/` to `.gitignore` (untracked from git).
- Why: it is not part of the shipped plugin, was not `export-ignore`d, and would have shipped inside any git-archive distributable built from this repo — a packaging risk found during the adoption inventory. User confirmed it is reference-only material.
- Alternatives rejected: excluding it in place via `.gitattributes` only — user preferred it out of `docs/` entirely.
- Supersedes: none

## D-007 — Accessibility target: WCAG 2.2 AA
- Date / phase: 2026-08-01 / Adoption
- Decision: WCAG 2.2 AA is the floor for the plugin's admin settings screens and checkout UI contributions, with AAA where feasible — Keel's standard default.
- Why: user confirmed the recommended default.
- Alternatives rejected: none proposed.
- Supersedes: none

## D-008 — Single assistant: Claude Code only
- Date / phase: 2026-08-01 / Adoption
- Decision: no native assistant-config package is generated for other tools (Codex, Cursor, Copilot, Gemini CLI, Windsurf) — only the Claude Code container (`.claude/`) plus the dual portability lock (`CLAUDE.md` + `AGENTS.md`) apply.
- Why: user confirmed only Claude Code works on this repo.
- Alternatives rejected: multi-tool config package — not needed today; can be added later if other tools join.
- Supersedes: none

## D-009 — Design system: one-off / n/a
- Date / phase: 2026-08-01 / Adoption
- Decision: no dedicated design system exists or is planned — the plugin ships plain, hand-written CSS for its admin settings and checkout contributions (`assets/css/*.css`), no tokens or component library.
- Why: matches the as-built reality; a WooCommerce settings-panel plugin does not warrant a founding design system.
- Alternatives rejected: none — default accepted, not explicitly asked as a separate question given the project's small UI surface.
- Supersedes: none

## D-010 — Client budget: no
- Date / phase: 2026-08-01 / Adoption
- Decision: no client-facing budget is produced for this project (`Client budget: no`); estimates stay internal AI-time figures only.
- Why: this is the author's own product, not client-billed work. Default accepted.
- Alternatives rejected: none.
- Supersedes: none

## D-011 — Session-start setup batch answers (adoption)
- Date / phase: 2026-08-01 / Adoption
- Decision: Durability = git remote `origin` (GitHub) — satisfied. Autonomy = automatic (Keel does not ask before pushing to `develop`, never merges to `master`/tags without explicit instruction). Forge issues = after-sprint duty ON, sweep interval 24h. Issue capture = OFF (a problem the user reports in chat is not auto-opened as a public issue). Notifications = `PushNotification` tool (desktop + phone), confirmed delivering. Branches = `develop` created as the integration branch (this repo previously had only `master`).
- Why: user's explicit answers in this and the prior session; recorded here (and in the project card) so no future session re-asks. Machine-local mechanics (automatic mode file) also mirrored in `CLAUDE.local.md`.
- Alternatives rejected: n/a.
- Supersedes: none

## D-012 — Keel embedded skill and lock refreshed to v5.9.0
- Date / phase: 2026-07-31 → 2026-08-01 / prior session + Adoption
- Decision: `.claude/skills/keel/` replaced wholesale with v5.9.0 (verified file-for-file); `CLAUDE.md` and `AGENTS.md` lock blocks restamped to v5.9.0.
- Why: project's embedded copy was badly behind (v1.11.0); routine Keel maintenance.
- Alternatives rejected: none.
- Supersedes: none

## D-013 — End-user guide (`guide/`) declined for now
- Date / phase: 2026-08-01 / Adoption
- Decision: no `guide/` HTML guide is built; `readme.txt` remains the user-facing documentation.
- Why: user confirmed the recommended default — the plugin's settings/checkout surface is small enough that readme.txt covers it.
- Alternatives rejected: building `guide/` on keel-docs-theme now — can be added later (User guide: card line updated then).
- Supersedes: none

## D-014 — Phase 5 scaffold built during adoption, not deferred
- Date / phase: 2026-08-01 / Adoption
- Decision: built the full Phase 5 testability scaffold now (`.wp-env.json`, `docs/playground.md`, `scripts/keel-doctor`, `scripts/keel-verify`, `scripts/keel-handoff-verify`, `docs/sprints/`, `docs/05-test-points.md`) instead of deferring to the first real sprint.
- Why: user explicitly chose to build it now when asked, rather than at the first issue-fix sprint.
- Alternatives rejected: deferring to the first real sprint — was the recommended default, user chose otherwise.
- Supersedes: none

## D-015 — Chaining: start (full automatic chat chaining)
- Date / phase: 2026-08-01 / maintenance
- Decision: the project card's `Chaining:` value changes from `off` to `start` — a clean session close-out now opens a brand-new Claude Code CLI session automatically (via `osascript` driving Terminal.app on macOS) and submits the continuation prompt, with no human keystroke in between.
- Why: user explicitly asked ("Abre tú mismo el nuevo chat en modo automático"), after being told plainly this only works from the standalone CLI (not the VS Code extension or desktop app) and requires the `claude` binary on PATH. The warning Keel requires before this choice was given: it changes the tool (development moves to CLI sessions), and it means development advances with nobody watching between links.
- What this required: installing the standalone `claude` CLI to `~/.local/bin/claude` (it was not previously on this machine's PATH — neither the desktop app nor a VS Code extension expose it), confirmed added to `~/.zshrc`. Built and end-to-end verified in a scratch `/tmp` repo (never in this project's own working tree): the single-lane lock (claim / busy-detection against a genuinely live PID / orphan recovery from a dead PID / release restricted to the owning session), the launch receipt (atomic `mkdir`, keyed by the hand-off's own `Generated`+`Commit` identity so a regenerated hand-off gets a fresh launch and a re-run of the same one does not fire twice), and the real `osascript` fire — which genuinely opened a new Terminal.app window, ran `claude` in the correct directory, and submitted the prompt; the test window was closed immediately after confirming.
- Alternatives rejected: `prefill` (the safer middle ground, human presses Enter) — user asked for full `start` directly, was given the choice between installing the CLI, recording the decision without it working yet, or staying `off`, and chose to install the CLI and get the real thing.
- Supersedes: none — the earlier `off` (recorded as the un-asked default, never a formal D-entry) is superseded by this one.

## D-016 — First automated test coverage: `RedsysLiteAPI` unit tests, no WordPress bootstrap
- Date / phase: 2026-08-01 / Phase 5 (sprint)
- Decision: added PHPUnit 9.6 unit tests for `RedsysLiteAPI` (`tests/Unit/RedsysLiteAPITest.php`, `composer.json`, `phpunit.xml.dist`, `tests/bootstrap.php`) as true unit tests against the class in isolation — `wp_json_encode()` is stubbed in `tests/bootstrap.php` rather than booting a full WordPress core test suite (`WP_UnitTestCase`). Tests run inside the existing `wp-env` `cli` Docker container (the host has no local PHP/Composer), via `composer install` + `vendor/bin/phpunit`.
- Why: `RedsysLiteAPI` has no WordPress runtime dependency beyond that one function, so a full WP bootstrap (WP_TESTS_DIR, `wp-env run tests-cli`, a test database) would add real engineering and runtime cost for zero additional coverage on this class. This was the highest-risk untested code path per `docs/threat-model.md`, flagged as a deferred item in `docs/PROGRESS.md`.
- Alternatives rejected: full `WP_UnitTestCase` integration bootstrap against the `tests-wordpress`/`tests-cli` containers — the right choice once tests are written for code that DOES touch WordPress (hooks, `$wpdb`, the gateway classes), but unnecessary overhead for this specific class; revisit when the gateway classes themselves get test coverage.
- Supersedes: none

## D-017 — `vendor/` gitignored, `composer.lock` committed
- Date / phase: 2026-08-01 / Phase 5 (sprint)
- Decision: `vendor/` (Composer's downloaded dependencies) is gitignored; `composer.lock` is committed so every environment installs the exact same dependency versions.
- Why: standard PHP project hygiene — `vendor/` is a regenerable build artifact (`composer install`), not source; the lock file is what makes that regeneration reproducible.
- Alternatives rejected: committing `vendor/` — unnecessary repo bloat for a dev-only dependency (PHPUnit never ships in the plugin's production package).
- Supersedes: none

## D-018 — Integration tests for the gateway's IPN validation, via wp-env's own WP core test scaffold
- Date / phase: 2026-08-01 / Phase 5 (sprint)
- Decision: added a second PHPUnit suite, `tests/Integration/` (`phpunit-integration.xml.dist`, `tests/bootstrap-integration.php`), covering `WC_Gateway_redsys::check_ipn_request_is_valid()` as a real `WP_UnitTestCase` against a booted WordPress + WooCommerce. It runs inside `wp-env`'s `tests-cli` container, reusing the WordPress core PHPUnit scaffold already provisioned there (`WP_TESTS_DIR=/wordpress-phpunit`) — no separate `install-wp-tests.sh` step was needed. Added `yoast/phpunit-polyfills` as a dev dependency (required by the WP core test bootstrap).
- Why: `WC_Gateway_redsys` extends `WC_Payment_Gateway` and genuinely depends on WordPress/WooCommerce (hooks, options, `WC_Logger`), unlike `RedsysLiteAPI` (D-016) — a true unit test would have to fake too much of WooCommerce to be trustworthy. This automates the exact fail-closed scenario already driven manually in the playground (`docs/05-test-points.md`, "Adoption — playground verification" row) plus forged-signature, tampered-payload, and wrong-order-signature cases.
- Alternatives rejected: mocking `WC_Payment_Gateway`/WooCommerce instead of booting it for real — rejected because the class under test IS the integration with WooCommerce; mocking it away would test the mock, not the gateway.
- Supersedes: none

## D-019 — Bizum IPN tests use a real WC_Order fixture (order-number-mapping transient)
- Date / phase: 2026-08-01 / Phase 5 (sprint)
- Decision: added `tests/Integration/GatewayBizumIpnTest.php`, covering `WC_Gateway_Bizum_Redsys::check_ipn_request_is_valid()`. Unlike `WC_Gateway_redsys` (D-018), this method requires a real `WC_Order`: it maps the incoming `Ds_Order` back to a real order ID via `WCRedL()->clean_order_number()` (a `redys_order_temp_<Ds_Order>` transient) and calls `WCRedL()->get_order()`, which throws on a nonexistent order. The test fixture builder creates a real order with `wc_create_order()` and sets that transient directly, mirroring what `WCRedL()->prepare_order_number()` does for real outgoing payments.
- Why: L-003 (`docs/lessons-learned.md`) records that an earlier attempt copied `GatewayRedsysIpnTest.php`'s fixture assuming the same shape, and it errored (`Invalid order`) because Bizum's method is genuinely different. This decision records the correct approach found by reading the full method.
- Alternatives rejected: none — mutation-tested for real (bypassed the signature comparison, confirmed 3/5 Bizum-specific tests failed, reverted, confirmed 10/10 green again across both integration test classes).
- Supersedes: none

## D-020 — Fixed the GooglePay signature-bypass vulnerability: fail closed like Redsys/Bizum
- Date / phase: 2026-08-01 / Phase 5 (sprint)
- Decision: `classes/class-wc-gateway-googlepay-redirection-redsys.php`'s `check_ipn_request_is_valid()` now fails closed (returns `false`, logs "no SHA256 secret configured") when no SHA-256 secret is configured, exactly like `class-wc-gateway-redsys.php` and `class-wc-gateway-bizum-redsys.php` already do. Replaced the previous fallback, which accepted the notification whenever the (attacker-supplied) `Ds_MerchantCode` matched the gateway's own merchant code (`$this->customer`) — not a secret, sent in plaintext in every outgoing payment form. Added `tests/Integration/GatewayGooglePayIpnTest.php` (5 tests), including a regression test confirmed against the pre-fix code (reverted the fix temporarily, confirmed the regression test fails exactly as expected, restored the fix, confirmed 15/15 integration tests green again).
- Why: user explicitly chose "fix now (fail closed) and add its test" when the vulnerability was escalated, over a private GitHub security advisory or leaving it documented-only.
- Alternatives rejected: private GitHub security advisory first — user preferred fixing immediately; opening one now (or at the next release) remains available if the user wants coordinated disclosure later. Documenting only — rejected, the fix was straightforward and low-risk (mirrors an already-shipped pattern in two sibling classes).
- Supersedes: none — this is a bug fix, not a reversal of a prior decision. `docs/threat-model.md`'s "Known vulnerabilities" section is updated to record the fix.

## D-021 — Inespay IPN tests: own fixture builder, WPDieException, WooCommerce settings option for the protected api_key
- Date / phase: 2026-08-01 / Phase 5 (sprint)
- Decision: added `tests/Integration/GatewayInespayIpnTest.php` (5 tests) covering `WC_Gateway_Inespay_Redsys::handle_callback()`. Three things differ from the other three gateway test classes: (1) the signature algorithm is plain `base64( hash_hmac( 'sha256', dataReturn, api_key, false ) )` (hex HMAC, then base64 of the hex string) — unrelated to `RedsysLiteAPI`, so the fixture builder computes it directly; (2) `handle_callback()` calls `wp_die()` on every path instead of returning a boolean, caught in tests as `WPDieException` (WordPress core's own PHPUnit test scaffold turns `wp_die()` into this exception); (3) `api_key` is a `protected` property, set via `update_option( 'woocommerce_inespayredsys_settings', [...] )` before instantiating the gateway, since it can't be assigned directly from outside the class.
- Why: reading the full method (per the L-003 rule) showed it does not share the other gateways' shape at all — deliberately built its own fixture rather than forcing the Redsys/Bizum/GooglePay pattern onto it.
- Alternatives rejected: none. Mutation-tested for real (bypassed the `hash_equals()` signature check, confirmed 2/5 tests failed as expected, reverted, confirmed 20/20 integration + 8/8 unit tests green again). Found a pre-existing, out-of-scope issue while writing the "accepts a valid callback" test — `handle_callback()` writes WooCommerce's internal `_payment_method` meta key via the generic meta API instead of `set_payment_method()` — acknowledged with `setExpectedIncorrectUsage()` rather than silently fixed or hidden; recorded as L-004 (`docs/lessons-learned.md`) and a deferred item in `docs/PROGRESS.md`.
- Supersedes: none

## D-022 — Playwright checkout smoke test, with real playground setup gaps found and documented
- Date / phase: 2026-08-01 / Phase 5 (sprint)
- Decision: added `@playwright/test` as a devDependency, `playwright.config.js`, and `tests/e2e/checkout-redsys.spec.js` — a guest-checkout smoke test against the wp-env playground with the Redsys gateway, from product to the generated (correctly signed) Redsys payment form. Every request to `*.redsys.es` is intercepted and aborted, so the test never depends on Redsys's live infrastructure. `npm run test:e2e` runs it.
- Why: recommended in `docs/04-adoption-audit.md` as the remaining testability gap after the PHPUnit suites; user chose to add it before the next release rather than after.
- What it needed that the playground didn't already have: pretty permalinks (the playground starts with plain `?p=` links, so `/checkout/` 404s) and a configured, enabled Redsys gateway (none is configured by default). Both are now documented as a one-time setup step in `docs/playground.md` rather than scripted into `.wp-env.json`, since they change playground *state* (options, rewrite rules) rather than its *definition* — `npx wp-env clean all` reverts both, and the setup step says so.
- Alternatives rejected: none. Mutation-tested for real (corrupted the merchant code written into the payment form, confirmed the test failed on the expected assertion, reverted, confirmed it passed again, 3 consecutive clean runs).
- Supersedes: none

## D-023 — Fixed `successful_request()` verifying against the wrong secret in Bizum and GooglePay
- Date / phase: 2026-08-01 / Phase 5 (sprint, full-plugin review)
- Decision: at the user's request ("revisa el plugin completo a ver si ves algo que no esté bien"), ran a full-codebase review (4 parallel agents covering every class/file, findings independently re-verified by reading the actual code) and fixed the two findings rated "Alto"/High. This entry covers the first: `class-wc-gateway-bizum-redsys.php::successful_request()` and `class-wc-gateway-googlepay-redirection-redsys.php::successful_request()` both hardcoded `$usesecretsha256 = $this->secretsha256` — the live-mode settings secret only — instead of the test-mode-aware, per-order-override-aware resolution `check_ipn_request_is_valid()` already used correctly. Since `successful_request()` is fired via `valid_{$id}_standard_ipn_request` right after `check_ipn_request_is_valid()` already accepted the notification with the CORRECT secret, this second (redundant) verification would then fail on genuinely valid test-mode payments (or any per-order secret override), and the order would silently never be marked paid — no error, no order note, nothing for the merchant to see.
- Fix: extracted the exact secret-resolution logic already in `check_ipn_request_is_valid()` into a new private `resolve_notification_secret( $mi_obj )` method on each class, called by BOTH methods — so the two can no longer drift apart the way they already had. Added a regression test to each gateway's PHPUnit integration suite (`test_successful_request_completes_the_order_when_signed_with_the_test_mode_secret`), confirmed to fail against the pre-fix code (reverted temporarily, confirmed the failure, restored the fix) and to pass with it. 31 automated tests total now (8 unit + 22 integration + 1 e2e), all green.
- Why: user chose to fix the two "Alto" findings now rather than only document them; this one was mechanical and safe to fix immediately (a DRY extraction of already-correct, already-tested logic).
- Alternatives rejected: none.
- Supersedes: none

## D-024 — Full-plugin review findings: 7 more issues found, documented, deferred (not fixed this session)
- Date / phase: 2026-08-01 / Phase 5 (sprint, full-plugin review)
- Decision: the same full-codebase review (see D-023) surfaced 7 additional real findings beyond the two "Alto" ones. User chose to fix only the two Highs (D-023 and D-025) this session; these 7 are recorded here and in `docs/PROGRESS.md` Open items as deferred, not silently dropped:
  1. **Medium — `woocommerce-redsys.php:385`:** an unauthenticated, blocking `sleep(5)` runs on the order-received/thank-you page whenever a visitor supplies a valid order `key` + any non-empty `Ds_MerchantParameters`, before any signature check. A repeatable resource-exhaustion vector against anyone who has (or leaks) an order key, and it also delays every legitimate customer's return-from-Redsys page load by 5 seconds.
  2. **Medium — `class-wc-gateway-googlepay-redirection-redsys.php` `check_ipn_request_is_valid()`:** looks up a real `WC_Order` (`WCRedL()->get_order()`, throws on an invalid ID) from attacker-controlled `Ds_Order` BEFORE verifying the HMAC signature — an unauthenticated caller can probe order existence or trigger an uncaught-exception 500 with no valid signature at all. The equivalent code in Redsys/Bizum verifies the signature first.
  3. **Medium — `class-wc-gateway-redsys-global-lite.php:1085-1105` (`prepare_order_number()`/`clean_order_number()`):** the order-number-mapping transient (1h TTL) can outlive its window on a delayed/retried notification; the fallback path (`ltrim(substr($ordernumber,3),'0')`) assumes exactly 3 random prefix characters, but `wp_rand(1,999)` can produce 1–3 digits — roughly 1 in 5 times the fallback triggers, it resolves to the wrong order (or none).
  4. **Low — all HMAC gateways except Inespay:** signature comparisons use `===`/`!==` instead of `hash_equals()` (theoretical timing side-channel; Inespay already does this correctly).
  5. **Low-medium — `class-wc-gateway-inespay-redsys.php` `handle_callback()`:** a refund-confirmation callback can land in the same "payment completed" branch as a real payment (both key off `singlePayinId` + `codStatus` OK/SETTLED) — produces a misleading order note/meta and fires the wrong action hook. Not currently exploitable to un-refund an order (WooCommerce's own `payment_complete()` guard blocks that).
  6. **Low — `class-wc-gateway-inespay-redsys.php:201` (`disable_inespay()`):** casts the cart total to `(int)` before comparing to the configured transaction limit, truncating decimals — a €200.50 cart with a €200 limit incorrectly passes.
  7. **Low — `class-wc-gateway-inespay-redsys.php:652` (`process_refund()`):** `$amount ? $amount : $order->get_total()` treats an explicit refund amount of exactly `0` as "not given," silently refunding the full order total instead of nothing.
- Why: user explicitly scoped this session's fixes to the two Highs only; documenting the rest here satisfies "a deliberate omission is recorded, always" rather than letting real, found bugs silently vanish once the session moves on.
- Alternatives rejected: fixing all 9 now — user chose to scope down.
- Supersedes: none

## D-025 — Plaintext-secret-in-order-meta finding: documented, not fixed, decision deferred
- Date / phase: 2026-08-01 / Phase 5 (sprint, full-plugin review)
- Decision: the second "Alto" finding from the same review — `class-wc-gateway-bizum-redsys.php` and `class-wc-gateway-googlepay-redirection-redsys.php` persist the actual Redsys SHA-256 signing secret in plaintext, permanently, to order postmeta (`_redsys_secretsha256`) — is documented in `docs/threat-model.md` ("Known vulnerabilities") but NOT fixed this session. Three options were presented to the user (stop persisting it and accept the residual risk to any per-user test-mode notification arriving after the 1h transient expires; encrypt it before storing and decrypt on read; leave it documented only) and the user chose the third: leave it documented, decide later.
- Why: this meta write is load-bearing for a real feature (per-user test-mode secrets, `testforuser`/`testforuserid` settings) — removing it without understanding how often that combination is actually used in production could silently break delayed notification verification (refund confirmations, retried IPNs) for real merchants; the user wanted more time to think it through rather than have either fix guessed at during an already long session.
- Alternatives rejected: fixing it now with either approach above — deferred at the user's explicit request.
- Supersedes: none — this is the resolution of the "Blocked, awaiting the user" item `docs/PROGRESS.md` recorded earlier in this same session; it is now recorded as a deliberate deferral, not an open question.

## D-026 — Fixed all 7 remaining D-024 findings (3 medium, 4 low)
- Date / phase: 2026-08-01 / Phase 5 (sprint, full-plugin review)
- Decision: user asked to fix all 7 remaining findings from D-024 in this same session (having already decided to defer only D-025's plaintext-secret item). Each fix, in the order applied:
  1. **`prepare_order_number()`'s random prefix widened to always 3 digits** (`wp_rand(1,999)` → `wp_rand(100,999)`, `class-wc-gateway-redsys-global-lite.php`) — the root cause of the ~1-in-5 wrong-order-resolution bug; the fallback's "always strip 3 chars" assumption is now actually guaranteed. Regression test (`tests/Integration/GlobalLiteOrderNumberTest.php`) confirmed to fail against the pre-fix code across 3 separate runs (different random failures each time), reliably.
  2. **`resolve_notification_secret()` in Bizum and GooglePay now catches the exception `WCRedL()->get_order()` throws on a nonexistent order**, falling back to the guest/no-user secret instead of letting it surface as an uncaught 500 to an unauthenticated caller. Regression test per gateway, confirmed to fail (uncaught `Exception: Invalid order.`) against the pre-fix code.
  3. **The unauthenticated `sleep(5)` in `redsyslite_mark_order_as_paid()` (`woocommerce-redsys.php`) is now rate-limited** by a 30-second per-order transient guard, and an already-paid order exits before the sleep entirely (the common case on a repeat thank-you-page visit). Regression test (`tests/Integration/MarkOrderAsPaidRateLimitTest.php`) times actual calls — confirmed to fail (both calls took ~5s) against the pre-fix code.
  4. **Signature comparisons switched from `===`/`!==` to `hash_equals()`** in `class-wc-gateway-redsys.php`, `class-wc-gateway-bizum-redsys.php`, `class-wc-gateway-googlepay-redirection-redsys.php` (both `check_ipn_request_is_valid()` and `successful_request()` in each, 6 sites total). No new test — existing accept/reject tests already exercise both outcomes and pass unchanged; the timing-attack property itself isn't something a functional test can assert.
  5. **Inespay `handle_callback()` no longer re-processes an OK/SETTLED callback for an already-resolved order as a new payment** (`needs_payment()` guard added before the payment-completion branch) — avoids the misleading "payment completed" note and re-fired `inespay_post_payment_complete` hook on a refund confirmation or duplicate notification. Regression test confirmed to fail against the pre-fix code.
  6. **Inespay `disable_inespay()` compares the cart total and transaction limit as floats**, not `(int)`-truncated — a €200.50 cart no longer incorrectly passes a €200 limit. Verified by code review and `php -l`; NOT independently regression-tested (WooCommerce's `is_checkout()`/`WC()->cart` context is not straightforward to fake in `WP_UnitTestCase` without disproportionate scaffolding for a one-line fix — marked `VERIFY`, not claimed as test-covered).
  7. **Inespay `process_refund()` checks `null !== $amount`** instead of a falsy check, so an explicit `0` refund amount is sent as `0`, not silently upgraded to the full order total. Regression test (mocks `pre_http_request`, captures the actual API payload) confirmed to fail against the pre-fix code.
  Full suite after all 7 fixes: 40 automated tests (8 unit + 31 integration + 1 e2e), all green.
- Why: user explicitly asked to fix everything remaining in one pass rather than split it across sessions.
- Alternatives rejected: none — user chose "all at once."
- Supersedes: none. D-025 (the plaintext-secret finding) remains the one deliberately deferred item.

## D-027 — Bizum + Google Pay checkout e2e tests, and enabling multiple gateways surfaced a real selection-order dependency in the existing Redsys test
- Date / phase: 2026-08-01 / Phase 5 (sprint, closing testability gaps)
- Decision: added `tests/e2e/checkout-bizum.spec.js` and `tests/e2e/checkout-googlepay.spec.js`, mirroring `checkout-redsys.spec.js`'s structure — both gateways generate the same self-submitting `#redsys_payment_form` against the same `sis-t.redsys.es` sandbox, so only the radio selector, thank-you copy, and settings option name differ. Seeded `woocommerce_bizumredsys_settings` and `woocommerce_googlepayredirecredsys_settings` in the playground with the same published Redsys test-sandbox values already used for `redsys`.
- Two real issues surfaced while enabling all three gateways at once (not previously exercised, since only `redsys` was ever enabled before this sprint):
  1. **`checkout-redsys.spec.js` assumed Redsys was the only enabled gateway** (asserting the radio was already checked and hidden, WooCommerce's "single gateway" UI behavior). With 3 gateways enabled it's no longer auto-selected. Fixed by having every gateway's spec explicitly `.check()` its own radio — a more realistic assertion of real multi-gateway checkout behavior anyway, not a workaround.
  2. **Google Pay was invisible to a guest checkout out of the box in test mode.** `check_user_show_payment_method()` (`class-wc-gateway-googlepay-redirection-redsys.php:1548`) hides the gateway from any guest (`$userid === false`) whenever `testmode === 'yes'` and the `testshowgateway` allowlist option is unset — a deliberate feature (don't show a live-test-mode gateway to real anonymous customers) that Bizum does not have. Not a bug; the playground setup now seeds `testshowgateway: [""]`, the value that satisfies the class's own guest-visible branch, documented in `docs/playground.md` with what it does and why it's needed only for this gateway.
- Mutation-tested both the same way D-022 did: corrupted `DS_MERCHANT_MERCHANTCODE` in each gateway's form-generation code, confirmed the corresponding test failed on the expected assertion, reverted (`git diff` confirmed byte-identical), confirmed both green again.
- Why: user chose to close all remaining testability gaps recorded in `docs/PROGRESS.md` before the next release.
- Alternatives rejected: setting Google Pay's `testmode` to `no` to sidestep the guest-visibility check — rejected because it would also switch the form to the LIVE Redsys URL/secret, breaking the test's own sandbox assertions and testing the wrong code path.
- Supersedes: none.

## D-028 — WooCommerce Blocks checkout e2e test (Redsys), proving the existing Blocks integration actually works end to end
- Date / phase: 2026-08-01 / Phase 5 (sprint, closing testability gaps)
- Decision: created a second WordPress page (`Checkout Blocks`, slug `checkout-blocks`, using the `woocommerce/checkout` block) in the playground, and added `tests/e2e/checkout-blocks-redsys.spec.js` — a guest checkout through it with the Redsys gateway, asserting the payment method renders (via `resources/js/frontend/index.js`'s compiled `assets/js/frontend/blocks.js` bundle and `includes/blocks/class-wc-gateway-redsys-lite-support.php`), is selectable, and produces the same signed `#redsys_payment_form` on the order-pay page as the classic checkout. This is the FIRST test of any kind to exercise the Blocks integration — it existed, wired up, since before this adoption, completely untested.
- Field locators use `page.getByLabel(...)`/`getByRole(...)` rather than `#id` selectors, unlike the classic-checkout specs — the Blocks checkout is a React app whose field IDs are implementation details of `@woocommerce/blocks-checkout`, not a stable contract the way the classic checkout's server-rendered form IDs are; label/role text is what WooCommerce commits to keeping stable.
- Mutation-tested the same way as D-022/D-027: corrupted `DS_MERCHANT_MERCHANTCODE` in `class-wc-gateway-redsys.php` (the same code path the classic-checkout test already covers — this test proves the Blocks *route into* that code works, not new signature logic), confirmed the test failed, reverted, confirmed green again. This mutation test surfaced a real playground-tooling gotcha, not a plugin bug — see L-005 (`docs/lessons-learned.md`): opcache in the persistent `wordpress` container cached the corrupted bytecode across the revert, requiring a container restart, not just a file revert, to get a trustworthy green run. Recorded so it isn't re-diagnosed from scratch next time.
- Why: user chose to close the "WooCommerce Blocks checkout has zero coverage" gap recorded in `docs/PROGRESS.md`. One gateway (Redsys) is representative coverage of the Blocks *integration path itself* (registration → render → order creation) — the other three gateways share the same `AbstractPaymentMethodType` pattern and already have their payment/signature logic covered via the classic-checkout specs (D-027); a Blocks-specific test per gateway would mostly re-prove the same registration wiring four times.
- Alternatives rejected: a Jest unit test of `resources/js/frontend/index.js` in isolation — rejected in the same session, recorded separately as D-031, since this e2e test already exercises the real compiled bundle end to end.
- Supersedes: none.

## D-029 — Inespay checkout e2e test, via a dev-only `pre_http_request` stub (not `page.route()`)
- Date / phase: 2026-08-01 / Phase 5 (sprint, closing testability gaps)
- Decision: Inespay's `process_payment()` (`classes/class-wc-gateway-inespay-redsys.php:354`) makes a real SERVER-SIDE `wp_remote_post()` to `apiflow.inespay.com` during checkout — unlike the other three gateways, which just redirect the browser to a self-submitting Redsys form. Playwright's `page.route()` only intercepts requests the BROWSER makes, so it cannot intercept this. Added `.wp-env-mu-plugins/inespay-http-stub.php` (a new `mappings` entry in `.wp-env.json`, `"wp-content/mu-plugins": "./.wp-env-mu-plugins"`) that hooks `pre_http_request` and, ONLY when the WP option `redsyslite_e2e_stub_inespay` is `'yes'` (off by default), short-circuits any request to `apiflow.inespay.com` with a fabricated `singlePayinLink`/`singlePayinId` JSON response — Inespay's real request-building and redirect-handling code runs completely unmodified; only the outbound network call is faked. The fabricated `singlePayinLink` points back at this site itself (`home_url('/?inespay_e2e_return=1')`) rather than a fake external domain, so the test's final assertion (the browser actually lands there) needs no further mocking. Added `tests/e2e/checkout-inespay.spec.js`.
- This mu-plugin is dev/test infrastructure ONLY — mapped via `.wp-env.json`, never bundled into the shipped plugin, in the same category as `tests/` or `playwright.config.js`; inert on any real site since the gating option is never set to `'yes'` outside this playground.
- Inespay is also restricted to `is_allowed_country()` (ES/PT/IT, `class-wc-gateway-inespay-redsys.php:161`), which falls back to the store's base location when no billing/shipping country is set yet — this playground's base location is `US`, so the test selects Spain as the billing country (triggering WooCommerce's checkout AJAX refresh) before the gateway becomes selectable, rather than needing to change the playground's base location for every other test.
- Mutation-tested by corrupting the stub's own response shape (renamed the `singlePayinLink` key so `process_payment()`'s `empty($body['singlePayinLink'])` check fails closed) — confirmed the test failed (timed out waiting for the redirect, since Inespay correctly refuses to redirect on a malformed API response), reverted, restarted the `wordpress` container per L-005, confirmed green again.
- Why: user chose to close the "the other three gateways' checkout flows" gap recorded in `docs/PROGRESS.md`; Inespay needed a materially different technique than the other two (D-027), documented here rather than forced into the same `page.route()` shape.
- Alternatives rejected: mocking at the Playwright/browser level only — impossible, since the request never reaches the browser; skipping Inespay's checkout flow entirely — rejected, since the assistant-drives-every-test-it-can-drive rule (Keel `SKILL.md`) means a real, if unconventional, technique should be tried before delegating this to the user.
- Supersedes: none.

## D-030 — `disable_inespay()` fractional-total coverage, and a first draft that couldn't have caught its own bug
- Date / phase: 2026-08-01 / Phase 5 (sprint, closing testability gaps)
- Decision: added `tests/e2e/inespay-transaction-limit.spec.js`, closing the D-026 fix #6 review trigger ("if a Playwright checkout test for a fractional-total cart is ever added"). Created a product ("E2E Transaction Limit Product", slug `e2e-transaction-limit-product`, €200.50) in the playground specifically for this test (D-026's float-vs-`(int)` bug only manifests on a genuinely fractional total), added to the cart via its product page rather than a hardcoded numeric post ID (a fresh `wp-env clean all` install won't necessarily reassign the same ID), configured Inespay's `transactionlimit` to `200`. Two cases: the €200.50 cart must NOT offer Inespay; a €10 cart (existing product #10, whose ID this playground already relies on elsewhere) must still offer it.
- **The first draft of this test could not have caught the bug it was written to catch — mutation-testing (per this project's own standing discipline) is what surfaced it, not review.** The initial version asserted `#payment_method_inespayredsys` has count 0 immediately after selecting the billing country, with no wait for WooCommerce's `wc-ajax=update_order_review` fragment-refresh AJAX call that actually re-evaluates `disable_inespay()`. Since Inespay isn't available at initial page load EITHER (this playground's store base location is `US`, outside `is_allowed_country()`'s `ES`/`PT`/`IT`), `toHaveCount(0)` was already trivially true before the AJAX call even fired — reverting the fix to the pre-fix `(int)` cast and restarting the `wordpress` container (L-005) still showed the test passing. Fixed by explicitly `page.waitForResponse()`-ing the specific `update_order_review` call triggered by the country selection before asserting on the gateway list. Re-ran the same mutation (revert to `(int)`, restart container) and confirmed the fixed test now fails as expected; reverted back to the float fix, confirmed both new test cases green again, confirmed the full 7-test e2e suite green.
- Why: user chose to close this specific, already-named review trigger from D-026.
- Alternatives rejected: a fixed `page.waitForTimeout()` sleep instead of waiting for the specific AJAX response — rejected as inherently flaky (too short: same race; too long: slow, still not a real guarantee) versus waiting for the actual network response that the assertion's correctness depends on.
- Supersedes: none. This is also a concrete instance of why this project's mutation-testing discipline (D-018 onward) is load-bearing, not ceremonial: a test that never fails against broken code isn't real coverage, and this one initially wasn't.

## D-031 — Recommended against adding JS unit-test tooling (Jest) from scratch for `resources/js/frontend/index.js`
- Date / phase: 2026-08-01 / Phase 5 (sprint, closing testability gaps)
- Decision: did NOT add `jest`/`@wordpress/scripts test-unit-js` or any `__tests__` suite. `resources/js/frontend/index.js` (the WooCommerce Blocks payment-method registration source) is pure declarative `registerPaymentMethod()` config — four near-identical objects built from `getSetting()` lookups and `decodeEntities(...) || default` fallbacks, with no independent branching logic, state, or error handling of its own. D-028's `checkout-blocks-redsys.spec.js` already exercises the REAL compiled bundle (`assets/js/frontend/blocks.js`, built by `npm run build` from this exact source) end to end — registration, rendering, selection, and order creation — which is stronger coverage of this file's actual behavior than a Jest test asserting on the same objects in isolation would be.
- This closes the last of the four testability gaps recorded in `docs/PROGRESS.md` ("No `jest.config.*` or JS test suite exists yet") — closed by a documented decision not to build it, not left silently unaddressed.
- Why: user chose to close all remaining testability gaps; for this one specifically, the cost (new test runner, new devDependencies, new CI-equivalent step to keep green) is disproportionate to catching bugs in code this thin, and the e2e route already proves it works for real.
- Alternatives rejected: adding `@wordpress/scripts test-unit-js` (Jest is bundled with the `@wordpress/scripts` devDependency already in `package.json`, so the marginal setup cost is low) — still rejected, since low setup cost doesn't change that there's near-zero independent logic in this file to unit-test.
- Supersedes: none. Review trigger (also recorded in `docs/PROGRESS.md`): revisit if `resources/js/frontend/index.js` grows real conditional logic (e.g. a `canMakePayment()` beyond the current `() => true`).
