<?php
/**
 * Checkout validation rules for delivery checkout flow.
 *
 * @package Theme
 */

add_action( 'woocommerce_after_checkout_validation', 'ferma_validate_delivery_address', 10, 2 );
function ferma_validate_delivery_address( $fields, $errors ) {
	unset( $fields );
	$is_delivery = false;
	if ( function_exists( 'ferma_is_delivery' ) ) {
		$is_delivery = (bool) ferma_is_delivery();
	} elseif ( isset( $_COOKIE['delivery'] ) && (string) $_COOKIE['delivery'] === '0' ) {
		$is_delivery = true;
	}

	if ( ! $is_delivery ) {
		return;
	}

	$posted_delivery = isset( $_POST['billing_delivery'] ) ? trim( (string) wp_unslash( $_POST['billing_delivery'] ) ) : '';
	$posted_addr_1   = isset( $_POST['billing_address_1'] ) ? trim( (string) wp_unslash( $_POST['billing_address_1'] ) ) : '';
	$cookie_coords   = isset( $_COOKIE['coords'] ) ? trim( (string) wp_unslash( (string) $_COOKIE['coords'] ) ) : '';
	$cookie_bcoords  = isset( $_COOKIE['billing_coords'] ) ? trim( (string) wp_unslash( (string) $_COOKIE['billing_coords'] ) ) : '';

	$has_address = ( $posted_delivery !== '' || $posted_addr_1 !== '' || $cookie_coords !== '' || $cookie_bcoords !== '' );

	$posted_slot = isset( $_POST['billing_asdx1'] ) ? trim( (string) wp_unslash( $_POST['billing_asdx1'] ) ) : '';
	$cookie_day  = isset( $_COOKIE['delivery_day'] ) ? trim( (string) wp_unslash( (string) $_COOKIE['delivery_day'] ) ) : '';
	$cookie_time = isset( $_COOKIE['delivery_time'] ) ? trim( (string) wp_unslash( (string) $_COOKIE['delivery_time'] ) ) : '';
	$has_time    = ( $posted_slot !== '' || ( $cookie_day !== '' && $cookie_time !== '' ) );

	if ( function_exists( 'WC' ) && WC()->session ) {
		$ctx_day  = (string) WC()->session->get( 'ferma_ctx_delivery_day', '' );
		$ctx_time = (string) WC()->session->get( 'ferma_ctx_delivery_time', '' );
		if ( $ctx_day !== '' && $ctx_time !== '' ) {
			$has_time = true;
		}
	}

	if ( ! $has_address || ! $has_time ) {
		$errors->add( 'validation', 'Введите корректный адрес и выберите время для доставки' );
	}
}

add_action( 'woocommerce_after_checkout_validation', 'ferma_checkout_require_delivery_house_and_flat', 25, 2 );
function ferma_checkout_require_delivery_house_and_flat( $data, $errors ) {
	if ( ! function_exists( 'ferma_is_delivery' ) || ! ferma_is_delivery() ) {
		return;
	}

	$apt = isset( $_POST['billing_dev_1'] ) ? trim( wp_unslash( $_POST['billing_dev_1'] ) ) : '';
	if ( $apt === '' ) {
		$errors->add( 'billing_dev_1', __( 'Укажите номер квартиры или офиса.', 'woocommerce' ) );
	}

	$street = '';
	if ( ! empty( $_POST['billing_delivery'] ) ) {
		$street = (string) wp_unslash( $_POST['billing_delivery'] );
	} elseif ( ! empty( $_POST['billing_address_1'] ) ) {
		$street = (string) wp_unslash( $_POST['billing_address_1'] );
	}

	$street = trim( $street );
	if ( $street !== '' && ! preg_match( '/\d/u', $street ) ) {
		$errors->add( 'billing_delivery', __( 'В адресе доставки укажите номер дома (нужна хотя бы одна цифра).', 'woocommerce' ) );
	}
}
