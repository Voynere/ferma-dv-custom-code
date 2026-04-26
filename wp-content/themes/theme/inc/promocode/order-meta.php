<?php
/**
 * Q promocode order meta integration.
 *
 * @package Theme
 */

// Сохраняем активный Q-промокод в мета-поля заказа.
add_action( 'woocommerce_checkout_create_order', 'ferma_save_qpromo_to_order', 30, 2 );
function ferma_save_qpromo_to_order( WC_Order $order, $data ) {

	// Берём активный промо из сессии (то, что ты кладёшь в q_active_promo).
	$active = q_get_active_promocode();

	$code = '';
	if ( ! empty( $active['code'] ) ) {
		$code = $active['code'];
	} elseif ( ! empty( $_COOKIE['ferma_promo_code'] ) ) {
		// запасной вариант – из куки.
		$code = sanitize_text_field( wp_unslash( $_COOKIE['ferma_promo_code'] ) );
	}

	if ( ! $code ) {
		return;
	}

	$code = strtoupper( trim( $code ) );

	// Чтобы не схватить мусор – только формата твоих промо.
	if ( ! preg_match( '/^[A-Z0-9]{1,9}$/', $code ) ) {
		return;
	}

	// Сохраняем в мету заказа, откуда потом берём для МойСклада.
	$order->update_meta_data( 'q_promocode', $code );
}
