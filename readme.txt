=== Design Cart Ship2pay ===
Contributors: designcart, pawelnosko
Tags: woocommerce, shipping, payments, checkout, ship2pay
Requires at least: 6.2
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Map WooCommerce payment methods to shipping methods. Pick a shipping method on the left, enable or disable payments on the right.

== Description ==

Design Cart Ship2pay lets you decide which payment methods are available for each shipping method.

The admin screen is a simple two-column map:

* Left column — shipping methods grouped by WooCommerce shipping zones
* Right column — payment methods with on/off toggles for the selected shipping method

On the checkout page (classic and WooCommerce Blocks) the payment list is filtered automatically from the chosen shipping method.

= Features =

* Dedicated DC Interface settings page (hero + form card)
* Per-shipping-method payment toggles
* Enable or disable all payments for the selected shipping method
* Unconfigured shipping methods keep every payment available
* New payment gateways stay available until you save the map again
* Compatible with HPOS and WooCommerce Checkout Blocks

== Installation ==

1. Upload the `design-cart-ship2pay` folder to `/wp-content/plugins/`.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Ensure **WooCommerce** is installed and active.
4. Open **WooCommerce → Design Cart Ship2pay** and save the map.

== Frequently Asked Questions ==

= Does this plugin require WooCommerce? =

Yes. WooCommerce must be active.

= What happens if I do not configure a shipping method? =

All enabled payment methods stay available for that shipping method.

= Does it work with Checkout Blocks? =

Yes. Payments are filtered on the server and in the Blocks checkout UI when the customer changes shipping.

== Changelog ==

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
