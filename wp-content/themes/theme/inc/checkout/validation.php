<?php
/**
 * Checkout validation rules for delivery checkout flow.
 *
 * @package Theme
 */

add_action( 'woocommerce_after_checkout_validation', 'ferma_validate_delivery_address', 10, 2 );
function ferma_validate_delivery_address( $fields, $errors ) {
	if ( isset( $_COOKIE['delivery'] ) && $_COOKIE['delivery'] == 0 && ( ! isset( $_COOKIE['coords'] ) || $_COOKIE['coords'] == '' ) ) {
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
