<?php 

// редирект на страницу checkout из корзины
add_action( 'template_redirect', function() {
    if ( function_exists( 'is_cart' ) && is_cart() ) {
        $checkout_url = wc_get_checkout_url();
        wp_safe_redirect( $checkout_url );
        exit;
    }
} );


// ajax-add-to-cart - замена текста на кнопке в корзину и кнопка поделиться
add_action( 'wp_enqueue_scripts', function() {
    // Только на фронтенде
    wp_enqueue_script(
        'theme-ajax-add-to-cart',
        get_template_directory_uri() . '/assets/js/ajax-add-to-cart.js',
        array(), // зависимостей нет
        '1.0',
        true
    );

    // Передаём URL для AJAX‑запросов
    wp_localize_script(
        'theme-ajax-add-to-cart',
        'ajax_cart_params',
        array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
        )
    );
} );
// Фильтр фрагментов мини‑корзины — отдаём только содержимое контейнера
add_filter( 'woocommerce_add_to_cart_fragments', function( $fragments ) {
	if ( ! is_array( $fragments ) ) {
		$fragments = array();
	}
	// До полной инициализации корзины WC()->cart может быть null — иначе фатал на get_cart() / mini-cart.
	if ( ! function_exists( 'WC' ) || ! WC() || ! WC()->cart ) {
		$fragments['span.cart-count'] = '<span class="cart-count">0</span>';
		return $fragments;
	}
	// генерируем полный HTML мини‑корзины
	ob_start();
	wc_get_template( 'cart/mini-cart.php' );
	$mini_cart_html = ob_get_clean();

	$wrapper_html  = '<div class="cart__container">';
	$wrapper_html .= $mini_cart_html;
	$wrapper_html .= '</div>';

	$fragments['.cart__container'] = $wrapper_html;

	ob_start();
	echo '<span class="cart-count">' . count( WC()->cart->get_cart() ) . '</span>';
	$fragments['span.cart-count'] = ob_get_clean();

	return $fragments;
} );

// Добавляется класс add_to_cart_button к кнопке «В корзину» в лупе
add_filter( 'woocommerce_loop_add_to_cart_link', 'custom_loop_add_to_cart_add_class', 10, 2 );
function custom_loop_add_to_cart_add_class( $html, $product ) {
    // если класс уже есть — ничего не делаем
    if ( false !== strpos( $html, 'add_to_cart_button' ) ) {
        return $html;
    }

    $html = str_replace(
        'class="',
        'class="add_to_cart_button ',
        $html
    );

    return $html;
}



// Изменяем количество похожих товаров
add_filter('woocommerce_related_products_args', 'custom_related_products_args');
function custom_related_products_args($args) {
    $args['posts_per_page'] = 4; 
    $args['columns'] = 4; 
    return $args;
}

// Добавляем класс к кнопке "В корзину" только на странице карточки товара
add_filter('woocommerce_loop_add_to_cart_args', 'add_custom_class_to_add_to_cart_button', 10, 2);
function add_custom_class_to_add_to_cart_button($args, $product) {
    // Проверка
	if ( ( function_exists( 'is_product' ) && is_product() ) || ( function_exists( 'is_product_category' ) && is_product_category() ) || is_search() ) {
        // Удаление класса button и добавление shop-ferma__rel-add
        $args['class'] = str_replace('button', '', $args['class']);
        $args['class'] .= ' shop-ferma__rel-add';
    }
    return $args;
}


// Шорткод [user_bonus_count] — возвращает только число баллов
function render_user_bonus_count() {
    if ( ! function_exists( 'get_real_kilbil_bonus' ) ) {
        return ''; 
    }
    return (int) get_real_kilbil_bonus();
}
add_shortcode( 'user_bonus_count', 'render_user_bonus_count' );

// Хлебные крошки
add_action( 'woocommerce_before_single_product', 'custom_breadcrumbs_before_title', 3 );
function custom_breadcrumbs_before_title() {
    echo '<div class="woocommerce-breadcrumbs shop-ferma__breadcrumbs">';
    woocommerce_breadcrumb();
    echo '</div>';
}
add_filter( 'woocommerce_breadcrumb_defaults', 'custom_breadcrumb_separator' );
function custom_breadcrumb_separator( $defaults ) {
    $defaults['delimiter'] = ' <span class="breadcrumb-separator">&gt;</span> ';
    return $defaults;
}
add_filter( 'woocommerce_get_breadcrumb', 'custom_product_category_breadcrumbs', 10, 2 );
function custom_product_category_breadcrumbs( $crumbs, $breadcrumb ) {
	if ( function_exists( 'is_product_category' ) && is_product_category() ) {
        $current_term = get_queried_object();

        if ( is_a( $current_term, 'WP_Term' ) ) {
            $new_crumbs = array();

            // Добавляется ссылка на главную
            $new_crumbs[] = array( __( 'Главная', 'woocommerce' ), home_url() );

            // Цепочка родительских категорий
            $ancestors = get_ancestors( $current_term->term_id, 'product_cat' );
            $ancestors = array_reverse( $ancestors );

            foreach ( $ancestors as $ancestor_id ) {
                $ancestor = get_term( $ancestor_id, 'product_cat' );
                if ( ! is_wp_error( $ancestor ) ) {
                    $new_crumbs[] = array( $ancestor->name, get_term_link( $ancestor ) );
                }
            }

            // Добавляем текущую категорию
            $new_crumbs[] = array( $current_term->name, get_term_link( $current_term ) );

            return $new_crumbs;
        }
    }

    return $crumbs;
}


// Вывод заголовка и артикула карточки товара перед summury
add_action( 'woocommerce_before_single_product', 'custom_product_title_and_sku', 5 );
function custom_product_title_and_sku() {
    global $product;

    if ( ! is_product() || ! $product ) {
        return;
    }

    echo '<div class="shop-ferma__header-title">';
    echo '<h1 class="prod_tit">' . get_the_title() . '</h1>';
    echo '<span class="sku_wrapper">';
    esc_html_e( 'SKU:', 'woocommerce' );
    echo ' <span class="sku">';
    echo ( $sku = $product->get_sku() ) ? esc_html( $sku ) : esc_html__( 'N/A', 'woocommerce' );
    echo '</span></span>';
    echo '</div>';
}

// Вывод selections на карточке товара
add_action( 'woocommerce_after_single_product_summary', 'custom_product_selection_product', 110 );
function custom_product_selection_product() {
    global $product;

    if ( ! is_product() || ! $product ) {
        return;
    }
    ?>
    <section class="selection">
        <div class="container">
            <h3 class="selection__title page-title">ПОПУЛЯРНЫЕ КАТЕГОРИИ</h3>
            <div class="selection__inner">
                <div class="swiper selectionSwiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <a class="selection__item selection__item--green "
                                href="<? echo get_home_url(); ?>/product-category/green-prices/">
                                <p>ЗЕЛЕНЫЕ <br>ЦЕННИКИ</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/selection_1.png"
                                    alt="">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item"
                                href="<? echo get_home_url(); ?>/product-category/molochnaya-produkcziya/">
                                <p>Молочная <br>продукция</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/selection_2.png"
                                    alt="">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item"
                                href="<? echo get_home_url(); ?>/product-category/polufabrikaty-domashnie/">
                                <p>Домашние <br>полуфабрикаты</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/selection_3.png"
                                    alt="">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item"
                                href="<? echo get_home_url(); ?>/product-category/kopchenosti/">
                                <p>Мясные <br>деликатесы</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/selection_4.png"
                                    alt="">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/kolbasy/">
                                <p>Колбасные <br>изделия</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/selection_5.png"
                                    alt="">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/ovoshhi/">
                                <p>Овощи, фрукты, <br>грибы, ягоды</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/vegetables.svg"
                                    alt="Овощи, фрукты, грибы, ягоды">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item"
                                href="<? echo get_home_url(); ?>/product-category/domashnie-syry/">
                                <p>Ремесленные <br>сыры</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/cheese.svg"
                                    alt="Ремесленные сыры">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/yajczo/">
                                <p>Яйца</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/eggs.svg"
                                    alt="Яйца">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item"
                                href="<? echo get_home_url(); ?>/product-category/domashnyaya-konservacziya/">
                                <p>Домашняя консервация</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/canned-food.svg"
                                    alt="Домашняя консервация">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/myaso/">
                                <p>Мясо и <br>рыба</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/meat.svg"
                                    alt="Мясо и рыба">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item"
                                href="<? echo get_home_url(); ?>/product-category/remeslennyj-hleb-i-vypechka/">
                                <p>Ремесленный <br>хлеб</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/bread.svg"
                                    alt="Ремесленный хлеб">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/bakaleya/">
                                <p>Бакалея</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/grocery.svg"
                                    alt="Бакалея">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/varene/">
                                <p>Варенье, <br>соки, компоты</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/jam.svg"
                                    alt="Варенье, соки, компоты">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item"
                                href="<? echo get_home_url(); ?>/product-category/chaj-travy-i-dikorosy/">
                                <p>Чай и дикоросы</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/tea.svg"
                                    alt="Чай и дикоросы">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/med/">
                                <p>Мёд</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/med.svg" alt="Мёд">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/">
                                <p>Кулинария</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/cooking.svg"
                                    alt="Кулинария">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="swiper-button-next selectionSwiper-next"></div>
                <div class="swiper-button-prev selectionSwiper-prev"></div>
            </div>
        </div>
    </section>
    <?php
}

// delivery_address для карточки товара
add_action( 'woocommerce_after_single_product_summary', 'custom_product_delivery_address', 105 );
function custom_product_delivery_address() {
    global $product;

    if ( ! is_product() || ! $product ) {
        return;
    }
    ?>
        <button class="shop-ferma__delivery-address delivery-address open-modal1 open-modal__st">
            <img src="<?php bloginfo('template_url') ?>/assets/img/delivery-address.svg" alt="Адрес доставки">

            <? if ( is_user_logged_in() ) {
            $user_id = get_current_user_id();
            $row = get_user_meta( $user_id, 'delivery', true );
            if($row == '') {
                unset($row);
            }
            if (isset($row)) {
                if ($row == 1) {
                    echo '<p>Самовывоз:</p>';
                    $resultArray = get_user_meta( $user_id, 'billing_samoviziv', true );

                } 
                if ($row == 0) {
                    if(isset($_COOKIE['delivery_time']) && isset($_COOKIE['delivery_day'])) {
                        if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "express") {
                            echo 'Экспресс-доставка';
                        } else if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "morning") {
                            echo 'Доставка с&nbsp;10&nbsp;до&nbsp;12';
                        } else if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "day") {
                            echo 'Доставка с&nbsp;15&nbsp;до&nbsp;17';
                        } else if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "evening") {
                            echo 'Доставка с&nbsp;19&nbsp;до&nbsp;22';
                        } else if($_COOKIE['delivery_day'] == "tomorrow" && $_COOKIE['delivery_time'] == "morning") {
                            echo 'Завтра с&nbsp;10&nbsp;до&nbsp;12';
                        } else if($_COOKIE['delivery_day'] == "tomorrow" && $_COOKIE['delivery_time'] == "day") {
                            echo 'Завтра с&nbsp;15&nbsp;до&nbsp;17';
                        } else if($_COOKIE['delivery_day'] == "tomorrow" && $_COOKIE['delivery_time'] == "evening") {
                            echo 'Завтра с&nbsp;19&nbsp;до&nbsp;22';
                        }
                    } else {
                        echo 'Доставка:';
                    }
                    $cookieValue = get_user_meta( $user_id, 'billing_delivery', true );
                    $cookieArray = explode(',', $cookieValue);
                    $resultArray = array_slice($cookieArray, 2);
                    $resultArray = implode(',', $resultArray);
                }
            } else {
                echo '<p><span>ВЫБРАТЬ АДРЕС </span>для доставки</p>';
                // $resultArray = 'Выберите способ получения';
            }
            } else {
                $row = $_COOKIE['delivery'];
                if (isset($row)) {
                    if ($row == 1) {
                        echo '<p>Самовывоз:</p>';
                        $resultArray = $_COOKIE['billing_samoviziv'];
                    } 
                    if ($row == 0) {
                        if(isset($_COOKIE['delivery_time']) && isset($_COOKIE['delivery_day'])) {
                            if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "express") {
                                echo 'Экспресс-доставка';
                            } else if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "morning") {
                                echo 'Доставка с&nbsp;10&nbsp;до&nbsp;12';
                            } else if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "day") {
                                echo 'Доставка с&nbsp;15&nbsp;до&nbsp;17';
                            } else if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "evening") {
                                echo 'Доставка с&nbsp;19&nbsp;до&nbsp;22';
                            } else if($_COOKIE['delivery_day'] == "tomorrow" && $_COOKIE['delivery_time'] == "morning") {
                                echo 'Завтра с&nbsp;10&nbsp;до&nbsp;12';
                            } else if($_COOKIE['delivery_day'] == "tomorrow" && $_COOKIE['delivery_time'] == "day") {
                                echo 'Завтра с&nbsp;15&nbsp;до&nbsp;17';
                            } else if($_COOKIE['delivery_day'] == "tomorrow" && $_COOKIE['delivery_time'] == "evening") {
                                echo 'Завтра с&nbsp;19&nbsp;до&nbsp;22';
                            }
                        } else {
                            echo '<p>Доставка:</p>';
                        }
                        $cookieValue = $_COOKIE['billing_delivery'];
                        $cookieArray = explode(',', $cookieValue);
                        $resultArray = array_slice($cookieArray, 2);
                        $resultArray = implode(',', $resultArray);
                    }
                } else {
                    echo '<p><span>ВЫБРАТЬ АДРЕС </span>для доставки</p>';
                    // $resultArray = 'Выберите способ получения';
                    
                }
            }
            ?>
                <p class="delivery-address__result"><span>
                        <?echo $resultArray;?></span></p>
        </button>
    <?php
}
// delivery adress для ПК версии
add_action( 'woocommerce_single_product_summary', 'desktop_product_delivery_address', 55 );
function desktop_product_delivery_address() {
    global $product;

    if ( ! is_product() || ! $product ) {
        return;
    }
    ?>
        <button class="shop-ferma__delivery-address-pc delivery-address open-modal1 open-modal__st">
            <img src="<?php bloginfo('template_url') ?>/assets/img/delivery-address.svg" alt="Адрес доставки">

            <? if ( is_user_logged_in() ) {
            $user_id = get_current_user_id();
            $row = get_user_meta( $user_id, 'delivery', true );
            if($row == '') {
                unset($row);
            }
            if (isset($row)) {
                if ($row == 1) {
                    echo '<p>Самовывоз:</p>';
                    $resultArray = get_user_meta( $user_id, 'billing_samoviziv', true );

                } 
                if ($row == 0) {
                    if(isset($_COOKIE['delivery_time']) && isset($_COOKIE['delivery_day'])) {
                        if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "express") {
                            echo 'Экспресс-доставка';
                        } else if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "morning") {
                            echo 'Доставка с&nbsp;10&nbsp;до&nbsp;12';
                        } else if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "day") {
                            echo 'Доставка с&nbsp;15&nbsp;до&nbsp;17';
                        } else if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "evening") {
                            echo 'Доставка с&nbsp;19&nbsp;до&nbsp;22';
                        } else if($_COOKIE['delivery_day'] == "tomorrow" && $_COOKIE['delivery_time'] == "morning") {
                            echo 'Завтра с&nbsp;10&nbsp;до&nbsp;12';
                        } else if($_COOKIE['delivery_day'] == "tomorrow" && $_COOKIE['delivery_time'] == "day") {
                            echo 'Завтра с&nbsp;15&nbsp;до&nbsp;17';
                        } else if($_COOKIE['delivery_day'] == "tomorrow" && $_COOKIE['delivery_time'] == "evening") {
                            echo 'Завтра с&nbsp;19&nbsp;до&nbsp;22';
                        }
                    } else {
                        echo 'Доставка:';
                    }
                    $cookieValue = get_user_meta( $user_id, 'billing_delivery', true );
                    $cookieArray = explode(',', $cookieValue);
                    $resultArray = array_slice($cookieArray, 2);
                    $resultArray = implode(',', $resultArray);
                }
            } else {
                echo '<p><span>ВЫБРАТЬ АДРЕС </span>для доставки</p>';
                // $resultArray = 'Выберите способ получения';
            }
            } else {
                $row = $_COOKIE['delivery'];
                if (isset($row)) {
                    if ($row == 1) {
                        echo '<p>Самовывоз:</p>';
                        $resultArray = $_COOKIE['billing_samoviziv'];
                    } 
                    if ($row == 0) {
                        if(isset($_COOKIE['delivery_time']) && isset($_COOKIE['delivery_day'])) {
                            if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "express") {
                                echo 'Экспресс-доставка';
                            } else if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "morning") {
                                echo 'Доставка с&nbsp;10&nbsp;до&nbsp;12';
                            } else if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "day") {
                                echo 'Доставка с&nbsp;15&nbsp;до&nbsp;17';
                            } else if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "evening") {
                                echo 'Доставка с&nbsp;19&nbsp;до&nbsp;22';
                            } else if($_COOKIE['delivery_day'] == "tomorrow" && $_COOKIE['delivery_time'] == "morning") {
                                echo 'Завтра с&nbsp;10&nbsp;до&nbsp;12';
                            } else if($_COOKIE['delivery_day'] == "tomorrow" && $_COOKIE['delivery_time'] == "day") {
                                echo 'Завтра с&nbsp;15&nbsp;до&nbsp;17';
                            } else if($_COOKIE['delivery_day'] == "tomorrow" && $_COOKIE['delivery_time'] == "evening") {
                                echo 'Завтра с&nbsp;19&nbsp;до&nbsp;22';
                            }
                        } else {
                            echo '<p>Доставка:</p>';
                        }
                        $cookieValue = $_COOKIE['billing_delivery'];
                        $cookieArray = explode(',', $cookieValue);
                        $resultArray = array_slice($cookieArray, 2);
                        $resultArray = implode(',', $resultArray);
                    }
                } else {
                    echo '<p><span>ВЫБРАТЬ АДРЕС </span>для доставки</p>';
                    // $resultArray = 'Выберите способ получения';
                    
                }
            }
            ?>
                <p class="delivery-address__result"><span>
                        <?echo $resultArray;?></span></p>
        </button>
    <?php
}

// Отключен вывод стандартных рекомендаций товаров
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
// Вывод после summary
add_action( 'woocommerce_after_single_product_summary', 'custom_output_related_products', 120 );
function custom_output_related_products() {
    woocommerce_output_related_products();
}

// Отключен вывод табов (вся информация уже выводится в карточке)
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
// Вывод form для добавление в корзину
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
add_action( 'woocommerce_after_single_product_summary', 'custom_output_add_to_cart_outside_summary', 5 );
function custom_output_add_to_cart_outside_summary() {
    global $product;

    // Только для простых товаров. Для вариативных и других типов нужно обрабатывать отдельно.
    if ( $product && $product->is_type( 'simple' ) ) {
        woocommerce_template_single_add_to_cart();
    }
}
// Отключил шаблон price.php
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );


// Вывод описания вверху summary
add_action( 'woocommerce_single_product_summary', 'custom_move_product_description_to_summary_top', 1 );
function custom_move_product_description_to_summary_top() {
	if ( ! is_product() ) return;

	global $post;

	$content = apply_filters( 'the_content', $post->post_content );
	echo '<div class="shop-ferma__description">';
	echo $content;
	echo '</div>';
}


// Открываем обертку перед галереей
add_action( 'woocommerce_before_single_product_summary', 'custom_open_gallery_summary_wrapper', 5 );
function custom_open_gallery_summary_wrapper() {
    echo '<div class="shop-ferma__wrapper-summary">';
}
// Закрываем обертку после summary
add_action( 'woocommerce_after_single_product_summary', 'custom_close_gallery_summary_wrapper', 100 );
function custom_close_gallery_summary_wrapper() {
    echo '</div><!-- shop-ferma__wrapper-summary -->';
}


// Страница "О нас"
add_action('wp_enqueue_scripts', function() {
    if (is_page('about')) {
        wp_enqueue_script(
            'page-about',
            get_template_directory_uri() . '/assets/js/page-about.js',
            array(), 
            '1.0',
            true
        );
    }
});
// Страница "Блог"
// add_action('wp_enqueue_scripts', function() {
//     if (is_page('blog')) {
//         wp_enqueue_script(
//             'page-blog',
//             get_template_directory_uri() . '/assets/js/page-blog.js',
//             array(), 
//             '1.0',
//             true
//         );
//     }
// });


function load_admin_styles() {
    wp_enqueue_style('admin_css', get_template_directory_uri() . '/assets/css/admin-styles.css', false, '1.0.2');
}
add_action('admin_enqueue_scripts', 'load_admin_styles');

// Подключение шаблона single-post.php для рубрик scontentsk и recipe
function custom_single_template($template) {
    global $post;
    
    if (is_single() && $post->post_type == 'post') {
        $categories = get_the_category($post->ID);
        $category_slugs = wp_list_pluck($categories, 'slug');
        
        // Применяем шаблон для рубрик "scontentsk" и "recipe"
        if (in_array('scontentsk', $category_slugs) || in_array('recipe', $category_slugs)) {
            $new_template = locate_template('single-post.php');
            if (!empty($new_template)) {
                return $new_template;
            }
        }
    }
    return $template;
}
add_filter('single_template', 'custom_single_template');
// Отключение старых стилей при использовании шаблона single-post.php и 404.php
function remove_main_style_for_custom_template() {
    // Проверяем: либо это пост в нужных категориях, либо это 404-страница
    if (
        ( is_single() && has_category( array( 'scontentsk', 'recipe', 'fermerskij-blog' ) ) )
        || is_404()
    ) {
        global $wp_styles;
        $main_style_url = get_stylesheet_uri();

        foreach ( $wp_styles->queue as $handle ) {
            if ( isset( $wp_styles->registered[ $handle ] ) && 
                 strpos( $wp_styles->registered[ $handle ]->src, $main_style_url ) !== false ) {
                wp_dequeue_style( $handle );
                wp_deregister_style( $handle );
            }
        }
    }
}
add_action( 'wp_enqueue_scripts', 'remove_main_style_for_custom_template', 100 );


// Создание содержания по кнопке
add_action('admin_init', 'add_contents_button_to_editor');
function add_contents_button_to_editor() {
    // Добавляем кнопку только для постов в нужной рубрике
    add_filter('mce_buttons', function($buttons) {
        global $post;
        if ($post && has_category('fermerskij-blog', $post)) {
            array_push($buttons, 'contents_button');
        }
        return $buttons;
    });
    
    // Регистрируем скрипт для кнопки с версионированием
    add_filter('mce_external_plugins', function($plugins) {
        global $post;
        if ($post && has_category('fermerskij-blog', $post)) {
            // Получаем путь к файлу
            $script_path = get_template_directory() . '/admin-contents.js';
            
            // Добавляем параметр версии на основе времени изменения файла
            $plugins['contents_button'] = add_query_arg(
                'ver', 
                filemtime($script_path), 
                get_template_directory_uri() . '/admin-contents.js'
            );
        }
        return $plugins;
    });
}
add_action('enqueue_block_editor_assets', 'add_contents_button_to_gutenberg');
function add_contents_button_to_gutenberg() {
    global $post;
    if (!$post || !has_category('fermerskij-blog', $post)) return;

    // Получаем путь к файлу
    $script_path = get_template_directory() . '/gutenberg-contents.js';
    
    // Проверяем существование файла
    if (!file_exists($script_path)) {
        error_log("Файл скрипта не найден: $script_path");
        return;
    }

    wp_enqueue_script(
        'gutenberg-contents-button',
        get_template_directory_uri() . '/gutenberg-contents.js',
        array('wp-element', 'wp-editor', 'wp-components', 'wp-i18n'),
        filemtime($script_path), // Версия = время последнего изменения
        true
    );
}
add_action('wp_footer', 'debug_checkout_notices');
function debug_checkout_notices() {
	if ( function_exists( 'is_checkout' ) && is_checkout() && function_exists( 'WC' ) && WC() && WC()->session ) {
		echo '<script>console.log("WC Notices:", ' . wp_json_encode( WC()->session->get( 'wc_notices', array() ) ) . ');</script>';
	}
}

// --- Посты рубрики "Вопрос-ответ" --- 
add_action('init', 'register_cpt_question_answer');
function register_cpt_question_answer() {

    $labels = array(
        'name'               => 'Вопросы и ответы',
        'singular_name'      => 'Вопрос-Ответ',
        'menu_name'          => 'FAQ',
        'name_admin_bar'     => 'FAQ',
        'add_new'            => 'Добавить вопрос',
        'add_new_item'       => 'Добавить новый вопрос',
        'edit_item'          => 'Редактировать вопрос',
        'new_item'           => 'Новый вопрос',
        'view_item'          => 'Просмотр вопроса',
        'search_items'       => 'Искать вопросы',
        'not_found'          => 'Ничего не найдено',
        'not_found_in_trash' => 'Ничего не найдено в корзине',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_in_menu'       => true,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-editor-help',
        'supports'           => array( 'title', 'editor' ), // заголовок + контент
        'has_archive'        => false,
        'rewrite'            => array( 'slug' => 'faq' ),
        'show_in_rest'       => true, // поддержка Gutenberg и REST API (для ACF)
    );

    register_post_type('question_answer', $args);
}
add_action( 'init', 'register_tax_question_answer_category' );
function register_tax_question_answer_category() {

    $labels = array(
        'name'              => 'Категории FAQ',
        'singular_name'     => 'Категория FAQ',
        'search_items'      => 'Искать категории',
        'all_items'         => 'Все категории',
        'edit_item'         => 'Редактировать категорию',
        'update_item'       => 'Обновить категорию',
        'add_new_item'      => 'Добавить новую категорию',
        'new_item_name'     => 'Название новой категории',
        'menu_name'         => 'Категории',
    );

    $args = array(
        'hierarchical'      => true, // как рубрики
        'labels'            => $labels,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'faq-category' ),
    );

    register_taxonomy('question_answer_category', array('question_answer'), $args);
}
