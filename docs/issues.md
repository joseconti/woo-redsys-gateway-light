# Issues — Payment Gateway for Redsys & WooCommerce Lite

> Living log of forge issues (GitHub, `joseconti/woo-redsys-gateway-light`). Inventory first, one entry per issue worked.
> Updated the moment an issue is triaged, worked, or closed.
> Last inbound sweep: 2026-10-21 15:41 (sprint 3 close — six open issues, no new issue or comment since the 13:45 sweep. Earlier, 13:45, session close — `gh issue list --state all`: six open issues, none new since #112 on 2026-09-16; no new comment; seventeen open Dependabot PRs (#94–#116, development dependencies only), triaged 2026-10-21 by S-089 — all superseded on `develop`, see "Dependabot pull requests" below. Outside the forge, two WordPress.org support threads report D-023 on 7.0.2 — recorded in `docs/PROGRESS.md`; replies drafted, not posted, waiting for the owner)

## Inventory
| # | Title | Type | Priority | Status | Entry |
|---|-------|------|----------|--------|-------|
| 112 | Mensaje "Your order can no longer be cancelled" al cancelar el usuario el pago en Redsys | bug | medium | resolved (fix ships in 7.1.0, not yet published) | E-002 |
| 93 | wc_enqueue_js() deprecada desde WooCommerce 10.4 (avisos en la página de pago) | bug | medium | resolved (fix ships in 7.1.0, not yet published) | E-001 |
| 34 | Falta el text domain | bug | low | resolved (moot — file rewritten since) | — |
| 21 | Posible error en string | bug | low | won't fix (dead code, not used in Lite) | — |
| 12 | PHP Fatal error: Uncaught Error: Call to a member function update_meta_data() on bool | bug | high | resolved (already fixed in shipped code) | — |
| 10 | Falta carga de opción orderdo en Bizum | bug | medium | resolved (already fixed in shipped code) | — |
| 9 | Error en la llamada $orders->count | bug | medium | resolved (already fixed in shipped code) | — |
| 2 | operación denegada | support | — | resolved (PSD2 terminal config, not a bug) | — |
| 1 | Not passing completed order | support | — | resolved (hosting/firewall config, not a bug) | — |

## Entries (one per issue worked)

### E-001 — #93 wc_enqueue_js() deprecada desde WooCommerce 10.4
- Link: https://github.com/joseconti/woo-redsys-gateway-light/issues/93   Status: fix landed, unreleased (awaiting a version bump + release before it reaches users)
- Diagnosis: `wc_enqueue_js()` was deprecated in WooCommerce 10.4 in favor of `wp_add_inline_script()`. Used in 4 call sites: `classes/class-wc-gateway-redsys.php` (lines ~717 and ~755, the two branches of `generate_redsys_form()`), `classes/class-wc-gateway-bizum-redsys.php` (~889), `classes/class-wc-gateway-googlepay-redirection-redsys.php` (~742). Each builds a blockUI overlay + auto-submit script for the redirect-to-Redsys receipt page.
- Resolution: replaced each `wc_enqueue_js( '...' )` call with `wp_add_inline_script( 'woocommerce', 'jQuery( function( $ ) {...} );' )`, using the exact wrapper the reporter proposed — `wc_enqueue_js()` auto-wrapped its argument in `jQuery(function($){...})`; `wp_add_inline_script()` does not, so the wrapper is now explicit in the string itself, preserving the `$` alias the existing code relies on.
- Changes: `classes/class-wc-gateway-redsys.php`, `classes/class-wc-gateway-bizum-redsys.php`, `classes/class-wc-gateway-googlepay-redirection-redsys.php` — not yet committed/released as of this entry (see `docs/PROGRESS.md` for the exact commit once it lands).
- Verification: see `docs/05-test-points.md` — `php -l` clean on all 3 files, 0 remaining `wc_enqueue_js` occurrences, `receipt_page()` invoked directly on a real pending order in the wp-env playground (WooCommerce 7.4.0) produced correct HTML with no PHP fatal/error. **Honest caveat:** the original deprecation warning could not be reproduced (playground is pinned to WC 7.4.0, which predates the 10.4 deprecation), and whether `wp_add_inline_script()` actually attaches the script tag on a real front-end page load was not independently confirmed (CLI eval context doesn't register the `woocommerce` script handle the way a real page load does) — tagged `VERIFY`, not claimed as fully proven.
- Replies: beat 1 — fix-landed comment posted 2026-08-01 (see GitHub, in Spanish since the reporter wrote in Spanish): fix implemented, thanks for the precise report and proposed fix, will ship in the next version. Issue left OPEN, not closed by Keel.
- Deploy: not yet — no release has been cut since this fix landed. `docs/PROGRESS.md` should track "needs a release before this reaches the reporter" as an open item.
- Closed by: still open — awaiting the next release + reporter confirmation, or the maintainer's own decision to close given how precisely-specified and verifiable the fix was.
- Inbound: none since the sweep (report has no earlier maintainer reply).
- Lesson: none recorded — straightforward, well-specified fix.
- Pending: a version bump + release (Phase 7) before the reporter can actually test this; then beats 2–3 of the reply lifecycle.

### E-002 — #112 "Your order can no longer be cancelled" al cancelar el usuario el pago en Redsys
- Link: https://github.com/joseconti/woo-redsys-gateway-light/issues/112   Status: fix landed on `develop`, unreleased
- Diagnosis: two defects, the first hiding the second. `DS_MERCHANT_URLKO` was built with `WC_Order::get_cancel_order_url()`, which is escaped for HTML, so the browser returned with `amp;`-prefixed parameters and WooCommerce's cancel handler never ran (the reporter's WPML add-on decoded the URL by accident, which is why it surfaced there). Once the URL is correct, the handler runs on an order the notification has already set `cancelled` and WooCommerce answers with the error in the title. Both reproduced by failing tests before any change.
- Resolution: `WCRedL()->get_cancel_url_raw()` for every non-markup use in the card, Bizum and Google Pay gateways; a callback on `woocommerce_valid_order_statuses_for_cancel` that accepts the one already cancelled order named in a verified cancel request, so WooCommerce shows "Your order was cancelled." (D-053). The reporter's second option; the choice is the assistant's and awaits the maintainer's confirmation before release.
- Changes: `classes/class-wc-gateway-redsys-global-lite.php`, the three gateway classes, `woocommerce-redsys.php`, `tests/Integration/CancelUrlTest.php`, `tests/e2e/cancel-return-redsys.spec.js` — slice S-038.
- Verification: `docs/05-test-points.md`, S-038 row. Not verified: a real refused payment against Redsys, and the reporter's WPML setup.
- Replies: none yet.
- Deploy: not yet — needs the next release before the reporter can test it.
- Closed by: still open — never closed by Keel on its own reading of the code.
- Inbound: none since the report (2026-09-16).
- Lesson: none recorded.
- Pending: beat 1 of the reply lifecycle (fix landed), then a release, then beats 2–3.


## Stale issues found already resolved (verified against current v7.0.2 code, no new work needed)

These predate this adoption and were never closed even though the underlying code has been fixed (in some cases for years) or the report turned out to be a configuration question rather than a plugin bug. Commented on each (in the language the issue was written in) confirming the current state, without closing any of them — per Keel's "never close an issue on its own reading of the code" rule, closing is left to the reporter's confirmation or the maintainer's own call.

- **#12** (2022) — `$order_id` used before being set in `class-wc-gateway-bizum-redsys.php`. Confirmed fixed: current code consistently uses `$order->get_id()`. Owner already replied "ya lo he solucionado" in 2022; the fix has shipped for years.
- **#10** (2022) — missing `orderdo` option load in the Bizum gateway. Confirmed fixed: `$this->orderdo = $this->get_option('orderdo')` and its settings-field definition both exist in current code, matching the owner's 2022 reply.
- **#9** (2021) — `$orders->count()` called on an array returned by `wc_get_orders()`. Confirmed fixed: current `class-wc-gateway-redsys-psd2-light.php` uses `$orders->total` (via `'paginate' => true`) and `$orders->orders[0]`, matching the owner's final "Ya está resuelto" comment from the same thread.
- **#34** (2024) — missing text domain on a string in `about-redsys.php` line 118. The file has since been substantially shortened (42 lines today); the specific string no longer exists, and every remaining `__()`/`esc_html_e()` call in the file uses the correct `woo-redsys-gateway-light` domain. Moot, not "fixed" in the sense of a targeted patch.
- **#21** (2024) — a string in `includes/data/number-order-type.php` looked wrong. Confirmed the owner's own 2024 reply: that file is never `require`d/`include`d anywhere in the Lite codebase (dead code left over from the premium version). Won't-fix for Lite; low-priority hygiene item (unused file) noted in `docs/04-adoption-audit.md`, not worth a code change on its own.
- **#2** (2021) — "operación denegada" turned out to be a PSD2-terminal-configuration question, resolved via conversation with the reporter's bank/terminal setup, not a plugin defect.
- **#1** (2018) — "not passing completed order" turned out to be a hosting firewall (Wordfence) blocking Redsys's Java-User-Agent callback IP, resolved via conversation, not a plugin defect.

## Dependabot pull requests (triaged 2026-10-21, S-089, D-098)

Seventeen open Dependabot PRs, all against `master` and all touching `package-lock.json` only (development tools; nothing from npm ships, D-050). Each was compared with `develop`'s lockfile: **every one is already superseded there** — the package is at or above the PR's target, or no longer in the tree at all.

| PR | Bump | On `develop` |
|----|------|--------------|
| #116 | axios 1.16.0 → 1.20.0 | not in the tree |
| #115 | markdown-it 12.3.2 → 14.3.2, @wordpress/scripts 30 → 36.0.0 | markdown-it 14.3.3 (S-089), @wordpress/scripts 36.0.0 (D-055) |
| #114 | ip-address 10.2.0 → 10.7.2 | not in the tree |
| #113 | adm-zip 0.5.16 → 0.6.1, @wordpress/scripts 30 → 35.0.0 | adm-zip 0.6.1, @wordpress/scripts 36.0.0 |
| #111 | js-yaml 3.14.2 → 3.15.2 and 4.1.1 → 4.3.2 | 3.15.2, 4.3.2 (and 5.2.3) |
| #110 | joi 17.13.3 → 17.13.7 | not in the tree |
| #109 | baseline-browser-mapping 2.9.19 → 2.11.21 | 2.11.27 |
| #108 | colord 2.9.3 → 2.10.0 | 2.10.0 |
| #107 | browserslist 4.28.1 → 4.28.9 | 4.29.3 |
| #106 | fast-uri 3.1.2 → 3.1.7 | 3.1.8 |
| #105 | postcss-selector-parser 6.1.2 → 6.1.4 and 7.1.0 → 7.1.5 | 6.1.4, 7.1.6 |
| #104 | brace-expansion 1.1.13 → 1.1.18 | 1.1.21 (and 2.1.7, 5.0.12) |
| #103 | nanoid 3.3.11 → 3.3.18 | 3.3.20 |
| #99 | body-parser 1.20.5 → 1.20.6 | 1.20.8 |
| #98 | shell-quote 1.8.4 → 1.10.0 | 1.12.0 |
| #97 | immutable 4.3.8 → 4.3.9 | 5.1.9 |
| #94 | websocket-driver 0.7.4 → 0.7.5 | 0.7.5 |

- Action: none on the forge. Merging any of them into `master` would conflict with, and be overwritten by, `develop`'s lockfile when the owner merges the release; Dependabot reads the default branch, so these PRs and the alert count resolve themselves once `develop` reaches `master`. They are not closed by Keel — that is the owner's call (or Dependabot's own, after the merge).
