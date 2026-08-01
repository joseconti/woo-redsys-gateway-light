# 01 — Discovery

> **Adopted project — reconstructed as-built.** This document describes what the project IS today, reconstructed from the codebase and the readme.txt changelog, not a pre-build discovery process. Sections marked `as-built, unverified` are inferred from reading code, never confirmed against a test or the user.

## Problem / outcome
The plugin lets a WooCommerce store accept payments through Redsys (the payment processor used by most Spanish banks) without a full custom integration: card payments via redirection to the Redsys payment page, plus Bizum, Apple/Google Pay redirection, and Inespay (bank-transfer redirection) as additional gateways. It is the **Lite** version of a commercial "premium" plugin (`https://woocommerce.com/products/redsys-gateway/`), distributed free on WordPress.org as a funnel toward the paid product.

## Proposed v1 / scope as it exists today
Already built and released (current version 7.0.2):
- Redsys card gateway (redirection flow) — `redsys` gateway ID
- Bizum gateway — `bizumredsys` gateway ID
- Apple Pay / Google Pay via redirection — `googlepayredirecredsys` gateway ID
- Inespay (bank-transfer redirection) — `inespayredsys` gateway ID
- WooCommerce classic checkout AND WooCommerce Blocks checkout support for all four gateways
- Admin settings screens per gateway (WooCommerce Settings API): merchant code, terminal, currency, SHA-256 secret/test-secret, order status mapping, titles/descriptions/icons
- Notification (IPN-equivalent) handling per gateway via `woocommerce_api_wc_gateway_<id>`, with signature verification (`RedsysLiteAPI`, HMAC_SHA256_V1) that fails closed when no secret is configured
- Multi-language ready (English base, `es_ES` shipped translation)

**Later / not in this Lite version** *(as-built, unverified — inferred from the readme.txt description contrasting Lite vs Premium, not confirmed line-by-line against the premium plugin)*: the readme.txt states the premium version has "many more" features than Lite; the exact delta was not inventoried during adoption since the premium plugin's code is not in this repo.

## Project type
WordPress plugin / WooCommerce extension (D-001). Security profile: `references/security/wordpress.md`. Accessibility: WCAG 2.2 AA floor (D-007).

## Constraints
- Must stay compatible with WooCommerce's Settings API, classic checkout, AND Blocks checkout simultaneously (four gateway classes each implement both).
- Must interoperate with Redsys's external redirection + signature-verification protocol, which the plugin does not control.
- WordPress.org distribution rules apply: GPL-2.0-or-later compatible license (D-003), no external dependency bundling beyond what WordPress.org tooling allows.
- No Composer/dependency manager — the Redsys signature logic is hand-rolled (`includes/class-redsysliteapi.php`).

## Competitive scan
Not run — adoption treats the competitive scan as recommended-but-optional (per `references/adoption.md` step 3) and it was skipped for this pass. It feeds the roadmap rather than gating adoption. If useful for prioritizing new features later, it can be run then.

## Installed base
**In production, with real users and stored data.** Evidence: WordPress.org distribution, current `Stable tag: 7.0.2`, a substantial `readme.txt` changelog documenting real fixes across many prior versions, and 8 open GitHub issues from real users reporting production problems (see `docs/issues.md`). Any code change from here on must treat backward compatibility and safe upgrades as mandatory (Phase 5 rule for adopted projects with an installed base).

## i18n & accessibility (initial)
- i18n: multi-language-ready, base English, `es_ES` shipped, mechanism confirmed consistent in the sampled main gateway class (D-002).
- Accessibility: WCAG 2.2 AA floor / AAA where feasible (D-007), applies to the admin settings screens and any checkout-facing markup the plugin renders.

## License
GPL-2.0-or-later, reconciled across `woocommerce-redsys.php`, `readme.txt`, `package.json`, and a new root `LICENSE` file (D-003).

## Design system
One-off / n/a — no design system exists or is planned (D-009).

## Website intent
No (D-005).

## Client budget
No (D-010).

## Preliminary estimate
Not produced — this is an adoption of an already-shipped, mature project, not a from-zero build. Estimates for specific future work (issue fixes, new features) will be produced per `references/estimation-budget.md` when that work is scoped.
