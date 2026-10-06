---
paths:
  - "woocommerce-redsys.php"
  - "about-redsys.php"
  - "classes/**/*.php"
  - "includes/**/*.php"
  - "resources/js/**/*.js"
  - "assets/css/**/*.css"
  - "languages/**"
  - "tests/**"
---

# Code style — Payment Gateway for Redsys & WooCommerce Lite

Source of truth: docs/03-technical-plan.md §Observed conventions. On any conflict, the plan wins — fix this file. Conventions here are OBSERVED from shipped code (adoption, D-002): never restyle existing code to a different taste.

- Prefix: gateway IDs are lowercase with no separators (`redsys`, `bizumredsys`, `googlepayredirecredsys`, `inespayredsys`); gateway classes are `WC_Gateway_<Name>_Redsys`; constants are `REDSYS_*`; shared helpers go through `WCRedL()`.
- Naming: class files are `class-wc-gateway-<name>-redsys.php` (kebab-case, `class-` prefix); one `includes/blocks/*-support.php` per gateway.
- Hooks: filters are `woocommerce_<gateway_id>_<thing>` (e.g. `woocommerce_redsys_icon`); the post-verification action is `valid_<gateway_id>_standard_ipn_request`. New hooks follow the pattern — do not copy the Bizum `_args` filter that reuses the card gateway's name (docs/api/INDEX.md records it as an anomaly).
- Coding standard: `phpcs.xml` (`WooCommerce-Core` + `WordPress-Extra`). Shipped PHP must stay valid for `Requires PHP: 7.0`; only `tests/` may assume PHP 7.4 (D-016).
- Error handling: the plan records no single strategy — match the method you are in. Notification handlers fail closed (docs/threat-model.md).
- Logging: `WC_Logger` through the gateway's `debug` setting (`$this->log->add( '<gateway_id>', ... )`) or `WCRedL()->debug()`. Never log a signing secret, API key or token (D-025).
- i18n: base language English; every user-facing string goes through the `__()` / `_e()` / `esc_html__()` family with the text domain `woo-redsys-gateway-light`, never concatenated. A new or changed string means regenerating the `.pot` (`npm run i18n:pot`) and updating `languages/` for `es_ES` (change map, docs/02-functional-spec.md).
- Comments: docblocks on every class, property and function you add or change (purpose, `@param`, `@return`, `@since`); comment the why of non-obvious decisions. English only (D-004).
- Front-end build: `resources/js/frontend/index.js` is the SOURCE; `assets/js/frontend/blocks.js` and `blocks.asset.php` are build output from `npm run build` — never hand-edit them. CSS in `assets/css/` is hand-written and unminified; the source/minified pairing is its own scheduled slice (D-038, S-027) — do not retrofit it inside another change.
- No JS unit-test tooling (Jest) is added for the Blocks bundle (D-031).
