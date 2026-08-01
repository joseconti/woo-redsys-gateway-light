# Issues — Payment Gateway for Redsys & WooCommerce Lite

> Living log of forge issues (GitHub, `joseconti/woo-redsys-gateway-light`). Inventory first, one entry per issue worked.
> Updated the moment an issue is triaged, worked, or closed.
> Last inbound sweep: 2026-08-01 17:50 (full triage — every issue's body/comments read, current code checked against each report)

## Inventory
| # | Title | Type | Priority | Status | Entry |
|---|-------|------|----------|--------|-------|
| 93 | wc_enqueue_js() deprecada desde WooCommerce 10.4 (avisos en la página de pago) | bug | medium | resolved (fix implemented, unreleased) | E-001 |
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

## Stale issues found already resolved (verified against current v7.0.2 code, no new work needed)

These predate this adoption and were never closed even though the underlying code has been fixed (in some cases for years) or the report turned out to be a configuration question rather than a plugin bug. Commented on each (in the language the issue was written in) confirming the current state, without closing any of them — per Keel's "never close an issue on its own reading of the code" rule, closing is left to the reporter's confirmation or the maintainer's own call.

- **#12** (2022) — `$order_id` used before being set in `class-wc-gateway-bizum-redsys.php`. Confirmed fixed: current code consistently uses `$order->get_id()`. Owner already replied "ya lo he solucionado" in 2022; the fix has shipped for years.
- **#10** (2022) — missing `orderdo` option load in the Bizum gateway. Confirmed fixed: `$this->orderdo = $this->get_option('orderdo')` and its settings-field definition both exist in current code, matching the owner's 2022 reply.
- **#9** (2021) — `$orders->count()` called on an array returned by `wc_get_orders()`. Confirmed fixed: current `class-wc-gateway-redsys-psd2-light.php` uses `$orders->total` (via `'paginate' => true`) and `$orders->orders[0]`, matching the owner's final "Ya está resuelto" comment from the same thread.
- **#34** (2024) — missing text domain on a string in `about-redsys.php` line 118. The file has since been substantially shortened (42 lines today); the specific string no longer exists, and every remaining `__()`/`esc_html_e()` call in the file uses the correct `woo-redsys-gateway-light` domain. Moot, not "fixed" in the sense of a targeted patch.
- **#21** (2024) — a string in `includes/data/number-order-type.php` looked wrong. Confirmed the owner's own 2024 reply: that file is never `require`d/`include`d anywhere in the Lite codebase (dead code left over from the premium version). Won't-fix for Lite; low-priority hygiene item (unused file) noted in `docs/04-adoption-audit.md`, not worth a code change on its own.
- **#2** (2021) — "operación denegada" turned out to be a PSD2-terminal-configuration question, resolved via conversation with the reporter's bank/terminal setup, not a plugin defect.
- **#1** (2018) — "not passing completed order" turned out to be a hosting firewall (Wordfence) blocking Redsys's Java-User-Agent callback IP, resolved via conversation, not a plugin defect.
