# Functions

> As-built reference, written from the code on 2026-10-06. It covers every global function the plugin defines: all of `woocommerce-redsys.php` and `about-redsys.php` (both read in full), and the data functions under `includes/data/` (two read in full, the rest listed by name only — see the end of this file).
>
> The functions are global and unnamespaced. Several carry generic names with no plugin prefix (`plugin_url_redsys`, `add_redsys_meta_box`, `mostrar_numero_autentificacion`); they are documented as they are.

## Availability

Functions marked **after WooCommerce** are declared inside `woocommerce_gateway_redsys_init()`, which runs on `woocommerce_loaded` at priority 11 and returns early when `WC_Payment_Gateway` does not exist. They do not exist before that hook, nor on a site without WooCommerce. Guard calls from other code with `function_exists()`.

## Constants

Defined at the top of `woocommerce-redsys.php`.

| Constant | Value |
|----------|-------|
| `REDSYS_WOOCOMMERCE_VERSION` | The plugin version (`7.0.2`). |
| `REDSYS_PLUGIN_URL` | URL of the plugin directory, with a trailing slash. |
| `REDSYS_PLUGIN_PATH` | Filesystem path of the plugin directory, with a trailing slash. |
| `REDSYS_PLUGIN_DATA_PATH`, `REDSYS_PLUGIN_DATA_URL` | Path and URL of `includes/data/`. |
| `REDSYS_PLUGIN_CLASS_PATH` | Path of `classes/`. |
| `REDSYS_POST_UPDATE_URL`, `REDSYS_TELEGRAM_URL`, `REDSYS_TELEGRAM_SIGNUP`, `REDSYS_REVIEW`, `REDSYS_DONATION` | Links used in the admin notices. |

## Accessors

### `WCRedL()` — after WooCommerce
- **Signature:** `WCRedL(): WC_Gateway_Redsys_Global_Lite` (`woocommerce-redsys.php:159`).
- **Returns:** a new helper object on every call.
- **Side effects:** loads `classes/class-wc-gateway-redsys-global-lite.php` on first use.

```php
if ( function_exists( 'WCRedL' ) ) {
	$is_paid = WCRedL()->is_paid( $order_id );
}
```

### `WCPSD2L()` — after WooCommerce
- **Signature:** `WCPSD2L(): WC_Gateway_Redsys_PSD2_Light` (`woocommerce-redsys.php:171`).
- **Returns:** a new PSD2 helper object on every call.

```php
if ( function_exists( 'WCPSD2L' ) ) {
	$psd2_block = WCPSD2L()->get_acctinfo( wc_get_order( $order_id ) );
}
```

### `redsyslite_asset_suffix()`
- **Signature:** `redsyslite_asset_suffix(): string` (`woocommerce-redsys.php`). Available as soon as the plugin file is loaded.
- **What it does:** returns `'.min'`, or `''` when the constant `SCRIPT_DEBUG` is defined and true. Every stylesheet and script the plugin enqueues is named through it, so production loads the minified file and a debugging site loads the readable one.

```php
wp_enqueue_style( 'my-handle', REDSYS_PLUGIN_URL . 'assets/css/redsys-css' . redsyslite_asset_suffix() . '.css', array(), REDSYS_WOOCOMMERCE_VERSION );
```

### `plugin_abspath_redsys()` and `plugin_url_redsys()` — after WooCommerce
- **Signatures:** `plugin_abspath_redsys(): string` (`woocommerce-redsys.php:325`) — the plugin directory path with a trailing slash; `plugin_url_redsys(): string` (`:331`) — the plugin URL without a trailing slash.
- **Used by:** the Blocks integrations, to locate `assets/js/frontend/blocks.js` and `languages/`.

```php
$script = plugin_url_redsys() . '/assets/js/frontend/blocks.js';
```

## Payment handling

### `redsyslite_mark_order_as_paid( $order_id )`
- **Signature:** `redsyslite_mark_order_as_paid( int $order_id ): void` (`woocommerce-redsys.php:401`).
- **What it does:** the fallback behind the order-received page (`docs/flows/notification-handling.md`, part C). Stops if it already ran for this order in the last 30 seconds or if the order is already paid; otherwise waits 5 seconds, and if the order belongs to one of the three Redsys-protocol gateways and is still unpaid, hands `Ds_MerchantParameters` and `Ds_Signature` from the query string to the gateway's `successful_request()`.
- **Side effects:** sets the transient `redsyslite_mark_paid_attempt_<order id>` for 30 seconds; clears the order caches; blocks for 5 seconds; may update the order.
- **Errors:** none returned. It reads `$_GET` directly, so it only does something useful in the request it was written for.

```php
// Runs by itself on the order-received page. Shown only to make the contract explicit:
redsyslite_mark_order_as_paid( $order_id );
```

### `redsyslite_force_mark_order_as_paid_on_thankyou_page()`
- **Signature:** `(): void` (`woocommerce-redsys.php:457`), attached to `wp_head`.
- **What it does:** on the order-received page, when the query string has `key` and `Ds_MerchantParameters`, resolves the order from the key and calls `redsyslite_mark_order_as_paid()`.

### `redsyslite_allow_cancel_return_for_cancelled_order( $statuses, $order )`
- **Signature:** `redsyslite_allow_cancel_return_for_cancelled_order( array $statuses, WC_Order|null $order = null ): array` (`woocommerce-redsys.php`), attached to WooCommerce's filter `woocommerce_valid_order_statuses_for_cancel`, priority 10.
- **What it does:** when Redsys refuses or the customer abandons a payment, the notification sets the order `cancelled` and Redsys then sends the customer to the order's cancel URL. WooCommerce only cancels `pending` and `failed` orders and answered that return with "Your order can no longer be cancelled". This callback adds `cancelled` to the list for one order only, so WooCommerce shows its own "Your order was cancelled." notice.
- **Conditions, all required:** the request carries `cancel_order`, `order_id` and a `_wpnonce` valid for `woocommerce-cancel_order`; `order_id` is the ID of the order being checked; that order is already `cancelled`; its payment method is `redsys`, `bizumredsys` or `googlepayredirecredsys`. Anything else returns `$statuses` unchanged — the My Account order list included.
- **Side effects:** none of its own. WooCommerce then clears the session's `order_awaiting_payment` and fires `woocommerce_cancelled_order`; the status does not change, so no order note is added and no status-transition hook fires (measured on WooCommerce 7.4).

```php
// Restore WooCommerce's stock behaviour for those returns:
remove_filter( 'woocommerce_valid_order_statuses_for_cancel', 'redsyslite_allow_cancel_return_for_cancelled_order', 10 );
```

### `redsyslite_bust_order_cache( $order_id )`
- **Signature:** `redsyslite_bust_order_cache( int $order_id ): void` (`woocommerce-redsys.php:384`).
- **What it does:** clears the post cache, the `posts`, `post_meta`, `orders` and `order_meta` object-cache entries, and WooCommerce's `OrderCache` entry when that class exists, so the next read of the order comes from the database. Works for both order storage modes.

```php
redsyslite_bust_order_cache( $order_id );
$order = wc_get_order( $order_id ); // Fresh from the database.
```

## Bootstrap and admin

| Function | Signature | Hooked to | What it does |
|----------|-----------|-----------|--------------|
| `redsys_language_init` | `(): void` (`:67`) | `plugins_loaded`, 10 | Loads the text domain `woo-redsys-gateway-light` from `languages/`. |
| `woocommerce_gateway_redsys_init` | `(): void` (`:141`) | `woocommerce_loaded`, 11 | Declares the functions marked "after WooCommerce", registers the admin menu, notices and gateways, and loads the four gateway classes. |
| `woocommerce_add_gateway_redsys_gateway` | `( array $methods ): array` (`:271`) | filter `woocommerce_payment_gateways` | Appends the four gateway class names. After WooCommerce. |
| `woocommerce_gateway_redsys_lite_block_support` | `(): void` (`:342`) | `woocommerce_blocks_loaded` | Loads and registers the four Blocks integrations when WooCommerce Blocks is available. After WooCommerce. |
| `redsys_menu` | `(): void` (`:148`) | `admin_menu` | Adds WooCommerce → About Redsys (`redsys-about-page`, capability `manage_options`). After WooCommerce. |
| `redsys_about_page` | `(): void` (`about-redsys.php:15`) | menu callback | Prints the About page and calls `Redsys_Lite_Apps_Plugins::render()`. |
| `redsys_welcome_splash` | `(): void` (`:99`) | `admin_init`, 1 | When the stored option `woocommerce-redsys-version` differs from the running version — and the request is not `update.php` or `update-core.php` — stores the new version, stores the first-seen time in `woocommerce-redsys-rate` if absent, and redirects to the About page. |
| `redsys_get_parent_page` | `(): string` (`:74`) | — | Base name of the running script; used by `redsys_welcome_splash()`. |
| `redsys_lite_add_notice_new_version` | `(): void` (`:188`) | `admin_notices` | Prints the "updated to version…" notice until dismissed for the running version. After WooCommerce. |
| `redsys_lite_ask_for_telegram` | `(): void` (`:228`) | `admin_notices` | Prints the Telegram-channel notice until dismissed. After WooCommerce. |
| `redsys_styles_css` | `( string $hook ): void` (`:84`) | `admin_enqueue_scripts` | Enqueues `assets/css/welcome.css` on the About page only. |
| `redsys_css_lite` | `(): void` (`:125`) | `admin_enqueue_scripts` | Enqueues `assets/css/redsys-css.css` on the WooCommerce settings screen. |
| `redsys_lite_notice_style` | `(): void` (`:260`) | `admin_enqueue_scripts` | Enqueues `assets/css/redsys-notice.css` on every admin screen. After WooCommerce. |
| `add_redsys_meta_box` | `( WC_Order $post_or_order_object ): void` (`:285`) | `woocommerce_admin_order_data_after_billing_address` | Prints the payment details block on the admin order screen for Redsys-type orders. After WooCommerce. |
| `mostrar_numero_autentificacion` | `( string $text, WC_Order $order ): string` (`:478`) | filter `woocommerce_thankyou_order_received_text`, 20 | Returns the transaction-details text for a paid Redsys-type order, otherwise `$text` unchanged. |
| `redsys_lite_add_head_text` | `(): void` (`:318`) | `wp_head` | Prints an HTML comment with the plugin name and version on every front-end page. After WooCommerce. |

All line references in this table are in `woocommerce-redsys.php` unless a file is named.

These are listeners, not an API: none is meant to be called directly. To change what one does, remove it and register your own, for example:

```php
add_action(
	'woocommerce_loaded',
	function () {
		remove_action( 'wp_head', 'redsys_lite_add_head_text' );
	},
	20
);
```

## Data functions (`includes/data/`)

Each file defines one function that returns a static array. They are loaded on demand by `WC_Gateway_Redsys_Global_Lite`.

Read in full:

| Function | File | Returns |
|----------|------|---------|
| `redsys_return_status_paid()` | `includes/data/redsys-status-paid.php` | `string[]` — the statuses in which an order counts as unpaid: `pending`, `redsys-pbankt`, `cancelled`, `pending-deposit`. Despite its name, this is the unpaid list. |
| `redsys_return_types()` | `includes/data/redsys-types.php` | `string[]` — the gateway IDs treated as "Redsys orders": `redsys`, `bizumredsys`, `googlepayredirecredsys`. |

```php
require_once REDSYS_PLUGIN_DATA_PATH . 'redsys-types.php';
$is_redsys_type = in_array( $order->get_payment_method(), redsys_return_types(), true );
```

Listed only, not read for this document — the name and file are verified, the returned data is not described: `redsys_return_allowed_currencies()` (`allowed-currencies.php`), `redsys_return_currencies()` (`currencies.php`), `redsys_get_country_code()` (`countries.php`), `redsys_get_country_code_2()` (`countries-2.php`), `redsys_return_languages()` (`languages.php`), `redsys_return_all_languages_code()` (`wplanguages.php`), `redsys_return_dserrors()` (`dserrors.php`), `redsys_return_dsresponse()` (`dsresponse.php`), `redsys_return_insiteerrors()` (`insiteerrors.php`), `redsys_return_number_order_type()` (`number-order-type.php`), `redsyslite_plugins_to_deactivate()` (`deactivate-plugins.php`).
