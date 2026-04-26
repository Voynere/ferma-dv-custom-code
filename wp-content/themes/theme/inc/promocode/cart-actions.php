<?php
/**
 * Q promocode cart actions and AJAX handlers.
 *
 * @package Theme
 */

function q_apply_promocode_discount( $promo ) {
	// Сохраняем полную информацию о промокоде в сессии.
	WC()->session->set(
		'q_active_promo',
		array(
			'code'          => $promo['code'],
			'id'            => $promo['id'],
			'discount_type' => $promo['discount_type'],
			'discount_val'  => $promo['discount_val'],
			'gift_sku'      => $promo['gift_sku'],
		)
	);

	// Рассчитываем скидку.
	$discount_amount = 0;

	if ( $promo['discount_type'] === 'percent' ) {
		$subtotal        = WC()->cart->get_subtotal();
		$discount_amount = ( $subtotal * $promo['discount_val'] ) / 100;
	} else {
		$discount_amount = $promo['discount_val'];
	}

	// Применяем скидку как fee (отрицательная плата).
	WC()->cart->add_fee( "Скидка по промокоду {$promo['code']}", -$discount_amount );

	return true;
}

add_action( 'wp_ajax_remove_q_promocode', 'handle_remove_q_promocode' );
add_action( 'wp_ajax_nopriv_remove_q_promocode', 'handle_remove_q_promocode' );

function handle_remove_q_promocode() {
	check_ajax_referer( 'q_promo_nonce', 'nonce' );

	// Удаляем подарки из корзины.
	$cart_items = WC()->cart->get_cart();

	foreach ( $cart_items as $cart_item_key => $cart_item ) {
		if ( isset( $cart_item['q_promo_gift'] ) ) {
			WC()->cart->remove_cart_item( $cart_item_key );
		}
	}

	// Удаляем промокод из сессии.
	WC()->session->__unset( 'q_active_promo' );

	// Пересчитываем корзину.
	WC()->cart->calculate_totals();

	wp_send_json_success(
		array(
			'message'   => 'Промокод удален',
			'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array() ),
		)
	);
}
