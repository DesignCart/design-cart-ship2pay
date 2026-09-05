<?php
/**
 * Design Cart Ship2pay
 *
 * Firma: Design Cart
 * Url firmy: https://www.designcart.pl
 * Autor: Paweł Nosko
 * Url autora: https://www.designcart.pl/pawel-nosko.html
 *
 * Plugin Name: Design Cart Ship2pay
 * Plugin URI: https://www.designcart.pl
 * Description: Proste mapowanie metod płatności na metody wysyłki WooCommerce. Wybierz wysyłkę po lewej, włącz lub wyłącz płatności po prawej — na kasie lista płatności filtruje się automatycznie.
 * Version: 1.0.0
 * Author: Paweł Nosko
 * Author URI: https://www.designcart.pl/pawel-nosko.html
 * Text Domain: design-cart-ship2pay
 * Domain Path: /languages
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 * WC requires at least: 8.0
 * WC tested up to: 11.0
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DC_SHIP2PAY_VERSION', '1.0.0' );
define( 'DC_SHIP2PAY_FILE', __FILE__ );
define( 'DC_SHIP2PAY_PATH', plugin_dir_path( __FILE__ ) );
define( 'DC_SHIP2PAY_URL', plugin_dir_url( __FILE__ ) );
define( 'DC_SHIP2PAY_BASENAME', plugin_basename( __FILE__ ) );
define( 'DC_SHIP2PAY_OPTION', 'dc_ship2pay_settings' );

/**
 * Check whether WooCommerce is active.
 *
 * @return bool
 */
function dc_ship2pay_is_woocommerce_active() {
	$dc_ship2pay_plugins = (array) get_option( 'active_plugins', array() );

	if ( is_multisite() ) {
		$dc_ship2pay_plugins = array_merge(
			$dc_ship2pay_plugins,
			array_keys( (array) get_site_option( 'active_sitewide_plugins', array() ) )
		);
	}

	foreach ( $dc_ship2pay_plugins as $dc_ship2pay_plugin ) {
		if ( 'woocommerce.php' === basename( (string) $dc_ship2pay_plugin ) ) {
			return true;
		}
	}

	return class_exists( 'WooCommerce' );
}

add_action(
	'before_woocommerce_init',
	static function () {
		if ( ! class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			return;
		}

		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', DC_SHIP2PAY_FILE, true );
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', DC_SHIP2PAY_FILE, true );
	}
);

add_action(
	'plugins_loaded',
	static function () {
		if ( ! dc_ship2pay_is_woocommerce_active() ) {
			add_action(
				'admin_notices',
				static function () {
					echo '<div class="notice notice-error"><p>';
					echo esc_html__( 'Design Cart Ship2pay wymaga aktywnego WooCommerce.', 'design-cart-ship2pay' );
					echo '</p></div>';
				}
			);
			return;
		}

		require_once DC_SHIP2PAY_PATH . 'includes/class-settings.php';
		require_once DC_SHIP2PAY_PATH . 'includes/class-admin.php';
		require_once DC_SHIP2PAY_PATH . 'includes/class-frontend.php';
		require_once DC_SHIP2PAY_PATH . 'includes/class-plugin.php';

		DC_Ship2pay_Plugin::instance()->init();
	}
);

register_activation_hook(
	__FILE__,
	static function () {
		if ( ! dc_ship2pay_is_woocommerce_active() ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( esc_html__( 'Design Cart Ship2pay wymaga aktywnego WooCommerce.', 'design-cart-ship2pay' ) );
		}
	}
);
