<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS: non-blocking load -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"
          media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"></noscript>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;700&display=swap" rel="stylesheet">

    <!-- Яндекс Карты: НЕ грузим здесь, lazy load при открытии модалки -->

    <?php wp_head(); ?>

    <script src="/wp-content/themes/theme/js/cookie.js" defer></script>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-PS4BM6F');</script>
    <!-- End Google Tag Manager -->

    <meta name="yandex-verification" content="02de7abccf41bbae" />
    <meta name="yandex-verification" content="50671f6ce40cf19f" />
    <meta name="mailru-domain" content="Rsg5YmdfoMnfaRN0" />

    <style>
        /* ===== CONSOLIDATED CRITICAL CSS ===== */
        html, body {
            width: 100%;
            margin: 0;
            padding: 0;
            font-family: Arial;
            font-size: 14px;
            background: #f3f0eb;
            font-family: 'Roboto', sans-serif;
        }

        /* Head separator: фиксированная высота чтобы не было CLS */
        .head-sep {
            height: 160px; /* desktop default */
        }
        @media(max-width: 1070px) { .head-sep { height: 140px; } }
        @media(max-width: 991px) { .head-sep { height: 60px; } }

        .head_fix {
            position: fixed;
            top: 0;
            background: white;
            z-index: 98;
            box-shadow: 0px 0px 8px 2px rgba(0, 0, 0, 0.1);
        }
        .head_menu {
            padding-top: 16px;
            padding-bottom: 16px;
        }
        .head_menu .second_menu { padding-top: 0px; }

        .top_head {
            background: #6ba802;
            color: white;
            padding: 15px;
            max-width: 1140px;
        }
        .top_head a {
            color: white;
            margin-right: 10px;
            transition: 0.3s;
        }
        .top_head a:hover {
            color: #fbe018;
            text-decoration: none;
        }

        .des_rlogo {
            padding-top: 25px;
            font-size: 20px;
            line-height: 0.5;
            margin-bottom: 0px;
            font-weight: 700;
            font-size: xx-large;
        }
        .open-modal__st {
            display: inline;
            cursor: pointer;
            color: white !important;
            text-align: center;
            border-radius: 10px;
            background: #3d7739;
            text-decoration: none;
            padding: 13px;
            font-size: 24px;
            line-height: 1;
            font-weight: 700;
            transition: 0.3s;
            font-size: 17px;
            display: block;
            width: auto;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .row.jc-center { justify-content: center; }

        .second_menu { padding: 0px; padding-top: 20px; }
        .second_menu a {
            display: inline;
            margin-right: 10px;
            color: grey;
            font-weight: 700;
            transition: 0.3s;
        }
        .second_menu a:hover { color: red; text-decoration: none; }

        .main_menu {
            border-bottom: 1px solid gainsboro;
            padding-bottom: 10px;
            margin-top: 1em;
            padding-top: 10px;
            justify-content: center;
        }
        .row.main_menu a.item_m img { height: 64px; }

        a.item_m {
            display: block;
            padding: 5px;
            transition: 0.3s;
            text-align: center;
            border-radius: 10px;
            width: 140px;
            font-size: 13px;
            color: #6ba802;
            font-weight: 700;
        }
        a.item_m span {
            display: block;
            background: #6ba802;
            border-radius: 5px;
            padding: 3px;
            color: #fff;
        }
        a.item_m.green-friday span { background: #000; }
        a.item_m:hover {
            box-shadow: 0px 0px 8px 2px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            color: black;
        }

        .menu_pic { padding-top: 130px; }
        .container.menu_pic.d-none.d-lg-block { padding-top: 20px !important; }

        .slider_home { padding: 0px; }
        .slider_home.mrg-0 { margin: 0 !important; }

        .footer_ferma {
            color: white;
            padding-top: 20px;
            padding-bottom: 20px;
        }
        .footer_ferma a {
            color: white;
            display: block;
            margin-bottom: 10px;
            transition: 0.3s;
        }
        .footer_ferma a:hover {
            text-decoration: none;
            color: rgb(226, 223, 25);
        }
        .yit_footer {
            font-weight: bold;
            display: block;
            margin-bottom: 20px;
            color: #ffeb3b;
        }

        .info_page { margin-top: 40px; }
        .breadcums a { color: #6ba802; }
        .adress_right { font-size: 14px; padding-top: 5px; }
        .serach_new { padding-top: 10px; }
        .prod_tit { color: black; font-weight: 700; font-size: 24px; margin-top: -20px; }
        .products.pb-0 { padding-bottom: 0 !important; }

        /* WooCommerce overrides */
        .woocommerce-tabs.wc-tabs-wrapper { display: none; }
        .woocommerce #respond input#submit.alt,
        .woocommerce a.button.alt,
        .woocommerce button.button.alt,
        .woocommerce input.button.alt {
            background-color: #6ba802;
            color: #fff;
        }
        .woocommerce ul.products li.product .woocommerce-loop-category__title,
        .woocommerce ul.products li.product .woocommerce-loop-product__title,
        .woocommerce ul.products li.product h3 {
            padding: .5em 0;
            margin: 0;
            font-size: 1em;
            color: black;
            font-weight: 700;
            height: auto !important;
            min-height: 52px;
        }
        .woocommerce #respond input#submit,
        .woocommerce a.button,
        .woocommerce button.button,
        .woocommerce input.button {
            font-size: 100%;
            margin: 0;
            line-height: 1;
            cursor: pointer;
            position: relative;
            text-decoration: none;
            overflow: visible;
            padding: .618em 1em;
            font-weight: 700;
            border-radius: 3px;
            left: auto;
            color: #ffffff;
            background-color: #6ba802;
            border: 0;
            display: inline-block;
            background-image: none;
            box-shadow: none;
            text-shadow: none;
        }
        span.woocommerce-Price-amount.amount { font-weight: 700; font-size: 18px; color: black; }
        mark.count { display: none; }

        .product-category {
            background: white;
            text-align: center;
            border-radius: 10px;
            transition: 0.3s;
        }
        .product-category img {
            border-radius: 10px 10px 0px 0px;
            height: 250px !important;
            object-fit: cover;
            width: 100%;
        }
        .product-category:hover {
            box-shadow: 0px 0px 17px 9px rgba(0, 0, 0, 0.07);
        }

        a.added_to_cart.wc-forward { display: none; }
        .dropdown, .dropup { position: relative; width: 50px !important; display: inline; }
        .dropdown:hover>.dropdown-menu { display: block; }
        .dropdown>.dropdown-toggle:active { pointer-events: none; }

        li.product {
            transition: 0.3s;
            text-align: center;
            padding-bottom: 20px !important;
            background: white;
            border-radius: 10px;
        }
        a.add_to_cart_button { background: #6ba802 !important; transition: 0.3s; }
        a.add_to_cart_button:hover { background: #507a06 !important; color: white !important; }
        li.product:hover { box-shadow: 0px 0px 21px 0px rgba(34, 60, 80, 0.25); }

        button, input[type="button"], input[type="reset"], input[type="submit"] {
            border: 1px solid;
            border-color: #ccc #ccc #bbb;
            border-radius: 3px;
            background: black;
            color: white;
            font-size: 0.75rem;
            line-height: 1;
            padding: .8em 1em .7em;
            cursor: pointer;
        }
        input.search-field { width: 285px !important; padding-left: 10px; }
        form.search-form { margin-top: 15px; }

        .but {
            font-size: 100%; margin: 0; line-height: 1; cursor: pointer;
            position: relative; text-decoration: none; overflow: visible;
            padding: 0.618em 1em; font-weight: 700; border-radius: 3px;
            left: auto; color: #ffffff; background-color: #6ba802; border: 0;
            display: inline-block; background-image: none; box-shadow: none; text-shadow: none;
        }
        .xoo-wsc-basket { display: block !important; }
        .xoo-wsc-footer { z-index: 111111111111111; }
        .xoo-wsc-pname a { color: red; }
        .xoo-wsc-ft-buttons-cont a { color: black; }
        span.xoo-wsc-ft-amt-label { display: none; }
        .xoo-wsc-ft-amt-value::before { content: "Итого: "; }
        a.xoo-wsc-ft-btn.button.btn.xoo-wsc-cart-close.xoo-wsc-ft-btn-continue { display: none; }
        a.xoo-wsc-ft-btn.button.btn.xoo-wsc-ft-btn-cart { display: none; }
        a.xoo-wsc-ft-btn.button.btn.xoo-wsc-ft-btn-checkout { background: #4caf50; color: white; }

        .coupon { display: none; }
        .related .qib-container { display: none !important; }
        .wpcf7-form input { margin-bottom: 10px !important; padding-left: 15px; width: 100%; }
        input.wpcf7-form-control.wpcf7-submit { cursor: pointer; }

        .new_life {
            position: fixed;
            z-index: 99999;
            top: 5px;
            left: 50px;
            color: white;
            font-size: 14px;
            line-height: 1.3;
        }

        .mobmenu-content li a {
            display: block;
            letter-spacing: 1px;
            padding: 5px 20px;
            text-decoration: none;
            font-size: 14px;
            text-decoration: underline;
        }

        article { width: 30%; float: left; margin: 10px; }
        article p { display: none; }
        article a { font-size: 16px; color: black; }

        .discount-offset { padding-top: 32px; }
        .new-price { margin-top: 6px; }
        .date-label {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #6ba802;
            border-radius: 4px;
            font-size: 12px;
            padding: 3px;
            color: #fff;
        }

        /* Responsive */
        @media(max-width: 1200px) {
            .container.max-row_2 { max-width: 1148px; }
            .container.max-row_2 .item_m { width: 120px; }
        }
        @media(min-width: 1200px) {
            .open-modal1 { font-size: 24px; }
            .open-modal1 span { font-size: 17px; }
        }
        @media(max-width: 1070px) {
            .head_menu { max-width: 1060px; padding-top: 18px; padding-bottom: 14px; }
            .head_menu img[src="https://ferma-dv.ru/wp-content/uploads/2022/07/Logotype-svg.svg"] { height: 48px !important; }
            .head_menu .second_menu { padding-top: 0; }
            .head_menu .dgwt-wcas-no-submit .dgwt-wcas-sf-wrapp input[type=search].dgwt-wcas-search-input { padding: 5px 15px 5px 40px; }
            .head_menu .dgwt-wcas-sf-wrapp input[type=search].dgwt-wcas-search-input { height: 30px; }
            .top_head .row.jc-center .col-6 img { height: 20px !important; }
            .des_rlogo { padding-top: 0; font-size: 18px; }
            .open-modal1 { font-size: 18px; }
            .open-modal1 span { font-size: 14px; }
            .row.jc-center { justify-content: space-between; align-items: flex-start; }
            .container.menu_pic.d-none.d-lg-block { padding-top: 8em !important; }
            .top_head { padding: 10px; }
        }
        @media(max-width: 1025px) {
            .container.menu_pic.d-none.d-lg-block { padding-top: 5em !important; }
        }
        @media(max-width: 1000px) {
            .product-category img { height: 150px !important; }
            .mob-menu-header-holder { width: 100%; background-color: #68aa2f; height: 50px; position: fixed; }
            .xoo-wsc-basket { top: 2vw; right: -5px; }
        }
        @media(min-width: 992px) {
            .mobile-search { display: none; }
            .logo-flex { display: flex; }
        }
        @media(max-width: 992px) {
            .slider_home.d-none { display: block !important; }
            .slider_home { margin-bottom: -50px; margin-top: -50px; }
        }
        @media(max-width: 768px) {
            .slider_home { margin-bottom: -100px; margin-top: -50px; }
            .discount-offset { padding-top: 34px; }
            .discount-offset span { display: block; }
            .menu-item-9430 a {
                display: inline-block;
                background: #6ba802;
                border-radius: 5px;
                padding: 3px;
                color: #fff !important;
            }
            .woocommerce ul.products li.product .woocommerce-loop-product__title { min-height: 72px; }
        }
        @media(max-width: 500px) {
            .xoo-wsc-basket { right: 16em !important; }
            #telegrambott { top: 88% !important; }
            .menumobile { display: block; }
            .modal-content1 { margin: 0 !important; border-radius: 0 !important; }
        }
        @media(max-width: 425px) {
            .slider_home { margin-bottom: -70px; margin-top: -30px; }
        }
        @media(max-width: 2560px) {
            .slider_home { margin-bottom: -150px; margin-top: 30px; }
        }
        @media(max-width: 1440px) {
            .slider_home { margin-bottom: -150px; margin-top: -70px; }
        }

        .mobile-search { margin-top: 20px; }

        /* Modal styles (loaded but modal HTML is deferred) */
        .modal1, .modal2 {
            display: none;
            position: fixed;
            z-index: 11111111111;
            left: 0; top: 0;
            width: 100%; height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content1, .modal-content2, .modal-content {
            background-color: white;
            margin: 2% auto;
            padding: 20px;
            padding-bottom: 0px;
            border-radius: 5px;
            min-width: 300px;
            max-width: 700px;
            position: relative;
        }
        .close-modal1, .close-modal2, .close {
            position: absolute;
            top: 10px; right: 10px;
            font-size: 30px;
            font-weight: bold;
            cursor: pointer;
        }
        .tab-container1 { margin-top: 5px; }
        .tab-button1 {
            background-color: lightgreen;
            color: white;
            border: none;
            padding: 10px 20px;
            width: 49%;
            font-size: 17px;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
            cursor: pointer;
        }
        .tab-button1.active1 { background-color: green; }
        .tab-content1 { display: none; padding: 20px; padding-top: 5px; }
        .tab-content1.active1 { display: block; }
        @media (max-width: 500px) { .tab-content1 { padding: 5px; } }

        #map, #map2 { width: 100%; height: 250px; }
        #suggest, #suggest1 {
            height: 50px; width: 100%;
            background: #eee; border-radius: 15px;
            padding-left: 20px; border: 1px solid #008000;
        }
        .disabled-btn { pointer-events: none; cursor: default; }
        .enable-dev { background: #2dbe64 !important; color: white !important; }
        .mainblock_time, .mainblock_time1 {
            display: flex; padding: 10px; border-radius: 18px;
            cursor: pointer; width: fit-content; align-items: center;
            border: 1px solid; justify-content: center;
        }
        .mainblock_time_express { grid-column: 1 / -1; max-width: 100%; }
        .underblocktime { height: fit-content; display: block; align-items: center; font-size: 15px; justify-content: center; }
        .underblocktime > p { margin: 0; text-align: center; }
        .enable { border-color: #2dbe64 !important; color: #2dbe64 !important; }
        .enable1 { border-color: #2dbe64 !important; background: #2dbe77; color: white; }
        .notenable { display: none; }
        .market_el p { margin-bottom: 0px !important; }
        .market_el {
            display: flex; border-bottom: 1px solid #afafaf;
            align-items: baseline; margin-bottom: 14px; padding-bottom: 14px;
        }
        .mainblock_time1 { margin-left: auto; }
        #billing_asdx1, #billing_type_delivery_sam { height: 35px; }
        .ymaps-2-1-79-gototech, .ymaps-2-1-79-map-copyrights-promo { display: none; }
        @media (max-width: 500px) { .first_part_time { width: 40% !important; } }
        .open-modal1, .open-modal2 {
            background-color: green; color: white;
            width: 100%; max-width: 347px;
            border: none; padding: 10px 20px;
            border-radius: 5px; cursor: pointer;
        }
    </style>
</head>

<body>
<?php
// ===== PHP LOGIC (no output before this) =====
$user_id = get_current_user_id();
$args_check = isset($_COOKIE['market']) ? $_COOKIE['market'] : '';
$url = $_SERVER['REQUEST_URI'];

// Cookie/POST handling
if (!empty($_POST["asd"])) {
    SetCookie("market", $_POST["asd"], time()+86400, '/');
    $_COOKIE["market"] = $_POST["asd"];
}
if (!empty($_POST["asd1"])) {
    SetCookie("market", $_POST["asd1"], time()+86400, '/');
    $_COOKIE["market"] = $_POST["asd1"];
}
if (!empty($_POST["vib"])) {
    SetCookie("vibor", $_POST["vib"], time()+86400, '/');
    $_COOKIE["vibor"] = $_POST["vib"];
}

// Redirect logic
if ($url == "/my-account/user-market/") {
    header('Location: https://ferma-dv.ru/user-market/');
    exit;
}

if (is_product_category()) {
    $url_parsed = $_SERVER['REQUEST_URI'];
    $parts = parse_url($url_parsed);
    parse_str(isset($parts['query']) ? $parts['query'] : '', $query);
    $check = isset($query['wms-addon-store-filter-form'][0]) ? $query['wms-addon-store-filter-form'][0] : null;
    $check1 = isset($query['post_type']) ? $query['post_type'] : null;
    $term_id = get_queried_object_id();
    $term_link = get_term_link($term_id);
    $delivery_cookie = isset($_COOKIE['delivery']) ? $_COOKIE['delivery'] : null;
    $key_market_cookie = isset($_COOKIE['key_market']) ? $_COOKIE['key_market'] : '';

    if ($delivery_cookie == 0) {
        if ($check != null) {
            header('Location: ' . $term_link);
            exit;
        }
    } else {
        if ($check != null && empty($check1)) {
            if ($check != $key_market_cookie) {
                header('Location: ' . $term_link . '?wms-addon-store-filter-form%5B0%5D=' . $key_market_cookie);
                exit;
            }
        }
        if ($check == null && $key_market_cookie) {
            header('Location: ' . $term_link . '?wms-addon-store-filter-form%5B0%5D=' . $key_market_cookie);
            exit;
        }
        if (!empty($check1)) {
            if ((is_user_logged_in() && get_user_meta(get_current_user_id(), 'delivery', true) == '0') ||
                (!is_user_logged_in() && $delivery_cookie == 0)) {
                header('Location: ' . $term_link);
                exit;
            } else {
                if ($check != $key_market_cookie) {
                    header('Location: ' . $term_link . '?post_type=page&wms-addon-store-filter-form%5B0%5D=' . $key_market_cookie);
                    exit;
                }
            }
        }
    }
}

// Helper: generate store filter URL suffix
function fdv_store_filter_suffix($args_check) {
    $map = [
        'ГринМаркет ТЦ Море' => 'cab1caa9-da10-11eb-0a80-07410026c356',
        'Жигура'              => '8cc659e5-4bfb-11ec-0a80-075000080e54',
        'Реми-Сити'           => 'b24e4c35-9609-11eb-0a80-0d0d008550c2',
        'Эгершельд'           => '7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93',
        'Космос'              => 'a99d6fdf-0970-11ed-0a80-0ed600075845',
        'Уссурийск'           => '9c9dfcc4-733f-11ec-0a80-0da1013a560d',
    ];
    if (isset($map[$args_check])) {
        return '?wms-addon-store-filter-form%5B0%5D=' . $map[$args_check];
    }
    return '';
}
$store_suffix = fdv_store_filter_suffix($args_check);

// Determine delivery display text
$delivery_display = '';
$delivery_address = 'Выберите способ получения';

if (is_user_logged_in()) {
    $row = get_user_meta($user_id, 'delivery', true);
    if ($row === '') {
        $delivery_display = 'Доставка или самовывоз';
        $delivery_address = 'Выберите способ получения';
    } elseif ($row == 1) {
        $delivery_display = 'Самовывоз';
        $delivery_address = get_user_meta($user_id, 'billing_samoviziv', true);
    } elseif ($row == 0) {
        $delivery_display = 'Доставка';
        if (isset($_COOKIE['delivery_day']) && isset($_COOKIE['delivery_time'])) {
            $dd = $_COOKIE['delivery_day'];
            $dt = $_COOKIE['delivery_time'];
            $time_labels = [
                'today_express'  => 'Экспресс-доставка',
                'today_morning'  => 'Доставка с&nbsp;10&nbsp;до&nbsp;12',
                'today_day'      => 'Доставка с&nbsp;15&nbsp;до&nbsp;17',
                'today_evening'  => 'Доставка с&nbsp;19&nbsp;до&nbsp;22',
                'tomorrow_morning' => 'Завтра с&nbsp;10&nbsp;до&nbsp;12',
                'tomorrow_day'   => 'Завтра с&nbsp;15&nbsp;до&nbsp;17',
                'tomorrow_evening' => 'Завтра с&nbsp;19&nbsp;до&nbsp;22',
            ];
            $key = $dd . '_' . $dt;
            if (isset($time_labels[$key])) {
                $delivery_display = $time_labels[$key];
            }
        }
        $cookieValue = get_user_meta($user_id, 'billing_delivery', true);
        $cookieArray = explode(',', $cookieValue);
        $delivery_address = implode(',', array_slice($cookieArray, 2));
    }
} else {
    $row = isset($_COOKIE['delivery']) ? $_COOKIE['delivery'] : null;
    if ($row === null || $row === '') {
        $delivery_display = 'Доставка или самовывоз';
        $delivery_address = 'Выберите способ получения';
    } elseif ($row == 1) {
        $delivery_display = 'Самовывоз';
        $delivery_address = isset($_COOKIE['billing_samoviziv']) ? $_COOKIE['billing_samoviziv'] : '';
    } elseif ($row == 0) {
        $delivery_display = 'Доставка';
        if (isset($_COOKIE['billing_delivery'])) {
            $cookieValue = $_COOKIE['billing_delivery'];
            $cookieArray = explode(',', $cookieValue);
            $delivery_address = implode(',', array_slice($cookieArray, 2));
        }
    }
}

// Dynamic style rules
$has_market = isset($_COOKIE["market"]);
$is_delivery_1 = (is_user_logged_in() && get_user_meta(get_current_user_id(), 'delivery', true) == '1') ||
    (!is_user_logged_in() && isset($_COOKIE['delivery']) && $_COOKIE['delivery'] == '1');
$is_delivery_0 = (is_user_logged_in() && get_user_meta(get_current_user_id(), 'delivery', true) == '0') ||
    (!is_user_logged_in() && isset($_COOKIE['delivery']) && $_COOKIE['delivery'] == '0');
?>

<?php if ($is_delivery_1): ?>
    <style>#billing_delivery_field, #billing_comment_field { display: none; }</style>
<?php elseif ($is_delivery_0): ?>
    <style>#billing_samoviziv_field, h6#primer { display: none; }</style>
<?php endif; ?>

<?php if ($has_market): ?>
    <style>.vibgoroda, .viborgoroda_1, .menumobile, .dblock22 { display: none !important; }</style>
<?php else: ?>
    <?php $vibor = isset($_COOKIE["vibor"]) ? $_COOKIE["vibor"] : (isset($_POST["vib"]) ? $_POST["vib"] : null); ?>
    <?php if ($vibor == 1): ?>
        <style>.vibgoroda { display: none !important; } .viborgoroda_1 { display: block !important; }</style>
    <?php elseif ($vibor == 2): ?>
        <style>.vibgoroda, .viborgoroda_1, .menumobile { display: none !important; }</style>
    <?php endif; ?>
<?php endif; ?>

<!-- Hidden data elements -->
<p style="display:none;"><?php echo isset($args_check) ? esc_html($args_check) : ''; ?></p>
<p id="postsumma" style="display:none"><?php global $woocommerce; echo $woocommerce->cart->total; ?></p>
<p style="display:none" id="carttovar" class="carttovar"><?php
    global $woocommerce;
    $age = 0;
    $array = [];
    foreach ($woocommerce->cart->get_cart() as $item) {
        $array[$age] = $item['product_id'];
        $age++;
    }
    echo json_encode($array);
    ?></p>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PS4BM6F"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

<!-- ===== MOBILE HEADER ===== -->
<p class="new_life dblock d-lg-none" style="display: flex; align-items: center; width: 83%;">
    <a href="/"><img src="https://ferma-dv.ru/wp-content/uploads/2022/07/Logotype-svg.svg" alt="Логотип Ферма ДВ" style="height:40px" width="120" height="40"></a>
    <span style="font-size:11px;margin-left:auto;margin-right:auto;width:14em;color:black;background:white;border:2px solid #036313c9;padding:8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;padding-top:3px;padding-bottom:3px;border-radius:9px;" class="<?php if(wp_is_mobile()) echo 'open-modal1'; ?>">
        <?php echo $delivery_display; ?><br><?php echo $delivery_address; ?>
    </span>
    <a href="https://ferma-dv.ru/my-account/" style="color:#ffffff;"><?php echo is_user_logged_in() ? 'ЛК' : 'ВХОД'; ?></a>
</p>

<!-- ===== DESKTOP HEADER ===== -->
<div class="container-fluid head_fix d-none d-lg-block">
    <div class="container top_head">
        <div class="row jc-center">
            <div class="col-6">
                <a href="https://ferma-dv.ru/about/">О нас</a>
                <a href="https://ferma-dv.ru/category/fermerskij-blog/">Фермерский блог</a>
                <a href="https://ferma-dv.ru/otziv/">Отзывы</a>
                <a href="https://ferma-dv.ru/novosti/">Новости</a>
                <a href="https://ferma-dv.ru/bonusnaya-programma/">Бонусная система</a>
            </div>
            <div style="position:relative;right:0px;max-width:max-content;" class="col-6">
                <a href="https://wa.me/79084411110" target="_blank" style="color:#ffeb3b;">Написать нам</a>
                <a href="https://wa.me/79084411110" target="_blank"><img src="https://ferma-dv.ru/wp-content/themes/theme/img/whatsapp.svg" style="height:26px" alt="WhatsApp" width="26" height="26"></a>
                <a href="https://vk.com/fermadv25/" target="_blank"><img style="height:26px" src="https://ferma-dv.ru/wp-content/themes/theme/img/vk.svg" alt="VK" width="26" height="26"></a>
                <a href="tel:+79084411110"><img style="height:26px" src="https://ferma-dv.ru/wp-content/themes/theme/img/telephone.svg" alt="Телефон" width="26" height="26"> +7 908 441 1110</a>
                <?php if (is_user_logged_in()): ?>
                    <a href="https://ferma-dv.ru/my-account/" style="color:#ffffff;">Личный кабинет</a>
                    <?php
                    // DB updates on POST (kept from original)
                    if (!empty($_POST["asd"])) {
                        $cur_user_id = get_current_user_id();
                        global $wpdb;
                        $wpdb->update('wp_users', ['user_market' => $_POST['asd']], ['ID' => $cur_user_id]);
                        header("Location: https://ferma-dv.ru/");
                        exit;
                    }
                    if (isset($_POST["vib"]) && $_POST["vib"] == 2) {
                        $cur_user_id = get_current_user_id();
                        global $wpdb;
                        $wpdb->update('wp_users', ['user_market' => 'Уссурийск'], ['ID' => $cur_user_id]);
                        header("Location: https://ferma-dv.ru/");
                        exit;
                    }
                    ?>
                <?php else: ?>
                    <a id="vhodacc" href="https://ferma-dv.ru/my-account/" style="color:#ffffff;">Вход/регистрация</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container head_menu">
        <div class="row jc-center">
            <div class="col-3 logo-flex" style="max-width:100%">
                <a href="https://ferma-dv.ru/"><img src="https://ferma-dv.ru/wp-content/uploads/2022/07/Logotype-svg.svg" style="float:left;height:75px;margin-right:15px;" alt="логотип Ферма ДВ" width="75" height="75"></a>
                <div class="flex_block" style="max-width:100%">
                    <div class="col-4" style="max-width:100%;font-family:Jingleberry"><p class="des_rlogo">Ферма ДВ</p></div>
                    <div class="col-4" style="max-width:100%"><h1 style="font-size:14px;">Доставка продуктов</h1></div>
                </div>
            </div>
            <div class="flex_block" style="width:fit-content">
                <div class="col-5 second_menu" style="max-width:100%">
                    <div class="dropdown">
                        <a href="#" data-toggle="dropdown" aria-haspopup="false" aria-expanded="true">Каталог</a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <?php
                            $categories = [
                                'bady' => 'Бады',
                                'bakaleya' => 'Бакалея',
                                'varene' => 'Варенье, домашние соки и компоты',
                                'domashnie-syry' => 'Домашние сыры',
                                'domashnyaya-konservacziya' => 'Домашняя консервация',
                                'kolbasy' => 'Колбасные изделия',
                                'kopchenosti' => 'Мясные деликатесы',
                                'med' => 'Мед',
                                'molochnaya-produkcziya' => 'Молочная продукция',
                                'myaso' => 'Мясо и рыба',
                                'ovoshhi' => 'Овощи, фрукты, ягоды',
                                'podarochnye-nabory' => 'Подарочные наборы',
                                'polufabrikaty-domashnie' => 'Полуфабрикаты домашние',
                                'remeslennyj-hleb-i-vypechka' => 'Ремесленный хлеб и выпечка',
                                'sladosti-i-deserty' => 'Сладости и десерты',
                                'tushenka-i-kashi-sobstvennoe-proizvodstvo' => 'Тушенки и каши собственного производства',
                                'chaj-travy-i-dikorosy' => 'Чай, травы и дикоросы',
                                'yajczo' => 'Яйцо домашнее',
                            ];
                            foreach ($categories as $slug => $name): ?>
                                <a class="dropdown-item" href="https://ferma-dv.ru/product-category/<?php echo $slug; ?>/<?php echo $store_suffix; ?>"><?php echo $name; ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <a href="https://ferma-dv.ru/dostavka/">Доставка и оплата</a>
                    <a href="https://ferma-dv.ru/nashi-magaziny/">Наши магазины</a>
                    <a href="https://ferma-dv.ru/category/akcii/">Акции</a>
                </div>
                <div class="col-4 serach_new" style="max-width:100%">
                    <?php echo do_shortcode('[fibosearch]'); ?>
                </div>
            </div>
            <div class="new_block_vibor col-5" style="width:35%;flex:0 0 35%;justify-content:center;display:flex;align-items:center;">
                <a class="open-modal1 open-modal__st"><?php echo esc_html($delivery_address); ?></a>
            </div>
        </div>
    </div>
</div>

<div class="head-sep"></div>

<?php
// Wide banner logic
$cur_shop = false;
if (isset($_COOKIE['delivery']) && $_COOKIE['delivery'] == 1 && isset($_COOKIE['key_market']) && $_COOKIE['key_market'] != '') {
    $cur_shop = $_COOKIE['key_market'];
} elseif ((!isset($_COOKIE['delivery']) || $_COOKIE['delivery'] == 0) && isset($_COOKIE['coords']) && $_COOKIE['coords'] != '') {
    $shops = ferma_get_shops_by_coords($_COOKIE['coords']);
    $cur_shop = $shops[0];
}

$banner_desktop = '';
$banner_link = '';
if ($cur_shop) {
    while (have_rows('wide_banners', 'option')) {
        the_row();
        $shops = get_sub_field('wide_banners_shop');
        if (in_array($cur_shop, $shops)) {
            $banner_desktop = get_sub_field("wide_banners_image");
            $banner_link = get_sub_field("wide_banners_link");
        }
    }
} else {
    $banner_desktop = get_field("wide_banner_image", "option");
    $banner_link = get_field("wide_banner_link", "option");
}
?>

<?php if ($banner_desktop != '' && !is_account_page()): ?>
    <div class="bwide-desktop">
        <a href="<?php echo $banner_link; ?>">
            <img src="<?php echo $banner_desktop; ?>" alt="Баннер" loading="lazy" />
        </a>
    </div>
<?php endif; ?>

<link href='<?php echo get_template_directory_uri(); ?>/css/slick.css?v=1.9' rel='stylesheet' />

<?php if (is_home() || is_front_page()): ?>
    <?php if (have_rows('mslider')): ?>
        <div class="mslider">
            <?php $first_slide = true; while (have_rows('mslider')): the_row(); ?>
                <div class="mslider__item">
                    <div class="mslider__image-mobile">
                        <img src="<?php echo get_sub_field('mslider_image'); ?>" alt="Слайд" <?php if ($first_slide) echo 'fetchpriority="high" loading="eager"'; else echo 'loading="lazy"'; ?> />
                    </div>
                    <div class="container">
                        <div class="mslider__title" style="color: <?php echo get_sub_field('mslider_color'); ?>"><?php echo get_sub_field('mslider_title'); ?></div>
                        <div class="mslider__descr" style="color: <?php echo get_sub_field('mslider_color'); ?>"><?php echo get_sub_field('mslider_text'); ?></div>
                        <div class="mslider__nav">
                            <a href="<?php echo get_sub_field('mslider_link'); ?>" class="mslider__btn"><?php echo get_sub_field('mslider_button'); ?></a>
                            <div class="mslider__arrows">
                                <a href="javascript:;" class="mslider__arrow mslider__arrow-prev">Назад</a>
                                <a href="javascript:;" class="mslider__arrow mslider__arrow-next">Вперед</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $first_slide = false; endwhile; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Category menu (desktop) -->
<div class="container menu_pic d-none d-lg-block max-row_2">
    <?php if (!is_product_category() && $pagename != 'checkout'): ?>
        <div class="row main_menu">
            <a href="https://ferma-dv.ru/product-category/green-prices/" class="item_m"><img src="https://ferma-dv.ru/wp-content/uploads/2023/08/akcii4.png" alt="Зеленые ценники" width="75" height="67" loading="lazy"><br><span>Зеленые ценники</span></a>
            <?php
            $menu_cats = [
                ['slug' => 'molochnaya-produkcziya', 'name' => 'Молочная продукция', 'img' => '2021/04/3.jpg'],
                ['slug' => 'polufabrikaty-domashnie', 'name' => 'Полуфабрикаты домашние', 'img' => '2021/04/1.jpg'],
                ['slug' => 'kopchenosti', 'name' => 'Мясные деликатесы', 'img' => '2021/04/0.jpg'],
                ['slug' => 'kolbasy', 'name' => 'Колбасные изделия', 'img' => '2022/02/kolbasa.jpg'],
                ['slug' => 'ovoshhi', 'name' => 'Овощи, фрукты, ягоды', 'img' => '2022/02/ovoshi.jpg'],
                ['slug' => 'domashnie-syry', 'name' => 'Домашние сыры', 'img' => '2021/04/6.jpg'],
                ['slug' => 'yajczo', 'name' => 'Яйцо', 'img' => '2022/02/eggs.jpg'],
                ['slug' => 'domashnyaya-konservacziya', 'name' => 'Домашняя консервация', 'img' => '2022/02/conservaziya.jpg'],
                ['slug' => 'myaso', 'name' => 'Мясо и рыба', 'img' => '2023/03/icons-food-1-1.jpg'],
                ['slug' => 'remeslennyj-hleb-i-vypechka', 'name' => 'Ремесленный хлеб', 'img' => '2022/02/hleb.jpg'],
                ['slug' => 'bakaleya', 'name' => 'Бакалея', 'img' => '2021/04/5.jpg'],
                ['slug' => 'varene', 'name' => 'Варенья, соки и компоты', 'img' => '2021/04/7.jpg'],
                ['slug' => 'chaj-travy-i-dikorosy', 'name' => 'Чай и дикоросы', 'img' => '2021/04/8.jpg'],
                ['slug' => 'med', 'name' => 'Мёд', 'img' => '2022/02/med.jpg'],
                ['slug' => 'gotovaya-eda', 'name' => 'Кулинария', 'img' => '2023/03/icons-food-2.jpg'],
            ];
            foreach ($menu_cats as $cat): ?>
                <a href="https://ferma-dv.ru/product-category/<?php echo $cat['slug']; ?>/<?php echo $store_suffix; ?>" class="item_m">
                    <img src="https://ferma-dv.ru/wp-content/uploads/<?php echo $cat['img']; ?>" alt="<?php echo esc_attr($cat['name']); ?>" width="84" height="67" loading="lazy"><br>
                    <?php echo $cat['name']; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($pagename == 'checkout'): ?>
    <h2 style="text-align:center;color:#6ba802;margin-top:80px;font-size:-webkit-xxx-large;"><strong>Оформление заказа</strong></h2>
<?php endif; ?>

<?php if (is_product_category()):
    $curcat_id = get_queried_object()->term_id;
    $current_term = get_queried_object();
    $par_cat = $current_term->parent;
    $subcats_list = get_categories(['taxonomy' => 'product_cat', 'parent' => $curcat_id]);

    if ($par_cat || $subcats_list):
        echo '<div class="container list_subcat_menu" style="margin-top: 80px">';
        if ($par_cat): ?>
            <a href="<?php echo get_term_link($par_cat, 'product_cat'); ?><?php echo $store_suffix; ?>" class="url_upimg"><img src="/wp-content/themes/theme/img/up_img.png" alt="Назад" loading="lazy"></a>
        <?php endif;
        if ($subcats_list):
            foreach ($subcats_list as $cat): ?>
                <a href="<?php echo get_term_link($cat->slug, 'product_cat'); ?><?php echo $store_suffix; ?>"><?php echo $cat->name; ?> (<strong><?php echo $cat->count; ?></strong>)</a>
            <?php endforeach;
        endif;
        echo '</div>';
    endif;
endif; ?>

<div class="container">
    <div class="breadcums" style="color:#999999;margin-bottom:30px;margin-top:20px;">
        <?php if (function_exists('bcn_display')) bcn_display(); ?>
    </div>
</div>

<!-- ===== MODAL (lazy loaded with Yandex Maps) ===== -->
<div class="modal1" id="delivery-modal">
    <div class="modal-content1">
        <span class="close-modal1">&times;</span>
        <h2>Выберите способ получения</h2>
        <div class="tab-container1">
            <button class="tab-button1 active1" data-target="#tab1">Доставка</button>
            <button class="tab-button1" data-target="#tab2">Самовывоз</button>

            <div id="tab1" class="tab-content1 active1">
                <p style="font-weight:bold;margin-bottom:5px;">Укажите адрес и нажмите ниже кнопку ВЫБРАТЬ ДОСТАВКУ, чтобы мы показали доступные товары</p>
                <input type="text" placeholder="Поиск..." id="suggest1">
                <ul style="margin-bottom:0px" id="suggest-list1"></ul>
                <p style="margin-top:5px;margin-bottom:5px;" class="map__note">Вы можете выбрать точку на карте и если она в зоне доставки, то мы сможем привезти заказ</p>
                <div class="VV_RWayChoiceModalDR__Note" id="delivery-message" style="display:none;gap:8px;color:#c31611;">
                    <div class="VV_RWayChoiceModalDR__NoteCol _img">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="10" fill="#D21E19"/><path d="M11.0964 4.755C11.0964 4.155 10.6164 3.66 10.0014 3.66C9.40141 3.66 8.89141 4.155 8.89141 4.755C8.89141 5.37 9.40141 5.865 10.0014 5.865C10.6164 5.865 11.0964 5.37 11.0964 4.755ZM9.17641 15H10.8264V7.5H9.17641V15Z" fill="white"/></svg>
                    </div>
                    <div class="VV_RWayChoiceModalDR__NoteCol _text">
                        <span class="VV_RWayChoiceModalDR__NoteText">К сожалению, доставка не работает в выбранном месте.</span>
                    </div>
                </div>
                <p id="status_delivery" style="display:none"><?php
                    if (is_user_logged_in()) {
                        $coords = get_user_meta(get_current_user_id(), 'coords', true);
                        if (!empty($coords)) echo $coords;
                    } elseif (isset($_COOKIE['coords'])) {
                        echo $_COOKIE['coords'];
                    }
                    ?></p>
                <p id="user_authorize" style="display:none"><?php echo is_user_logged_in() ? 1 : 0; ?></p>
                <div id="map"></div>
                <div id="marker-coordinates"></div>
                <div id="coords" style="display:none"></div>
                <div id="infodev" style="display:none;"></div>
                <div id="time_change">
                    <div class="flex_time" id="flex_time"></div>
                </div>
                <input type="text" id="comment_cur" placeholder="Комментарий курьеру" style="width:100%;margin-top:1em;display:none;border-radius:10px;background:#f8f8fa;height:47px;padding-left:15px;">
                <input type="text" id="comment_cur1" placeholder="Комментарий по заказу" style="width:100%;margin-top:1em;display:none;border-radius:10px;background:#f8f8fa;height:47px;padding-left:15px;">
                <p id="com_cur" style="margin-bottom:5px;margin-top:5px;display:none;">Здесь Вы можете оставить дополнительный комментарий для курьера</p>
                <button type="button" id="but_dev" class="disabled-btn" style="border-radius:20px;width:100%;height:60px;margin-top:0.5rem;font-size:20px;background:#eee;color:#bcbcc3">Выбрать доставку</button>
            </div>

            <div id="tab2" class="tab-content1">
                <p style="font-weight:bold;margin-bottom:-1px;">Выберите магазин, чтобы посмотреть товары в наличии и оформить заказ</p>
                <button type="button" style="background:#eee;margin-top:10px;margin-bottom:15px;" id="list_market">
                    <svg width="15" height="20" viewBox="0 0 12 8" fill="none"><path d="M0.666667 8H11.3333C11.7 8 12 7.7 12 7.33333C12 6.96667 11.7 6.66667 11.3333 6.66667H0.666667C0.3 6.66667 0 6.96667 0 7.33333C0 7.7 0.3 8 0.666667 8ZM0.666667 4.66667H11.3333C11.7 4.66667 12 4.36667 12 4C12 3.63333 11.7 3.33333 11.3333 3.33333H0.666667C0.3 3.33333 0 3.63333 0 4C0 4.36667 0.3 4.66667 0.666667 4.66667ZM0 0.666667C0 1.03333 0.3 1.33333 0.666667 1.33333H11.3333C11.7 1.33333 12 1.03333 12 0.666667C12 0.3 11.7 0 11.3333 0H0.666667C0.3 0 0 0.3 0 0.666667Z" fill="#1A1A1A"/></svg>
                    <span style="color:black;font-weight:700;font-size:14px;vertical-align:super;">Список магазинов</span>
                </button>
                <input style="display:none" type="text" placeholder="Поиск..." id="suggest">
                <ul style="display:none" id="suggest-list"></ul>
                <div id="map2"></div>
                <div style="display:flex;margin-top:0.5rem;">
                    <div class="first_part_time" style="width:27%;">
                        <?php $current_time1 = current_time('H:i'); ?>
                        <select name="data" id="id_select_time_samoviviz" style="width:100%;height:40px;border-radius:47px;padding-left:10px;background:#fbfbfb;">
                            <option value="today">Сегодня</option>
                            <option value="tomorrow" <?php if ($current_time1 > '20:00') echo 'selected'; ?>>Завтра</option>
                        </select>
                    </div>
                    <div class="second_part_time" style="width:73%;">
                        <?php
                        $start_time = strtotime('10:00');
                        $end_time = strtotime('21:00');
                        $interval = 2 * 3600;
                        $current_time = current_time('H:i');
                        ?>
                        <select name="" class="enable_time" id="select_time_1" style="width:100%;height:40px;border-radius:47px;padding-left:10px;background:#fbfbfb;<?php if ($current_time1 > '20:00') echo 'display:none;'; ?>">
                            <?php for ($time = $start_time; $time <= $end_time; $time += $interval):
                                $start = date('H:i', $time);
                                $end_t = date('H:i', min($time + $interval, strtotime('21:00')));
                                if ($end_t > '21:00') $end_t = '21:00';
                                if ($start > $current_time): ?>
                                    <option value="<?php echo $start . '-' . $end_t; ?>"><?php echo $start . '-' . $end_t; ?></option>
                                <?php endif;
                            endfor; ?>
                        </select>
                        <select name="" id="select_time_2" style="width:100%;height:40px;border-radius:47px;padding-left:10px;background:#fbfbfb;<?php echo ($current_time1 > '20:00') ? 'display:block !important;' : 'display:none;'; ?>">
                            <?php for ($time = $start_time; $time <= $end_time; $time += $interval):
                                $start = date('H:i', $time);
                                $end_t = date('H:i', min($time + $interval, strtotime('21:00')));
                                if ($end_t > '21:00') $end_t = '21:00'; ?>
                                <option value="<?php echo $start . '-' . $end_t; ?>"><?php echo $start . '-' . $end_t; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <p style="margin-top:5px;">Выберете удобное для Вас время и мы подготовим заказ</p>
                <div id="selectedMarker"></div>
                <p id="samoviziv" style="display:none"></p>
                <button type="button" id="but_dev2" class="disabled-btn" style="border-radius:20px;width:100%;height:60px;margin-top:0.5rem;font-size:20px;background:#eee;color:#bcbcc3">Выбрать самовывоз</button>
            </div>
        </div>
    </div>
</div>

<!-- Store list modal -->
<div id="modal2" class="modal1">
    <div class="modal-content">
        <span id="close_list_market" class="close">&times;</span>
        <h2>Выбор магазина</h2>
        <h5 style="font-weight:bold;">Владивосток:</h5>
        <?php
        $stores = [
            ['name' => 'Эгершельд, Верхнепортовая,68а', 'data' => '11'],
            ['name' => 'Реми-Сити (ул. Народный пр-т, 20)', 'data' => '1'],
            ['name' => 'Заря (ул. Чкалова, 30)', 'data' => '6'],
            ['name' => 'ТЦ «Море», Гипермаркет (ул. Некрасовская, 49а)', 'data' => '2'],
            ['name' => 'ул. Тимирязева,31 строение 1 (район Спутник)', 'data' => '3'],
        ];
        foreach ($stores as $store): ?>
            <div class="market_el">
                <p><?php echo $store['name']; ?></p>
                <div class="mainblock_time1 enable1" data-market="<?php echo $store['data']; ?>">
                    <div class="underblocktime1"><p class="delivery-text" style="margin-bottom:0px;">Выбрать</p></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<p id="data_of_samoviviz" style="display:none;"><?php echo isset($_COOKIE['data_of_samoviviz']) ? esc_html($_COOKIE['data_of_samoviviz']) : ''; ?></p>

<!-- ===== DEFERRED: Yandex Maps + Modal JS ===== -->
<!-- Yandex Maps loads ONLY when modal opens -->
<script>
    (function(){
        var ymapsLoaded = false;
        var ymapsCallbacks = [];

        function loadYandexMaps(callback) {
            if (ymapsLoaded) { callback(); return; }
            ymapsCallbacks.push(callback);
            if (document.getElementById('ymaps-script')) return;

            var s = document.createElement('script');
            s.id = 'ymaps-script';
            s.src = 'https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=55d7f767-15bc-4e82-863e-31000395a522&suggest_apikey=ee0101d4-9a5a-4ee2-9a97-116d9e6207c9';
            s.onload = function() {
                ymapsLoaded = true;
                ymaps.ready(function() {
                    for (var i = 0; i < ymapsCallbacks.length; i++) ymapsCallbacks[i]();
                    ymapsCallbacks = [];
                });
            };
            document.body.appendChild(s);
        }

        // Tab switching
        document.querySelectorAll('.tab-button1').forEach(function(btn){
            btn.addEventListener('click', function(){
                document.querySelectorAll('.tab-button1').forEach(function(b){ b.classList.remove('active1'); });
                document.querySelectorAll('.tab-content1').forEach(function(c){ c.classList.remove('active1'); });
                btn.classList.add('active1');
                document.querySelector(btn.getAttribute('data-target')).classList.add('active1');
            });
        });

        // Close modals
        document.querySelector('.close-modal1').addEventListener('click', function(){
            document.getElementById('delivery-modal').style.display = 'none';
        });
        document.getElementById('close_list_market').addEventListener('click', function(){
            document.getElementById('modal2').style.display = 'none';
        });
        document.getElementById('list_market').addEventListener('click', function(){
            document.getElementById('modal2').style.display = 'block';
        });

        // Time select toggle
        var selSam = document.getElementById('id_select_time_samoviviz');
        if (selSam) {
            selSam.addEventListener('change', function(){
                var st1 = document.getElementById('select_time_1');
                var st2 = document.getElementById('select_time_2');
                if (this.value === 'tomorrow') {
                    st1.classList.remove('enable_time'); st1.style.display='none';
                    st2.style.display='block'; st2.classList.add('enable_time');
                } else {
                    st1.classList.add('enable_time'); st1.style.display='block';
                    st2.style.display='none'; st2.classList.remove('enable_time');
                }
            });
        }

        var map1Initialized = false;
        var map2Initialized = false;

        function initDeliveryMap() {
            if (map1Initialized) return;
            map1Initialized = true;

            // Show map containers
            document.getElementById('map').style.cssText = 'width:100%;height:250px;display:block;visibility:visible;opacity:1;position:static;left:auto;';

            var deliveryMessage = document.getElementById('delivery-message');
            var suggestView1 = new ymaps.SuggestView('suggest1');
            var myPlacemark, myMap = new ymaps.Map('map', {
                center: [43.1056, 131.874], zoom: 4,
                controls: ['zoomControl', 'searchControl']
            }, { searchControlProvider: 'yandex#search' });

            function get_delivery_prices(lat, lon) {
                if (!myPlacemark) {
                    myPlacemark = new ymaps.Placemark([lat, lon], {}, { preset: 'islands#dotIcon', iconColor: '#0095b6' });
                    myMap.geoObjects.add(myPlacemark);
                } else {
                    myPlacemark.geometry.setCoordinates([lat, lon]);
                }
                myMap.setCenter([lat, lon], 17);
                document.getElementById('status_delivery').innerHTML = lat + ',' + lon;

                ymaps.geocode([lat, lon]).then(function(res) {
                    if (res.geoObjects.get(0).getPremiseNumber() != null) {
                        jQuery.ajax({
                            type: "post", dataType: "json",
                            url: "/wp-admin/admin-ajax.php",
                            data: { action: 'get_delivery_prices', coords: [lat, lon] },
                            beforeSend: function() {
                                document.getElementById('comment_cur').style.display = "none";
                                document.getElementById('comment_cur1').style.display = "none";
                                document.getElementById('com_cur').style.display = "none";
                                document.getElementById('but_dev').classList.remove('enable-dev');
                                document.getElementById('but_dev').classList.add('disabled-btn');
                                document.getElementById('flex_time').classList.remove('active');
                            },
                            success: function(data) {
                                if (!data.success) {
                                    document.querySelector('.VV_RWayChoiceModalDR__NoteText').innerHTML = data.data.error;
                                    deliveryMessage.style.display = 'flex';
                                } else {
                                    var fullAddress = res.geoObjects.get(0).getAddressLine();
                                    document.getElementById('infodev').innerHTML = fullAddress;
                                    document.getElementById('comment_cur').style.display = "block";
                                    document.getElementById('comment_cur1').style.display = "block";
                                    document.getElementById('com_cur').style.display = "block";
                                    document.getElementById('but_dev').classList.remove('disabled-btn');
                                    document.getElementById('but_dev').classList.add('enable-dev');

                                    var times = document.getElementById('flex_time');
                                    var choices = '';

                                    if (typeof data.data.today != "undefined") {
                                        jQuery.each(data.data.today, function(type, price) {
                                            var priceText = price.price == 0 ? 'Бесплатно' : 'от 0 до ' + price.price + '&nbsp;₽';
                                            var checked = data.data.current == 'today_' + type ? ' enable' : '';
                                            choices += '<div class="mainblock_time' + checked + '" data-day="today" data-time="' + type + '"><div class="underblocktime"><p class="delivery-text">Сегодня</p><p>' + price.description + '</p><p class="mainblock_time__price">' + priceText + '</p></div></div>';
                                        });
                                    }
                                    if (typeof data.data.tomorrow != "undefined") {
                                        jQuery.each(data.data.tomorrow, function(type, price) {
                                            var priceText = price.price == 0 ? 'Бесплатно' : 'от 0 до ' + price.price + '&nbsp;₽';
                                            var checked = data.data.current == 'tomorrow_' + type ? ' enable' : '';
                                            choices += '<div class="mainblock_time' + checked + '" data-day="tomorrow" data-time="' + type + '"><div class="underblocktime"><p class="delivery-text">Завтра</p><p>' + price.description + '</p><p class="mainblock_time__price">' + priceText + '</p></div></div>';
                                        });
                                    }

                                    jQuery(document).off('click', '.mainblock_time').on('click', '.mainblock_time', function() {
                                        jQuery('.mainblock_time').removeClass('enable');
                                        jQuery(this).addClass('enable');
                                    });

                                    times.innerHTML = choices;
                                    if (data.data.current == "") jQuery('.mainblock_time').first().addClass('enable');
                                    times.classList.add('active');
                                }
                            }
                        });
                    }
                });
            }

            suggestView1.events.add('select', function(e) {
                var item = e.get('item');
                ymaps.geocode(item.value).then(function(res) {
                    var coords = res.geoObjects.get(0).geometry.getCoordinates();
                    get_delivery_prices(coords[0], coords[1]);
                });
            });

            var searchControl = myMap.controls.get('searchControl');
            searchControl.options.set({ openBalloon: true, noPopup: true });

            myMap.events.add('click', function(e) {
                var coords = e.get('coords');
                ymaps.geocode(coords).then(function(res) {
                    document.getElementById('comment_cur').style.display = "none";
                    document.getElementById('comment_cur1').style.display = "none";
                    document.getElementById('com_cur').style.display = "none";
                    document.getElementById('but_dev').classList.remove('enable-dev');
                    document.getElementById('but_dev').classList.add('disabled-btn');
                    document.getElementById('flex_time').classList.remove('active');
                    get_delivery_prices(coords[0], coords[1]);
                });
            });

            // Auto-load current coords
            var sd = document.getElementById('status_delivery').innerHTML.trim();
            if (sd !== '') {
                var arr = sd.split(',');
                get_delivery_prices(parseFloat(arr[0]), parseFloat(arr[1]));
            } else {
                navigator.geolocation.getCurrentPosition(function(pos) {
                    get_delivery_prices(pos.coords.latitude, pos.coords.longitude);
                });
            }
        }

        function initPickupMap() {
            if (map2Initialized) return;
            map2Initialized = true;

            document.getElementById('map2').style.cssText = 'width:100%;height:250px;display:block;visibility:visible;opacity:1;position:static;left:auto;';

            var map2 = new ymaps.Map('map2', { center: [43.1798, 131.8869], zoom: 10, controls: ['zoomControl'] });

            var placemarks = {
                '11': new ymaps.Placemark([43.09968, 131.863907], { hintContent: 'Эгершельд, Верхнепортовая,68а' }),
                '1': new ymaps.Placemark([43.128381, 131.919746], { hintContent: 'Реми-Сити (ул. Народный пр-т, 20)' }),
                '2': new ymaps.Placemark([43.127427, 131.909317], { hintContent: 'ТЦ «Море», Гипермаркет (ул. Некрасовская, 49а)' }),
                '3': new ymaps.Placemark([43.24827778336888, 132.02109573106299], { hintContent: 'ул. Тимирязева,31 строение 1 (район Спутник)' }),
                '6': new ymaps.Placemark([43.181235883133674, 131.9154298472213], { hintContent: 'Заря (Чкалова, 30)' })
            };

            var myGroup = new ymaps.GeoObjectCollection({}, { draggable: false, preset: 'islands#blueIcon', iconColor: '#3caa3c' });
            for (var k in placemarks) myGroup.add(placemarks[k]);
            map2.geoObjects.add(myGroup);

            myGroup.events.add('click', function(e) {
                var target = e.get('target');
                ymaps.geocode(target.geometry.getCoordinates()).then(function(res) {
                    var fullAddress = res.geoObjects.get(0).getAddressLine();
                    var addrEl = document.getElementById('suggest');
                    if (addrEl) addrEl.value = fullAddress;
                    myGroup.each(function(el) { el.options.set('iconColor', '#3caa3c'); });
                    target.options.set('iconColor', '#ff0000');
                    document.getElementById('samoviziv').innerHTML = target.properties.get('hintContent');
                    document.getElementById('but_dev2').classList.remove('disabled-btn');
                    document.getElementById('but_dev2').classList.add('enable-dev');
                });
            });

            // Store list buttons
            document.querySelectorAll('.mainblock_time1[data-market]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var dm = this.getAttribute('data-market');
                    myGroup.each(function(el) { el.options.set('iconColor', '#3caa3c'); });
                    if (placemarks[dm]) {
                        placemarks[dm].options.set('iconColor', '#ff0000');
                        document.getElementById('samoviziv').innerHTML = placemarks[dm].properties.get('hintContent');
                        ymaps.geocode(placemarks[dm].geometry.getCoordinates()).then(function(res) {
                            document.getElementById('suggest').value = res.geoObjects.get(0).getAddressLine();
                        });
                        map2.setCenter(placemarks[dm].geometry.getCoordinates(), 15);
                    }
                    document.getElementById('but_dev2').classList.remove('disabled-btn');
                    document.getElementById('but_dev2').classList.add('enable-dev');
                    document.getElementById('modal2').style.display = 'none';
                });
            });
        }

        // Open modal -> lazy load maps
        function openDeliveryModal() {
            document.getElementById('delivery-modal').style.display = 'block';
            loadYandexMaps(function() {
                initDeliveryMap();
                initPickupMap();
            });
        }

        document.querySelectorAll('.open-modal1').forEach(function(el) {
            el.addEventListener('click', openDeliveryModal);
        });

        // Delivery button
        document.getElementById('but_dev').addEventListener('click', function() {
            var enableEl = document.querySelector('.mainblock_time.enable');
            if (!enableEl) return;
            var delivery_day = enableEl.getAttribute('data-day');
            var delivery_time = enableEl.getAttribute('data-time');
            var address1 = {
                billing_delivery: document.getElementById('infodev').innerHTML,
                billing_comment: document.getElementById('comment_cur').value,
                billing_comment_zakaz: document.getElementById('comment_cur1').value,
                time_type: enableEl.getAttribute('data-count'),
                coords: document.getElementById('status_delivery').innerHTML,
                time: enableEl.getAttribute('data-time'),
                delivery_day: delivery_day,
                delivery_time: delivery_time
            };
            jQuery.ajax({
                type: 'POST', url: '/wp-admin/admin-ajax.php',
                data: { action: 'update_user_address', address: address1 },
                success: function() { window.location.reload(); }
            });
        });

        // Pickup button
        document.getElementById('but_dev2').addEventListener('click', function() {
            var selEl = document.getElementById('id_select_time_samoviviz');
            var selectedOption = selEl.options[selEl.selectedIndex].innerHTML;
            var enableTime = document.querySelector('.enable_time');
            var selectedValue = enableTime ? enableTime.value : '';
            jQuery.ajax({
                type: 'POST', url: '/wp-admin/admin-ajax.php',
                data: {
                    action: 'update_user_address1',
                    address: {
                        billing_samoviziv: document.getElementById('samoviziv').innerHTML,
                        data_of: selectedOption + ', ' + selectedValue
                    }
                },
                success: function() { window.location.reload(); }
            });
        });

        // Checkout address link
        var billingInput = document.getElementById('billing_delivery');
        if (billingInput) {
            billingInput.readOnly = true;
            var link = document.createElement('a');
            link.href = 'javascript:;';
            link.innerHTML = 'Изменить адрес';
            link.addEventListener('click', openDeliveryModal);
            document.getElementById('billing_delivery_field').append(link);
        }
    })();
</script>