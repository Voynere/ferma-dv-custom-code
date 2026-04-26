<?php
/**
 * Checkout delivery session/cookie sync and shopping gate helpers.
 *
 * @package Theme
 */

add_action( 'wp_ajax_update_user_address', 'update_user_address_callback' );
add_action( 'wp_ajax_nopriv_update_user_address', 'update_user_address_callback' ); // для неавторизованных пользователей

function update_user_address_callback() {
	setcookie( 'delivery', 0, time() + 3600 * 24 * 7, '/' );
	WC()->session->set( 'custom_cache', false );
	setcookie( 'billing_delivery', 0, time() - 1, '/' );
	setcookie( 'billing_comment', 0, time() - 1, '/' );
	$user_id = get_current_user_id();
	$address = $_POST['address'];
	setcookie( 'data_check', $address['billing_comment_zakaz'], time() + 3600 * 24 * 7, '/' );
	setcookie( 'time', $address['time'], time() + 3600 * 24 * 7, '/' );
	setcookie( 'coords', $address['coords'], time() + 3600 * 24 * 7, '/' );
	setcookie( 'time_type', $address['time_type'], time() + 3600 * 24 * 7, '/' );
	setcookie( 'delivery_time', $address['delivery_time'], time() + 3600 * 24 * 7, '/' );
	setcookie( 'delivery_day', $address['delivery_day'], time() + 3600 * 24 * 7, '/' );
	setcookie( 'billing_coords', $address['coords'], time() + 3600 * 24 * 7, '/' );

	$points = array();

	$points = ferma_get_shops_by_coords( $address['coords'] );

	setcookie( 'wms_city', base64_encode( serialize( $points ) ), time() + 3600 * 24 * 7, '/' );

	if ( is_user_logged_in() ) {
		update_user_meta( $user_id, 'delivery', 0 );
		update_user_meta( $user_id, 'time_type', 0 );
		foreach ( $address as $key => $value ) {
			update_user_meta( $user_id, $key, $value );
		}
		$user_data = array(
			'ID'        => $user_id,
			'time_type' => $address['time_type'],
		);

		wp_update_user( $user_data );
	} else {
		global $file_prefix;
		wp_cache_clean_cache( $file_prefix, true );
		setcookie( 'delivery', 0, time() + 3600 * 24 * 7, '/' );

		// Сохраняем адрес в cookie
		setcookie( 'billing_delivery', $address['billing_delivery'], time() + 3600 * 24 * 7, '/' );
		setcookie( 'billing_comment', $address['billing_comment'], time() + 3600 * 24 * 7, '/' );
		setcookie( 'coords', $address['coords'], time() + 3600 * 24 * 7, '/' );
		setcookie( 'billing_coords', $address['coords'], time() + 3600 * 24 * 7, '/' );
	}

	change_delivery_remove_items( $points );

	// Logged-in: keep cookies in sync with user meta (header/JS and some caches read cookies).
	if ( is_user_logged_in() && is_array( $address ) && ! empty( $address['billing_delivery'] ) ) {
		$raw_addr = (string) $address['billing_delivery'];
		$raw_addr = str_replace( array( "\0", "\r" ), '', $raw_addr );
		$raw_addr = trim( $raw_addr );
		if ( $raw_addr !== '' ) {
			setcookie( 'delivery', '0', time() + 3600 * 24 * 7, '/' );
			setcookie( 'billing_delivery', $raw_addr, time() + 3600 * 24 * 7, '/' );
		}
	}
}

if ( ! function_exists( 'ferma_get_answer_user_shopping_flag' ) ) {
	/**
	 * Hidden #answer_user: 1 = allow add-to-cart; 0 = gate and open delivery modal on hijacked buttons.
	 */
	function ferma_get_answer_user_shopping_flag() {
		if ( is_user_logged_in() ) {
			$uid  = (int) get_current_user_id();
			$mode = get_user_meta( $uid, 'delivery', true );
			if ( $mode !== '' && $mode !== null && $mode !== false ) {
				return 1;
			}
			$bd = get_user_meta( $uid, 'billing_delivery', true );
			if ( is_string( $bd ) && $bd !== '' ) {
				return 1;
			}
			$ba1 = get_user_meta( $uid, 'billing_address_1', true );
			if ( is_string( $ba1 ) && $ba1 !== '' ) {
				return 1;
			}
			return 0;
		}
		if ( ! isset( $_COOKIE['delivery'] ) ) {
			return 0;
		}
		$d = (string) wp_unslash( (string) $_COOKIE['delivery'] );
		if ( $d === '1' ) {
			return 1;
		}
		$has_coords  = ( isset( $_COOKIE['coords'] ) && (string) $_COOKIE['coords'] !== '' );
		$has_sam     = ( isset( $_COOKIE['billing_samoviziv'] ) && (string) wp_unslash( (string) $_COOKIE['billing_samoviziv'] ) !== '' );
		$has_billdel = ( isset( $_COOKIE['billing_delivery'] ) && (string) wp_unslash( (string) $_COOKIE['billing_delivery'] ) !== '' && $d === '0' );
		if ( $d === '0' && ( $has_coords || $has_sam || $has_billdel ) ) {
			return 1;
		}
		return 0;
	}
}

add_action( 'woocommerce_checkout_update_order_meta', 'ferma_checkout_sync_delivery_usermeta_from_checkout', 20, 1 );
function ferma_checkout_sync_delivery_usermeta_from_checkout( $order_id ) {
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
	unset( $order_id );
	if ( ! is_user_logged_in() || empty( $_POST ) || ! is_array( $_POST ) ) {
		return;
	}
	if ( empty( $_POST['billing_delivery'] ) || ! is_string( $_POST['billing_delivery'] ) ) {
		return;
	}
	$raw = str_replace( array( "\0", "\r" ), '', (string) wp_unslash( $_POST['billing_delivery'] ) );
	$raw = trim( $raw );
	if ( $raw === '' ) {
		return;
	}
	$uid = (int) get_current_user_id();
	if ( $uid <= 0 ) {
		return;
	}
	update_user_meta( $uid, 'delivery', '0' );
	update_user_meta( $uid, 'billing_delivery', $raw );
	setcookie( 'delivery', '0', time() + 3600 * 24 * 7, '/' );
	setcookie( 'billing_delivery', $raw, time() + 3600 * 24 * 7, '/' );
}
