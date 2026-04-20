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