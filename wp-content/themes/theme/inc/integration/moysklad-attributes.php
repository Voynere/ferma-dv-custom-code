<?php
/**
 * MoySklad product attribute integration helpers.
 *
 * @package Theme
 */

add_filter( 'wms_product_attributes', 'fdv_add_fasovka_attribute', 10, 3 );

function fdv_add_fasovka_attribute( $attributes, $product, $ms_product ) {
	unset( $product );

	if ( empty( $ms_product->attributes ) ) {
		return $attributes;
	}

	foreach ( $ms_product->attributes as $attr ) {
		if ( $attr->name !== 'Фасовка' ) { // ← НАЗВАНИЕ характеристики в МойСкладе!
			continue;
		}

		$value = trim( (string) $attr->value );
		if ( $value === '' ) {
			continue;
		}

		// Создаст/использует таксономию pa_fasovka
		$taxonomy = 'pa_fasovka';

		// Создаём термин, если нет
		if ( ! term_exists( $value, $taxonomy ) ) {
			wp_insert_term( $value, $taxonomy );
		}

		$term = get_term_by( 'name', $value, $taxonomy );
		if ( ! $term ) {
			continue;
		}

		// Записываем в массив атрибутов, который WooMS затем присвоит товару
		$attributes[ $taxonomy ] = array(
			'name'         => $taxonomy,
			'value'        => $term->term_id,
			'is_visible'   => 1,
			'is_variation' => 0,
			'is_taxonomy'  => 1,
		);
	}

	return $attributes;
}
