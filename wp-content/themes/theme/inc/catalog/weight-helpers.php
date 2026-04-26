<?php
/**
 * Catalog weighted product helper functions.
 *
 * @package Theme
 */

/**
 * Проверка: товар в категориях "0.1 кг" (по slug) или их подкатегориях
 */
function ferma_product_in_ratio_01_categories( $product_id ) {
	static $cache = array();

	$product_id = (int) $product_id;
	if ( isset( $cache[ $product_id ] ) ) {
		return $cache[ $product_id ];
	}

	// slugs категорий, для которых нужен шаг 0.1 кг
	$target_slugs = array(
		'domashnie-syry', // "Домашние сыры"
		'konfety',
	);

	$terms = get_the_terms( $product_id, 'product_cat' );
	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		$cache[ $product_id ] = false;
		return false;
	}

	foreach ( $terms as $term ) {
		// 1) Совпадение по slug самой категории
		if ( in_array( $term->slug, $target_slugs, true ) ) {
			$cache[ $product_id ] = true;
			return true;
		}

		// 2) Проверка всех предков
		$ancestors = get_ancestors( $term->term_id, 'product_cat' );
		if ( ! empty( $ancestors ) ) {
			foreach ( $ancestors as $ancestor_id ) {
				$parent = get_term( $ancestor_id, 'product_cat' );
				if ( $parent && ! is_wp_error( $parent ) && in_array( $parent->slug, $target_slugs, true ) ) {
					$cache[ $product_id ] = true;
					return true;
				}
			}
		}
	}

	$cache[ $product_id ] = false;
	return false;
}

function ferma_is_weighted_product( $product_id ) {
	static $weighted_cache = array();

	$product_id = (int) $product_id;
	if ( ! array_key_exists( $product_id, $weighted_cache ) ) {
		$weighted_cache[ $product_id ] = ( get_field( 'razbivka_vesa', $product_id ) == 'да' );
	}

	return $weighted_cache[ $product_id ];
}

function ferma_get_catalog_weight_ratio( $product_id ) {
	static $ratio_cache = array();

	$product_id = (int) $product_id;
	if ( ! isset( $ratio_cache[ $product_id ] ) ) {
		if ( function_exists( 'fdv_ms_get_weight_ratio_for_product' ) ) {
			$ratio = (float) fdv_ms_get_weight_ratio_for_product( $product_id );
		} else {
			$ratio = 0.1;
		}

		if ( ferma_product_in_ratio_01_categories( $product_id ) ) {
			$ratio = 0.1;
		}
		if ( $ratio <= 0 ) {
			$ratio = 0.1;
		}

		$ratio_cache[ $product_id ] = $ratio;
	}

	return $ratio_cache[ $product_id ];
}
