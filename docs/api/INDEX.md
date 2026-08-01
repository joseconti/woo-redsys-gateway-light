# API Index — Payment Gateway for Redsys & WooCommerce Lite

> One line per public surface. Grep here FIRST; open the full doc only on a hit.
> Full per-surface docs are backfilled progressively (adoption rule): each surface gets its complete doc the first time a slice touches it.

| Surface | Kind | Code file | Doc | Purpose (one line) |
|---------|------|-----------|-----|--------------------|
| `valid_redsys_standard_ipn_request` | action | classes/class-wc-gateway-redsys.php | — (progressive) | Fires after a Redsys card-gateway notification passes signature verification |
| `valid_bizumredsys_standard_ipn_request` | action | classes/class-wc-gateway-bizum-redsys.php | — (progressive) | Fires after a Bizum notification passes signature verification |
| `valid_googlepayredirecredsys_standard_ipn_request` | action | classes/class-wc-gateway-googlepay-redirection-redsys.php | — (progressive) | Fires after an Apple/Google Pay redirection notification passes signature verification |
| `googlepayredirecredsys_post_payment_complete` | action | classes/class-wc-gateway-googlepay-redirection-redsys.php | — (progressive) | Fires after a Google/Apple Pay redirection payment completes |
| `googlepayredirecredsys_post_payment_error` | action | classes/class-wc-gateway-googlepay-redirection-redsys.php | — (progressive) | Fires when a Google/Apple Pay redirection payment errors |
| `inespay_post_payment_complete` | action | classes/class-wc-gateway-inespay-redsys.php | — (progressive) | Fires after an Inespay payment completes |
| `woocommerce_redsys_args` | filter | classes/class-wc-gateway-redsys.php | — (progressive) | Filters the outgoing signed request args sent to Redsys (card gateway) |
| `woocommerce_redsys_args` (reused — the Bizum gateway hardcodes the card gateway's filter name here instead of its own `woocommerce_bizumredsys_args`, unlike its `_icon` filter which does follow the `woocommerce_<gateway_id>_args` pattern) | filter | classes/class-wc-gateway-bizum-redsys.php | — (progressive) | Filters the outgoing signed request args sent to Redsys (Bizum); shares the card gateway's filter name rather than a Bizum-specific one |
| `woocommerce_googlepayredirecredsys_args` | filter | classes/class-wc-gateway-googlepay-redirection-redsys.php | — (progressive) | Filters the outgoing signed request args (Apple/Google Pay redirection) |
| `woocommerce_redsys_icon` | filter | classes/class-wc-gateway-redsys.php + includes/blocks/ | — (progressive) | Filters the checkout icon shown for the Redsys card gateway |
| `woocommerce_bizumredsys_icon` | filter | classes/class-wc-gateway-bizum-redsys.php + includes/blocks/ | — (progressive) | Filters the checkout icon shown for Bizum |
| `woocommerce_googlepayredirecredsys_icon` | filter | classes/class-wc-gateway-googlepay-redirection-redsys.php + includes/blocks/ | — (progressive) | Filters the checkout icon shown for Apple/Google Pay redirection |
| `woocommerce_inespayredsys_icon` | filter | classes/class-wc-gateway-inespay-redsys.php + includes/blocks/ | — (progressive) | Filters the checkout icon shown for Inespay |
| `redsys_status_pending` | filter | classes/class-wc-gateway-redsys-global-lite.php | — (progressive) | Filters the WooCommerce order status used for a "pending" Redsys result |
| `redsys_lite_apps_plugins_mac_app` / `_free` / `_premium` / `_webs` / `_skills` / `_profiles` | filter | includes/class-redsys-lite-apps-plugins.php | — (progressive) | Filters the admin cross-sell widget's data (internal/marketing, not payment-relevant) |
