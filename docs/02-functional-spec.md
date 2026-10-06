# 02 — Functional Spec

> **Adopted project — reconstructed as-built, progressive backfill.** Enough depth to work safely now; each area is deepened the next time a slice touches it. Inferences not confirmed by the user or a test are labeled `as-built, unverified`.

## Features / flows

### F1 — Card payment via Redsys redirection (gateway `redsys`)
- Customer selects "Redsys" at checkout (classic or Blocks) → order created as `pending` → customer redirected to Redsys's hosted payment page with a signed request → Redsys sends a signed server-to-server notification (HTTP POST) to the store's notification URL (`?wc-api=WC_Gateway_redsys`), and separately returns the customer's browser to the order-received page.
- `RedsysLiteAPI::create_merchant_signature_notif()` (`includes/class-redsysliteapi.php`) verifies the HMAC_SHA256_V1 signature against the merchant's configured SHA-256 secret (`classes/class-wc-gateway-redsys.php`: `check_ipn_request_is_valid()` line 833, `check_ipn_response()` line 898, and again inside `successful_request()` line 914).
- **Fails closed**: if no SHA-256 secret is configured in the gateway settings, the notification is rejected rather than trusted (hardened in 7.0.1/7.0.2 per the readme.txt changelog; proven by `AC-07` below).
- Order status transitions (`class-wc-gateway-redsys.php`): amount mismatch between order total and notification total → `on-hold` (line 1028); successful confirmation → `payment_complete()` (line 1056), then `completed` only when the `orderdo` setting is `completed` (line 1058); Redsys-side denial (`Ds_Response` above 99) → `cancelled` (line 1070).
- Extensibility: `do_action('valid_redsys_standard_ipn_request', $post_data)` fires on every valid notification; `apply_filters('woocommerce_redsys_args', $redsys_args)` filters the outgoing signed request; `apply_filters('woocommerce_redsys_icon', ...)` filters the checkout icon.

### F2 — Bizum payment (gateway `bizumredsys`)
- Same redirection + signature-verification shape as F1, dedicated gateway class `class-wc-gateway-bizum-redsys.php`, own icon (`bizum.png`), own hook names (`valid_bizumredsys_standard_ipn_request`, `woocommerce_bizumredsys_icon`).
- **Reported gap, not present in the current code** (GitHub issue #10, "Falta carga de opción orderdo en Bizum"): the `orderdo` field is defined (`classes/class-wc-gateway-bizum-redsys.php:477`) and loaded (`:315`) today. The issue itself is tracked in `docs/issues.md`; closing it is the reporter's or the maintainer's act.

### F3 — Apple Pay / Google Pay via redirection (gateway `googlepayredirecredsys`)
- Redirection-based, not the native Payment Request API — `class-wc-gateway-googlepay-redirection-redsys.php`. Adds `do_action($this->id . '_post_payment_complete', $order_id)` and `_post_payment_error` hooks beyond the F1 shape (lines 1214, 1264).

### F4 — Inespay bank-transfer redirection (gateway `inespayredsys`)
- Redirection-based bank payment, `class-wc-gateway-inespay-redsys.php`. Uses a `handle_callback` notification handler (rather than the shared `check_ipn_response` pattern) and its own `do_action('inespay_post_payment_complete', $order_id)` (line 628). The readme.txt 7.0.1/7.0.2 changelog documents a notification-forgery fix specific to this gateway; proven by `AC-36` and `AC-37` below.

### F5 — WooCommerce Blocks checkout support (all four gateways)
- `includes/blocks/*-support.php`, one per gateway, backed by the compiled `assets/js/frontend/blocks.js` (source: `resources/js/frontend/index.js`, built via `@wordpress/scripts`/webpack). Registers each gateway as a WooCommerce Blocks-compatible payment method so the modern block-based checkout works alongside the classic checkout.

### F6 — Admin settings per gateway
- Each gateway class implements WooCommerce's Settings API (`WC_Payment_Gateway::init_form_fields()`). The main Redsys gateway defines 16 fields (`classes/class-wc-gateway-redsys.php`, `init_form_fields()` line 404); Bizum defines 15, Google Pay redirection 12 and Inespay 12. Every key, type, default and effect is listed in `docs/usage/configuration.md`. Rendered under WooCommerce → Settings → Payments → [gateway].

### F7 — Upsell / cross-sell admin notice
- `includes/class-redsys-lite-apps-plugins.php` + `assets/css/welcome.css` — an admin-facing widget promoting the premium plugin and related apps. Filterable via `redsys_lite_apps_plugins_mac_app` / `_free` / `_premium` / `_webs` / `_skills` / `_profiles` (internal/marketing data, not payment-relevant).

### F8 — Refunds from the WooCommerce order screen (all four gateways)
- Every gateway declares `supports = products, refunds` and implements `process_refund( $order_id, $amount, $reason )`.
- Redsys card, Bizum and Google Pay redirection send a signed transaction-type-`3` request to Redsys and then poll for the refund notification that Redsys posts back to the same notification URL (`Ds_TransactionType` `3`, `Ds_Response` `900`). Inespay calls its own refund API and returns as soon as the API accepts the request. See `docs/flows/refund.md`.

### F9 — Order payment details and platform declarations
- `add_redsys_meta_box()` (`woocommerce-redsys.php` line 285, on `woocommerce_admin_order_data_after_billing_address`) prints the Redsys order number, date, hour and authorisation code on the admin order screen; `mostrar_numero_autentificacion()` (line 478, on `woocommerce_thankyou_order_received_text`) replaces the thank-you text with the transaction details. Both apply only to orders whose payment method is listed by `redsys_return_types()` (`includes/data/redsys-types.php`): `redsys`, `bizumredsys`, `googlepayredirecredsys` — Inespay orders are not included.
- The plugin declares WooCommerce High-Performance Order Storage compatibility (`custom_order_tables`, `woocommerce-redsys.php` line 52).

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
| Version bump | `woocommerce-redsys.php` header `Version:` + `REDSYS_WOOCOMMERCE_VERSION` constant, `readme.txt` `Stable tag:` + changelog entry, `package.json` `version` (checked by `scripts/keel-verify` since D-050) |
| Translation-affecting change (new/changed user-facing string) | wrap in `__()`/`_e()` family with the `woo-redsys-gateway-light` text domain, regenerate `.pot` (`npm run i18n:pot`), `languages/` `.po`/`.mo`/`.json` for `es_ES` |
| Front-end JS/CSS change | edit the source (`resources/js/frontend/index.js`, or the readable `assets/css/<name>.css`) — never a `.min` file or `blocks.js`; rebuild with `npm run build:assets` (regenerates `blocks.js`, `blocks.min.js`, both `.asset.php` files and every `.min.css`); commit source and output together; `scripts/keel-verify` check 11 must pass |

## Flows index
One file per multi-step or branching journey, written from the code as built:
- `docs/flows/checkout-redsys-card.md` — card payment by redirection (gateway `redsys`), classic and Blocks checkout.
- `docs/flows/checkout-bizum.md` — Bizum (gateway `bizumredsys`).
- `docs/flows/checkout-googlepay-redirection.md` — Google Pay by redirection (gateway `googlepayredirecredsys`).
- `docs/flows/checkout-inespay.md` — Inespay bank transfer (gateway `inespayredsys`).
- `docs/flows/notification-handling.md` — signature validation to order status, for the three Redsys-protocol gateways and for the Inespay callback, plus the thank-you-page fallback.
- `docs/flows/refund.md` — refund from the WooCommerce order screen, all four gateways.

Not yet written as flow files (progressive backfill): installation/activation and the first-run redirect to the About page, and the admin configuration journey. Their steps are in `docs/usage/installation.md` and `docs/usage/configuration.md`.

## Acceptance criteria
Reconstructed as-built on 2026-10-06 from the code. One namespace, `AC-01` onward; an ID is never reused or renumbered, new criteria are appended, and a criterion that dies is marked withdrawn.

Reading the tables:
- **Proof** names the automated test that proves the criterion, as `file::method` (PHPUnit) or `file::"test title"` (Playwright). All PHPUnit paths are under `tests/`.
- **`uncovered`** means no automated test asserts the criterion today. The behavior is described from a reading of the code and is `as-built, unverified` until a test or a driven run proves it.
- The existing tests predate these IDs, so their names do not carry an `AC-nn` prefix yet; this table is the link between them. A test written from now on carries its ID in its name.

### F1 — Card payment via Redsys redirection (`redsys`)

| ID | Criterion | Proof |
|----|-----------|-------|
| AC-01 | Choosing Redsys at the classic checkout creates the order and lands on the order-pay page, which shows a form posting `Ds_SignatureVersion`, `Ds_MerchantParameters` and `Ds_Signature` to the Redsys test URL when `testmode` is `yes`; the decoded parameters carry the configured merchant code and terminal. | `e2e/checkout-redsys.spec.js::"guest checkout with the Redsys gateway generates a correctly-signed payment form"` |
| AC-02 | The outgoing request signature is HMAC-SHA256 over the Base64 merchant parameters, keyed with the merchant secret diversified by 3DES with the order number, Base64-encoded. | `Unit/RedsysLiteAPITest.php::test_create_merchant_signature_matches_the_documented_algorithm` |
| AC-03 | The notification signature uses the same key diversification over the received parameters, Base64URL-encoded, and changes when the payload or the merchant secret changes. | `Unit/RedsysLiteAPITest.php::test_create_merchant_signature_notif_matches_the_documented_algorithm`, `::test_notif_signature_changes_when_the_payload_is_tampered_with`, `::test_notif_signature_differs_for_a_different_merchant_secret` |
| AC-04 | A received `Ds_MerchantParameters` value has spaces restored to `+` and every character outside the Base64/Base64URL alphabet removed before it is decoded or verified. | `Unit/RedsysLiteAPITest.php::test_sanitize_merchant_parameters`, `::test_sanitize_merchant_parameters_rejects_null_byte_injection` |
| AC-05 | The order number sent to Redsys is always 12 characters (3 random digits followed by the zero-padded order ID), and the real order ID is recovered from it through the mapping transient, then through the order meta `_payment_order_number_redsys`, then through the legacy strip-three-characters rule. | `Integration/GlobalLiteOrderNumberTest.php::test_prepare_order_number_always_produces_a_fixed_width_number`, `::test_clean_order_number_recovers_the_real_order_id_after_the_transient_expires`, `::test_clean_order_number_recovers_large_order_ids_via_persistent_meta_lookup`, `::test_clean_order_number_falls_back_to_the_legacy_heuristic_when_nothing_matches` |
| AC-06 | A notification whose signature matches the configured secret is accepted. | `Integration/GatewayRedsysIpnTest.php::test_accepts_a_correctly_signed_notification` |
| AC-07 | With no SHA-256 secret configured for the active mode, every notification is rejected (fail closed). | `Integration/GatewayRedsysIpnTest.php::test_rejects_the_notification_when_no_secret_is_configured` |
| AC-08 | A notification with a forged signature, a payload altered after signing, or a signature made for another order is rejected. | `Integration/GatewayRedsysIpnTest.php::test_rejects_a_notification_with_a_forged_signature`, `::test_rejects_a_notification_whose_amount_was_tampered_with_after_signing`, `::test_rejects_a_notification_signed_for_a_different_order` |
| AC-09 | An accepted notification with `Ds_Response` 0–99 and an amount equal to the order total stores the Redsys order number, date, hour, merchant code, authorisation code, card country and card type in order meta, adds the two order notes, calls `payment_complete()`, and sets the order to `completed` only when `orderdo` is `completed`. | uncovered |
| AC-10 | An accepted notification with `Ds_Response` 0–99 whose amount differs from the order total sets the order `on-hold` with a note showing both amounts, and does not complete payment. | uncovered |
| AC-11 | An accepted notification with `Ds_Response` above 99 sets the order `cancelled`, adds a note and empties the cart. | uncovered |
| AC-62 | For the card, Bizum and Google Pay gateways, the cancel URL sent to Redsys as `DS_MERCHANT_URLKO` is a plain URL with literal `&` separators, carrying the order ID, the order key and the cancel nonce. | `Integration/CancelUrlTest.php::test_the_cancel_url_sent_to_redsys_is_a_url_not_html`, `::test_no_gateway_uses_the_html_escaped_cancel_url_outside_markup`, `e2e/cancel-return-redsys.spec.js` |
| AC-64 | For the card, Bizum and Google Pay gateways, a notification whose parameters name no order (no `Ds_Order`, an empty or zero one, or data that is not JSON) is rejected whatever signature accompanies it, including the HMAC of the parameters under an empty key and an empty signature. | `Unit/RedsysLiteAPITest.php::test_notif_signature_for_a_payload_without_an_order_cannot_be_computed_without_the_secret`, `Integration/NotificationWithoutOrderTest.php::test_a_notification_without_an_order_signed_without_the_secret_is_rejected`, `::test_a_notification_without_an_order_and_with_an_empty_signature_is_rejected` |
| AC-65 | The Logo setting of the card, Bizum and Inespay gateways is stored as a URL (anything that cannot be part of one is removed on save; a disallowed scheme stores nothing), and the icon of the four gateways, whether it comes from that setting, from an earlier stored value or from the `woocommerce_<gateway id>_icon` filter, reaches the classic checkout image and the Blocks checkout data as an escaped URL. | `Integration/GatewayLogoTest.php::test_saving_the_logo_setting_keeps_only_a_url`, `::test_saving_the_logo_setting_keeps_an_ordinary_url_as_it_is`, `::test_a_stored_logo_value_cannot_add_attributes_to_the_checkout_icon`, `::test_a_stored_ordinary_logo_url_is_the_icon`, `::test_the_blocks_checkout_receives_a_clean_icon_url`, `::test_a_filtered_icon_is_escaped_too`, `::test_what_the_logo_setting_stores` |
| AC-63 | A customer who returns through that cancel URL to an order of those three gateways that a notification has already set `cancelled` is shown WooCommerce's "Your order was cancelled." notice and no error; orders of other gateways, orders in any other status and requests without a valid cancel nonce keep WooCommerce's own behaviour. | `Integration/CancelUrlTest.php::test_returning_to_an_order_redsys_already_cancelled_is_not_an_error`, `::test_an_order_of_another_gateway_keeps_woocommerce_behaviour`, `::test_a_cancelled_order_is_not_cancellable_outside_its_own_cancel_request`, `::test_a_completed_order_is_never_made_cancellable`, `e2e/cancel-return-redsys.spec.js` |
| AC-12 | A payment notification for an order that is already paid (any status outside the list filtered by `redsys_status_pending`) changes nothing. | uncovered |
| AC-13 | When the customer returns to the order-received page with `key` and `Ds_MerchantParameters` in the URL and the order is still unpaid after the 5-second wait, the returned parameters go through the gateway's `successful_request()`, which verifies the signature before acting. | uncovered |
| AC-14 | That return handling runs at most once every 30 seconds per order, and an already-paid order skips the 5-second wait. | `Integration/MarkOrderAsPaidRateLimitTest.php::test_repeat_calls_for_the_same_unpaid_order_are_rate_limited`, `::test_an_already_paid_order_skips_the_sleep_entirely` |
| AC-15 | While `testmode` is `yes` and the gateway is enabled, the checkout page shows the test-mode warning banner. | uncovered |
| AC-16 | The gateway is disabled when the store currency is not in the allowed-currencies list. | uncovered |

### F2 — Bizum (`bizumredsys`)

| ID | Criterion | Proof |
|----|-----------|-------|
| AC-17 | Choosing Bizum at the classic checkout produces the order-pay form with the three signed fields, posted to the Redsys test URL in test mode, carrying the configured merchant code and terminal. | `e2e/checkout-bizum.spec.js::"guest checkout with the Bizum gateway generates a correctly-signed payment form"` |
| AC-18 | A correctly signed notification for a real order is accepted. | `Integration/GatewayBizumIpnTest.php::test_accepts_a_correctly_signed_notification_for_a_real_order` |
| AC-19 | With no SHA-256 secret configured for the active mode, every notification is rejected. | `Integration/GatewayBizumIpnTest.php::test_rejects_the_notification_when_no_secret_is_configured` |
| AC-20 | A forged signature, a payload altered after signing, or a signature made for another order is rejected. | `Integration/GatewayBizumIpnTest.php::test_rejects_a_notification_with_a_forged_signature`, `::test_rejects_a_notification_whose_amount_was_tampered_with_after_signing`, `::test_rejects_a_notification_signed_for_a_different_order` |
| AC-21 | `successful_request()` verifies against the same resolved secret as the validity check (per-order meta, then the checkout transient, then the settings secret for the active mode) and completes the order when the notification is signed with the test-mode secret. | `Integration/GatewayBizumIpnTest.php::test_successful_request_completes_the_order_when_signed_with_the_test_mode_secret` |
| AC-22 | A notification that names an order that does not exist is rejected without a fatal error. | `Integration/GatewayBizumIpnTest.php::test_does_not_crash_on_a_notification_referencing_a_nonexistent_order` |
| AC-23 | An amount mismatch sets the order `on-hold`; `Ds_Response` above 99 sets it `cancelled`, stores the Redsys error text in `_redsys_error_payment_ds_response_value` and empties the cart; a notification for an already-paid order changes nothing. | uncovered |
| AC-24 | With a `transactionlimit` set, Bizum is removed from the checkout when the cart total is above the limit, compared as decimals, and stays available when the total equals the limit or is below it. (Revised in S-035, D-052: the as-built text described the integer comparison, which was the defect.) | `Integration/CandidateDefectsTest.php::test_bizum_transaction_limit_compares_real_amounts` |

### F3 — Google Pay via redirection (`googlepayredirecredsys`)

| ID | Criterion | Proof |
|----|-----------|-------|
| AC-25 | Choosing Google Pay at the classic checkout produces the order-pay form with the three signed fields, posted to the Redsys test URL in test mode, carrying the configured merchant code and terminal. | `e2e/checkout-googlepay.spec.js::"guest checkout with the Google Pay gateway generates a correctly-signed payment form"` |
| AC-26 | A correctly signed notification for a real order is accepted. | `Integration/GatewayGooglePayIpnTest.php::test_accepts_a_correctly_signed_notification_for_a_real_order` |
| AC-27 | With no SHA-256 secret configured for the active mode, every notification is rejected. | `Integration/GatewayGooglePayIpnTest.php::test_rejects_the_notification_when_no_secret_is_configured` |
| AC-28 | A forged signature, a payload altered after signing, or a signature made for another order is rejected. | `Integration/GatewayGooglePayIpnTest.php::test_rejects_a_notification_with_a_forged_signature`, `::test_rejects_a_notification_whose_amount_was_tampered_with_after_signing`, `::test_rejects_a_notification_signed_for_a_different_order` |
| AC-29 | `successful_request()` verifies against the same resolved secret as the validity check and completes the order when the notification is signed with the test-mode secret. | `Integration/GatewayGooglePayIpnTest.php::test_successful_request_completes_the_order_when_signed_with_the_test_mode_secret` |
| AC-30 | A notification that names an order that does not exist is rejected without a fatal error. | `Integration/GatewayGooglePayIpnTest.php::test_does_not_crash_on_a_notification_referencing_a_nonexistent_order` |
| AC-31 | An amount mismatch sets the order `on-hold`; `Ds_Response` above 99 sets it `cancelled`, stores the Redsys error text in order meta and empties the cart. | uncovered |
| AC-32 | `googlepayredirecredsys_post_payment_complete` fires with the order ID after a completed payment, and `googlepayredirecredsys_post_payment_error` fires with the order ID and the error text after a denied one. | uncovered |
| AC-33 | In test mode the gateway is offered only to the user IDs listed in the stored `testshowgateway` setting, and to everyone when that setting is absent or holds no usable entry; outside test mode it is offered to everyone. (Revised in S-035, D-052: it used to be hidden from everyone when the setting was absent.) | `Integration/CandidateDefectsTest.php::test_googlepay_in_test_mode_is_offered_when_no_user_list_exists`, `::test_googlepay_in_test_mode_still_honours_a_user_list` |
| AC-59 | A Google Pay payment notification for an order that is already paid changes nothing. | `Integration/CandidateDefectsTest.php::test_googlepay_ignores_a_notification_for_an_order_that_is_already_paid` |
| AC-60 | Returning to the order-received page with a correctly signed Redsys return completes a still unpaid Google Pay order; the signature version is passed to the gateway with the other two parameters. | `Integration/CandidateDefectsTest.php::test_thank_you_fallback_passes_the_signature_version_to_the_gateway` |
| AC-61 | With debug logging on, neither the signing secret nor the locally computed signature is written to the log, on the notification path (Bizum, Google Pay), on the refund path (Redsys, Bizum, Google Pay) or while the payment form is built (Redsys, Bizum, Google Pay; added by S-044). | `Integration/CandidateDefectsTest.php::test_debug_logging_never_writes_the_signing_secret`, `::test_debug_logging_of_a_refund_never_writes_the_signing_secret`, `::test_debug_logging_of_the_payment_form_never_writes_the_signing_secret` |

### F4 — Inespay bank transfer (`inespayredsys`)

| ID | Criterion | Proof |
|----|-----------|-------|
| AC-34 | Choosing Inespay at checkout creates a single pay-in through the Inespay API and redirects the customer to the returned payment link. | `e2e/checkout-inespay.spec.js::"guest checkout with the Inespay gateway redirects to the (stubbed) payment link"` |
| AC-35 | With the API key or API token missing, or when the API call fails, returns a non-200 status or omits the link or the pay-in ID, checkout shows an error notice and the customer stays on the checkout page. | uncovered |
| AC-36 | A callback is answered `401` when the API key is not configured, or when `dataReturn` or its signature is missing. | `Integration/GatewayInespayIpnTest.php::test_rejects_the_callback_when_the_api_key_is_not_configured` |
| AC-37 | A callback with a forged signature, or with `dataReturn` altered after signing, is answered `401` and changes nothing. | `Integration/GatewayInespayIpnTest.php::test_rejects_a_callback_with_a_forged_signature`, `::test_rejects_a_callback_tampered_with_after_signing` |
| AC-38 | A correctly signed callback with status `OK` or `SETTLED` completes the payment of the order found by its pay-in ID. | `Integration/GatewayInespayIpnTest.php::test_accepts_a_correctly_signed_callback_and_completes_the_order` |
| AC-39 | A signed amount that differs from the order total sets the order `on-hold` and does not complete payment. | `Integration/GatewayInespayIpnTest.php::test_flags_an_order_on_hold_when_the_signed_amount_does_not_match_the_order_total` |
| AC-40 | An `OK`/`SETTLED` callback for an order that no longer needs payment adds an informational note only. | `Integration/GatewayInespayIpnTest.php::test_a_callback_for_an_already_completed_order_does_not_add_a_misleading_payment_note` |
| AC-41 | An order in a currency other than EUR is set `on-hold` instead of completed. | uncovered |
| AC-42 | A signed callback with any other status adds a note with that status and stores the Inespay fields in order meta, without changing the order status. | uncovered |
| AC-43 | Inespay is offered only when the customer's shipping country — or billing country, or the store base country when neither is known — is `ES`, `PT` or `IT`. | uncovered |
| AC-44 | With a `transactionlimit` set, Inespay is removed from the checkout when the cart total is above the limit, compared as decimals, and stays available when it is not. | `e2e/inespay-transaction-limit.spec.js::"Inespay is hidden when the cart total exceeds the transaction limit (fractional total)"`, `::"Inespay is still offered when the cart total is under the transaction limit"` |

### F5 — WooCommerce Blocks checkout

| ID | Criterion | Proof |
|----|-----------|-------|
| AC-45 | The Redsys gateway is registered in the Blocks checkout, can be selected, and leads to the same signed order-pay form as the classic checkout. | `e2e/checkout-blocks-redsys.spec.js::"guest checkout via the Blocks checkout with the Redsys gateway generates a correctly-signed payment form"` |
| AC-46 | Bizum, Google Pay redirection and Inespay are each registered in the Blocks checkout with their title, description and icon; Inespay is active there only for `ES`, `PT` and `IT`. | uncovered |

### F6 — Admin settings per gateway

| ID | Criterion | Proof |
|----|-----------|-------|
| AC-47 | Each gateway shows its fields under WooCommerce → Settings → Payments and saves them in the option `woocommerce_<gateway id>_settings`, with the keys and defaults listed in `docs/usage/configuration.md`. | uncovered |
| AC-48 | Accessibility conditions for the UI the plugin renders — settings fields, admin notices, the order-pay form and its buttons, the test-mode banners and the gateway rows at checkout: operable by keyboard and assistive technology, name/role/state exposed, contrast met, focus visible, errors not identified by color alone, adequate target size, user preferences honored (D-007). | uncovered — no automated or assistive-technology pass has been run; state `TO BUILD` in `docs/accessibility.md` |

### F7 — Cross-sell page

| ID | Criterion | Proof |
|----|-----------|-------|
| AC-49 | WooCommerce → About Redsys renders the "Other plugins, Skills & APPS" page for users with `manage_options`, and each of its six lists can be changed through its `redsys_lite_apps_plugins_*` filter. | uncovered |

### F8 — Refunds

| ID | Criterion | Proof |
|----|-----------|-------|
| AC-50 | For Redsys, Bizum and Google Pay, a refund from the order screen sends a signed transaction-type-`3` request for the stored Redsys order number, then checks for the refund notification every 5 seconds, up to 21 times, and reports success as soon as it arrives. | uncovered |
| AC-51 | Before the refund request is sent, the Redsys-order-number-to-order mapping transient is saved again for 24 hours, so the refund notification resolves to the right order. | `Integration/RefundOrderNumberTransientTest.php::test_redsys_process_refund_renews_the_order_number_transient`, `::test_bizum_process_refund_renews_the_order_number_transient`, `::test_googlepay_process_refund_renews_the_order_number_transient` |
| AC-52 | For those three gateways the refund returns an error when the order has no stored Redsys order number or the request to Redsys fails, and returns failure when no refund notification arrives in time. | uncovered |
| AC-53 | An Inespay refund with an explicit amount of `0` requests `0`, not the order total; a refund with no amount requests the full order total. | `Integration/GatewayInespayIpnTest.php::test_process_refund_with_an_explicit_zero_amount_does_not_refund_the_full_total`, `::test_process_refund_with_no_amount_given_refunds_the_full_total` |
| AC-54 | An Inespay refund returns an error when the order has no pay-in ID, the API call fails, or the API does not answer with status `200`; on success it adds an order note with the amount and the pay-in ID. | uncovered |
| AC-58 | For Redsys, Bizum and Google Pay, a refund with an explicit amount of zero returns an error and sends no request to Redsys; a refund with an amount, or with none, still sends one. | `Integration/CandidateDefectsTest.php::test_a_refund_of_zero_never_asks_redsys_for_anything`, `::test_a_refund_with_an_amount_or_without_one_still_asks_redsys` |

### F9 — Order payment details and platform declarations

| ID | Criterion | Proof |
|----|-----------|-------|
| AC-55 | The admin order screen shows the payment gateway, Redsys order number, date, hour and authorisation code for orders paid with `redsys`, `bizumredsys` or `googlepayredirecredsys`. | uncovered |
| AC-56 | For a paid order of those three gateways, the order-received text lists the site, merchant code, authorisation number, store name, date and hour. | uncovered |
| AC-57 | The plugin declares compatibility with WooCommerce High-Performance Order Storage. | uncovered |

### Coverage summary
65 criteria, `AC-01` to `AC-65`. 41 are proven by a named automated test; 24 are `uncovered`:
`AC-09`, `AC-10`, `AC-11`, `AC-12`, `AC-13`, `AC-15`, `AC-16`, `AC-23`, `AC-31`, `AC-32`, `AC-35`, `AC-41`, `AC-42`, `AC-43`, `AC-46`, `AC-47`, `AC-48`, `AC-49`, `AC-50`, `AC-52`, `AC-54`, `AC-55`, `AC-56`, `AC-57`.

The largest gap is the order-status half of the three Redsys-protocol gateways: signature validation is proven for all of them, but what the card gateway does with an accepted notification (`AC-09` to `AC-12`) has no test at all, and the mismatch and denial branches of Bizum and Google Pay (`AC-23`, `AC-31`) have none either.

## Testing
An automated suite exists (built after adoption, D-016 to D-033). Counted in the source tree on 2026-10-06 — counted, not executed, in the slice that wrote this paragraph:
- **Unit** (`phpunit.xml.dist`, `tests/Unit/`): 1 class, 10 test methods, 28 tests when run (data providers; recounted and run on 2026-10-06, S-043) — `RedsysLiteAPI` only, no WordPress bootstrap.
- **Integration** (`phpunit-integration.xml.dist`, `tests/Integration/`): 12 classes, 67 test methods (run on 2026-10-06 at S-045; data providers multiply them) — the four gateways' notification handling, order-number mapping, refunds and the thank-you rate limit, on a booted WordPress + WooCommerce.
- **End-to-end** (`playwright.config.js`, `tests/e2e/`): 6 spec files, 7 tests — the four classic checkout flows, the Blocks checkout for the Redsys gateway and the Inespay transaction limit.

Commands and the last recorded runs are in `docs/03-technical-plan.md` (Testing) and `docs/05-test-points.md`. The criteria still without an automated test are listed under "Coverage summary" above. No JavaScript unit-test tooling exists, by decision (D-031).
