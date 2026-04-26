<?php
/**
 * Q promocode apply flow and AJAX apply endpoint.
 *
 * @package Theme
 */

add_action( 'wp_ajax_apply_q_promocode', 'handle_apply_q_promocode' );
add_action( 'wp_ajax_nopriv_apply_q_promocode', 'handle_apply_q_promocode' );

function handle_apply_q_promocode() {
	check_ajax_referer( 'q_promo_nonce', 'nonce' );

	$promo_code = sanitize_text_field( $_POST['promo_code'] );

	$result = q_apply_promocode_with_gift( $promo_code );

	if ( is_wp_error( $result ) ) {

		// Добавляем сообщение в стандартный WooCommerce вывод ошибок.
		wc_add_notice( $result->get_error_message(), 'error' );

		// Возвращаем JSON для фронта.
		wp_send_json_error(
			array(
				'message' => $result->get_error_message(),
				'wc_html' => wc_print_notices( true ), // HTML всех ошибок WC.
			)
		);
	} else {
		// ОБНОВЛЯЕМ КОРЗИНУ.
		WC()->cart->calculate_totals();
		WC()->cart->set_session();

		// ПОЛУЧАЕМ ФРАГМЕНТЫ КОРЗИНЫ.
		$fragments = get_cart_fragments();

		wp_send_json_success(
			array(
				'message'             => 'Промокод применен! Подарок добавлен в корзину.',
				'fragments'           => $fragments,
				'cart_contents_count' => WC()->cart->get_cart_contents_count(),
				'cart_total'          => WC()->cart->get_cart_total(),
			)
		);
	}
}

function q_apply_promocode_with_gift( $promo_code ) {
	$promo = q_get_local_promocode( $promo_code );

	if ( ! $promo ) {
		return new WP_Error(
			'invalid_promo',
			'Промокод с истекшим сроком, попробуйте ввести другой промокод'
		);
	}

	// ПРОВЕРКА ЛИМИТА — ИМЕННО В МОМЕНТ ПРИМЕНЕНИЯ.
	if ( ! q_can_use_promo_for_user( $promo ) ) {
		return new WP_Error(
			'usage_limit',
			'Промокод уже использован максимально допустимое количество раз'
		);
	}

	if ( ! empty( $promo['gift_sku'] ) ) {
		$gift_product_id = wc_get_product_id_by_sku( $promo['gift_sku'] );

		if ( ! $gift_product_id ) {
			return new WP_Error( 'gift_error', 'Товар-подарок не найден' );
		}

		// Чистим прошлые подарки.
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( isset( $cart_item['q_promo_gift'] ) ) {
				WC()->cart->remove_cart_item( $cart_item_key );
			}
		}

		$cart_item_data = array(
			'q_promo_gift' => true,
			'q_promo_code' => $promo_code,
			'custom_price' => 0,
		);

		$added = WC()->cart->add_to_cart( $gift_product_id, 1, 0, array(), $cart_item_data );

		if ( ! $added ) {
			return new WP_Error( 'gift_error', 'Не удалось добавить подарок в корзину' );
		}

		WC()->cart->calculate_totals();
		WC()->cart->set_session();
	}

	$result = q_apply_promocode_discount( $promo );

	if ( ! is_wp_error( $result ) ) {
		// Считаем использование промокода.
		q_mark_promo_used_for_user( $promo );
	}

	return $result;
}
