<?php
/**
 * Checkout bonus cookie state management.
 *
 * @package Theme
 */

add_action( 'woocommerce_cart_calculate_fees', 'ferma_checkout_clear_bonus_cookies_when_disabled', 1 );
function ferma_checkout_clear_bonus_cookies_when_disabled() {
	if ( ! function_exists( 'ferma_checkout_bonuses_allowed' ) || ferma_checkout_bonuses_allowed() ) {
		return;
	}

	if ( isset( $_COOKIE['balik'] ) ) {
		setcookie( 'balik', '', time() - YEAR_IN_SECONDS, '/' );
		unset( $_COOKIE['balik'] );
	}

	if ( isset( $_COOKIE['vibo1r'] ) ) {
		setcookie( 'vibo1r', '', time() - YEAR_IN_SECONDS, '/' );
		unset( $_COOKIE['vibo1r'] );
	}
}
