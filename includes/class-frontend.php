<?php
/**
 * Design Cart Ship2pay
 *
 * Firma: Design Cart
 * Url firmy: https://www.designcart.pl
 * Autor: Paweł Nosko
 * Url autora: https://www.designcart.pl/pawel-nosko.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter available payment methods from the chosen shipping rate.
 */
class DC_Ship2pay_Frontend {

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_filter( 'woocommerce_available_payment_gateways', array( __CLASS__, 'filter_gateways' ), 100 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ), 30 );
	}

	/**
	 * Hide payment gateways that are off for the selected shipping method.
	 *
	 * @param array<string,WC_Payment_Gateway> $gateways Gateways.
	 * @return array<string,WC_Payment_Gateway>
	 */
	public static function filter_gateways( $gateways ) {
		if ( ! is_array( $gateways ) || empty( $gateways ) ) {
			return $gateways;
		}

		if ( is_admin() && ! wp_doing_ajax() ) {
			return $gateways;
		}

		if ( ! DC_Ship2pay_Settings::is_enabled() ) {
			return $gateways;
		}

		if ( function_exists( 'WC' ) && WC()->cart && ! WC()->cart->needs_shipping() ) {
			return $gateways;
		}

		$chosen = DC_Ship2pay_Settings::chosen_shipping_ids();
		if ( empty( $chosen ) ) {
			return $gateways;
		}

		$map = DC_Ship2pay_Settings::get_map();

		foreach ( $gateways as $id => $gateway ) {
			unset( $gateway );
			foreach ( $chosen as $rate_id ) {
				if ( ! DC_Ship2pay_Settings::is_payment_allowed( $map, $rate_id, (string) $id ) ) {
					unset( $gateways[ $id ] );
					break;
				}
			}
		}

		return $gateways;
	}

	/**
	 * Checkout Blocks: hide methods in the UI when shipping changes.
	 */
	public static function enqueue() {
		if ( is_admin() ) {
			return;
		}

		if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
			return;
		}

		$deps = array();
		if ( wp_script_is( 'wc-blocks-registry', 'registered' ) ) {
			$deps[] = 'wc-blocks-registry';
		}

		wp_enqueue_script(
			'dc-ship2pay-checkout',
			DC_SHIP2PAY_URL . 'assets/js/checkout.js',
			$deps,
			DC_SHIP2PAY_VERSION,
			true
		);

		wp_localize_script(
			'dc-ship2pay-checkout',
			'dcShip2pay',
			DC_Ship2pay_Settings::js_config()
		);
	}
}
