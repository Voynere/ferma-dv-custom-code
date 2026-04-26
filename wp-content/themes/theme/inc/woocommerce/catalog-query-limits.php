<?php
/**
 * WooCommerce catalog query limits and pagination guards.
 *
 * @package Theme
 */

function ferma_catalog_products_per_page_default() {
	return (int) apply_filters( 'ferma_catalog_products_per_page', 12 );
}

add_filter( 'loop_shop_per_page', 'ferma_loop_shop_per_page_catalog', 20, 1 );
function ferma_loop_shop_per_page_catalog( $per_page ) {
	if ( ! function_exists( 'WC' ) ) {
		return $per_page;
	}
	$default = ferma_catalog_products_per_page_default();
	if ( $default < 1 ) {
		return $per_page;
	}
	return (int) apply_filters( 'ferma_catalog_loop_posts_per_page', $default, $per_page );
}

function ferma_catalog_apply_posts_per_page_limit( $q ) {
	$default = ferma_catalog_products_per_page_default();
	$per     = (int) apply_filters( 'ferma_catalog_loop_posts_per_page', $default, (int) $q->get( 'posts_per_page' ) );
	if ( $per < 1 ) {
		return;
	}
	$q->set( 'nopaging', false );
	$q->set( 'posts_per_page', $per );
}

add_action( 'woocommerce_product_query', 'ferma_catalog_woocommerce_product_query_limit', PHP_INT_MAX, 2 );
function ferma_catalog_woocommerce_product_query_limit( $q, $wc_query_instance = null ) {
	if ( is_admin() || ! apply_filters( 'ferma_catalog_force_posts_per_page_enabled', true ) ) {
		return;
	}
	if ( ! $q instanceof WP_Query || ! $q->is_main_query() ) {
		return;
	}
	ferma_catalog_apply_posts_per_page_limit( $q );
}

add_action( 'pre_get_posts', 'ferma_catalog_force_main_query_posts_per_page', 999999 );
function ferma_catalog_force_main_query_posts_per_page( $q ) {
	if ( is_admin() || ! $q instanceof WP_Query || ! $q->is_main_query() ) {
		return;
	}
	if ( ! apply_filters( 'ferma_catalog_force_posts_per_page_enabled', true ) ) {
		return;
	}
	if ( ! function_exists( 'WC' ) ) {
		return;
	}
	if ( ! ferma_catalog_is_main_product_listing_query( $q ) ) {
		return;
	}
	ferma_catalog_apply_posts_per_page_limit( $q );
}

add_action( 'pre_get_posts', 'ferma_catalog_force_main_query_posts_per_page_last', PHP_INT_MAX );
function ferma_catalog_force_main_query_posts_per_page_last( $q ) {
	ferma_catalog_force_main_query_posts_per_page( $q );
}

function ferma_catalog_is_main_product_listing_query( $q ) {
	if ( ! $q instanceof WP_Query ) {
		return false;
	}
	if ( 'product_query' === $q->get( 'wc_query' ) ) {
		return true;
	}

	$taxonomies = get_object_taxonomies( 'product', 'names' );
	if ( empty( $taxonomies ) ) {
		return false;
	}
	if ( $q->is_tax( $taxonomies ) ) {
		return true;
	}
	$qv_tax = $q->get( 'taxonomy' );
	if ( $qv_tax && taxonomy_exists( $qv_tax ) && in_array( $qv_tax, $taxonomies, true ) ) {
		return true;
	}

	$pt = $q->get( 'post_type' );
	if ( $pt !== 'product' && ! ( is_array( $pt ) && in_array( 'product', $pt, true ) ) ) {
		return false;
	}
	if ( $q->is_post_type_archive( 'product' ) ) {
		return true;
	}

	return false;
}

add_filter( 'post_limits', 'ferma_catalog_post_limits_sql', PHP_INT_MAX - 10, 2 );
function ferma_catalog_post_limits_sql( $limits, $query ) {
	if ( is_admin() || ! apply_filters( 'ferma_catalog_force_posts_per_page_enabled', true ) ) {
		return $limits;
	}
	if ( ! $query instanceof WP_Query || ! $query->is_main_query() ) {
		return $limits;
	}
	if ( ! function_exists( 'WC' ) ) {
		return $limits;
	}
	if ( ! ferma_catalog_is_main_product_listing_query( $query ) ) {
		return $limits;
	}

	$default = ferma_catalog_products_per_page_default();
	$per     = (int) apply_filters( 'ferma_catalog_loop_posts_per_page', $default, (int) $query->get( 'posts_per_page' ) );
	if ( $per < 1 ) {
		return $limits;
	}

	$paged  = max( 1, (int) $query->get( 'paged' ), (int) $query->get( 'page' ) );
	$offset = ( $paged - 1 ) * $per;

	return sprintf( 'LIMIT %d, %d', $offset, $per );
}

add_filter( 'posts_results', 'ferma_catalog_posts_results_cap', PHP_INT_MAX - 9, 2 );
function ferma_catalog_posts_results_cap( $posts, $query ) {
	if ( is_admin() || ! $query instanceof WP_Query || ! $query->is_main_query() ) {
		return $posts;
	}
	if ( ! apply_filters( 'ferma_catalog_force_posts_per_page_enabled', true ) || ! function_exists( 'WC' ) ) {
		return $posts;
	}
	if ( ! ferma_catalog_is_main_product_listing_query( $query ) ) {
		return $posts;
	}
	$per = (int) apply_filters( 'ferma_catalog_loop_posts_per_page', ferma_catalog_products_per_page_default(), (int) $query->get( 'posts_per_page' ) );
	if ( $per < 1 ) {
		return $posts;
	}
	$n = count( $posts );
	if ( $n <= $per ) {
		return $posts;
	}
	$paged  = max( 1, (int) $query->get( 'paged' ), (int) $query->get( 'page' ) );
	$offset = ( $paged - 1 ) * $per;
	return array_slice( $posts, $offset, $per );
}
