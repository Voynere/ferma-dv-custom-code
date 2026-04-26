<?php
/**
 * Q promocode session and AJAX session state API.
 *
 * @package Theme
 */

function q_get_active_promocode() {
	if ( ! function_exists( 'WC' ) || ! WC() || ! WC()->session ) {
		return null;
	}

	return WC()->session->get( 'q_active_promo' );
}

add_action( 'wp_ajax_check_active_promo', 'handle_check_active_promo' );
add_action( 'wp_ajax_nopriv_check_active_promo', 'handle_check_active_promo' );

function handle_check_active_promo() {
	check_ajax_referer( 'q_promo_nonce', 'nonce' );

	$active_promo = q_get_active_promocode();

	if ( $active_promo ) {
		wp_send_json_success(
			array(
				'active_promo' => true,
				'promo_code'   => $active_promo['code'],
			)
		);
	} else {
		wp_send_json_success(
			array(
				'active_promo' => false,
			)
		);
	}
}
