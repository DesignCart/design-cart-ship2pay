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
 * Stored map: shipping rate ID → payment ID → bool.
 */
class DC_Ship2pay_Settings {

	/**
	 * Default settings.
	 *
	 * @return array<string,mixed>
	 */
	public static function defaults() {
		return array(
			'enabled' => 'yes',
			'map'     => array(),
		);
	}

	/**
	 * @return array<string,mixed>
	 */
	public static function all() {
		$stored = get_option( DC_SHIP2PAY_OPTION, array() );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		$settings = wp_parse_args( $stored, self::defaults() );
		if ( ! isset( $settings['map'] ) || ! is_array( $settings['map'] ) ) {
			$settings['map'] = array();
		}

		return $settings;
	}

	/**
	 * @return bool
	 */
	public static function is_enabled() {
		$settings = self::all();
		return 'yes' === $settings['enabled'];
	}

	/**
	 * @return array<string,array<string,bool>>
	 */
	public static function get_map() {
		$settings = self::all();
		$map      = array();

		foreach ( $settings['map'] as $rate_id => $payments ) {
			$rate_id = (string) $rate_id;
			if ( '' === $rate_id || ! is_array( $payments ) ) {
				continue;
			}
			$map[ $rate_id ] = array();
			foreach ( $payments as $gateway_id => $allowed ) {
				$map[ $rate_id ][ (string) $gateway_id ] = self::is_truthy( $allowed );
			}
		}

		return $map;
	}

	/**
	 * @param mixed $value Value.
	 * @return bool
	 */
	public static function is_truthy( $value ) {
		return in_array( $value, array( true, 1, '1', 'yes', 'on' ), true );
	}

	/**
	 * Persist settings.
	 *
	 * @param array<string,mixed> $data Data.
	 */
	public static function save( $data ) {
		$current = self::all();
		$enabled = ( isset( $data['enabled'] ) && self::is_truthy( $data['enabled'] ) ) ? 'yes' : 'no';
		$map     = array();

		$posted_map = isset( $data['map'] ) && is_array( $data['map'] ) ? $data['map'] : array();
		$gateways   = array_keys( self::get_payment_gateways( false ) );
		$rate_ids   = self::get_all_rate_ids();

		foreach ( $rate_ids as $rate_id ) {
			$row = isset( $posted_map[ $rate_id ] ) && is_array( $posted_map[ $rate_id ] ) ? $posted_map[ $rate_id ] : array();
			$map[ $rate_id ] = array();
			foreach ( $gateways as $gateway_id ) {
				$map[ $rate_id ][ $gateway_id ] = isset( $row[ $gateway_id ] ) && self::is_truthy( $row[ $gateway_id ] );
			}
		}

		$current['enabled'] = $enabled;
		$current['map']     = $map;

		update_option( DC_SHIP2PAY_OPTION, $current, false );
	}

	/**
	 * Whether a payment is allowed for a shipping rate.
	 *
	 * Unconfigured shipping or a new payment method stays allowed.
	 *
	 * @param array<string,array<string,bool>> $map        Map.
	 * @param string                           $rate_id    Shipping rate ID.
	 * @param string                           $gateway_id Payment ID.
	 * @return bool
	 */
	public static function is_payment_allowed( $map, $rate_id, $gateway_id ) {
		$rate_id    = (string) $rate_id;
		$gateway_id = (string) $gateway_id;

		if ( '' === $rate_id || '' === $gateway_id ) {
			return true;
		}

		if ( ! isset( $map[ $rate_id ] ) || ! is_array( $map[ $rate_id ] ) ) {
			return true;
		}

		if ( ! array_key_exists( $gateway_id, $map[ $rate_id ] ) ) {
			return true;
		}

		return (bool) $map[ $rate_id ][ $gateway_id ];
	}

	/**
	 * Chosen shipping rate IDs from session / checkout POST.
	 *
	 * @return array<int,string>
	 */
	public static function chosen_shipping_ids() {
		$methods = array();

		if ( function_exists( 'WC' ) && WC()->session ) {
			$methods = (array) WC()->session->get( 'chosen_shipping_methods', array() );
		}

		$dc_ship2pay_nonce_ok = false;

		if ( isset( $_POST['security'] ) ) {
			$dc_ship2pay_nonce_ok = (bool) wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'update-order-review' );
		}

		if ( ! $dc_ship2pay_nonce_ok && isset( $_POST['woocommerce-process-checkout-nonce'] ) ) {
			$dc_ship2pay_nonce_ok = (bool) wp_verify_nonce(
				sanitize_text_field( wp_unslash( $_POST['woocommerce-process-checkout-nonce'] ) ),
				'woocommerce-process_checkout'
			);
		}

		if ( empty( $methods ) && $dc_ship2pay_nonce_ok && isset( $_POST['shipping_method'] ) ) {
			$dc_ship2pay_posted = map_deep( wp_unslash( $_POST['shipping_method'] ), 'sanitize_text_field' );
			if ( ! empty( $dc_ship2pay_posted ) ) {
				$methods = (array) $dc_ship2pay_posted;
			}
		}

		if ( empty( $methods ) && function_exists( 'is_checkout_pay_page' ) && is_checkout_pay_page() ) {
			global $wp;
			$order_id = isset( $wp->query_vars['order-pay'] ) ? absint( $wp->query_vars['order-pay'] ) : 0;
			$order    = $order_id ? wc_get_order( $order_id ) : false;
			if ( $order ) {
				foreach ( $order->get_shipping_methods() as $item ) {
					$instance = (int) $item->get_instance_id();
					$method   = (string) $item->get_method_id();
					$methods[] = $instance > 0 ? $method . ':' . $instance : $method;
				}
			}
		}

		$ids = array();
		foreach ( $methods as $rate_id ) {
			$rate_id = is_string( $rate_id ) ? trim( $rate_id ) : '';
			if ( '' === $rate_id ) {
				continue;
			}
			$ids[] = $rate_id;
		}

		return array_values( array_unique( $ids ) );
	}

	/**
	 * Shipping zones with method instances.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	public static function get_zones_with_methods() {
		if ( ! class_exists( 'WC_Shipping_Zones' ) ) {
			return array();
		}

		$out   = array();
		$zones = WC_Shipping_Zones::get_zones();

		foreach ( $zones as $zone_data ) {
			$methods = array();
			if ( ! empty( $zone_data['shipping_methods'] ) && is_array( $zone_data['shipping_methods'] ) ) {
				foreach ( $zone_data['shipping_methods'] as $method ) {
					$formatted = self::format_shipping_method( $method );
					if ( $formatted ) {
						$methods[] = $formatted;
					}
				}
			}

			$out[] = array(
				'id'      => isset( $zone_data['id'] ) ? (int) $zone_data['id'] : 0,
				'name'    => isset( $zone_data['zone_name'] ) ? (string) $zone_data['zone_name'] : '',
				'methods' => $methods,
			);
		}

		$worldwide  = new WC_Shipping_Zone( 0 );
		$ww_methods = array();
		foreach ( $worldwide->get_shipping_methods() as $method ) {
			$formatted = self::format_shipping_method( $method );
			if ( $formatted ) {
				$ww_methods[] = $formatted;
			}
		}

		$out[] = array(
			'id'      => 0,
			'name'    => $worldwide->get_zone_name(),
			'methods' => $ww_methods,
		);

		return $out;
	}

	/**
	 * @param WC_Shipping_Method $method Method.
	 * @return array<string,mixed>|null
	 */
	public static function format_shipping_method( $method ) {
		if ( ! is_object( $method ) || empty( $method->id ) ) {
			return null;
		}

		$instance_id = isset( $method->instance_id ) ? (int) $method->instance_id : 0;
		$rate_id     = $instance_id > 0 ? $method->id . ':' . $instance_id : (string) $method->id;
		$title       = method_exists( $method, 'get_title' ) ? (string) $method->get_title() : (string) $method->id;

		if ( '' === $title ) {
			$title = (string) $method->id;
		}

		return array(
			'rate_id'  => $rate_id,
			'id'       => (string) $method->id,
			'instance' => $instance_id,
			'title'    => $title,
			'enabled'  => isset( $method->enabled ) && 'yes' === $method->enabled,
		);
	}

	/**
	 * @return array<int,string>
	 */
	public static function get_all_rate_ids() {
		$ids = array();
		foreach ( self::get_zones_with_methods() as $zone ) {
			if ( empty( $zone['methods'] ) || ! is_array( $zone['methods'] ) ) {
				continue;
			}
			foreach ( $zone['methods'] as $method ) {
				if ( ! empty( $method['rate_id'] ) ) {
					$ids[] = (string) $method['rate_id'];
				}
			}
		}

		return array_values( array_unique( $ids ) );
	}

	/**
	 * Payment gateways.
	 *
	 * @param bool $only_enabled Only gateways enabled in WooCommerce.
	 * @return array<string,array<string,mixed>>
	 */
	public static function get_payment_gateways( $only_enabled = true ) {
		if ( ! function_exists( 'WC' ) || ! WC()->payment_gateways() ) {
			return array();
		}

		$out = array();
		foreach ( WC()->payment_gateways()->payment_gateways() as $id => $gateway ) {
			$enabled = isset( $gateway->enabled ) && 'yes' === $gateway->enabled;
			if ( $only_enabled && ! $enabled ) {
				continue;
			}

			$method_title = method_exists( $gateway, 'get_method_title' ) ? (string) $gateway->get_method_title() : (string) $id;
			$checkout     = method_exists( $gateway, 'get_title' ) ? (string) $gateway->get_title() : $method_title;

			$out[ (string) $id ] = array(
				'id'      => (string) $id,
				'title'   => $method_title,
				'label'   => $checkout,
				'enabled' => $enabled,
			);
		}

		return $out;
	}

	/**
	 * Config for checkout JS (Blocks).
	 *
	 * @return array<string,mixed>
	 */
	public static function js_config() {
		$map      = self::get_map();
		$js_map   = array();
		$gateways = array_keys( self::get_payment_gateways( true ) );

		foreach ( $map as $rate_id => $payments ) {
			$js_map[ $rate_id ] = array();
			foreach ( $payments as $gateway_id => $allowed ) {
				$js_map[ $rate_id ][ $gateway_id ] = (bool) $allowed;
			}
		}

		return array(
			'enabled'  => self::is_enabled(),
			'map'      => $js_map,
			'gateways' => $gateways,
		);
	}

	/**
	 * Count enabled payments for a shipping rate (admin badge).
	 *
	 * @param string                           $rate_id  Rate ID.
	 * @param array<string,array<string,bool>> $map      Map.
	 * @param array<string,array<string,mixed>> $gateways Gateways.
	 * @return array{on:int,total:int}
	 */
	public static function count_for_rate( $rate_id, $map, $gateways ) {
		$total = count( $gateways );
		$on    = 0;

		foreach ( $gateways as $gateway_id => $gateway ) {
			unset( $gateway );
			if ( self::is_payment_allowed( $map, $rate_id, $gateway_id ) ) {
				++$on;
			}
		}

		return array(
			'on'    => $on,
			'total' => $total,
		);
	}
}
