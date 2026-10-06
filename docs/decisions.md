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
- Not checked: whether wp-env can route the container's outbound traffic through a recording proxy (which would intercept the server-side request without a mu-plugin), and whether Inespay offers a sandbox endpoint that accepts test credentials — neither was examined; the claim is only that browser-level interception cannot see a request the browser never makes.
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

## D-032 — Added an `== Unreleased ==` changelog section to `readme.txt` documenting this session's unreleased fixes
- Date / phase: 2026-08-01 / Phase 5 (post-sprint documentation, at the user's request "apunta todo en el readme.txt")
- Decision: added an `== Unreleased ==` section at the top of `readme.txt`'s `== Changelog ==`, above `== 7.0.2 ==`, listing the 9 real bug fixes from the full-plugin review (D-020, D-023, D-026) and the Issue #93 fix (E-001) in user-facing language, following the existing `* Security Fix: ...` / `* Fix: ...` style. Did NOT bump `Stable tag:`, the plugin header `Version:`, the `REDSYS_WOOCOMMERCE_VERSION` constant, or add a numbered version heading — per Keel's version-change policy, none of those move without the user's explicit instruction in the conversation, and none was given here.
- Deliberately did NOT mention D-025 (the plaintext-secret-in-order-meta finding, still open) — it is an unpatched, undisclosed issue; naming it in a public, WordPress.org-hosted changelog before a fix ships would advertise the weakness to anyone reading the plugin's public page.
- Why: the user asked to have everything documented in `readme.txt`, right after this session's sprint closed with real, unreleased fixes recorded only in `docs/decisions.md`/`docs/PROGRESS.md` (internal, not user-facing).
- Alternatives rejected: waiting until the actual release/version-bump to write the changelog entry — rejected since the user asked for it now, and an `Unreleased` section is a normal, reversible way to keep the changelog current without pre-committing to a version number.
- Supersedes: none. When a release is authorized, the `== Unreleased ==` heading becomes the new version's heading (e.g. `== 7.0.3 ==`) as part of that Phase 7 work — this entry's content does not need to be rewritten, only re-headed.

## D-033 — Fixed the "1 billion bug": high-order digits of 10+ digit order IDs lost by prepare_order_number(), with an insufficiently-covered refund path
- Date / phase: 2026-08-02 / Phase 5 (sprint, follow-up to D-026)
- Decision: a real user support ticket ("ID del pedido no válido en la llamada a read_multiple()", intermittent 500s on Redsys/refund notifications) led to re-examining `prepare_order_number()`/`clean_order_number()` beyond what D-026's fix #1 covered. Found and fixed a second, deeper bug in the same functions, ported from the equivalent fix already shipped in the premium plugin (`woocommerce-gateway-redsys` v31.0.3, changelog 2026.06.27, itself building on an earlier incomplete "1 billion" fix from v15.2.0 2021.06.19):
  1. **Root cause**, distinct from D-026 #1: `prepare_order_number()` (`class-wc-gateway-redsys-global-lite.php`) only reserves 9 digits for the real order ID inside the 12-character Ds_Order (`str_pad( $order_id, 12, ... )` then `substr_replace( ..., 0, -9 )`). For any `$order_id` of 10+ digits (≥ 1,000,000,000 — "the 1 billion bug"), the high-order digits are overwritten by the random 3-digit prefix and are gone from the Ds_Order string itself, before the transient even enters the picture — D-026's fix (forcing the prefix to always be 3 digits) made the loss consistent but did not stop it from happening. `clean_order_number()`'s legacy `substr($ordernumber,3)`/`ltrim` fallback can never reconstruct those digits once the 1h transient mapping expires.
  2. **Fix**: `clean_order_number()` now tries a new reverse lookup, `get_order_id_by_redsys_order_number()`, via `wc_get_orders( ['meta_query' => [['key' => '_payment_order_number_redsys', 'value' => $ordernumber]]] )`, BEFORE falling back to the lossy substr/ltrim heuristic. `_payment_order_number_redsys` is postmeta already written at payment time by the Redsys, Bizum and Google Pay gateways (confirmed by reading `class-wc-gateway-redsys.php:1033`, `class-wc-gateway-bizum-redsys.php:1289`, `class-wc-gateway-googlepay-redirection-redsys.php:1099` — Inespay does not use `clean_order_number()` to match orders, it matches by its own `singlePayinId`, so it needed no change, matching the premium's own changelog note).
  3. **Refund path was even more exposed than payments**: `process_refund()` in all three affected gateways reuses the SAME Ds_Order stored at payment time (`_payment_order_number_redsys`) without ever renewing the `redys_order_temp_*` transient, and a refund is typically requested well past its 1h TTL — meaning the refund confirmation notification hit the lossy fallback path almost every time, not just occasionally like payments. Fixed by re-saving the transient with a 24h TTL (`DAY_IN_SECONDS`) in `process_refund()`, right before calling `ask_for_refund()`, in `class-wc-gateway-redsys.php`, `class-wc-gateway-bizum-redsys.php`, and `class-wc-gateway-googlepay-redirection-redsys.php` — matching the premium's `ask_for_refund()` re-save, adapted to this plugin's per-gateway (not centralized) refund methods.
  4. **Regression tests**: `tests/Integration/GlobalLiteOrderNumberTest.php` gained two new tests (large-order-ID meta-lookup recovery; legacy-heuristic-still-used-as-last-resort). New file `tests/Integration/RefundOrderNumberTransientTest.php` covers all three gateways' `process_refund()` transient renewal (HTTP stubbed via `pre_http_request` to fail fast, avoiding the `check_redsys_refund()` sleep-loop). All 4 new tests mutation-tested for real: reverted the 4 source files to pre-fix (`git diff`/`git apply`, not hand-editing), confirmed all 4 new tests fail as expected against the old code (300 assertions → the 4 targeted ones fail with the exact old lossy values), restored the fix, confirmed 36/36 green again (was 31/31 before this session). Full unit suite (8/8) also re-run, unaffected.
  5. **`readme.txt`**: added one `== Unreleased ==` entry in user-facing language, following D-032's established pattern; no version bump (per Keel's version-change policy).
- Why: user reported this as an unresolved customer ticket and asked to verify against the light plugin's actual current code (not from memory) — the light's own full-plugin review (D-024) had already found and fixed the RELATED "1-in-5" bug (D-026 #1) but had not found this deeper one, because no test in that review used an order ID large enough to exercise it. The premium plugin (`woocommerce-gateway-redsys`) already had this exact class of bug found and fixed in production, so the fix was ported rather than re-derived from scratch, following the same reverse-lookup + refund-TTL-renewal shape.
- Alternatives rejected: changing `prepare_order_number()` to reserve more digits for the order ID (e.g. widen the 12-character format) — rejected because it would change the Ds_Order format sent to Redsys for ALL orders (a live, external-facing contract with Redsys's own systems), whereas the meta-lookup fallback fixes recoverability without touching what is actually sent over the wire; matches the premium's own choice not to touch the generation side either.
- Supersedes: none. Extends, does not reopen, D-026 #1 (which remains correct and necessary — it fixed a separate, more frequent bug on the same functions).

## D-034 — Keel v6.5.0 installed in the repository: embedded trees replaced, lock block refreshed; reconciliation left pending
- Date / phase: 2026-10-06 / maintenance of the Keel scaffolding (no product code touched)
- Decision: on the user's explicit instruction to install Keel, after a machine reinstall, the session-start update check found the user-level install at v6.5.0 (the latest release tag) and both embedded trees at v5.9.0. Both `.claude/skills/keel/` and `.agents/skills/keel/` were replaced with the whole installed tree and verified file-for-file against it (42 files each, none zero-byte, the two trees identical to each other). The lock block between the `KEEL:BEGIN`/`KEEL:END` delimiters was rewritten from the canonical copy and restamped v6.5.0 in `CLAUDE.md` and `AGENTS.md`; nothing outside the delimiters was touched.
- Why: an embedded copy behind the running skill binds other tools and future sessions to an obsolete workflow. The `.agents/` tree had additionally diverged from the `.claude/` tree (tool names rewritten by hand inside `SKILL.md`), which the portability rule forbids — the trees are byte-identical and never hand-edited.
- Consequence: `Keel baseline:` deliberately stays v5.9.0. A skill update never advances it; only a completed post-update reconciliation does. It is recorded as pending in `docs/PROGRESS.md` open items.
- Alternatives rejected: advancing the baseline together with the copy — rejected because it would hide exactly the gap the reconciliation exists to close.
- Supersedes: none.

## D-035 — Competitive scan declined for the v5.9.0 → v6.5.0 reconciliation
- Date / phase: 2026-10-06 / post-update reconciliation
- Decision: the user declined, explicitly, every reconciliation row that concerns the competition — the competitive scan, `docs/00-competitive-landscape.md`, and the confrontation of the product's scope against competitor functionality. None of them is produced.
- Why: the user's call ("no hace falta mirar competencia"). The plugin is already released and in production as the Lite edition of the user's own commercial plugin; its scope is set by that relationship, not by a market scan.
- Consequence: those rows are `declined` (this entry) in `docs/keel-conformance.md`, never `missing`. Re-offered only if the user asks.
- Supersedes: none.

## D-036 — Test-first policy: pure-logic
- Date / phase: 2026-10-06 / post-update reconciliation (question introduced by Keel v5.11.0, never asked here)
- Decision: the user chose `pure-logic`, Keel's default. Pure functions of their inputs — signature computation and verification, order-number preparation and recovery, amount handling, validators — get their test written and seen failing before their code. Not retroactive.
- Holds at every value regardless: a bug fix starts from a test that reproduces the bug and fails, and a test derived from an `AC-nn` or a reproduced bug is never edited to make it pass.
- Alternatives rejected: `pure-logic + acceptance` (slower, not chosen); `none`.
- Supersedes: none.

## D-037 — Chaining model: opus
- Date / phase: 2026-10-06 / post-update reconciliation (question introduced by Keel v5.13.0, never asked here)
- Decision: every chat that the close-out chains launches with the model `opus`, passed on the launch line. Asked, not inferred.
- Why: long unattended stretches on payment code; the user's explicit choice over `sonnet` and `fable`.
- Supersedes: none. Extends D-015 (`Chaining: start`), which stands.

## D-038 — Front-end minification scheduled as its own slice (S-027), not applied inside the reconciliation
- Date / phase: 2026-10-06 / post-update reconciliation
- Decision: Keel's build-assets contract (source plus minified pair, minified served unless `SCRIPT_DEBUG`) is NOT met today and is not applied by the reconciliation. The user scheduled it as slice S-027 in sprint 3, before the next release.
- Why: applying it changes enqueue code that reaches production stores; it deserves its own slice with its own verification rather than riding a scaffolding change.
- Consequence: the conformance row stays `missing`, scheduled (S-027), by this decision. It is neither declined nor forgotten.
- Supersedes: none.

## D-039 — Native Claude Code config package accepted in full
- Date / phase: 2026-10-06 / post-update reconciliation
- Decision: the question D-008 never explicitly put — whether the project carries Keel's native assistant config for its one accepted tool — was asked and the user accepted the full package for Claude Code: path-scoped rules (`.claude/rules/`), reviewer and security-auditor subagents (`.claude/agents/`), the confidential-data pre-commit gate (`.githooks/pre-commit`) and a committed permission allow-list (`.claude/settings.json`), the last one confirmed by the user separately before it is written.
- Why: a payment gateway handles merchant signing secrets; a mechanical gate against committing them is cheap, and reviewers bound to the project's own recorded decisions keep later sessions from re-deriving them.
- Not included: forge CI workflows and MCP registration were not part of what the user accepted; `CI runs on: n/a`.
- Supersedes: the card's `Assistant config: none`. Extends D-008 (Claude Code remains the only accepted tool).

## D-040 — Sprint plan created; Security audit derived as required
- Date / phase: 2026-10-06 / post-update reconciliation
- Decision: (1) `docs/sprints/` now holds the plan Keel v5.21.0 requires in every phase: sprint 1 backfills the work done before the plan existed (hours estimated, labelled as such), sprint 2 is this reconciliation, sprint 3 is the release preparation built from the open items, and `docs/sprints/deferred.md` holds D-025 and the L-004 follow-up. `Sprints: on` and `Push test scope: affected` are defaults written on the card, not questions. (2) `Security audit: required` is derived, not asked: the plugin moves money and exposes IPN/callback endpoints reachable from outside. The next release gate therefore needs an active audit covering the candidate (scheduled as S-028) or a decision entry declining it.
- Why: (1) the instruction in D-014's scaffold — do not invent a sprint file until one is planned with the user — was correct under v5.9.0; the user ordered the full reconciliation, which is that planning. (2) per `references/security-audit.md`.
- Supersedes: none. Extends D-014.

## D-041 — Candidate defects found while backfilling the documentation are scheduled (S-035), not fixed inside the reconciliation
- Date / phase: 2026-10-06 / post-update reconciliation, slice S-025
- Decision: reading the source to write `docs/flows/` and `docs/reference/` surfaced behaviour that contradicts the documentation or repeats a bug class already fixed elsewhere. None was reproduced and none was fixed: the reconciliation changes no product code. They are slice S-035 in sprint 3, a dependency of the release gate, and each starts from a failing reproduction test (D-036).
- The list, as read from code (unverified until reproduced):
  1. `woocommerce-redsys.php:433-436` — the order-received fallback passes only the merchant parameters and the signature; `classes/class-wc-gateway-googlepay-redirection-redsys.php:941` calls `wp_die()` when the signature version is missing, which would kill the thank-you page of an unpaid Google Pay order.
  2. `classes/class-wc-gateway-googlepay-redirection-redsys.php` `successful_request()` has no already-paid guard, unlike the card gateway (`:1012`) and Bizum (`:1258`): a duplicate notification is reprocessed.
  3. A refund amount of `0` refunds the full total — `classes/class-wc-gateway-redsys.php:1276`, `classes/class-wc-gateway-bizum-redsys.php:1622`, `classes/class-wc-gateway-googlepay-redirection-redsys.php:1453`. The bug class D-026 fixed for Inespay only.
  4. `classes/class-wc-gateway-bizum-redsys.php:637-638` — the transaction limit truncates with integer casts (the class D-026/D-030 fixed for Inespay) and hides Bizum at a total equal to the limit.
  5. Debug logging writes the signing secret in clear — `classes/class-wc-gateway-bizum-redsys.php:1203-1204`, `classes/class-wc-gateway-googlepay-redirection-redsys.php:1023-1024`. Not covered by D-025, which concerns order meta only; to be added to `docs/threat-model.md` in S-035.
  6. `classes/class-wc-gateway-googlepay-redirection-redsys.php:1554` reads `testshowgateway`, which has no settings field: on a store configured through the UI the gateway is hidden for everyone in test mode. `:1195` tests `$this->orderdo`, never assigned.
  7. Lower severity, recorded for S-035 triage: Inespay is absent from `includes/data/redsys-types.php` (no admin details block, no thank-you text); `on-hold` counts as paid in `includes/data/redsys-status-paid.php`; Bizum and Google Pay refunds post to the redirection URL while the card gateway uses the REST URL; `wpml-config.xml` registers a settings key for a gateway that does not exist.
- Documentation found stale and not owned by S-025: `docs/architecture.md` (claims no automated suite; describes the notification as a browser redirect; says a match marks the order `completed`), and `docs/02-functional-spec.md` F2's claim about issue #10. Corrected in S-026.
- Why not now: the user ordered a reconciliation of the Keel scaffolding. Fixing payment code on an unreproduced reading, inside a scaffolding change, is exactly the scope widening "When to stop and ask" forbids for anything that looks like a security problem.
- Supersedes: none.

## D-042 — The Redsys generic test signing key is public and may stay in the repository; the gate recognises it by hash
- Date / phase: 2026-10-06 / post-update reconciliation, slice S-024
- Decision: the new confidential-data gate (`.githooks/pre-commit`) flagged one value in three tracked files — `classes/class-wc-gateway-bizum-redsys.php:288`, `classes/class-wc-gateway-googlepay-redirection-redsys.php:201` and `docs/playground.md`. The user confirmed it is the generic demonstration signing key Redsys publishes for its test merchant (the one paired with merchant code 999008881), not a credential of any real merchant. It stays where it is. The gate carries the SHA-256 of that exact value and drops a match only when the matched token hashes to it; the value itself is not written into the gate, and any other key still blocks the commit.
- Why: the value is already in the released plugin and in pushed history, and it is public by design; blocking every commit that touches those files, or bypassing the gate each time, would train the habit of bypassing it.
- Verified: the whole-tree scan (`.githooks/pre-commit --scan-tree`) reported three hits before the exception and none after it.
- Alternatives rejected: removing the key from `docs/playground.md` only; leaving the gate strict and bypassing it per commit.
- Not the same thing as D-025 or D-041 item 5, which concern a MERCHANT's real secret persisted to order meta or written to debug logs. Those stay open.
- Supersedes: none.

## D-043 — Committed Claude Code settings confirmed; subagent model map
- Date / phase: 2026-10-06 / post-update reconciliation, slice S-024
- Decision: (1) the user confirmed the committed `.claude/settings.json`: an allow-list limited to starting and stopping the playground, running the three test suites, `npm run build`, the read-and-verify `scripts/keel-*` commands, the tree scan of the gate and edits under `tests/`, plus the registration of `scripts/keel-stop-hook` as the `Stop` hook. Deliberately absent: `keel-doctor --fix`, the playground reset, any `gh` command, any git write. (2) The machine-local `.claude/settings.local.json` `env.PATH` gained `/usr/sbin`, `/sbin` and the nvm Node directory, all written as literal absolute paths. (3) Subagents use Keel's default map — reviewer and security auditor on `sonnet`, the mechanical agents (docs verifier, playground QA, test driver, accessibility auditor) on `haiku` — chosen by the user over `opus / sonnet` and all-`opus`.
- Why: committed permissions bind everyone who opens the repository, so they are confirmed by name; the PATH lacked the system directories the chain check requires and the Node the test tooling needs.
- Supersedes: none. Completes D-039.

## D-044 — The playground does not build today; restoring it is slice S-036 and the test-dependent slices wait on it
- Date / phase: 2026-10-06 / post-update reconciliation
- Decision: the automated suites could NOT be run during the reconciliation. `npx wp-env start` fails on the pinned PHP 7.4 image (Debian bullseye package fetches return 404), and a diagnostic PHP 8.1 override got further but did not yield a working site (L-007). The diagnostic override was removed and the half-started environment stopped; `.wp-env.json` is unchanged. Restoring the playground is slice S-036, and S-027, S-029, S-032 and S-035 depend on it.
- Consequence, stated plainly: nothing in this reconciliation was verified by running the plugin's test suites. The reconciliation changes no product code; its one edit to a test-runner file (`playwright.config.js`, the worker count read from `PW_WORKERS` with the same default of 1) is unexercised.
- Why not fix it now: the realistic fix moves the playground off PHP 7.4, which reverses a recorded pin and changes what "tested on" means for the next release. That is a decision, not a repair.
- Not checked: whether an older `@wordpress/env` release, a pinned image digest, or pointing apt at Debian's archive mirror would let the PHP 7.4 image build unchanged; none of the three was tried.
- Supersedes: none.

## D-045 — Acceptance criteria were backfilled as-built: the 26 with no automated proof carry no test-point row until a slice drives them
- Date / phase: 2026-10-06 / post-update reconciliation, slices S-025 and S-026
- Decision: `docs/02-functional-spec.md` now carries `AC-01` to `AC-57`, written from the code as it is (adoption, as-built). 31 are proven by a named existing test and are bound (29 to the rows of the slices that wrote those tests, and AC-05 and AC-51 to a row backfilled for the D-033 slice) to the test-point rows of the slices that wrote those tests. The other 26 are recorded in the spec as `as-built, unverified`; they get NO row in `docs/05-test-points.md` now, because a row is evidence of something driven and nothing was driven. Each gets its row in the slice that first drives it. `scripts/keel-verify` reads the list from this entry and reports those ids instead of failing on them; an id that is neither bound to a row nor listed here still fails.
- Unverified ids: AC-09, AC-10, AC-11, AC-12, AC-15, AC-16, AC-23, AC-31, AC-32, AC-35, AC-41, AC-42, AC-43, AC-46, AC-47, AC-48, AC-49, AC-50, AC-52, AC-54, AC-55, AC-56, AC-57.
- Removed from the list on 2026-10-06 (S-035, D-052): AC-24 and AC-33, now bound to tests.
- Why: this is adoption's progressive-backfill rule applied to criteria. Inventing 26 rows, or tagging them as delegated to the user, would turn an honest gap into false evidence. Several of these ids are exactly where D-041's candidate defects sit (the card gateway past the signature check, Google Pay's duplicate handling, the refund paths), so S-035 is expected to bind a good part of them.
- Consequence: the gap is visible and counted, not hidden — the release gate (S-032) reads this list.
- Supersedes: none.

## D-046 — keel-verify enforces newest-first order in the readme.txt changelog
- Date / phase: 2026-10-06 / post-update reconciliation, slice S-026
- Decision: the regenerated `scripts/keel-verify` checks that `readme.txt`'s changelog runs newest entry first, with `== Unreleased ==` allowed only at the top. Keel's phase reference words the check as oldest to newest; this project keeps the WordPress.org convention, which is what the existing version-touchpoint check ("top changelog entry") already relied on.
- Why: reversing a released plugin's changelog to satisfy a checker would be the code adapting to the tool.
- Supersedes: none.

## D-047 — The reconciliation commits are pushed past the pre-push hook, once, with the user's approval
- Date / phase: 2026-10-06 / post-update reconciliation
- Decision: the commits of this reconciliation are pushed to `develop` with the pre-push hook bypassed. The user approved it explicitly, for these commits, after being told why: the hook's selection for them is the entire suite (they touch `playwright.config.js` and the selector itself), and the suite cannot run because the playground does not build (D-044).
- Not checked: whether the suite could have been run somewhere other than this machine (a second machine, a CI runner, or the sibling project's running wp-env with this plugin mounted into it) — none was tried; the claim is only that this checkout's own playground does not build today.
- What this does and does not cover: it covers the commits of sprint 2 only, none of which touches plugin code. It is not a standing permission. The next push that reaches product code goes through the hook, which needs S-036 first.
- Why: the alternative was leaving the work on one machine, which is the state Keel's durability rule exists to eliminate; the hook's remedy was one the session could not perform.
- Supersedes: none.

## D-048 — One-time verifications of the reconciliation: what was observed, what was proven only in fixtures, what is scheduled (S-037)
- Date / phase: 2026-10-06 / post-update reconciliation, slice S-026
- Decision: the evidence for the new scaffolding is recorded as it is, in three grades, in `docs/05-test-points.md` (sprint 2 row):
  1. **Observed for real in this repository:** the chaining smoke test (`scripts/keel-chain-check --smoke` opened a Terminal window, read the marker back, and a second fire opened nothing — it wrote `Chain verified:`); the Stop hook blocking a live turn four times, for uncommitted work, unpushed commits, the plan behind the work, and a non-empty queue; the confidential-data gate running on every commit since `core.hooksPath` was set, and blocking a non-public key in a throwaway repository; the post-commit hook deleting a hand-off on a real commit.
  2. **Proven in throwaway fixtures only:** every Stop-hook rule in both directions including the cede to a live session, the close-out discharge and the rename and path-with-space cases (35 assertions); the launcher's degrade and terminal paths; `scripts/keel-affected-tests` on historical and synthetic diffs and the pre-push hook against a stub runner; the `scripts/keel-time` report in its three named cases; `scripts/keel-verify`'s new checks in both directions (110 cases).
  3. **Not done, scheduled as slice S-037:** the Stop hook observed after a full session restart; the timing report read in this repository over a real multi-session slice; a real push through `.githooks/pre-push` with a real selection (needs S-036).
- The allow-list entry for running `scripts/keel-stop-hook` by hand is NOT added: the hook is invoked by the harness, not through the shell tool, and the user confirmed the committed allow-list by name without it (D-043). Offered again only if manual runs of the hook turn out to be needed.
- Why: fixture evidence is real evidence of the logic and no evidence of the wiring; writing the two down separately is what keeps "verified" from meaning "ran somewhere".
- Supersedes: none.

## D-049 — Keel v5.9.0 → v6.5.0 reconciliation: applied, declined, scheduled
- Date / phase: 2026-10-06 / post-update reconciliation, sprint 2 close
- Applied: the embedded skill and lock block (D-034); the session clock and sprint plan (`scripts/keel-time`, `scripts/keel-plan`, `docs/sessions.md`, `docs/sprints/`, `docs/.keel/plan.json`); the chaining family to the current contract, proven by a real smoke launch (`Chain verified:` on the card); test selection with the pre-push hook; the post-commit hook; the confidential-data gate; the Claude Code config package (D-039, D-043); `scripts/keel-doctor` and a 27-check `scripts/keel-verify`; the documentation backfill (AC ids, flows, usage, reference, API index); the environment and driver sections of the technical plan; the card lines `Chaining model`, `Chain verified`, `Test-first policy`, `Push test scope`, `Sprints`, `Security audit`, `CI runs on`, `Models`.
- Declined: the competitive scan (D-035); forge CI and MCP registration (D-039); an allow-list entry for running the Stop hook by hand (D-048). Already excluded before today and untouched: the website (D-005), the client budget (D-010), the end-user guide (D-013).
- Scheduled, each as a named slice: minification S-027 (D-038); the security audit the card now requires S-028 (D-040); the accessibility passes S-029 and S-030; the playground repair S-036 (D-044); the remaining one-time verifications S-037 (D-048).
- Result: `docs/keel-conformance.md` — Table 1, 65 present, 2 missing and scheduled, 3 declined, 25 n/a (95 rows); Table 3, 71 present, 3 missing and scheduled, 1 declined, 32 n/a (107 rows); no unresolved row. `Keel baseline:` advances to v6.5.0.
- What this reconciliation did NOT establish: that the plugin's test suites still pass. They were not run (D-044), and no product code was changed.
- Supersedes: none. Closes the pending item opened by D-034.

## D-050 — Dependabot triage (S-031): unused dependencies removed, version synced; the rest needs two major upgrades
- Date / phase: 2026-10-06 / sprint 3, slice S-031
- Found: all 55 open Dependabot alerts sit in `package-lock.json`. Nothing from npm ships: `package.json`, `package-lock.json`, `node_modules/`, `resources/` and `webpack.config.js` are `export-ignore`, and the one built file (`assets/js/frontend/blocks.js`) externalises its imports. The alerts are exposure of the development machine, not of stores running the plugin. The `wp-scripts` entry was npm's own empty security-holding package (`0.0.1-security`, "security holding package"), not a hostile one; the `wp-scripts` binary the npm scripts call comes from `@wordpress/scripts`. `react-scripts` was imported nowhere.
- Decision: (1) both entries removed, which empties `dependencies`; (2) `package.json` `version` set to 7.0.2, the plugin's current released version — not a version bump — and `scripts/keel-verify` now fails when it disagrees with the plugin header; (3) `npm audit fix` applied without `--force`. `npm audit` went from 147 findings (5 critical) to 115 (2 critical).
- Not done, and why: what remains needs `@wordpress/scripts` 30 → 36 (80 findings) and `@wordpress/env` 10 → 11 (the 2 critical, in `simple-git`, plus the Playground CLI chain). Both are major upgrades of the tools that build the shipped asset and run the playground; the first is slice S-039, the second is tried inside S-036 because it is the playground tool.
- Also found: `npx wp-scripts build` fails on this machine's Node 24 with an OpenSSL "unsupported" error raised by the md4 hash in `@wordpress/dependency-extraction-webpack-plugin` 2.9.0 (pulled by `@woocommerce/dependency-extraction-webpack-plugin` ^1.7.0). It is independent of this change and runs with `NODE_OPTIONS=--openssl-legacy-provider`. Fixed properly in S-039.
- Verified: with that option, the build after the change reproduces `assets/js/frontend/blocks.js` and `blocks.asset.php` byte for byte (same SHA-1 before and after); the new `keel-verify` check passes at 7.0.2 and fails at 4.0.0.
- Not checked: whether the suites could run somewhere other than this machine; whether Node 22 builds without the legacy OpenSSL option; whether the pre-existing build failure also occurs on the Node version used for the 7.0.2 release.
- Not verified: the PHPUnit and Playwright suites (playground down, D-044). Dependabot reads the default branch, so the alert count on GitHub does not move until this reaches `master`.
- Not pushed: the pre-push selection for a lockfile change is the whole suite, which cannot run before S-036. D-047's bypass covered sprint 2 only and is not reused.
- Supersedes: none. Closes the three `package.json`/Dependabot deferred items in `docs/PROGRESS.md`.

## D-051 — The playground is restored on the pinned PHP 7.4 by moving `@wordpress/env` to 11; the PHP-version question is withdrawn
- Date / phase: 2026-10-06 / sprint 3, slice S-036
- Decision: `@wordpress/env` goes from `^10.0.0` to `^11.16.0`. With it `npx wp-env start` builds and runs on `phpVersion: 7.4` unchanged, so the pin recorded in D-014 stands and the question parked in D-044 (which PHP version to move to) no longer needs an answer. It was never answered by the user; it is withdrawn, not decided.
- `.wp-env.json` changes in one respect only: `AUTOMATIC_UPDATER_DISABLED` and `WP_AUTO_UPDATE_CORE: false` are added. This enforces the existing WordPress 7.0 pin (the development site had updated itself to 7.1.2, L-008); it does not change what the playground is meant to be.
- New: `scripts/playground-setup`, the scripted form of the one-time setup that `docs/playground.md` described in prose (L-008). It is not in the committed allow-list; adding it there is the user's to confirm (D-043).
- Verified: environment destroyed and rebuilt from nothing, then `scripts/keel-affected-tests --run` — 8 unit, 36 integration and 7 e2e tests, 49 of 49, green; PHP 7.4.33, WordPress 7.0, WooCommerce 7.4.0 read from the running containers; the debug log holds no PHP error beyond two known notices (WooCommerce's early translation loading on WordPress 7.0, and L-004).
- Correction to D-050: the upgrade does not clear the critical audit findings. `@wordpress/env` 11.16.0 still depends on the flagged `simple-git`; `npm audit` reports 115 findings, 3 critical, all three in that chain. They are in a development tool and nothing from it ships. Left as is until upstream releases a fix.
- wp-env 11 prints deprecation warnings for `clean` (now `reset`) and for the combined development-and-tests configuration this project uses. Both still work; not changed here.
- Not checked: PHP 8.x. The suite has only ever run on 7.4, so nothing here says the plugin works on the PHP versions most stores run. Whether to add a second playground on a current PHP is a separate question for the user.
- Supersedes: the "why not fix it now" and "not checked" lines of D-044. Unblocks S-027, S-029, S-035, S-037, S-038, S-039.

## D-052 — The candidate defects of D-041 were reproduced and fixed (S-035); what changed in behaviour, and what was left
- Date / phase: 2026-10-06 / sprint 3, slice S-035
- Each of items 1 to 6 of D-041 was reproduced by a failing test in `tests/Integration/CandidateDefectsTest.php` before its fix (D-036). All were real.
- Fixed, with the behaviour chosen:
  1. Thank-you fallback: `redsyslite_mark_order_as_paid()` now forwards `Ds_SignatureVersion` with the other two return parameters. The gateway still verifies the signature itself.
  2. Google Pay: a notification for an order that is already paid returns without changing anything. The guard sits after signature verification and after the refund branch, and uses `return` where the card and Bizum classes use `exit`.
  3. Refund of zero (card, Bizum, Google Pay): an explicit amount that rounds to zero cents or less returns a `WP_Error` and sends nothing to Redsys. This differs on purpose from Inespay (D-026), which sends the explicit `0`: a zero refund request to Redsys would be followed by up to 100 seconds of polling for a confirmation that cannot arrive. A missing amount still means the full total. One new translatable string, added to the `es_ES` files.
  4. Bizum limit: decimals compared, and a total EQUAL to the limit is now allowed, as in Inespay. This is a visible change for a store whose customers pay exactly the limit.
  5. Debug logs: no line writes the signing secret or the locally computed signature any more. The tests found four more such lines per gateway than D-041 had listed (the "saved" line and three on the refund path, one of them in the card gateway).
  6. Google Pay in test mode: an absent or empty `testshowgateway` list now means "offered to everyone". This is the larger behaviour change: a store that has Google Pay enabled in test mode will start showing it to customers, as the card and Bizum gateways already do in test mode.
- Also found and fixed: with debug on, a refund request that failed at the HTTP level concatenated the `WP_Error` object into a log line, a fatal error (three classes). `$orderdo` was an undeclared property in the Google Pay class; it is declared and read from the stored settings, where nothing writes it.
- `AC-24` and `AC-33` were revised in place rather than withdrawn: both had been written as-built from the defective behaviour (D-045). They are now bound to tests and leave D-045's unverified list. `AC-58` to `AC-61` are new.
- Review: an independent security read and an independent code read of the diff. Applied from them: the locally computed signature no longer logged; the zero guard rounds to cents; the user-list filter ignores non-scalar entries; the tests reset the gateway registry and the cart.
- Left as it is: the inbound `Ds_Signature` and the raw notification data are still written to the debug log. They are request data, and a debug log exists to show them; redacting them is deferred as S-040. Item 7 of D-041 (Inespay missing from `redsys-types.php`, `on-hold` counted as paid, refund URL difference, stray `wpml-config.xml` key) was triaged only and is deferred as S-041.
- Not checked: a real refund or a real duplicate notification against Redsys's test environment (no merchant credentials, `CREDENTIAL`); PHP 7.0 to 7.3, which the header declares and the playground cannot run — the changed lines were read for 7.0 syntax, not executed on it.
- Supersedes: the "unverified" readings of D-041 items 1 to 6.

## D-053 — Issue 112: the cancel URL sent to Redsys is a URL, and a return to an order Redsys already cancelled is a cancellation, not an error (S-038)
- Date / phase: 2026-10-06 / sprint 3, slice S-038
- Reproduced before any change (D-036), both halves of the report:
  1. `DS_MERCHANT_URLKO` carried `&amp;` separators in the card, Bizum and Google Pay gateways — `WC_Order::get_cancel_order_url()` is escaped for HTML. The browser came back with parameters named `amp;order_id` and `amp;_wpnonce`, so WooCommerce's cancel handler never ran.
  2. With a correct URL, a customer returning to an order the notification had already set `cancelled` got WooCommerce's "Your order can no longer be cancelled" error. This half was hidden by the first: fixing only the URL would have exposed it to every store, which is why they ship together.
- Fixed:
  1. `WCRedL()->get_cancel_url_raw( $order )` — WooCommerce's own `get_cancel_order_url_raw()`, the decoded escaped URL as fallback, an empty string when it cannot be built. The nine non-markup uses in the three gateway classes go through it; the `href` uses in the payment forms keep the escaped variant, which is correct there. Same shape as the premium plugin's fix (its D-094), without that helper's fallback calling itself.
  2. `redsyslite_allow_cancel_return_for_cancelled_order()` on WooCommerce's `woocommerce_valid_order_statuses_for_cancel`: for the one order named in a nonce-verified cancel request, already `cancelled` and paid through one of the three gateways, `cancelled` is accepted. WooCommerce then shows its own "Your order was cancelled." notice. No new string, no new endpoint, no status change.
- **Chosen by the assistant, for the user to confirm before release.** The reporter left the second half to the maintainer and named two options: point `DS_MERCHANT_URLKO` somewhere other than the cancel URL, or keep it and treat an already cancelled order as success. The second was taken because the cancel URL is still what cancels the order when the notification never arrives (blocked or delayed), so the customer sees the same thing whichever of the two wins the race, and nothing about where they land changes. Not taken: sending the customer to the cart or the checkout with no cancel action (an order whose notification is lost would stay `pending`), and setting the order `failed` instead of `cancelled` in the notification handler (changes order status for every store). Nothing reaches users until the release, which is the user's act.
- Measured on WooCommerce 7.4: a second `update_status( 'cancelled' )` on a cancelled order adds no order note and fires no status-transition hook; `woocommerce_cancelled_order` does fire again.
- Review: an independent security read and an independent code read of the diff. No defect found. Applied: the callback returns a non-array filter value untouched; the tests reset the current user.
- Found on the way, not fixed (deferred as S-042): the Google Pay `get_redsys_args()` reads the billing name through the generic meta API, a WooCommerce `doing_it_wrong` notice; the value is still read correctly.
- Not checked: a real refused or abandoned payment against Redsys's test environment (no merchant credentials, `CREDENTIAL`), so whether Redsys itself would have decoded the entities is not known; the reporter's WPML setup; PHP 7.0 to 7.3 — the changed lines were read for 7.0 syntax, not executed on it.
- Noted for the premium plugin, not touched from here: its `get_cancel_url_raw()` fallback branch calls itself instead of the order's method. It is reachable only for an order object without `get_cancel_order_url_raw()`.

## D-054 — Source plus minified pairs for every shipped stylesheet and script (S-027)
- Date / phase: 2026-10-06 / sprint 3, slice S-027
- Decision: Keel's build-assets contract is applied as written. Each of the three stylesheets has a `.min.css` beside it, built by `bin/build-assets.js` (postcss + cssnano). The Blocks checkout script is built twice from `resources/js/frontend/index.js`: `blocks.js` readable, `blocks.min.js` minified, each with its own `.asset.php`. `redsyslite_asset_suffix()` names the file to load: minified, or readable when `SCRIPT_DEBUG` is on. The build runs locally (`npm run build:assets`) and its output is committed; no CI builds it.
- What production loads: `blocks.min.js` is byte-identical to the `blocks.js` shipped in 7.0.2 — the file stores run changes name, not content. `blocks.js` is now the readable build. The stylesheets lose only whitespace and comments.
- Checked mechanically: `scripts/keel-verify` check 11 fails on a source with no tracked pair, on a stale minified stylesheet, and on built script files that differ from a fresh build into a temporary directory. Seen failing on a planted change to the script source. A comment-only change to a stylesheet leaves its minified file identical, so it is correctly not reported.
- Left as it is: `cssnano` and `postcss` are used through `@wordpress/scripts`' dependency tree rather than declared in `package.json`. Declaring them made npm rewrite 421 lines of the lockfile (peer-dependency resolution), which is not this slice's change to make one slice before S-039 rewrites the lockfile anyway. S-039 declares them.
- Not checked: whether the script translation file in `languages/` (`…-es_ES-<hash>.json`) is the one WordPress looks up for this script — its hash is not that of `assets/js/frontend/blocks.js`, with or without this change. WordPress resolves a `.min.js` script to the same translation file as its `.js`, so this slice does not change which file is looked up. The playground is in English; a Spanish Blocks checkout was not driven.
- Supersedes: D-038 (the gap it scheduled is closed).

## D-055 — Build toolchain upgraded to `@wordpress/scripts` 36; the Blocks script's JSX is compiled against `wp-element` (S-039)
- Date / phase: 2026-10-06 / sprint 3, slice S-039
- Done: `@wordpress/scripts` 30 → 36.0.0 and `@woocommerce/dependency-extraction-webpack-plugin` 1.7 → 5.1.0. `cross-env` and `webpack-cli` removed: nothing in the project called either, and `wp-scripts` brings its own webpack. `cssnano` and `postcss` declared (D-054 had left them implicit). `engines.node` in `package.json` now states what the tools require instead of `>=6.9.4`.
- How: npm refused both an in-place upgrade and a plain uninstall over the existing lockfile (peer-dependency resolution error), so `package-lock.json` was regenerated from the edited `package.json`. Side effect inside the declared ranges: `@playwright/test` 1.62.1 → 1.63.0. `@wordpress/env` stayed at 11.16.0.
- Result: `npx wp-scripts build` runs on Node 24 with no `NODE_OPTIONS` workaround (the md4 failure of D-050 is gone). `npm audit` 115 → 45 findings; the 3 critical remain and are the `@wordpress/env` chain D-051 already recorded. Nothing from npm ships.
- **A regression the upgrade would have shipped, caught and closed.** The new default build compiles JSX with the automatic runtime and declares the script dependency `react-jsx-runtime`. WordPress registers that handle from 6.6 on; on an older WordPress the Blocks script would not have been printed and the four gateways would have disappeared from the Blocks checkout. The 7.0.2 build depended on `react` and bundled the runtime. Bundling it again was not taken: the installed React is 19, whose runtime creates elements a site's React 18 rejects. The source now declares the classic runtime with `createElement` from `@wordpress/element`, so the built script depends on `wp-element`, which exists on every WordPress that has blocks and uses the site's own React whatever its version. Dependencies of the built script: `wc-blocks-registry`, `wc-settings`, `wp-element`, `wp-html-entities`, `wp-i18n` — `react` replaced by `wp-element`, nothing else changed.
- Consequence for the release: the Blocks script stores load is no longer byte-identical to 7.0.2's (D-054 said it was; that held until this rebuild). It is the same source built by newer tools, 2745 bytes against 3295.
- Verified: whole suite green (unit 8, integration 74, e2e 8) on the regenerated toolchain; the Blocks checkout driven with `SCRIPT_DEBUG` on and off; the stylesheets' minified output is unchanged under `cssnano` 7; `scripts/keel-verify` check 11 passes without the workaround.
- Not checked: a WordPress older than 6.6 — the playground pins 7.0, so that `wp-element` loads there is reasoned from the handle's history, not driven. Node 22 (only Node 24 is on this machine).
- Supersedes: the "needs two major upgrades" remainder of D-050 for `@wordpress/scripts`; the "left as it is" line of D-054.

## D-056 — Active security audit of the release candidate (S-028): result, what is committed, what follows
- Date / phase: 2026-10-06 / sprint 3, slice S-028
- Run: full scope at `e50ab39`, profile `references/security/wordpress.md`, per `references/security-audit.md`. Seven hunters in one parallel block over 21 units, then a coverage check by a reader given only the recon and the coverage table, then a second hunting round over the four gaps accepted from it (25 units in all). 19 candidates after merging duplicates; each went to a fresh verifier that received only the candidate card, the recon and the profile — never the hunter's reasoning. One verifier executed in the playground; the others read source, including WooCommerce 7.4 core.
- Result: 9 confirmed (0 critical, 0 high, 3 medium, 6 low), 2 `needs_validation`, 8 rejected. No path was found by which an outsider marks a real order paid, refunded or cancelled.
- What is committed and what is not: the raw run stays in `docs/security-audit/2026-10-06-e50ab39/`, gitignored — this repository is public and the report describes unfixed findings. Committed: the counts-only row in `docs/security-audit.md`, the corrected control states in `docs/threat-model.md`, and the slices. This is the recorded exception to "the work never lives only on this machine": the run is reproducible at the audited commit.
- Each confirmed finding is a slice with a neutral title, S-043 to S-051; every fix starts from a test that reproduces the finding and fails (D-036). S-052 is the scoped re-audit the release gate requires: each finding's verifier re-run against the candidate, plus the diff since `e50ab39`. S-032 now depends on S-052. Nothing is critical or high, so the hotfix path of a released product does not apply: the fixes ship with the release sprint 3 prepares.
- Threat model corrected in the same commit: three rows declared `IN PLACE` or fixed go back to `TO BUILD` with their slice; two `VERIFY` rows are now `IN PLACE` on the audit's evidence; two rows that still said no test suite and no dependency audit existed were stale and are corrected.
- For the user, as one batch (the release gate needs each resolved or acknowledged): the two `needs_validation` items. Each depends on one observation only the owner can make — one on a Redsys test terminal, one in the Inespay sandbox. Until answered they are not findings and carry no severity.
- Non-findings: the hardening notes are deferred as S-053, to be triaged with the user. The packaging gaps the audit noticed (development files not excluded from an archive export) belong to the package-hygiene part of S-032.
- Not covered, stated in the log: multisite, HPOS order storage, PHP 8.x, WordPress older than 6.5; no production, no live processor account, no deployment facts.
- Not a public issue: a confirmed finding is never filed on the public tracker, whatever `Issue capture:` says.
- Supersedes: none. Discharges the audit D-040 scheduled; the gate itself stays open until S-052.


## D-057 — A notification that names no order never verifies (S-043, audit finding SA-01)
- Date / phase: 2026-10-06 / sprint 3, slice S-043
- Reproduced before any change (D-036): for a payload with no order number the key that signs a notification had zero bytes, so the expected signature was the HMAC of the parameters under an empty key and did not depend on the merchant secret. 8 unit cases and 12 gateway cases (card, Bizum, Google Pay) failed. Such a notification resolves to no order, so no order, money or status was reachable; what did not hold was the gate itself, and the `valid_<gateway>_standard_ipn_request` action fired.
- Fixed in one place rather than at the six comparison sites: `RedsysLiteAPI::diversify_notif_key()` (private) derives the key for the three notification-signature methods. With no order number, a non-scalar one, or a secret that decodes to nothing, the methods return the encoding of 32 fresh random bytes. The call sites and their `hash_equals()` are unchanged, and third-party code calling the public method is covered too. Not taken: returning an empty string or `false` (an empty `Ds_Signature` would match the first; the second is a type error in `hash_equals()` on PHP 8).
- Behaviour kept: a notification with a real order number produces the same signature byte for byte (existing tests, plus one for the upper-case `DS_ORDER` key). An order number of `"0"` counts as no order, as it already did in `get_order_notif()`.
- The audit report said `Ds_Order` `"0"` was not forgeable. The test showed it was: `empty( '0' )` is true, so it took the same path. Covered by the fix.
- Review: an independent security read and an independent code read. Applied: the single helper (the first version repeated the rule three times), English comments, the `random_bytes()` fallback, no error silencers in the unit test, and the cases for an array order, a JSON scalar, an empty secret, the SOAP response variant and a positive control. No `@since` tag was written: the version number is the user's decision at the release gate.
- Not checked: PHP 8.x and PHP 7.0 to 7.3 (the playground is 7.4; the changed lines were read for 7.0 syntax). `successful_request()` was not called directly with such a payload: before the fix it reaches `exit` and would end the test run; it uses the same method.
- Disclosure: this repository is public, and the fix and its tests are readable on `develop` before the release. The audit log keeps counts only until the release, as D-056 set.
- Supersedes: the `TO BUILD` state D-056 gave the signature-verification row of `docs/threat-model.md`.

## D-058 — The Google Pay debug log no longer writes the signing secret when the payment form is built (S-044, audit finding SA-03)
- Date / phase: 2026-10-06 / sprint 3, slice S-044
- Reproduced before the change (D-036): with debug on, `get_redsys_args()` of the Google Pay gateway printed the whole array it builds the form from, and that array carries the signing secret. The card and Bizum form paths were driven by the same test and did not log it.
- Fixed: that one line prints the array without the secret. Nothing else changes; the log keeps every other field.
- Why D-052 missed it (L-009): its tests drove the notification and refund paths, and the control was declared for the class. The test now drives the form path of the three gateways, and the threat-model row names the three paths its evidence covers.
- Sweep of the class: every logger call that prints an array or names a secret was listed by search, and an independent read went through the 90 call sites that print a variable. No other line writes a signing secret or the Inespay API key. Read, not executed, for the paths that have no test.
- Driven in the playground: a Google Pay checkout with debug on, then the gateway log read back — the form line is there, the configured secret is not.
- For the release notes: a store that has had Google Pay debug logging on holds its signing secret in existing log files (`wp-content/uploads/wc-logs/`, name starting with the gateway id). The fix stops new lines; it does not clean old files. Whether to tell merchants to delete those logs, or to rotate the key, is the user's wording to decide when the version is proposed.
- Left as it is: the secret kept in the `redsys_signature_<order>` transient (the D-025 family, hardening notes, S-053); the inbound signature and raw notification data in the log (S-040).
- Supersedes: the `TO BUILD` state D-056 gave the debug-log row of `docs/threat-model.md`.

## D-059 — The Logo setting is a URL: validated on save and escaped where the icon is used (S-045, audit finding SA-17)
- Date / phase: 2026-10-06 / sprint 3, slice S-045
- Reproduced before any change (D-036): a Logo value with a double quote in it was stored as typed and became the gateway icon; WooCommerce prints the icon inside an image tag without escaping, so the value added attributes to the image on the classic checkout. Entering it takes a user who may change the gateway settings (shop manager or administrator). 13 cases failed, over the card, Bizum and Inespay gateways.
- Fixed at both ends, because a value saved earlier is still in the database: `validate_logo_field()` in the three gateways stores the posted value through `esc_url_raw()`; the icon is escaped at each of its seven use sites — `esc_url()` in the four gateway constructors (Google Pay included: its icon is a constant, but it goes through a filter), `esc_url_raw()` in the four Blocks integrations.
- Visible changes, for the release notes:
  1. A Logo URL with a query string is now stored as entered. WooCommerce's default text filter had been storing `&` as `&amp;`.
  2. A value with a scheme WordPress does not allow (`data:`, `javascript:`) is stored empty, so the bundled icon is shown. A merchant who had pasted a `data:` image as the logo loses it.
  3. A path with no leading slash (`wp-content/uploads/logo.png`) is read as a host name. It was never reliable: it resolved against the URL of whatever page showed it. A path from the site root (`/wp-content/…`) and a protocol-relative URL are kept.
  4. The value a `woocommerce_<gateway>_icon` listener returns is escaped as a URL. A listener that returns a URL sees no difference.
- Not migrated: stored values are not rewritten. They are cleaned each time they are read, and replaced the next time the settings are saved.
- The validator is three identical one-line methods. WooCommerce calls it by name on the gateway object, so each class needs its own; a shared helper would add a fourth method to save nothing.
- No `@since` tag on the new methods, as in D-057: the version number is not decided yet. `.claude/rules/code-style.md` asks for the tag, so the release slice (S-032) adds it to the methods of S-043 and S-045 once the number is approved.
- Review: an independent security read and an independent code read. Applied: Google Pay and filtered-value cases for the four gateways, scheme and relative-path cases, comment placement, the documentation rows.
- Found on the way, added as S-054: the file and line references in `docs/reference/` drift whenever a class gains lines (this slice moved everything below the new method in three classes), and nothing checks them.
- Not checked: PHP 8.x; WooCommerce newer than 7.4, whose payment-settings screen may read the icon differently.
- Supersedes: the `TO BUILD` state D-056 gave the output-escaping row of `docs/threat-model.md`.

## D-060 — The order-received fallback verifies before it waits; an older regression test had its fixture revised (S-046, audit finding SA-07)
- Date / phase: 2026-10-06 / sprint 3, slice S-046
- Reproduced before any change (D-036): `redsyslite_mark_order_as_paid()` waited five seconds before it looked at the gateway, the order or the signature. An order of any gateway, or no order at all, with any text in `Ds_MerchantParameters`, held the request. D-026's limit is per order, so a visitor with several orders of their own was not limited by it.
- New order of the function, stopping at the first check that fails: return parameters present; no signed return handled for this order in the last 30 seconds; the order exists, belongs to one of the three gateways and is unpaid; the return names this order; the signature verifies. Then the 30-second attempt is taken, the function waits five seconds, and if the order is still unpaid it calls `successful_request()`, which verifies again.
- New public method `is_valid_return( array $params ): bool` in the card, Bizum and Google Pay classes: it verifies and does nothing else. `check_ipn_request_is_valid()` could not be reused, it reads `$_POST` and (Bizum, Google Pay) deletes a transient when it fails.
- Two things the first version of the fix missed, found by the independent security read and reproduced by failing tests before being fixed:
  1. One genuine signed return could be replayed against every other pending order of its holder, each waiting five seconds. The return must now name the order in the URL: checked against the order-number mapping before verification when the mapping is still there, and through `clean_order_number()` after verification when it has expired.
  2. `RedsysLiteAPI::get_parameter()` raised a `TypeError` on PHP 8 (a warning on 7.x) after decoding data that is not a JSON object. It answers `null` now. This also removes the uncaught error the audit's hardening notes listed for Bizum and Google Pay notifications of that shape.
- **A regression test was changed, on the record.** `MarkOrderAsPaidRateLimitTest::test_repeat_calls_for_the_same_unpaid_order_are_rate_limited` (D-026) used an order with no gateway and no return parameters and asserted that the first call waits five seconds. That request is exactly what must no longer wait, so the requirement changed, not only the code. The fixture is now a card order with a signed return, and the order is put back to `pending` between the two calls so that only the per-order guard can stop the second. Both assertions are unchanged. Checked by mutation: without the guard the test fails. `AC-13` is revised in place and is now proven by a test; `AC-66` is new.
- Behaviour a store can notice: none for a genuine return. A request without a valid signature no longer blocks the next genuine return of that order for 30 seconds, which it used to.
- Not defended, recorded in `docs/threat-model.md`: the attempt is not claimed atomically, so parallel requests with the same valid signed return for the same order can each wait and each call `successful_request()` (it was not atomic before either; it now takes a genuine signature). Deferred as S-055, together with making `successful_request()` verify through `is_valid_return()` instead of repeating it.
- Related, not changed: SA-06 (a signed success return replayed for an order cancelled later) still waits for the owner's observation. The binding added here does not decide it: that replay names its own order.
- Not checked: PHP 8.x itself (the `TypeError` was reproduced as PHP 7.4's warning, which the test run turns into an error); concurrency.
- Supersedes: the "FIXED" claim of D-026 for this vector, which D-056 had marked incomplete; the fixture of D-026's rate-limit test.

## D-061 — The admin notices and the welcome redirect check who is asking (S-047 and S-048, audit findings SA-08 and SA-09)
- Date / phase: 2026-10-06 / sprint 3, slices S-047 and S-048
- Reproduced before any change (D-036). (1) The "new version" and Telegram notices were printed for every user who can open an admin screen, and their dismiss handlers checked the nonce but no capability: a contributor could hide both for everyone. (2) `redsys_welcome_splash()` runs on `admin_init`, which also fires for `admin-ajax.php` and `admin-post.php`, with nobody logged in: such a request, while the stored version differed from the running one, wrote two options, was answered with a redirect to the About page and never reached its own handler.
- Fixed: both notices return at once without `manage_woocommerce`, for display and for dismissal; the nonce is read only when present. The welcome redirect returns at once unless the user has `manage_options`, the request is not AJAX and it is not a submitted form.
- Capability chosen for the redirect: `manage_options`, not the `manage_woocommerce` the audit suggested. The About page it leads to is registered with `manage_options`; a shop manager sent there would land on a permission error and the welcome would be spent. The welcome now waits for an administrator.
- The submitted-form guard came from the independent review: an administrator saving any form right after an update had the submission swallowed by the redirect. Reproduced by a failing test before the guard was added.
- `wp_doing_ajax()` is not used bare: it exists from WordPress 4.7 and `readme.txt` declares 4.0. The function is called when it exists and the `DOING_AJAX` constant is read otherwise.
- Visible changes: users below shop manager no longer see the two notices. After an update the About page opens on the administrator's next screen, not on whichever request came first.
- Left for S-051 (WooCommerce absent): with WooCommerce inactive the redirect still sends an administrator to a page that is not registered then.
- Not changed: the notice for a PHP older than 7.0 (`admin_notice_mcrypt_encrypt`), which writes nothing and cannot print on a supported PHP; multisite network admin (not exercised, D-056).
- Not checked: WordPress older than 7.0 (the 4.0-safe form was read, not run); multisite.

## D-062 — A refund waits for its own confirmation (S-049, audit finding SA-05)
- Date / phase: 2026-10-06 / sprint 3, slice S-049
- Reproduced before any change (D-036), on the card, Bizum and Google Pay gateways: with a confirmation flag left on the order from earlier, a refund whose request Redsys accepted and never confirmed was reported as done at the first look. No attacker is involved. The ordinary ways a flag gets left: a confirmation that arrives after the wait has ended, and a refund made in the Redsys back office.
- Fixed, the same in the three classes: `process_refund()` clears the flag immediately before the request is sent; the notification handler records a confirmation through the new `set_refund_confirmed()`, which keeps it 10 minutes (it had no expiry); the confirmation is still used once.
- New filter `woocommerce_<gateway id>_refund_confirmation_attempts` (default 20 further looks, as before). It exists because the reproduction test needs the real `process_refund()` to finish in seconds, and it is useful by itself on a host where the notification is slow. Documented with the other hooks.
- **Deliberately not done, and why:** the audit also proposed tying the confirmation to the amount asked. That needs the exact form of the amount in Redsys's refund notification, which cannot be established without a Redsys test terminal (`CREDENTIAL`). A comparison written from a guess would make every refund fail on the stores where the guess is wrong, and this is the path that moves money. The same holds for reading Redsys's answer to the refund request, which today is ignored. Recorded in the threat model's "Not defended" table with its consequence.
- What is left after the fix, stated plainly: a confirmation that arrives while a refund of the same order is being waited for confirms it, whichever refund it belongs to; and a refund Redsys makes but confirms after about 105 seconds is reported as failed, so a retry can refund twice. The second was already so; before the fix the retry would have appeared to succeed at once on the old flag.
- **For the user, when a Redsys test terminal is at hand:** one refund with debug on, to read what the refund notification and the answer to the refund request contain. With that, the amount comparison (or reading the answer) can be built and the "Not defended" row closed.
- Review: an independent security read. Applied: a test that drives the notification handler's refund branch in the three gateways.
- Not checked: a real refund against Redsys; a persistent object cache (transients live there instead of the options table; an eviction can only produce a false failure).

## D-063 — The Bizum and Inespay transaction limit holds wherever an order can be paid (S-050, audit finding SA-16)
- Date / phase: 2026-10-06 / sprint 3, slice S-050
- Reproduced before any change (D-036): the limit was only a filter on the list of offered gateways, applied only on the checkout page and against the cart total. On the order-pay page (empty cart), in a request that is not the checkout page (the Blocks checkout), and when the payment was actually started, nothing applied it.
- Fixed: one rule, `is_over_transaction_limit()`, in both classes. The filter uses the order total on the order-pay page and the cart total elsewhere, and no longer depends on being on the checkout page. `process_payment()` of both gateways refuses an order above the limit with an error notice and sends nothing to the processor.
- Found by the independent review and reproduced by a failing test before being fixed: Bizum's payment form page is rendered by WooCommerce directly for a pending order whose method is already Bizum, without `process_payment()`. It now shows the same message and no form. Inespay has no such page.
- One new translatable string, used in three places, added to the `es_ES` files.
- Unchanged: an amount equal to the limit is allowed (D-052); empty or zero means no limit.
- Known and left as it is: the limit is read with PHP's number conversion, so `200,50` is read as 200 and `1.000` as 1. It was already so. The setting gives no format hint; a hint or a stricter reading changes how existing values are understood and is the user's call.
- Not checked: a real Blocks checkout request above the limit (the route is reasoned from WooCommerce's code: the Store API takes the gateway's `failure` result and its notice); PHP 8.x.

## D-064 — The plugin does not take the front end down when WooCommerce is absent (S-051, audit finding SA-19)
- Date / phase: 2026-10-06 / sprint 3, slice S-051
- Reproduced before the change (D-036) by loading the main file in its own PHP process with no WooCommerce function defined: the callback hooked on `wp_head` at file scope called a WooCommerce function and ended in a fatal error, on every front-end page. WordPress enforces the plugin's dependency on WooCommerce from 6.5 and only in the admin screens, so WooCommerce can go missing under an active copy of this plugin.
- Fixed: the callback returns at once when WooCommerce's function or the plugin's own helper is not there. Also, from D-061's note: the welcome redirect does nothing when WooCommerce is not loaded, because the About page it leads to is not registered then.
- Sweep of the class: everything else the main file hooks at file scope was read — it is either a WooCommerce hook (never fired without WooCommerce) or uses only WordPress functions.
- Driven in the playground with WooCommerce deactivated through WP-CLI: the front end answers normally. WooCommerce was reactivated and the site checked afterwards.
- Not checked: a WordPress older than 6.5; the class-name collision with the premium plugin when both are active (hardening notes, S-053).

## D-065 — Scoped security re-audit of the candidate (S-052): the nine findings are refuted, the diff raised nothing, one item needs the owner
- Date / phase: 2026-10-06 / sprint 3, slice S-052
- Run: scoped at `476d52e`, per `references/security-audit.md`: the nine findings confirmed at `e50ab39`, and the shipped-code diff since that commit (9 PHP files, `readme.txt`, `.gitignore`). A partial pass by construction; everything else rests on the full run (D-056).
- How the nine were re-verified: one fresh verifier per finding, given the original candidate card, the recon and the profile, and told not to read this file, the sprint files or the earlier run. Each was asked to refute the claim against the code on disk and to attack whatever control it found (the sibling classes, the other path to the same sink, the sad inputs). Result: nine `rejected`, each naming the control on every path of the claim.
- The diff: three hunters over the changed files, then a coverage check by a reader given only the recon, the coverage table and the patch. It found one stretch nobody had read (the refund branch of Google Pay's `successful_request()`); a fourth hunter took it and also settled that each class's `is_valid_return()` selects the secret the way that class's own `successful_request()` does. No candidate from any of the four.
- One new item, SA-20, `needs_validation`: raised from a verifier's side note and decided by a different verifier. It depends on one fact about Redsys that the source cannot show. It is not a finding and carries no severity. Its details stay in the local run, as D-056 set for anything open.
- For the user, as one batch with the two already waiting (SA-06, SA-13): each needs one observation on a Redsys test terminal or in the Inespay sandbox, then investigate, accept or dismiss. The release gate needs the three resolved or acknowledged.
- Hardening notes: 18, in the local report, added to S-053's triage. One is worth reading first: on PHP 8, a Bizum notification posted without its signature-version field may end in an uncaught error instead of a refusal. No bypass; unchanged code; read, not executed.
- What is committed: the second row of `docs/security-audit.md` (counts only) and this entry. The raw run is in `docs/security-audit/2026-10-06-476d52e/`, gitignored.
- Gate state (`Security audit: required`): a run covers the candidate (full at `e50ab39` plus this scoped one) as long as nothing outside `docs/` and `scripts/` changes before the release; no confirmed finding is open; the log row exists. Still missing: the owner's word on the three `needs_validation` items.
- Not checked: nothing was executed in this run — every verdict is a reading of the source; the fixes' regression tests were last run at their own slices. PHP 8.x, multisite, HPOS and WordPress older than 6.5, as in the full run.
- Supersedes: the "scoped re-audit pending" state of D-056.

## D-066 — Line references in `docs/reference/` are checked against the symbol they sit beside (S-054)
- Date / phase: 2026-10-06 / sprint 3, slice S-054
- Found: 64 of the 96 `file:line` references in the four documents of `docs/reference/` pointed at a line that no longer held their symbol. The security fixes had added lines to every gateway class and to the main file.
- Fixed: every reference re-resolved to the current line of the definition, the call, or the `do_action()` / `apply_filters()` call it documents. The functions table used a line-only shorthand explained by a sentence under the table; those 15 references now name their file, so each reference can be read alone.
- Check added, `scripts/keel-verify` check 28: for each reference, the names written in code spans beside it (the label after it, then the ones before it on the line, then the section heading) are collected, and the referenced line must name one of them — as a definition, a call, a quoted name, or a hook call whose literal parts fit the name (`'woocommerce_' . $this->id . '_icon'` fits `woocommerce_redsys_icon`). A line-only reference needs a full one before it on the same line.
- Limit of the check, stated: it proves the line names the symbol, not that it is the intended occurrence. A reference that drifts onto another line naming the same symbol passes.
- Not checked: references outside `docs/reference/` (the flows, the threat model and the decisions cite lines too; decisions are history and are not re-resolved).
- Supersedes: none.

## D-067 — Automated accessibility pass built and run (S-029); the About page's contrast fixed (S-056); the notice buttons recorded and deferred (S-057)
- Date / phase: 2026-10-06 / sprint 3, slices S-029 and S-056
- Built: `tests/e2e/accessibility.spec.js`, 14 tests. Each scans one screen in one state with axe-core, limited to the region this plugin renders, with the rules tagged WCAG 2.0, 2.1 and 2.2 at levels A and AA (D-007). What WooCommerce, WordPress or the theme render around those regions is not asserted.
- Tool: `axe-core` is injected into the page through Playwright. It was already in the dependency tree (through `@wordpress/scripts`); it is now declared as a development dependency of its own so the spec does not rest on somebody else's dependency. `@axe-core/playwright`, which the plan had named, is not used: one more package for what four lines do. The doctor has a row for it and the plan's requirements table too.
- Result of the first run: the gateway rows of the classic checkout (four gateways, each selected), the payment form of the three Redsys gateways, the payment options of the Blocks checkout (each selected) and the four settings forms have no violation. Two regions had some:
  1. About page: 32 nodes below 4.5:1 — the host name under each card (`#8c8f94` on white at 11.5px, 3.24:1) and three of the coloured initials. This is the plugin's own markup. Fixed as slice S-056, added to the plan before the change: the host name is `#646970` and the three text colours are darker; every colour pair the page's data declares was computed and is at or above 4.5:1. The failing scan is the reproduction; it passes after the change.
  2. Admin notices: the four link buttons of the "new version" notice, 4.22:1 by axe's measure of white text against its text shadow. The colours come from WooCommerce 7.4's stylesheet for a primary button inside a WooCommerce message; the plugin only chose the classes. **Not fixed, on purpose:** giving those buttons colours of their own is a visual decision, which is the user's, and how a current WooCommerce styles them has not been observed. Deferred as S-057. The spec lists this one item as known, by rule and selector, so the test still fails on anything else in the notices.
- The colours of S-056 are the assistant's choice within the existing palette (same hues, darker). No design system exists for this page (D-009). If the user prefers other values, any pair at or above 4.5:1 passes the same test.
- What the pass does not prove is written in `docs/accessibility.md`: one environment, one theme, English, the states listed there; nothing about keyboard order, focus in use or what a screen reader announces. `AC-48` is recorded as partly covered and stays on D-045's list of unverified criteria until the guided pass.
- The guided assistive-technology pass (S-030) is not run: it needs a person with a screen reader (`ASSISTIVE-TECH`). Its script, eleven steps, is in `docs/accessibility.md`.
- Test selection: the About page's two files now map to the new spec in `scripts/keel-affected-tests` (they had no test).
- Not checked: a current WooCommerce and WordPress, another theme, the Spanish locale, a narrow viewport, high-contrast or dark schemes; the checkout's error states, the order-received page and the test-mode banners; the 21 About-page nodes whose contrast axe could not compute.
- Supersedes: the `TO BUILD` state of "Accessibility automation" in `docs/03-technical-plan.md` and of the automated pass in `docs/accessibility.md`.
