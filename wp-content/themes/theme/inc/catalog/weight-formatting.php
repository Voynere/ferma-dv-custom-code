<?php
/**
 * Catalog weight ratio and display formatting helpers.
 *
 * @package Theme
 */

function get_weight_ratio( $product_id ) {
	// Если включена разбивка веса – всегда 0.1 кг
	if ( ferma_is_weighted_product( $product_id ) ) {
		return 0.1;
	}

	// Всё остальное – без разбивки (1 кг или шт., в зависимости от логики)
	return 1;
}

function fdv_format_weight( $kg ) {
	$kg = (float) $kg;

	// нормализуем до 1 знака
	$kg = round( $kg, 1 );

	if ( abs( $kg - round( $kg ) ) < 0.00001 ) {
		return (int) round( $kg ) . ' кг';
	}

	return number_format( $kg, 1, ',', ' ' ) . ' кг';
}
