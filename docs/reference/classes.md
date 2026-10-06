# Classes

> As-built reference, written from the code on 2026-10-06 under the adoption rule of progressive backfill: a class or method is documented in full the first time a slice reads it end to end. **This file is not complete.** Each class says which of its methods are documented here and which are only listed by name. A method that is only listed has not been read for this document, and nothing is claimed about its behavior.
>
> The plugin uses no namespaces and no autoloader; files are loaded with `require_once` from `woocommerce-redsys.php`. None of the classes is designed to be replaced: there is no filter on the gateway class names and no factory. Extending is done through the hooks in `docs/reference/hooks-and-extension-points.md`.

## Index

| Class | File | Role |
|-------|------|------|
| `WC_Gateway_Redsys` | `classes/class-wc-gateway-redsys.php` | Card gateway by redirection, ID `redsys` |
| `WC_Gateway_Bizum_Redsys` | `classes/class-wc-gateway-bizum-redsys.php` | Bizum gateway, ID `bizumredsys` |
| `WC_Gateway_GooglePay_Redirection_Redsys` | `classes/class-wc-gateway-googlepay-redirection-redsys.php` | Google Pay by redirection, ID `googlepayredirecredsys` |
| `WC_Gateway_Inespay_Redsys` | `classes/class-wc-gateway-inespay-redsys.php` | Inespay bank transfer, ID `inespayredsys` |
| `WC_Gateway_Redsys_Global_Lite` | `classes/class-wc-gateway-redsys-global-lite.php` | Shared helpers, reached through `WCRedL()` |
| `WC_Gateway_Redsys_PSD2_Light` | `classes/class-wc-gateway-redsys-psd2-light.php` | PSD2 / 3-D Secure data, reached through `WCPSD2L()` |
| `RedsysLiteAPI` | `includes/class-redsysliteapi.php` | Redsys request encoding and signatures |
| `WC_Gateway_Redsys_Lite_Support`, `WC_Gateway_Bizum_Lite_Support`, `WC_Gateway_GooglePay_Redirection_Redsys_Support`, `WC_Gateway_Inespay_Lite_Support` | `includes/blocks/` | WooCommerce Blocks checkout integrations |
| `Redsys_Lite_Apps_Plugins` | `includes/class-redsys-lite-apps-plugins.php` | The About page content |

---

## The three Redsys-protocol gateways

`WC_Gateway_Redsys`, `WC_Gateway_Bizum_Redsys`, `WC_Gateway_GooglePay_Redirection_Redsys` — each extends WooCommerce's `WC_Payment_Gateway`. They share a shape but no code: every method below exists separately in each class.

- **Constructed by:** WooCommerce, from the class names added in `woocommerce_add_gateway_redsys_gateway()` (`woocommerce-redsys.php:271`). The constructor takes no arguments, loads the settings, and registers the listeners for the order-pay page, the settings save, the `wc-api` callback and the checkout banner. It disables the gateway when `is_valid_for_use()` is false.
- **Settings:** `docs/usage/configuration.md`.

| Method | Signature | Returns | Notes |
|--------|-----------|---------|-------|
| `is_valid_for_use` | `()` | `bool` | True when the store currency is in `WCRedL()->allowed_currencies()`. |
| `admin_options` | `()` | `void` | Prints the settings screen. Not read in full for this document. |
| `init_form_fields` | `()` | `void` | Fills `$this->form_fields`. |
| `validate_logo_field` | `( string $key, string $value )` | `string` | Called by WooCommerce when the settings are saved. Returns the posted Logo value as a clean URL (`esc_url_raw()`), or an empty string. Card, Bizum and Inespay; Google Pay has no Logo setting. |
| `get_redsys_args` | `( WC_Order $order )` | `array` | The three signed form fields; applies the `_args` filter. Side effects: creates the order-number mapping transient; Bizum and Google Pay also store the signing secret in a transient. |
| `generate_redsys_form` | `( int $order_id )` | `string` | HTML of the auto-submitting form; attaches an inline script to the `woocommerce` handle. `receipt_page()` passes it the order it received. |
| `process_payment` | `( int $order_id )` | `array` | `result` `success` and `redirect` to the order-pay URL. |
| `receipt_page` | `( WC_Order $order )` | `void` | Prints the message and the form. Card and Bizum pass the form through `wp_kses()`; Google Pay prints it as built. |
| `check_ipn_request_is_valid` | `()` | `bool` | Reads `$_POST`; true only for a correctly signed notification. |
| `check_ipn_response` | `()` | `void` | The `wc-api` handler. Fires `valid_<id>_standard_ipn_request` or calls `wp_die()`. |
| `is_valid_return` | `( array $params )` | `bool` | Verifies and nothing else: true when `Ds_MerchantParameters` and `Ds_Signature` in `$params` carry a signature made with the secret this gateway would verify a notification against (live or test mode; for Bizum and Google Pay, the order's own secret when it has one). False for a missing field or when no secret is configured. Changes no order and never waits. Used by `redsyslite_mark_order_as_paid()` before its wait. |
| `successful_request` | `( array\|null $params = null )` | `void` | Re-verifies and updates the order; with `null` it reads `$_POST`. Some branches end the request with `exit`. |
| `ask_for_refund` | `( int $order_id, string $transaction_id, string $amount )` | `true\|WP_Error` | Sends the refund request; `$amount` is in minor units. |
| `check_redsys_refund` | `( int $order_id )` | `bool` | True when the refund-confirmed transient exists. |
| `set_refund_confirmed` | `( int $order_id )` | `void` | Records Redsys's confirmation of a refund of the order: the transient `<order id>_redsys_refund`, for 10 minutes. Called by the notification handler; `process_refund()` clears it before asking and uses it once. |
| `process_refund` | `( int $order_id, float\|null $amount = null, string $reason = '' )` | `bool\|WP_Error` | WooCommerce's refund entry point. Clears any earlier confirmation, sends the request, then blocks for up to about 105 seconds waiting for Redsys's confirmation (the number of looks is filterable). |

Class-specific public methods:

| Class | Method | Signature | Returns | Notes |
|-------|--------|-----------|---------|-------|
| `WC_Gateway_Redsys` | `admin_notice_mcrypt_encrypt` | `static ()` | `void` | Prints an admin error only on PHP below 7.0 without `mcrypt`. |
| `WC_Gateway_Redsys` | `get_redsys_url_gateway_rest` | `()` | `string` | Test or live REST URL, used for refunds. |
| `WC_Gateway_Redsys` | `get_redsys_order` | `( int $order_id )` | `WC_Order` | `new WC_Order()`; throws for an ID that does not exist. |
| `WC_Gateway_Redsys` | `warning_checkout_test_mode` | `()` | `void` | Prints the test-mode banner. |
| Bizum, Google Pay | `check_user_test_mode` | `( int\|string $userid )` | `bool` | Bizum: true when per-user test mode is on and the ID is listed. Google Pay: always false. |
| Bizum, Google Pay | `get_redsys_url_gateway` | `( int $user_id, string $type = 'rd' )` | `string` | Test or live URL; `rd` is the redirection URL. |
| Bizum, Google Pay | `get_redsys_sha256` | `( int $user_id )` | `string` | The signing secret for the active mode. |
| Bizum, Google Pay | `warning_checkout_test_mode_bizum` | `()` | `void` | Prints the test-mode banner (same method name in both classes). |
| `WC_Gateway_Bizum_Redsys` | `disable_bizum` | `( array $available_gateways )` | `array` | Listener on `woocommerce_available_payment_gateways`; applies the transaction limit. |
| `WC_Gateway_GooglePay_Redirection_Redsys` | `check_user_show_payment_method` | `( int\|false $userid = false )` | `bool` | Test-mode visibility rule. |
| `WC_Gateway_GooglePay_Redirection_Redsys` | `show_payment_method` | `( array $available_gateways )` | `array` | Listener on `woocommerce_available_payment_gateways`. |

Bizum and Google Pay also have a private `resolve_notification_secret( RedsysLiteAPI $mi_obj ): string`, internal by design.

**Example** — reading a gateway's live object, for instance to check whether it is in test mode:

```php
$gateways = WC()->payment_gateways()->payment_gateways();
if ( isset( $gateways['bizumredsys'] ) && 'yes' === $gateways['bizumredsys']->testmode ) {
	// Bizum is in test mode.
}
```

## `WC_Gateway_Inespay_Redsys`

Extends `WC_Payment_Gateway`. All public methods are documented here.

| Method | Signature | Returns | Notes |
|--------|-----------|---------|-------|
| `is_available` | `()` | `bool` | Enabled and the customer's country is `ES`, `PT` or `IT`. |
| `disable_inespay` | `( array $available_gateways )` | `array` | Listener on `woocommerce_available_payment_gateways`; applies the transaction limit. |
| `admin_options` | `()` | `void` | Prints the settings screen. |
| `init_form_fields` | `()` | `void` | Fills `$this->form_fields`. |
| `validate_logo_field` | `( string $key, string $value )` | `string` | Called by WooCommerce when the settings are saved. Returns the posted Logo value as a clean URL (`esc_url_raw()`), or an empty string. Card, Bizum and Inespay; Google Pay has no Logo setting. |
| `process_payment` | `( int $order_id )` | `array` | Creates the pay-in; `result` `success` with the pay-in link, or `failure` with the checkout URL and an error notice. |
| `handle_callback` | `()` | `void` | The `wc-api` handler; always ends with `wp_die()` (`OK` 200 or `KO` 401). |
| `process_refund` | `( int $order_id, float\|null $amount = null, string $reason = '' )` | `true\|WP_Error` | Error codes `inespay_refund_missing_payin`, `inespay_refund_failed`, or the HTTP error. |
| `warning_checkout_test_mode_inespay` | `()` | `void` | Prints a test-mode banner; not attached to any hook. |

Protected: `is_allowed_country(): bool`, `get_api_url( string $path ): string`, `get_order_by_payin_id( string $payin_id ): WC_Order|false`.

## `RedsysLiteAPI`

A plain class with no WordPress dependency except `wp_json_encode()`. It holds one request or one notification in memory. Create a new instance for each message.

| Method | Signature | Returns | Notes |
|--------|-----------|---------|-------|
| `set_parameter` | `( string $key, mixed $value )` | `void` | Adds a request parameter. |
| `get_parameter` | `( string $key )` | `mixed\|null` | Reads a parameter, including those loaded by a decode. `null` when the key is absent or the decoded data was not a JSON object. |
| `create_merchant_parameters` | `()` | `string` | Base64 of the JSON of all parameters. |
| `create_merchant_signature` | `( string $key )` | `string` | Request signature, Base64. `$key` is the Base64 merchant secret. Needs `DS_MERCHANT_ORDER` set. |
| `decode_merchant_parameters` | `( string $datos )` | `string` | Decodes Base64URL and loads the fields; returns the decoded JSON text. |
| `create_merchant_signature_notif` | `( string $key, string $datos )` | `string` | Notification signature, Base64URL, for comparison with `Ds_Signature`. When `$datos` names no order (no `Ds_Order`, an empty one, or data that is not JSON) or `$key` is empty, the notification cannot be authenticated and the method returns the encoding of 32 fresh random bytes: never empty, different on every call, so no comparison can succeed. Always compare with `hash_equals()`. |
| `sanitize_merchant_parameters` | `static ( string $raw )` | `string` | Restores `+` and strips everything outside the Base64/Base64URL alphabet. |
| `mac256` | `( string $ent, string $key )` | `string` | Raw HMAC-SHA256. |
| `encrypt_3des` | `( string $message, string $key )` | `string` | Key diversification (3DES-CBC, zero IV, zero padding). |
| `base64_url_encode`, `encode_base64`, `base64_url_decode`, `decode_base64` | `( string )` | `string` | Encoding helpers. |
| `get_order`, `get_order_notif` | `()` | `string` | The order number of the request / of the decoded notification. |
| `array_to_json` | `()` | `string` | JSON of the parameters. |

Listed only, not read for this document: `get_order_notif_soap`, `get_request_notif_soap`, `get_response_notif_soap`, `string_to_array`, `create_merchant_signature_notif_soap_request`, `create_merchant_signature_notif_soap_response`.

**Example** — verifying a notification by hand, as the tests do:

```php
$api      = new RedsysLiteAPI();
$data     = RedsysLiteAPI::sanitize_merchant_parameters( $received_parameters );
$expected = $api->create_merchant_signature_notif( $merchant_secret, $data );
if ( hash_equals( $expected, $received_signature ) ) {
	$response = (int) $api->get_parameter( 'Ds_Response' );
}
```

`$merchant_secret` is the value of the gateway's secret setting; read it from the settings, never hard-code it.

To ask a gateway whether a return is genuine, without touching the order:

```php
$gateways = WC()->payment_gateways()->payment_gateways();
$params   = array(
	'Ds_MerchantParameters' => $received_parameters,
	'Ds_Signature'          => $received_signature,
);
if ( isset( $gateways['redsys'] ) && $gateways['redsys']->is_valid_return( $params ) ) {
	// The parameters were signed with this store's secret.
}
```

## `WC_Gateway_Redsys_Global_Lite`

Shared helpers. `WCRedL()` returns a **new** instance on every call, so the object keeps no state between calls.

Documented:

| Method | Signature | Returns | Notes |
|--------|-----------|---------|-------|
| `get_redsys_option` | `( string $option, string $gateway )` | `mixed\|false` | One key of the option `woocommerce_<gateway>_settings`; false when absent. |
| `get_order` | `( int $order_id )` | `WC_Order` | `new WC_Order()`; throws for an ID that does not exist. |
| `get_order_meta` | `( int $order_id, string $key, bool $single = true )` | `mixed\|false` | False when the order does not exist. |
| `update_order_meta` | `( int $post_id, array\|string $meta_key_array, mixed $meta_value = false )` | `void` | Accepts one key and value, or an array of key/value pairs; saves the order. |
| `is_redsys_order` | `( int $order_id, string\|null $type = null )` | `bool` | True when the order's payment method is in `redsys_return_types()`, or equals `$type`. |
| `get_gateway` | `( int $order_id )` | `string\|false` | The order's payment method ID. |
| `get_status_pending` | `()` | `string[]` | The unpaid statuses; applies `redsys_status_pending`. |
| `is_paid` | `( int $order_id )` | `bool` | False when the order does not exist or its status is in the unpaid list. |
| `is_gateway_enabled` | `( string $gateway )` | `bool` | The `enabled` setting equals `yes`. |
| `prepare_order_number` | `( int $order_id )` | `string` | 12-character Redsys order number; stores the mapping transient for one hour. |
| `clean_order_number` | `( string $ordernumber )` | `string` | The order ID for a Redsys order number. |
| `get_order_id_by_redsys_order_number` | `( string $ordernumber )` | `int\|false` | Looks the number up in order meta. |
| `redsys_amount_format` | `( float $total )` | `string` | Amount in minor units, no separators. |
| `product_description` | `( WC_Order $order, string $gateway )` | `string\|null` | Description sent to Redsys; null for an order that is not a Redsys-type order. |
| `get_cancel_url_raw` | `( WC_Order\|object $order )` | `string` | The order's cancel URL with literal `&` separators — `get_cancel_order_url_raw()` when the order has it, otherwise the HTML-escaped URL decoded; empty when `$order` is not an object or has neither method. Use it wherever the URL is sent or redirected to, never inside an `href`. Example: `$url = WCRedL()->get_cancel_url_raw( wc_get_order( $order_id ) );` |
| `get_psd2_arg` | `( WC_Order $order, string $gateway )` | `string` | The PSD2 block when the gateway's stored `psd2` setting is `yes`, otherwise empty. |

Listed only, not read for this document: `debug`, `clean_data`, `set_txnid`, `set_token_type`, `get_txnid`, `get_token_type`, `get_ds_error`, `get_ds_response`, `get_msg_error`, `get_country_codes_phone`, `get_country_codes_2`, `get_country_codes`, `get_country_codes_3`, `is_ds_error`, `is_ds_response`, `is_msg_error`, `get_msg_error_by_code`, `get_error_by_code`, `get_response_by_code`, `is_redsys_error`, `get_error`, `get_error_type`, `get_currencies`, `allowed_currencies`, `get_redsys_languages`, `get_redsys_wp_languages`, `get_orders_type`, `get_lang_code`, `order_exist`, `post_exist`, `get_order_mumber`, `get_order_date`, `get_order_hour`, `get_order_auth`, `check_if_token_is_valid`, `check_type_exist_in_tokens`, `get_redsys_users_token`, `get_users_token_bulk`.

**Example:**

```php
if ( function_exists( 'WCRedL' ) && WCRedL()->is_redsys_order( $order_id ) && WCRedL()->is_paid( $order_id ) ) {
	$authorisation = WCRedL()->get_order_meta( $order_id, '_authorisation_code_redsys' );
}
```

`WCRedL()` is defined only after `woocommerce_loaded`, and only when WooCommerce is active; guard the call as shown.

## `WC_Gateway_Redsys_PSD2_Light`

Builds the 3-D Secure data block for the card gateway. Reached through `WCPSD2L()`, which also returns a new instance on every call. The only method used by the documented flows is `get_acctinfo( WC_Order $order, $user_data_3ds = false, $user_id = false )`, whose return value the card gateway sends as `Ds_Merchant_EMV3DS`. Its body was not read for this document.

Listed only, not read for this document: `clean_data`, `debug`, `get_redsys_option`, `get_email`, `get_homephone`, `get_work`, `get_adress_ship`, `addr_match`, `get_challenge_wwndow_size`, `days`, `get_post_num`, `get_accept_headers`, `get_agente_navegador`, `get_idioma_navegador`, `get_altura_pantalla`, `get_anchura_pantalla`, `get_profundidad_color`, `get_diferencia_horaria`, `get_browserjavaenabled`, the same eight with the suffix `_user`, `shipnameindicator`, `get_acctinfo`.

## Blocks integrations

Four `final` classes extending WooCommerce's `AbstractPaymentMethodType`, registered on `woocommerce_blocks_payment_method_type_registration` (`woocommerce-redsys.php:342`). Each implements the four methods WooCommerce calls:

| Method | Returns | Notes |
|--------|---------|-------|
| `initialize()` | `void` | Loads the option `woocommerce_<gateway id>_settings`. Read in the Inespay class; the other three were not read at this method. |
| `is_active()` | `bool` | Inespay: enabled and the customer's country is `ES`, `PT` or `IT`. The other three were not read at this method. |
| `get_payment_method_script_handles()` | `string[]` | Registers the script built from `resources/js/frontend/index.js` (`assets/js/frontend/blocks.js`). Read in the Inespay class, where the handle is `wc-inespayredsys-payments-blocks`. |
| `get_payment_method_data()` | `array` | `title`, `description`, `icon` (through the gateway's `_icon` filter, then `esc_url_raw()`) and `supports`. Read in all four. |

## `Redsys_Lite_Apps_Plugins`

A `final` class with one public static method, `render(): void`, called from `redsys_about_page()` (`about-redsys.php:15`). It prints the "Other plugins, Skills & APPS" page from six private data methods, each of which applies one `redsys_lite_apps_plugins_*` filter. Everything else in the class is private.
