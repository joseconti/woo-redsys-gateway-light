# 02 — Functional Spec

> **Adopted project — reconstructed as-built, progressive backfill.** Enough depth to work safely now; each area is deepened the next time a slice touches it. Inferences not confirmed by the user or a test are labeled `as-built, unverified`.

## Features / flows

### F1 — Card payment via Redsys redirection (gateway `redsys`)
- Customer selects "Redsys" at checkout (classic or Blocks) → order created as `pending` → customer redirected to Redsys's hosted payment page with a signed request → Redsys redirects back to the store's notification URL (`?wc-api=WC_Gateway_redsys`) with a signed response.
- `RedsysLiteAPI::create_merchant_signature_notif()` (`includes/class-redsysliteapi.php`) verifies the HMAC_SHA256_V1 signature against the merchant's configured SHA-256 secret (`classes/class-wc-gateway-redsys.php`, `check_ipn_response` handler, lines ~868–943).
- **Fails closed**: if no SHA-256 secret is configured in the gateway settings, the notification is rejected rather than trusted (hardened in 7.0.1/7.0.2 per the readme.txt changelog — `as-built, unverified`: not re-tested during adoption, taken from the changelog + current code reading).
- Order status transitions (`class-wc-gateway-redsys.php`): amount mismatch between order total and notification total → `on-hold` (line ~1026); successful confirmation → `completed`/payment-complete (line ~1056); Redsys-side cancellation → `cancelled` (line ~1068).
- Extensibility: `do_action('valid_redsys_standard_ipn_request', $post_data)` fires on every valid notification; `apply_filters('woocommerce_redsys_args', $redsys_args)` filters the outgoing signed request; `apply_filters('woocommerce_redsys_icon', ...)` filters the checkout icon.

### F2 — Bizum payment (gateway `bizumredsys`)
- Same redirection + signature-verification shape as F1, dedicated gateway class `class-wc-gateway-bizum-redsys.php`, own icon (`bizum.png`), own hook names (`valid_bizumredsys_standard_ipn_request`, `woocommerce_bizumredsys_icon`).
- **Known gap** (GitHub issue #10, "Falta carga de opción orderdo en Bizum" — untriaged in depth yet): a reported missing settings-load behavior. See `docs/issues.md`.

### F3 — Apple Pay / Google Pay via redirection (gateway `googlepayredirecredsys`)
- Redirection-based, not the native Payment Request API — `class-wc-gateway-googlepay-redirection-redsys.php`. Adds `do_action($this->id . '_post_payment_complete', $order_id)` and `_post_payment_error` hooks beyond the F1 shape (lines ~1204, ~1254).

### F4 — Inespay bank-transfer redirection (gateway `inespayredsys`)
- Redirection-based bank payment, `class-wc-gateway-inespay-redsys.php`. Uses a `handle_callback` notification handler (rather than the shared `check_ipn_response` pattern) and its own `do_action('inespay_post_payment_complete', $order_id)` (line 602). The readme.txt 7.0.1/7.0.2 changelog documents a notification-forgery fix specific to this gateway — `as-built, unverified`.

### F5 — WooCommerce Blocks checkout support (all four gateways)
- `includes/blocks/*-support.php`, one per gateway, backed by the compiled `assets/js/frontend/blocks.js` (source: `resources/js/frontend/index.js`, built via `@wordpress/scripts`/webpack). Registers each gateway as a WooCommerce Blocks-compatible payment method so the modern block-based checkout works alongside the classic checkout.

### F6 — Admin settings per gateway
- Each gateway class implements WooCommerce's Settings API (`WC_Payment_Gateway::init_form_fields()`). Fields observed in the main Redsys gateway (representative of the pattern, ~38 fields): merchant code, terminal, currency, SHA-256 secret + test secret, enable/disable, title, description, icon, order-status mapping options. Rendered under WooCommerce → Settings → Payments → [gateway].

### F7 — Upsell / cross-sell admin notice
- `includes/class-redsys-lite-apps-plugins.php` + `assets/css/welcome.css` — an admin-facing widget promoting the premium plugin and related apps. Filterable via `redsys_lite_apps_plugins_mac_app` / `_free` / `_premium` / `_webs` / `_skills` / `_profiles` (internal/marketing data, not payment-relevant).

## Data model
No custom database tables or post types. State lives in:
- WooCommerce's own `wp_options` (gateway settings, one option group per gateway) and order meta (via `WC_Order` methods) — no direct `$wpdb` queries observed in the sampled gateway class.
- Standard WooCommerce order status field, transitioned through the gateway's own logic (F1–F4 above).

## Integrations
- **Redsys** (external payment processor) — the plugin's core integration; redirection + signed request/response, no SDK, hand-rolled in `includes/class-redsysliteapi.php`.
- **WooCommerce core** — `WC_Payment_Gateway` base class, order API, Settings API, Blocks checkout extensibility API.
- **WooCommerce Blocks** — via `@woocommerce/dependency-extraction-webpack-plugin` and the blocks-support classes.

## Permissions
No custom capabilities defined. Settings screens rely on WooCommerce's own admin capability gating (`manage_woocommerce`, standard WordPress admin access) — `as-built, unverified`: not traced to a specific capability check in the sampled code, this is WooCommerce's default behavior for its Settings API pages.

## Change map (recurring change types → what must be touched)

| Change type | Must touch |
|---|---|
| New gateway setting (e.g. a new form field) | the gateway class's `init_form_fields()`, its `process_payment`/notification handler if the setting affects behavior, `readme.txt` changelog, `docs/api/INDEX.md` if it's a new filter/action, `docs/03-technical-plan.md` code map if it's a new file |
| New gateway (5th payment method) | new class in `classes/`, matching `includes/blocks/*-support.php`, icon asset in `assets/images/`, registration in `woocommerce-redsys.php`, `readme.txt` description + changelog, `docs/api/INDEX.md` for its new hooks, `docs/02-functional-spec.md` new F-entry |
| Signature/security-relevant change (anything touching `RedsysLiteAPI` or a notification handler) | the class itself, a regression test if the test suite exists by then, `readme.txt` changelog (security fixes are historically called out explicitly), `docs/threat-model.md` control state, `docs/lessons-learned.md` if it fixes a real incident |
| Version bump | `woocommerce-redsys.php` header `Version:` + `REDSYS_WOOCOMMERCE_VERSION` constant, `readme.txt` `Stable tag:` + changelog entry, `package.json` `version` (currently drifted — see PROGRESS.md deferred items) |
| Translation-affecting change (new/changed user-facing string) | wrap in `__()`/`_e()` family with the `woo-redsys-gateway-light` text domain, regenerate `.pot` (`npm run i18n:pot`), `languages/` `.po`/`.mo`/`.json` for `es_ES` |
| Front-end JS/CSS change | edit the source (`resources/js/frontend/index.js`), rebuild via `npm run build` (regenerates `assets/js/frontend/blocks.js` + `.asset.php`) — no minified-CSS pairing exists yet, see the build-assets gap in `docs/04-adoption-audit.md` |

## Acceptance criteria
Not reconstructed line-by-line for existing features (progressive backfill) — each feature's acceptance criteria will be written the first time that feature's area is next touched by a slice, per the adoption progressive-backfill rule.

## Testing
**No automated test suite exists today** (no phpunit, no jest — confirmed in the inventory). See `docs/04-adoption-audit.md` (Testability) for the gap and remediation proposal, and `docs/PROGRESS.md` deferred items.
