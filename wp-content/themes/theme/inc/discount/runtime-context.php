<?php
/**
 * Runtime discount context and price helpers.
 *
 * @package Theme
 */

//add_filter( 'woocommerce_product_get_price' , 'products_price_with_discount' , 5, 2 );
add_filter( 'woocommerce_product_get_price', 'products_price_with_discount', 40, 2 );
//add_filter( 'woocommerce_product_variation_get_price', 'products_price_with_discount', 5, 2 );
//add_filter( 'woocommerce_product_get_sale_price', 'products_price_with_discount', 5, 2 );
//add_filter( 'woocommerce_product_variation_get_sale_price', 'products_price_with_discount', 5, 2 );
//add_filter( 'woocommerce_variation_prices_price', 'products_price_with_discount', 5, 2 );
//add_filter( 'woocommerce_variation_prices_sale_price', 'products_price_with_discount', 5, 2 );

function ferma_get_discount_runtime_context() {
	static $ctx = null;
	if ( $ctx !== null ) {
		return $ctx;
	}

	$price_date_raw = (string) ferma_get_cached_option_field( 'pricedate' );
	$zp_end_raw     = (string) ferma_get_cached_option_field( 'zp_date_end' );
	$zp_start_raw   = (string) ferma_get_cached_option_field( 'zp_date_start' );

	$price_end_ts = strtotime( date( 'Y-m-d 23:59:59', strtotime( $price_date_raw ) ) );
	$zp_end_ts    = strtotime( date( 'Y-m-d 23:59:59', strtotime( $zp_end_raw ) ) );
	$zp_start_ts  = strtotime( date( 'Y-m-d 23:59:59', strtotime( $zp_start_raw ) ) );

	$ctx = array(
		'now_ts'       => time(),
		'price_end_ts' => $price_end_ts ?: 0,
		'zp_end_ts'    => $zp_end_ts ?: 0,
		'zp_start_ts'  => $zp_start_ts ?: 0,
	);

	return $ctx;
}

function products_price_with_discount( $price, $product ) {
	$discount   = ferma_get_cached_option_field( 'priceint' );
	$product_id = $product->get_id();

	//$price = $product->get_regular_price();

	//$_product = wc_get_product( $product_id );

	//$attributes = $product->get_attributes();

	$is_action = $product->get_attribute( 'pa_akcziya' );

	$ctx          = ferma_get_discount_runtime_context();
	$end_date     = (int) $ctx['price_end_ts'];
	$current_date = (int) $ctx['now_ts'];

	$green_friday_discount = ferma_get_green_friday_discount_for_product( $product_id );
	if ( $green_friday_discount !== null ) {
		$is_action = 1;
		$end_date  = (int) $ctx['zp_end_ts'];
		$discount  = $green_friday_discount;
	}

	if ( $end_date > $current_date && $discount > 0 && $is_action == 1 ) {
		//echo $price . "<br>" . $price - (($price / 100) * $discount);
		return $price - ( ( $price / 100 ) * $discount );
	} else {
		return $price;
	}
}

function get_green_friday_products() {
	static $green_friday_products = null;

	if ( $green_friday_products !== null ) {
		return $green_friday_products;
	}

	$cache_key = 'ferma_green_friday_products';
	$cached    = wp_cache_get( $cache_key, 'theme' );
	if ( $cached !== false ) {
		$green_friday_products = $cached;
		return $green_friday_products;
	}

	$path                 = trailingslashit( $_SERVER['DOCUMENT_ROOT'] ) . 'green-friday.json';
	$green_friday_products = array(
		'good_ids'               => array(),
		'good_ids_with_discount' => array(),
	);

	if ( is_readable( $path ) ) {
		$goods   = file_get_contents( $path );
		$decoded = json_decode( $goods, true );
		if ( is_array( $decoded ) ) {
			$green_friday_products = array_merge( $green_friday_products, $decoded );
		}
	}

	wp_cache_set( $cache_key, $green_friday_products, 'theme', 300 );

	return $green_friday_products;
}

function ferma_get_green_friday_discount_for_product( $product_id ) {
	static $discount_map = null;

	if ( $discount_map === null ) {
		$discount_map          = array();
		$green_friday_products = get_green_friday_products();
		$product_groups        = $green_friday_products['good_ids_with_discount'] ?? array();

		foreach ( $product_groups as $percent => $product_ids ) {
			foreach ( (array) $product_ids as $id ) {
				$discount_map[ (int) $id ] = (int) $percent;
			}
		}
	}

	$product_id = (int) $product_id;

	return $discount_map[ $product_id ] ?? null;
}

function ferma_get_cached_option_field( $field_name ) {
	static $option_cache = array();

	if ( ! array_key_exists( $field_name, $option_cache ) ) {
		$option_cache[ $field_name ] = get_field( $field_name, 'option' );
	}

	return $option_cache[ $field_name ];
}

function product_is_green_price( $product ) {
	$product_id = $product->get_id();

	$is_action = $product->get_attribute( 'pa_akcziya' );

	$discount     = ferma_get_cached_option_field( 'priceint' );
	$ctx          = ferma_get_discount_runtime_context();
	$end_date     = (int) $ctx['price_end_ts'];
	$current_date = (int) $ctx['now_ts'];

	$green_friday_discount = ferma_get_green_friday_discount_for_product( $product_id );
	if ( $green_friday_discount !== null ) {
		$is_action = 1;
		$end_date  = (int) $ctx['zp_end_ts'];
		$discount  = $green_friday_discount;
	}

	if ( $end_date > $current_date && $discount > 0 && $is_action == 1 ) {
		return true;
	}

	return false;
}

//add_filter( 'woocommerce_get_price_html', 'products_price_html_with_discount', 10, 2 );
function products_price_html_with_discount( $price, $product ) {
	//echo $price;
	unset( $product );
	return $price;
}
