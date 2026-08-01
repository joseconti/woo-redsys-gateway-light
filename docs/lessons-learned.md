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
