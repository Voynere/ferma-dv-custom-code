<?php
/**
 * Remove X-Pingback headers from frontend responses.
 *
 * @package Theme
 */

add_filter( 'wp_headers', 'ferma_remove_pingback_header', 99 );
function ferma_remove_pingback_header( $headers ) {
	if ( isset( $headers['X-Pingback'] ) ) {
		unset( $headers['X-Pingback'] );
	}

	return $headers;
}

add_action( 'send_headers', 'ferma_remove_pingback_header_late', PHP_INT_MAX );
function ferma_remove_pingback_header_late() {
	if ( ! headers_sent() ) {
		header_remove( 'X-Pingback' );
	}
}
