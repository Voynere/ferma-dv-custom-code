<?php
/**
 * Q promocode frontend assets.
 *
 * @package Theme
 */

function ferma_should_load_promocode_assets() {
	if ( function_exists( 'is_cart' ) && is_cart() ) {
		return true;
	}

	if ( function_exists( 'is_checkout' ) && is_checkout() ) {
		return true;
	}

	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		return true;
	}

	return false;
}

function ferma_enqueue_promocode_assets() {
	if ( ! ferma_should_load_promocode_assets() ) {
		return;
	}

	wp_enqueue_script(
		'q-promo-toast',
		get_template_directory_uri() . '/assets/js/q-promo-toast1.js',
		array( 'jquery' ),
		'1.0.0',
		true
	);

	wp_enqueue_script(
		'q-promocodes-js',
		get_template_directory_uri() . '/assets/js/promocodes11.js',
		array( 'jquery', 'q-promo-toast' ),
		filemtime( get_template_directory() . '/assets/js/promocodes11.js' ),
		true
	);

	wp_localize_script(
		'q-promocodes-js',
		'q_promo_vars',
		array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'q_promo_nonce' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'ferma_enqueue_promocode_assets' );
