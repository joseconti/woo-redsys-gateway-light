# Configuration

> Developer- and operator-facing. Every key below was read from the gateway's `init_form_fields()` on 2026-10-06; effects are described from the code that reads the key. Credentials are described, never shown: no real or test secret appears in this file, and none should be added.

## Where settings live

- **Screen:** WooCommerce → Settings → Payments → the gateway. Access is controlled by WooCommerce's own settings screens.
- **Storage:** one WordPress option per gateway, an array keyed by the setting names below.

| Gateway | Gateway ID | Option | Fields defined in |
|---------|-----------|--------|-------------------|
| Redsys (card) | `redsys` | `woocommerce_redsys_settings` | `classes/class-wc-gateway-redsys.php`, `init_form_fields()` line 404 |
| Bizum | `bizumredsys` | `woocommerce_bizumredsys_settings` | `classes/class-wc-gateway-bizum-redsys.php`, line 416 |
| Google Pay redirection | `googlepayredirecredsys` | `woocommerce_googlepayredirecredsys_settings` | `classes/class-wc-gateway-googlepay-redirection-redsys.php`, line 298 |
| Inespay | `inespayredsys` | `woocommerce_inespayredsys_settings` | `classes/class-wc-gateway-inespay-redsys.php`, line 263 |

Checkbox settings are stored as the strings `yes` and `no`.

A setting can be read or written from the command line, which is how the playground is configured (`docs/playground.md`):

```bash
wp option get woocommerce_redsys_settings --format=json
wp option patch update woocommerce_redsys_settings testmode yes
```

The four gateways are configured independently. The merchant code, terminal and secret are **not** shared: Bizum and Google Pay each need their own values entered, even when they are the same as the card gateway's.

## Settings common to the three Redsys-protocol gateways

Present in `redsys`, `bizumredsys` and `googlepayredirecredsys` unless a note says otherwise.

| Key | Label | Type | Default | Effect |
|-----|-------|------|---------|--------|
| `enabled` | Enable/Disable | checkbox | `no` | Offers the gateway at checkout. The gateway is also switched off at run time when the store currency is not an allowed one. |
| `title` | Title | text | "Servired/RedSys", "Bizum", "Google Pay" | Name shown to the customer at checkout. |
| `description` | Description | textarea | a default sentence per gateway | Text shown under the name at checkout. |
| `logo` | Logo | text (URL) | empty | Card and Bizum only. URL of an image that replaces the bundled icon. Google Pay has no such field. |
| `customer` | Commerce number (FUC) | text | empty | The merchant code your bank assigned. Sent as `DS_MERCHANT_MERCHANTCODE`. |
| `commercename` | Commerce Name | text | empty | Sent as `DS_MERCHANT_MERCHANTNAME`. |
| `terminal` | Terminal number | text | empty | Sent as `DS_MERCHANT_TERMINAL`. |
| `not_use_https` | HTTPS SNI Compatibility | checkbox | `no` | When `yes`, Redsys is given the notification URL with `http:` instead of `https:`. Intended for certificates Redsys does not accept; if the site forces HTTPS, the notification URL needs an exception. |
| `secretsha256` | Encryption secret passphrase SHA-256 | text | empty | The live signing secret from your bank. Signs requests and verifies notifications in live mode. |
| `customtestsha256` | TEST MODE: Encryption secret passphrase SHA-256 | text | empty | The test signing secret from your bank. Used in test mode; see "Test mode and live mode". |
| `redsyslanguage` | Language Gateway | select | `001` (Spanish) | Language of the Redsys page. Ignored when WPML is active — the customer's language is used instead. The card gateway lists 31 languages in its own code; Bizum and Google Pay take the list from `includes/data/languages.php`. |
| `testmode` | Running in test mode | checkbox | `yes` | Sends customers to the Redsys test host and uses the test secret. **New installations start in test mode.** |
| `debug` | Debug Log | checkbox | `no` | Writes a detailed log; see "Debug log". |

## Settings specific to one gateway

### Redsys (card)

| Key | Label | Type | Default | Effect |
|-----|-------|------|---------|--------|
| `payoptions` | Pay Options | select | `T` | Sent as `DS_MERCHANT_PAYMETHODS`. Values: a single space ("All Methods"), `T` ("Credit Card") and `C` (also labelled "Credit Card"). |
| `lwvactive` | Enable LWV | checkbox | `no` | When `yes`, orders of 30.00 or less are sent with the low-value exemption `DS_MERCHANT_EXCEP_SCA` = `LWV`. Your bank must have enabled it. |
| `orderdo` | What to do after payment? | select | `processing` | `processing`: leave the order where WooCommerce's `payment_complete()` puts it. `completed`: also mark it `completed`. |

### Bizum

| Key | Label | Type | Default | Effect |
|-----|-------|------|---------|--------|
| `orderdo` | What to do after payment? | select | `processing` | As for the card gateway. |
| `transactionlimit` | Transaction Limit | text | empty | A number. Bizum is removed from the checkout when the cart total reaches it. Both values are truncated to whole numbers before comparing. Empty or zero means no limit. |

### Google Pay redirection

No specific fields. It has no `logo`, no `orderdo` and no `transactionlimit` field.

### Inespay

Inespay shares none of the Redsys merchant settings.

| Key | Label | Type | Default | Effect |
|-----|-------|------|---------|--------|
| `enabled` | Enable/Disable | checkbox | `no` | Offers the gateway at checkout, to customers in `ES`, `PT` or `IT` only. |
| `title` | Title | text | "Inespay Bank Transfer" | Name shown at checkout. |
| `description` | Description | textarea | "Pay securely via your online banking with Inespay." | Text shown at checkout. |
| `logo` | Logo | text (URL) | empty | Replaces the bundled icon. |
| `api_key` | API Key | text | empty | The API key of your Inespay project. Sent as the `X-Api-Key` header, and used as the key that verifies callback signatures. |
| `api_token` | API Token | password | empty | The API token of your Inespay project. Sent as the `Authorization` header. |
| `creditor_account` | Creditor IBAN (optional) | text | empty | Sent as `creditorAccount` on pay-ins and as `collectingIBAN` on refunds. |
| `expiration` | Link expiration (minutes) | number, minimum 1 | empty | Sent as `expiration` on pay-ins when filled in. |
| `orderdo` | What to do after payment? | select | `processing` | As for the card gateway. |
| `transactionlimit` | Transaction Limit | text | empty | Inespay is removed from the checkout when the cart total is greater than this number, compared as decimals. |
| `testmode` | Running in test mode | checkbox | `yes` | Uses the Inespay sandbox environment instead of production. |
| `debug` | Debug Log | checkbox | `no` | Writes a detailed log. |

## Stored keys with no field on the settings screen

The code reads these keys from the stored settings, but the Lite settings screens have no field for them. They keep their absent value unless something else writes them. They are listed because they change behavior when present.

| Key | Gateway | Read by | Effect when present |
|-----|---------|---------|---------------------|
| `testshowgateway` | Google Pay | `check_user_show_payment_method()` | In test mode, who sees the gateway: a list of user IDs, or a list with one empty string for "everyone". **When absent, Google Pay is hidden on the front end for as long as test mode is on.** |
| `testforuser`, `testforuserid` | Bizum | `check_user_test_mode()` | Per-user test mode in a live store. |
| `buttoncheckout`, `butonbgcolor`, `butontextcolor` | Bizum | constructor only | Read into properties and used nowhere else in the plugin. |
| `secret`, `hashtype` | Card | constructor only | Read into properties and used nowhere else in the plugin. |
| `descripredsys` | Bizum (through `product_description()`) | `WC_Gateway_Redsys_Global_Lite` | `id`, `name` or `sku` change the description sent to Redsys; otherwise "Order" plus the order number. |
| `psd2` | any (through `get_psd2_arg()`) | `WC_Gateway_Redsys_Global_Lite` | None today: nothing in the plugin calls `get_psd2_arg()`. The card gateway always sends the PSD2 block. |

## Test mode and live mode

| | Test mode (`testmode` = `yes`) | Live mode |
|---|---|---|
| Redsys host | the Redsys test host | the Redsys production host |
| Card: signing and verifying secret | `customtestsha256`; when that is empty, `secretsha256` | `secretsha256` |
| Bizum and Google Pay: signing secret | `customtestsha256`; when that is empty, a generic Redsys test secret built into the class | `secretsha256` |
| Bizum and Google Pay: notifications accepted at all | only when `customtestsha256` or `secretsha256` is filled in | only when `secretsha256` is filled in |
| Inespay environment | sandbox | production |
| Checkout banner | shown for card, Bizum and Google Pay while the gateway is enabled; none for Inespay | none |

Points that follow from the table:

- A gateway with **no secret filled in** for its mode still builds a payment form, but rejects every notification. The order stays `pending`.
- Going live is one change per gateway: fill in `secretsha256` (and the live merchant code and terminal, if they differ) and turn `testmode` off. Do it for each gateway you use.
- The hosts themselves are listed in `docs/reference/endpoints.md` ("Outbound").

## Notification URLs to give your bank

The plugin sends its own notification URL with every request, so nothing has to be configured on the Redsys side for the notification to arrive. If your bank asks for the URL, it is:

| Gateway | URL |
|---------|-----|
| Redsys (card) | `https://<your-site>/?wc-api=WC_Gateway_redsys` |
| Bizum | `https://<your-site>/?wc-api=WC_Gateway_bizumredsys` |
| Google Pay redirection | `https://<your-site>/?wc-api=WC_Gateway_googlepayredirecredsys` |
| Inespay | `https://<your-site>/?wc-api=wc_gateway_inespayredsys` |

No settings screen displays this URL: in the code it is used only when a request is built.

## Debug log

With `debug` set to `yes`, each gateway writes to the WooCommerce log (WooCommerce → Status → Logs) under these sources:

| Gateway | Log source |
|---------|-----------|
| Redsys (card) | `redsys` |
| Bizum | `bizumredsys` |
| Google Pay redirection | `googlepayredirecredsys` |
| Inespay | `inespayredsys` |

The Bizum field's own help text names the file `bizum-{date}-{number}.log`; the source the code writes to is `bizumredsys`.

**Treat these logs as confidential.** They contain the full request and notification data, and some entries include the signing secret and computed signatures in clear (the "SHA256 Settings" and "SHA256 Transcient" lines at `classes/class-wc-gateway-bizum-redsys.php:1203` and `classes/class-wc-gateway-googlepay-redirection-redsys.php:1023`, and the "Signature verification failed… Local: … Remote: …" lines of all three Redsys-protocol gateways). Turn `debug` off when you are done, and remove the secret from any log before sharing it.

`WC_Gateway_Redsys_Global_Lite::debug()` also writes to a `redsys-global` source, only when `WP_DEBUG` is on.

## Stored data

Everything the plugin keeps. There are no custom tables.

### Options

| Option | Content |
|--------|---------|
| `woocommerce_redsys_settings`, `woocommerce_bizumredsys_settings`, `woocommerce_googlepayredirecredsys_settings`, `woocommerce_inespayredsys_settings` | The settings above, **including the merchant secrets and the Inespay API key and token, unencrypted** — as WooCommerce stores all gateway settings. |
| `woocommerce-redsys-version` | The plugin version last seen by an admin; drives the one-time redirect to the About page. |
| `woocommerce-redsys-rate` | Timestamp of the first admin visit after install. |
| `hide-new-version-redsys-notice` | The version for which the update notice was dismissed. |
| `telegram-redsys-notice` | `yes` once the Telegram notice was dismissed. |
| `txnid_<n>`, `token_type_<n>` | Written by `set_txnid()` and `set_token_type()` in `WC_Gateway_Redsys_Global_Lite`. Nothing in the plugin calls either method, so the Lite edition does not write these options. |

### Transients

| Transient | Lifetime | Purpose |
|-----------|----------|---------|
| `redys_order_temp_<Redsys order number>` | 1 hour at checkout; 24 hours when renewed by a refund | Maps the order number sent to Redsys back to the WooCommerce order ID. The spelling `redys` is the code's. |
| `redsys_signature_<Redsys order number>` | 600 seconds (Bizum), 3600 seconds (Google Pay) | The signing secret used for that payment, in clear. |
| `<order id>_redsys_refund` | no expiry; deleted when the refund is confirmed | Flag set by a refund notification. |
| `redsyslite_mark_paid_attempt_<order id>` | 30 seconds | Rate limit of the order-received fallback. |

### Order meta

| Meta key | Written by | Content |
|----------|-----------|---------|
| `_payment_order_number_redsys` | card, Bizum, Google Pay | The order number sent to Redsys. Needed for refunds. |
| `_payment_date_redsys`, `_payment_hour_redsys` | card, Bizum, Google Pay | Date and time reported by Redsys. |
| `_authorisation_code_redsys` | card, Bizum, Google Pay | Authorisation code. |
| `_order_fuc_redsys` | card, Bizum, Google Pay | Merchant code reported by Redsys. |
| `_card_country_redsys` | card, Bizum, Google Pay | Card country code. |
| `_card_type_redsys` | card, Bizum | `Credit` or `Debit`. |
| `_payment_terminal_redsys`, `_corruncy_code_redsys` | Bizum, Google Pay | Terminal and currency code. The spelling `corruncy` is the code's. |
| `_redsys_error_payment_ds_response_value` | Bizum, Google Pay | Redsys error text of a denied payment. |
| `_redsys_secretsha256` | Bizum, Google Pay | **The signing secret, in clear, kept with the order.** Known and deliberately deferred: D-025. |
| `_inespay_single_payin_id`, `_inespay_status`, `_inespay_reference`, `_inespay_creditor_account`, `_inespay_debtor_account`, `_inespay_debtor_name` | Inespay | Pay-in data. The debtor name and account are personal data of the payer. |
| `_redsys_done` | Inespay | `yes` after a completed payment. |

## Translations

The text domain is `woo-redsys-gateway-light`; translations load from `languages/`, which ships `es_ES`. `wpml-config.xml` registers the card gateway's `title` and `description` as translatable admin texts for WPML — only the card gateway's, plus a `woocommerce_iupay_settings` entry for a gateway this plugin does not contain.

## External setup

No external-setup record exists for this project (it was adopted, and has no design handoff). What must exist outside WordPress is a merchant contract: a Redsys merchant code, terminal and SHA-256 secret from the acquiring bank, with Bizum and wallet payments enabled on that terminal if those gateways are used; or an Inespay project with its API key and token. None of this has been verified against a real merchant account in this project — see `docs/playground.md`.

## Related
- `docs/usage/getting-started.md` — the shortest path to a first test payment.
- `docs/reference/endpoints.md` — the notification URLs in detail.
- `docs/threat-model.md` — how the secrets are protected, and where they are not.
