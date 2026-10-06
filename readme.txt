=== Payment Gateway for Redsys & WooCommerce Lite ===
Contributors: j.conti
Tags: woocommerce, redsys, bizum, google pay, apple pay, inespay
Requires at least: 4.0
Tested up to: 7.0
Requires PHP: 7.0
Donate link: https://plugins.joseconti.com/product-category/plugins/donaciones/
Stable tag: 7.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
WC requires at least: 7.4
WC tested up to: 10.9

Add Redsys Gateway, BIZUM, and Apple/Google Pay redirection to WooCommerce. Lite version of the premium Redsys plugin on WooCommerce.com.

== Description ==

= Light version Features =

This is the Light version of the Redsys for WooCommerce Premium at WooCommerce.com.

You can find the PRO version at [plugins.joseconti.com](https://plugins.joseconti.com/product/plugin-woocommerce-redsys-gateway/)

With this extension, you get all you need to use Redsys Gateway.

* Always compatible with the latest WooCommerce version.
* PSD2 Compatible
* Redsys Redirection Lite
* Bizum Lite
* Apple & Google Pay redirection
* Inespay Lite
* WPML compatible.
* Works with SNI certificates like Let's Encrypt, EX: SiteGround, HostFusion, etc
* Gateway language selection from settings.
* Checkout logo customization.
* Added checkout logo customization.

Why is it not compatible with versions of WooCommerce lower than 2.9? Because they have vulnerabilities, and I will not support versions that you should not use.

= Premium version Features =

Check [Redsys for WooCommerce Premium version](https://plugins.joseconti.com/product/plugin-woocommerce-redsys-gateway/)

* Credit card Form in the Checkout as Stripe (InSite)
* Redsys redirection with Modal (Customer doesn't leave the site.)
* Bizum
* Bizum InSite (Bizum without leaving the site)
* MasterPass
* Google Pay
* Apple Pay
* Preauthorizations, global or product by product.
* Approve preauthorizations from WooCommerce order
* Bulk approve Pre-authorizations from Orders List
* Subscriptions (Woo Subscriptions, Yith Subscriptions premium y SUMO Subscriptions).
* Bank transfers
* Direct debits
* Pay in a Modal without leaving the site.
* Always compatible with WooCommerce & Continuous audits by WooCommerce Team.
* WPML compatible.
* Works with SNI certificates like Let's Encrypt, EX: SiteGround, HostFusion, etc
* Gateway language selection from settings.
* Checkout logo customization.
* Tokenization
* Pay with 1 click
* Pay with 1 click without leaving the website 
* Pay with 1 click from product page. 
* Bulk Charge orders from the Orders List (With Tokens).
* Direct Debit
* Private Products
* Infinite Terminals number.
* Sequential Invoice Number, essential in Spain by the Public Treasury.
* Refund from Order.
* Error action selection, what do you want that happen when a user makes an error on the Gateway?
* Export Order to CSV, export all date orders between two dates to CSV.
* Pay with 1 click.
* Virtually Unlimited Terminals, FUC's, etc. Special developed Filter for it.
* emails to admin and customers when there is an error paying at Redsys.
* Check at Thank you page. If a customer arrives on to Thank you page and the order has not been marked as paid, an email is sent to the administrator.
* Widget to easily add the Credit Card image required by redsys
* And more to come.

== Installation ==

 * Unzip the files and upload the folder into your plugins folder (wp-content/plugins/) overwriting old versions if they exist
 * Activate the plugin in your WordPress admin area.
 * Open the settings page for WooCommerce and click the "Payment Gateways" tab
 * Click on the sub tab for "Redsys/Servired"
 * Configure your Redsys settings.


== Frequently Asked Questions ==

== Screenshots ==

1. Welcome screen: Latest updates & premium version.
2. Redsys: Redsys settings screenshot.
3. Iupay: Iupay settings screenshot.
4. Language: Set the Redsys Gateway Language.

== Changelog ==

== Unreleased ==

* Fix: A notification to the Bizum gateway that arrived without its signature-version field ended in a PHP error (an HTTP 500 on PHP 8). It was already rejected; it is now rejected without the error.
* Fix: On WooCommerce 9.2 or newer with the legacy order storage, recovering an order from its Redsys order number (refund notifications, late notifications) wrote a "not supported on the current order datastore" notice to the debug log. The lookup no longer uses that query there.
* Fix: On PHP 8.1 or newer, a notification without a date or an hour wrote two deprecation notices to the debug log (Redsys, Bizum and Google Pay gateways).
* Tweak: Several text colours of the About page did not reach the 4.5:1 contrast of WCAG 2.2 AA; they are darker now.
* Fix: With WooCommerce removed or deactivated while this plugin was still active, every front-end page ended in a fatal error. The plugin now does nothing in that case.
* Fix: The transaction limit of the Bizum and Inespay gateways now also applies when an order is paid from the order-pay page or through the Blocks checkout, and an order above the limit is refused when the payment is started.
* Fix: A refund through the Redsys, Bizum or Google Pay gateways could be reported as done when Redsys had not confirmed it, if a confirmation of an earlier refund of the same order had been left behind. Each refund now waits for its own confirmation.
* Dev: New filters woocommerce_redsys_refund_confirmation_attempts, woocommerce_bizumredsys_refund_confirmation_attempts and woocommerce_googlepayredirecredsys_refund_confirmation_attempts, to change how long a refund waits for Redsys's confirmation.
* Security Fix: The plugin's two admin notices are now shown to, and can be dismissed by, users who manage WooCommerce only. Any user able to open an admin screen could dismiss them for everyone.
* Fix: The one-time redirect to the About page after an update now happens only for an administrator on an admin screen. It could be triggered by any request to an admin entry point, including from a visitor who was not logged in, and it could interrupt that request.
* Security Fix: A return to the order-received page is now checked (gateway, order status and Redsys signature) before the plugin waits for the payment notification. A request without a valid signature no longer holds the page for five seconds.
* Security Fix: The Logo setting of the Redsys, Bizum and Inespay gateways is now saved as a URL and escaped where the checkout prints it. A value that was not a plain URL could add markup to the checkout page; only a user allowed to change the gateway settings could enter one.
* Security Fix: A payment notification that names no order is now always rejected by the Redsys, Bizum and Google Pay gateways. Such a notification could not reach any order, but it passed the signature check.
* Tweak: The plugin's stylesheets and its Blocks checkout script are now loaded minified. The readable files are still included and are loaded when SCRIPT_DEBUG is on.
* Fix: The cancel URL sent to Redsys for the card, Bizum and Google Pay gateways contained HTML-escaped separators, so a customer returning from a cancelled or refused payment did not have the cancellation processed. It is now sent as a plain URL.
* Fix: A customer returning from a payment that Redsys had already cancelled was shown "Your order can no longer be cancelled"; the return is now treated as the cancellation it is.
* Fix: A refund explicitly requested for 0 through the Redsys, Bizum or Google Pay gateways is now refused instead of being sent to Redsys as a refund of the full order total.
* Fix: Debug logs of the Bizum, Google Pay and Redsys gateways no longer contain the signing secret or the locally computed signature, including the Google Pay log line written when the payment form is built.
* Fix: With debug logging on, a refund request that could not reach Redsys caused a fatal error; it now reports the error message.
* Fix: A repeated payment notification for a Google Pay order that is already paid is now ignored, as in the other gateways.
* Fix: Returning to the order-received page with a still unpaid Google Pay order stopped the page with an error; the payment is now verified and completed there.
* Fix: Google Pay was hidden from every customer while test mode was on; it is now offered in test mode like the other gateways.
* Fix: Bizum's transaction-limit check no longer drops the decimals of the limit and of the cart total, and a cart total equal to the limit is now allowed.
* Security Fix: The Google Pay redirection gateway's notification check now fails closed when no SHA-256 secret is configured, matching the Redsys and Bizum gateways (it previously accepted the notification in that case).
* Fix: The final signature check on the order-received page (successful_request()) was verifying test-mode payments against the wrong secret for Bizum and Google Pay, so a genuinely valid test-mode payment could be left unmarked as paid with no error shown.
* Fix: Removed an unauthenticated, repeatable 5-second delay on the order-received page; it is now rate-limited per order.
* Fix: Bizum and Google Pay no longer risk a fatal error when a payment notification references an order that does not exist.
* Fix: Corrected the internal order-number mapping used to match delayed or retried notifications to their order — previously, roughly 1 in 5 such notifications could resolve to the wrong order.
* Fix: Signature comparisons now use a constant-time comparison across all gateways.
* Fix: An Inespay refund-confirmation notification could be mislabeled as a completed payment; it is now correctly ignored once the order is already paid.
* Fix: Inespay's transaction-limit check no longer truncates decimals in the cart total, so a configured limit (e.g. 200) is now correctly enforced against totals like 200.50.
* Fix: An Inespay refund explicitly requested for 0 is no longer silently upgraded to a full refund of the order.
* Fix: Replaced a deprecated WooCommerce function (wc_enqueue_js()) with wp_add_inline_script() on the order-received page (props to the reporter of issue #93 on WordPress.org).
* Fix: Payment and refund notifications for orders with 10+ digit IDs (common on long-running stores using WooCommerce's order tables) could resolve to the wrong order, or fail with a fatal error, once the short-lived internal order-number mapping expired. Notifications now also fall back to a permanent record of the order number, and refunds refresh that record with a longer validity window at the time the refund is requested.

== 7.0.2 ==

* Security Fix: Added Inespay notification signature (signatureDataReturn / HMAC-SHA256) and amount verification in the Inespay callback to prevent unauthenticated payment forgery. The 7.0.1 signature hardening did not cover the Inespay gateway. Thanks to Shivamani Vastrala for the responsible disclosure.
* Fix: Restored the "+" characters in the received Ds_MerchantParameters before validating the notification. On some servers/proxies the "+" of the standard Base64 arrives as a space and sanitize_text_field() collapsed it, breaking the JSON decoding and the signature check (signature did not match), so valid payments were rejected. Applied to the Redsys, Bizum and Google Pay gateways.
* Fix: The merchant SHA-256 secret key is now trimmed before use, so accidental leading/trailing spaces no longer cause signature mismatches.

== 7.0.1 ==

* Security Fix: Added cryptographic signature (Ds_Signature) verification in successful_request() for Redsys, Bizum, and Google Pay gateways to prevent payment forgery via the Order Received page.
* Fix: Test SHA-256 secret was being overwritten with the production secret in successful_request(), causing signature validation failures in test mode.

== 7.0.0 ==

* NEW: Plugin renamed to "Payment Gateway for Redsys & WooCommerce".
* NEW: Added Inespay payment gateway.
* Several code improvements and fixes.

== 6.5.0 ==

* Added translation files to /languages plugin folder.

== 6.4.0 ==

* Updated hook for mark payments as made bu URL Params.
* Fixed Redsys Data translation in Thankyou Page.

== 6.3.1 ==

* Fixed fatal error in Bizum notification.
* Fixed PHP Warning:  Undefined variable $redsys.
* Fixed PHP Warning:  Attempt to read property "debug" on null in woocommerce-redsys.php on line 317

== 6.3.0 ==

* Fixed Google Pay logo checkout.
* Now orders are marked as paid if Redsys adds parameters to the URL.

== 6.2.2 ==

* Updated escaping in Bizum from esc_html_ to kses.
* Removed Merchant Module.

== 6.2.1 ==

* Fixed Deprecated $logo.
* Added elseif.
* Added wp_kses.
* removed extra items in array().
* removed some strings (iupay).
* Removed duplicated items.
* Moved Class inicialization.
* Removed iUpay Strings.

== 6.2.0 ==

* Fixed a double “Code” tag in Redsys redirection description. https://github.com/joseconti/woo-redsys-gateway-light/issues/22
* Fixed many Deprecated.

== 6.1.3 ==

* FIX: Refunds in Google Pay redirection.

== 6.1.2 ==

* FIX: Enhanced update_order_meta() for accept array() values.

== 6.1.1 ==

* FIX: API function ERROR

== 6.1.0 ==

* NEW: Removed support for PHP < 7.
* UPDATE: Compatibility with PHP 8.2 & 8.3
* UPDATE: Code is Poetry
* UPDATE: Some Links
* FIX: Fixed an issue where Redsys notification to the site would fail if the terminal was in test mode and the real SHA256 was not entered in Bizum and Google Pay redirection.

== 6.0.0 ==

* NEW: Added Google Pay redirection.
* UPDATE: Many links updated.

== 5.3.0 ==

* FIXED: Fixed Bizum redirection with some themes.
* FIXED: Fixed One Billion Order Bug.
* UPDATE: Merged some security Pulls from Github React.
* UPDATE: Declared compatibility with WooCommerce 8.3 & WordPress 6.4.

== 5.2.2 ==

* FIXED: Fixed refunds with Redsys redirection.

== 5.2.1 ==

* FIXED: Fixed a problem with Italy. This update is mandatory!

== 5.2.0 ==

* Improved: Country code EMV3DS
* Improved: Phone country code.

== 5.1.0 ==

* NEW: Bizum Checkout logo customization.
* NEW: Added option What to do after payment to Bizum.
* Improved: Smaller default Bizum logo.
* FIXED: A fatal error when Bizum Payment is not paid.
* FIXED: Fixed Bizum update status.

== 5.0.1 ==
* FIXED: Fixed an error cleaning Order number.

== 5.0.0 ==
* NEW: HPOS compatibility.
* NEW: Declared WordPress 6.1 compatibility.
* NEW: Declared WooCommerce 7.1 compatibility.
* Fixed: The default Redsys logo at Checkout is shown again.
* Fixed: Fixed a problem with Bizum. Under some circumstances, orders were not marked as paid.

== 4.0.0 ==
* NEW: WooCommerce Checkout Block Compatibility.
* NEW: Refactoring for add code order.
* NEW: Added LWV SCA.

== 3.0.6 ==
* FIXED: fixed a bug introduced in v3.0.5. Now refunds are marked again as refunds.

== 3.0.5 ==
* NEW: Now check if the Order is paid before taking action. Related problem > https://wordpress.org/support/topic/pedido-cancelado-por-redsys-despues-del-pago/#post-15280747

== 3.0.4 ==
* NEW: Now you can set a limit cart amount for use Bizum.
* NEW: Now the customer name is sent to Redsys.
* Fixed Thank you page error when directly acceded without associated order ID.


== 3.0.3 ==
* Fixed missing translation string (The Redsys Authorization number is:)
* Fixed Bizum field duplication. Some servers make fatal errors.
* Declared compatibility with WordPress 5.7
* Declared compatibility with WooCommerce 5.3

== 3.0.2 ==
* Fixed an issue where the Redsys authorization number message was displayed on the thank you page even if the order was not with Redsys.
* Fixed all Bizum text-domain that I had inherited from the premium plugin.
* Fixed: Now, the checkout warning about test mode is not shown if the gateway is disabled in WooCommerce.
* Declared compatibility with WooCommerce 4.9

== 3.0.1 ==
* Fixed a problem with PHP 8.0

== 3.0.0 ==
* New: Added PSD2 Compatibility
* New: Added Bizum
* Declared compatibility with WooCommerce 4.7
* Declared compatibility with WordPress 5.6

== 2.1.0 ==
* New: Added a notice in the checkout when Redsys for WooCommerce is in Test Mode.
* Fixed PHP Notices when you visit the callback URL.
* Fixed the Admin Notice URL.
* Declared compatibility with WooCommerce 4.2

== 2.0.1 ==
* Declared compatibility with WooCommerce 4.1

== 2.0.0 ==
* New: Added refunds.

== 1.5.0 ==
* New: Added new Redsys Languages.
* Removed export tab
* Declared compatibility with WooCommerce 4.0

== 1.4.1 ==
* Fixed a bug with the SHA256 Test field

== 1.4.0 ==
* NEW: Added a new settings field for SHA256 Test mode.

== 1.3.10 ==
* Now, when an Order is canceled on Redsys side, it is canceled in WooCommerce.
* Removed PSD2 / SCA notice.
* Added Telegram Redsys Channel notice.

== 1.3.9 ==
* Fixed ARS (Peso argentino) currency
* Added a notice linking to PSD2 / SCA. Post.
* Fixed translation domain on some strings

== 1.3.8 ==
* Added MXN currency
* Fixed a problem with amounts less than 1.
* Declared compatibility with WooCommerce 3.7

== 1.3.7 ==
* Added +230 new currencies supported by Redsys.
* Added an admin notice to link to a post explaining new features.

== 1.3.6 ==
* Improved WooCommerce Order processing when "Mark as completed" is selected.
* Improved some string translations.

== 1.3.5 ==
* Now, if an Order is canceled by Redsys, it is canceled at WooCommerce

== 1.3.4 ==
* Fixed dismissible admin_notice. Now you can dismiss it forever.

== 1.3.3 ==
* Removed admin notice about SHA256.
* Added notice about Forums help.

== 1.3.2 ==
* Fixed URLs on the About page.
* Fixed encoded Date & hour at order edit page.

== 1.3.1.1 ==
* Missing CSS file.

== 1.3.1 ==
* Added useful links on Redsys Settings.

== 1.3.0 ==
* Now you can select if the order has to be marked as complete or as Processing (WooCommerce Default).

== 1.2.2 ==
* Fixed a translation error.

== 1.2.1 ==
* Fixed a problem in some server settings that the plugin crashed at activation.

== 1.2.0 ==
* Removed iupay and added payment options in Redsys setting page. Now you can select if you want Iupay or not from settings.
* Fix: Fixed a bug with amounts less than 1€.

== 1.1.1 ==
* Removed message about Mcrypt when PHP is 7.0 or above

== 1.1.0 ==
* Added Redsys API for PHP 5.x and 7.x
* Added ability to customize checkout logo.

= 1.0.1 =
* NEW: Added logo customization
* Updated spinner. This update improves gateway redirection.

= 1.0.0 =
* First public release.


== Upgrade Notice ==

= 7.0.2 =
Security release. Adds signature + amount verification to the Inespay callback (update immediately if you use Inespay) and fixes signature validation failures (Ds_MerchantParameters "+" arriving as spaces, and spaces in the SHA-256 key) that could reject valid payments on Redsys, Bizum and Google Pay.
