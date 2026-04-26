<?php
/**
 * Cart quantity AJAX endpoint.
 *
 * @package Theme
 */

add_action( 'wp_ajax_update_cart_qty', 'theme_update_cart_qty' );
add_action( 'wp_ajax_nopriv_update_cart_qty', 'theme_update_cart_qty' );

function theme_update_cart_qty() {
	check_ajax_referer( 'update_cart_qty', 'nonce' );

	$cart_item_key = isset( $_POST['cart_item_key'] )
		? wc_clean( wp_unslash( $_POST['cart_item_key'] ) )
		: '';

	$product_id = isset( $_POST['product_id'] )
		? absint( $_POST['product_id'] )
		: 0;

	$qty = isset( $_POST['qty'] )
		? wc_format_decimal( wp_unslash( $_POST['qty'] ) )
		: 0;

	$qty = (float) $qty;

	if ( ! WC()->cart ) {
		wp_send_json_error( array( 'message' => 'Cart not initialized' ) );
	}

	// --- НОВЫЙ БЛОК ПОИСКА СТРОКИ КОРЗИНЫ ---
	$line_key = '';

	// 1) Если пришел cart_item_key и он реально есть в корзине — используем его.
	if ( $cart_item_key && isset( WC()->cart->cart_contents[ $cart_item_key ] ) ) {
		$line_key = $cart_item_key;
	}
	// 2) Иначе, если пришел product_id — ищем по product_id в корзине.
	elseif ( $product_id ) {
		foreach ( WC()->cart->get_cart() as $key => $item ) {
			if ( (int) $item['product_id'] === (int) $product_id ) {
				$line_key = $key;
				break;
			}
		}
	}

	// 3) Если так и не нашли — отдаем ошибку.
	if ( ! $line_key ) {
		wp_send_json_error( array( 'message' => 'Cart item not found' ) );
	}

	// 4) Применяем количество.
	if ( $qty <= 0 ) {
		WC()->cart->remove_cart_item( $line_key );
	} else {
		WC()->cart->set_quantity( $line_key, $qty, true );
	}

	// 5) Отдаем фрагменты.
	WC_AJAX::get_refreshed_fragments();
	wp_die();
}
