<?php
/**
 * Catalog cache and storefront cache-control logic.
 *
 * @package Theme
 */

add_action( 'init', 'force_no_cache_for_checkout' );

if ( ! function_exists( 'ferma_get_request_method' ) ) {
	/**
	 * Returns current HTTP method in uppercase.
	 *
	 * @return string
	 */
	function ferma_get_request_method() {
		if ( ! isset( $_SERVER['REQUEST_METHOD'] ) ) {
			return '';
		}

		return strtoupper( (string) $_SERVER['REQUEST_METHOD'] );
	}
}

function force_no_cache_for_checkout() {
	if ( function_exists( 'is_checkout' ) && is_checkout() && function_exists( 'is_order_received_page' ) && ! is_order_received_page() ) {
		// Headers to disable caching for checkout request/response cycle.
		header( 'Cache-Control: no-cache, no-store, must-revalidate' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );
		header( 'X-Accel-Expires: 0' );

		// Nginx FastCGI.
		header( 'X-Accel-Buffering: no' );

		// Cloudflare.
		header( 'CF-Cache-Status: BYPASS' );
	}
}

// Add unique query arg to checkout URL.
add_filter( 'woocommerce_get_checkout_url', 'add_nocache_to_checkout_url' );

function add_nocache_to_checkout_url( $url ) {
	// Add timestamp if URL already has parameters.
	if ( strpos( $url, '?' ) !== false ) {
		$url .= '&nocache=' . time();
	} else {
		$url .= '?nocache=' . time();
	}
	return $url;
}

/**
 * Remove legacy addon query params from storefront URLs.
 * We filter catalog by cookies; old links with ?wms-addon-store-filter-form must canonicalize.
 */
add_action( 'template_redirect', 'ferma_strip_legacy_store_filter_query', 1 );
function ferma_strip_legacy_store_filter_query() {
	if ( is_admin() ) {
		return;
	}
	$has_legacy_filter    = isset( $_GET['wms-addon-store-filter-form'] );
	$legacy_post_type     = isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : '';
	$has_legacy_post_type = 'page' === $legacy_post_type;
	if ( ! $has_legacy_filter && ! $has_legacy_post_type ) {
		return;
	}
	if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
		return;
	}
	$request_uri = (string) $_SERVER['REQUEST_URI'];
	$path        = strtok( $request_uri, '?' );
	if ( ! is_string( $path ) || $path === '' ) {
		$path = '/';
	}
	wp_safe_redirect( home_url( $path ), 301 );
	exit;
}

/**
 * Storefront pages: shop, categories and tags (excluding cart/checkout/account).
 */
if ( ! function_exists( 'ferma_is_catalog_cache_candidate' ) ) {
	function ferma_is_catalog_cache_candidate() {
		if ( ! function_exists( 'is_shop' ) ) {
			return false;
		}
		if (
			( function_exists( 'is_cart' ) && is_cart() )
			|| ( function_exists( 'is_checkout' ) && is_checkout() )
			|| ( function_exists( 'is_account_page' ) && is_account_page() )
		) {
			return false;
		}
		return is_front_page() || is_home()
			|| ( function_exists( 'is_shop' ) && is_shop() )
			|| ( function_exists( 'is_product_category' ) && is_product_category() )
			|| ( function_exists( 'is_product_tag' ) && is_product_tag() );
	}
}

/**
 * Anonymous GET without WC action params -> unified SSR + public cache headers for CDN/proxy.
 */
if ( ! function_exists( 'ferma_is_public_catalog_cache_request' ) ) {
	function ferma_is_public_catalog_cache_request() {
		if ( ! ferma_is_catalog_cache_candidate() ) {
			return false;
		}
		if ( is_user_logged_in() ) {
			return false;
		}
		if ( isset( $_GET['add-to-cart'] ) || isset( $_GET['added-to-cart'] ) || isset( $_GET['remove_item'] ) ) {
			return false;
		}
		if ( is_preview() || ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) ) {
			return false;
		}
		if ( wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return false;
		}
		if ( 'GET' !== ferma_get_request_method() ) {
			return false;
		}
		return true;
	}
}

add_filter( 'wp_get_nocache_headers', 'ferma_strip_nocache_headers_for_public_catalog', 99, 1 );
function ferma_strip_nocache_headers_for_public_catalog( $headers ) {
	if ( ferma_is_public_catalog_cache_request() ) {
		return array();
	}
	return $headers;
}

add_filter( 'woocommerce_enable_nocache_headers', 'ferma_wc_disable_nocache_headers_public_catalog', 99 );
function ferma_wc_disable_nocache_headers_public_catalog( $enabled ) {
	if ( ferma_is_public_catalog_cache_request() ) {
		return false;
	}
	return $enabled;
}

add_action( 'send_headers', 'ferma_send_public_cache_headers_catalog', 999 );
function ferma_send_public_cache_headers_catalog() {
	if ( ! ferma_is_public_catalog_cache_request() || headers_sent() ) {
		return;
	}
	// Remove headers that prevent CDN/proxy cache for guest storefront pages.
	header_remove( 'Pragma' );
	header_remove( 'Expires' );
	header_remove( 'Set-Cookie' );
	header( 'Vary: Accept-Encoding', true );
	header( 'Cache-Control: public, max-age=120, s-maxage=600, stale-while-revalidate=60', true );
}

if ( ! function_exists( 'ferma_is_guest_cacheable_page' ) ) {
	function ferma_is_guest_cacheable_page() {
		if ( is_user_logged_in() ) {
			return false;
		}
		if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return false;
		}
		if (
			( function_exists( 'is_cart' ) && is_cart() )
			|| ( function_exists( 'is_checkout' ) && is_checkout() )
			|| ( function_exists( 'is_account_page' ) && is_account_page() )
		) {
			return false;
		}
		if ( 'GET' !== ferma_get_request_method() ) {
			return false;
		}
		return true;
	}
}

add_action( 'send_headers', 'ferma_force_guest_public_cache_headers', 10000 );
function ferma_force_guest_public_cache_headers() {
	if ( ! ferma_is_guest_cacheable_page() || headers_sent() ) {
		return;
	}
	if ( function_exists( 'session_cache_limiter' ) ) {
		@session_cache_limiter( '' );
	}
	header_remove( 'Pragma' );
	header_remove( 'Expires' );
	header( 'Vary: Accept-Encoding', true );
	header( 'Cache-Control: public, max-age=120, s-maxage=600, stale-while-revalidate=60', true );
}
