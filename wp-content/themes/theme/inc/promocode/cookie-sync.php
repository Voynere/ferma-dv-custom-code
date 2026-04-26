<?php
/**
 * Q promocode cookie to session synchronization.
 *
 * @package Theme
 */

add_action( 'woocommerce_checkout_update_order_review', 'ferma_apply_q_promo_from_cookie', 10, 1 );

function ferma_apply_q_promo_from_cookie( $posted_data ) {
	// Берём промокод из cookie.
	$code = ! empty( $_COOKIE['ferma_promo_code'] )
		? sanitize_text_field( wp_unslash( $_COOKIE['ferma_promo_code'] ) )
		: '';

	$active = q_get_active_promocode();

	// 1) Куки НЕТ – промо считаем выключенным, чистим сессию и выходим.
	if ( ! $code ) {
		if ( $active && function_exists( 'WC' ) && WC() && WC()->session ) {
			WC()->session->__unset( 'q_active_promo' );
		}
		return;
	}

	// 2) Кука есть, но этот же код уже активен – ничего не делаем,
	// чтобы не дублировать скидку/подарок.
	if ( $active && ! empty( $active['code'] ) && strtoupper( $active['code'] ) === strtoupper( $code ) ) {
		return;
	}

	// 3) Пытаемся применить промо.
	$result = q_apply_promocode_with_gift( $code );

	if ( is_wp_error( $result ) ) {
		if ( function_exists( 'WC' ) && WC() && WC()->session ) {
			WC()->session->__unset( 'q_active_promo' );
		}

		// Чистим куку.
		setcookie( 'ferma_promo_code', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN );
		if ( SITECOOKIEPATH !== COOKIEPATH ) {
			setcookie( 'ferma_promo_code', '', time() - 3600, SITECOOKIEPATH, COOKIE_DOMAIN );
		}

		// ВАЖНО: добавляем notice, чтобы Woo вернул его в "messages".
		wc_add_notice( $result->get_error_message(), 'error' );

		return;
	}
}

add_action(
	'woocommerce_before_checkout_form',
	function () {
		// Только на checkout и только не в AJAX.
		if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || wp_doing_ajax() ) {
			return;
		}

		// posted_data внутри не используется, можно передать пустую строку.
		ferma_apply_q_promo_from_cookie( '' );

		// Пересчёт тоталов после возможного применения промо.
		if ( WC()->cart ) {
			WC()->cart->calculate_totals();
			WC()->cart->set_session();
		}
	},
	5
);
