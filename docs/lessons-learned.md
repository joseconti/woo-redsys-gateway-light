# Lessons Learned — Payment Gateway for Redsys & WooCommerce Lite

## L-001 — Vendored third-party reference code inside `docs/` was not export-ignored
- Symptom: `docs/inespay-payment/` contained a full copy of a third-party Inespay gateway plugin (own text domain, own PHP API client), sitting inside the `docs/` tree.
- Cause: reference material was dropped into `docs/` at some point and never excluded from `.gitattributes`' `export-ignore` rules, unlike the plugin's other dev-only paths.
- Fix: moved to `.reference/inespay-payment/` (repo root) and gitignored, per D-006.
- Where: adoption inventory, step 1 (git/package hygiene).
- What failed first: n/a — caught before any distributable package was built from this state.
- Check added: none possible yet — `scripts/keel-verify` does not exist for this project. Proposed check for when the Phase 5 scaffold adds it: a rule that flags any tracked file under `docs/` whose path or contents don't match the project's own text domain / namespace.
- Rule for next time: reference material that is not part of the shipped plugin belongs outside `docs/` (or gitignored) from the moment it's added — never inside a tree that is packaged for distribution without an explicit exclusion.

## L-002 — This plugin did not auto-activate on `wp-env start` despite being listed in `.wp-env.json`'s `plugins`
- Symptom: after `npx wp-env start`, `wp plugin list --status=active` showed only `woocommerce` active; `woo-redsys-gateway-light` was present (correct version 7.0.2, correctly mapped) but `inactive`.
- Cause: not root-caused during this session — `wp-env` is documented to auto-activate plugins listed under `plugins` in `.wp-env.json` on a fresh install, so this may be an interaction with how the mapped-source-vs-zip plugins are ordered, or a `wp-env` version quirk. Not confirmed either way.
- Fix: `wp plugin activate woo-redsys-gateway-light` (via `npx wp-env run cli`) — one command, no error once run.
- Where: adoption Phase 5 scaffold verification, first `wp-env start`.
- What failed first: nothing — first automated check (`wp plugin list --status=active`) simply revealed the gap before any test relied on the plugin being active.
- Check added: none possible yet — this is a `docs/playground.md` step-1/step-2 instruction, not a `scripts/keel-verify` check (verify only checks static files, not a running environment). If this recurs across resets, worth adding an explicit `wp plugin activate` call to a setup script rather than relying on `.wp-env.json`'s auto-activation.
- Rule for next time: after any `wp-env start` or `wp-env clean all`, always confirm plugin activation state with `wp plugin list --status=active` before trusting anything else in the environment — don't assume `.wp-env.json`'s `plugins` list guarantees an active state.

## L-003 — Assumed a sibling gateway class shared `check_ipn_request_is_valid()`'s shape from its first ~15 lines; it didn't
- Symptom: wrote `tests/Integration/GatewayBizumIpnTest.php` as a near-copy of the working `GatewayRedsysIpnTest.php` (same fixture, same assertions); 4 of 5 tests errored with `Exception: Invalid order` instead of failing or passing.
- Cause: `class-wc-gateway-bizum-redsys.php`'s `check_ipn_request_is_valid()` starts identically to `class-wc-gateway-redsys.php`'s (confirmed by reading its first ~15 lines only), but after the signature check it diverges: it looks up a real `WC_Order` via `WCRedL()->get_order( $order2 )` (throws if none exists) and derives the effective secret per-order from a transient/`_redsys_secretsha256` order meta, not only the gateway's own setting. Assuming "same first 15 lines" meant "same method" skipped reading the rest.
- Fix: removed the wrong test rather than committing it; recorded the real shape and the fixture it needs (a real `WC_Order`) as an open deferred item in `docs/PROGRESS.md`, to build properly as its own slice.
- Where: Phase 5 sprint, extending IPN validation test coverage beyond `WC_Gateway_redsys` (2026-08-01).
- What failed first: `vendor/bin/phpunit -c phpunit-integration.xml.dist` — the errors surfaced immediately, before anything was committed.
- Check added: none mechanical (this is a "read the whole method, not the matching prefix" discipline issue, not something `keel-verify` can catch).
- Rule for next time: when a "twin" class/method is found via matching the first N lines, read the ENTIRE method before assuming it can be tested the same way — a shared prefix is not evidence of a shared control-flow shape, and two of the four gateway classes in this project (`inespay`) don't even share the signature algorithm.

## L-004 — `handle_callback()` writes WooCommerce's internal `_payment_method` meta key via the generic meta helper
- Symptom: writing `tests/Integration/GatewayInespayIpnTest.php`'s "accepts a correctly-signed callback" test failed with `Unexpected incorrect usage notice for is_internal_meta_key` — a WooCommerce `doing_it_wrong()` notice, not a real PHP error.
- Cause: `classes/class-wc-gateway-inespay-redsys.php`'s `handle_callback()` (the success path) calls `WCRedL()->update_order_meta( $order->get_id(), array( '_payment_method' => $this->id ) )`. `_payment_method` is WooCommerce-internal order data (`WC_Order::set_payment_method()`/`get_payment_method()`), and `WC_Data::update_meta_data()` explicitly warns against setting it through the generic meta API — the value is written to postmeta but bypasses whatever WooCommerce does through the dedicated setter (cache priming, back-compat handling).
- Fix: not fixed this session (out of scope for a test-writing slice; the same call also exists for `_redsys_done`, which is not an internal key and is unaffected). The test uses `$this->setExpectedIncorrectUsage( 'is_internal_meta_key' )` to acknowledge the existing behavior without silently hiding it or fixing production code as a side effect of writing a test.
- Where: Phase 5 sprint, Inespay IPN test coverage (2026-08-01).
- What failed first: the PHPUnit assertion inside `WP_UnitTestCase`'s `doing_it_wrong` tracking — WordPress core's test scaffold treats an unexpected `doing_it_wrong()`/`_doing_it_wrong()` notice as a test failure by design, which is exactly what surfaced this.
- Check added: none mechanical — flagged as a deferred item in `docs/PROGRESS.md` (low severity: works today, but should use `$order->set_payment_method( $this->id ); $order->save();` instead of the generic meta call).
- Rule for next time: a `doing_it_wrong()` failure inside `WP_UnitTestCase` is not a broken test — it is the test scaffold catching a real, pre-existing WooCommerce API misuse. Read the actual notice before adding `setExpectedIncorrectUsage()` reflexively; only use it for genuinely out-of-scope pre-existing behavior, and record what it's masking as a lesson/deferred item so it doesn't stay invisible.

## L-005 — Reverting a PHP mutation during Playwright mutation-testing didn't take effect until the `wordpress` container was restarted
- Symptom: while mutation-testing `checkout-blocks-redsys.spec.js` (D-028), corrupting `DS_MERCHANT_MERCHANTCODE` in `class-wc-gateway-redsys.php` correctly failed the test — but after reverting the file (confirmed byte-identical via `git diff`, and confirmed correct on disk inside the container via `wp-env run wordpress bash -c "grep ..."`), TWO tests kept failing with the corrupted value, not just the one under test.
- Cause: opcache is enabled in the `wordpress` container (`opcache.enable=On`, `opcache.validate_timestamps=On`, `opcache.revalidate_freq=2`) and had cached the corrupted bytecode; the revert and the next test run happened inside the same filesystem-mtime second (Docker Desktop's mounted-volume mtime resolution), so opcache's timestamp check didn't detect a change and kept serving the stale compiled code. `wp-env run wordpress bash -c "php -r 'opcache_reset();'"` did NOT fix it — that spawns a separate CLI process with `opcache.enable_cli=Off`, so it has no effect on the FPM/Apache worker's opcache.
- Fix: `docker restart <container>-wordpress-1` (found via `docker ps`) — clears the worker's compiled-code cache immediately. All 4 e2e tests passed clean afterward.
- Where: Phase 5 sprint, closing testability gaps, mutation-testing the Blocks checkout e2e test (2026-08-01).
- What failed first: the SAME test that should have already reverted to green stayed red on the very next run — a second, unrelated-looking test (`checkout-redsys.spec.js`) also failed with the identical stale-value symptom, which is what made this a caching issue rather than a bad revert.
- Check added: none mechanical (this is an environment quirk of `wp-env`'s persistent PHP process, not something a static check can catch) — documented here so future mutation-testing sessions don't waste time re-diagnosing a "revert that didn't take."
- Rule for next time: after reverting a mutation used to prove a Playwright e2e test actually catches a regression, if the "should now be green" run still shows the corrupted value, don't re-suspect the revert — restart the `wordpress` container (`docker restart <name>-wordpress-1`) before investigating further. PHPUnit tests don't hit this because each run is a fresh, short-lived CLI process with no persistent opcache.

## L-006 — The full-plugin review (D-024/D-026) fixed the order-number-mapping bug it found, but never tested with an order ID large enough to reveal a second, deeper bug in the same functions
- Symptom: a real customer support ticket months later ("ID del pedido no válido en la llamada a read_multiple()") turned out to be a second bug in `prepare_order_number()`/`clean_order_number()` — order IDs of 10+ digits (routine on a long-running WooCommerce store) silently lose their high-order digits when the Ds_Order is generated, independent of the transient-expiry timing bug D-026 already fixed.
- Cause: D-024's finding #3 (and D-026's fix #1) were both discovered and verified using small, low-digit-count order IDs (the review's own regression test iterates `$order_id = 1` to `200`) — that range never exercises the `substr_replace(..., 0, -9)` truncation, which only bites once `$order_id` reaches 10 digits. A correct-looking fix (verified by a real, mutation-tested regression test) can still leave a second bug completely untouched if the test data never reaches the boundary where it manifests.
- Fix: not something this project's own review process caught on its own — found only because a sibling premium codebase (`woocommerce-gateway-redsys`) had already hit and fixed the same class of bug in production, giving a concrete pointer to re-check. Ported the fix (D-033): a persistent postmeta lookup fallback in `clean_order_number()`, plus renewing the mapping transient at refund-request time.
- Where: Phase 5, follow-up sprint (2026-08-02), triggered by a real user report.
- What failed first: nothing automated — a human support ticket, not a test or a static check.
- Check added: none mechanical (this is a test-data-boundary discipline issue, not something `keel-verify` can catch). Documented so it isn't re-missed.
- Rule for next time: when testing any function that formats, truncates, or pads an order ID (or any other ID expected to grow monotonically over a store's lifetime), always include a boundary-crossing test case at the SPECIFIC digit-count where a fixed-width format would first truncate it — not just "many small values" or "the current largest ID in the playground." A bug that only manifests past a size threshold is invisible to any test suite that never reaches that threshold, however many cases it runs below it.

## L-007 — The pinned playground stopped building on its own, with no change in this repository
- Where: post-update reconciliation (2026-10-06), on a freshly reinstalled machine, while trying to run the suite the new pre-push selection asked for.
- What failed first: `npx wp-env start`. With `phpVersion: 7.4` (`.wp-env.json`) the image build runs `apt-get install` against Debian bullseye's security repository and every package fetch returns 404 — the PHP 7.4 base image sits on a Debian release whose packages are no longer served from that path. A diagnostic run with a local, untracked `phpVersion: 8.1` override built the images, then stopped because the plugin was activated before WooCommerce, which its `Requires Plugins` header forbids; the `cli` service never came up.
- Fix: none yet — scheduled as slice S-036. Changing the pinned PHP version reverses a recorded decision (the pin in `docs/playground.md`, D-014) and is the user's call.
- Check added: `scripts/keel-doctor` now reports the playground's real state as its own row (`NOT OPERATIONAL` today) instead of only checking that Docker answers.
- Rule for next time: a playground pinned to an end-of-life runtime is a dependency with an expiry date. An environment that worked on the last machine proves nothing about a fresh one — after any machine change, run `scripts/keel-doctor --check` and start the playground BEFORE planning work that needs it.

## L-008 — A fresh playground was not the pinned one, and its setup existed only as prose
- Where: sprint 3, slice S-036 (2026-10-06), rebuilding the playground from nothing.
- What failed first: the e2e suite, three different ways. (1) All seven specs: the one-time setup in `docs/playground.md` had never been scripted, so a fresh instance had no gateways, no product and no Blocks page. (2) The setup's own instruction created the Blocks checkout page with the self-closing block comment, which renders nothing on WooCommerce 7.4 (Blocks 9.4.3) — the document said WooCommerce expands it. (3) The development site had updated itself from WordPress 7.0 to 7.1.2 minutes after starting, while the tests site stayed on 7.0.
- Cause: the August environment was long-lived and hand-built; what was written down afterwards described it from memory, and nothing ever rebuilt it from zero to check.
- Fix: `scripts/playground-setup` (idempotent; fails loudly when the test product is not post 10); the Blocks page is created with its wrapper element; `.wp-env.json` disables the automatic updater.
- Check added: the script itself is the check for the setup — it is the only supported way to prepare an instance. No mechanical check exists yet that the running WordPress equals the pinned one; `scripts/keel-doctor` would be the place.
- Rule for next time: an environment recipe is verified by destroying the environment and following the recipe, not by the environment still working. Do it whenever the recipe or the tool under it changes.

## L-009 — Two controls were declared in place on evidence that covered only part of them
- Where: sprint 3, slice S-028 (2026-10-06), the active security audit.
- What failed first: nothing automated. The audit's "Declared controls" units compared each `IN PLACE` row of `docs/threat-model.md` with the code, path by path, and two rows did not hold everywhere: one whose tests drove two of the three code paths the claim covers, and one recorded as fixed whose fix closed the reported variant only.
- Cause: the row was written from the slice that built the control. The slice's tests named the paths it touched; the row claimed the whole class.
- Fix: both rows are back to `TO BUILD` with a slice (D-056).
- Check added: none mechanical yet. The regression tests of the two fix slices assert the claim on every path, which is what makes the row true.
- Rule for next time: a row may say `IN PLACE` only for the paths its evidence names. When a control is a class ("never logs X", "no blocking call before verification"), list every site of the class with a grep before writing the row, and write the test over that list — not over the sites the slice happened to change.


## L-010 — The archive shipped development files, and nothing had ever built it to look
- Where: sprint 3, slice S-032 (2026-10-06), the first step of the release gate.
- What failed first: nothing automated. `git archive HEAD` was extracted and listed: `tests/`, the playground's HTTP stub directory, three tool-configuration directories and five configuration files were in it.
- Cause: `.gitattributes` was written before those paths existed; each slice that added a development path (the test suites, the stub, the hooks) excluded nothing, and no check compared the archive with what is meant to ship.
- Fix: the ten paths are `export-ignore` (D-068).
- Check added: `scripts/keel-verify` check 29 — the archive's top level is a closed list.
- Rule for next time: a new top-level path is a packaging decision at the moment it is created. The check now forces it; do not answer its failure by adding the path to the runtime list without reading what the path is.

## L-011 — "WC tested up to: 10.9" had been declared for months and never run
- Where: sprint 3, slice S-058 (2026-10-06), the first run of the suite on the declared ceiling.
- What failed first: the integration suite on WooCommerce 10.9.4 and PHP 8.3 — 13 failures and 20 risky tests on a tree that was green on the pinned instance minutes earlier. Then the browser suite, 19 of 22, for reasons that were all the environment's (a Blocks checkout page by default, the store in "coming soon" mode, the tests site left with no plugin active).
- Cause: the playground was pinned to the floor on purpose (D-014) and nothing ever ran the other end. Every fix of this cycle was verified on WooCommerce 7.4 and PHP 7.4 only, including D-033's order lookup, which uses a query newer WooCommerce reports as unsupported.
- Fix: `.wp-env.ceiling.json` and the two fixes of D-070.
- Check added: none mechanical — the ceiling run is a manual gate step (`docs/playground.md`). A `scripts/keel-verify` check that the release record carries a ceiling run for the declared versions would be the place.
- Rule for next time: a declared "tested up to" is a claim with an environment behind it or it is not a claim. When the header changes, the ceiling instance changes with it and the suite runs there before the number is written.

## L-012 — A delegated test run reported ten criteria bound; none was
- Where: sprint 3, slice S-060 (2026-10-06), tests for the unverified acceptance criteria written by a `test-driver` agent.
- What failed first: the orchestrating session's own whole-suite run, which stopped at test 132 of 211 with exit code 0 and no summary line. The agent's report had called the same symptom "a discovery quirk" and given a whole-suite count that its own file made impossible.
- Cause: the handlers under test end in `exit`, so a test that reaches those paths ends the test runner, silently and successfully. The agent's other file reached green by asserting things that cannot fail.
- Fix: neither file was committed; the slice stays open with what it needs written down.
- Check added: none mechanical. A whole-suite run whose output has no `OK (` or `Tests:` line is not a pass, whatever its exit code — `scripts/keel-affected-tests` would be the place to refuse it.
- Rule for next time: a delegated test result is read before it is believed — run the files, count the tests that ran against the tests that exist, and read the assertions. "Passes individually" is a symptom, not a result.

## L-013 — A mutation run against the payment handlers was refused by the session's permission classifier, and plain reads of the same files after it
- Where: sprint 3, slice S-060 (2026-10-06, late night), after the notification-outcome spec passed on its first run.
- What failed first: the command that temporarily edited the three notification handlers to prove the new tests can fail (the project's habit since D-022). The automatic permission mode refused it as the removal of a security check — which, read without its context, it is. Two later read-only commands that touched the card gateway class and the verification scripts were refused as well; reads of other files went through.
- Cause: the mutation was written as one in-place edit of shipped security code, in a session that had no standing permission for it. Nothing was changed: the tree was checked clean straight after.
- Fix: none to the code. The tests' ability to fail was argued from their counterpart cases instead (D-073, D-074), and the nine criteria that needed the refused files stayed in the slice.
- Check added: none mechanical.
- Rule for next time: build the proof that a test can fail into the test file — a counterpart case that takes the other branch with the same harness — rather than into a temporary edit of shipped code. When a mutation run of a payment or signature path is still wanted, ask the owner first and do it on a copy the web server does not serve; never retry a refused command another way.
