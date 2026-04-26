<?php
/**
 * Product page cart UI helpers.
 *
 * @package Theme
 */

add_action(
	'woocommerce_before_add_to_cart_button',
	function () {
		if ( ! WC()->cart ) {
			return;
		}

		global $product;
		$found_key = '';

		foreach ( WC()->cart->get_cart() as $key => $item ) {
			if ( (int) $item['product_id'] === (int) $product->get_id() ) {
				$found_key = $key; // нашли позицию
				break;
			}
		}

		if ( $found_key ) {
			echo '<input type="hidden" id="single_cart_item_key" value="' . esc_attr( $found_key ) . '">';
		}
	}
);
