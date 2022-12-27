=== WooCommerce Redsys Gateway Light ===
Contributors: j.conti
Tags: woocommerce, redsys, pasarela redsys, bizum, gateway, redsys gateway, redsys pasarela, redsys woocommerce, woocommerce redsys, iupay, Iupay gateway, Iupay woocommerce, woocommerce iupay, iupay pasarela, pasarela iupay
Requires at least: 4.0
Tested up to: 6.1
Donate link: https://www.joseconti.com/cursos-online/micropatrocinio/
Stable tag: 5.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
WC requires at least: 4.0
WC tested up to: 7.3

Add Redsys Gateway and BIZUM to WooCommerce. This is the Lite version of the official WooCommerce Redsys plugin at WooCommerce.com.

== Description ==

= Light version Features =

This is the Light version of the official WooCommerce Redsys plugin at WooCommerce.com.

You can find the PRO version at [WooCommerce.com](https://woocommerce.com/products/redsys-gateway/)

With this extension, you get all you need to use Redsys Gateway.

* Always compatible with the latest WooCommerce version & continuous audits by WooCommerce Team.
* PSD2 Compatible
* Redsys Redirection Lite
* Bizum Lite
* WPML compatible.
* Works with SNI certificates like Let's Encrypt, EX: SiteGround, HostFusion, etc
* Gateway language selection from settings.
* Checkout logo customization.
* Added checkout logo customization.

As this is WooCommerce official extension, always will be compatible with the latest WooCommerce version.

Why is it not compatible with versions of WooCommerce lower than 2.9? Because they have vulnerabilities, and I will not support versions that you should not use.

= Premium version Features =

* Credit card Form in the Checkout as Stripe
* Add Bizum
* Add MasterPass
* Add InSite
* Add Preauthorizations
* Add Bank transfers
* Add Direct debits
* Browser iFrame
* Always compatible with WooCommerce & Continuous audits by WooCommerce Team.
* Compatible with WooCommerce Subscriptions
* WPML compatible.
* Works with SNI certificates like Let's Encrypt, EX: SiteGround, HostFusion, etc
* Gateway language selection from settings.
* Checkout logo customization.
* Tokenization
* Pay with 1 click
* Pay with 1 click without leaving the website 
* Preauthorizations
* Approve preauthorizations from WooCommerce order
* Bulk approve Pre-authorizations from Orders List
* Bulk Charge orders from the Orders List (With Tokens).
* Direct Debit
* Private Products
* Second Terminal number. Very useful for security purposes.
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
* New: Added a notice in the checkout when WooCommerce Redsys Gateway is in Test Mode.
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
