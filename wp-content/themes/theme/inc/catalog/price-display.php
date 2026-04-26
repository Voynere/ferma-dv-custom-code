<?php
/**
 * Catalog price display helpers.
 *
 * @package Theme
 */

function fdv_get_cart_qty_for_product( $product_id ) {
	static $cart_qty_map = null;

	if ( ! WC()->cart || WC()->cart->is_empty() ) {
		return 0;
	}

	$product_id = (int) $product_id;

	if ( $cart_qty_map === null ) {
		$cart_qty_map = array();
		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$cart_product_id = (int) $cart_item['product_id'];
			if ( ! isset( $cart_qty_map[ $cart_product_id ] ) ) {
				$cart_qty_map[ $cart_product_id ] = 0;
			}
			$cart_qty_map[ $cart_product_id ] += (float) $cart_item['quantity'];
		}
	}

	return $cart_qty_map[ $product_id ] ?? 0;
}

function fdv_format_price_rub( $value ) {
	return number_format( (float) $value, 0, '', ' ' ) . ' ₽';
}

add_filter( 'woocommerce_get_price_html', 'wb_change_product_html', 30 );
function wb_change_product_html( $price ) {
	global $product;
	if ( ! $product ) {
		return $price;
	}

	$product_id  = $product->get_id();
	$real_price  = (float) $product->get_regular_price(); // без скидки
	$price_tovar = (float) $product->get_price();
	$is_weighted = ferma_is_weighted_product( $product_id );

	// НЕ весовые как были — можно оставить
	if ( ! $is_weighted ) {
		if ( $price_tovar != $real_price ) {
			return '<span class="old-price woocommerce-Price-amount amount">' . fdv_format_price_rub( $real_price ) . '</span>
                    <span class="woocommerce-Price-amount amount discount-offset" 
                          data-price-base="' . esc_attr( $price_tovar ) . '" 
                          data-ratio="1" 
                          data-is-weighted="0">
                        ' . fdv_format_price_rub( $price_tovar ) . ' <span class="price-unit-text">за шт.</span>
                    </span>';
		}

		return '<span class="woocommerce-Price-amount amount discount-offset"
                      data-price-base="' . esc_attr( $price_tovar ) . '" 
                      data-ratio="1" 
                      data-is-weighted="0">
                    ' . fdv_format_price_rub( $price_tovar ) . ' <span class="price-unit-text">за шт.</span>
                </span>';
	}

	// ВЕСОВЫЕ
	$ratio = ferma_get_catalog_weight_ratio( $product_id );

	// Смотрим, есть ли товар в корзине
	$cart_qty = fdv_get_cart_qty_for_product( $product_id );
	if ( $cart_qty <= 0 ) {
		$cart_qty = 1; // дефолт: 1 шаг (0.1 кг или 1 кг)
	}

	// Итоговый вес и цена для отображения
	$total_weight         = $ratio * $cart_qty;              // в кг
	$display_price_per_step = $price_tovar * $ratio;         // за один шаг
	$display_price_total  = $display_price_per_step * $cart_qty;

	$unit_label = fdv_format_weight( $total_weight );

	// СКИДКА (реальная цена за 1 кг была больше)
	if ( $price_tovar != $real_price ) {
		$old_price_total = (float) $real_price * $ratio * $cart_qty;

		return '<span class="old-price woocommerce-Price-amount amount">'
			. fdv_format_price_rub( $old_price_total ) . '</span>

                <span class="woocommerce-Price-amount amount discount-offset"
                      data-price-base="' . esc_attr( $price_tovar ) . '"  /* цена за 1 кг */
                      data-ratio="' . esc_attr( $ratio ) . '"
                      data-is-weighted="1">
                    ' . fdv_format_price_rub( $display_price_total ) . ' 
                    <span class="price-unit-text">за ' . esc_html( $unit_label ) . '</span>
                </span>';
	}

	// БЕЗ скидки
	return '<span class="woocommerce-Price-amount amount discount-offset"
                  data-price-base="' . esc_attr( $price_tovar ) . '"   /* цена за 1 кг */
                  data-ratio="' . esc_attr( $ratio ) . '"
                  data-is-weighted="1">
                ' . fdv_format_price_rub( $display_price_total ) . '
                <span class="price-unit-text">за ' . esc_html( $unit_label ) . '</span>
            </span>';
}

add_filter( 'woocommerce_quantity_input_args', 'fdv_default_qty_from_cart', 10, 2 );
function fdv_default_qty_from_cart( $args, $product ) {
	if ( is_admin() ) {
		return $args;
	}

	$product_id = $product->get_id();
	$cart_qty   = fdv_get_cart_qty_for_product( $product_id );

	if ( $cart_qty > 0 ) {
		$args['input_value'] = $cart_qty;
	}

	return $args;
}
