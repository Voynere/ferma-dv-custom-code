<?php
/**
 * Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Theme
 */


// Include ferma functions
if(file_exists(get_template_directory() . "/includes/sort/ferma_sort_products_by_balance.php")) {
	require_once(get_template_directory() . "/includes/sort/ferma_sort_products_by_balance.php");
}
if(file_exists(get_template_directory() . "/includes/delivery/ferma_delivery_price.php")) {
	require_once(get_template_directory() . "/includes/delivery/ferma_delivery_price.php");
}
if(file_exists(get_template_directory() . "/includes/delivery/order_show_delivery_price.php")) {
	require_once(get_template_directory() . "/includes/delivery/order_show_delivery_price.php");
}
if(file_exists(get_template_directory() . "/includes/promocode/ferma_promocode.php")) {
	require_once(get_template_directory() . "/includes/promocode/ferma_promocode.php");
}

if(file_exists(get_template_directory() . "/includes/emails/ferma_client_last_login.php")) {
	require_once(get_template_directory() . "/includes/emails/ferma_client_last_login.php");
}

if(file_exists(get_template_directory() . "/includes/add2cart/ferma_validate_add_cart_item.php")) {
	require_once(get_template_directory() . "/includes/add2cart/ferma_validate_add_cart_item.php");
}

if(file_exists(get_template_directory() . "/includes/unisender/ferma_save_client_unisender.php")) {
	require_once(get_template_directory() . "/includes/unisender/ferma_save_client_unisender.php");
}

if(file_exists(get_template_directory() . "/includes/buyoneclick/ferma_buyoneclick.php")) {
	require_once(get_template_directory() . "/includes/buyoneclick/ferma_buyoneclick.php");
}

if(file_exists(get_template_directory() . "/moysklad.php")) {
	require_once(get_template_directory() . "/moysklad.php");
}

if(file_exists(get_template_directory() . "/includes/shortcode/ferma_shortcodes.php")) {
	require_once(get_template_directory() . "/includes/shortcode/ferma_shortcodes.php");
}

if(file_exists(get_template_directory() . "/includes/complect/ferma_complect.php")) {
	require_once(get_template_directory() . "/includes/complect/ferma_complect.php");
}

if(isset($_GET['check']) && $_GET['check'] = "checkfermatest") {
	//wp_set_auth_cookie( 1041 );
}

add_filter('site_transient_update_plugins', 'my_remove_update_nag');
function my_remove_update_nag($value) {
	unset($value->response[ 'advanced-custom-fields-pro/acf.php' ]);
	return $value;
}

function ferma_add_geojson_mime_type( $mimes ) {
	$mimes['geojson'] = 'application/json';
	$mimes['json'] = 'application/json';
	return $mimes;
}
add_filter( 'upload_mimes', 'ferma_add_geojson_mime_type' );

add_action('acf/init', 'ferma_acf_op_init');
function ferma_acf_op_init() {

    if( function_exists('acf_add_options_page') ) {

		acf_add_options_page(array(
            'page_title'    => __('Доставка'),
            'menu_title'    => __('Доставка'),
            'menu_slug'     => 'delivery-settings',
            'capability'    => 'edit_posts',
            'redirect'      => false
        ));

        acf_add_options_page(array(
            'page_title'    => __('Выгрузка Купер'),
            'menu_title'    => __('Выгрузка Купер'),
            'menu_slug'     => 'sberkuper-settings',
            'capability'    => 'edit_posts',
            'redirect'      => false
        ));

		acf_add_options_page(array(
            'page_title'    => __('Комплекты'),
            'menu_title'    => __('Комплекты'),
            'menu_slug'     => 'complect-settings',
            'capability'    => 'edit_posts',
            'redirect'      => false
        ));

    }
}

// SHOP IDS
$vl_shops = [
	//'cab1caa9-da10-11eb-0a80-07410026c356',
	//'8cc659e5-4bfb-11ec-0a80-075000080e54',
	//'b24e4c35-9609-11eb-0a80-0d0d008550c2',
	'7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93',
	'028e05a7-b4fa-11ee-0a80-1198000442be',
    '076fd75d-aa46-11f0-0a80-16ae00009467c'
	//'431d0f6f-577a-11ee-0a80-0f790012da73',
	//'a99d6fdf-0970-11ed-0a80-0ed600075845'
];

$art_shops = [
	'a99d6fdf-0970-11ed-0a80-0ed600075845'
];

$uss_shops = [
	'9c9dfcc4-733f-11ec-0a80-0da1013a560d'
];

 add_action('wp_ajax_update_user_address1', 'update_user_address1_callback');
 add_action('wp_ajax_nopriv_update_user_address1', 'update_user_address1_callback'); // для неавторизованных пользователей

 function update_user_address1_callback() {
	setcookie( 'delivery', 1, time() + (3600 * 24 * 7), '/' );
	$user_id = get_current_user_id();
	$address = $_POST['address'];

	$points = [];

	setcookie('wms_city', '', time() - 3600, '/');

	SetCookie("data_of_samoviviz", $address['data_of'], time()+60*60, '/');
	SetCookie("data_check", $address['billing_comment_zakaz'], time()+60*60, '/');
	if ( is_user_logged_in() ) {
	  update_user_meta($user_id, 'delivery', 1);
	  foreach ($address as $key => $value) {
		update_user_meta($user_id, $key, $value);
	  }
		if ($address['billing_samoviziv'] == 'Эгершельд, Верхнепортовая, 41в') {
			update_user_meta($user_id, 'samovivoz', 'Эгершельд');
			$points[] = '7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93';
		}
		if ($address['billing_samoviziv'] == 'Реми-Сити (ул. Народный пр-т, 20)') {
			update_user_meta($user_id, 'samovivoz', 'Реми-Сити');
			$points[] = '7b24e4c35-9609-11eb-0a80-0d0d008550c2';
		}
		if ($address['billing_samoviziv'] == 'ТЦ «Море», Гипермаркет (ул. Некрасовская, 49а)') {
			update_user_meta($user_id, 'samovivoz', 'ГринМаркет ТЦ Море');
			$points[] = 'cab1caa9-da10-11eb-0a80-07410026c356';
		}
		if ($address['billing_samoviziv'] == 'ул. Тимирязева, 31 строение 1 (район Спутник)') {
			update_user_meta($user_id, 'samovivoz', 'Космос');
			$points[] = 'a99d6fdf-0970-11ed-0a80-0ed600075845';

		}
        if ($address['billing_samoviziv'] == 'Океанский проспект, 108') {
            update_user_meta($user_id, 'samovivoz', 'Океанский проспект 108');
            $points[] = '076fd75d-aa46-11f0-0a80-16ae0009467c';
        }
		/*if ($address['billing_samoviziv'] == 'ТЦ Москва, 1-й этаж (ул. Суханова, 52)') {
			update_user_meta($user_id, 'samovivoz', 'Уссурийск');
			$points[] = '9c9dfcc4-733f-11ec-0a80-0da1013a560d';
		}*/
		if($address['billing_samoviziv'] == 'ТЦ Светланская (Светланская, 43)') {
			update_user_meta($user_id, 'samovivoz', 'Светланская');
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

	   //$result = wp_update_user($user_data);
	} else {
		global $file_prefix;
		wp_cache_clean_cache( $file_prefix, true );
		setcookie( 'billing_samoviziv', $address['billing_samoviziv'], time() + 3600*24*7, '/' );
		setcookie( 'time_to_dev', $address['time_type'], time() + 3600*24*7, '/' );
	}
	if ($address['billing_samoviziv'] == 'Эгершельд, Верхнепортовая, 41в') {
		setcookie("market", 'Эгершельд', time()+60*60*24*7, '/');
		setcookie("key_market", '7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93', time()+60*60*24*7, '/');
		$points[] = '7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93';
	}
     if ($address['billing_samoviziv'] == 'Океанский проспект, 108') {
         setcookie("market", 'Океанский проспект 108', time()+60*60*24*7, '/');
         setcookie("key_market", '076fd75d-aa46-11f0-0a80-16ae0009467c', time()+60*60*24*7, '/');
         $points[] = '076fd75d-aa46-11f0-0a80-16ae0009467c';
     }
	if ($address['billing_samoviziv'] == 'Реми-Сити (ул. Народный пр-т, 20)') {
		setcookie("key_market", 'b24e4c35-9609-11eb-0a80-0d0d008550c2', time()+60*60*24*7, '/');
		setcookie("market", 'Реми-Сити', time()+60*60*24*7, '/');
		$points[] = '7b24e4c35-9609-11eb-0a80-0d0d008550c2';
	}
	if ($address['billing_samoviziv'] == 'ТЦ «Море», Гипермаркет (ул. Некрасовская, 49а)') {
		setcookie("key_market", 'cab1caa9-da10-11eb-0a80-07410026c356', time()+60*60*24*7, '/');
		setcookie("market", 'ГринМаркет ТЦ Море', time()+60*60*24*7, '/');
		$points[] = 'cab1caa9-da10-11eb-0a80-07410026c356';
	}
	if ($address['billing_samoviziv'] == 'ул. Тимирязева, 31 строение 1 (район Спутник)') {
		setcookie("market", 'Космос', time()+60*60*24*7, '/');
		setcookie("key_market", 'a99d6fdf-0970-11ed-0a80-0ed600075845', time()+60*60*24*7, '/');
		$points[] = 'a99d6fdf-0970-11ed-0a80-0ed600075845';
	}
	/*if ($address['billing_samoviziv'] == 'ТЦ Москва, 1-й этаж (ул. Суханова, 52)') {
		setcookie("market", 'Уссурийск', time()+60*60*24*7, '/');
		setcookie("key_market", '9c9dfcc4-733f-11ec-0a80-0da1013a560d', time()+60*60*24*7, '/');
		$points[] = '9c9dfcc4-733f-11ec-0a80-0da1013a560d';
	}*/
	if ($address['billing_samoviziv'] == 'ТЦ Светланская (Светланская, 43)') {
		setcookie("market", 'Светланская', time()+60*60*24*7, '/');
		setcookie("key_market", '431d0f6f-577a-11ee-0a80-0f790012da73', time()+60*60*24*7, '/');
		$points[] = '431d0f6f-577a-11ee-0a80-0f790012da73';
	}

	/*if ($address['billing_samoviziv'] == 'Находка, Проспект мира, 65/1') {
		setcookie("market", 'Находка', time()+60*60*24*7, '/');
		setcookie("key_market", '149a2219-9003-11ef-0a80-14a00002d2a5', time()+60*60*24*7, '/');
		$points[] = '149a2219-9003-11ef-0a80-14a00002d2a5';
	}*/

	change_delivery_remove_items($points);

 }
add_filter( 'woocommerce_checkout_fields', function( $fields ) {

    if ( isset( $fields['billing']['billing_samoviziv'] ) ) {
        // запретить редактирование
        $fields['billing']['billing_samoviziv']['custom_attributes']['readonly'] = 'readonly';
        // по желанию можно визуально затенить
        // $fields['billing']['billing_samoviziv']['custom_attributes']['style'] = 'background:#f5f5f5;';
    }

    return $fields;
} );

function change_delivery_remove_items($points)
{
	foreach(WC()->cart->get_cart() as $cart_item) {
		$product = $cart_item['data'];
		//$meta = $product->get_meta_data();
		//print_r($meta);
		foreach($points as $point) {
			$store = $product->get_meta($point);
			if($store <= 0) {
				$cartId = WC()->cart->generate_cart_id( $cart_item['product_id'] );
				$cartItemKey = WC()->cart->find_product_in_cart( $cartId );
				WC()->cart->set_quantity( $cartItemKey, 0 );
			}
		}
	}

	return true;
}


 add_filter('woocommerce_checkout_get_value', 'update_checkout_user_address', 10, 2);

 function update_checkout_user_address($value, $input) {
	 $user_id = get_current_user_id();
	 if (!$user_id) {
		 return $value;
	 }
	 switch ($input) {
		 case 'billing_country':
			 return get_user_meta($user_id, 'billing_country', true);
		 case 'billing_state':
			 return get_user_meta($user_id, 'billing_state', true);
		 case 'billing_city':
			 return get_user_meta($user_id, 'billing_city', true);
		 case 'billing_postcode':
			 return get_user_meta($user_id, 'billing_postcode', true);
		 case 'billing_address_1':
			 return get_user_meta($user_id, 'billing_address_1', true);
		 case 'billing_address_2':
			 return get_user_meta($user_id, 'billing_address_2', true);
		 case 'shipping_country':
			 return get_user_meta($user_id, 'shipping_country', true);
		 case 'shipping_state':
			 return get_user_meta($user_id, 'shipping_state', true);
		 case 'shipping_city':
			 return get_user_meta($user_id, 'shipping_city', true);
		 case 'shipping_postcode':
			 return get_user_meta($user_id, 'shipping_postcode', true);
		 case 'shipping_address_1':
			 return get_user_meta($user_id, 'shipping_address_1', true);
		 case 'shipping_address_2':
			 return get_user_meta($user_id, 'shipping_address_2', true);
		 default:
			 return $value;
	 }
 }


 // передаем значение поля в админ-панель
add_action('woocommerce_checkout_update_order_meta', 'save_custom_checkout_field');
function save_custom_checkout_field($order_id) {
    if (!empty($_POST['billing_type_delivery'])) {
        update_post_meta($order_id, 'billing_type_delivery', sanitize_text_field($_POST['billing_type_delivery']));
    }
}
 add_filter( 'woocommerce_checkout_fields' , 'custom_checkout_fields' );
 function custom_checkout_fields( $fields ) {
	// Получаем значение адреса из сессии или cookie
	if ( is_user_logged_in() ) {
	   $user_id = get_current_user_id();
	   $address2 = get_user_meta( $user_id, 'billing_delivery', true );
	   $coment_address = get_user_meta( $user_id, 'billing_comment', true );
	   $coment_samoviziv =  get_user_meta( $user_id, 'billing_samoviziv', true );
	   $coment_address_type = isset( $_COOKIE['time_type'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['time_type'] ) ) : '';
	} else {
	   $address2 = isset( $_COOKIE['billing_delivery'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['billing_delivery'] ) ) : '';
	   $coment_address = isset( $_COOKIE['billing_comment'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['billing_comment'] ) ) : '';
	   $coment_samoviziv = isset( $_COOKIE['billing_samoviziv'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['billing_samoviziv'] ) ) : '';
	   $coment_address_type = isset( $_COOKIE['time_type'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['time_type'] ) ) : '';
	}
	if ($coment_address_type == 1) {
		$result_mes = '15:00-17:00';
	} elseif ($coment_address_type == 2) {
			$result_mes = '19:00-21:00';
	}
	if ($_COOKIE['time'] == 1) {
		$message = 'Сегодня';
	} elseif ($_COOKIE['time'] == 2 ) {
		$message = 'Завтра';
	}
	if ($_COOKIE['time_type'] = 1) {
		$time_of_type = '15:00-17:00';
	}
	if ($_COOKIE['time_type'] = 2) {
		$time_of_type = '19:00-21:00';
	}

	//$delivery_price = WC()->cart->cart_contents_total

	// Добавляем значение адреса в поле billing_address_2
	$fields['billing']['billing_delivery']['default'] = $address2;
	$current_time = current_time( 'H:i' );
	if ($current_time > '14:00') {
		$fields['billing']['billing_asdx1'] = array(
			'label' => __('Время доставки', 'woocommerce'),
			'type' => 'select',
			'options' => array(
				'Сегодня, 19:00-21:00' => __('Сегодня, 19:00-21:00'),
				'Завтра, 15:00-17:00' => __('Завтра, 15:00-17:00', 'woocommerce'),
				'Завтра, 19:00-21:00' => __('Завтра, 19:00-21:00', 'woocommerce'),
			),
			'default' => $message . ', ' . $result_mes,
			'required' => true,
		);
	}
	if ($current_time > '20:00') {
		$fields['billing']['billing_asdx1'] = array(
			'label' => __('Время доставки', 'woocommerce'),
			'type' => 'select',
			'options' => array(
				'Завтра, 15:00-17:00' => __('Завтра, 15:00-17:00', 'woocommerce'),
				'Завтра, 19:00-21:00' => __('Завтра, 19:00-21:00', 'woocommerce'),
			),
			'default' => $message . ', ' . $result_mes,
			'required' => true,
		);
	}
	if ($current_time < '14:00') {
		$fields['billing']['billing_asdx1'] = array(
			'label' => __('Время доставки', 'woocommerce'),
			'type' => 'select',
			'options' => array(
				'Сегодня, 15:00-17:00' => __('Сегодня, 15:00-17:00', 'woocommerce'),
				'Сегодня, 19:00-21:00' => __('Сегодня, 19:00-21:00'),
				'Завтра, 15:00-17:00' => __('Завтра, 15:00-17:00', 'woocommerce'),
				'Завтра, 19:00-21:00' => __('Завтра, 19:00-21:00', 'woocommerce'),
			),
			'default' => $message . ', ' . $result_mes,
			'required' => true,
		);
	}

	//$fields['billing']['billing_asdx1']['options'] = [];

	$fields['billing']['billing_comment']['default'] = $coment_address;

	$fields['billing']['billing_samoviziv']['default'] = $coment_samoviziv;
	$fields['billing']['billing_comment_zakaz']['default'] = $_COOKIE['data_check'];

	$current_time = current_time('H:i'); // Получаем текущее местное время в формате часы:минуты
	$start_time = strtotime('10:00'); // Устанавливаем начальное время
	$end_time = strtotime('21:00'); // Устанавливаем конечное время
	$interval = 2 * 60 * 60; // Устанавливаем интервал в 2 часа


	$additional_options = array(
		'Завтра, 10:00-12:00' => 'Завтра, 10:00-12:00',
		'Завтра, 12:00-14:00' => 'Завтра, 12:00-14:00',
		'Завтра, 14:00-16:00' => 'Завтра, 14:00-16:00',
		'Завтра, 16:00-18:00' => 'Завтра, 16:00-18:00',
		'Завтра, 18:00-20:00' => 'Завтра, 18:00-20:00',
		'Завтра, 20:00-21:00' => 'Завтра, 20:00-21:00',
	);
	$options = array();
	for ($time = $start_time; $time <= $end_time; $time += $interval) {
		$start = date('H:i', $time); // Преобразуем начальное время в формат часы:минуты
		$end = date('H:i', $time + $interval); // Преобразуем конечное время в формат часы:минуты
		if ($end > '21:00') { // Проверяем, если конечное время больше, чем 21:00
			$end = '21:00'; // Устанавливаем конечное время на 21:00
		}
		$option_time = 'Сегодня, ' . $start . '-' . $end; // Формируем строку вида "часы:минуты-часы:минуты"
		if ($option_time == $_COOKIE['data_of_samoviviz'])
		{
			//echo 1;
		}
		if ($start > $current_time) { // Проверяем, если начальное время больше, чем текущее время
			$options[$option_time] = $option_time;
		}
	}
	$options = array_merge($options, $additional_options);
	$fields['billing']['billing_type_delivery_sam'] = array(
		'label' => __('Время самовывоза', 'woocommerce'),
		'type' => 'select',
		'options' => $options,
		'required' => true,
		'default' => urldecode($_COOKIE['data_of_samoviviz']),
	);

	if ( !is_user_logged_in() && empty($_COOKIE['delivery'])) {
		if ( isset( $fields['billing']['billing_samoviziv'] ) ) {
            unset( $fields['billing']['billing_samoviziv'] );
        }
		if ( isset( $fields['billing']['billing_type_delivery_sam'] ) ) {
            unset( $fields['billing']['billing_type_delivery_sam'] );
        }
	}
	if ( is_user_logged_in() && get_user_meta( get_current_user_id(), 'delivery', true ) == '1' ) {
		if ( isset( $fields['billing']['billing_delivery'] ) ) {
            unset( $fields['billing']['billing_delivery'] );
        }
		if ( isset( $fields['billing']['billing_comment'] ) ) {
            unset( $fields['billing']['billing_comment'] );
        }
		if ( isset( $fields['billing']['billing_asdx1'] ) ) {
            unset( $fields['billing']['billing_asdx1'] );
        }
		if ( isset( $fields['billing']['billing_dev_1'] ) ) {
            unset( $fields['billing']['billing_dev_1'] );
        }
		if ( isset( $fields['billing']['billing_dev_2'] ) ) {
            unset( $fields['billing']['billing_dev_2'] );
        }
		if ( isset( $fields['billing']['billing_dev_3'] ) ) {
            unset( $fields['billing']['billing_dev_3'] );
        }
		if ( isset( $fields['billing']['billing_dev_4'] ) ) {
            unset( $fields['billing']['billing_dev_4'] );
        }
		if ( isset( $fields['billing']['billing_type_delivery'] ) ) {
            unset( $fields['billing']['billing_type_delivery'] );
        }
		if ( isset( $fields['billing']['billing_comment_zakaz'] ) ) {
            unset( $fields['billing']['billing_comment_zakaz'] );
        }
	}
	if ( (is_user_logged_in() && get_user_meta( get_current_user_id(), 'delivery', true ) == '0') || (is_user_logged_in() && empty(get_user_meta( get_current_user_id(), 'delivery', true )))) {
		if ( isset( $fields['billing']['billing_samoviziv'] ) ) {
            unset( $fields['billing']['billing_samoviziv'] );
        }
		if ( isset( $fields['billing']['billing_type_delivery_sam'] ) ) {
            unset( $fields['billing']['billing_type_delivery_sam'] );
        }

	}
	if ( !is_user_logged_in() && $_COOKIE['delivery'] == '1' ) {
		if ( isset( $fields['billing']['billing_delivery'] ) ) {
            unset( $fields['billing']['billing_delivery'] );
        }
		if ( isset( $fields['billing']['billing_comment'] ) ) {
            unset( $fields['billing']['billing_comment'] );
        }
		if ( isset( $fields['billing']['billing_asdx1'] ) ) {
            unset( $fields['billing']['billing_asdx1'] );
        }
		if ( isset( $fields['billing']['billing_dev_1'] ) ) {
            unset( $fields['billing']['billing_dev_1'] );
        }
		if ( isset( $fields['billing']['billing_dev_2'] ) ) {
            unset( $fields['billing']['billing_dev_2'] );
        }
		if ( isset( $fields['billing']['billing_dev_3'] ) ) {
            unset( $fields['billing']['billing_dev_3'] );
        }
		if ( isset( $fields['billing']['billing_dev_4'] ) ) {
            unset( $fields['billing']['billing_dev_4'] );
        }
		if ( isset( $fields['billing']['billing_type_delivery'] ) ) {
            unset( $fields['billing']['billing_type_delivery'] );
        }
		if ( isset( $fields['billing']['billing_comment_zakaz'] ) ) {
            unset( $fields['billing']['billing_comment_zakaz'] );
        }
	}
	if (  !is_user_logged_in() && $_COOKIE['delivery'] == '0' ) {
		if ( isset( $fields['billing']['billing_samoviziv'] ) ) {
            unset( $fields['billing']['billing_samoviziv'] );
        }
		if ( isset( $fields['billing']['billing_type_delivery_sam'] ) ) {
            unset( $fields['billing']['billing_type_delivery_sam'] );
        }

	}


	return $fields;

 }

 add_action( 'woocommerce_checkout_update_order_review', 'custom_woocommerce_checkout_update_order_review', 10, 1 );
 function custom_woocommerce_checkout_update_order_review( $post_data ) {
	WC()->session->set( 'custom_cache', false );
 }

 add_action('wp_ajax_update_user_address', 'update_user_address_callback');
 add_action('wp_ajax_nopriv_update_user_address', 'update_user_address_callback'); // для неавторизованных пользователей

function update_user_address_callback() {
	setcookie( 'delivery', 0, time() + 3600 * 24 * 7, '/' );
	WC()->session->set( 'custom_cache', false );
	setcookie( 'billing_delivery', 0, time() - 1, '/' );
	setcookie( 'billing_comment', 0, time() - 1, '/' );
	$user_id = get_current_user_id();
	$address = $_POST['address'];
	setcookie("data_check", $address['billing_comment_zakaz'], time()+3600 * 24 * 7, '/');
	setcookie( 'time', $address['time'], time() + 3600 * 24 * 7, '/' );
	setcookie( 'coords', $address['coords'], time() + 3600 * 24 * 7, '/' );
	setcookie( 'time_type', $address['time_type'], time() + 3600 * 24 * 7, '/' );
	setcookie( 'delivery_time', $address['delivery_time'], time() + 3600 * 24 * 7, '/' );
	setcookie( 'delivery_day', $address['delivery_day'], time() + 3600 * 24 * 7, '/' );
	setcookie( 'billing_coords', $address['coords'], time() + 3600 * 24 * 7, '/' );

	$points = [];

	$points = ferma_get_shops_by_coords($address['coords']);

	setcookie( 'wms_city', base64_encode(serialize($points)), time() + 3600 * 24 * 7, '/' );

	/*if(mb_strpos($address['billing_delivery'], "Владивосток") !== false && mb_strpos($address['billing_delivery'], "Трудовое") === false) {
		setcookie( 'wms_city', "vl", time() + 3600 * 24 * 7, '/' );
		global $vl_shops;
		$points = $vl_shops;
	}
	if(mb_strpos($address['billing_delivery'], "Уссурийск") !== false) {
		setcookie( 'wms_city', "uss", time() + 3600 * 24 * 7, '/' );
		global $uss_shops;
		$points = $uss_shops;
	}
	if(mb_strpos($address['billing_delivery'], "Трудовое") !== false ||
		mb_strpos($address['billing_delivery'], "Надеждинский") !== false ||
		mb_strpos($address['billing_delivery'], "Лазурная") !== false ||
		mb_strpos($address['billing_delivery'], "Артековская") !== false ||
		mb_strpos($address['billing_delivery'], "Артём") !== false) {
		setcookie( 'wms_city', "art", time() + 3600 * 24 * 7, '/' );
		global $art_shops;
		$points = $art_shops;
	}*/

	if ( is_user_logged_in() ) {
		update_user_meta($user_id, 'delivery', 0);
		update_user_meta($user_id, 'time_type', 0);
		foreach ($address as $key => $value) {
			update_user_meta($user_id, $key, $value);
		}
		$user_data = array(
			'ID' => $user_id,
			'time_type' => $address['time_type']
		);

		wp_update_user($user_data);
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

	change_delivery_remove_items($points);
}


if ( ! function_exists( 'theme_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function theme_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Theme, use a find and replace
		 * to change 'theme' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'theme', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', 'theme' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'theme_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
	}
endif;


function my_shortcode(){
	$img1 = get_field( "1_izobrazhenie" );
	$img2 = get_field( "2_izobrazhenie" );
	$img3 = get_field( "3_izobrazhenie" );
	$link1 = get_field( "1_ssylka" );
	$link2 = get_field( "2_ssylka" );
	$link3 = get_field( "3_ssylka" );
	echo'
	<div class="short_bnr">
	<div style="display:flex">
	<img style="width: 633px;;margin-bottom: -5px;" id="photo_pc_1" onclick="location.href = '. "'" . $link1. "'" .';" src="' . $img1 . '" height="250" alt="" /> 
  <div class="class" style="width:50%;margin-left:5px"> 
	  <img  onclick="location.href = '. "'" . $link2. "'" .';" src="' . $img2 . '" style="width:100%;height: 50%;"  alt="" /> 
	<img  onclick="location.href = '. "'" . $link3. "'" .';" src="' . $img3 . '" id="photo_pc_2" style="margin-top:5px;width:100%;height: 50%;" alt="" /> 
  
  </div>
   
  </div>
	<img style="width: 633px;display:none" id="photo_0"  onclick="location.href = '. "'" . $link1. "'" .';" src="' . $img1 . '" height="250" alt="" /> 
   <img  onclick="location.href = '. "'" . $link2. "'" .';" style="width: 100%;display:none;margin-top:1em" id="photo_1" src="' . $img2 . '" height="250" alt="" /> 
   <img style="width: 100%;display:none;margin-top:1em;" onclick="location.href = '. "'" . $link3. "'" .';"  id="photo_2" src="' . $img3 . '" height="250" alt="" />
   </div>';
 }
add_shortcode('say_banner','my_shortcode');


add_filter ( 'woocommerce_account_menu_items', 'truemisha_log_history_link', 25 );
function truemisha_log_history_link( $menu_links ){
	$menu_links = array_slice( $menu_links, 0, 4, true ) + array( 'user-market' => 'Выбор ближайшего магазина' ) + array_slice( $menu_links, 4, NULL, true );
	return $menu_links;

}

add_action( 'woocommerce_order_status_on-hold', 'callback_check_bonus' );
function callback_check_bonus($order_id) {
	date_default_timezone_set('Asia/Vladivostok');

	$bonus = get_post_meta( $order_id, 'billing_bonus', true );
	$order = wc_get_order( $order_id );
	$total = $order->get_total();

	$percent = $total / 100 * 30;

	if($bonus > $percent) {
		update_post_meta( $order_id, 'billing_bonus', $percent );
		$bonus = $percent;
	}

	$fp = fopen(dirname(__FILE__) . '/kilbil.txt', 'a+');
	fwrite($fp, "\n---INPUT DATA---\n");
	fwrite($fp, date("Y-m-d H:i:s\n"));
	fwrite($fp, $order_id . " - " . $percent . " - " . $total . "\n");
	fclose($fp);

	if((int) $bonus > 0) {
		$real_bonus = get_real_kilbil_bonus();

		if($real_bonus == 0) {
			update_post_meta( $order_id, 'billing_bonus', 0 );
			unset($_COOKIE['balik']);
			setcookie('balik', null, -1, '/');
		} else if($real_bonus < $bonus) {
			update_post_meta( $order_id, 'billing_bonus', $real_bonus );
			unset($_COOKIE['balik']);
			setcookie('balik', null, -1, '/');
		} else if($real_bonus >= $bonus) {
			update_post_meta( $order_id, 'billing_bonus', $bonus );
			unset($_COOKIE['balik']);
			setcookie('balik', null, -1, '/');
		} else {
			update_post_meta( $order_id, 'billing_bonus', 0 );
			unset($_COOKIE['balik']);
			setcookie('balik', null, -1, '/');
		}
	}
}


add_action( 'woocommerce_order_status_completed', 'callback_order_bonus' );
function callback_order_bonus($order_id) {
	$bonus_added = get_field('order_bonus_added', $order_id);
	$samovivoz = get_post_meta( $order_id, 'billing_samoviziv', true );
	if(!$bonus_added && $samovivoz == '') {
		$order = wc_get_order( $order_id );

		$user_id = $order->get_user_id();
		$total = $order->get_total();
		$percent = 5;
		$fulltotal =  $total * ($percent / 100);
		$user_info = get_userdata($user_id);
		$userlogin = $user_info->user_login;
		$content = preg_replace("/[^0-9]/", "", $userlogin);

		$arr = array('search_mode' => 0, 'search_value' => $content);

		$url = "https://bonus.kilbil.ru/load/searchclient?h=666c13d171b01d80b04e590794a968b7";
		$content = json_encode($arr);
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER,
				array("Content-type: application/json"));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		$json_response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$obj = json_decode($json_response);
		$userbonus = $obj->{'client_id'};
		curl_close($curl);

		$arr = array('client_id' => $userbonus, 'bonus_in' => $fulltotal);

		$url = "https://bonus.kilbil.ru/load/manualadd?h=666c13d171b01d80b04e590794a968b7";
		$content = json_encode($arr);
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER,
				array("Content-type: application/json"));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		$json_response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$obj = json_decode($json_response);
		curl_close($curl);
		//echo "<script>console.log('Debug Objects: " . $content . "' );</script>";
		$data  = $order->get_data();
		$bonus = get_post_meta( $order->get_id(), 'billing_bonus', true );
		if($bonus > 0) {
			$fp = fopen(dirname(__FILE__) . '/kilbil.txt', 'a+');
			fwrite($fp, "\n---INPUT DATA---\n");
			fwrite($fp, date("Y-m-d H:i:s\n"));
			fwrite($fp, "Начисление бонуса: " . $bonus . "\n");
			fclose($fp);
			$arr = array('client_id' => $userbonus, 'bonus_out' => $bonus);

			$url = "https://bonus.kilbil.ru/load/manualadd?h=666c13d171b01d80b04e590794a968b7";
			$content = json_encode($arr);
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER,
					array("Content-type: application/json"));
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
			$json_response = curl_exec($curl);
			$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			$obj = json_decode($json_response);
			curl_close($curl);
			unset($_COOKIE['balik']);
			setcookie('balik', null, -1, '/');
			echo "<script>console.log('Debug Objects: " . $content . "' );</script>";
		}

		update_field('order_bonus_added', 1, $order_id);
	}
}

function get_real_kilbil_bonus()
{
	$userbonus = 0;

	$user_id = get_current_user_id();
	$user_info = get_userdata($user_id);
	$userlogin = $user_info->user_login;
	$content = preg_replace("/[^0-9]/", "", $userlogin);

	if(strlen($content) < 10) {
		return 0;
	}

	$arr = array('search_mode' => 0, 'search_value' => $content);

	$url = "https://bonus.kilbil.ru/load/searchclient?h=666c13d171b01d80b04e590794a968b7";
	$content = json_encode($arr);
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER,
			array("Content-type: application/json"));
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
	$json_response = curl_exec($curl);
	$obj = json_decode($json_response);
	if(isset($obj->{'balance'}) && (int) $obj->{'balance'} > 0) {
		$userbonus = $obj->{'balance'};
	}
	curl_close($curl);

	return $userbonus;
}

//add_action( 'woocommerce_order_status_completed', 'callback_function_name' );
function callback_function_name() {
	$url = $_SERVER["REQUEST_URI"];
	$parts = parse_url($url);
	parse_str($parts['query'], $query);
	$order_id = $query['post'][0];
	$order = wc_get_order( $order_id );
	$user_id = $order->get_user_id();
	$total = $order->get_total();
	$percent = 5;
	$fulltotal =  $total * ($percent / 100);
	$user_info = get_userdata($user_id);
	$userlogin = $user_info->user_login;
	$content = preg_replace("/[^0-9]/", "", $userlogin);

	if(strlen($content) < 10) {
		return false;
	}

	$arr = array('search_mode' => 0, 'search_value' => $content);

	$url = "https://bonus.kilbil.ru/load/searchclient?h=666c13d171b01d80b04e590794a968b7";
	$content = json_encode($arr);
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER,
			array("Content-type: application/json"));
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
	$json_response = curl_exec($curl);
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$obj = json_decode($json_response);
	$userbonus = $obj->{'client_id'};
	curl_close($curl);

	$arr = array('client_id' => $userbonus, 'bonus_in' => $fulltotal);

	$url = "https://bonus.kilbil.ru/load/manualadd?h=666c13d171b01d80b04e590794a968b7";
	$content = json_encode($arr);
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER,
			array("Content-type: application/json"));
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
	$json_response = curl_exec($curl);
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$obj = json_decode($json_response);
	curl_close($curl);
	echo "<script>console.log('Debug Objects: " . $content . "' );</script>";
	$data  = $order->get_data();
	$bonus = get_post_meta( $order->get_id(), 'billing_bonus', true );
	if($bonus > 0) {
		$arr = array('client_id' => $userbonus, 'bonus_out' => $bonus);

		$url = "https://bonus.kilbil.ru/load/manualadd?h=666c13d171b01d80b04e590794a968b7";
		$content = json_encode($arr);
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER,
				array("Content-type: application/json"));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		$json_response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$obj = json_decode($json_response);
		curl_close($curl);
		unset($_COOKIE['balik']);
		setcookie('balik', null, -1, '/');
		echo "<script>console.log('Debug Objects: " . $content . "' );</script>";
	}

}
add_action( 'init', 'truemisha_add_endpoint', 25 );
function truemisha_add_endpoint() {

	add_rewrite_endpoint( 'user-market', EP_PAGES );

}
add_action( 'woocommerce_account_user-market_endpoint', 'truemisha_content', 25 );
function truemisha_content() {

	echo 'В последний раз вы входили вчера через браузер Safari.';

}
add_action( 'after_setup_theme', 'theme_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function theme_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'theme_content_width', 640 );
}
add_action( 'after_setup_theme', 'theme_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function theme_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'theme' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'theme' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'theme_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function theme_scripts() {
	if ( ! is_page(2 & 18120) & ! is_product() ) {
		wp_enqueue_style( 'theme-style', get_stylesheet_uri(), '', '2.9.9' );
    }

	wp_enqueue_style( 'complect-style', get_template_directory_uri() . '/css/complect.css', '', '1.0' );

	wp_enqueue_script( 'jquery-min', get_template_directory_uri() . '/js/jquery.min.js', array(), '3.1.0', false );

	wp_enqueue_script( 'datepicker', get_template_directory_uri() . '/js/datepicker.js', array(), '1.0', true );



	$style_path = get_template_directory() . '/assets/css/style.min.css';
	$style_uri = get_template_directory_uri() . '/assets/css/style.min.css';
	$version = file_exists($style_path) ? filemtime($style_path) : null;
	wp_enqueue_style( 'new-style', $style_uri, [], $version );

	wp_enqueue_script( 'slick', get_template_directory_uri() . '/js/slick.min.js', array(), '1.0', true );

	wp_enqueue_script( 'buyoneclick', get_template_directory_uri() . '/js/buyoneclick.js', array(), '1.1', true );

	wp_enqueue_script( 'complect', get_template_directory_uri() . '/js/complect.js', array(), '1.1', true );
	wp_localize_script( 'complect', 'complect', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

	wp_enqueue_script( 'theme-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );

	wp_enqueue_script( 'theme-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	if (is_checkout() && !is_order_received_page()) {
        wp_enqueue_script(
            'custom-checkout-js',
            get_stylesheet_directory_uri() . '/assets/js/checkout.js',
            array('jquery'),
            '1.4',
            true
        );
    }


    wp_enqueue_script(
        'mini-cart-qty',
        get_template_directory_uri() . '/assets/js/mini-cart-qty.js',
        array( 'jquery', 'wc-cart-fragments' ),
        '1.0',
        true
    );
    wp_enqueue_script(
        'catalog-qty-js',
        get_template_directory_uri() . '/assets/js/catalog-qty23.js',
        array('jquery'),
        '1.0',
        true
    );
    wp_localize_script(
        'mini-cart-qty',
        'CartQtyData',
        array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'update_cart_qty' ),
        )
    );
}
add_action( 'wp_enqueue_scripts', 'theme_scripts' );
add_action('woocommerce_before_add_to_cart_button', function() {
    if (!WC()->cart) return;

    global $product;
    $found_key = '';

    foreach (WC()->cart->get_cart() as $key => $item) {
        if ($item['product_id'] == $product->get_id()) {
            $found_key = $key; // нашли позицию
            break;
        }
    }

    if ($found_key) {
        echo '<input type="hidden" id="single_cart_item_key" value="'.esc_attr($found_key).'">';
    }
});

require get_template_directory() . '/wc-functions.php';
/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

//add_filter('show_admin_bar', '__return_false'); // отключить


// Display the mobile phone field
// add_action( 'woocommerce_edit_account_form_start', 'add_billing_mobile_phone_to_edit_account_form' ); // At start
add_action( 'woocommerce_edit_account_form', 'add_billing_mobile_phone_to_edit_account_form' ); // After existing fields
function add_billing_mobile_phone_to_edit_account_form() {
    $user = wp_get_current_user();
    ?>


     <div class="form-row page-account__editForm-number">
     	<script src="https://unpkg.com/imask"></script>
     	<label for="billing_phone"><?php _e( 'Номер телефона', 'woocommerce' ); ?> <span
     			class="required">*</span></label>
     	<input placeholder="Телефон" type="text" class="woocommerce-Input woocommerce-Input--phone" name="billing_phone" id="billing_phone" value="<?php echo esc_attr( $user->billing_phone ); ?>" disabled />
     	<script>
     		function izmena() {
     			var inputs = document.getElementsByTagName('input');
     			for (i = 0; i < inputs.length; i++) {
     				inputs[i].disabled = false;
     			}

     			var a = FindByAttributeValue("for", "billing_phone");
     			a.innerHTML = 'Введите новый номер телефона <span class="required">*</span>';
     			var b = FindByAttributeValue("name", "save_account_details");
     			b.disabled = true;
     			$("#smenanomera").hide();
     			$("#smenanomera2").show();

     			function FindByAttributeValue(attribute, value, element_type) {
     				element_type = element_type || "*";
     				var All = document.getElementsByTagName(element_type);
     				for (var i = 0; i < All.length; i++) {
     					if (All[i].getAttribute(attribute) == value) {
     						return All[i];
     					}
     				}
     			}
     		}
     	</script>
     	<style>
     		#smenanomera2,
     		#smenanomera,
     		#smenanomera3 {
     			font-size: 100%;
     			margin: 0;
     			line-height: 1;
     			cursor: pointer;
     			position: relative;
     			text-decoration: none;
     			overflow: visible;
     			padding: 16px 32px;
     			font-weight: 700;
     			border-radius: 12px;
     			left: auto;
     			color: #ffffff;
     			background-color: var(--color-green);
     			border: 0;
     			display: inline-block;
     			background-image: none;
     			box-shadow: none;
     			text-shadow: none;
				transition: ease-in-out .2s;
     		}
			#smenanomera2,
     		#smenanomera,
     		#smenanomera3:hover {
				background-color: rgba(79,189,1,.85);
			}
     	</style>
     	<button class="btn-green" type="button" onclick="izmena()" id="smenanomera">Изменить</button>
     	<button class="btn-green" type="button" style="display:none" id="smenanomera2">Применить</button>
     	<button class="btn-green" type="button" style="display:none" id="smenanomera3">Изменить</button>
     	<input type="hidden" id="id_user" value="<?$cur_user_id = get_current_user_id(); echo $cur_user_id;?>">








     	<script>
     		var e = FindByAttributeValue("id", "billing_phone");

     		$("#smenanomera2").on("click", function () {
     			$.ajax({
     				url: '/wp-content/themes/theme/obrabotka.php',
     				method: 'post',
     				dataType: 'html',
     				data: {
     					text: e.value
     				},
     				success: function (data) {
     					var jsonData = JSON.parse(data);
     					if (jsonData.error == 1) {
     						document.getElementById("ajaxresult").innerHTML =
     							'<p>Вы неверно ввели номер</p>';
     					} else {
     						document.cookie = "snemanomera1=1;path=/;max-age=30;";
     						var number = FindByAttributeValue("name", "billing_phone").value;
     						document.getElementById("ajaxresult").innerHTML =
     							'<input type="hidden" id="code_telephone" name="code_telephone" value="' +
     							jsonData.code +
     							'" ><input type="hidden" id="telephone" name="telephone" value="' +
     							number + '" >';
     						var g = FindByAttributeValue("name", "billing_phone");
     						g.value = "";
     						var a = FindByAttributeValue("for", "billing_phone");
     						a.innerHTML =
     							'ВВЕДИТЕ ПОСЛЕДНИЕ 4 ЦИФРЫ ЗВОНЯЩЕГО НОМЕРА <span class="required">*</span>';
     						$("#smenanomera").hide();
     						$("#smenanomera2").hide();
     						$("#smenanomera3").show();
     					}



     				}
     			});
     		});

     		function FindByAttributeValue(attribute, value, element_type) {
     			element_type = element_type || "*";
     			var All = document.getElementsByTagName(element_type);
     			for (var i = 0; i < All.length; i++) {
     				if (All[i].getAttribute(attribute) == value) {
     					return All[i];
     				}
     			}
     		}
     	</script>

     	<script>
     		$("#smenanomera3").on("click", function () {
     			var e = FindByAttributeValue("id", "billing_phone");
     			var k = document.getElementById("code_telephone");
     			$.ajax({
     				url: '/wp-content/themes/theme/obrabotka1.php',
     				method: 'post',
     				dataType: 'html',
     				data: {
     					text: e.value,
     					code: k.value
     				},
     				success: function (data) {
     					var jsonData = JSON.parse(data);
     					if (jsonData.success == 0) {
     						document.getElementById("ajaxresult").innerHTML +=
     							'<p>Вы ввели неверный код</p>';
					} else {
						if (!jsonData.handoff) {
							document.getElementById("ajaxresult").innerHTML +=
								'<p>Не удалось выдать сессию. Обновите страницу и повторите ввод кода.</p>';
							return;
						}
						document.cookie = "snemanomera=" + encodeURIComponent(jsonData.handoff) + ";path=/;max-age=120;SameSite=Lax";
						var g = FindByAttributeValue("name", "billing_phone");
     						var m = FindByAttributeValue("id", "telephone").value;
     						g.value = m;
     						document.getElementById("ajaxresult").innerHTML +=
     							'<p>Вы успешно прошли изменение номера</p>';
     						$("#smenanomera2").hide();
     						var b = FindByAttributeValue("name", "save_account_details");
     						b.disabled = false;
     						IMask(
     							document.getElementById('billing_phone'), {
     								mask: '+{7}(000)0000000'
     							});
     						FindByAttributeValue("name", "save_account_details").click();
     					}

     				}
     			});
     		});

     		function FindByAttributeValue(attribute, value, element_type) {
     			element_type = element_type || "*";
     			var All = document.getElementsByTagName(element_type);
     			for (var i = 0; i < All.length; i++) {
     				if (All[i].getAttribute(attribute) == value) {
     					return All[i];
     				}
     			}
     		}
     	</script>
     	<div id="ajaxresult" class="ajaxresult">

     	</div>
     	<script>
     	</script>
     </div>
    <?php
}
add_filter( 'wms_product_attributes', 'fdv_add_fasovka_attribute', 10, 3 );

function fdv_add_fasovka_attribute( $attributes, $product, $ms_product ) {

    if ( empty( $ms_product->attributes ) ) {
        return $attributes;
    }

    foreach ( $ms_product->attributes as $attr ) {

        if ( $attr->name !== 'Фасовка' ) { // ← НАЗВАНИЕ характеристики в МойСкладе!
            continue;
        }

        $value = trim( (string) $attr->value );

        if ( $value === '' ) {
            continue;
        }

        // Создаст/использует таксономию pa_fasovka
        $taxonomy = 'pa_fasovka';

        // Создаём термин, если нет
        if ( ! term_exists( $value, $taxonomy ) ) {
            wp_insert_term( $value, $taxonomy );
        }

        $term = get_term_by( 'name', $value, $taxonomy );
        if ( ! $term ) {
            continue;
        }

        // Записываем в массив атрибутов, который WooMS затем присвоит товару
        $attributes[$taxonomy] = [
            'name'      => $taxonomy,
            'value'     => $term->term_id,
            'is_visible'=> 1,
            'is_variation' => 0,
            'is_taxonomy'  => 1,
        ];
    }

    return $attributes;
}

/**
 * Проверка: товар в категориях "0.1 кг" (по slug) или их подкатегориях
 */
function ferma_product_in_ratio_01_categories( $product_id ) {
    // slugs категорий, для которых нужен шаг 0.1 кг
    $target_slugs = array(
        'domashnie-syry', // "Домашние сыры"
        'konfety',
    );

    $terms = get_the_terms( $product_id, 'product_cat' );
    if ( empty( $terms ) || is_wp_error( $terms ) ) {
        return false;
    }

    foreach ( $terms as $term ) {
        // 1) Совпадение по slug самой категории
        if ( in_array( $term->slug, $target_slugs, true ) ) {
            return true;
        }

        // 2) Проверка всех предков
        $ancestors = get_ancestors( $term->term_id, 'product_cat' );
        if ( ! empty( $ancestors ) ) {
            foreach ( $ancestors as $ancestor_id ) {
                $parent = get_term( $ancestor_id, 'product_cat' );
                if ( $parent && ! is_wp_error( $parent ) && in_array( $parent->slug, $target_slugs, true ) ) {
                    return true;
                }
            }
        }
    }

    return false;
}

// Check and validate the mobile phone
add_action( 'woocommerce_save_account_details_errors','billing_mobile_phone_field_validation', 20, 1 );
function billing_mobile_phone_field_validation( $args ){
    if ( isset($_POST['billing_phone']) && empty($_POST['billing_phone']) )
        $args->add( 'error', __( 'Please fill in your Mobile phone', 'woocommerce' ),'');
}

// Save the mobile phone value to user data
add_action( 'woocommerce_save_account_details', 'my_account_saving_billing_mobile_phone', 20, 1 );
function my_account_saving_billing_mobile_phone( $user_id ) {
    if( isset($_POST['billing_phone']) && ! empty($_POST['billing_phone']) )
        update_user_meta( $user_id, 'billing_phone', sanitize_text_field($_POST['billing_phone']) );
		if($_COOKIE['snemanomera1'] == 1) {
			global $wpdb;
			$cur_user_id = get_current_user_id();
			$wpdb->update( 'wp_users',
			array( 'user_login' => $_POST['billing_phone'], 'display_name' => $_POST['billing_phone']),

			array( 'ID' => $cur_user_id )
		);

		}


}



add_action( 'woocommerce_before_calculate_totals', 'func_quantity_based_price' );
function func_quantity_based_price( $cart_object ) {

	// 100 гр
	$product_cat_sir = array();
	$product_cat_sir[] = 144;

	$product_cats_sir = get_categories( [
		'taxonomy' => 'product_cat',
		'parent'   => 144
	] );

    foreach ( $cart_object->get_cart() as $cart_id => $cart_item ) {

        $product = $cart_item['data'];
        if ( ! $product instanceof WC_Product ) {
            continue;
        }

        $product_id = (int) ( $product->is_type('variation')
            ? $product->get_parent_id()
            : $product->get_id()
        );

        $rz = function_exists('get_field')
            ? (string) get_field('razbivka_vesa', $product_id)
            : (string) get_post_meta($product_id, 'razbivka_vesa', true);

        $rz = mb_strtolower(trim($rz));

        if ( $rz !== 'да' && ! ferma_product_in_ratio_01_categories( $product_id ) ) {
            continue;
        }

        $ratio = function_exists('fdv_ms_get_weight_ratio_for_product')
            ? (float) fdv_ms_get_weight_ratio_for_product( $product_id )
            : 0.1;

        if ( ferma_product_in_ratio_01_categories( $product_id ) ) {
            $ratio = 0.1;
        }

        if ( $ratio == 1 || $ratio <= 0 ) {
            continue;
        }

        $price_per_kg = (float) $product->get_regular_price();
        if ( $price_per_kg <= 0 ) {
            $price_per_kg = (float) $product->get_price();
        }

        $product->set_price( $price_per_kg * $ratio );
    }



    $product_cat_pr5[] = 156;
	$product_cats_kopch = get_categories( [
		'taxonomy' => 'product_cat',
		'parent'   => 156
	] );

    foreach( $product_cats_kopch as $product_kopch ) {
             $product_cat_pr5[] = $product_kopch->term_id;
    }

	$product_cat_pr[] = 164;
	$product_cats_myaso = get_categories( [
		'taxonomy' => 'product_cat',
		'parent'   => 164
	] );

    foreach( $product_cats_myaso as $product_myaso ) {
         $product_cat_pr[] = $product_myaso->term_id;
    }

	// Колбаски для жарки
	$product_cat_pr[] = 265;

	$product_cat_pr[] = 168;

	$product_cats_ryba = get_categories( [
		'taxonomy' => 'product_cat',
		'parent'   => 168
	] );

    foreach( $product_cats_ryba as $product_ryba ) {
		$product_cat_pr[] = $product_ryba->term_id;
    }



}
add_action( 'init', 'fdv_set_razbivka_for_konfety' );
function fdv_set_razbivka_for_konfety() {
    // Выполняем только для админа и только в админке, чтобы не ловить лишних запусков на фронте
    if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Чтобы скрипт не крутился вечно – запускаем один раз по GET-параметру
    if ( empty( $_GET['run_konfety_razbivka'] ) ) {
        return;
    }

    // ВАЖНО: тут укажи реальный slug категории "Конфеты"
    $category_slug = 'konfety';

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $category_slug,
            ),
        ),
        'fields' => 'ids',
    );

    $products = get_posts( $args );

    if ( empty( $products ) ) {
        error_log( 'fdv_set_razbivka_for_konfety: товаров не найдено.' );
        return;
    }

    foreach ( $products as $product_id ) {
        // если поле ACF, лучше использовать update_field, но можно и update_post_meta
        update_field( 'razbivka_vesa', 'да', $product_id );
        // или:
        // update_post_meta( $product_id, 'razbivka_vesa', 'да' );
    }

    error_log( 'fdv_set_razbivka_for_konfety: обновлено товаров: ' . count( $products ) );
}

function fdv_get_cart_qty_for_product( $product_id ) {
    if ( ! WC()->cart || WC()->cart->is_empty() ) {
        return 0;
    }

    $qty = 0;

    foreach ( WC()->cart->get_cart() as $cart_item ) {
        if ( (int) $cart_item['product_id'] === (int) $product_id ) {
            $qty += (float) $cart_item['quantity'];
        }
    }

    return $qty;
}

function fdv_format_price_rub( $value ) {
    return number_format( (float) $value, 0, '', ' ' ) . ' ₽';
}
add_filter( 'woocommerce_get_price_html', 'wb_change_product_html', 30 );
function wb_change_product_html( $price ) {

    global $product;
    if ( ! $product ) return $price;

    $product_id  = $product->get_id();
    $real_price  = (float) $product->get_regular_price(); // без скидки
    $price_tovar = (float) $product->get_price();
    $is_weighted = ( get_field( 'razbivka_vesa', $product_id ) == 'да' );

    // НЕ весовые как были — можно оставить
    if ( ! $is_weighted ) {
        if ( $price_tovar != $real_price ) {
            return '<span class="old-price woocommerce-Price-amount amount">' . fdv_format_price_rub( $real_price ) . '</span>
                    <span class="woocommerce-Price-amount amount discount-offset" 
                          data-price-base="' . esc_attr( $price_tovar ) . '" 
                          data-ratio="1" 
                          data-is-weighted="0">
                        ' . fdv_format_price_rub( $price_tovar ) . ' <span class="price-unit-text">за шт.</span>
                    </span>';
        }

        return '<span class="woocommerce-Price-amount amount discount-offset"
                      data-price-base="' . esc_attr( $price_tovar ) . '" 
                      data-ratio="1" 
                      data-is-weighted="0">
                    ' . fdv_format_price_rub( $price_tovar ) . ' <span class="price-unit-text">за шт.</span>
                </span>';
    }

    // ВЕСОВЫЕ
    if ( function_exists( 'fdv_ms_get_weight_ratio_for_product' ) ) {
        $ratio = (float) fdv_ms_get_weight_ratio_for_product( $product_id );
    } else {
        $ratio = 0.1;
    }

    if ( ferma_product_in_ratio_01_categories( $product_id ) ) {
        $ratio = 0.1;
    }
    if ( $ratio <= 0 ) {
        $ratio = 0.1;
    }

    // Смотрим, есть ли товар в корзине
    $cart_qty = fdv_get_cart_qty_for_product( $product_id );
    if ( $cart_qty <= 0 ) {
        $cart_qty = 1; // дефолт: 1 шаг (0.1 кг или 1 кг)
    }

    // Итоговый вес и цена для отображения
    $total_weight = $ratio * $cart_qty;                    // в кг
    $display_price_per_step = $price_tovar * $ratio;       // за один шаг
    $display_price_total    = $display_price_per_step * $cart_qty;

    $unit_label = fdv_format_weight( $total_weight );

    // СКИДКА (реальная цена за 1 кг была больше)
    if ( $price_tovar != $real_price ) {
        $old_price_total = (float) $real_price * $ratio * $cart_qty;

        return '<span class="old-price woocommerce-Price-amount amount">'
            . fdv_format_price_rub( $old_price_total ) . '</span>

                <span class="woocommerce-Price-amount amount discount-offset"
                      data-price-base="' . esc_attr( $price_tovar ) . '"  /* цена за 1 кг */
                      data-ratio="' . esc_attr( $ratio ) . '"
                      data-is-weighted="1">
                    ' . fdv_format_price_rub( $display_price_total ) . ' 
                    <span class="price-unit-text">за ' . esc_html( $unit_label ) . '</span>
                </span>';
    }

    // БЕЗ скидки
    return '<span class="woocommerce-Price-amount amount discount-offset"
                  data-price-base="' . esc_attr( $price_tovar ) . '"   /* цена за 1 кг */
                  data-ratio="' . esc_attr( $ratio ) . '"
                  data-is-weighted="1">
                ' . fdv_format_price_rub( $display_price_total ) . '
                <span class="price-unit-text">за ' . esc_html( $unit_label ) . '</span>
            </span>';
}

add_filter( 'woocommerce_quantity_input_args', 'fdv_default_qty_from_cart', 10, 2 );
function fdv_default_qty_from_cart( $args, $product ) {

    if ( is_admin() ) {
        return $args;
    }

    $product_id = $product->get_id();
    $cart_qty   = fdv_get_cart_qty_for_product( $product_id );

    if ( $cart_qty > 0 ) {
        $args['input_value'] = $cart_qty;
    }

    return $args;
}


// add_filter( 'woocommerce_cart_item_price', 'wpd_show_regular_price_on_cart', 30, 3 );
// function wpd_show_regular_price_on_cart( $price, $values, $cart_item_key ) {

// 	$sale_percent = get_field('priceint', 'option');

// 	$is_on_sale = array_shift( wc_get_product_terms( $values['data']->id, 'pa_akcziya', array( 'fields' => 'names' ) ) );


//    if ( $sale_percent and $is_on_sale ) {
// 		$_product = $values['data'];
// 		$price_tovar = $_product->get_regular_price();
// 		$sale_price = $price_tovar - ($price_tovar * ($sale_percent / 100));
//         $price = '<span class="wpd-discount-price" style="text-decoration: line-through; opacity: 0.5; padding-right: 5px;">' . wc_price( price_tovar ) . '</span>' . $sale_price;

//    }

//    return $price;

// }


add_action( 'woocommerce_cart_calculate_fees', 'truemisha_add_fee_paypal', 15);

function truemisha_add_fee_paypal($cart) {
	if(isset($_COOKIE["balik"])) {
		$userbonus = 0;

		$user_id = get_current_user_id();
		$user_info = get_userdata($user_id);
		$userlogin = $user_info->user_login;
		$content = preg_replace("/[^0-9]/", "", $userlogin);

		$arr = array('search_mode' => 0, 'search_value' => $content);

		$url = "https://bonus.kilbil.ru/load/searchclient?h=666c13d171b01d80b04e590794a968b7";
		$content = json_encode($arr);
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER,
				array("Content-type: application/json"));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		$json_response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$obj = json_decode($json_response);
		$userbonus = $obj->{'balance'};
		curl_close($curl);

		$real_balik = $cart->subtotal * 0.3;
		if($_COOKIE["balik"] != $real_balik) {
			$_COOKIE["balik"] = $real_balik;
		}

		if((int) $userbonus >= $_COOKIE["balik"]) {
			WC()->cart->add_fee( 'Бонусы', -$_COOKIE["balik"]);
		} else {
			if((int) $userbonus > 0) {
				WC()->cart->add_fee( 'Бонусы', -$userbonus);
			}
		}
	}
}

add_action( 'woocommerce_cart_calculate_fees', 'truemisha_add_fee_paypal1', 15);

function truemisha_add_fee_paypal1() {
	if(isset($_COOKIE["discount"])) {
		if($_COOKIE["discount"] == 0) {

		} else {
			WC()->cart->add_fee( 'Скидка', -$_COOKIE["discount"]);
		}

	}
}
add_action('woocommerce_cart_item_removed', 'remove_discount_cookie_on_cart_item_removed', 10, 2);

function remove_discount_cookie_on_cart_item_removed($cart_item_key, $cart) {
    // проверяем, есть ли установленная cookie
    if (isset($_COOKIE['discount'])) {
        // удаляем cookie
        setcookie('discount', '', time() - 3600, '/');
    }
}


// add_action( 'template_redirect', function(){
//     ob_start( function( $ag_filter ){
//         $ag_filter = str_replace( array( '<input type="email"' ), '<input type="text"', $ag_filter );
//         return $ag_filter;
//     });
// });


if( function_exists('acf_add_options_page') ) {

	acf_add_options_page(array(
		'page_title' 	=> 'Доп. настройки',
		'menu_title'	=> 'Доп. настройки',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));

	acf_add_options_page(array(
		'page_title' 	=> 'Уведомления',
		'menu_title'	=> 'Уведомления',
		'menu_slug' 	=> 'notice-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
}

// add_filter( 'woocommerce_get_price_html', 'truemisha_display_price', 99, 2 );

// function truemisha_display_price( $price_html, $product ) {

// 	// ничего не делаем в админке
// 	if ( is_admin() ) {
// 		return $price_html;
// 	}

// 	// если цена пустая, тоже забиваем
// 	if ( '' === $product->get_price() ) {
// 		return $price_html;
// 	}

// 	$fabric_price = $product->get_price();
// 	$sale_percent = get_field('priceint', 'option');
// 	$sale_price = $fabric_price - ($fabric_price * ($sale_percent / 100));

// 	$check_ac = array_shift( wc_get_product_terms( $product->id, 'pa_akcziya', array( 'fields' => 'names' ) ) );

// 	// класс, это наш пользователь сайта, ему вешаем скидку 20%
// 	if ( $check_ac and $sale_percent ) {
// 		$price_html = wc_price( wc_get_price_to_display( $product ) + 1 );
// 	}

// 	return $price_html;

// }

//add_filter( 'woocommerce_product_get_price' , 'products_price_with_discount' , 5, 2 );
add_filter( 'woocommerce_product_get_price', 'products_price_with_discount', 40, 2 );
//add_filter( 'woocommerce_product_variation_get_price', 'products_price_with_discount', 5, 2 );
//add_filter( 'woocommerce_product_get_sale_price', 'products_price_with_discount', 5, 2 );
//add_filter( 'woocommerce_product_variation_get_sale_price', 'products_price_with_discount', 5, 2 );
//add_filter( 'woocommerce_variation_prices_price', 'products_price_with_discount', 5, 2 );
//add_filter( 'woocommerce_variation_prices_sale_price', 'products_price_with_discount', 5, 2 );

function products_price_with_discount( $price, $product )
{
    $discount = get_field('priceint', 'option');
    $product_id = $product->get_id();

	//$price = $product->get_regular_price();

	//$_product = wc_get_product( $product_id );

	//$attributes = $product->get_attributes();

	$is_action = $product->get_attribute( 'pa_akcziya' );

	$price_date = get_field('pricedate', 'option');

	$price_date = date("Y-m-d 23:59:59", strtotime($price_date));

	$end_date = strtotime($price_date);
	$current_date = strtotime(date("Y-m-d H:i:s"));

	$green_friday_products = get_green_friday_products();

	foreach($green_friday_products['good_ids_with_discount'] as $percent => $green_friday_product) {
		if(in_array($product_id, $green_friday_product)) {
			$is_action = 1;
			$price_date = get_field('zp_date_end', 'option');
			$end_date = strtotime($price_date);
			$discount = $percent;
		}
	}

	if($end_date > $current_date && $discount > 0 && $is_action == 1) {
		//echo $price . "<br>" . $price - (($price / 100) * $discount);
		return $price - (($price / 100) * $discount);
	} else {
		return $price;
	}

}

function get_green_friday_products()
{

	$goods = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/green-friday.json");
	//if(current_user_can('administrator')) {
		return json_decode($goods, true);
	/*} else {
		return json_decode(['good_ids' => [], 'good_ids_with_discount' => []], true);
	}*/
}

function product_is_green_price($product) {

	$is_action = $product->get_attribute( 'pa_akcziya' );

	$discount = get_field('priceint', 'option');
	$price_date = get_field('pricedate', 'option');

	$price_date = date("Y-m-d 23:59:59", strtotime($price_date));

	$end_date = strtotime($price_date);
	$current_date = strtotime(date("Y-m-d H:i:s"));

	$green_friday_products = get_green_friday_products();

	foreach($green_friday_products['good_ids_with_discount'] as $percent => $green_friday_product) {
		if(in_array($product_id, $green_friday_product)) {
			$is_action = 1;
			$price_date = get_field('zp_date_end', 'option');
			$end_date = strtotime($price_date);
			$discount = $percent;
		}
	}

	if($end_date > $current_date && $discount > 0 && $is_action == 1) {
		return true;
	}

	return false;
}

//add_filter( 'woocommerce_get_price_html', 'products_price_html_with_discount', 10, 2 );
function products_price_html_with_discount( $price, $product )
{
	//echo $price;
	return $price;
}

function pre_get_posts_product_actions( $q ) {
	$cat_obj = $q->get_queried_object();

	if($cat_obj->term_id == 355) {
		$price_date = get_field('pricedate', 'option');
		//$price_date = date("Y-m-d 23:59:59", strtotime($price_date));
		$discount = get_field('priceint', 'option');

		$price_date = date("Y-m-d 23:59:59", strtotime($price_date));

		$end_date = strtotime($price_date);
		$current_date = strtotime(date("Y-m-d H:i:s"));

		if($current_date > $end_date || $discount == 0) {
			$q->set( 'cat', '7815' );
		}

		$terms = get_terms( array( 'product_cat' ), array( 'fields' => 'ids' ) );

		$q->set( 'tax_query', array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'pa_akcziya',
				'field' => 'slug',
				'terms' => array(1),
				'operator' => 'IN',
			),
			array(
				'taxonomy' => 'product_cat',
				'field' => 'id',
				'terms' => $terms,
				'operator' => 'IN'
			)
		));
	}

	if($cat_obj->term_id == 2626) {
		$zp_date_start = get_field('zp_date_start', 'option');
		$zp_date_end = get_field('zp_date_end', 'option');
		//$price_date = date("Y-m-d 23:59:59", strtotime($price_date));
		$discount = get_field('priceint', 'option');

		$zp_date_start = date("Y-m-d 23:59:59", strtotime($zp_date_start));
		$zp_date_end = date("Y-m-d 23:59:59", strtotime($zp_date_end));

		$current_date = strtotime(date("Y-m-d H:i:s"));

		if($current_date > $zp_date_end || $current_date < $zp_date_start) {
			$q->set( 'cat', '7815' );
		}

		$q->set( 'tax_query', null);

		$green_friday_products = get_green_friday_products();
		$good_ids = $green_friday_products['good_ids'];

		$terms = get_terms( array( 'product_cat' ), array( 'fields' => 'ids' ) );

		$q->set( 'tax_query', array(
			'relation' => 'OR',
			array(
				'taxonomy' => 'product_cat',
				'field' => 'id',
				'terms' => $terms,
				'operator' => 'IN'
			)
		));
		$q->set( 'post__in', $good_ids );
	}

}
add_action( 'pre_get_posts', 'pre_get_posts_product_actions' );


/*add_filter( 'woocommerce_package_rates', 'custom_shipping_costs', 10, 2 );
function custom_shipping_costs( $rates, $package ) {
	if($_SERVER['REMOTE_ADDR'] == "217.150.75.150") {
		$delivery_price = ferma_get_delivery_price($_COOKIE['coords'], $_COOKIE['delivery_time']);
		foreach($rates as $key => $value) {
			$rates[$key]->cost = $delivery_price;
		}
	}

    return $rates;
}*/
function get_weight_ratio($product_id)
{
    // Если включена разбивка веса – всегда 0.1 кг
    if ( get_field( "razbivka_vesa", $product_id ) == 'да' ) {
        return 0.1;
    }

    // Всё остальное – без разбивки (1 кг или шт., в зависимости от логики)
    return 1;
}

add_action( 'woocommerce_after_checkout_validation', 'ferma_validate_delivery_address', 10, 2 );
function fdv_format_weight( $kg ) {
    $kg = (float) $kg;

    // нормализуем до 1 знака
    $kg = round( $kg, 1 );

    if ( abs( $kg - round( $kg ) ) < 0.00001 ) {
        return (int) round( $kg ) . ' кг';
    }

    return number_format( $kg, 1, ',', ' ' ) . ' кг';
}

function ferma_validate_delivery_address( $fields, $errors ){
    if ( isset($_COOKIE['delivery']) && $_COOKIE['delivery'] == 0 && (!isset($_COOKIE['coords']) || $_COOKIE['coords'] == '') ) {
        $errors->add( 'validation', 'Введите корректный адрес и выберите время для доставки' );
    }
}


// add_action( 'woocommerce_after_single_product_summary', 'checkout_show_green_prices', 110 );

function checkout_show_green_prices( ) {
	echo '<div class="order-green-prices">';
	$related_products = wc_get_products(array(
		'limit' => -1,
		'tax_query' => array(
			array(
				'taxonomy' => 'pa_akcziya',
				'field' => 'slug',
				'terms' => array(1),
				'operator' => 'IN',
			),
		),
	));
	if ( $related_products ) : ?>

	<section class="related products">

		<?php
		$heading = "Зелёные ценники";

		if ( $heading ) :
			?>
			<h2><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php woocommerce_product_loop_start(); ?>

			<?php foreach ( $related_products as $related_product ) : ?>

					<?php
					$post_object = get_post( $related_product->get_id() );

					setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

					wc_get_template_part( 'content', 'product' );
					?>

			<?php endforeach; ?>

		<?php woocommerce_product_loop_end(); ?>

	</section>
	<?php endif; ?>
</div>
<?
}
function custom_display_price( $price, $cart_item, $cart_item_key ) {
    if ( empty( $cart_item['data'] ) || ! $cart_item['data'] instanceof WC_Product ) {
        return $price;
    }

    $product    = $cart_item['data'];
    $product_id = $product->get_id();
    $qty        = isset( $cart_item['quantity'] ) ? (float) $cart_item['quantity'] : 1;

    if ( $qty <= 0 ) {
        return $price;
    }

    // Коэффициент веса (для весовых 0.1, для обычных 1)
    $weight_ratio = function_exists( 'get_weight_ratio' ) ? (float) get_weight_ratio( $product_id ) : 1;
    if ( $weight_ratio <= 0 ) {
        $weight_ratio = 1;
    }

    // Базовая старая цена за 1 "единицу" (1 кг / 1 шт)
    $regular_base = (float) $product->get_regular_price();
    if ( $regular_base <= 0 ) {
        return $price; // нечего сравнивать
    }

    $discounted_base = (float) $product->get_price() / $weight_ratio;


    // Если реальной скидки нет – оставляем стандартный вывод
    if ( $discounted_base >= $regular_base - 0.01 ) {
        return $price;
    }

    // Считаем старую и новую сумму за всю позицию
    $old_total = $regular_base    * $weight_ratio * $qty;
    $new_total = $discounted_base * $weight_ratio * $qty;

    $currency = get_woocommerce_currency_symbol();

    return sprintf(
        '<span class="woocommerce-Price-amount amount"><bdi><s>%s</s>&nbsp;%s&nbsp;<span class="woocommerce-Price-currencySymbol">%s</span></bdi></span>',
        wc_format_decimal( $old_total, 0 ),
        wc_format_decimal( $new_total, 0 ),
        esc_html( $currency )
    );
}
add_filter( 'woocommerce_cart_item_subtotal', 'custom_display_price', 10, 3 );

function so_43922864_add_content() {
	global $product;

	$ugl = $product->get_attribute('pa_uglevody-g');
	$jir = $product->get_attribute('pa_жиры-г');
	$belk = $product->get_attribute('pa_белки-г');
	$kal = $product->get_attribute('pa_energeticheskaya-cen');

	if(!empty($ugl) || !empty($jir) || !empty($belk) || !empty($kal)) : ?>

	<div class="shop-ferma__params shop-ferma__params_pc prod-params">

		<div class="shop-ferma__params-title prod-params__title">Пищевая ценность на 100 грамм</div>

		<div class="shop-ferma__params-list prod-params__list">
			<?php if(!empty($belk)) : ?>
				<div class="shop-ferma__params-item prod-params__item">
					<span>Белки — </span>
					<?php echo $belk; ?>
				</div>
			<?php endif; ?>

			<?php if(!empty($jir)) : ?>
				<div class="shop-ferma__params-item prod-params__item">
					<span>Жиры — </span>
					<?php echo $jir; ?>
				</div>
			<?php endif; ?>

			<?php if(!empty($ugl)) : ?>
				<div class="shop-ferma__params-item prod-params__item">
					<span>Углеводы — </span>
					<?php echo $ugl; ?>
				</div>
			<?php endif; ?>

			<?php if(!empty($kal)) : ?>
				<div class="shop-ferma__params-item prod-params__item">
					<span>Калории — </span>
					<?php echo $kal; ?>
				</div>
			<?php endif; ?>
		</div>

	</div>

	<?php endif;
}
add_action( 'woocommerce_single_product_summary', 'so_43922864_add_content', 45 );

// Удалил старый фильтр
remove_filter( 'the_content', 'display_attributes_after_product_description', 10 );
// Добавил вывод атрибутов в .summary
add_action( 'woocommerce_single_product_summary', 'custom_display_product_attributes_in_summary', 35 );
function custom_display_product_attributes_in_summary() {
	if ( ! is_product() ) return;

	global $product;

	$country = $product->get_attribute('pa_strana');
	$energy  = $product->get_attribute('pa_energeticheskaya-cen');
	$volume  = $product->get_attribute('pa_obyom-ml');
	$weight  = $product->get_attribute('pa_ves-g');
	$sostav  = $product->get_attribute('pa_sostav');
	$usl     = $product->get_attribute('pa_usloviya-hraneniya');
	$srok    = $product->get_attribute('pa_srok-godnosti');
	$mesto   = $product->get_attribute('pa_mesto-proishojdeniya');

	if (
		!$country && !$energy && !$volume && !$weight &&
		!$sostav && !$usl && !$srok && !$mesto
	) return;

	echo '<div class="shop-ferma__attributes">';

	if ($country) echo '<div class="product-attribute"><span class="product-attribute__text">Страна происхождения: </span>' . esc_html($country) . '</div>';
	if ($mesto)   echo '<div class="product-attribute"><span class="product-attribute__text">Место происхождения: </span>' . esc_html($mesto) . '</div>';
	if ($energy)  echo '<div class="product-attribute"><span class="product-attribute__text">Энергетическая ценность на 100 г, кКал: </span>' . esc_html($energy) . '</div>';
	if ($volume)  echo '<div class="product-attribute"><span class="product-attribute__text">Объём, мл: </span>' . esc_html($volume) . '</div>';
	if ($weight)  echo '<div class="product-attribute"><span class="product-attribute__text">Вес, гр: </span>' . esc_html($weight) . '</div>';
	if ($sostav)  echo '<div class="product-attribute"><span class="product-attribute__text">Состав: </span>' . esc_html($sostav) . '</div>';
	if ($usl)     echo '<div class="product-attribute"><span class="product-attribute__text">Условия хранения: </span>' . esc_html($usl) . '</div>';
	if ($srok)    echo '<div class="product-attribute"><span class="product-attribute__text">Срок годности: </span>' . esc_html($srok) . '</div>';

	echo '</div>';
}


function redirect_child_category() {
    if (is_product_category()) {
        $category = get_queried_object();
        $category_id = $category->term_id;

		$category_link = get_category_link($category_id);

		if($_SERVER['REMOTE_ADDR'] == "217.150.75.124") {

			$current_url = strtok("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?');

			if ($category_link !== $current_url && !isset($_SERVER['HTTP_REFERER'])) {
				if (!empty($_SERVER['QUERY_STRING'])) {
					$category_link .= '?' . $_SERVER['QUERY_STRING'];
				}
				wp_redirect($category_link, 301);
				exit;
			}
		}
    }
}
add_action('template_redirect', 'redirect_child_category');

//add_action( 'woocommerce_email', 'ferma_disable_emails' );

function ferma_disable_emails( $email_class ) {
    //remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
    remove_action( 'woocommerce_order_status_pending_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
    remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
    remove_action( 'woocommerce_order_status_failed_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
    //remove_action( 'woocommerce_order_status_failed_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
    //remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );

	//remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
    //remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );

	remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );
}

//add_filter( 'pre_option_woocommerce_hide_out_of_stock_items', 'fix_kulichi_category' );
function fix_kulichi_category( $hide ) {
   if ( is_product_category( 'kulichi' )) {
      $hide = 'no';
   }
   return $hide;
}

function ferma_woocommerce_email_recipient( $recipient, $order, $email ) {
    if ( ! $order || ! is_a( $order, 'WC_Order' ) ) return $recipient;

    $recipient = '';
    return $recipient;
}
add_filter( 'woocommerce_email_recipient_customer_on_hold_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_processing_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_pending_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_on-hold_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_completed_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_cancelled_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_refunded_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_failed_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_podtverjden_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_sobran_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_otgrujen_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_kurer-naznachen_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_zakaz-v-puti_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_zakaz-oplachen_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_picked-up_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_dostavlen_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_otmenen_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_vozvrat_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_otmena-otkaz_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_otmena-otkaz2_order', 'ferma_woocommerce_email_recipient', 10, 3 );

//Moysklad



// CPT "Промокоды"
add_action( 'init', function () {
    register_post_type( 'q_promocode', array(
        'labels' => array(
            'name'          => 'Промокоды Q',
            'singular_name' => 'Промокод Q',
            'add_new'       => 'Добавить промокод',
            'add_new_item'  => 'Добавить промокод',
            'edit_item'     => 'Редактировать промокод',
        ),
        'public'       => false,
        'show_ui'      => true,
        'menu_position'=> 25,
        'menu_icon'    => 'dashicons-tickets-alt',
        'supports'     => array('title'),
    ) );
} );

// Метабоксы
add_action( 'add_meta_boxes', function () {
    add_meta_box(
        'q_promocode_meta',
        'Настройки промокода',
        'q_promocode_meta_box_cb',
        'q_promocode',
        'normal',
        'high'
    );
} );

function q_promocode_meta_box_cb( $post ) {
    $code          = get_post_meta( $post->ID, '_q_code', true );
    $gift_sku      = get_post_meta( $post->ID, '_q_gift_sku', true );
    $discount_type = get_post_meta( $post->ID, '_q_discount_type', true ); // percent|absolute
    $discount_val  = get_post_meta( $post->ID, '_q_discount_val', true );
    $lifetime      = get_post_meta( $post->ID, '_q_lifetime_hours', true );
    $usage_limit   = get_post_meta( $post->ID, '_q_usage_limit', true );
    ?>
        <style>
            .product-card__cart {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .product-card__cart .add_to_cart_button {
                margin-left: auto;
                display: inline-flex;
                justify-content: center;
                align-items: center;
                white-space: nowrap;
            }
            .product-card__cart {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .product-card {
                padding-right: 10px; /* или больше/меньше */
            }
            .add_to_cart_button.shop-ferma__rel-add {
                margin-left: 28px; /* или сколько тебе нужно */
            }
            .product-card__cart .cart__qty {
                margin-right: 32px; /* подбери число под макет */
            }

            /* или, если такого контейнера нет, просто так: */
            .cart__qty {
                margin-right: 20px;
            }
            .product-card__cart .add_to_cart_button {
                margin-left: auto;
                margin-right: 10px; /* сколько нужно – подбери */
                display: inline-flex;
                justify-content: center;
                align-items: center;
                white-space: nowrap;
            }

            /* количество как и раньше */
            .product-card__cart .cart__qty {
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

        </style>
    <p>
        <label>Код (формат Q123):</label><br>
        <input type="text" name="q_code" value="<?php echo esc_attr( $code ); ?>" style="width:100%;">
    </p>

    <p>
        <label>Артикул товара (SKU подарка):</label><br>
        <input type="text" name="q_gift_sku" value="<?php echo esc_attr( $gift_sku ); ?>" style="width:100%;">
    </p>
    <p>
        <label>Тип скидки:</label><br>
        <select name="q_discount_type">
            <option value="percent"  <?php selected($discount_type,'percent'); ?>>Процент, %</option>
            <option value="absolute" <?php selected($discount_type,'absolute'); ?>>Абсолютная цена (руб.)</option>
        </select>
    </p>
    <p>
        <label>Значение скидки (процент или итоговая цена):</label><br>
        <input type="number" step="0.01" name="q_discount_val" value="<?php echo esc_attr( $discount_val ); ?>">
    </p>
    <p>
        <label>Срок действия, часов:</label><br>
        <input type="number" name="q_lifetime_hours" value="<?php echo esc_attr( $lifetime ); ?>">
    </p>
    <p>
        <label>Макс. применений на 1 пользователя (по телефону):</label><br>
        <input type="number" name="q_usage_limit" value="<?php echo esc_attr( $usage_limit ); ?>">
    </p>
    <?php
}

add_action( 'save_post_q_promocode', function ( $post_id ) {
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

    if ( isset($_POST['q_code']) ) {
        $code = strtoupper( trim($_POST['q_code']) );

        // любой код из 1–6 латинских букв/цифр
        if ( preg_match('/^[A-Z0-9]{1,9}$/', $code) ) {
            update_post_meta( $post_id, '_q_code', $code );
        }
    }
    if ( isset($_POST['q_gift_sku']) ) {
        update_post_meta( $post_id, '_q_gift_sku', sanitize_text_field($_POST['q_gift_sku']) );
    }
    if ( isset($_POST['q_discount_type']) ) {
        update_post_meta( $post_id, '_q_discount_type', $_POST['q_discount_type'] === 'absolute' ? 'absolute' : 'percent' );
    }
    if ( isset($_POST['q_discount_val']) ) {
        update_post_meta( $post_id, '_q_discount_val', floatval($_POST['q_discount_val']) );
    }
    if ( isset($_POST['q_lifetime_hours']) ) {
        update_post_meta( $post_id, '_q_lifetime_hours', intval($_POST['q_lifetime_hours']) );
    }
    if ( isset($_POST['q_auto_add_gift']) ) {
        update_post_meta( $post_id, '_q_auto_add_gift', '1' );
    } else {
        delete_post_meta( $post_id, '_q_auto_add_gift' );
    }
    if ( isset($_POST['q_usage_limit']) ) {
        update_post_meta( $post_id, '_q_usage_limit', intval($_POST['q_usage_limit']) );
    }
} );


function q_get_active_promocode() {
    return WC()->session->get( 'q_active_promo' );
}
add_action( 'wp_ajax_check_active_promo', 'handle_check_active_promo' );
add_action( 'wp_ajax_nopriv_check_active_promo', 'handle_check_active_promo' );

function handle_check_active_promo() {
    check_ajax_referer( 'q_promo_nonce', 'nonce' );

    $active_promo = q_get_active_promocode();

    if ( $active_promo ) {
        wp_send_json_success( array(
            'active_promo' => true,
            'promo_code' => $active_promo['code']
        ) );
    } else {
        wp_send_json_success( array(
            'active_promo' => false
        ) );
    }
}
function q_apply_promocode_discount( $promo ) {
    // Сохраняем полную информацию о промокоде в сессии
    WC()->session->set( 'q_active_promo', array(
        'code' => $promo['code'],
        'id' => $promo['id'],
        'discount_type' => $promo['discount_type'],
        'discount_val' => $promo['discount_val'],
        'gift_sku' => $promo['gift_sku']
    ) );

    // Рассчитываем скидку
    $discount_amount = 0;

    if ( $promo['discount_type'] === 'percent' ) {
        $subtotal = WC()->cart->get_subtotal();
        $discount_amount = ( $subtotal * $promo['discount_val'] ) / 100;
    } else {
        $discount_amount = $promo['discount_val'];
    }

    // Применяем скидку как fee (отрицательная плата)
    WC()->cart->add_fee( "Скидка по промокоду {$promo['code']}", -$discount_amount );

    return true;
}
add_action( 'wp_ajax_remove_q_promocode', 'handle_remove_q_promocode' );
add_action( 'wp_ajax_nopriv_remove_q_promocode', 'handle_remove_q_promocode' );

function handle_remove_q_promocode() {
    check_ajax_referer( 'q_promo_nonce', 'nonce' );

    // Удаляем подарки из корзины
    $cart_items = WC()->cart->get_cart();

    foreach ( $cart_items as $cart_item_key => $cart_item ) {
        if ( isset( $cart_item['q_promo_gift'] ) ) {
            WC()->cart->remove_cart_item( $cart_item_key );
        }
    }

    // Удаляем промокод из сессии
    WC()->session->__unset( 'q_active_promo' );

    // Пересчитываем корзину
    WC()->cart->calculate_totals();

    wp_send_json_success( array(
        'message' => 'Промокод удален',
        'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array() )
    ) );
}

add_action( 'woocommerce_before_calculate_totals', 'set_zero_price_for_promo_gifts', 1 );

function set_zero_price_for_promo_gifts( $cart ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
        return;
    }

    foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
        if ( isset( $cart_item['q_promo_gift'] ) && $cart_item['q_promo_gift'] ) {
            $cart_item['data']->set_price( 0 );
        }
    }
}
add_action( 'woocommerce_checkout_update_order_review', 'ferma_apply_q_promo_from_cookie', 10, 1 );


function ferma_apply_q_promo_from_cookie( $posted_data ) {
    // Берём промокод из cookie
    $code = ! empty( $_COOKIE['ferma_promo_code'] )
        ? sanitize_text_field( wp_unslash( $_COOKIE['ferma_promo_code'] ) )
        : '';

    $active = q_get_active_promocode();

    // 1) Куки НЕТ – промо считаем выключенным, чистим сессию и выходим
    if ( ! $code ) {
        if ( $active ) {
            WC()->session->__unset( 'q_active_promo' );
        }
        return;
    }

    // 2) Кука есть, но этот же код уже активен – ничего не делаем,
    // чтобы не дублировать скидку/подарок
    if ( $active && ! empty( $active['code'] ) && strtoupper( $active['code'] ) === strtoupper( $code ) ) {
        return;
    }

    // 3) Пытаемся применить промо
    $result = q_apply_promocode_with_gift( $code );

    if ( is_wp_error( $result ) ) {
        // Чистим сессию
        WC()->session->__unset( 'q_active_promo' );

        // Чистим куку
        setcookie( 'ferma_promo_code', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN );
        if ( SITECOOKIEPATH !== COOKIEPATH ) {
            setcookie( 'ferma_promo_code', '', time() - 3600, SITECOOKIEPATH, COOKIE_DOMAIN );
        }

        // ВАЖНО: добавляем notice, чтобы Woo вернул его в "messages"
        wc_add_notice( $result->get_error_message(), 'error' );

        return;
    }
}

add_action( 'woocommerce_before_checkout_form', function() {
    // Только на checkout и только не в AJAX
    if ( ! is_checkout() || wp_doing_ajax() ) {
        return;
    }

    // posted_data внутри не используется, можно передать пустую строку
    ferma_apply_q_promo_from_cookie( '' );

    // Пересчёт тоталов после возможного применения промо
    if ( WC()->cart ) {
        WC()->cart->calculate_totals();
        WC()->cart->set_session();
    }
}, 5 );
// Сохраняем активный Q-промокод в мета-поля заказа
add_action( 'woocommerce_checkout_create_order', 'ferma_save_qpromo_to_order', 30, 2 );
function ferma_save_qpromo_to_order( WC_Order $order, $data ) {

    // Берём активный промо из сессии (то, что ты кладёшь в q_active_promo)
    $active = q_get_active_promocode();

    $code = '';
    if ( ! empty( $active['code'] ) ) {
        $code = $active['code'];
    } elseif ( ! empty( $_COOKIE['ferma_promo_code'] ) ) {
        // запасной вариант – из куки
        $code = sanitize_text_field( wp_unslash( $_COOKIE['ferma_promo_code'] ) );
    }

    if ( ! $code ) {
        return;
    }

    $code = strtoupper( trim( $code ) );

    // Чтобы не схватить мусор – только формата твоих промо
    if ( ! preg_match( '/^[A-Z0-9]{1,9}$/', $code ) ) {
        return;
    }

    // Сохраняем в мету заказа, откуда потом берём для МойСклада
    $order->update_meta_data( 'q_promocode', $code );
}

add_action( 'wp_ajax_apply_q_promocode', 'handle_apply_q_promocode' );
add_action( 'wp_ajax_nopriv_apply_q_promocode', 'handle_apply_q_promocode' );

function handle_apply_q_promocode() {
    check_ajax_referer( 'q_promo_nonce', 'nonce' );

    $promo_code = sanitize_text_field( $_POST['promo_code'] );

    $result = q_apply_promocode_with_gift( $promo_code );

    if ( is_wp_error( $result ) ) {

        // Добавляем сообщение в стандартный WooCommerce вывод ошибок
        wc_add_notice( $result->get_error_message(), 'error' );

        // Возвращаем JSON для фронта
        wp_send_json_error( array(
            'message'  => $result->get_error_message(),
            'wc_html'  => wc_print_notices( true ) // HTML всех ошибок WC
        ) );
    } else {
        // ОБНОВЛЯЕМ КОРЗИНУ
        WC()->cart->calculate_totals();
        WC()->cart->set_session();

        // ПОЛУЧАЕМ ФРАГМЕНТЫ КОРЗИНЫ
        $fragments = get_cart_fragments();

        wp_send_json_success( array(
            'message' => 'Промокод применен! Подарок добавлен в корзину.',
            'fragments' => $fragments,
            'cart_contents_count' => WC()->cart->get_cart_contents_count(),
            'cart_total' => WC()->cart->get_cart_total()
        ) );
    }
}
add_action( 'wp_ajax_nopriv_wc_print_errors', 'ferma_wc_print_errors' );
add_action( 'wp_ajax_wc_print_errors', 'ferma_wc_print_errors' );

function ferma_wc_print_errors() {
    wp_send_json_success( array(
        'html' => wc_print_notices( true )
    ) );
}

function get_cart_fragments() {
    $fragments = array();

    // Мини-корзина
    ob_start();
    woocommerce_mini_cart();
    $fragments['div.widget_shopping_cart_content'] = '<div class="widget_shopping_cart_content">' . ob_get_clean() . '</div>';

    // Счетчик товаров (если у вас есть такой элемент)
    $fragments['span.cart-contents-count'] = '<span class="cart-contents-count">' . WC()->cart->get_cart_contents_count() . '</span>';

    // Итоговая сумма (если у вас есть такой элемент)
    $fragments['span.cart-total'] = '<span class="cart-total">' . WC()->cart->get_cart_total() . '</span>';

    // Обновляем всю секцию корзины если есть
    ob_start();
    echo '<div class="cart-update-section">';
    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
        $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

        if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 ) {
            echo '<div class="cart-item">';
            echo $_product->get_name() . ' × ' . $cart_item['quantity'];
            echo '</div>';
        }
    }
    echo '</div>';
    $fragments['div.cart-update-section'] = ob_get_clean();

    return $fragments;
}
add_filter('woocommerce_add_cart_item_data', function($cart_item_data, $product_id) {

    if (isset($cart_item_data['q_promo_gift'])) {
        $cart_item_data['q_promo_gift'] = true;
    }

    if (isset($cart_item_data['q_promo_code'])) {
        $cart_item_data['q_promo_code'] = sanitize_text_field($cart_item_data['q_promo_code']);
    }

    return $cart_item_data;
}, 10, 2);
add_filter('woocommerce_get_cart_item_from_session', function($item, $values) {

    if (isset($values['q_promo_gift'])) {
        $item['q_promo_gift'] = true;
    }

    if (isset($values['q_promo_code'])) {
        $item['q_promo_code'] = sanitize_text_field($values['q_promo_code']);
    }

    return $item;
}, 10, 2);
function q_reset_promo_after_checkout( $order_id ) {
    // Сбрасываем активный промокод
    WC()->session->__unset( 'q_active_promo' );

    // Убираем cookie промокода, чтобы он не применялся автоматически
    if ( isset($_COOKIE['ferma_promo_code']) ) {
        setcookie('ferma_promo_code', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
        if ( SITECOOKIEPATH !== COOKIEPATH ) {
            setcookie('ferma_promo_code', '', time() - 3600, SITECOOKIEPATH, COOKIE_DOMAIN);
        }
    }
}

function q_apply_promocode_with_gift( $promo_code ) {
    $promo = q_get_local_promocode( $promo_code );

    if ( ! $promo ) {
        return new WP_Error(
            'invalid_promo',
            'Промокод с истекшим сроком, попробуйте ввести другой промокод'
        );
    }

    // ПРОВЕРКА ЛИМИТА — ИМЕННО В МОМЕНТ ПРИМЕНЕНИЯ
    if ( ! q_can_use_promo_for_user( $promo ) ) {
        return new WP_Error(
            'usage_limit',
            'Промокод уже использован максимально допустимое количество раз'
        );
    }

    if ( ! empty( $promo['gift_sku'] ) ) {
        $gift_product_id = wc_get_product_id_by_sku( $promo['gift_sku'] );

        if ( ! $gift_product_id ) {
            return new WP_Error( 'gift_error', 'Товар-подарок не найден' );
        }

        // Чистим прошлые подарки
        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            if ( isset( $cart_item['q_promo_gift'] ) ) {
                WC()->cart->remove_cart_item( $cart_item_key );
            }
        }

        $cart_item_data = array(
            'q_promo_gift' => true,
            'q_promo_code' => $promo_code,
            'custom_price' => 0,
        );

        $added = WC()->cart->add_to_cart( $gift_product_id, 1, 0, array(), $cart_item_data );

        if ( ! $added ) {
            return new WP_Error( 'gift_error', 'Не удалось добавить подарок в корзину' );
        }

        WC()->cart->calculate_totals();
        WC()->cart->set_session();

    }
    $result = q_apply_promocode_discount( $promo );

    if ( ! is_wp_error( $result ) ) {
        // считаем использование промокода
        q_mark_promo_used_for_user( $promo );
    }

    return $result;
}
add_action( 'wp_enqueue_scripts', function() {
    // Всплывашка
    wp_enqueue_script(
        'q-promo-toast',
        get_template_directory_uri() . '/assets/js/q-promo-toast1.js',
        array('jquery'),
        '1.0.0',
        true
    );

    wp_enqueue_script(
        'q-promocodes-js',
        get_template_directory_uri() . '/assets/js/promocodes11.js',
        array('jquery', 'q-promo-toast'),
        filemtime( get_template_directory() . '/assets/js/promocodes11.js' ),
        true
    );



    wp_localize_script( 'q-promocodes-js', 'q_promo_vars', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'q_promo_nonce' )
    ) );
} );

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'product-card-qty',
        get_template_directory_uri() . '/assets/css/product-card-qty.css',
        [],
        '1.0'
    );
});
add_filter( 'woocommerce_cart_item_name', 'ferma_cart_gift_label_under_name', 10, 3 );
function ferma_cart_gift_label_under_name( $name, $cart_item, $cart_item_key ) {

    // Наш подарок по промокоду
    if ( empty( $cart_item['q_promo_gift'] ) ) {
        return $name;
    }

    /** @var WC_Product $product */
    $product = $cart_item['data'];
    $qty     = max( 1, (int) $cart_item['quantity'] );

    // Берём базовую цену (до обнуления)
    $regular_price = (float) $product->get_regular_price();
    if ( $regular_price <= 0 ) {
        $regular_price = (float) $product->get_price();
    }

    // Если вообще нет адекватной цены — только "В подарок"
    if ( $regular_price <= 0 ) {
        return $name . '<div class="ferma-gift-info"><span class="ferma-gift-label">В подарок</span></div>';
    }

    // Старая сумма за всё количество
    $old_line_total = wc_price( $regular_price * $qty );

    // Без sprintf — безопасно
    $gift_html =
        '<div class="ferma-gift-info">
            <span class="ferma-gift-old-price"><del>' . $old_line_total . '</del></span>
            <span class="ferma-gift-label">В подарок</span>
        </div>';

    return $name . $gift_html;
}

add_filter( 'woocommerce_cart_item_subtotal', 'ferma_gift_subtotal_replace', 10, 3 );
function ferma_gift_subtotal_replace( $subtotal, $cart_item, $cart_item_key ) {

    // В чекауте НИЧЕГО не меняем – пусть будет стандартный 0 ₽
    if ( is_checkout() ) {
        return $subtotal;
    }

    // Не подарок — не трогаем
    if ( empty( $cart_item['q_promo_gift'] ) ) {
        return $subtotal;
    }

    /** @var WC_Product $product */
    $product = $cart_item['data'];
    $qty     = max( 1, (int) $cart_item['quantity'] );

    $regular_price = (float) $product->get_regular_price();
    if ( $regular_price <= 0 ) {
        $regular_price = (float) $product->get_price();
    }

    if ( $regular_price <= 0 ) {
        return '<div class="ferma-gift-subtotal">
                    <span class="ferma-gift-label">В подарок</span>
                </div>';
    }

    $old_line_total = wc_price( $regular_price * $qty );

    return sprintf(
        '<div class="ferma-gift-subtotal">
            <span class="ferma-gift-old"><del>%s</del></span>
            <span class="ferma-gift-label">В подарок</span>
        </div>',
        $old_line_total
    );
}
add_filter( 'woocommerce_cart_item_data_to_restore', function( $item_data, $cart_item ) {
    if ( isset( $cart_item['q_promo_gift'] ) ) {
        $item_data['q_promo_gift'] = $cart_item['q_promo_gift'];
    }
    return $item_data;
}, 10, 2 );
add_filter( 'woocommerce_add_cart_item_data', function( $cart_item_data, $product_id, $variation_id ) {
    if ( isset( $cart_item_data['q_promo_gift'] ) ) {
        $cart_item_data['q_promo_gift'] = true;
    }
    return $cart_item_data;
}, 10, 3 );
function q_get_local_promocode( $code ) {
    $code = strtoupper( trim($code) );

    if ( ! preg_match('/^[A-Z0-9]{1,9}$/', $code) ) {
        return false;
    }

    $q = new WP_Query([
        'post_type'      => 'q_promocode',
        'posts_per_page' => 1,
        'meta_query'     => [
            [
                'key'   => '_q_code',
                'value' => $code,
            ],
        ],
        'post_status'    => 'publish',
    ]);

    if ( ! $q->have_posts() ) {
        return false;
    }

    $post = $q->posts[0];
    $id   = $post->ID;

    $promo = [
        'id'            => $id,
        'code'          => get_post_meta($id, '_q_code', true),
        'gift_sku'      => get_post_meta($id, '_q_gift_sku', true),
        'discount_type' => get_post_meta($id, '_q_discount_type', true),
        'discount_val'  => (float) get_post_meta($id, '_q_discount_val', true),
        'lifetime'      => (int) get_post_meta($id, '_q_lifetime_hours', true),
        'usage_limit'   => (int) get_post_meta($id, '_q_usage_limit', true),
        'created'       => get_post_time('U', true, $id),
    ];

    // срок действия
    if ( $promo['lifetime'] > 0 && ( time() - $promo['created'] ) > $promo['lifetime'] * 3600 ) {
        return false;
    }

    // просто записываем телефон, не проверяя лимиты
    $phone = '';
    if ( is_user_logged_in() ) {
        $customer = WC()->customer;
        if ( $customer ) {
            $phone = preg_replace('/\D+/', '', $customer->get_billing_phone() );
        }
    }
    $promo['phone'] = $phone;

    return $promo;
}
function q_mark_promo_used_for_user( array $promo ): void {
    $usage_limit = (int) ( $promo['usage_limit'] ?? 0 );
    if ( $usage_limit <= 0 ) {
        // Лимит не задан — ничего не считаем
        return;
    }

    if ( ! is_user_logged_in() ) {
        // Для гостей сейчас не считаем (как и в q_can_use_promo_for_user)
        return;
    }

    $phone = $promo['phone'] ?? '';
    if ( ! $phone ) {
        // Если нет телефона, не к чему привязаться
        return;
    }

    $user_id  = get_current_user_id();
    $meta_key = '_q_promo_used_' . $promo['id'] . '_' . $phone;
    $used     = (int) get_user_meta( $user_id, $meta_key, true );

    update_user_meta( $user_id, $meta_key, $used + 1 );
}

function q_can_use_promo_for_user( array $promo ): bool {
    $usage_limit = (int) ( $promo['usage_limit'] ?? 0 );
    if ( $usage_limit <= 0 ) {
        // Лимит не задан — без ограничений
        return true;
    }

    if ( ! is_user_logged_in() ) {
        // Если гостей тоже надо считать — дописать отдельную схему, сейчас пропускаем
        return true;
    }

    $phone = $promo['phone'] ?? '';
    if ( ! $phone ) {
        return true;
    }

    $user_id = get_current_user_id();
    $meta_key = '_q_promo_used_' . $promo['id'] . '_' . $phone;
    $used     = (int) get_user_meta( $user_id, $meta_key, true );

    return $used < $usage_limit;
}

add_action( 'wp_ajax_update_cart_qty', 'theme_update_cart_qty' );
add_action( 'wp_ajax_nopriv_update_cart_qty', 'theme_update_cart_qty' );

function theme_update_cart_qty() {
    check_ajax_referer( 'update_cart_qty', 'nonce' );

    $cart_item_key = isset($_POST['cart_item_key'])
        ? wc_clean( wp_unslash( $_POST['cart_item_key'] ) )
        : '';

    $product_id = isset($_POST['product_id'])
        ? absint( $_POST['product_id'] )
        : 0;

    $qty = isset($_POST['qty'])
        ? wc_format_decimal( wp_unslash( $_POST['qty'] ) )
        : 0;

    $qty = (float) $qty;

    if ( ! WC()->cart ) {
        wp_send_json_error( array( 'message' => 'Cart not initialized' ) );
    }

    // --- НОВЫЙ БЛОК ПОИСКА СТРОКИ КОРЗИНЫ ---
    $line_key = '';

    // 1) Если пришёл cart_item_key и он реально есть в корзине — используем его
    if ( $cart_item_key && isset( WC()->cart->cart_contents[ $cart_item_key ] ) ) {
        $line_key = $cart_item_key;
    }
    // 2) Иначе, если пришёл product_id — ищем по product_id в корзине
    elseif ( $product_id ) {
        foreach ( WC()->cart->get_cart() as $key => $item ) {
            if ( (int) $item['product_id'] === (int) $product_id ) {
                $line_key = $key;
                break;
            }
        }
    }

    // 3) Если так и не нашли — отдаем ошибку
    if ( ! $line_key ) {
        wp_send_json_error( array( 'message' => 'Cart item not found' ) );
    }

    // 4) Применяем количество
    if ( $qty <= 0 ) {
        WC()->cart->remove_cart_item( $line_key );
    } else {
        WC()->cart->set_quantity( $line_key, $qty, true );
    }

    // 5) Отдаём фрагменты
    WC_AJAX::get_refreshed_fragments();
    wp_die();
}

add_filter( 'woocommerce_loop_add_to_cart_link', 'ferma_loop_add_to_cart_with_qty', 10, 3 );
function ferma_loop_add_to_cart_with_qty( $button, $product, $args ) {

    if ( is_admin() ) {
        return $button;
    }

    $product_id = $product->get_id();
    $is_weighted = (get_field( "razbivka_vesa", $product_id ) == 'да');

    // Для каталога всегда показываем целые числа
    $display_qty = 1;
    // Реальное количество для корзины
    $cart_qty = $is_weighted ? 0.1 : 1;

    ob_start();
    ?>
    <div class="product-card__cart">
        <div class="cart__qty"
             data-product_id="<?php echo esc_attr( $product_id ); ?>"
             data-is_weighted="<?php echo $is_weighted ? '1' : '0'; ?>"
             data-step="<?php echo esc_attr( $step ); ?>"
             data-current_qty="<?php echo esc_attr( $display_qty ); ?>"
             data-max_qty="<?php echo esc_attr( $product->get_max_purchase_quantity() ); ?>">



            <button type="button"
                    class="cart__qty-btn cart__qty-btn--minus is-disabled"
                    aria-label="<?php esc_attr_e( 'Уменьшить количество', 'woocommerce' ); ?>">
                –
            </button>

            <span class="cart__qty-val">
                <?php echo esc_html( $display_qty ); ?>
            </span>

            <button type="button"
                    class="cart__qty-btn cart__qty-btn--plus"
                    aria-label="<?php esc_attr_e( 'Увеличить количество', 'woocommerce' ); ?>">
                +
            </button>
            <style>
                .cart__qty {
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    margin-right: 15px;
                }

                .cart__qty-val {
                    min-width: 30px;
                    text-align: center;
                    font-weight: 600;
                    font-size: 16px;
                }

                .cart__qty-btn {
                    width: 32px;
                    height: 32px;
                    border-radius: 6px;
                    border: 1px solid #d0d0d0;
                    font-size: 16px;
                    line-height: 1;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: #fff;
                }

                .cart__qty-btn--plus {
                    background: #4fbd01;
                    border-color: #4fbd01;
                    color: #fff;
                    font-weight: bold;
                }

                .cart__qty-btn--minus {
                    color: #444;
                    font-weight: bold;
                }

                .cart__qty-btn.is-disabled {
                    opacity: 0.4;
                    cursor: default;
                    pointer-events: none;
                }

                .product-card__cart {
                    display: flex;
                    align-items: center;
                    justify-content: flex-start;
                }

                .added_to_cart {
                    display: none !important;
                }

                .product-in-cart .add_to_cart_button {
                    background: #cccccc !important;
                    border-color: #cccccc !important;
                    cursor: default;
                }
            </style>
        </div>
        <?php
        $button = update_cart_button_quantity( $button, $cart_qty, $product_id );
        echo $button;
        ?>
    </div>
    <?php

    return ob_get_clean();
}
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script(
        'cart-validation',
        get_template_directory_uri() . '/js/cart-validation.js',
        array('jquery'),
        '1.0.0',
        true
    );

    wp_localize_script( 'cart-validation', 'theme_qty', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'update_cart_qty' ),
    ) );
});


// Добавьте эту новую функцию в functions.php
function update_cart_button_quantity( $button, $quantity, $product_id ) {
    // Обновляем data-quantity атрибут
    if ( preg_match( '/data-quantity=["\']([^"\']*)["\']/', $button ) ) {
        $button = preg_replace(
            '/(data-quantity=["\'])([^"\']*)(["\'])/',
            '$1' . esc_attr( $quantity ) . '$3',
            $button
        );
    } else {
        $button = preg_replace(
            '/<a\s+/',
            '<a data-quantity="' . esc_attr( $quantity ) . '" ',
            $button,
            1
        );
    }

    // Обновляем URL параметр quantity
    $button = preg_replace(
        '/(href=["\'])([^"\']*add-to-cart=' . $product_id . '[^"\']*)(["\'])/',
        '$1$2&quantity=' . $quantity . '$3',
        $button
    );

    return $button;
}


add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_script(
        'theme-delivery-address',
        get_template_directory_uri() . '/assets/js/delivery-address.js',
        array( 'jquery' ),
        '1.0',
        true
    );
} );

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'catalog-qty',
        get_template_directory_uri() . '/assets/css/catalog-qty.css',
        [],
        '1.0',
        'all'
    );
});

function enqueue_cart_validation_script() {
    if (is_product() || is_shop() || is_product_category()) {
        wp_enqueue_script(
            'cart-validation',
            get_template_directory_uri() . '/js/cart-validation.js',
            array('jquery'),
            '1.0.0',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_cart_validation_script');

add_filter('woocommerce_add_to_cart_validation', 'validate_stock_before_add', 10, 3);

function validate_stock_before_add( $passed, $product_id, $quantity ) {
    // Если уже кто-то завалил валидацию – выходим
    if ( ! $passed ) {
        return false;
    }

    $product = wc_get_product( $product_id );
    if ( ! $product ) {
        return $passed;
    }

    // Весовые товары (твоя разбивка) – вообще не проверяем руками, доверяем Woo
    if ( get_field( 'razbivka_vesa', $product_id ) === 'да' ) {
        return $passed;
    }

    // Стандартная проверка: товар в наличии?
    if ( ! $product->is_in_stock() ) {
        wc_add_notice(
            sprintf( 'Товар "%s" отсутствует на складе', $product->get_name() ),
            'error'
        );
        return false;
    }

    // Для товаров с управлением остатками используем штатную логику Woo
    if ( $product->managing_stock() ) {
        // has_enough_stock сам учитывает null, backorders и т.д.
        if ( ! $product->has_enough_stock( $quantity ) ) {
            $stock_quantity = $product->get_stock_quantity();

            wc_add_notice(
                sprintf(
                    'Недостаточно товара "%s" на складе. Доступно: %s.',
                    $product->get_name(),
                    $stock_quantity !== null ? $stock_quantity : 0
                ),
                'error'
            );
            return false;
        }
    }

    return $passed;
}
// Разрешаем десятичное количество для весовых товаров при добавлении в корзину
add_filter( 'woocommerce_add_to_cart_validation', 'ferma_allow_decimal_qty_for_weighted', 10, 5 );
function ferma_allow_decimal_qty_for_weighted( $passed, $product_id, $quantity, $variation_id = 0, $variations = array() ) {

    // если это НЕ наш весовой товар — не трогаем стандартную валидацию
    if ( get_post_meta( $product_id, '_is_weighted', true ) != '1' ) {
        return $passed;
    }

    // приводим к float и валидируем «по-своему»
    $qty = floatval( $quantity );

    // минимальное количество 0.1, дальше шаг ты уже держишь на фронте
    if ( $qty <= 0 ) {
        return false;
    }

    // здесь можно добавить свою проверку остатка, если нужно
    // if ( $qty > ferma_get_stock_in_kg( $product_id ) ) { ... }

    return true; // не блочим добавление, даже если количество дробное
}


add_action( 'wp_enqueue_scripts', 'ferma_enqueue_catalog_price_script' );
function ferma_enqueue_catalog_price_script() {

    // Если нужно только в каталоге:
    if ( ! ( is_shop() || is_product_category() || is_product_tag() ) ) {
        return;
    }

    wp_enqueue_script(
        'ferma-catalog-price', // handle
        get_template_directory_uri() . '/assets/js/ferma-catalog-price4.js',
        array( 'jquery' ),     // зависимости
        '1.0.0',
        true                   // в футере
    );
}
// Добавляем колонку "Осталось" в список промокодов
add_filter( 'manage_q_promocode_posts_columns', function( $columns ) {
    $new = [];

    // Вставим нашу колонку после заголовка (Title)
    foreach ( $columns as $key => $label ) {
        $new[ $key ] = $label;

        if ( 'title' === $key ) {
            $new['q_promo_time_left'] = 'Осталось';
        }
    }

    return $new;
} );

// Заполняем колонку "Осталось"
add_action( 'manage_q_promocode_posts_custom_column', function( $column, $post_id ) {
    if ( 'q_promo_time_left' !== $column ) {
        return;
    }

    $lifetime_hours = (int) get_post_meta( $post_id, '_q_lifetime_hours', true );

    // Без срока действия
    if ( $lifetime_hours <= 0 ) {
        echo '<span class="q-promo-no-expire">Без срока</span>';
        return;
    }

    // Время создания промо (UTC)
    $created_ts = get_post_time( 'U', true, $post_id );
    $expires_ts = $created_ts + $lifetime_hours * 3600;

    // Текущее время (UTC)
    $now  = current_time( 'timestamp', true );
    $diff = $expires_ts - $now;

    if ( $diff <= 0 ) {
        echo '<span class="q-promo-expired">Истёк</span>';
        return;
    }

    // Рисуем "заглушку" + передаём diff в секундах в data-атрибут
    echo '<span 
            class="q-promo-countdown q-promo-active" 
            data-seconds-left="' . esc_attr( $diff ) . '"
          ></span>';

}, 10, 2 );
// Динамический обратный отсчёт в списке q_promocode
add_action( 'admin_footer-edit.php', function () {
    $screen = get_current_screen();
    if ( ! $screen || $screen->post_type !== 'q_promocode' ) {
        return;
    }
    ?>
    <style>
        .column-q_promo_time_left {
            width: 120px;
        }
        .q-promo-active {
            color: #2e7d32; /* зелёный */
            font-weight: 600;
        }
        .q-promo-expired {
            color: #b71c1c;
            font-weight: 600;
        }
        .q-promo-no-expire {
            color: #555;
        }
    </style>
    <script>
        (function () {
            function formatTime(seconds) {
                if (seconds <= 0) {
                    return 'Истёк';
                }

                var hours = seconds / 3600;

                // Больше или равно часу — показываем в часах с одним знаком после запятой
                if (hours >= 1) {
                    var hStr = hours.toFixed(1).replace('.', ',');
                    return hStr + ' ч';
                }

                // Меньше часа — показываем в минутах
                var mins = Math.floor(seconds / 60);
                if (mins < 1) mins = 1;
                return mins + ' мин';
            }

            function tick() {
                var nodes = document.querySelectorAll('.q-promo-countdown[data-seconds-left]');
                nodes.forEach(function (el) {
                    var sec = parseInt(el.getAttribute('data-seconds-left'), 10);
                    if (isNaN(sec)) {
                        return;
                    }

                    if (sec <= 0) {
                        el.textContent = 'Истёк';
                        el.classList.remove('q-promo-active');
                        el.classList.add('q-promo-expired');
                        el.removeAttribute('data-seconds-left');
                        return;
                    }

                    el.textContent = formatTime(sec);
                    el.setAttribute('data-seconds-left', sec - 1);
                });
            }

            document.addEventListener('DOMContentLoaded', function () {
                // первичный рендер
                tick();
                // тикаем каждую секунду
                setInterval(tick, 1000);
            });
        })();
    </script>
    <?php
});

// Форматирование оставшегося времени: "2 д 3 ч", "5 ч 20 мин", "15 мин"
function q_promocode_format_time_left( int $seconds ): string {
    $days  = floor( $seconds / DAY_IN_SECONDS );
    $hours = floor( ( $seconds % DAY_IN_SECONDS ) / HOUR_IN_SECONDS );
    $mins  = floor( ( $seconds % HOUR_IN_SECONDS ) / MINUTE_IN_SECONDS );

    $parts = [];

    if ( $days > 0 ) {
        $parts[] = $days . ' д';
    }
    if ( $hours > 0 ) {
        $parts[] = $hours . ' ч';
    }
    if ( $days === 0 && $mins > 0 ) {
        // минуты показываем только если дней нет (чтоб не раздувать строку)
        $parts[] = $mins . ' мин';
    }

    if ( empty( $parts ) ) {
        // На всякий случай, если осталось меньше минуты
        return 'меньше 1 мин';
    }

    return implode( ' ', $parts );
}
add_action( 'send_headers', function() {
    if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
        nocache_headers();
    }
} );

function fas_log($msg) {
    // Путь к файлу лога
    $file = ABSPATH . 'wp-content/fasovka.log';

    // Если файла нет – пытаемся создать
    if (!file_exists($file)) {
        // Пробуем создать пустой файл
        @file_put_contents($file, "");
    }

    // Пишем в лог
    @file_put_contents(
        $file,
        date('[d-M-Y H:i:s] ') . $msg . "\n",
        FILE_APPEND | LOCK_EX
    );
}

add_action( 'all', function( $tag ) {
    if (strpos($tag, 'wms') !== false) {
        error_log("WMS_HOOK: " . $tag);
    }
});
add_action('rest_api_init', function () {
    register_rest_route('fdv/v1', '/ms-product/', [
        'methods'  => 'POST',
        'callback' => 'fdv_mysklad_webhook_handler',
        'permission_callback' => '__return_true',
    ]);
});
function fdv_mysklad_webhook_handler($request) {

    $data = $request->get_json_params();

    fas_log("WEBHOOK PRODUCT RECEIVED");

    if (empty($data['id'])) {
        fas_log("нет ID");
        return;
    }

    // WooCommerce product_id здесь есть (WooMS создаёт mapping)
    $product_id = wc_get_product_id_by_sku($data['code']);
    // или по uuid если у тебя есть таблица соответствия

    if (!$product_id) {
        fas_log("не найден product_id для МС товара {$data['code']}");
        return;
    }

    // Вытаскиваем фасовку:
    $fasovka = null;

    if (!empty($data['attributes'])) {
        foreach ($data['attributes'] as $attr) {
            if ($attr['name'] === 'Фасовка') {
                $fasovka = trim($attr['value']);
            }
        }
    }

    if (!$fasovka) {
        fas_log("нет фасовки для product_id=$product_id");
        return;
    }

    // Обновляем ACF:
    update_field('cvet_fasovka', $fasovka, $product_id);

    fas_log("Фасовка обновлена: product_id=$product_id → $fasovka");

    return ['status' => 'ok'];
}

add_filter('wms_attribute_before_update', function($attr, $all, $product_id){

    fas_log("ATTR HOOK: product_id={$product_id}");
    fas_log("ATTR HOOK attr label={$attr['label']} value={$attr['value']}");

    return $attr;

}, 5, 3);

// Маппинг категорий/подкатегорий → базовый вес (аналог weightedList из RN)
$FERMA_WEIGHTED_LIST = [
    [
        'name'  => 'Орехи, сухофрукты, снеки',
        'weight'=> 0.5,
        'array' => [
            [ 'name' => 'Цукаты',              'weight' => 0.5 ],
            [ 'name' => 'Снеки',               'weight' => 0.5 ],
            [ 'name' => 'Семечки и семена',    'weight' => 0.5 ],
            [ 'name' => 'Орехи',               'weight' => 0.5 ],
            [ 'name' => 'Сухофрукты',          'weight' => 0.5 ],
        ],
    ],
    [
        'name'  => 'Чай, травы и дикоросы',
        'array' => [
            [ 'name' => 'Ягоды плоды',         'weight' => 1 ],
        ],
    ],
    [
        'name'  => 'Сладости и десерты',
        'array' => [
            [ 'name' => 'Зефир',               'weight' => 0.5 ],
            [ 'name' => 'Конфеты',             'weight' => 0.5 ],
            [ 'name' => 'Торты',               'weight' => 0.5 ],
            [ 'name' => 'Пирожные и десерты',  'weight' => 0.5 ],
        ],
    ],
    [
        'name'  => 'Домашняя консервация',
        'array' => [
            [ 'name' => 'Соленья бочковые',    'weight' => 1 ],
        ],
    ],
    [
        'name'  => 'Колбасные изделия',
        'weight'=> 0.5,
        'array' => [
            [ 'name' => 'Вареные колбасы',         'weight' => 0.5 ],
            [ 'name' => 'Сосиски и сардельки',     'weight' => 0.5 ],
            [ 'name' => 'Паштеты',                 'weight' => 0.5 ],
            [ 'name' => 'Полукопченые колбасы',    'weight' => 0.5 ],
            [ 'name' => 'Сырокопченые изделия',    'weight' => 0.5 ],
            [ 'name' => 'Варено-копченые изделия', 'weight' => 0.5 ],
            [ 'name' => 'Сыровяленые изделия',     'weight' => 0.5 ],
            [ 'name' => 'Запеченные, жареные изделия', 'weight' => 0.5 ],
        ],
    ],
    [
        'name'  => 'Молочная продукция',
        'array' => [
            [ 'name' => 'Масло сливочное',         'weight' => 0.5 ],
            [ 'name' => 'Сырники, творожные десерты', 'weight' => 1 ],
        ],
    ],
    [
        'name'  => 'Овощи, фрукты, ягоды, грибы',
        'array' => [
            [ 'name' => 'Ягода',                   'weight' => 0.5 ],
        ],
    ],
    [
        'name'  => 'Мясо и рыба',
        'weight'=> 1,
        'array' => [
            [ 'name' => 'Для шашлыка и гриля', 'weight' => 1 ],
            [ 'name' => 'Свинина и говядина', 'weight' => 1 ],
            [ 'name' => 'Мясо кролика',       'weight' => 1 ],
            [ 'name' => 'Рыба и морепродукты','weight' => 1 ],
            [ 'name' => 'Мясо птицы',         'weight' => 1 ],
        ],
    ],
    [
        'name'  => 'Мясные деликатесы',
        'weight'=> 0.5,
        'array' => [
            [ 'name' => 'Рулеты фермерские',          'weight' => 0.5 ],
            [ 'name' => 'Копченые, сырокопченые изделия','weight' => 0.5 ],
            [ 'name' => 'Колбасы фермерские',         'weight' => 0.5 ],
            [ 'name' => 'Холодец, зельц, рулька',     'weight' => 0.5 ],
            [ 'name' => 'Барбекю',                    'weight' => 0.5 ],
            [ 'name' => 'Вареные, варено-копченые изделия','weight' => 0.5 ],
            [ 'name' => 'Ветчина фермерская',         'weight' => 0.5 ],
            [ 'name' => 'Сало',                       'weight' => 0.5 ],
        ],
    ],
    [
        'name'  => 'Домашние и ремесленные сыры',
        'weight'=> 0.3,
        'array' => [
            [ 'name' => 'Молодые и рассольные сыры', 'weight' => 0.3 ],
            [ 'name' => 'Твердые и полутвердые сыры','weight' => 0.3 ],
            [ 'name' => 'Сыры с плесенью',           'weight' => 0.3 ],
            [ 'name' => 'Творожные сыры',            'weight' => 0.3 ],
            [ 'name' => 'Сыры из козьего молока',    'weight' => 0.3 ],
        ],
    ],
    [
        'name'  => 'Полуфабрикаты домашние',
        'weight'=> 1,
        'array' => [
            [ 'name' => 'Блинчики',                       'weight' => 1 ],
            [ 'name' => 'Сырники',                        'weight' => 1 ],
            [ 'name' => 'Готовые блюда',                  'weight' => 1 ],
            [ 'name' => 'Рыбные полуфабрикаты',           'weight' => 1 ],
            [ 'name' => 'Котлеты, биточки',               'weight' => 1 ],
            [ 'name' => 'Рулеты',                         'weight' => 1 ],
            [ 'name' => 'Полуфабрикаты из мяса, мяса птицы','weight' => 1 ],
            [ 'name' => 'Выпечка, лапша',                 'weight' => 1 ],
            [ 'name' => 'Пироги для духовки',             'weight' => 1 ],
            [ 'name' => 'Бульоны замороженные',           'weight' => 1 ],
            [ 'name' => 'Пельмени, манты, хинкали',       'weight' => 1 ],
            [ 'name' => 'Вареники',                       'weight' => 1 ],
            [ 'name' => 'Полуфабрикаты из овощей',        'weight' => 1 ],
            [ 'name' => 'Фарш',                           'weight' => 1 ],
        ],
    ],
];
/**
 * Аналог fillWeighed из мобилки.
 *
 * @param string $pathName  Строка из МойСклад вида "Мясо и рыба/Свинина и говядина"
 * @return array{sign:string, weight:float|null}
 */
function ferma_fill_weighed_from_path( $pathName ) {
    global $FERMA_WEIGHTED_LIST;

    if ( empty( $pathName ) ) {
        return [ 'sign' => 'шт', 'weight' => null ];
    }

    $normalizedPath = mb_strtolower( $pathName, 'UTF-8' );

    foreach ( $FERMA_WEIGHTED_LIST as $category ) {
        $catName = mb_strtolower( $category['name'], 'UTF-8' );

        // Если в pathName есть название категории
        if ( mb_strpos( $normalizedPath, $catName ) !== false ) {

            // Сначала пытаемся найти подкатегорию
            if ( ! empty( $category['array'] ) && is_array( $category['array'] ) ) {
                foreach ( $category['array'] as $sub ) {
                    $subName = mb_strtolower( $sub['name'], 'UTF-8' );
                    if ( mb_strpos( $normalizedPath, $subName ) !== false ) {
                        $w = (float) $sub['weight'];
                        if ( $w === 1.0 ) {
                            return [ 'sign' => 'кг', 'weight' => 1.0 ];
                        }
                        return [ 'sign' => 'г', 'weight' => $w ];
                    }
                }
            }

            // Если подкатегории не совпали, но у самой категории есть weight
            if ( isset( $category['weight'] ) ) {
                $w = (float) $category['weight'];
                if ( $w === 1.0 ) {
                    return [ 'sign' => 'кг', 'weight' => 1.0 ];
                }
                return [ 'sign' => 'г', 'weight' => $w ];
            }
        }
    }

    // Ничего не нашли — считаем штучным
    return [ 'sign' => 'шт', 'weight' => null ];
}

add_filter( 'wms_assortment_ms_array', 'ferma_debug_ms_assortment', 10, 2 );
function ferma_debug_ms_assortment( $ms_array, $product_id ) {
    if ( (int) $product_id === 4497 ) {
        fas_log( 'MS_ASSORTMENT_RAW: ' . print_r( $ms_array, true ) );
    }
    return $ms_array;
}
// Устанавливаем фасовку на основе штрихкода МойСклад
add_filter( 'wms_assortment_ms_array', 'fas_set_fasovka_from_ms_item', 20, 4 );
function fas_set_fasovka_from_ms_item( $ms_item, $product_id, $data, $ctx ) {
    fas_log( "FAS: intercepted assortment item" );
    fas_log( "FAS: incoming product_id=" . var_export( $product_id, true ) );

    // 1. Резолвим product_id через SKU, если не пришёл
    if ( empty( $product_id ) || ! is_numeric( $product_id ) ) {
        $sku = '';
        if ( ! empty( $ms_item['code'] ) ) {
            $sku = (string) $ms_item['code'];
        }

        if ( $sku !== '' ) {
            $resolved_id = wc_get_product_id_by_sku( $sku );
            fas_log( "FAS: trying to resolve product_id by SKU={$sku}, resolved_id=" . var_export( $resolved_id, true ) );
            if ( $resolved_id ) {
                $product_id = (int) $resolved_id;
            }
        }
    }

    fas_log( "FAS: final product_id=" . var_export( $product_id, true ) );

    if ( empty( $product_id ) || ! is_numeric( $product_id ) ) {
        fas_log( "FAS: product_id не определён — выходим" );
        return $ms_item;
    }

    // 2. Забираем флаги из МойСклад
    $has_ms_weighed = array_key_exists( 'weighed', $ms_item );
    $ms_weighed     = $has_ms_weighed ? (bool) $ms_item['weighed'] : false;
    $pathName       = isset( $ms_item['pathName'] ) ? (string) $ms_item['pathName'] : '';

    fas_log( "FAS: MS weighed flag = " . var_export( $ms_weighed, true ) );
    fas_log( "FAS: MS pathName = " . $pathName );

    // 3. Аналог fillWeighed — только для определения "разбивки"
    $unitInfo = ferma_fill_weighed_from_path( $pathName );
    fas_log( "FAS: unitInfo from path = " . print_r( $unitInfo, true ) );

    // 4. Тип фасовки: ДОВЕРЯЕМ ТОЛЬКО МойСклад
    //    если weighed = true → весовая; weighed = false → штучная
    $is_weight = $ms_weighed;
    fas_log( "FAS: resolved is_weight from MS only = " . var_export( $is_weight, true ) );

    if ( $is_weight ) {

        // Тип фасовки = весовая
        update_post_meta( $product_id, '_ferma_fasovka', 'vesovaya' );
        wp_set_object_terms( $product_id, 'Весовая', 'pa_fasovka', false );

        // Разбивка веса:
        // если в маппинге категория даёт вес < 1 кг (0.3, 0.5 и т.п.) — "да"
        $razbivka_value = ( $unitInfo['weight'] !== null && $unitInfo['weight'] < 1 )
            ? 'да'
            : 'нет';

        update_field( 'field_627cbc0e2d6f3', $razbivka_value, $product_id );
        fas_log( "FAS: ACF razbivka_vesa → {$razbivka_value} for product {$product_id}" );

    } else {

        // Тип фасовки = штучная
        update_post_meta( $product_id, '_ferma_fasovka', 'shtuchnaya' );
        wp_set_object_terms( $product_id, 'Штучная', 'pa_fasovka', false );

        // Разбивка веса в штучке не бывает
        update_field( 'field_627cbc0e2d6f3', 'нет', $product_id );
        fas_log( "FAS: non-weight product, ACF razbivka_vesa → нет for product {$product_id}" );
    }

    return $ms_item;
}
