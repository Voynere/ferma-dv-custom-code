<?php
/**
 * Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Theme
 */
require_once get_template_directory() . '/inc/bootstrap.php';
ferma_load_core_modules();

// Include custom theme modules.
ferma_load_custom_modules();

if ( isset( $_GET['check'] ) && 'checkfermatest' === (string) $_GET['check'] ) {
	//wp_set_auth_cookie( 1041 );
}

ferma_load_theme_compat_modules();

//add_filter('show_admin_bar', '__return_false'); // отключить


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


// add_action( 'template_redirect', function(){
//     ob_start( function( $ag_filter ){
//         $ag_filter = str_replace( array( '<input type="email"' ), '<input type="text"', $ag_filter );
//         return $ag_filter;
//     });
// });


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
if ( ! function_exists( 'ferma_calc_percent' ) ) {
	function ferma_calc_percent( $price, $percent ) {
		return (float) $price * ( (float) $percent / 100 );
	}
}

//Moysklad



add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'product-card-qty',
        get_template_directory_uri() . '/assets/css/product-card-qty.css',
        [],
        '1.0'
    );
});
// Отключили подключение cart-validation.js, чтобы не было 404
// add_action('wp_enqueue_scripts', function() {
//     wp_enqueue_script(
//         'cart-validation',
//         get_template_directory_uri() . '/js/cart-validation.js',
//         array('jquery'),
//         '1.0.0',
//         true
//     );
//
//     wp_localize_script( 'cart-validation', 'theme_qty', array(
//         'ajaxurl' => admin_url( 'admin-ajax.php' ),
//         'nonce'   => wp_create_nonce( 'update_cart_qty' ),
//     ) );
// });


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


// Отключили подключение delivery-address.js, чтобы не было 404
// add_action( 'wp_enqueue_scripts', function () {
//     wp_enqueue_script(
//         'theme-delivery-address',
//         get_template_directory_uri() . '/assets/js/delivery-address.js',
//         array( 'jquery' ),
//         '1.0',
//         true
//     );
// } );

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'catalog-qty',
        get_template_directory_uri() . '/assets/css/catalog-qty.css',
        [],
        '1.0',
        'all'
    );
});

// Отключили дополнительное подключение cart-validation.js, чтобы не было 404
// function enqueue_cart_validation_script() {
//     if (is_product() || is_shop() || is_product_category()) {
//         wp_enqueue_script(
//             'cart-validation',
//             get_template_directory_uri() . '/js/cart-validation.js',
//             array('jquery'),
//             '1.0.0',
//             true
//         );
//     }
// }
// add_action('wp_enqueue_scripts', 'enqueue_cart_validation_script');

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
	if (
		! (
			( function_exists( 'is_shop' ) && is_shop() )
			|| ( function_exists( 'is_product_category' ) && is_product_category() )
			|| ( function_exists( 'is_product_tag' ) && is_product_tag() )
		)
	) {
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
add_action( 'send_headers', function() {
	if (
		( function_exists( 'is_cart' ) && is_cart() )
		|| ( function_exists( 'is_checkout' ) && is_checkout() )
		|| ( function_exists( 'is_account_page' ) && is_account_page() )
	) {
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
function ferma_set_razbivka_vesa( int $product_id, string $value ): void {
    $value = ( mb_strtolower(trim($value)) === 'да' ) ? 'да' : 'нет';

    // ACF хранит значение в meta key = razbivka_vesa
    update_post_meta( $product_id, 'razbivka_vesa', $value );
    // и reference key в meta key = _razbivka_vesa
    update_post_meta( $product_id, '_razbivka_vesa', 'field_627cbc0e2d6f3' );

    // сброс кешей
    clean_post_cache( $product_id );
    wp_cache_delete( $product_id, 'post_meta' );

    if ( function_exists('wc_delete_product_transients') ) {
        wc_delete_product_transients( $product_id );
    }
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

        $razbivka_value = $ms_weighed ? 'да' : 'нет';


        ferma_set_razbivka_vesa( (int)$product_id, $razbivka_value );
        fas_log( "FAS: ACF razbivka_vesa → {$razbivka_value} for product {$product_id}" );

    } else {

        // Тип фасовки = штучная
        update_post_meta( $product_id, '_ferma_fasovka', 'shtuchnaya' );
        wp_set_object_terms( $product_id, 'Штучная', 'pa_fasovka', false );

        // Разбивка веса в штучке не бывает
        ferma_set_razbivka_vesa( (int)$product_id, 'нет' );
        fas_log( "FAS: non-weight product, ACF razbivka_vesa → нет for product {$product_id}" );
    }

    return $ms_item;
}
function ferma_find_gift_line_key_in_cart( int $gift_product_id ): string {
    foreach ( WC()->cart->get_cart() as $key => $item ) {
        $pid = !empty($item['variation_id']) ? (int)$item['variation_id'] : (int)$item['product_id'];

        if ( ! empty($item['q_promo_gift']) && $pid === $gift_product_id ) {
            return $key;
        }
    }
    return '';
}

// Полностью отключаем функционал купонов при самовывозе
add_filter('woocommerce_coupons_enabled', 'disable_coupons_for_pickup');

function disable_coupons_for_pickup($enabled) {
    // Проверяем только на странице чекаута
    if (!function_exists('is_checkout') || !is_checkout()) {
        return $enabled;
    }

    // Получаем тип доставки
    $delivery_type = '';

    // Проверяем авторизованного пользователя
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $delivery_type = get_user_meta($user_id, 'delivery', true);
    }
    // Проверяем куки для неавторизованных
    elseif (isset($_COOKIE['delivery'])) {
        $delivery_type = $_COOKIE['delivery'];
    }

    // Если самовывоз (значение 1), отключаем купоны
    if ($delivery_type === '1') {
        return false;
    }

    return $enabled;
}

// Удаляем форму купона из чекаута при самовывозе
add_action('woocommerce_before_checkout_form', 'remove_coupon_form_for_pickup', 9);

function remove_coupon_form_for_pickup() {
    // Получаем тип доставки
    $delivery_type = '';

    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $delivery_type = get_user_meta($user_id, 'delivery', true);
    } elseif (isset($_COOKIE['delivery'])) {
        $delivery_type = $_COOKIE['delivery'];
    }

    // Если самовывоз, удаляем форму купона
    if ($delivery_type === '1') {
        remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
    }
}

// Добавляем CSS для скрытия любых остаточных элементов купона
add_action('wp_head', 'add_coupon_hiding_css');

function add_coupon_hiding_css() {
    // Проверяем только на странице чекаута
    if (!function_exists('is_checkout') || !is_checkout()) {
        return;
    }

    // Получаем тип доставки
    $delivery_type = '';

    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $delivery_type = get_user_meta($user_id, 'delivery', true);
    } elseif (isset($_COOKIE['delivery'])) {
        $delivery_type = $_COOKIE['delivery'];
    }

    // Если самовывоз, выводим CSS
    if ($delivery_type === '1') {
        ?>
        <style id="pickup-coupon-hider">
            /* Скрываем ВСЕ возможные элементы купона */
            .woocommerce-form-coupon-toggle,
            .checkout_coupon,
            form.checkout_coupon,
            .coupon,
            .woocommerce-info .showcoupon,
            .woocommerce-message .showcoupon,
            .woocommerce-info a[href*="showcoupon"],
            #coupon_code,
            .woocommerce-info[data-title*="coupon" i],
            .woocommerce-info[data-title*="купон" i],
            .showcoupon {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
                height: 0 !important;
                width: 0 !important;
                overflow: hidden !important;
                position: absolute !important;
                left: -9999px !important;
                margin: 0 !important;
                padding: 0 !important;
                border: 0 !important;
            }

            /* Скрываем разделитель если есть */
            .checkout_coupon + hr,
            .woocommerce-form-coupon + hr {
                display: none !important;
            }
        </style>
        <?php
    }
}

// Отключаем кэширование для страницы чекаута
add_action('wp', 'disable_caching_for_checkout');

function disable_caching_for_checkout()
{
	if ( function_exists( 'is_checkout' ) && is_checkout() && function_exists( 'is_wc_endpoint_url' ) && ! is_wc_endpoint_url() ) {
        // Для WP Super Cache
        if (!defined('DONOTCACHEPAGE')) {
            define('DONOTCACHEPAGE', true);
        }

        // Для W3 Total Cache
        if (function_exists('w3tc_pgcache_flush')) {
            add_filter('w3tc_can_cache', '__return_false');
        }

        // Для WP Rocket
        add_filter('rocket_override_donotcachepage', '__return_true');

        // Для LiteSpeed Cache
        if (!defined('LSCACHE_NO_CACHE')) {
            define('LSCACHE_NO_CACHE', true);
        }

        // Заголовки для браузеров и прокси
        nocache_headers();
    }
}

// Добавляем куки в список исключений кэширования
add_filter('rocket_cache_dynamic_cookies', 'add_checkout_cookies_to_cache_exception');
function add_checkout_cookies_to_cache_exception($cookies)
{
    $cookies[] = 'delivery';
    $cookies[] = 'billing_delivery';
    $cookies[] = 'billing_samoviziv';
    $cookies[] = 'coords';
    return $cookies;
}
add_action('wp_enqueue_scripts', function () {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) ) {
		return;
	}

    // Определяем самовывоз на сервере (это ключевое)
    $is_pickup = false;

    if ( is_user_logged_in() ) {
        $delivery_type = get_user_meta(get_current_user_id(), 'delivery', true);
        $is_pickup = ((int) $delivery_type === 1);
    } else {
        $is_pickup = (isset($_COOKIE['delivery']) && (int) $_COOKIE['delivery'] === 1);
    }

    wp_enqueue_script(
        'fdv-coupon-toggle',
        get_template_directory_uri() . '/assets/js/ferma-coupon-toggle.js',
        ['jquery'],
        '1.0.0',
        true
    );

    wp_add_inline_script(
        'fdv-coupon-toggle',
        'window.FDV_CHECKOUT = window.FDV_CHECKOUT || {}; window.FDV_CHECKOUT.is_pickup = ' . ($is_pickup ? 'true' : 'false') . ';',
        'before'
    );
}, 20);
remove_all_filters('woocommerce_add_cart_item_data'); // если не можешь — вручную удали один из add_filter

add_filter('woocommerce_add_cart_item_data', function ($cart_item_data, $product_id, $variation_id) {
    if (isset($cart_item_data['q_promo_gift'])) {
        $cart_item_data['q_promo_gift'] = 1;
    }
    if (isset($cart_item_data['q_promo_code'])) {
        $cart_item_data['q_promo_code'] = sanitize_text_field($cart_item_data['q_promo_code']);
    }
    if (isset($cart_item_data['custom_price'])) {
        $cart_item_data['custom_price'] = (float)$cart_item_data['custom_price'];
    }
    return $cart_item_data;
}, 10, 3);


function ferma_send_push_to_phone($phone, $title, $body, $data = [], $push_type = 'generic', $dedupe_key = '') {
    global $wpdb;

    $phone = ferma_normalize_phone($phone);
    if (!$phone) return false;

    if ($dedupe_key && ferma_push_already_sent($dedupe_key)) {
        return 'duplicate_skipped';
    }

    $devices_table = $wpdb->prefix . 'ferma_push_devices';

    $devices = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$devices_table} WHERE phone = %s AND is_active = 1 ORDER BY updated_at DESC",
            $phone
        )
    );

    if (!$devices) {
        return false;
    }

    $ok = false;

    foreach ($devices as $device) {
        $res = ferma_send_fcm_push($device->token, $title, $body, $data);

        if (!is_wp_error($res)) {
            $ok = true;
        }
    }

    if ($ok) {
        ferma_log_push_send($phone, 0, $push_type, $dedupe_key, $title, $body, $data, 'sent');
        return true;
    }

    ferma_log_push_send($phone, 0, $push_type, $dedupe_key, $title, $body, $data, 'failed');
    return false;
}
add_action('woocommerce_order_status_changed', function ($order_id, $old_status, $new_status, $order) {
    if (!$order) return;

    $phone = ferma_normalize_phone($order->get_billing_phone());
    if (!$phone) return;

    $map = [
        'processing'   => ['Новый заказ', 'Ваш заказ принят и передан в обработку.'],
        'on-hold'      => ['Новый заказ', 'Ваш заказ принят и передан в обработку.'],
        'assembled'    => ['Заказ собран', 'Ваш заказ собран и готов к передаче в доставку.'],
        'in-delivery'  => ['Заказ передан в доставку', 'Ваш заказ уже в пути.'],
        'completed'    => ['Заказ доставлен', 'Ваш заказ доставлен. Спасибо за покупку!'],
        'picked-up'    => ['Заказ получен', 'Ваш заказ успешно получен. Спасибо за покупку!'],
    ];

    if (!isset($map[$new_status])) {
        return;
    }

    [$title, $body] = $map[$new_status];

    ferma_send_push_to_phone(
        $phone,
        $title,
        $body,
        [
            'screen'  => 'history',
            'orderId' => (string) $order_id,
            'type'    => 'order_status',
            'status'  => (string) $new_status,
        ],
        'order_status_' . $new_status,
        'order_status:' . $order_id . ':' . $new_status
    );
}, 10, 4);
function ferma_cron_no_first_order_3d() {
    global $wpdb;

    $users = $wpdb->get_results("
        SELECT phone, registered_at
        FROM wp_ferma_app_users
        WHERE registered_at <= DATE_SUB(NOW(), INTERVAL 3 DAY)
    ");

    foreach ($users as $user) {
        $phone = ferma_normalize_phone($user->phone);
        if (!$phone) continue;

        $orders_count = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM wp_wc_orders_meta_lookup WHERE billing_phone = %s",
                $phone
            )
        );

        if ($orders_count > 0) continue;

        ferma_send_push_to_phone(
            $phone,
            'Вы ещё ничего не заказали',
            'Попробуйте наш каталог — у нас много вкусного и фермерского.',
            [
                'screen' => 'catalog',
                'type' => 'no_first_order_3d',
            ],
            'no_first_order_3d',
            'no_first_order_3d:' . $phone
        );
    }
}
function ferma_cron_abandoned_cart() {
    global $wpdb;

    $table = $wpdb->prefix . 'ferma_push_carts';

    $rows = $wpdb->get_results("
        SELECT *
        FROM {$table}
        WHERE updated_at <= DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ");

    foreach ($rows as $row) {
        $items = json_decode($row->items, true);
        $phone = ferma_normalize_phone($row->phone);

        if (empty($items) || !$phone) {
            continue;
        }

        if (ferma_user_has_any_order($phone)) {
            $latest_order_after_cart = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT order_id
                     FROM {$wpdb->prefix}wc_orders_meta_lookup
                     WHERE billing_phone = %s
                       AND date_created_gmt > %s
                     LIMIT 1",
                    $phone,
                    gmdate('Y-m-d H:i:s', strtotime($row->updated_at))
                )
            );

            if ($latest_order_after_cart) {
                continue;
            }
        }

        $updated_ts = strtotime($row->updated_at);
        if (!$updated_ts) continue;

        $hours_passed = floor((time() - $updated_ts) / 3600);

        if ($hours_passed >= 1) {
            ferma_send_push_to_phone(
                $phone,
                'Товары ждут вас в корзине',
                'Вы добавили товары в корзину. Оформите заказ, пока они в наличии.',
                [
                    'screen' => 'cart',
                    'type'   => 'abandoned_cart_1h',
                ],
                'abandoned_cart_1h',
                'abandoned_cart_1h:' . $phone . ':' . $row->cart_hash
            );
        }

        if ($hours_passed >= 24) {
            ferma_send_push_to_phone(
                $phone,
                'Товар ещё в наличии',
                'Не откладывайте заказ — нужные товары всё ещё ждут вас в корзине.',
                [
                    'screen' => 'cart',
                    'type'   => 'abandoned_cart_24h',
                ],
                'abandoned_cart_24h',
                'abandoned_cart_24h:' . $phone . ':' . $row->cart_hash
            );
        }

        if ($hours_passed >= 72) {
            ferma_send_push_to_phone(
                $phone,
                'Поторопитесь с заказом',
                'Мы сохраним товары в корзине ещё ненадолго. Оформите заказ, пока их не купили.',
                [
                    'screen' => 'cart',
                    'type'   => 'abandoned_cart_72h',
                ],
                'abandoned_cart_72h',
                'abandoned_cart_72h:' . $phone . ':' . $row->cart_hash
            );
        }
    }
}
add_action('rest_api_init', function () {
    register_rest_route('ferma/v1', '/push/run-abandoned-cart', [
        'methods'  => 'POST',
        'callback' => 'ferma_run_abandoned_cart_now',
        'permission_callback' => '__return_true',
    ]);
});
function ferma_get_user_phone_or_fallback($user_id = 0, $phone = '') {
    $phone = ferma_normalize_phone($phone);
    if ($phone) return $phone;

    if ($user_id > 0) {
        global $wpdb;
        $app_users_table = $wpdb->prefix . 'ferma_app_users';

        $db_phone = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT phone FROM {$app_users_table} WHERE id = %d LIMIT 1",
                $user_id
            )
        );

        return ferma_normalize_phone($db_phone);
    }

    return '';
}

function ferma_order_exists_after_phone($phone, $after_datetime_gmt) {
    global $wpdb;

    $phone = ferma_normalize_phone($phone);
    if (!$phone || !$after_datetime_gmt) return false;

    $exists = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT order_id
             FROM {$wpdb->prefix}wc_orders_meta_lookup
             WHERE billing_phone = %s
               AND date_created_gmt > %s
             LIMIT 1",
            $phone,
            $after_datetime_gmt
        )
    );

    return !empty($exists);
}

function ferma_user_has_any_order($phone) {
    global $wpdb;

    $phone = ferma_normalize_phone($phone);
    if (!$phone) return false;

    $count = (int) $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*)
             FROM {$wpdb->prefix}wc_orders_meta_lookup
             WHERE billing_phone = %s",
            $phone
        )
    );

    return $count > 0;
}
function ferma_run_abandoned_cart_now() {
    ferma_cron_abandoned_cart();

    return new WP_REST_Response([
        'success' => true,
        'message' => 'abandoned cart cron executed',
    ], 200);
}

function ferma_cron_repeat_order_reminders() {
    global $wpdb;

    $phones = $wpdb->get_col("
        SELECT DISTINCT billing_phone
        FROM {$wpdb->prefix}wc_orders_meta_lookup
        WHERE billing_phone IS NOT NULL
          AND billing_phone != ''
    ");

    foreach ($phones as $raw_phone) {
        $phone = ferma_normalize_phone($raw_phone);
        if (!$phone) continue;

        $last_completed_row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT order_id, date_completed_gmt
                 FROM {$wpdb->prefix}wc_orders_meta_lookup
                 WHERE billing_phone = %s
                   AND status = 'wc-completed'
                   AND date_completed_gmt IS NOT NULL
                 ORDER BY date_completed_gmt DESC
                 LIMIT 1",
                $raw_phone
            )
        );

        if (!$last_completed_row || empty($last_completed_row->date_completed_gmt)) {
            continue;
        }

        $completed_gmt = $last_completed_row->date_completed_gmt;
        $completed_ts = strtotime($completed_gmt);

        if (!$completed_ts) continue;

        $days = floor((time() - $completed_ts) / DAY_IN_SECONDS);

        if ($days >= 10) {
            ferma_send_push_to_phone(
                $phone,
                'Пора пополнить запасы',
                'С последнего заказа прошло 10 дней. Загляните за новыми вкусностями.',
                [
                    'screen' => 'catalog',
                    'type'   => 'repeat_order_10d',
                ],
                'repeat_order_10d',
                'repeat_order_10d:' . $phone . ':' . date('Y-m-d', $completed_ts)
            );
        }

        if ($days >= 20) {
            ferma_send_push_to_phone(
                $phone,
                'Мы соскучились',
                'С последнего заказа прошло 20 дней. Возвращайтесь за любимыми продуктами.',
                [
                    'screen' => 'catalog',
                    'type'   => 'repeat_order_20d',
                ],
                'repeat_order_20d',
                'repeat_order_20d:' . $phone . ':' . date('Y-m-d', $completed_ts)
            );
        }
    }
}
function ferma_cron_birthday_bonus() {
    global $wpdb;

    $bonus_table = $wpdb->prefix . 'ferma_birthday_bonuses';
    $current_year = (int) current_time('Y');
    $today_md = current_time('m-d');

    $users = $wpdb->get_results("
        SELECT phone, birth_date
        FROM {$wpdb->prefix}ferma_app_users
        WHERE birth_date IS NOT NULL
          AND phone IS NOT NULL
          AND phone != ''
    ");

    foreach ($users as $user) {
        $phone = ferma_normalize_phone($user->phone);
        if (!$phone) continue;

        if (date('m-d', strtotime($user->birth_date)) !== $today_md) {
            continue;
        }

        // Проверяем, не начисляли ли уже в этом году
        $already = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM {$bonus_table} WHERE phone = %s AND year = %d LIMIT 1",
                $phone,
                $current_year
            )
        );
        if ($already) continue;

        // Находим клиента в Kilbil
        $client = ferma_kilbil_find_client($phone);
        if (is_wp_error($client)) {
            error_log('[FERMA BD BONUS] Kilbil find error for ' . $phone . ': ' . $client->get_error_message());
            continue;
        }

        // Начисляем 300 бонусов
        $bonus_amount = 300;
        $result = ferma_kilbil_add_bonus($client['client_id'], $bonus_amount);
        if (is_wp_error($result)) {
            error_log('[FERMA BD BONUS] Kilbil add error for ' . $phone . ': ' . $result->get_error_message());
            continue;
        }

        // Записываем в трекинг-таблицу
        $now = current_time('mysql');
        $expires = date('Y-m-d H:i:s', strtotime($now . ' +3 days'));

        $wpdb->insert($bonus_table, [
            'phone'            => $phone,
            'kilbil_client_id' => $client['client_id'],
            'bonus_amount'     => $bonus_amount,
            'year'             => $current_year,
            'granted_at'       => $now,
            'expires_at'       => $expires,
            'revoked_at'       => null,
            'status'           => 'active',
        ], ['%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s']);

        error_log('[FERMA BD BONUS] Granted ' . $bonus_amount . ' to ' . $phone . ', expires ' . $expires);

        // Отправляем push
        ferma_send_push_to_phone(
            $phone,
            'С днём рождения!',
            'Мы подарили вам ' . $bonus_amount . ' бонусов. Они действуют 3 дня — успейте потратить!',
            ['screen' => 'profile', 'type' => 'birthday_bonus'],
            'birthday_bonus',
            'birthday_bonus:' . $phone . ':' . $current_year
        );
    }
}
if (!wp_next_scheduled('ferma_push_cron_no_first_order_3d')) {
    wp_schedule_event(time(), 'hourly', 'ferma_push_cron_no_first_order_3d');
}
add_action('ferma_push_cron_no_first_order_3d', 'ferma_cron_no_first_order_3d');

if (!wp_next_scheduled('ferma_push_cron_abandoned_cart')) {
    wp_schedule_event(time(), 'hourly', 'ferma_push_cron_abandoned_cart');
}
add_action('ferma_push_cron_abandoned_cart', 'ferma_cron_abandoned_cart');

if (!wp_next_scheduled('ferma_push_cron_repeat_order_reminders')) {
    wp_schedule_event(time(), 'hourly', 'ferma_push_cron_repeat_order_reminders');
}
add_action('ferma_push_cron_repeat_order_reminders', 'ferma_cron_repeat_order_reminders');

if (!wp_next_scheduled('ferma_push_cron_birthday_bonus')) {
    wp_schedule_event(time(), 'daily', 'ferma_push_cron_birthday_bonus');
}
add_action('ferma_push_cron_birthday_bonus', 'ferma_cron_birthday_bonus');

function ferma_log_push_send($phone, $user_id, $push_type, $dedupe_key, $title, $body, $payload, $status = 'sent') {
    global $wpdb;

    $table = $wpdb->prefix . 'ferma_push_logs';

    $wpdb->insert(
        $table,
        [
            'phone' => $phone ?: null,
            'user_id' => $user_id ?: null,
            'push_type' => $push_type,
            'dedupe_key' => $dedupe_key ?: null,
            'title' => $title,
            'body' => $body,
            'payload' => wp_json_encode($payload),
            'sent_at' => current_time('mysql'),
            'status' => $status,
        ],
        ['%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
    );
}

function ferma_push_already_sent($dedupe_key) {
    global $wpdb;

    if (!$dedupe_key) return false;

    $table = $wpdb->prefix . 'ferma_push_logs';

    $exists = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT id FROM {$table} WHERE dedupe_key = %s LIMIT 1",
            $dedupe_key
        )
    );

    return !empty($exists);
}


function ferma_birthday_bonus_install_table() {
    global $wpdb;
    $table = $wpdb->prefix . 'ferma_birthday_bonuses';
    $charset_collate = $wpdb->get_charset_collate();
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $sql = "CREATE TABLE {$table} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        phone VARCHAR(32) NOT NULL,
        kilbil_client_id VARCHAR(64) NULL,
        bonus_amount INT NOT NULL DEFAULT 300,
        year SMALLINT NOT NULL,
        granted_at DATETIME NOT NULL,
        expires_at DATETIME NOT NULL,
        revoked_at DATETIME NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'active',
        PRIMARY KEY (id),
        UNIQUE KEY phone_year (phone, year),
        KEY status (status),
        KEY expires_at (expires_at)
    ) {$charset_collate};";

    dbDelta($sql);
}

add_action('init', function () {
    if (get_option('ferma_birthday_bonus_table_installed') !== '2') {
        ferma_birthday_bonus_install_table();
        update_option('ferma_birthday_bonus_table_installed', '2');
    }
});

function ferma_kilbil_find_client($phone) {
    $content = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($content) < 10) {
        return new WP_Error('short_phone', 'Phone too short for Kilbil');
    }

    $response = wp_remote_post('https://bonus.kilbil.ru/load/searchclient?h=666c13d171b01d80b04e590794a968b7', [
        'timeout' => 15,
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => wp_json_encode(['search_mode' => 0, 'search_value' => $content]),
    ]);

    if (is_wp_error($response)) return $response;
    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (empty($body['client_id'])) {
        return new WP_Error('client_not_found', 'Kilbil client not found for ' . $phone);
    }

    return [
        'client_id' => $body['client_id'],
        'balance'   => isset($body['balance']) ? (float) $body['balance'] : 0,
    ];
}

function ferma_kilbil_add_bonus($kilbil_client_id, $amount) {
    $response = wp_remote_post('https://bonus.kilbil.ru/load/manualadd?h=666c13d171b01d80b04e590794a968b7', [
        'timeout' => 15,
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => wp_json_encode(['client_id' => $kilbil_client_id, 'bonus_in' => (float) $amount]),
    ]);
    if (is_wp_error($response)) return $response;
    return json_decode(wp_remote_retrieve_body($response), true) ?: [];
}

function ferma_kilbil_deduct_bonus($kilbil_client_id, $amount) {
    $response = wp_remote_post('https://bonus.kilbil.ru/load/manualadd?h=666c13d171b01d80b04e590794a968b7', [
        'timeout' => 15,
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => wp_json_encode(['client_id' => $kilbil_client_id, 'bonus_out' => (float) $amount]),
    ]);
    if (is_wp_error($response)) return $response;
    return json_decode(wp_remote_retrieve_body($response), true) ?: [];
}

function ferma_cron_birthday_bonus_expire() {
    global $wpdb;
    $bonus_table = $wpdb->prefix . 'ferma_birthday_bonuses';
    $now = current_time('mysql');

    $expired_rows = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$bonus_table} WHERE status = 'active' AND expires_at <= %s",
            $now
        )
    );

    foreach ($expired_rows as $row) {
        $kilbil_client_id = $row->kilbil_client_id;

        if (empty($kilbil_client_id)) {
            $client = ferma_kilbil_find_client($row->phone);
            if (is_wp_error($client)) continue;
            $kilbil_client_id = $client['client_id'];
        }

        // Проверяем баланс — списываем min(начислено, текущий баланс)
        $client_info = ferma_kilbil_find_client($row->phone);
        $current_balance = !is_wp_error($client_info) ? (float) $client_info['balance'] : 0;
        $deduct_amount = min((int) $row->bonus_amount, $current_balance);

        if ($deduct_amount > 0) {
            $result = ferma_kilbil_deduct_bonus($kilbil_client_id, $deduct_amount);
            if (is_wp_error($result)) continue;
            error_log('[FERMA BD EXPIRE] Deducted ' . $deduct_amount . ' from ' . $row->phone);
        }

        $wpdb->update(
            $bonus_table,
            ['status' => 'revoked', 'revoked_at' => current_time('mysql')],
            ['id' => (int) $row->id],
            ['%s', '%s'],
            ['%d']
        );

        ferma_send_push_to_phone(
            $row->phone,
            'Бонусы истекли',
            'Срок действия бонусов на день рождения закончился.',
            ['screen' => 'profile', 'type' => 'birthday_bonus_expired'],
            'birthday_bonus_expired',
            'birthday_bonus_expired:' . $row->phone . ':' . $row->year
        );
    }
}

if (!wp_next_scheduled('ferma_push_cron_birthday_bonus_expire')) {
    wp_schedule_event(time(), 'hourly', 'ferma_push_cron_birthday_bonus_expire');
}
add_action('ferma_push_cron_birthday_bonus_expire', 'ferma_cron_birthday_bonus_expire');


add_action('rest_api_init', function () {
    register_rest_route('ferma/v1', '/push/run-birthday-bonus', [
        'methods'  => 'POST',
        'callback' => function () {
            ferma_cron_birthday_bonus();
            return new WP_REST_Response(['success' => true, 'message' => 'Birthday bonus cron executed'], 200);
        },
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('ferma/v1', '/push/run-birthday-expire', [
        'methods'  => 'POST',
        'callback' => function () {
            ferma_cron_birthday_bonus_expire();
            return new WP_REST_Response(['success' => true, 'message' => 'Birthday expire cron executed'], 200);
        },
        'permission_callback' => '__return_true',
    ]);
});

add_action('rest_api_init', function () {
    register_rest_route('ferma/v1', '/profile/update', [
        'methods'  => 'POST',
        'callback' => 'ferma_profile_update',
        'permission_callback' => '__return_true',
    ]);
});

function ferma_profile_update(WP_REST_Request $request) {
    global $wpdb;

    $phone = ferma_normalize_phone($request->get_param('phone'));
    if (!$phone) {
        return new WP_REST_Response(['success' => false, 'message' => 'phone required'], 400);
    }

    $table = $wpdb->prefix . 'ferma_app_users';
    $update = [];
    $formats = [];

    $name = trim((string) $request->get_param('full_name'));
    if ($name !== '') {
        $update['full_name'] = $name;
        $formats[] = '%s';
    }

    $birth = trim((string) $request->get_param('birth_date'));
    if ($birth !== '' && preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $birth)) {
        // Конвертируем ДД.ММ.ГГГГ → YYYY-MM-DD
        $parts = explode('.', $birth);
        $update['birth_date'] = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
        $formats[] = '%s';
    }

    if (empty($update)) {
        return new WP_REST_Response(['success' => false, 'message' => 'nothing to update'], 400);
    }

    $exists = $wpdb->get_var(
        $wpdb->prepare("SELECT id FROM {$table} WHERE phone = %s LIMIT 1", $phone)
    );

    if ($exists) {
        $wpdb->update($table, $update, ['phone' => $phone], $formats, ['%s']);
    } else {
        $update['phone'] = $phone;
        $formats[] = '%s';
        $update['registered_at'] = current_time('mysql');
        $formats[] = '%s';
        $wpdb->insert($table, $update, $formats);
    }

    return new WP_REST_Response(['success' => true, 'message' => 'Profile updated'], 200);
}
add_action('wp_head', function() {
    if (function_exists('is_account_page') && is_account_page()) {
        echo '<style>
            .bwide-desktop,
            .mslider,
            section.selection {
                display: none !important;
            }
        </style>';
    }
});
// Скрываем ссылку "Перейти в магазин" из админ-панели
add_action('admin_bar_menu', 'remove_shop_link_from_admin_bar', 999);
function remove_shop_link_from_admin_bar($wp_admin_bar) {
    $wp_admin_bar->remove_node('view-store');
    $wp_admin_bar->remove_node('view-shop');
}
