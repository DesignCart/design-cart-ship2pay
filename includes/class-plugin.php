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
 * Plugin bootstrap / hooks.
 */
class DC_Ship2pay_Plugin {

	/**
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * @return self
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register hooks.
	 */
	public function init() {
		add_action( 'admin_menu', array( 'DC_Ship2pay_Admin', 'register_menu' ), 60 );
		add_action( 'admin_enqueue_scripts', array( 'DC_Ship2pay_Admin', 'enqueue' ) );
		add_action( 'admin_post_dc_ship2pay_save', array( 'DC_Ship2pay_Admin', 'handle_save' ) );
		add_filter( 'plugin_action_links_' . DC_SHIP2PAY_BASENAME, array( $this, 'action_links' ) );

		DC_Ship2pay_Frontend::init();
	}

	/**
	 * @param array<int,string> $links Links.
	 * @return array<int,string>
	 */
	public function action_links( $links ) {
		$url = DC_Ship2pay_Admin::settings_url();
		array_unshift(
			$links,
			'<a href="' . esc_url( $url ) . '">' . esc_html__( 'Ustawienia', 'design-cart-ship2pay' ) . '</a>'
		);

		return $links;
	}
}
