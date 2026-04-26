<?php
/**
 * Catalog category query helpers and pre_get_posts adjustments.
 *
 * @package Theme
 */

/**
 * Кешируемый список ID всех рубрик product_cat (для тяжёлых tax_query на акционных категориях).
 *
 * @return int[]
 */
function ferma_get_all_product_cat_term_ids() {
	static $memo = null;

	if ( $memo !== null ) {
		return $memo;
	}

	$cache_key = 'ferma_all_product_cat_ids_v1';
	$ids       = get_transient( $cache_key );

	if ( false === $ids || ! is_array( $ids ) ) {
		$ids = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'fields'     => 'ids',
				'hide_empty' => false,
			)
		);
		if ( is_wp_error( $ids ) ) {
			$ids = array();
		}
		set_transient( $cache_key, $ids, HOUR_IN_SECONDS );
	}

	$memo = array_map( 'intval', (array) $ids );

	return $memo;
}

function pre_get_posts_product_actions( $q ) {
	if ( is_admin() || ! $q->is_main_query() ) {
		return;
	}

	if ( ! $q->is_tax( 'product_cat' ) ) {
		return;
	}

	$cat_obj = $q->get_queried_object();
	if ( ! is_a( $cat_obj, 'WP_Term' ) || ! isset( $cat_obj->term_id ) ) {
		return;
	}
	$term_id = (int) $cat_obj->term_id;
	if ( $term_id !== 355 && $term_id !== 2626 ) {
		return;
	}

	if ( $term_id === 355 ) {
		$discount     = (float) ferma_get_cached_option_field( 'priceint' );
		$ctx          = ferma_get_discount_runtime_context();
		$end_date     = (int) $ctx['price_end_ts'];
		$current_date = (int) $ctx['now_ts'];

		if ( $current_date > $end_date || $discount == 0 ) {
			$q->set( 'cat', '7815' );
		}

		$q->set(
			'tax_query',
			array(
				array(
					'taxonomy' => 'pa_akcziya',
					'field'    => 'slug',
					'terms'    => array( 1 ),
					'operator' => 'IN',
				),
			)
		);
	}

	if ( $term_id === 2626 ) {
		$ctx           = ferma_get_discount_runtime_context();
		$zp_date_start = (int) $ctx['zp_start_ts'];
		$zp_date_end   = (int) $ctx['zp_end_ts'];
		$current_date  = (int) $ctx['now_ts'];

		if ( $current_date > $zp_date_end || $current_date < $zp_date_start ) {
			$q->set( 'cat', '7815' );
		}

		$green_friday_products = get_green_friday_products();
		$good_ids = isset( $green_friday_products['good_ids'] ) ? array_map( 'intval', (array) $green_friday_products['good_ids'] ) : array();
		if ( empty( $good_ids ) ) {
			$good_ids = array( 0 );
		}
		$q->set( 'post__in', $good_ids );
	}
}
add_action( 'pre_get_posts', 'pre_get_posts_product_actions' );
