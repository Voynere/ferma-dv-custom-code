<?php
/**
 * Q promocode cart session synchronization hooks.
 *
 * @package Theme
 */

add_filter(
	'woocommerce_add_cart_item_data',
	function ( $cart_item_data, $product_id, $variation_id ) {
		if ( isset( $cart_item_data['q_promo_gift'] ) ) {
			$cart_item_data['q_promo_gift'] = 1; // лучше 1, чем true.
		}
		if ( isset( $cart_item_data['q_promo_code'] ) ) {
			$cart_item_data['q_promo_code'] = sanitize_text_field( $cart_item_data['q_promo_code'] );
		}
		return $cart_item_data;
	},
	10,
	3
);

add_filter(
	'woocommerce_get_cart_item_from_session',
	function ( $item, $values ) {

		// Woo сохраняет custom cart item data на верхнем уровне,
		// но при некоторых сценариях часть может оказаться в $values['data'].
		// Поэтому проверяем оба варианта.
		if ( isset( $values['q_promo_gift'] ) ) {
			$item['q_promo_gift'] = $values['q_promo_gift'];
		} elseif ( isset( $values['data']['q_promo_gift'] ) ) {
			$item['q_promo_gift'] = $values['data']['q_promo_gift'];
		}

		if ( isset( $values['q_promo_code'] ) ) {
			$item['q_promo_code'] = sanitize_text_field( $values['q_promo_code'] );
		} elseif ( isset( $values['data']['q_promo_code'] ) ) {
			$item['q_promo_code'] = sanitize_text_field( $values['data']['q_promo_code'] );
		}

		if ( isset( $values['custom_price'] ) ) {
			$item['custom_price'] = (float) $values['custom_price'];
		} elseif ( isset( $values['data']['custom_price'] ) ) {
			$item['custom_price'] = (float) $values['data']['custom_price'];
		}

		return $item;
	},
	20,
	2
);
