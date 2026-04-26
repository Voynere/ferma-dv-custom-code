<?php
/**
 * Q promocode gift pricing and cart display hooks.
 *
 * @package Theme
 */

add_action( 'woocommerce_before_calculate_totals', 'set_zero_price_for_promo_gifts', 1 );
function set_zero_price_for_promo_gifts( $cart ) {
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}

	foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
		if ( isset( $cart_item['q_promo_gift'] ) && $cart_item['q_promo_gift'] ) {
			$cart_item['data']->set_price( 0 );
		}
	}
}

add_filter( 'woocommerce_cart_item_name', 'ferma_cart_gift_label_under_name', 10, 3 );
function ferma_cart_gift_label_under_name( $name, $cart_item, $cart_item_key ) {

	// Наш подарок по промокоду.
	if ( empty( $cart_item['q_promo_gift'] ) ) {
		return $name;
	}

	/** @var WC_Product $product */
	$product = $cart_item['data'];
	$qty     = max( 1, (int) $cart_item['quantity'] );

	// Берём базовую цену (до обнуления).
	$regular_price = (float) $product->get_regular_price();
	if ( $regular_price <= 0 ) {
		$regular_price = (float) $product->get_price();
	}

	// Если вообще нет адекватной цены — только "В подарок".
	if ( $regular_price <= 0 ) {
		return $name . '<div class="ferma-gift-info"><span class="ferma-gift-label">В подарок</span></div>';
	}

	// Старая сумма за всё количество.
	$old_line_total = wc_price( $regular_price * $qty );

	// Без sprintf — безопасно.
	$gift_html =
		'<div class="ferma-gift-info">
			<span class="ferma-gift-old-price"><del>' . $old_line_total . '</del></span>
			<span class="ferma-gift-label">В подарок</span>
		</div>';

	return $name . $gift_html;
}

add_filter( 'woocommerce_cart_item_subtotal', 'ferma_gift_subtotal_replace', 10, 3 );
function ferma_gift_subtotal_replace( $subtotal, $cart_item, $cart_item_key ) {

	// В чекауте НИЧЕГО не меняем – пусть будет стандартный 0 ₽.
	if ( function_exists( 'is_checkout' ) && is_checkout() ) {
		return $subtotal;
	}

	// Не подарок — не трогаем.
	if ( empty( $cart_item['q_promo_gift'] ) ) {
		return $subtotal;
	}

	/** @var WC_Product $product */
	$product = $cart_item['data'];
	$qty     = max( 1, (int) $cart_item['quantity'] );

	$regular_price = (float) $product->get_regular_price();
	if ( $regular_price <= 0 ) {
		$regular_price = (float) $product->get_price();
	}

	if ( $regular_price <= 0 ) {
		return '<div class="ferma-gift-subtotal">
					<span class="ferma-gift-label">В подарок</span>
				</div>';
	}

	$old_line_total = wc_price( $regular_price * $qty );

	return sprintf(
		'<div class="ferma-gift-subtotal">
			<span class="ferma-gift-old"><del>%s</del></span>
			<span class="ferma-gift-label">В подарок</span>
		</div>',
		$old_line_total
	);
}

add_filter(
	'woocommerce_cart_item_data_to_restore',
	function( $item_data, $cart_item ) {
		if ( isset( $cart_item['q_promo_gift'] ) ) {
			$item_data['q_promo_gift'] = $cart_item['q_promo_gift'];
		}
		return $item_data;
	},
	10,
	2
);
add_filter(
	'woocommerce_add_cart_item_data',
	function( $cart_item_data, $product_id, $variation_id ) {
		if ( isset( $cart_item_data['q_promo_gift'] ) ) {
			$cart_item_data['q_promo_gift'] = true;
		}
		return $cart_item_data;
	},
	10,
	3
);
