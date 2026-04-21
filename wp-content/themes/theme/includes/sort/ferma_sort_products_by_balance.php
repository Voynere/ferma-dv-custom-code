<?php

if(!function_exists('ferma_sort_products_by_balance')) {
	add_filter( 'woocommerce_get_catalog_ordering_args', 'ferma_sort_products_by_balance' );
	function ferma_sort_products_by_balance( $args ) {
		
		$shops = ferma_get_current_shops();
		
		if(isset($shops[0])) {
			$args[ 'meta_key' ] = $shops[0];
			$args[ 'orderby' ] = array( 'meta_value' => 'DESC' );
		}
		
		return $args;
	}
}

if ( ! function_exists( 'ferma_catalog_sort_by_sales_photo_and_stock' ) ) {
	add_filter( 'posts_clauses', 'ferma_catalog_sort_by_sales_photo_and_stock', 120, 2 );

	/**
	 * Каталог: приоритет сортировки
	 * 1) В наличии на выбранном складе
	 * 2) С фото
	 * 3) По продажам (total_sales DESC)
	 * 4) Затем исходная сортировка WooCommerce
	 */
	function ferma_catalog_sort_by_sales_photo_and_stock( $clauses, $query ) {
		if ( is_admin() || ! $query instanceof WP_Query || ! $query->is_main_query() ) {
			return $clauses;
		}
		if ( ! function_exists( 'is_shop' ) || ! function_exists( 'ferma_get_current_shops' ) ) {
			return $clauses;
		}
		if ( ! ( is_shop() || is_product_category() || is_product_tag() ) ) {
			return $clauses;
		}
		if ( 'product_query' !== (string) $query->get( 'wc_query' ) && 'product' !== (string) $query->get( 'post_type' ) ) {
			return $clauses;
		}

		$shops = ferma_get_current_shops();
		$shop  = isset( $shops[0] ) ? (string) $shops[0] : '';
		if ( $shop === '' ) {
			return $clauses;
		}

		global $wpdb;

		$thumb_alias = 'ferma_thumb_pm';
		$thumb_post_alias = 'ferma_thumb_post';
		$thumb_file_alias = 'ferma_thumb_file_pm';
		$sales_alias = 'ferma_sales_pm';
		$stock_alias = 'ferma_stock_pm';

		$clauses['join'] .= $wpdb->prepare(
			" LEFT JOIN {$wpdb->postmeta} AS {$thumb_alias}
			    ON ({$wpdb->posts}.ID = {$thumb_alias}.post_id AND {$thumb_alias}.meta_key = %s)
			  LEFT JOIN {$wpdb->posts} AS {$thumb_post_alias}
			    ON ({$thumb_post_alias}.ID = {$thumb_alias}.meta_value)
			  LEFT JOIN {$wpdb->postmeta} AS {$thumb_file_alias}
			    ON ({$thumb_file_alias}.post_id = {$thumb_post_alias}.ID AND {$thumb_file_alias}.meta_key = %s)
			  LEFT JOIN {$wpdb->postmeta} AS {$sales_alias}
			    ON ({$wpdb->posts}.ID = {$sales_alias}.post_id AND {$sales_alias}.meta_key = %s)
			  LEFT JOIN {$wpdb->postmeta} AS {$stock_alias}
			    ON ({$wpdb->posts}.ID = {$stock_alias}.post_id AND {$stock_alias}.meta_key = %s) ",
			'_thumbnail_id',
			'_wp_attached_file',
			'total_sales',
			$shop
		);

		$stock_rank_sql = "CASE WHEN CAST(COALESCE(NULLIF({$stock_alias}.meta_value, ''), '0') AS DECIMAL(18,4)) > 0 THEN 0 ELSE 1 END";
		$photo_rank_sql = "CASE
			WHEN {$thumb_alias}.meta_value IS NULL OR {$thumb_alias}.meta_value = '' THEN 1
			WHEN {$thumb_file_alias}.meta_value LIKE '%2023/02/photo_2023-02-20_10-42-46%' THEN 1
			WHEN {$thumb_post_alias}.guid LIKE '%photo_2023-02-20_10-42-46%' THEN 1
			ELSE 0
		END";
		$sales_sql      = "CAST(COALESCE(NULLIF({$sales_alias}.meta_value, ''), '0') AS UNSIGNED)";

		$existing_orderby = isset( $clauses['orderby'] ) ? trim( (string) $clauses['orderby'] ) : '';
		if ( $existing_orderby === '' ) {
			$existing_orderby = "{$wpdb->posts}.menu_order ASC, {$wpdb->posts}.post_title ASC";
		}

		$clauses['orderby'] = "{$stock_rank_sql} ASC, {$photo_rank_sql} ASC, {$sales_sql} DESC, {$existing_orderby}";

		return $clauses;
	}
}