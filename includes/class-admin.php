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
 * DC Interface admin page.
 */
class DC_Ship2pay_Admin {

	/**
	 * @return string
	 */
	public static function settings_url() {
		return admin_url( 'admin.php?page=dc-ship2pay' );
	}

	/**
	 * WooCommerce submenu.
	 */
	public static function register_menu() {
		add_submenu_page(
			'woocommerce',
			__( 'Design Cart Ship2pay', 'design-cart-ship2pay' ),
			__( 'Design Cart Ship2pay', 'design-cart-ship2pay' ),
			'manage_woocommerce',
			'dc-ship2pay',
			array( __CLASS__, 'render_page' )
		);
	}

	/**
	 * @param string $hook Hook.
	 */
	public static function enqueue( $hook = '' ) {
		unset( $hook );

		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( 'dc-ship2pay' !== $page ) {
			return;
		}

		$fa  = DC_SHIP2PAY_URL . 'admin/assets/font-awesome/font-awesome.min.css';
		$css = DC_SHIP2PAY_URL . 'admin/css/';
		$js  = DC_SHIP2PAY_URL . 'admin/js/';

		wp_enqueue_style( 'dc-ship2pay-font-awesome', $fa, array(), '4.7.0' );
		wp_enqueue_style( 'dc-ship2pay-dc-interface', $css . 'dc-interface.css', array( 'dc-ship2pay-font-awesome' ), DC_SHIP2PAY_VERSION );
		wp_enqueue_style( 'dc-ship2pay-admin', $css . 'admin-page.css', array( 'dc-ship2pay-dc-interface' ), DC_SHIP2PAY_VERSION );

		wp_enqueue_script( 'dc-ship2pay-dc-interface', $js . 'dc-interface.js', array(), DC_SHIP2PAY_VERSION, true );
		wp_enqueue_script( 'dc-ship2pay-admin', $js . 'admin.js', array( 'dc-ship2pay-dc-interface' ), DC_SHIP2PAY_VERSION, true );
	}

	/**
	 * admin-post handler.
	 */
	public static function handle_save() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'Nie masz uprawnień, aby to zrobić.', 'design-cart-ship2pay' ) );
		}

		check_admin_referer( 'dc_ship2pay_save' );

		$data = isset( $_POST['dc_ship2pay'] ) ? wp_unslash( $_POST['dc_ship2pay'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		DC_Ship2pay_Settings::save( is_array( $data ) ? $data : array() );

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'    => 'dc-ship2pay',
					'updated' => '1',
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	/**
	 * Settings screen.
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$dc_ship2pay_settings = DC_Ship2pay_Settings::all();
		$dc_ship2pay_zones    = DC_Ship2pay_Settings::get_zones_with_methods();
		$dc_ship2pay_gateways = DC_Ship2pay_Settings::get_payment_gateways( true );
		$dc_ship2pay_map      = DC_Ship2pay_Settings::get_map();

		include DC_SHIP2PAY_PATH . 'admin/views/settings.php';
	}
}
