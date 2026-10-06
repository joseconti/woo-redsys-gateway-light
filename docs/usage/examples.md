# Examples

> Realistic, end-to-end uses of what the plugin exposes. The PHP examples go in a small plugin of your own or a must-use plugin, never in this plugin's files. They were written against the code as read on 2026-10-06; they have not been executed as part of writing this document, except where a test file that does the same thing is named.

## 1. Run a card payment's notification by hand, in a test

The integration tests build a correctly signed notification with the plugin's own signing class and post it to the gateway. This is the same technique, reduced to its core (`tests/Integration/GatewayRedsysIpnTest.php` is the full version):

```php
// Inside a WP_UnitTestCase, with WooCommerce and this plugin loaded.
$secret = base64_encode( str_repeat( 'A', 24 ) ); // A throwaway key for the test only.

update_option(
	'woocommerce_redsys_settings',
	array(
		'enabled'          => 'yes',
		'testmode'         => 'yes',
		'customtestsha256' => $secret,
	)
);

$json  = wp_json_encode(
	array(
		'Ds_Order'    => '0000000001',
		'Ds_Response' => '0000',
		'Ds_Amount'   => '100',
	)
);
$param = strtr( base64_encode( $json ), '+/', '-_' );

$api       = new RedsysLiteAPI();
$signature = $api->create_merchant_signature_notif( $secret, $param );

$_POST['Ds_SignatureVersion']   = 'HMAC_SHA256_V1';
$_POST['Ds_MerchantParameters'] = $param;
$_POST['Ds_Signature']          = $signature;

$gateway = new WC_Gateway_Redsys();
$this->assertTrue( $gateway->check_ipn_request_is_valid() );
```

Change one character of `$param` after signing and the same call returns `false`. Run the project's own versions with the commands in `docs/03-technical-plan.md` (Testing).

## 2. Tell the warehouse when a card payment is confirmed

The card gateway has no "payment complete" action of its own, so listen to the notification action after the gateway's own listener (priority 10) and check the order:

```php
add_action(
	'valid_redsys_standard_ipn_request',
	function ( $post ) {
		if ( empty( $post['Ds_MerchantParameters'] ) || ! function_exists( 'WCRedL' ) ) {
			return;
		}
		$api = new RedsysLiteAPI();
		$api->decode_merchant_parameters(
			RedsysLiteAPI::sanitize_merchant_parameters( $post['Ds_MerchantParameters'] )
		);
		$order_id = (int) WCRedL()->clean_order_number( $api->get_parameter( 'Ds_Order' ) );
		$order    = wc_get_order( $order_id );
		if ( $order && $order->is_paid() ) {
			do_action( 'my_shop_notify_warehouse', $order_id );
		}
	},
	20
);
```

Two limits to know:

- The gateway ends the request in some branches (amount mismatch, already-paid order, refund error). A listener at priority 20 does not run in those cases — which is what you want here.
- WooCommerce's own `woocommerce_payment_complete` action fires for all four gateways, because all of them call `payment_complete()`. Unless you need the Redsys data, it is the simpler hook:

```php
add_action(
	'woocommerce_payment_complete',
	function ( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( $order && in_array( $order->get_payment_method(), array( 'redsys', 'bizumredsys', 'googlepayredirecredsys', 'inespayredsys' ), true ) ) {
			do_action( 'my_shop_notify_warehouse', $order_id );
		}
	}
);
```

## 3. React to Google Pay and Inespay outcomes

```php
add_action(
	'googlepayredirecredsys_post_payment_complete',
	function ( $order_id ) {
		wc_get_logger()->info( "Google Pay paid: order {$order_id}", array( 'source' => 'my-shop' ) );
	}
);

add_action(
	'googlepayredirecredsys_post_payment_error',
	function ( $order_id, $error ) {
		wc_get_logger()->warning( "Google Pay denied: order {$order_id}: " . trim( $error ), array( 'source' => 'my-shop' ) );
	},
	10,
	2
);

add_action(
	'inespay_post_payment_complete',
	function ( $order_id ) {
		wc_get_logger()->info( "Inespay paid: order {$order_id}", array( 'source' => 'my-shop' ) );
	}
);
```

## 4. Use your own icons

One filter per gateway covers both the classic and the Blocks checkout:

```php
foreach ( array( 'redsys', 'bizumredsys', 'googlepayredirecredsys', 'inespayredsys' ) as $gateway_id ) {
	add_filter(
		"woocommerce_{$gateway_id}_icon",
		function () use ( $gateway_id ) {
			return content_url( "uploads/branding/{$gateway_id}.svg" );
		}
	);
}
```

For card, Bizum and Inespay the same result is available without code, through the **Logo** setting.

## 5. Show the Redsys payment details somewhere else

The details the admin order screen shows are plain order meta:

```php
function my_shop_redsys_details( $order_id ) {
	if ( ! function_exists( 'WCRedL' ) || ! WCRedL()->is_redsys_order( $order_id ) ) {
		return array();
	}
	return array(
		'redsys_order'  => WCRedL()->get_order_meta( $order_id, '_payment_order_number_redsys' ),
		'authorisation' => WCRedL()->get_order_meta( $order_id, '_authorisation_code_redsys' ),
		'date'          => WCRedL()->get_order_meta( $order_id, '_payment_date_redsys' ),
		'hour'          => WCRedL()->get_order_meta( $order_id, '_payment_hour_redsys' ),
	);
}
```

`is_redsys_order()` is true for card, Bizum and Google Pay orders. For Inespay read `_inespay_single_payin_id` and `_inespay_status` from the order directly. Never expose `_redsys_secretsha256`.

## 6. Let a later notification rescue a failed order

By default only `pending`, `redsys-pbankt`, `cancelled` and `pending-deposit` orders count as unpaid. An order in any other status ignores further payment notifications in the card and Bizum gateways. To let a successful notification complete an order that some other plugin marked `failed`:

```php
add_filter(
	'redsys_status_pending',
	function ( $status ) {
		$status[] = 'failed';
		return $status;
	}
);
```

## 7. Check all four callbacks refuse a forged request

A script for a deployment checklist. It changes nothing on the site:

```bash
SITE="https://<your-site>"

for gw in WC_Gateway_redsys WC_Gateway_bizumredsys WC_Gateway_googlepayredirecredsys; do
  curl -s -o /dev/null -w "$gw -> HTTP %{http_code}\n" -X POST "$SITE/?wc-api=$gw" \
    --data-urlencode "Ds_SignatureVersion=HMAC_SHA256_V1" \
    --data-urlencode "Ds_MerchantParameters=<any-base64-text>" \
    --data-urlencode "Ds_Signature=<not-a-valid-signature>"
done

curl -s -o /dev/null -w "inespay -> HTTP %{http_code}\n" -X POST "$SITE/?wc-api=wc_gateway_inespayredsys" \
  -H "Content-Type: application/json" \
  -d '{"dataReturn":"<any-base64-text>","signatureDataReturn":"<not-a-valid-signature>"}'
```

Inespay must answer 401. The three Redsys-protocol callbacks must answer with an error status and their "do not access this page directly" text — never 200. The exact error status comes from WordPress's `wp_die()` default and is not fixed by this plugin.

## 8. Run the whole local suite

```bash
npx wp-env start
npx wp-env run cli bash -c "cd wp-content/plugins/woo-redsys-gateway-light && vendor/bin/phpunit"
npx wp-env run tests-cli bash -c "cd wp-content/plugins/woo-redsys-gateway-light && vendor/bin/phpunit -c phpunit-integration.xml.dist"
npx playwright test
```

These are the commands recorded in `docs/05-test-points.md`. First-time setup (Composer install inside the container, playground state for the end-to-end tests) is in `docs/playground.md` and `docs/03-technical-plan.md`.

## Related
- `docs/reference/hooks-and-extension-points.md` — every hook, with its parameters.
- `docs/reference/classes.md`, `docs/reference/functions.md` — the helpers used above.
