<h1>Design Cart Ship2pay</h1>

<p><strong>Map WooCommerce payment methods to shipping methods. Pick a delivery on the left, switch payments on the right — checkout follows that map.</strong></p>

<h2>GitHub About / short description</h2>
<p>Paste into the repository <strong>About</strong> description field:</p>
<p>WooCommerce plugin that maps payment methods to each shipping method. Toggle payments per delivery; checkout shows only what you allowed. By Paweł Nosko / Design Cart.</p>

<hr>

<h2>Links</h2>
<ul>
  <li><strong>Download (Design Cart):</strong> <a href="https://www.designcart.pl/laboratorium/362-jak-w-woocommerce-uzaleznic-metody-platnosci-od-wybranej-metody-wysylki.html">https://www.designcart.pl/laboratorium/362-jak-w-woocommerce-uzaleznic-metody-platnosci-od-wybranej-metody-wysylki.html</a></li>
  <li><strong>GitHub:</strong> <a href="">[add repository URL]</a></li>
  <li><strong>Documentation / article:</strong> <a href="https://www.designcart.pl/laboratorium/362-jak-w-woocommerce-uzaleznic-metody-platnosci-od-wybranej-metody-wysylki.html">https://www.designcart.pl/laboratorium/362-jak-w-woocommerce-uzaleznic-metody-platnosci-od-wybranej-metody-wysylki.html</a></li>
  <li><strong>Author — Paweł Nosko:</strong> <a href="https://www.designcart.pl/pawel-nosko.html">https://www.designcart.pl/pawel-nosko.html</a></li>
  <li><strong>Studio — Design Cart:</strong> <a href="https://www.designcart.pl/">https://www.designcart.pl/</a></li>
</ul>

<hr>

<h2>What it is</h2>
<p>WooCommerce keeps shipping and payments as two separate lists. A customer can pick locker delivery and still see cash on delivery, or pick store pickup and still see a courier COD method the shop cannot fulfil.</p>
<p><strong>Design Cart Ship2pay</strong> is a small plugin for that one job. In admin you see shipping methods on the left and, for each of them, a list of payment methods on the right. You turn payments on or off. On checkout (classic and Blocks) the payment list is filtered from the chosen shipping method.</p>
<p>It does not add a gateway. It does not change shipping rates. It only decides which already-enabled WooCommerce payments may appear next to a given delivery.</p>
<p>Full documentation (Polish): <a href="https://www.designcart.pl/laboratorium/362-jak-w-woocommerce-uzaleznic-metody-platnosci-od-wybranej-metody-wysylki.html">Jak w WooCommerce uzależnić metody płatności od wybranej metody wysyłki?</a>.</p>
<p>Author: <a href="https://www.designcart.pl/pawel-nosko.html"><strong>Paweł Nosko</strong></a> · Company: <a href="https://www.designcart.pl/"><strong>Design Cart</strong></a> · License: GPL-2.0-or-later · Version: 1.0.0</p>

<h2>The problem it solves</h2>
<p>Shops usually know the rules by heart: no COD in a locker, cash only on store pickup, card and bank transfer for export. Those rules live in someone’s head until an order arrives that nobody can fulfil.</p>
<p>Ship2pay puts the same rules on one screen. Staff do not have to remember exceptions. Customers do not see a payment the shop cannot take with that delivery.</p>

<h2>Typical use</h2>
<ul>
  <li><strong>Store pickup</strong> — keep bank transfer, BLIK, or pay-in-store; hide courier COD.</li>
  <li><strong>Parcel locker / pickup point</strong> — hide cash on delivery if the carrier does not collect it there.</li>
  <li><strong>Two rates from one carrier</strong> — prepaid vs COD each get their own payment list.</li>
  <li><strong>International zones</strong> — leave card or PayPal; hide Polish-only methods.</li>
  <li><strong>Bulky goods</strong> — keep prepaid methods only on the oversized shipping rate.</li>
</ul>

<h2>Features</h2>
<ul>
  <li>One settings screen under <strong>WooCommerce → Design Cart Ship2pay</strong></li>
  <li>Shipping methods grouped by WooCommerce zones (including Rest of the world)</li>
  <li>On/off toggle for every enabled payment gateway, per shipping method</li>
  <li>Enable all / disable all for the shipping method you are editing</li>
  <li>Counter on each shipping row (e.g. <code>3/5</code> payments allowed)</li>
  <li>Master switch to turn checkout filtering off without deleting the map</li>
  <li>Works on classic checkout and WooCommerce Checkout Blocks</li>
  <li>HPOS compatibility declared</li>
  <li>Admin UI uses the Design Cart DC Interface (hero + form card)</li>
</ul>

<h2>Honest limits</h2>
<ul>
  <li>Only gateways enabled in WooCommerce appear in the map.</li>
  <li>A shipping method with no saved map still shows every payment — the shop does not go blank after activation.</li>
  <li>A newly added gateway stays available until you save the map again.</li>
  <li>Virtual carts that do not need shipping are not filtered.</li>
</ul>

<h2>Requirements</h2>
<ul>
  <li>WordPress 6.2+</li>
  <li>PHP 7.4+</li>
  <li>WooCommerce 8.0+ (must be active)</li>
  <li>Tested up to WordPress 7.1</li>
</ul>

<h2>Installation</h2>
<ol>
  <li>Download the ZIP from the Design Cart site or from this GitHub repository.</li>
  <li>WordPress → <strong>Plugins → Add Plugin → Upload Plugin</strong>.</li>
  <li>Upload the ZIP. The root folder must be <code>design-cart-ship2pay</code> with <code>design-cart-ship2pay.php</code> inside.</li>
  <li>Activate <strong>Design Cart Ship2pay</strong>. WooCommerce must already be active.</li>
  <li>Open <strong>WooCommerce → Design Cart Ship2pay</strong>, set the map, click <strong>Save</strong>.</li>
</ol>
<p>Manual / FTP: copy <code>design-cart-ship2pay/</code> into <code>/wp-content/plugins/</code>, then activate. A Settings link also appears on the Plugins list.</p>

<h2>Admin screen</h2>
<p>There is a single page. No extra tabs.</p>

<h3>Hero</h3>
<p>Title, breadcrumbs, and a <strong>Save</strong> button that submits the same form as the button at the bottom. Use it when the map is long.</p>

<h3>Enable payment filtering at checkout</h3>
<p>Master switch. On — checkout uses the saved map. Off — WooCommerce shows every enabled payment again, but the map stays in the database. Remember to click <strong>Save</strong>.</p>

<h3>Empty states</h3>
<ul>
  <li>No shipping methods — add them under <strong>WooCommerce → Settings → Shipping</strong>.</li>
  <li>No enabled payment methods — enable them under <strong>WooCommerce → Settings → Payments</strong>.</li>
</ul>

<h3>Left column — Shipping</h3>
<ul>
  <li>Methods grouped by zone name.</li>
  <li>Click a method to load its payment list on the right. Clicking does not save.</li>
  <li><strong>Disabled</strong> badge — the method is off in WooCommerce; you can still prepare its map.</li>
  <li>Counter <code>on/total</code> — how many payments are allowed for that method. Updates as you toggle.</li>
</ul>

<h3>Right column — Payments</h3>
<ul>
  <li>Lead text repeats the shipping title and zone so you know what you are editing.</li>
  <li><strong>Enable all</strong> / <strong>Disable all</strong> — only for the method currently selected.</li>
  <li>Each row: checkout title, gateway title, gateway id (e.g. <code>bacs</code>, <code>cod</code>).</li>
  <li>On = this payment may appear with that shipping method. Off = hidden on checkout for that delivery.</li>
</ul>

<h3>Save</h3>
<p>Writes the master switch and the full map. A notice confirms the save. Place a test order and change shipping — the payment list should match the map.</p>

<h2>How checkout filtering works</h2>
<p>The plugin reads the chosen shipping rate from the WooCommerce session (and, on the pay-for-order page, from the order). It then removes payment gateways that are switched off for that rate.</p>
<p>Classic checkout refreshes payments with WooCommerce <code>update_checkout</code>. Checkout Blocks use the official payment-method extension callbacks, so the list updates when the customer changes delivery — including two rates with the same price.</p>
<p>If several packages are in the cart, a payment must be allowed for every chosen rate.</p>

<h2>License</h2>
<p>GPL-2.0-or-later. See the plugin header in <code>design-cart-ship2pay.php</code>.</p>
