<?php
/**
 * Catalog stock visibility overrides.
 *
 * @package Theme
 */

//add_filter( 'pre_option_woocommerce_hide_out_of_stock_items', 'fix_kulichi_category' );
function fix_kulichi_category( $hide ) {
	if ( function_exists( 'is_product_category' ) && is_product_category( 'kulichi' ) ) {
		$hide = 'no';
	}

	return $hide;
}
