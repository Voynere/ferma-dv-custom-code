<?php
/**
 * Cart fragments helpers.
 *
 * @package Theme
 */

function get_cart_fragments() {
	$fragments = array();

	if ( ! function_exists( 'WC' ) || ! WC() || ! WC()->cart ) {
		return $fragments;
	}

	// Мини-корзина.
	ob_start();
	woocommerce_mini_cart();
	$fragments['div.widget_shopping_cart_content'] = '<div class="widget_shopping_cart_content">' . ob_get_clean() . '</div>';

	// Счетчик товаров (если у вас есть такой элемент).
	$fragments['span.cart-contents-count'] = '<span class="cart-contents-count">' . WC()->cart->get_cart_contents_count() . '</span>';

	// Итоговая сумма (если у вас есть такой элемент).
	$fragments['span.cart-total'] = '<span class="cart-total">' . WC()->cart->get_cart_total() . '</span>';

	// Обновляем всю секцию корзины если есть.
	ob_start();
	echo '<div class="cart-update-section">';
	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

		if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 ) {
			echo '<div class="cart-item">';
			echo $_product->get_name() . ' × ' . $cart_item['quantity'];
			echo '</div>';
		}
	}
	echo '</div>';
	$fragments['div.cart-update-section'] = ob_get_clean();

	return $fragments;
}
