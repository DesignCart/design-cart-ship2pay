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

$dc_ship2pay_enabled        = isset( $dc_ship2pay_settings['enabled'] ) ? $dc_ship2pay_settings['enabled'] : 'yes';
$dc_ship2pay_has_shipping   = false;
$dc_ship2pay_first_rate_id  = '';
$dc_ship2pay_shipping_count = 0;

foreach ( $dc_ship2pay_zones as $dc_ship2pay_zone ) {
	if ( ! empty( $dc_ship2pay_zone['methods'] ) ) {
		$dc_ship2pay_has_shipping = true;
		if ( '' === $dc_ship2pay_first_rate_id ) {
			$dc_ship2pay_first_rate_id = (string) $dc_ship2pay_zone['methods'][0]['rate_id'];
		}
		$dc_ship2pay_shipping_count += count( $dc_ship2pay_zone['methods'] );
	}
}
?>
<div class="wrap dc-ship2pay-admin-wrap">
	<h1><?php esc_html_e( 'Design Cart Ship2pay', 'design-cart-ship2pay' ); ?></h1>
	<div class="dc-page dc-ship2pay-admin-page">
		<div class="dc-hero">
			<div class="dc-hero__mesh"></div>
			<img class="dc-hero__logo" src="<?php echo esc_url( DC_SHIP2PAY_URL . 'admin/assets/dc-logo-white.png' ); ?>" alt="" aria-hidden="true" width="384" height="384">
			<div class="dc-hero__orb dc-hero__orb--1"></div>
			<div class="dc-hero__orb dc-hero__orb--2"></div>
			<div class="dc-hero__inner">
				<div class="dc-hero__row">
					<div class="dc-hero__brand">
						<span class="dc-hero__icon"><i class="fa fa-exchange"></i></span>
						<div>
							<p class="dc-hero__eyebrow">Design Cart</p>
							<h1 class="dc-hero__title"><?php esc_html_e( 'Design Cart Ship2pay', 'design-cart-ship2pay' ); ?></h1>
						</div>
					</div>
					<div class="dc-hero__actions">
						<button type="submit" form="dc-ship2pay-form" class="dc-btn dc-btn--light">
							<i class="fa fa-save"></i> <?php esc_html_e( 'Zapisz', 'design-cart-ship2pay' ); ?>
						</button>
					</div>
				</div>
				<ul class="dc-hero__bc">
					<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings' ) ); ?>">WooCommerce</a></li>
					<li><a href="<?php echo esc_url( DC_Ship2pay_Admin::settings_url() ); ?>"><?php esc_html_e( 'Design Cart Ship2pay', 'design-cart-ship2pay' ); ?></a></li>
					<li><?php esc_html_e( 'Ustawienia', 'design-cart-ship2pay' ); ?></li>
				</ul>
			</div>
		</div>

		<div class="dc-page__wrap">
			<div class="dc-form-card">
				<div class="dc-interface" id="dcShip2payInterface">
					<?php if ( ! empty( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
						<div class="notice notice-success is-dismissible">
							<p><?php esc_html_e( 'Ustawienia zostały zapisane.', 'design-cart-ship2pay' ); ?></p>
						</div>
					<?php endif; ?>

					<form class="dc-form dc-form--full" id="dc-ship2pay-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<?php wp_nonce_field( 'dc_ship2pay_save' ); ?>
						<input type="hidden" name="action" value="dc_ship2pay_save">

						<div class="dc-form-card__body">
							<div class="dc-section-card dc-section">
								<div class="dc-section-card__head">
									<span class="dc-section-card__icon"><i class="fa fa-cog"></i></span>
									<div>
										<h3 class="dc-section-card__title"><?php esc_html_e( 'Wysyłka → płatność', 'design-cart-ship2pay' ); ?></h3>
										<p class="dc-section-card__sub"><?php esc_html_e( 'Kliknij metodę wysyłki po lewej. Po prawej włącz lub wyłącz płatności, które mają być dostępne przy tej wysyłce.', 'design-cart-ship2pay' ); ?></p>
									</div>
								</div>

								<label class="dc-toggle dc-ship2pay-master">
									<input class="dc-toggle__input" type="checkbox" name="dc_ship2pay[enabled]" value="yes" <?php checked( $dc_ship2pay_enabled, 'yes' ); ?>>
									<span class="dc-toggle__track"></span>
									<span><?php esc_html_e( 'Włącz filtrowanie płatności na kasie', 'design-cart-ship2pay' ); ?></span>
								</label>
							</div>

							<?php if ( ! $dc_ship2pay_has_shipping ) : ?>
								<div class="dc-section-card dc-section">
									<p class="dc-hint" style="margin:0;">
										<?php
										echo wp_kses_post(
											sprintf(
												/* translators: %s: shipping zones URL */
												__( 'Nie znaleziono metod wysyłki. Dodaj je w <a href="%s">WooCommerce → Ustawienia → Wysyłka</a>.', 'design-cart-ship2pay' ),
												esc_url( admin_url( 'admin.php?page=wc-settings&tab=shipping' ) )
											)
										);
										?>
									</p>
								</div>
							<?php elseif ( empty( $dc_ship2pay_gateways ) ) : ?>
								<div class="dc-section-card dc-section">
									<p class="dc-hint" style="margin:0;">
										<?php
										echo wp_kses_post(
											sprintf(
												/* translators: %s: payments URL */
												__( 'Nie znaleziono włączonych metod płatności. Włącz je w <a href="%s">WooCommerce → Ustawienia → Płatności</a>.', 'design-cart-ship2pay' ),
												esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout' ) )
											)
										);
										?>
									</p>
								</div>
							<?php else : ?>
								<div class="dc-ship2pay-grid" data-dc-ship2pay-mapper>
									<aside class="dc-ship2pay-col dc-ship2pay-col--shipping" aria-label="<?php esc_attr_e( 'Metody wysyłki', 'design-cart-ship2pay' ); ?>">
										<div class="dc-ship2pay-col__head">
											<span class="dc-ship2pay-col__title"><i class="fa fa-truck"></i> <?php esc_html_e( 'Wysyłka', 'design-cart-ship2pay' ); ?></span>
											<span class="dc-ship2pay-col__meta"><?php echo esc_html( (string) $dc_ship2pay_shipping_count ); ?></span>
										</div>
										<div class="dc-ship2pay-col__body">
											<?php foreach ( $dc_ship2pay_zones as $dc_ship2pay_zone ) : ?>
												<?php if ( empty( $dc_ship2pay_zone['methods'] ) ) : ?>
													<?php continue; ?>
												<?php endif; ?>
												<p class="dc-ship2pay-zone"><?php echo esc_html( $dc_ship2pay_zone['name'] ); ?></p>
												<?php foreach ( $dc_ship2pay_zone['methods'] as $dc_ship2pay_method ) : ?>
													<?php
													$dc_ship2pay_rate_id = (string) $dc_ship2pay_method['rate_id'];
													$dc_ship2pay_active  = $dc_ship2pay_rate_id === $dc_ship2pay_first_rate_id;
													$dc_ship2pay_counts  = DC_Ship2pay_Settings::count_for_rate( $dc_ship2pay_rate_id, $dc_ship2pay_map, $dc_ship2pay_gateways );
													?>
													<button
														type="button"
														class="dc-ship2pay-ship<?php echo $dc_ship2pay_active ? ' is-active' : ''; ?><?php echo empty( $dc_ship2pay_method['enabled'] ) ? ' is-disabled' : ''; ?>"
														data-ship="<?php echo esc_attr( $dc_ship2pay_rate_id ); ?>"
														aria-pressed="<?php echo $dc_ship2pay_active ? 'true' : 'false'; ?>"
													>
														<span class="dc-ship2pay-ship__name"><?php echo esc_html( $dc_ship2pay_method['title'] ); ?></span>
														<span class="dc-ship2pay-ship__meta">
															<?php if ( empty( $dc_ship2pay_method['enabled'] ) ) : ?>
																<span class="dc-ship2pay-pill dc-ship2pay-pill--off"><?php esc_html_e( 'wyłączona', 'design-cart-ship2pay' ); ?></span>
															<?php endif; ?>
															<span class="dc-ship2pay-count" data-ship-count="<?php echo esc_attr( $dc_ship2pay_rate_id ); ?>"><?php echo esc_html( $dc_ship2pay_counts['on'] . '/' . $dc_ship2pay_counts['total'] ); ?></span>
														</span>
													</button>
												<?php endforeach; ?>
											<?php endforeach; ?>
										</div>
									</aside>

									<section class="dc-ship2pay-col dc-ship2pay-col--payments" aria-label="<?php esc_attr_e( 'Metody płatności', 'design-cart-ship2pay' ); ?>">
										<div class="dc-ship2pay-col__head">
											<span class="dc-ship2pay-col__title"><i class="fa fa-credit-card"></i> <?php esc_html_e( 'Płatności', 'design-cart-ship2pay' ); ?></span>
											<span class="dc-ship2pay-col__actions">
												<button type="button" class="dc-btn dc-btn--ghost dc-ship2pay-bulk" data-bulk="on"><?php esc_html_e( 'Włącz wszystkie', 'design-cart-ship2pay' ); ?></button>
												<button type="button" class="dc-btn dc-btn--ghost dc-ship2pay-bulk" data-bulk="off"><?php esc_html_e( 'Wyłącz wszystkie', 'design-cart-ship2pay' ); ?></button>
											</span>
										</div>
										<div class="dc-ship2pay-col__body">
											<?php foreach ( $dc_ship2pay_zones as $dc_ship2pay_zone ) : ?>
												<?php
												if ( empty( $dc_ship2pay_zone['methods'] ) ) {
													continue;
												}
												?>
												<?php foreach ( $dc_ship2pay_zone['methods'] as $dc_ship2pay_method ) : ?>
													<?php
													$dc_ship2pay_rate_id = (string) $dc_ship2pay_method['rate_id'];
													$dc_ship2pay_active  = $dc_ship2pay_rate_id === $dc_ship2pay_first_rate_id;
													?>
													<div
														class="dc-ship2pay-panel<?php echo $dc_ship2pay_active ? ' is-active' : ''; ?>"
														data-ship-panel="<?php echo esc_attr( $dc_ship2pay_rate_id ); ?>"
														<?php echo $dc_ship2pay_active ? '' : 'hidden'; ?>
													>
														<p class="dc-ship2pay-panel__lead">
															<?php
															echo esc_html(
																sprintf(
																	/* translators: 1: shipping method, 2: zone name */
																	__( 'Dla wysyłki „%1$s” (%2$s)', 'design-cart-ship2pay' ),
																	$dc_ship2pay_method['title'],
																	$dc_ship2pay_zone['name']
																)
															);
															?>
														</p>
														<ul class="dc-ship2pay-pays">
															<?php foreach ( $dc_ship2pay_gateways as $dc_ship2pay_gateway_id => $dc_ship2pay_gateway ) : ?>
																<?php
																$dc_ship2pay_checked = DC_Ship2pay_Settings::is_payment_allowed( $dc_ship2pay_map, $dc_ship2pay_rate_id, $dc_ship2pay_gateway_id );
																$dc_ship2pay_field   = 'dc_ship2pay[map][' . $dc_ship2pay_rate_id . '][' . $dc_ship2pay_gateway_id . ']';
																$dc_ship2pay_input   = 'dc-ship2pay-' . md5( $dc_ship2pay_rate_id . '|' . $dc_ship2pay_gateway_id );
																?>
																<li class="dc-ship2pay-pay">
																	<input type="hidden" name="<?php echo esc_attr( $dc_ship2pay_field ); ?>" value="0">
																	<label class="dc-toggle" for="<?php echo esc_attr( $dc_ship2pay_input ); ?>">
																		<input
																			class="dc-toggle__input"
																			type="checkbox"
																			id="<?php echo esc_attr( $dc_ship2pay_input ); ?>"
																			name="<?php echo esc_attr( $dc_ship2pay_field ); ?>"
																			value="1"
																			<?php checked( $dc_ship2pay_checked ); ?>
																		>
																		<span class="dc-toggle__track"></span>
																		<span class="dc-ship2pay-pay__text">
																			<strong><?php echo esc_html( $dc_ship2pay_gateway['label'] ? $dc_ship2pay_gateway['label'] : $dc_ship2pay_gateway['title'] ); ?></strong>
																			<small><?php echo esc_html( $dc_ship2pay_gateway['title'] ); ?> · <?php echo esc_html( $dc_ship2pay_gateway_id ); ?></small>
																		</span>
																	</label>
																</li>
															<?php endforeach; ?>
														</ul>
													</div>
												<?php endforeach; ?>
											<?php endforeach; ?>
										</div>
									</section>
								</div>
							<?php endif; ?>

							<div class="dc-actions">
								<button type="submit" class="dc-btn dc-btn--primary"><i class="fa fa-save"></i> <?php esc_html_e( 'Zapisz', 'design-cart-ship2pay' ); ?></button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
