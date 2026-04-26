<?php
/**
 * Checkout delivery addressing and pickup/delivery state helpers.
 *
 * @package Theme
 */

// SHOP IDS.
$vl_shops = array(
	// 'cab1caa9-da10-11eb-0a80-07410026c356',
	// '8cc659e5-4bfb-11ec-0a80-075000080e54',
	// 'b24e4c35-9609-11eb-0a80-0d0d008550c2',
	'7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93',
	'028e05a7-b4fa-11ee-0a80-1198000442be',
	'076fd75d-aa46-11f0-0a80-16ae00009467c',
	// '431d0f6f-577a-11ee-0a80-0f790012da73',
	// 'a99d6fdf-0970-11ed-0a80-0ed600075845',
);

$art_shops = array(
	'a99d6fdf-0970-11ed-0a80-0ed600075845',
);

$uss_shops = array(
	'9c9dfcc4-733f-11ec-0a80-0da1013a560d',
);

add_action( 'wp_ajax_update_user_address1', 'update_user_address1_callback' );
add_action( 'wp_ajax_nopriv_update_user_address1', 'update_user_address1_callback' ); // для неавторизованных пользователей

function update_user_address1_callback() {
	setcookie( 'delivery', 1, time() + ( 3600 * 24 * 7 ), '/' );
	$user_id = get_current_user_id();
	$address = $_POST['address'];

	$points = array();

	setcookie( 'wms_city', '', time() - 3600, '/' );

	SetCookie( 'data_of_samoviviz', $address['data_of'], time() + 60 * 60, '/' );
	SetCookie( 'data_check', $address['billing_comment_zakaz'], time() + 60 * 60, '/' );
	if ( is_user_logged_in() ) {
		update_user_meta( $user_id, 'delivery', 1 );
		foreach ( $address as $key => $value ) {
			update_user_meta( $user_id, $key, $value );
		}
		if ( $address['billing_samoviziv'] == 'Эгершельд, Верхнепортовая, 41в' ) {
			update_user_meta( $user_id, 'samovivoz', 'Эгершельд' );
			$points[] = '7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93';
		}
		if ( $address['billing_samoviziv'] == 'Реми-Сити (ул. Народный пр-т, 20)' ) {
			update_user_meta( $user_id, 'samovivoz', 'Реми-Сити' );
			$points[] = '7b24e4c35-9609-11eb-0a80-0d0d008550c2';
		}
		if ( $address['billing_samoviziv'] == 'ТЦ «Море», Гипермаркет (ул. Некрасовская, 49а)' ) {
			update_user_meta( $user_id, 'samovivoz', 'ГринМаркет ТЦ Море' );
			$points[] = 'cab1caa9-da10-11eb-0a80-07410026c356';
		}
		if ( $address['billing_samoviziv'] == 'ул. Тимирязева, 31 строение 1 (район Спутник)' ) {
			update_user_meta( $user_id, 'samovivoz', 'Космос' );
			$points[] = 'a99d6fdf-0970-11ed-0a80-0ed600075845';
		}
		if ( $address['billing_samoviziv'] == 'Океанский проспект, 108' ) {
			update_user_meta( $user_id, 'samovivoz', 'Океанский проспект 108' );
			$points[] = '076fd75d-aa46-11f0-0a80-16ae0009467c';
		}
		/*if ($address['billing_samoviziv'] == 'ТЦ Москва, 1-й этаж (ул. Суханова, 52)') {
			update_user_meta($user_id, 'samovivoz', 'Уссурийск');
			$points[] = '9c9dfcc4-733f-11ec-0a80-0da1013a560d';
		}*/
		if ( $address['billing_samoviziv'] == 'ТЦ Светланская (Светланская, 43)' ) {
			update_user_meta( $user_id, 'samovivoz', 'Светланская' );
			$points[] = '431d0f6f-577a-11ee-0a80-0f790012da73';
		}

		/*if ($address['billing_samoviziv'] == 'Находка, Проспект мира, 65/1') {
			update_user_meta($user_id, 'samovivoz', 'Находка');
			$points[] = '149a2219-9003-11ef-0a80-14a00002d2a5';
		}*/

		$user_data = array(
			'ID' => $user_id,
			// другие поля можно добавить по аналогии
		);

		// $result = wp_update_user($user_data);
	} else {
		global $file_prefix;
		wp_cache_clean_cache( $file_prefix, true );
		setcookie( 'billing_samoviziv', $address['billing_samoviziv'], time() + 3600 * 24 * 7, '/' );
		setcookie( 'time_to_dev', $address['time_type'], time() + 3600 * 24 * 7, '/' );
	}
	if ( $address['billing_samoviziv'] == 'Эгершельд, Верхнепортовая, 41в' ) {
		setcookie( 'market', 'Эгершельд', time() + 60 * 60 * 24 * 7, '/' );
		setcookie( 'key_market', '7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93', time() + 60 * 60 * 24 * 7, '/' );
		$points[] = '7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93';
	}
	if ( $address['billing_samoviziv'] == 'Океанский проспект, 108' ) {
		setcookie( 'market', 'Океанский проспект 108', time() + 60 * 60 * 24 * 7, '/' );
		setcookie( 'key_market', '076fd75d-aa46-11f0-0a80-16ae0009467c', time() + 60 * 60 * 24 * 7, '/' );
		$points[] = '076fd75d-aa46-11f0-0a80-16ae0009467c';
	}
	if ( $address['billing_samoviziv'] == 'Реми-Сити (ул. Народный пр-т, 20)' ) {
		setcookie( 'key_market', 'b24e4c35-9609-11eb-0a80-0d0d008550c2', time() + 60 * 60 * 24 * 7, '/' );
		setcookie( 'market', 'Реми-Сити', time() + 60 * 60 * 24 * 7, '/' );
		$points[] = '7b24e4c35-9609-11eb-0a80-0d0d008550c2';
	}
	if ( $address['billing_samoviziv'] == 'ТЦ «Море», Гипермаркет (ул. Некрасовская, 49а)' ) {
		setcookie( 'key_market', 'cab1caa9-da10-11eb-0a80-07410026c356', time() + 60 * 60 * 24 * 7, '/' );
		setcookie( 'market', 'ГринМаркет ТЦ Море', time() + 60 * 60 * 24 * 7, '/' );
		$points[] = 'cab1caa9-da10-11eb-0a80-07410026c356';
	}
	if ( $address['billing_samoviziv'] == 'ул. Тимирязева, 31 строение 1 (район Спутник)' ) {
		setcookie( 'market', 'Космос', time() + 60 * 60 * 24 * 7, '/' );
		setcookie( 'key_market', 'a99d6fdf-0970-11ed-0a80-0ed600075845', time() + 60 * 60 * 24 * 7, '/' );
		$points[] = 'a99d6fdf-0970-11ed-0a80-0ed600075845';
	}
	/*if ($address['billing_samoviziv'] == 'ТЦ Москва, 1-й этаж (ул. Суханова, 52)') {
		setcookie("market", 'Уссурийск', time()+60*60*24*7, '/');
		setcookie("key_market", '9c9dfcc4-733f-11ec-0a80-0da1013a560d', time()+60*60*24*7, '/');
		$points[] = '9c9dfcc4-733f-11ec-0a80-0da1013a560d';
	}*/
	if ( $address['billing_samoviziv'] == 'ТЦ Светланская (Светланская, 43)' ) {
		setcookie( 'market', 'Светланская', time() + 60 * 60 * 24 * 7, '/' );
		setcookie( 'key_market', '431d0f6f-577a-11ee-0a80-0f790012da73', time() + 60 * 60 * 24 * 7, '/' );
		$points[] = '431d0f6f-577a-11ee-0a80-0f790012da73';
	}

	/*if ($address['billing_samoviziv'] == 'Находка, Проспект мира, 65/1') {
		setcookie("market", 'Находка', time()+60*60*24*7, '/');
		setcookie("key_market", '149a2219-9003-11ef-0a80-14a00002d2a5', time()+60*60*24*7, '/');
		$points[] = '149a2219-9003-11ef-0a80-14a00002d2a5';
	}*/

	change_delivery_remove_items( $points );
}

add_filter(
	'woocommerce_checkout_fields',
	function( $fields ) {
		if ( isset( $fields['billing']['billing_samoviziv'] ) ) {
			$fields['billing']['billing_samoviziv']['custom_attributes']['readonly'] = 'readonly';
		}

		return $fields;
	}
);

add_filter( 'woocommerce_checkout_fields', 'ferma_make_billing_email_optional', 20 );
function ferma_make_billing_email_optional( $fields ) {
	if ( isset( $fields['billing']['billing_email'] ) ) {
		$fields['billing']['billing_email']['required'] = false;
	}
	return $fields;
}

function change_delivery_remove_items( $points ) {
	if ( ! is_array( $points ) || empty( $points ) ) {
		return true;
	}
	foreach ( WC()->cart->get_cart() as $cart_item ) {
		$product = $cart_item['data'];
		foreach ( $points as $point ) {
			$store = $product->get_meta( $point );
			if ( $store <= 0 ) {
				$cart_id       = WC()->cart->generate_cart_id( $cart_item['product_id'] );
				$cart_item_key = WC()->cart->find_product_in_cart( $cart_id );
				WC()->cart->set_quantity( $cart_item_key, 0 );
			}
		}
	}

	return true;
}

add_filter( 'woocommerce_checkout_get_value', 'update_checkout_user_address', 10, 2 );
function update_checkout_user_address( $value, $input ) {
	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		return $value;
	}
	switch ( $input ) {
		case 'billing_country':
			return get_user_meta( $user_id, 'billing_country', true );
		case 'billing_state':
			return get_user_meta( $user_id, 'billing_state', true );
		case 'billing_city':
			return get_user_meta( $user_id, 'billing_city', true );
		case 'billing_postcode':
			return get_user_meta( $user_id, 'billing_postcode', true );
		case 'billing_address_1':
			return get_user_meta( $user_id, 'billing_address_1', true );
		case 'billing_address_2':
			return get_user_meta( $user_id, 'billing_address_2', true );
		case 'shipping_country':
			return get_user_meta( $user_id, 'shipping_country', true );
		case 'shipping_state':
			return get_user_meta( $user_id, 'shipping_state', true );
		case 'shipping_city':
			return get_user_meta( $user_id, 'shipping_city', true );
		case 'shipping_postcode':
			return get_user_meta( $user_id, 'shipping_postcode', true );
		case 'shipping_address_1':
			return get_user_meta( $user_id, 'shipping_address_1', true );
		case 'shipping_address_2':
			return get_user_meta( $user_id, 'shipping_address_2', true );
		default:
			return $value;
	}
}

add_action( 'woocommerce_checkout_update_order_meta', 'save_custom_checkout_field' );
function save_custom_checkout_field( $order_id ) {
	if ( ! empty( $_POST['billing_type_delivery'] ) ) {
		update_post_meta( $order_id, 'billing_type_delivery', sanitize_text_field( $_POST['billing_type_delivery'] ) );
	}
}
