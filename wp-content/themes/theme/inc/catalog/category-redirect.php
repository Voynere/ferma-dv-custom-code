<?php
/**
 * Product category redirect helper.
 *
 * @package Theme
 */

function redirect_child_category() {
	if ( ! function_exists( 'is_product_category' ) || ! is_product_category() ) {
		return;
	}

	if ( ! isset( $_SERVER['REMOTE_ADDR'] ) || $_SERVER['REMOTE_ADDR'] !== '217.150.75.124' ) {
		return;
	}

	$category = get_queried_object();
	if ( ! $category || empty( $category->term_id ) ) {
		return;
	}

	$category_link = get_term_link( (int) $category->term_id, 'product_cat' );
	if ( is_wp_error( $category_link ) || empty( $category_link ) ) {
		return;
	}

	$current_url = strtok( "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?' );

	if ( $category_link !== $current_url && ! isset( $_SERVER['HTTP_REFERER'] ) ) {
		if ( ! empty( $_SERVER['QUERY_STRING'] ) ) {
			$category_link .= '?' . $_SERVER['QUERY_STRING'];
		}
		wp_redirect( $category_link, 301 );
		exit;
	}
}
add_action( 'template_redirect', 'redirect_child_category' );
