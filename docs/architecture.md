# Architecture — Payment Gateway for Redsys & WooCommerce Lite

> Consolidated from `docs/02-functional-spec.md` and `docs/03-technical-plan.md` (as-built, reconstructed during adoption).

## Overview
A WordPress plugin that registers four WooCommerce payment gateways (Redsys card, Bizum, Apple/Google Pay redirection, Inespay), each following the same shape: a `WC_Payment_Gateway` subclass that (1) renders WooCommerce Settings API admin fields, (2) builds a signed redirection request to the external processor at checkout, and (3) verifies a signed notification on return via a shared WooCommerce notification URL (`?wc-api=WC_Gateway_<id>`), transitioning the order's status accordingly.

## Components

```
woocommerce-redsys.php  (bootstrap)
        │
        ├── classes/class-wc-gateway-redsys-global-lite.php   (shared base/utility logic)
        │
        ├── classes/class-wc-gateway-redsys.php                ─┐
        ├── classes/class-wc-gateway-bizum-redsys.php            │  one WC_Payment_Gateway subclass
        ├── classes/class-wc-gateway-googlepay-redirection-...    │  per payment method, same shape
        ├── classes/class-wc-gateway-inespay-redsys.php          ─┘
        │        │
        │        ├── uses includes/class-redsysliteapi.php   (RedsysLiteAPI — signature build/verify)
        │        ├── uses includes/data/*                     (currencies, countries, error codes, status maps)
        │        └── paired with includes/blocks/*-support.php (WooCommerce Blocks checkout registration)
        │
        └── includes/class-redsys-lite-apps-plugins.php        (admin upsell widget — no payment logic)
```

Front-end: `resources/js/frontend/index.js` (source) → built by `@wordpress/scripts`/webpack → `assets/js/frontend/blocks.js` + `.asset.php` (the runtime bundle WooCommerce Blocks loads for the checkout payment-method registration of all four gateways).

## Request/response flow (representative — Redsys card gateway; Bizum and Google/Apple Pay redirection follow the same shape, Inespay uses its own `handle_callback`)

1. Customer checks out, selects the gateway → order created `pending`.
2. Gateway class builds the signed redirection payload (`RedsysLiteAPI`), filterable via `apply_filters('woocommerce_redsys_args', ...)`.
3. Customer is redirected to Redsys's hosted payment page (outside this plugin's control).
4. Redsys redirects back to `?wc-api=WC_Gateway_redsys` with a signed response.
5. `check_ipn_response()` verifies the HMAC_SHA256_V1 signature against the configured secret — **fails closed** if no secret is configured.
6. Order total is cross-checked against the notification total; mismatch → `on-hold`. Match → order marked paid/`completed`. Redsys-side cancellation → `cancelled`.
7. `do_action('valid_redsys_standard_ipn_request', $post_data)` fires for third-party extensibility once the notification is trusted.

## Data flow
No custom tables. All state lives in WooCommerce's own `wp_options` (gateway settings, one group per gateway) and `WC_Order` meta/status, accessed through WooCommerce's own APIs — no direct `$wpdb` queries observed in the sampled code.

## External dependencies
- **Redsys** — card/Bizum/Apple-Google-Pay-redirection processor; redirection + signed request/response protocol, hand-rolled client (`RedsysLiteAPI`), no SDK.
- **Inespay** — bank-transfer redirection processor; own notification shape (`handle_callback`).
- **WooCommerce core** (`WC_Payment_Gateway`, order API, Settings API) and **WooCommerce Blocks** (checkout extensibility API via `@woocommerce/dependency-extraction-webpack-plugin`).

## Extension points
See `docs/api/INDEX.md` for the full list of actions/filters this plugin exposes to third-party code (per-gateway `_standard_ipn_request` actions, `_args`/`_icon` filters, plus the internal upsell-widget filters).

## Known architectural gaps
See `docs/04-adoption-audit.md` for the full gap audit; the headline ones: no automated test suite, no CSS minification pipeline, `## Environment requirements` / `scripts/keel-doctor` / `scripts/keel-verify` not yet built (all deferred to the first real Phase 5 sprint, per `docs/keel-conformance.md`).
