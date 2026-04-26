<?php
/**
 * Cart pricing adjustments for weighted products.
 *
 * @package Theme
 */

add_action( 'woocommerce_before_calculate_totals', 'func_quantity_based_price' );
function func_quantity_based_price( $cart_object ) {
	// 100 гр
	$product_cat_sir   = array();
	$product_cat_sir[] = 144;

	$product_cats_sir = get_categories(
		array(
			'taxonomy' => 'product_cat',
			'parent'   => 144,
		)
	);
	unset( $product_cats_sir, $product_cat_sir );

	foreach ( $cart_object->get_cart() as $cart_id => $cart_item ) {
		unset( $cart_id );

		$product = $cart_item['data'];
		if ( ! $product instanceof WC_Product ) {
			continue;
		}

		$product_id = (int) ( $product->is_type( 'variation' )
			? $product->get_parent_id()
			: $product->get_id()
		);

		$is_weighted = ferma_is_weighted_product( $product_id );

		if ( ! $is_weighted && ! ferma_product_in_ratio_01_categories( $product_id ) ) {
			continue;
		}

		if ( ! $is_weighted ) {
			continue;
		}

		$ratio = ferma_get_catalog_weight_ratio( $product_id );

		if ( $ratio == 1 || $ratio <= 0 ) {
			continue;
		}

		$price_per_kg = (float) $product->get_regular_price();
		if ( $price_per_kg <= 0 ) {
			$price_per_kg = (float) $product->get_price();
		}

		$product->set_price( $price_per_kg * $ratio );
	}

	$product_cat_pr5   = array();
	$product_cat_pr5[] = 156;
	$product_cats_kopch = get_categories(
		array(
			'taxonomy' => 'product_cat',
			'parent'   => 156,
		)
	);

	foreach ( $product_cats_kopch as $product_kopch ) {
		$product_cat_pr5[] = $product_kopch->term_id;
	}

	$product_cat_pr   = array();
	$product_cat_pr[] = 164;
	$product_cats_myaso = get_categories(
		array(
			'taxonomy' => 'product_cat',
			'parent'   => 164,
		)
	);

	foreach ( $product_cats_myaso as $product_myaso ) {
		$product_cat_pr[] = $product_myaso->term_id;
	}

	// Колбаски для жарки
	$product_cat_pr[] = 265;

	$product_cat_pr[] = 168;

	$product_cats_ryba = get_categories(
		array(
			'taxonomy' => 'product_cat',
			'parent'   => 168,
		)
	);

	foreach ( $product_cats_ryba as $product_ryba ) {
		$product_cat_pr[] = $product_ryba->term_id;
	}
}
