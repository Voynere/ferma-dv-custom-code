<?php
/**
 * Cart item display pricing customizations.
 *
 * @package Theme
 */

function custom_display_price( $price, $cart_item, $cart_item_key ) {
	if ( empty( $cart_item['data'] ) || ! $cart_item['data'] instanceof WC_Product ) {
		return $price;
	}

	$product    = $cart_item['data'];
	$product_id = $product->get_id();
	$qty        = isset( $cart_item['quantity'] ) ? (float) $cart_item['quantity'] : 1;

	if ( $qty <= 0 ) {
		return $price;
	}

	// Коэффициент веса (для весовых 0.1, для обычных 1)
	$weight_ratio = function_exists( 'get_weight_ratio' ) ? (float) get_weight_ratio( $product_id ) : 1;
	if ( $weight_ratio <= 0 ) {
		$weight_ratio = 1;
	}

	// Базовая старая цена за 1 "единицу" (1 кг / 1 шт)
	$regular_base = (float) $product->get_regular_price();
	if ( $regular_base <= 0 ) {
		return $price; // нечего сравнивать
	}

	$discounted_base = (float) $product->get_price() / $weight_ratio;

	// Если реальной скидки нет – оставляем стандартный вывод
	if ( $discounted_base >= $regular_base - 0.01 ) {
		return $price;
	}

	// Считаем старую и новую сумму за всю позицию
	$old_total = $regular_base * $weight_ratio * $qty;
	$new_total = $discounted_base * $weight_ratio * $qty;

	$currency = get_woocommerce_currency_symbol();

	return sprintf(
		'<span class="woocommerce-Price-amount amount"><bdi><s>%s</s>&nbsp;%s&nbsp;<span class="woocommerce-Price-currencySymbol">%s</span></bdi></span>',
		wc_format_decimal( $old_total, 0 ),
		wc_format_decimal( $new_total, 0 ),
		esc_html( $currency )
	);
}
add_filter( 'woocommerce_cart_item_subtotal', 'custom_display_price', 10, 3 );
