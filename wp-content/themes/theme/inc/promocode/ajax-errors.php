<?php
/**
 * Q promocode AJAX error notice endpoint.
 *
 * @package Theme
 */

add_action( 'wp_ajax_nopriv_wc_print_errors', 'ferma_wc_print_errors' );
add_action( 'wp_ajax_wc_print_errors', 'ferma_wc_print_errors' );

function ferma_wc_print_errors() {
	wp_send_json_success(
		array(
			'html' => wc_print_notices( true ),
		)
	);
}
