# Endpoints

> As-built reference, written from the code on 2026-10-06. The plugin registers **no REST routes, no `admin-ajax` actions, no WP-CLI commands and no shortcodes** (searched for `register_rest_route`, `wp_ajax_`, `admin_post_`, `WP_CLI` and `add_shortcode` in `woocommerce-redsys.php`, `about-redsys.php`, `classes/` and `includes/`: no matches). Its externally reachable entry points are the four WooCommerce `wc-api` callbacks, one query-string handler on the order-received page, and two admin notice-dismiss links.
>
> Examples use placeholders in angle brackets. Never paste a real merchant secret, API key or signature into a document, a ticket or a shell history.

## Inbound

| # | Entry point | Method | Handler | Authentication |
|---|-------------|--------|---------|----------------|
| 1 | `?wc-api=WC_Gateway_redsys` | `POST` | `WC_Gateway_Redsys::check_ipn_response()` | Redsys HMAC signature |
| 2 | `?wc-api=WC_Gateway_bizumredsys` | `POST` | `WC_Gateway_Bizum_Redsys::check_ipn_response()` | Redsys HMAC signature |
| 3 | `?wc-api=WC_Gateway_googlepayredirecredsys` | `POST` | `WC_Gateway_GooglePay_Redirection_Redsys::check_ipn_response()` | Redsys HMAC signature |
| 4 | `?wc-api=wc_gateway_inespayredsys` | `POST` | `WC_Gateway_Inespay_Redsys::handle_callback()` | Inespay HMAC signature |
| 5 | Order-received page with `key` and `Ds_MerchantParameters` | `GET` | `redsyslite_force_mark_order_as_paid_on_thankyou_page()` | WooCommerce order key, then the Redsys HMAC signature |
| 6 | Any admin page with `redsys-hide-new-version` | `GET` | `redsys_lite_add_notice_new_version()` | `manage_woocommerce` + nonce |
| 7 | Any admin page with `redsys-telegram` | `GET` | `redsys_lite_ask_for_telegram()` | `manage_woocommerce` + nonce |

Entry points 1 to 4 are reachable by anyone on the internet, by design: the payment processor is not a WordPress user. They carry no nonce and need no login. The signature check is the whole of their protection; see `docs/threat-model.md`.

The `wc-api` value is matched by WooCommerce, which fires the action `woocommerce_api_<lower-cased value>`. Each gateway attaches its handler to `woocommerce_api_wc_gateway_<gateway id>`.

---

### 1–3. Redsys notification — `?wc-api=WC_Gateway_<gateway id>`

- **URL:** `<site home URL>/?wc-api=WC_Gateway_redsys`, `…=WC_Gateway_bizumredsys`, `…=WC_Gateway_googlepayredirecredsys`. Built in each constructor (`classes/class-wc-gateway-redsys.php:258`, `classes/class-wc-gateway-bizum-redsys.php:293`, `classes/class-wc-gateway-googlepay-redirection-redsys.php:206`). When the gateway's `not_use_https` setting is `yes`, the plugin gives Redsys the same URL with `http:` instead of `https:`.
- **Caller:** Redsys, server to server, after a payment attempt or a refund.
- **Parameters** (form-encoded body):

  | Name | Type | Required | Description |
  |------|------|----------|-------------|
  | `Ds_SignatureVersion` | string | yes | `HMAC_SHA256_V1`. Google Pay rejects the request when it is absent; the card and Bizum handlers read it but do not test its value. |
  | `Ds_MerchantParameters` | string | yes | Base64 (or Base64URL) JSON with the result: `Ds_Order`, `Ds_Amount`, `Ds_Currency`, `Ds_Response`, `Ds_AuthorisationCode`, `Ds_MerchantCode`, `Ds_Terminal`, `Ds_TransactionType`, `Ds_Date`, `Ds_Hour`, `Ds_Card_Country`, `Ds_Card_Type` and others. |
  | `Ds_Signature` | string | yes | Base64URL HMAC-SHA256 of `Ds_MerchantParameters`, keyed with the merchant secret diversified with `Ds_Order`. |

- **Responses:**

  | Case | Response |
  |------|----------|
  | Signature valid | Status line `HTTP/1.1 200 OK`; the action `valid_<gateway id>_standard_ipn_request` fires and the order is updated. The body is whatever the listeners print — normally empty. |
  | No secret configured, missing field, or signature mismatch | `wp_die()` with a one-line text: "Do not access this page directly (Redsys redirección Lite)", "Do not access this page directly (Bizum Lite)", or "There is nothing to see here, do not access this page directly (Google Pay redirection)". The plugin passes no status code, so WordPress's default for `wp_die()` applies (500 in WordPress core; not asserted by a test in this project). |

- **Side effects:** see `docs/flows/notification-handling.md`, part A.
- **Example** — a request with a made-up signature must be refused and must leave the order untouched:

  ```bash
  curl -i -X POST "https://<your-site>/?wc-api=WC_Gateway_redsys" \
    --data-urlencode "Ds_SignatureVersion=HMAC_SHA256_V1" \
    --data-urlencode "Ds_MerchantParameters=<base64-json>" \
    --data-urlencode "Ds_Signature=<not-a-valid-signature>"
  ```

  Expected: the "Do not access this page directly…" text and no change to any order. This is the check recorded as a driven run in `docs/05-test-points.md` (first row) and automated in `tests/Integration/GatewayRedsysIpnTest.php`.

### 4. Inespay callback — `?wc-api=wc_gateway_inespayredsys`

- **URL:** `<site home URL>/?wc-api=wc_gateway_inespayredsys` (`classes/class-wc-gateway-inespay-redsys.php:133`). The plugin sends it to Inespay as `notifUrl` at checkout and as `okNotifUrl` / `errorNotifUrl` on refunds.
- **Caller:** Inespay, server to server.
- **Body:** JSON (the content type the plugin asks for). A form-encoded body and plain `POST` fields are accepted as fallbacks.
- **Parameters:**

  | Name | Type | Required | Description |
  |------|------|----------|-------------|
  | `dataReturn` | string | yes | Base64 JSON with the result. Fields read: `singlePayinId`, `codStatus`, `amount` (minor units), `reference`, `debtorAccount`, `debtorName`, `creditorAccount`. |
  | `signatureDataReturn` | string | yes | Base64 of the hexadecimal HMAC-SHA256 of the `dataReturn` string, keyed with the gateway's API key. Also accepted under the names `signature_data_return` and `signature`. |

- **Responses** (the body is plain text produced by `wp_die()`):

  | Case | Status | Body |
  |------|--------|------|
  | `dataReturn` or the signature missing, or no API key configured | 401 | `KO` |
  | Signature mismatch | 401 | `KO` |
  | `dataReturn` does not decode to a JSON object | 401 | `KO` |
  | Signature valid, no `singlePayinId` or no matching order | 200 | `OK` (nothing changes) |
  | Signature valid, order found | 200 | `OK` (order updated per `docs/flows/notification-handling.md`, part B) |

- **Example** — an unsigned request must be refused:

  ```bash
  curl -i -X POST "https://<your-site>/?wc-api=wc_gateway_inespayredsys" \
    -H "Content-Type: application/json" \
    -d '{"dataReturn":"<base64-json>","signatureDataReturn":"<not-a-valid-signature>"}'
  ```

  Expected: status 401 and the body `KO`. Automated in `tests/Integration/GatewayInespayIpnTest.php`.

### 5. Return to the order-received page

- **URL:** WooCommerce's order-received URL, which the plugin gives Redsys as `DS_MERCHANT_URLOK` with `utm_nooverride=1` added. Redsys appends its own result parameters when it sends the customer back.
- **Handler:** `redsyslite_force_mark_order_as_paid_on_thankyou_page()` on `wp_head` (`woocommerce-redsys.php:565`), which calls `redsyslite_mark_order_as_paid()` (`:452`).
- **Parameters** (query string):

  | Name | Type | Required | Description |
  |------|------|----------|-------------|
  | `key` | string | yes | The WooCommerce order key; identifies the order. |
  | `Ds_MerchantParameters` | string | yes | As in entry points 1–3. |
  | `Ds_Signature` | string | no | As in entry points 1–3; an absent value fails verification, before any wait. |

- **Behavior:** nothing is printed by the handler itself. The order must have been placed with `redsys`, `bizumredsys` or `googlepayredirecredsys` and be unpaid, and the signature must verify (`is_valid_return()`); a request that fails any of these ends at once. A signed return is handled at most once every 30 seconds per order: the handler waits 5 seconds for the server-to-server notification and, if the order is still unpaid, hands the parameters to the gateway's `successful_request()`, which verifies the signature again before changing the order.
- **Authentication:** possession of the order key gets as far as the signature check; only a valid Redsys signature reaches the wait or changes anything.
- **Errors:** an unknown key, an order of another gateway, an already-paid order, or a missing or invalid signature end silently.
- **Example:** not runnable by hand without a signature made by Redsys. The rate limit and the early exits are exercised by `tests/Integration/MarkOrderAsPaidRateLimitTest.php` and `tests/Integration/MarkOrderAsPaidUnsignedReturnTest.php`.

### 6–7. Admin notice dismissal

- **URL:** any WordPress admin page with `redsys-hide-new-version=hide-new-version-redsys&_redsys_hide_new_version_nonce=<nonce>`, or with `redsys-telegram=telegram-redsys&_redsys_telegram_nonce=<nonce>`. The links are printed inside the notices themselves.
- **Handlers:** `redsys_lite_add_notice_new_version()` (`woocommerce-redsys.php:217`) and `redsys_lite_ask_for_telegram()` (`:266`), both on `admin_notices`.
- **Effect:** with a valid nonce (actions `redsys_hide_new_version_nonce` and `redsys_telegram_nonce`), stores the option `hide-new-version-redsys-notice` (the current plugin version) or `telegram-redsys-notice` (`yes`), which hides the notice. With an invalid nonce nothing is stored.
- **Permissions:** the capability `manage_woocommerce` and the nonce. A user without the capability is neither shown the notices nor able to dismiss them (S-047, D-061).

---

## Outbound

Requests the plugin makes or sends the customer to. Hosts are fixed in the code.

| Purpose | Destination | How | Code |
|---------|-------------|-----|------|
| Payment form, live | `https://sis.redsys.es/sis/realizarPago` | browser form `POST` | `liveurl` in the three Redsys-protocol classes |
| Payment form, test | `https://sis-t.redsys.es:25443/sis/realizarPago` | browser form `POST` | `testurl` in the same classes |
| Card refund | `https://sis.redsys.es/sis/rest/trataPeticionREST` (live) or the same path on the test host | `wp_remote_post()`, 45 s timeout | `classes/class-wc-gateway-redsys.php:1210` |
| Bizum / Google Pay refund | the payment-form URL above for the active mode | `wp_remote_post()`, 45 s timeout | `classes/class-wc-gateway-bizum-redsys.php:1555`, `classes/class-wc-gateway-googlepay-redirection-redsys.php:1386` |
| Inespay pay-in | `https://apiflow.inespay.com/pro/v22/payins/single/init` (live) or `…/san/…` (test) | `wp_remote_post()`, 30 s timeout, headers `X-Api-Key` and `Authorization` | `classes/class-wc-gateway-inespay-redsys.php:398` |
| Inespay refund | `https://apiflow.inespay.com/pro/v22/refunds/init` (live) or `…/san/…` (test) | `wp_remote_post()`, 30 s timeout, same headers | `classes/class-wc-gateway-inespay-redsys.php:703` |

The classes also hold two SOAP web-service URLs (`liveurlws`, `testurlws`); `get_redsys_url_gateway()` returns them only when called with a type other than `rd`, which no code in this plugin does.

## Related
- `docs/reference/hooks-and-extension-points.md` — the actions these entry points fire.
- `docs/flows/notification-handling.md`, `docs/flows/refund.md` — the journeys behind them.
- `docs/threat-model.md` — the controls on the public callbacks.
