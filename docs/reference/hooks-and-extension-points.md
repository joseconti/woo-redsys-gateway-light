# Hooks and extension points

> As-built reference, written from the code on 2026-10-06. It lists every action and filter this plugin **fires** — every `do_action()` and `apply_filters()` call in the plugin's own PHP (`woocommerce-redsys.php`, `about-redsys.php`, `classes/`, `includes/`), excluding `vendor/`, `node_modules/` and `tests/`. The plugin has no `do_action_ref_array()`, `apply_filters_ref_array()` or deprecated-hook calls.
>
> Count: **34 call sites, 20 distinct hook names** — 6 actions and 14 filters. One of the filters (`woocommerce_ajax_loader_url`) belongs to WooCommerce and is only applied here.
>
> "Since" is the `@since` tag in the source docblock where one exists. Those tags are not consistent with the plugin's release history and were not checked against it; treat them as the author's annotation, not as a verified version.

## How to use the examples
Every example is ordinary WordPress code. Put it in a small plugin of your own or in a must-use plugin (`wp-content/mu-plugins/<name>.php`); do not edit this plugin's files. Hooks fired in a gateway constructor (the icon filters) run when WooCommerce builds its gateway list, so the listener must be registered before that — a must-use plugin, or any plugin loaded on `plugins_loaded`, is early enough.

## Summary

| # | Hook | Kind | Fired from |
|---|------|------|------------|
| 1 | `valid_redsys_standard_ipn_request` | action | `classes/class-wc-gateway-redsys.php:920` |
| 2 | `valid_bizumredsys_standard_ipn_request` | action | `classes/class-wc-gateway-bizum-redsys.php:1165` |
| 3 | `valid_googlepayredirecredsys_standard_ipn_request` | action | `classes/class-wc-gateway-googlepay-redirection-redsys.php:927` |
| 4 | `googlepayredirecredsys_post_payment_complete` | action | `classes/class-wc-gateway-googlepay-redirection-redsys.php:1266` |
| 5 | `googlepayredirecredsys_post_payment_error` | action | `classes/class-wc-gateway-googlepay-redirection-redsys.php:1316` |
| 6 | `inespay_post_payment_complete` | action | `classes/class-wc-gateway-inespay-redsys.php:687` |
| 7 | `woocommerce_redsys_args` | filter | `classes/class-wc-gateway-redsys.php:701`, `classes/class-wc-gateway-bizum-redsys.php:909` |
| 8 | `woocommerce_googlepayredirecredsys_args` | filter | `classes/class-wc-gateway-googlepay-redirection-redsys.php:724` |
| 9 | `woocommerce_redsys_icon` | filter | `classes/class-wc-gateway-redsys.php:243`, `:245`; `includes/blocks/class-wc-gateway-redsys-lite-support.php:89`, `:91` |
| 10 | `woocommerce_bizumredsys_icon` | filter | `classes/class-wc-gateway-bizum-redsys.php:272`, `:281`; `includes/blocks/class-wc-gateway-bizum-lite-support.php:89`, `:91` |
| 11 | `woocommerce_googlepayredirecredsys_icon` | filter | `classes/class-wc-gateway-googlepay-redirection-redsys.php:204`; `includes/blocks/class-wc-gateway-googlepay-redirection-redsys-support.php:87` |
| 12 | `woocommerce_inespayredsys_icon` | filter | `classes/class-wc-gateway-inespay-redsys.php:123`, `:125`; `includes/blocks/class-wc-gateway-inespay-lite-support.php:105`, `:107` |
| 13 | `redsys_status_pending` | filter | `classes/class-wc-gateway-redsys-global-lite.php:871` |
| 14 | `redsys_lite_apps_plugins_mac_app` | filter | `includes/class-redsys-lite-apps-plugins.php:369` |
| 15 | `redsys_lite_apps_plugins_free` | filter | `includes/class-redsys-lite-apps-plugins.php:420` |
| 16 | `redsys_lite_apps_plugins_premium` | filter | `includes/class-redsys-lite-apps-plugins.php:519` |
| 17 | `redsys_lite_apps_plugins_webs` | filter | `includes/class-redsys-lite-apps-plugins.php:570` |
| 18 | `redsys_lite_apps_plugins_skills` | filter | `includes/class-redsys-lite-apps-plugins.php:634` |
| 19 | `redsys_lite_apps_plugins_profiles` | filter | `includes/class-redsys-lite-apps-plugins.php:674` |
| 20 | `woocommerce_ajax_loader_url` (WooCommerce's own) | filter | `classes/class-wc-gateway-redsys.php:738`, `:777`; `classes/class-wc-gateway-bizum-redsys.php:942`; `classes/class-wc-gateway-googlepay-redirection-redsys.php:757` |
| 21 | `woocommerce_redsys_refund_confirmation_attempts`, `woocommerce_bizumredsys_refund_confirmation_attempts`, `woocommerce_googlepayredirecredsys_refund_confirmation_attempts` | filter | `classes/class-wc-gateway-redsys.php:1384`; `classes/class-wc-gateway-bizum-redsys.php:1780`; `classes/class-wc-gateway-googlepay-redirection-redsys.php:1561` |

Five of these names are built at run time from the gateway ID (`'valid_' . $this->id . '_standard_ipn_request'`, `'woocommerce_' . $this->id . '_icon'`, `'woocommerce_' . $this->id . '_args'`, `$this->id . '_post_payment_complete'`, `$this->id . '_post_payment_error'`). The names above are the resolved ones; searching the code for the full string does not find those call sites.

All hooks predate the project's adoption into Keel, so none has an introducing slice.

---

## Actions

### 1–3. `valid_<gateway id>_standard_ipn_request`

`valid_redsys_standard_ipn_request`, `valid_bizumredsys_standard_ipn_request`, `valid_googlepayredirecredsys_standard_ipn_request`

- **Kind:** action.
- **Fired from:** `check_ipn_response()` — `classes/class-wc-gateway-redsys.php:920`, `classes/class-wc-gateway-bizum-redsys.php:1165` (since 2.0.0), `classes/class-wc-gateway-googlepay-redirection-redsys.php:927` (since 1.0.0).
- **Parameters:**
  - `$post` (`array`) — the notification's `POST` fields after `stripslashes_deep()`, not otherwise sanitized. Expected keys: `Ds_SignatureVersion`, `Ds_MerchantParameters` (Base64 JSON), `Ds_Signature`.
- **When:** each time a Redsys notification for that gateway passes signature validation, immediately after the `200` status header is sent. It fires for payment notifications and for refund notifications alike, and before the order has been updated.
- **Used internally:** each gateway registers its own `successful_request()` on this action at priority 10. That method is what updates the order, so a listener at a priority below 10 runs before the order changes and one above 10 runs after — but several branches of `successful_request()` end the request (`exit`), in which case later listeners never run.
- **Reading the payload:** decode `Ds_MerchantParameters` with `RedsysLiteAPI` rather than by hand.

```php
add_action(
	'valid_redsys_standard_ipn_request',
	function ( $post ) {
		if ( empty( $post['Ds_MerchantParameters'] ) ) {
			return;
		}
		$api = new RedsysLiteAPI();
		$api->decode_merchant_parameters(
			RedsysLiteAPI::sanitize_merchant_parameters( $post['Ds_MerchantParameters'] )
		);
		wc_get_logger()->info(
			sprintf(
				'Redsys notification: order %s, response %s',
				$api->get_parameter( 'Ds_Order' ),
				$api->get_parameter( 'Ds_Response' )
			),
			array( 'source' => 'my-redsys-listener' )
		);
	},
	5
);
```

### 4. `googlepayredirecredsys_post_payment_complete`

- **Kind:** action. **Since:** 2.0.0.
- **Fired from:** `successful_request()` — `classes/class-wc-gateway-googlepay-redirection-redsys.php:1266`.
- **Parameters:**
  - `$order_id` (`int`) — the WooCommerce order ID.
- **When:** after an authorised Google Pay notification whose amount matched: the payment meta is saved, the notes are added and `payment_complete()` has run.

```php
add_action(
	'googlepayredirecredsys_post_payment_complete',
	function ( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( $order ) {
			$order->add_order_note( 'Google Pay payment confirmed; warehouse notified.' );
		}
	}
);
```

### 5. `googlepayredirecredsys_post_payment_error`

- **Kind:** action. **Since:** 2.0.0.
- **Fired from:** `successful_request()` — `classes/class-wc-gateway-googlepay-redirection-redsys.php:1316`.
- **Parameters:**
  - `$order_id` (`int`) — the WooCommerce order ID.
  - `$error` (`string`) — the Redsys response text and the Redsys error text joined by a space; either part can be empty.
- **When:** after a denied Google Pay notification (`Ds_Response` above 99): the order is already `cancelled` and the cart emptied.

```php
add_action(
	'googlepayredirecredsys_post_payment_error',
	function ( $order_id, $error ) {
		wc_get_logger()->warning(
			sprintf( 'Google Pay denied for order %d: %s', $order_id, trim( $error ) ),
			array( 'source' => 'my-redsys-listener' )
		);
	},
	10,
	2
);
```

### 6. `inespay_post_payment_complete`

- **Kind:** action.
- **Fired from:** `handle_callback()` — `classes/class-wc-gateway-inespay-redsys.php:687`.
- **Parameters:**
  - `$order_id` (`int`) — the WooCommerce order ID.
- **When:** after a correctly signed Inespay callback with status `OK` or `SETTLED` for an order that still needed payment, once `payment_complete()` has run and the meta `_payment_method` and `_redsys_done` are saved. It does not fire for callbacks about an already-resolved order, nor when the amount or currency check puts the order on hold.
- **Naming:** this is the only action not prefixed with the gateway ID (`inespay_…`, not `inespayredsys_…`).

```php
add_action(
	'inespay_post_payment_complete',
	function ( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( $order ) {
			$order->update_meta_data( '_my_bank_transfer_confirmed', gmdate( 'c' ) );
			$order->save();
		}
	}
);
```

---

## Filters

### 7. `woocommerce_redsys_args`

- **Kind:** filter. **Since:** 2.0.0 (docblock at the Bizum call site).
- **Applied from:** `get_redsys_args()` — `classes/class-wc-gateway-redsys.php:701` (card) **and** `classes/class-wc-gateway-bizum-redsys.php:909` (Bizum). Bizum reuses the card gateway's filter name; there is no `woocommerce_bizumredsys_args`.
- **Parameters:**
  - `$redsys_args` (`array`) — the three fields of the payment form: `Ds_SignatureVersion` (`string`, `HMAC_SHA256_V1`), `Ds_MerchantParameters` (`string`, Base64 JSON of the request), `Ds_Signature` (`string`, Base64).
- **Returns:** the array whose keys and values become the hidden inputs of the payment form.
- **When:** each time the order-pay page builds the form for a card or Bizum order.
- **Limits:** the order is not passed, and the parameters are already encoded and signed. Changing `Ds_MerchantParameters` without re-signing makes Redsys reject the request; this filter is useful for adding extra form fields or for inspection, not for changing the amount or the order number. Because the name is shared, a listener cannot tell card from Bizum except by decoding `DS_MERCHANT_PAYMETHODS` (`z` for Bizum).

```php
add_filter(
	'woocommerce_redsys_args',
	function ( $redsys_args ) {
		$decoded = json_decode( base64_decode( $redsys_args['Ds_MerchantParameters'] ), true );
		wc_get_logger()->debug(
			'Redsys request for order number ' . ( $decoded['DS_MERCHANT_ORDER'] ?? 'unknown' ),
			array( 'source' => 'my-redsys-listener' )
		);
		return $redsys_args;
	}
);
```

### 8. `woocommerce_googlepayredirecredsys_args`

- **Kind:** filter. **Since:** 1.0.0.
- **Applied from:** `get_redsys_args()` — `classes/class-wc-gateway-googlepay-redirection-redsys.php:724`.
- **Parameters / returns / limits:** identical to `woocommerce_redsys_args`.
- **When:** each time the order-pay page builds the form for a Google Pay order.

```php
add_filter(
	'woocommerce_googlepayredirecredsys_args',
	function ( $redsys_args ) {
		$redsys_args['my_tracking_field'] = 'gpay';
		return $redsys_args;
	}
);
```

### 9–12. `woocommerce_<gateway id>_icon`

`woocommerce_redsys_icon`, `woocommerce_bizumredsys_icon`, `woocommerce_googlepayredirecredsys_icon`, `woocommerce_inespayredsys_icon`

- **Kind:** filter. **Since:** 1.0.0 (Bizum docblock), 6.0.0 (Google Pay docblock).
- **Applied from:** each gateway's constructor (classic checkout and admin) and each Blocks integration's `get_payment_method_data()` (Blocks checkout) — file and line in the summary table.
- **Parameters:**
  - `$icon_url` (`string`) — the URL of the gateway's icon. For card, Bizum and Inespay it is the `logo` setting (stored as a URL: validated when the settings are saved) when that is filled in, otherwise the bundled image (`assets/images/redsys.png`, `assets/images/bizum.png`, `assets/images/inespay.svg`). Google Pay has no `logo` setting and always passes `assets/images/GPay-peque.svg`.
- **Returns:** the URL to use. It must be a URL: the value returned is passed through `esc_url()` before it becomes the gateway icon (WooCommerce prints the icon inside an image tag without escaping it) and through `esc_url_raw()` before it is handed to the Blocks checkout script. Anything that is not part of a URL is dropped, and a scheme WordPress does not allow yields an empty string.
- **When:** every time the gateway object is constructed, and every time the Blocks checkout collects payment-method data. Both places must be considered: the same filter name covers both, so one listener changes the icon everywhere.

```php
add_filter(
	'woocommerce_bizumredsys_icon',
	function ( $icon_url ) {
		return content_url( 'uploads/branding/bizum-dark.svg' );
	}
);
```

### 13. `redsys_status_pending`

- **Kind:** filter.
- **Applied from:** `WC_Gateway_Redsys_Global_Lite::get_status_pending()` — `classes/class-wc-gateway-redsys-global-lite.php:871`.
- **Parameters:**
  - `$status` (`string[]`) — order statuses, without the `wc-` prefix, in which an order counts as **not yet paid**. Default, from `includes/data/redsys-status-paid.php`: `pending`, `redsys-pbankt`, `cancelled`, `pending-deposit`.
- **Returns:** the list of statuses.
- **When:** on every `WCRedL()->is_paid()` call — while a notification is processed, in the order-received fallback and when the order-received text is built.
- **Effect:** an order whose status is in the list is treated as unpaid, so a notification may still complete it. Any other status — including `on-hold` and `failed` — counts as paid, and payment notifications for it are ignored by the card and Bizum gateways.

```php
add_filter(
	'redsys_status_pending',
	function ( $status ) {
		$status[] = 'failed'; // Let a later successful notification complete a failed order.
		return $status;
	}
);
```

### 14. `redsys_lite_apps_plugins_mac_app`

- **Kind:** filter. **Since:** 7.0.2.
- **Applied from:** `Redsys_Lite_Apps_Plugins::get_mac_app()` — `includes/class-redsys-lite-apps-plugins.php:369`.
- **Parameters:**
  - `$defaults` (`array`) — the featured desktop-app card: `name`, `badge`, `logo` (URL), `desc`, `version`, `requirements`. The result is cast to `array`.
- **When:** each time WooCommerce → About Redsys is rendered.

```php
add_filter(
	'redsys_lite_apps_plugins_mac_app',
	function ( $defaults ) {
		$defaults['badge'] = '';
		return $defaults;
	}
);
```

### 15–19. `redsys_lite_apps_plugins_free`, `_premium`, `_webs`, `_skills`, `_profiles`

- **Kind:** filter. **Since:** 7.0.2.
- **Applied from:** `includes/class-redsys-lite-apps-plugins.php:420` (`_free`), `:519` (`_premium`), `:570` (`_webs`), `:634` (`_skills`), `:674` (`_profiles`).
- **Parameters:**
  - `$items` (`array[]`) — the list shown in that section of the About page. Each item is an array with `ini` (short text for the badge), `bg` and `fg` (hex colors), `name` and `url`; every list except `_profiles` also has `desc`, and `_skills` also has `badge`.
- **Returns:** the list. It is cast to `array`, and a `host` key derived from each item's `url` is added afterwards, so a `host` set by a listener is overwritten.
- **When:** each time WooCommerce → About Redsys is rendered.

```php
add_filter(
	'redsys_lite_apps_plugins_premium',
	'__return_empty_array'
);
```

### 20. `woocommerce_ajax_loader_url` (WooCommerce's filter, applied here)

- **Kind:** filter, owned by WooCommerce — documented here because this plugin applies it, not because it defines it.
- **Applied from:** `generate_redsys_form()` — `classes/class-wc-gateway-redsys.php:738` and `:777`, `classes/class-wc-gateway-bizum-redsys.php:942`, `classes/class-wc-gateway-googlepay-redirection-redsys.php:757`.
- **Parameters:**
  - `$url` (`string`) — the spinner image shown in the "redirecting" overlay on the order-pay page; default WooCommerce's `assets/images/select2-spinner.gif`.
- **When:** each time the order-pay form is built for a card, Bizum or Google Pay order.

```php
add_filter(
	'woocommerce_ajax_loader_url',
	function ( $url ) {
		return content_url( 'uploads/branding/spinner.gif' );
	}
);
```

### 21. `woocommerce_<gateway id>_refund_confirmation_attempts`

`woocommerce_redsys_refund_confirmation_attempts`, `woocommerce_bizumredsys_refund_confirmation_attempts`, `woocommerce_googlepayredirecredsys_refund_confirmation_attempts`

- **Kind:** filter. Added by S-049 (unreleased).
- **Applied from:** each gateway's `process_refund()`, after the refund request has been sent — file and line in the summary table.
- **Parameters:**
  - `$attempts` (`int`) — how many more times, five seconds apart, the gateway looks for Redsys's confirmation after its first look. Default `20`, about 105 seconds in all.
  - `$order_id` (`int`) — the order being refunded.
- **Returns:** the number to use. It is cast to an integer; zero, a negative number or anything that is not a number means one look only.
- **When:** once per refund. The request to Redsys has already left: a lower number shortens how long the order screen waits, it does not cancel the refund, and a refund Redsys confirms after the wait is still reported as failed.

```php
// Wait up to about three minutes on a host where Redsys's notification is slow to arrive.
add_filter(
	'woocommerce_redsys_refund_confirmation_attempts',
	function ( $attempts, $order_id ) {
		return 35;
	},
	10,
	2
);
```

---

## What is not exposed

Recorded so that an absence is not mistaken for an oversight in this document. None of these exists in the code today:

- No hook fires after a completed or a denied payment for the **card** or **Bizum** gateways; only Google Pay and Inespay have a "payment complete" action, and only Google Pay has a "payment error" action.
- No hook fires on the Inespay callback before the order is updated, and none on a rejected notification or callback for any gateway.
- The request sent to Redsys cannot be changed before it is signed: the `_args` filters receive the already-signed fields.
- The Inespay pay-in payload and refund payload have no filter, and there is no `woocommerce_inespayredsys_args`.
- Refunds fire no hook in any gateway.
- User-facing strings (order-pay messages, order notes, banners) are translatable through the text domain `woo-redsys-gateway-light`, but have no dedicated filter.
- The settings fields of the four gateways have no filter of this plugin's own.

Closing any of these is new work, to be specified and built as its own slice.

## Related
- `docs/api/INDEX.md` — one row per hook.
- `docs/reference/endpoints.md` — the HTTP entry points that lead to these hooks.
- `docs/flows/notification-handling.md` — where each action sits in the notification flow.
