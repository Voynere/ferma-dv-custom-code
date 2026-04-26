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

//add_filter( 'pre_option_woocommerce_hide_out_of_stock_items', 'fix_kulichi_category' );
function fix_kulichi_category( $hide ) {
	if ( function_exists( 'is_product_category' ) && is_product_category( 'kulichi' ) ) {
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

function ferma_admin_new_order_recipient( $recipient, $order, $email ) {
	return 'zakaz@ferma-dv.ru';
}
add_filter( 'woocommerce_email_recipient_new_order', 'ferma_admin_new_order_recipient', 10, 3 );

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
		if ( $active && function_exists( 'WC' ) && WC() && WC()->session ) {
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
		if ( function_exists( 'WC' ) && WC() && WC()->session ) {
			WC()->session->__unset( 'q_active_promo' );
		}

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
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || wp_doing_ajax() ) {
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

	if ( ! function_exists( 'WC' ) || ! WC() || ! WC()->cart ) {
		return $fragments;
	}

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
add_filter('woocommerce_add_cart_item_data', function ($cart_item_data, $product_id, $variation_id) {
    if (isset($cart_item_data['q_promo_gift'])) {
        $cart_item_data['q_promo_gift'] = 1; // лучше 1, чем true
    }
    if (isset($cart_item_data['q_promo_code'])) {
        $cart_item_data['q_promo_code'] = sanitize_text_field($cart_item_data['q_promo_code']);
    }
    return $cart_item_data;
}, 10, 3);
add_filter('woocommerce_get_cart_item_from_session', function ($item, $values) {

    // Woo сохраняет custom cart item data на верхнем уровне,
    // но при некоторых сценариях часть может оказаться в $values['data'].
    // Поэтому проверяем оба варианта.

    if (isset($values['q_promo_gift'])) {
        $item['q_promo_gift'] = $values['q_promo_gift'];
    } elseif (isset($values['data']['q_promo_gift'])) {
        $item['q_promo_gift'] = $values['data']['q_promo_gift'];
    }

    if (isset($values['q_promo_code'])) {
        $item['q_promo_code'] = sanitize_text_field($values['q_promo_code']);
    } elseif (isset($values['data']['q_promo_code'])) {
        $item['q_promo_code'] = sanitize_text_field($values['data']['q_promo_code']);
    }

    if (isset($values['custom_price'])) {
        $item['custom_price'] = (float) $values['custom_price'];
    } elseif (isset($values['data']['custom_price'])) {
        $item['custom_price'] = (float) $values['data']['custom_price'];
    }

    return $item;
}, 20, 2);

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
	if ( function_exists( 'is_checkout' ) && is_checkout() ) {
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


add_action('rest_api_init', function () {
    register_rest_route('ferma/v1', '/stocks', [
        'methods'  => 'GET',
        'callback' => 'ferma_get_stocks',
        'permission_callback' => '__return_true',
    ]);
    register_rest_route('ferma/v1', '/stock-migration-status', [
        'methods'  => 'GET',
        'callback' => 'ferma_get_stock_migration_status',
        'permission_callback' => '__return_true',
    ]);
});


function ferma_get_stocks(WP_REST_Request $req)
{
    $id = intval($req->get_param('product_id'));
    if (!$id) {
        return new WP_REST_Response(['error' => 'product_id is required'], 400);
    }

    return new WP_REST_Response(ferma_build_stock_payload($id), 200);
}


add_action('rest_api_init', function () {

    register_rest_route('ferma/v1', '/stocks-by-skus', [
        'methods' => 'GET',
        'callback' => 'ferma_get_stocks_by_skus',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('ferma/v1', '/stocks-by-sku', [
        'methods' => 'GET',
        'callback' => 'ferma_get_stocks_by_sku',
        'permission_callback' => '__return_true',
    ]);

});

function ferma_store_meta_map(): array {
    return [
        '028e05a7-b4fa-11ee-0a80-1198000442be' => 'Заря',
        '7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93' => 'Эгершельд',
        'a99d6fdf-0970-11ed-0a80-0ed600075845' => 'Космос',
        'b24e4c35-9609-11eb-0a80-0d0d008550c2' => 'Реми-Сити',
    ];
}

function ferma_get_store_stocks_with_fallback(int $product_id, array $store_ids): array {
    $stocks = [];

    if (class_exists('\Wdc\Addition\Stores\StockTable')) {
        $stocks = \Wdc\Addition\Stores\StockTable::get_product_stocks($product_id, $store_ids);
    }

    foreach ($store_ids as $store_id) {
        if (!array_key_exists($store_id, $stocks)) {
            $stocks[$store_id] = (float) get_post_meta($product_id, $store_id, true);
        } else {
            $stocks[$store_id] = (float) $stocks[$store_id];
        }
    }

    return $stocks;
}

function ferma_build_stock_payload(int $product_id): array {
    $store_meta = ferma_store_meta_map();
    $store_stocks = ferma_get_store_stocks_with_fallback($product_id, array_keys($store_meta));

    $data = [
        'product_id' => $product_id,
        'sku' => (string) get_post_meta($product_id, '_sku', true),
        'manage_stock' => get_post_meta($product_id, '_manage_stock', true) === 'yes',
        'stock_total' => (float) get_post_meta($product_id, '_stock', true),
        'stores' => [],
    ];

    foreach ($store_meta as $meta_key => $title) {
        $data['stores'][] = [
            'id' => $meta_key,
            'name' => $title,
            'stock' => (float) ($store_stocks[$meta_key] ?? 0),
        ];
    }

    return $data;
}

function ferma_get_stock_migration_status(WP_REST_Request $req) {
    if (!class_exists('\Wdc\Addition\Stores\StockTable')) {
        return new WP_REST_Response([
            'enabled' => false,
            'error' => 'StockTable service is not available',
        ], 200);
    }

    return new WP_REST_Response([
        'enabled' => true,
        'status' => \Wdc\Addition\Stores\StockTable::get_diagnostics(),
    ], 200);
}

function ferma_get_stocks_by_sku(WP_REST_Request $req) {
    $sku = (string) $req->get_param('sku');
    $sku = trim($sku);

    if ($sku === '') {
        return new WP_REST_Response(['error' => 'sku is required'], 400);
    }

    $q = new WP_Query([
        'post_type' => ['product', 'product_variation'],
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'meta_query' => [
            [
                'key' => '_sku',
                'value' => $sku,
                'compare' => '=',
            ]
        ],
    ]);

    if (empty($q->posts)) {
        return new WP_REST_Response([], 200);
    }

    $pid = (int) $q->posts[0];
    return new WP_REST_Response(ferma_build_stock_payload($pid), 200);
}

function ferma_get_stocks_by_skus(WP_REST_Request $req) {
    $raw = $req->get_param('skus');

    // принимает и "a,b,c", и skus[]=a&skus[]=b
    $skus = [];
    if (is_array($raw)) {
        $skus = $raw;
    } else {
        $skus = explode(',', (string)$raw);
    }

    $skus = array_values(array_unique(array_filter(array_map(function ($s) {
        $s = trim((string)$s);
        return $s !== '' ? $s : null;
    }, $skus))));

    if (empty($skus)) {
        return new WP_REST_Response(['error' => 'skus is required'], 400);
    }

    $q = new WP_Query([
        'post_type' => ['product', 'product_variation'],
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'meta_query' => [
            [
                'key' => '_sku',
                'value' => $skus,
                'compare' => 'IN',   // <-- ВОТ ЭТО КЛЮЧЕВО
            ]
        ],
    ]);

    $out = [];
    foreach ($q->posts as $pid) {
        $pid = (int)$pid;
        $sku = (string) get_post_meta($pid, '_sku', true);
        if ($sku === '') continue;
        $out[$sku] = ferma_build_stock_payload($pid);
    }

    return new WP_REST_Response($out, 200);
}


add_action('rest_api_init', function () {
    register_rest_route('ferma/v1', '/push/register', [
        'methods'  => 'POST',
        'callback' => 'ferma_push_register_device',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('ferma/v1', '/push/test', [
        'methods'  => 'POST',
        'callback' => 'ferma_push_test_send',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('ferma/v1', '/push/cart-sync', [
        'methods'  => 'POST',
        'callback' => 'ferma_push_cart_sync',
        'permission_callback' => '__return_true',
    ]);
});
function ferma_push_register_device(WP_REST_Request $request) {
    global $wpdb;

    $table = $wpdb->prefix . 'ferma_push_devices';

    if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) !== $table) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Push devices table not found',
            'table' => $table,
        ], 500);
    }

    $token = trim((string) $request->get_param('token'));
    $platform = trim((string) $request->get_param('platform'));
    $phone = ferma_normalize_phone($request->get_param('phone'));
    $user_id = absint($request->get_param('user_id'));
    $app_version = trim((string) $request->get_param('app_version'));

    if ($token === '' || strlen($token) < 50) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Invalid token',
        ], 400);
    }

    if ($platform === '') {
        $platform = 'android';
    }

    $now = current_time('mysql');

    $existing = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT id, token FROM {$table} WHERE token = %s LIMIT 1",
            $token
        )
    );

    if (!empty($wpdb->last_error)) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'DB select error',
            'db_error' => $wpdb->last_error,
        ], 500);
    }

    $data = [
        'user_id'      => $user_id ?: null,
        'phone'        => $phone ?: null,
        'platform'     => $platform,
        'app_version'  => $app_version ?: null,
        'is_active'    => 1,
        'last_seen_at' => $now,
        'updated_at'   => $now,
    ];

    if ($existing) {
        $updated = $wpdb->update(
            $table,
            $data,
            ['id' => (int) $existing->id],
            ['%d', '%s', '%s', '%s', '%d', '%s', '%s'],
            ['%d']
        );

        if ($updated === false) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'DB update error',
                'db_error' => $wpdb->last_error,
            ], 500);
        }

        return new WP_REST_Response([
            'success'   => true,
            'message'   => 'Device updated',
            'device_id' => (int) $existing->id,
        ], 200);
    }

    $inserted = $wpdb->insert(
        $table,
        [
            'user_id'      => $user_id ?: null,
            'phone'        => $phone ?: null,
            'token'        => $token,
            'platform'     => $platform,
            'app_version'  => $app_version ?: null,
            'is_active'    => 1,
            'last_seen_at' => $now,
            'created_at'   => $now,
            'updated_at'   => $now,
        ],
        ['%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s']
    );

    if ($inserted === false) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'DB insert error',
            'db_error' => $wpdb->last_error,
        ], 500);
    }

    $device_id = (int) $wpdb->get_var(
        $wpdb->prepare(
            "SELECT id FROM {$table} WHERE token = %s LIMIT 1",
            $token
        )
    );

    return new WP_REST_Response([
        'success'   => true,
        'message'   => 'Device registered',
        'device_id' => $device_id,
    ], 200);
}


function ferma_fcm_get_access_token() {
    $service_account_path = WP_CONTENT_DIR . '/uploads/ferma/firebase-service-account.json';

    if (!file_exists($service_account_path)) {
        return new WP_Error('missing_service_account', 'Firebase service account file not found');
    }

    $json = json_decode(file_get_contents($service_account_path), true);

    if (
        !$json ||
        empty($json['client_email']) ||
        empty($json['private_key']) ||
        empty($json['project_id'])
    ) {
        return new WP_Error('invalid_service_account', 'Invalid Firebase service account JSON');
    }

    $header = ['alg' => 'RS256', 'typ' => 'JWT'];
    $now = time();

    $claims = [
        'iss'   => $json['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud'   => 'https://oauth2.googleapis.com/token',
        'exp'   => $now + 3600,
        'iat'   => $now,
    ];

    $base64UrlEncode = function ($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    };

    $jwt_header = $base64UrlEncode(json_encode($header));
    $jwt_claims = $base64UrlEncode(json_encode($claims));
    $signature_input = $jwt_header . '.' . $jwt_claims;

    $private_key = $json['private_key'];
    $signature = '';

    $ok = openssl_sign($signature_input, $signature, $private_key, 'sha256WithRSAEncryption');
    if (!$ok) {
        return new WP_Error('jwt_sign_error', 'Failed to sign JWT');
    }

    $jwt = $signature_input . '.' . $base64UrlEncode($signature);

    $response = wp_remote_post('https://oauth2.googleapis.com/token', [
        'timeout' => 20,
        'body' => [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ],
    ]);

    if (is_wp_error($response)) {
        return $response;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (empty($body['access_token'])) {
        return new WP_Error('access_token_error', 'Failed to get Firebase access token', $body);
    }

    return [
        'access_token' => $body['access_token'],
        'project_id'   => $json['project_id'],
    ];
}

function ferma_send_fcm_push($token, $title, $body, $data = []) {
    $auth = ferma_fcm_get_access_token();

    if (is_wp_error($auth)) {
        return $auth;
    }

    $access_token = $auth['access_token'];
    $project_id = $auth['project_id'];

    $payload = [
        'message' => [
            'token' => $token,
            'notification' => [
                'title' => $title,
                'body'  => $body,
            ],
            'data' => array_map('strval', $data),
            'android' => [
                'priority' => 'high',
            ],
        ],
    ];

    $response = wp_remote_post(
        "https://fcm.googleapis.com/v1/projects/{$project_id}/messages:send",
        [
            'timeout' => 20,
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type'  => 'application/json',
            ],
            'body' => wp_json_encode($payload),
        ]
    );

    if (is_wp_error($response)) {
        return $response;
    }

    $status = wp_remote_retrieve_response_code($response);
    $resp_body = json_decode(wp_remote_retrieve_body($response), true);

    if ($status < 200 || $status >= 300) {
        return new WP_Error('fcm_send_error', 'FCM send failed', [
            'status' => $status,
            'body'   => $resp_body,
        ]);
    }

    return $resp_body;
}

function ferma_push_test_send(WP_REST_Request $request) {
    global $wpdb;

    $table = $wpdb->prefix . 'ferma_push_devices';

    $phone = ferma_normalize_phone($request->get_param('phone'));
    $user_id = absint($request->get_param('user_id'));

    if (!$phone && !$user_id) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'phone or user_id required',
        ], 400);
    }

    if ($user_id) {
        $devices = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table WHERE user_id = %d AND is_active = 1",
                $user_id
            )
        );
    } else {
        $devices = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table WHERE phone = %s AND is_active = 1",
                $phone
            )
        );
    }

    if (!$devices) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'No active devices found',
        ], 404);
    }

    $results = [];

    foreach ($devices as $device) {
        $result = ferma_send_fcm_push(
            $device->token,
            'Тестовый пуш',
            'Проверка отправки уведомлений',
            [
                'type' => 'test_push',
                'screen' => 'catalog',
            ]
        );

        $results[] = [
            'device_id' => (int) $device->id,
            'phone' => $device->phone,
            'result' => is_wp_error($result)
                ? [
                    'error' => $result->get_error_code(),
                    'message' => $result->get_error_message(),
                    'data' => $result->get_error_data(),
                ]
                : $result,
        ];
    }

    return [
        'success' => true,
        'results' => $results,
    ];
}

function ferma_normalize_phone($phone) {
    $phone = preg_replace('/\D+/', '', (string) $phone);

    if ($phone === '') {
        return '';
    }

    if (strpos($phone, '8') === 0) {
        $phone = '7' . substr($phone, 1);
    }

    if (strpos($phone, '7') !== 0) {
        $phone = '7' . $phone;
    }

    return $phone;
}

function ferma_push_install_table() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $devices = $wpdb->prefix . 'ferma_push_devices';
    $logs = $wpdb->prefix . 'ferma_push_logs';
    $carts = $wpdb->prefix . 'ferma_push_carts';

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $sql_devices = "CREATE TABLE $devices (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT UNSIGNED NULL,
        phone VARCHAR(32) NULL,
        token TEXT NOT NULL,
        platform VARCHAR(20) NOT NULL DEFAULT 'android',
        app_version VARCHAR(32) NULL,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        last_seen_at DATETIME NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        PRIMARY KEY (id),
        KEY user_id (user_id),
        KEY phone (phone),
        KEY is_active (is_active)
    ) $charset_collate;";

    $sql_logs = "CREATE TABLE $logs (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        phone VARCHAR(32) NULL,
        user_id BIGINT UNSIGNED NULL,
        push_type VARCHAR(64) NOT NULL,
        dedupe_key VARCHAR(191) NULL,
        title VARCHAR(255) NOT NULL,
        body TEXT NOT NULL,
        payload LONGTEXT NULL,
        sent_at DATETIME NOT NULL,
        status VARCHAR(32) NOT NULL DEFAULT 'sent',
        PRIMARY KEY (id),
        KEY phone (phone),
        KEY user_id (user_id),
        KEY push_type (push_type),
        KEY dedupe_key (dedupe_key)
    ) $charset_collate;";

    $sql_carts = "CREATE TABLE $carts (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        phone VARCHAR(32) NULL,
        user_id BIGINT UNSIGNED NULL,
        cart_hash VARCHAR(191) NULL,
        items LONGTEXT NULL,
        updated_at DATETIME NOT NULL,
        PRIMARY KEY (id),
        KEY phone (phone),
        KEY user_id (user_id),
        KEY cart_hash (cart_hash)
    ) $charset_collate;";

    dbDelta($sql_devices);
    dbDelta($sql_logs);
    dbDelta($sql_carts);
}

add_action('init', function () {
    if (get_option('ferma_push_tables_installed') !== '1') {
        ferma_push_install_table();
        update_option('ferma_push_tables_installed', '1');
    }
});
function ferma_push_cart_sync(WP_REST_Request $request) {
    global $wpdb;

    $table = $wpdb->prefix . 'ferma_push_carts';

    $phone = ferma_normalize_phone($request->get_param('phone'));
    $user_id = absint($request->get_param('user_id'));
    $items = $request->get_param('items');

    if (!$phone && !$user_id) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'phone or user_id required',
        ], 400);
    }

    if (!is_array($items)) {
        $items = [];
    }

    $now = current_time('mysql');
    $items_json = wp_json_encode($items);
    $cart_hash = md5($items_json . '|' . $phone . '|' . $user_id);

    $existing = null;

    if ($user_id) {
        $existing = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id FROM {$table} WHERE user_id = %d LIMIT 1",
                $user_id
            )
        );
    } elseif ($phone) {
        $existing = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id FROM {$table} WHERE phone = %s LIMIT 1",
                $phone
            )
        );
    }

    if ($existing) {
        $updated = $wpdb->update(
            $table,
            [
                'phone' => $phone ?: null,
                'user_id' => $user_id ?: null,
                'cart_hash' => $cart_hash,
                'items' => $items_json,
                'updated_at' => $now,
            ],
            ['id' => (int) $existing->id],
            ['%s', '%d', '%s', '%s', '%s'],
            ['%d']
        );

        if ($updated === false) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'DB update error',
                'db_error' => $wpdb->last_error,
            ], 500);
        }

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Cart updated',
        ], 200);
    }

    $inserted = $wpdb->insert(
        $table,
        [
            'phone' => $phone ?: null,
            'user_id' => $user_id ?: null,
            'cart_hash' => $cart_hash,
            'items' => $items_json,
            'updated_at' => $now,
        ],
        ['%s', '%d', '%s', '%s', '%s']
    );

    if ($inserted === false) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'DB insert error',
            'db_error' => $wpdb->last_error,
        ], 500);
    }

    return new WP_REST_Response([
        'success' => true,
        'message' => 'Cart saved',
    ], 200);
}
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
