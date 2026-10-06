# Flow — Google Pay by redirection (`googlepayredirecredsys`)

> As-built, written from the code on 2026-10-06. Steps marked *(unverified)* are read from the code and have no automated test.

## Trigger / entry point
A customer chooses the payment method with ID `googlepayredirecredsys` on the WooCommerce checkout and places the order. The wallet itself is shown by Redsys on its hosted page; this plugin does not use the browser Payment Request API.

## Actors
- **Customer** — browser and wallet.
- **Store** — WordPress + WooCommerce + this plugin (`WC_Gateway_GooglePay_Redirection_Redsys`).
- **Redsys** — the hosted page and the notification sender.

## Preconditions
- The gateway is enabled and the store currency is in the allowed list (`is_valid_for_use()`).
- The gateway is visible to this visitor (`show_payment_method()`, see "Branches").
- A SHA-256 secret is configured for the active mode; without one every notification is rejected (`AC-27`).

## Steps

1. **Customer → Store:** places the order. WooCommerce creates it `pending`; `process_payment()` returns a redirect to the order-pay URL.
2. **Store → Customer:** `woocommerce_receipt_googlepayredirecredsys` runs `receipt_page()`, which prints the form built by `generate_redsys_form()`.
3. **Store (building the request):** `get_redsys_args( $order )` builds the same parameter set as Bizum with `DS_MERCHANT_PAYMETHODS` fixed to `xpay`, signs it, stores the signing secret in the transient `redsys_signature_<Redsys order number>` for 3600 seconds, and passes the three signed fields through the filter `woocommerce_googlepayredirecredsys_args`.
4. **Store → Customer:** the form posts `Ds_SignatureVersion`, `Ds_MerchantParameters` and `Ds_Signature` to the Redsys test or live host and is auto-submitted (`AC-25`).
5. **Customer → Redsys:** pays with the wallet.
6. **Redsys → Store:** posts the notification to `?wc-api=WC_Gateway_googlepayredirecredsys`. Continues in `docs/flows/notification-handling.md`.
7. **Redsys → Customer → Store:** returns the customer to the order-received URL or to the cancel-order URL.

## Order states

| From | Event | To |
|------|-------|----|
| — | order placed | `pending` |
| `pending` | accepted notification, authorised, amount matches | status set by `payment_complete()` (`AC-29`); action `googlepayredirecredsys_post_payment_complete` fires (`AC-32`) |
| `pending` | accepted notification, authorised, amount differs | `on-hold` (`AC-31`) |
| `pending` | accepted notification, denied | `cancelled`; action `googlepayredirecredsys_post_payment_error` fires (`AC-31`, `AC-32`) |
| `pending` | notification rejected, or none arrives | stays `pending` |

This gateway has no `orderdo` setting. Its code tests `$this->orderdo` after `payment_complete()`, but the property is never assigned, so the order is never forced to `completed` here *(unverified)*.

## Branches and conditions
- **Visibility in test mode (`show_payment_method()` / `check_user_show_payment_method()`):** outside test mode the gateway is shown to everyone. In test mode it reads the stored setting `testshowgateway`: a list of user IDs shows it only to those logged-in users; a list holding one empty string shows it to everyone; when the key is absent it is shown to nobody on the front end. The Lite settings screen has no field for this key, so a store that only uses the settings screen has the gateway hidden for as long as test mode is on (`AC-33`, *unverified*; the playground seeds the key by hand — see `docs/playground.md`).
- **Signing secret (`get_redsys_sha256()`):** in test mode, `customtestsha256` when filled in, otherwise a generic Redsys test secret built into the class; in live mode, `secretsha256`.
- **Per-user test mode:** `check_user_test_mode()` always returns `false` in this class.
- **Blocks checkout:** registered by `WC_Gateway_GooglePay_Redirection_Redsys_Support` (`AC-46`, *unverified*).
- **Test-mode banner:** `warning_checkout_test_mode_bizum()` (the method keeps the Bizum name) on `woocommerce_before_checkout_form`.

## Failure paths and recovery
- **Customer abandons or the wallet payment is refused:** return to the cancel-order URL; a denied notification cancels the order, stores the Redsys error text, empties the cart and fires `googlepayredirecredsys_post_payment_error`.
- **Notification rejected:** the `redsys_signature_<number>` transient is deleted and the order stays `pending`.
- **Return to the order-received page while the order is still unpaid:** the shared fallback calls this gateway's `successful_request()` with only `Ds_MerchantParameters` and `Ds_Signature`. This class stops with `wp_die()` when `Ds_SignatureVersion` is missing (line 941), so the fallback ends the page instead of processing the payment *(unverified — read from the code, not reproduced)*. `docs/flows/notification-handling.md`, part C, describes the fallback.

## Diagram

```mermaid
sequenceDiagram
    participant C as Customer
    participant S as Store (WC_Gateway_GooglePay_Redirection_Redsys)
    participant R as Redsys
    C->>S: Place order (payment method googlepayredirecredsys)
    S-->>C: Order-pay page with signed form (PAYMETHODS xpay)
    Note over S: signing secret kept in a transient for 3600 s
    C->>R: POST signed form
    R->>S: POST notification to ?wc-api=WC_Gateway_googlepayredirecredsys
    alt valid and authorised
        S-->>R: 200, payment complete, post_payment_complete
    else valid and denied
        S-->>R: 200, cancelled, post_payment_error
    else invalid
        S-->>R: wp_die, order unchanged
    end
    R-->>C: Redirect to URLOK or URLKO
```

## Acceptance criteria covered
`AC-25`, `AC-33`, `AC-46`; the notification half is `AC-26` to `AC-32`.

## Source files
- `classes/class-wc-gateway-googlepay-redirection-redsys.php` — `__construct()` line 184, `init_form_fields()` 298, `check_user_test_mode()` 392, `get_redsys_url_gateway()` 481, `get_redsys_sha256()` 554, `get_redsys_args()` 605, `generate_redsys_form()` 723, `process_payment()` 777, `receipt_page()` 790, `warning_checkout_test_mode_bizum()` 1533, `check_user_show_payment_method()` 1554, `show_payment_method()` 1582.
- `classes/class-wc-gateway-redsys-global-lite.php` — `prepare_order_number()` line 1141, `get_redsys_option()` 51.
- `includes/class-redsysliteapi.php` — request encoding and signature.
- `includes/blocks/class-wc-gateway-googlepay-redirection-redsys-support.php` — Blocks registration.
