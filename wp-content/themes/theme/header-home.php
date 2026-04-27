<!DOCTYPE html>
<html lang="ru">
<!-- ferma-debug-header: header-home.php -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="yandex-verification" content="02de7abccf41bbae" />
    <meta name="yandex-verification" content="50671f6ce40cf19f" />
    <meta name="mailru-domain" content="Rsg5YmdfoMnfaRN0" />

    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- old -->
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=55d7f767-15bc-4e82-863e-31000395a522&suggest_apikey=ee0101d4-9a5a-4ee2-9a97-116d9e6207c9" type="text/javascript"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;700&display=swap" rel="stylesheet">

    <?php wp_head(); ?>
    <script src="/wp-content/themes/theme/js/cookie.js"></script>
    <!-- old -->


    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-PS4BM6F');</script>
    <!-- End Google Tag Manager -->
    <title>Ферма DВ</title>
</head>

<?php
$ferma_forced_body_classes = array();
if ( function_exists( 'is_product_category' ) && is_product_category() ) {
    $ferma_forced_body_classes[] = 'archive';
    $ferma_forced_body_classes[] = 'tax-product_cat';
    $ferma_forced_body_classes[] = 'woocommerce-archive';
}
if ( function_exists( 'is_post_type_archive' ) && is_post_type_archive( 'product' ) ) {
    $ferma_forced_body_classes[] = 'archive';
    $ferma_forced_body_classes[] = 'post-type-archive';
    $ferma_forced_body_classes[] = 'post-type-archive-product';
    $ferma_forced_body_classes[] = 'woocommerce-archive';
}
?>
<body <?php body_class( $ferma_forced_body_classes ); ?>>
<!-- Старый код -->
<?if ( is_user_logged_in() && get_user_meta( get_current_user_id(), 'delivery', true ) == '1' ) {?>
    <style>
        #billing_delivery_field {
            display: none;
        }
        #billing_comment_field {
            display: none;
        }
    </style>
<?} if ( is_user_logged_in() && get_user_meta( get_current_user_id(), 'delivery', true ) == '0') {?>
    <style>
        #billing_samoviziv_field, h6#primer {
            display: none;
        }
    </style>
<?}?>
<?if ( !is_user_logged_in() && $_COOKIE['delivery'] == '1' ) {?>
    <style>
        #billing_delivery_field {
            display: none;
        }
        #billing_comment_field {
            display: none;
        }
    </style>
<?} if ( !is_user_logged_in() && $_COOKIE['delivery'] == '0' ) {?>
    <style>
        #billing_samoviziv_field, h6#primer {
            display: none;
        }

    </style>
<?}?>
<style>
    #map, #map2 {
        height: 280px;
        width: 100%;
    }

    .mapboxgl-popup {
        max-width: 200px;
    }

    .selected {
        border: 2px solid blue;
    }

    .mapboxgl-marker {
        cursor: pointer;
    }

    .red-marker {
        background-image: url('https://cdn.mapmarker.io/api/v1/pin?size=50&background=%2300ff00&text=%20');
        background-size: contain;
        margin-top: -5px !important;
    }

    .blue-marker {
        background-image: url('https://cdn.mapmarker.io/api/v1/pin?size=50&background=%2300ff00&text=%20');
        margin-top: -5px !important;
        background-size: contain;
    }
</style>
<style>
    .xoo-wsc-basket {
        display: none !important;
    }
    .but {
        font-size: 100%;
        margin: 0;
        line-height: 1;
        cursor: pointer;
        position: relative;
        text-decoration: none;
        overflow: visible;
        padding: 0.618em 1em;
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
</style>
<style>
    .xoo-wsc-footer {
        z-index: 111111111111111;
    }
</style>
<style>
    /* Swiper navigation fallback: keep arrows vertically centered */
    .home-slider__inner .homeSwiper-next,
    .home-slider__inner .homeSwiper-prev,
    .selection__inner .selectionSwiper-next,
    .selection__inner .selectionSwiper-prev {
        position: absolute;
        top: 50% !important;
        bottom: auto !important;
        transform: translateY(-50%) !important;
        margin-top: 0 !important;
        z-index: 3 !important;
    }

    /* Prevent generic Swiper height stretching across content sections (except header mobile banner). */
    .swiper:not(.bannerSwiper) .swiper-slide {
        height: auto !important;
    }

    .swiper:not(.bannerSwiper) .swiper-wrapper {
        height: auto !important;
        align-items: stretch;
    }

    /* Guard against legacy global snippets from Simple CSS&JS on inner pages. */
    body:not(.home):not(.single-product) #photo_pc_1,
    body:not(.home):not(.single-product) #photo_pc_2,
    body:not(.home):not(.single-product) #photo_0,
    body:not(.home):not(.single-product) #photo_1,
    body:not(.home):not(.single-product) #photo_2,
    body:not(.home):not(.single-product) .mslider,
    body:not(.home):not(.single-product) .dslider {
        display: none !important;
    }

    body:not(.home):not(.single-product) .class {
        display: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Replacement for safe parts of disabled custom snippet 4438.css */
    body.woocommerce ul.products li.product .woocommerce-loop-category__title,
    body.woocommerce ul.products li.product .woocommerce-loop-product__title,
    body.woocommerce ul.products li.product h3 {
        padding: 0.5em 0;
        margin: 0;
        font-size: 1em;
        color: #7e3b2a;
        font-weight: 700;
        height: 95px;
    }

    body.woocommerce span.woocommerce-Price-amount.amount {
        font-weight: 700;
        font-size: 18px;
        color: #7e3b2a;
    }

    body.woocommerce .quantity .qty {
        height: 35px;
    }

    body.woocommerce li.product .qib-container {
        width: 100%;
        align-items: center;
        display: flex !important;
        justify-content: center;
        text-align: center;
    }

    @media (max-width: 500px) {
        #choose {
            margin-bottom: 2em !important;
        }
    }

    .mapboxgl-ctrl-geocoder--button {
        background: #e87e7e !important;
    }

    /* Keep header layout controlled by main stylesheets (style.min.css / style.css),
       avoid page-wide inline overrides that break single blog/recipe/promo pages. */

</style>
<p style="display:none;">
    <?
    $user_id = get_current_user_id();
    $args_check =  $_COOKIE['market'];?>

</p>
<?
$url = $_SERVER['REQUEST_URI'];
if($url == "/my-account/user-market/") {
    header('Location: https://ferma-dv.ru/user-market/');
} else {
}

?>
<?
if (isset($_COOKIE["market"])) { ?>
    <style>
        .vibgoroda {display: none !important;}
        .viborgoroda_1 {display: none !important;}
        .menumobile {display: none !important;}
    </style>
<?}?>
<p id="postsumma" style="display:none">
    <?
    global $woocommerce;
    echo $woocommerce->cart->total;
    ?>
</p>
<p style="display:none" id="carttovar" class="carttovar"><?
    global $woocommerce;
    $age = 0;
    foreach ($woocommerce->cart->get_cart() as $item):
        $array[$age] = $item['product_id'];
        $age++;
    endforeach;
    echo json_encode($array);
    ?></p>
<?
if ($_COOKIE["vibor"] == 1 or $_POST["vib"] == 1 )  { ?>
    <style>
        .vibgoroda {display: none !important;}
        .viborgoroda_1 {display: block !important;}

    </style>
<?}?>
<?
if ($_COOKIE["vibor"] == 2 or $_POST["vib"] == 2 or isset($_COOKIE["market"])) { ?>
    <style>
        .vibgoroda {display: none !important;}
        .viborgoroda_1 {display: none !important;}
        .menumobile {display: none !important;}
    </style>
<?}?>
<?
if (!empty($_POST["asd"])) {
    SetCookie("market", $_POST["asd"], time()+60*60*24*1, '/');
    $_COOKIE["market"] = $_POST["asd"];
} else {
}
?>
<?
if (!empty($_POST["asd1"])) {
    SetCookie("market", $_POST["asd1"], time()+60*60*24*1, '/');
    $_COOKIE["market"] = $_POST["asd1"];
} else {
}
?>
<?
if (!empty($_POST["vib"])) {
    SetCookie("vibor", $_POST["vib"], time()+60*60*24*1, '/');
    $_COOKIE["vibor"] = $_POST["vib"];
} else {
}
?>
<?if (isset($_COOKIE["market"])) {?>
    <style>
        .dblock22 {
            display: none !important;
        }
    </style>
<?} ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PS4BM6F"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<style>
    @media (max-width: 500px) {
        .xoo-wsc-basket {
            right: 16em !important;
        }
        #telegrambott {
            top: 88% !important;
        }
    }
</style>
<style>
    .mobmenu-content li a {
        display: block;
        letter-spacing: 1px;
        padding: 5px 20px;
        text-decoration: none;
        font-size: 14px;
        text-decoration: underline;
    }

    .wpcf7-form input {
        margin-bottom: 10px !important;
        padding-left: 15px;
        width: 100%;
    }

    input.wpcf7-form-control.wpcf7-submit {
        cursor: pointer;
    }
    .row.jc-center{
        justify-content: center;
    }
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

    .head_fix {
        position: fixed;
        top: 0;
        background: white;
        z-index: 98;
        box-shadow: 0px 0px 8px 2px rgba(0, 0, 0, 0.1);
        -webkit-box-shadow: 0px 0px 8px 2px rgba(0, 0, 0, 0.1);
        -moz-box-shadow: 0px 0px 8px 2px rgba(0, 0, 0, 0.1);
    }

    .head_menu {
        padding-top: 16px;
        padding-bottom: 16px;
    }
    .head_menu .second_menu {
        padding-top: 0px;
    }
    .des_rlogo {
        padding-top: 25px;
        font-size: 20px;
        line-height: 0.5;
        margin-bottom: 0px;
        font-weight: 700;
        font-size: xx-large
    }
    .slider_home {
        padding: 0px;
    }
    .slider_home.mrg-0 {
        margin: 0!important;
    }
    .yit_footer {
        font-weight: bold;
        display: block;
        margin-bottom: 20px;
        color: #ffeb3b;
    }

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

    .menu_pic {
        padding-top: 130px;
    }
    .container.menu_pic.d-none.d-lg-block {
        padding-top: 20px !important;
    }
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

    a.item_m.green-friday span {
        background: #000;
    }

    a.item_m:hover {
        box-shadow: 0px 0px 8px 2px rgba(0, 0, 0, 0.1);
        -webkit-box-shadow: 0px 0px 8px 2px rgba(0, 0, 0, 0.1);
        -moz-box-shadow: 0px 0px 8px 2px rgba(0, 0, 0, 0.1);
        text-decoration: none;
        color: black;
    }

    .footer_ferma a:hover {
        text-decoration: none;
        color: rgb(226, 223, 25);
    }

    .adress_right {
        font-size: 14px;
        padding-top: 5px;
    }

    input.search-field {
        width: 285px !important;
        padding-left: 10px;
    }

    form.search-form {
        margin-top: 15px;
    }

    .son_m {
        padding-top: 5px;
    }

    .second_menu {
        padding: 0px;
        padding-top: 20px;
    }

    .second_menu a {
        display: inline;
        margin-right: 10px;
        color: grey;
        font-weight: 700;
        transition: 0.3s;
    }

    .second_menu a:hover {
        color: red;
        text-decoration: none;
    }

    .main_menu {
        border-bottom: 1px solid gainsboro;
        padding-bottom: 10px;
        margin-top: 1em;
        padding-top: 10px;
        justify-content: center;
    }
    .row.main_menu a.item_m img {
        height: 64px;
    }
    .info_page {
        margin-top: 40px;
    }

    .footer_info {
        height: 338px;

    }

    .breadcums a {
        color: #6ba802;
    }

    .dropdown,
    .dropup {
        position: relative;
        width: 50px !important;
        display: inline;
    }

    .prod_tit {
        color: black;
        font-weight: 700;
        font-size: 24px;
        margin-top: -20px;
    }
    .products.pb-0 {
        padding-bottom: 0!important;
    }
    .woocommerce-tabs.wc-tabs-wrapper {
        display: none;
    }

    .woocommerce #respond input#submit.alt,
    .woocommerce a.button.alt,
    .woocommerce button.button.alt,
    .woocommerce input.button.alt {
        background-color: #6ba802;
        color: #fff;
        -webkit-font-smoothing: antialiased;
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

    span.woocommerce-Price-amount.amount {
        font-weight: 700;
        font-size: 18px;
        color: black;
    }

    mark.count {
        display: none;
    }

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
        transition: 0.3s;
        box-shadow: 0px 0px 17px 9px rgba(0, 0, 0, 0.07);
        -webkit-box-shadow: 0px 0px 17px 9px rgba(0, 0, 0, 0.07);
        -moz-box-shadow: 0px 0px 17px 9px rgba(0, 0, 0, 0.07);
    }

    .xoo-wsc-basket {

    }

    a.added_to_cart.wc-forward {
        display: none;
    }

    h2.woocommerce-loop-product__title {
        color: red !important;
    }

    select.orderby {
        padding: 5px;
        font-weight: bold;
    }

    p.woocommerce-result-count {
        display: none;
    }

    .dropdown:hover>.dropdown-menu {
        display: block;
    }

    .dropdown>.dropdown-toggle:active {
        pointer-events: none;
    }

    li.product {
        transition: 0.3s;
        text-align: center;
        padding-bottom: 20px !important;
        background: white;
        border-radius: 10px;
    }

    li.product:hover {
        -webkit-box-shadow: 0px 0px 21px 0px rgba(34, 60, 80, 0.25);
        -moz-box-shadow: 0px 0px 21px 0px rgba(34, 60, 80, 0.25);
        box-shadow: 0px 0px 21px 0px rgba(34, 60, 80, 0.25);
    }

    .xoo-wsc-pname a {
        color: red;
    }

    .xoo-wsc-ft-buttons-cont a {
        color: black;
    }

    span.xoo-wsc-ft-amt-label {
        display: none;
    }

    .xoo-wsc-ft-amt-value::before {
        content: "Итого: ";
    }

    .new_life {
        position: fixed;
        z-index: 99999;
        top: 5px;
        left: 50px;
        color: white;
        font-size: 14px;
        line-height: 1.3;
    }

    .coupon {
        display: none;
    }

    a.xoo-wsc-ft-btn.button.btn.xoo-wsc-cart-close.xoo-wsc-ft-btn-continue {
        display: none;
    }

    a.xoo-wsc-ft-btn.button.btn.xoo-wsc-ft-btn-cart {
        display: none;
    }

    a.xoo-wsc-ft-btn.button.btn.xoo-wsc-ft-btn-checkout {
        background: #4caf50;
        color: white;
    }
    .serach_new {
        padding-top: 10px;
    }
    @media(max-width: 1200px){
        .container.max-row_2{
            max-width: 1148px;
        }
        .container.max-row_2 .item_m{
            width: 120px;
        }
    }
    @media(max-width: 1070px){
        .head_menu {
            max-width: 1060px;
            padding-top: 18px;
            padding-bottom: 14px;
        }
        .head_menu img[src="https://ferma-dv.ru/wp-content/uploads/2022/07/Logotype-svg.svg"]{
            height: 48px!important;
        }
        .head_menu .second_menu {
            padding-top: 0;
        }
        .head_menu .dgwt-wcas-no-submit .dgwt-wcas-sf-wrapp input[type=search].dgwt-wcas-search-input {
            padding: 5px 15px 5px 40px;
        }
        .head_menu .dgwt-wcas-sf-wrapp input[type=search].dgwt-wcas-search-input {
            height: 30px;
        }
        .top_head .row.jc-center .col-6 img {
            height: 20px!important;
        }
        .des_rlogo{
            padding-top: 0;
            font-size: 18px;
        }
        .row.jc-center {
            justify-content: space-between;
            align-items: flex-start;
        }
        .container.menu_pic.d-none.d-lg-block {
            padding-top: 8em !important;
        }
        .top_head {
            padding: 10px;
        }
    }
    @media(max-width: 1025px){
        .container.menu_pic.d-none.d-lg-block {
            padding-top: 5em !important;
        }
    }
    @media(max-width:1000px) {
        .product-category img {
            border-radius: 10px 10px 0px 0px;
            height: 150px !important;
            object-fit: cover;
            width: 100%;
        }

        .mob-menu-header-holder {
            display: none !important;
        }

        .xoo-wsc-basket {
            top: 2vw;
            right: -5px;
        }
    }

    .mobile-search {
        margin-top: 20px;
    }

    @media(min-width: 992px) {
        .mobile-search {
            display: none;
        }
        .logo-flex{
            display: flex;
        }

    }
    @media(max-width: 2560px) {
        .slider_home {
            margin-bottom: -150px;
            margin-top: 30px;
        }
    }

    @media(max-width: 1440px) {
        .slider_home {
            margin-bottom: -150px;
            margin-top: -70px;
        }
    }

    @media(max-width: 992px) {
        .slider_home.d-none {
            display: block!important;
        }
        .slider_home {
            margin-bottom: -50px;
            margin-top: -50px;
        }
    }

    @media(max-width: 768px) {
        .slider_home {
            margin-bottom: -100px;
            margin-top: -50px;
        }
    }

    @media(max-width: 425px) {
        .slider_home {
            margin-bottom: -70px;
            margin-top: -30px;
        }
    }

    .related .qib-container {
        display: none !important;
    }

    .discount-offset {
        padding-top: 32px;
    }
    .new-price {
        margin-top: 6px;
    }
    @media(max-width: 768px) {
        .discount-offset {
            padding-top: 34px;
        }
        .discount-offset span {
            display: block;
        }

        .menu-item-9430 a {
            display: inline-block ;
            background: #6ba802;
            border-radius: 5px;
            padding: 3px;
            color: #fff !important;
        }
        .woocommerce ul.products li.product .woocommerce-loop-product__title {
            min-height: 72px;
        }
    }
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
</style>
<style>
    @media (max-width:500px) {
        .menumobile {
            display: block;
        }
    }
</style>

<!-- Старый код 2-->

<!-- Старый код 3 -->
<a class="test" style="display: none;">
    <? if ( is_user_logged_in() ) {
        $user_id = get_current_user_id();
        $row = get_user_meta( $user_id, 'delivery', true );
        if($row == '') {
            unset($row);
        }
        if (isset($row)) {
            if ($row == 1) {
                echo 'Самовывоз';
                $resultArray = get_user_meta( $user_id, 'billing_samoviziv', true );

            }
            if ($row == 0) {
                if(isset($_COOKIE['delivery_time']) && isset($_COOKIE['delivery_day'])) {
                    if($_COOKIE['delivery_day'] == "today") {
                        echo '';
                    } else if($_COOKIE['delivery_day'] == "tomorrow") {
                        echo '';
                    }
                } else {
                    echo '';
                }
                $cookieValue = get_user_meta( $user_id, 'billing_delivery', true );
                $cookieArray = explode(',', $cookieValue);
                $resultArray = array_slice($cookieArray, 2);
                $resultArray = implode(',', $resultArray);
            }
        } else {
            echo 'Доставка или самовывоз';
            $resultArray = 'Выберите способ получения';
        }
    } else {
        $row = $_COOKIE['delivery'];
        if (isset($row)) {
            if ($row == 1) {
                echo 'Самовывоз';
                $resultArray = $_COOKIE['billing_samoviziv'];
            }
            if ($row == 0) {
                if(isset($_COOKIE['delivery_time']) && isset($_COOKIE['delivery_day'])) {
                    if($_COOKIE['delivery_day'] == "today") {
                        echo '';
                    } else if($_COOKIE['delivery_day'] == "tomorrow") {
                        echo '';
                    }
                } else {
                    echo '';
                }
                $cookieValue = $_COOKIE['billing_delivery'];
                $cookieArray = explode(',', $cookieValue);
                $resultArray = array_slice($cookieArray, 2);
                $resultArray = implode(',', $resultArray);
            }
        } else {
            echo 'Доставка или самовывоз';
            $resultArray = 'Выберите способ получения';

        }
    }
    ?><br>
    <span style=""><?echo $resultArray;?></span>
</a>


<!-- Модальное окно -->
<div class="modal1">
    <div class="modal-content1 modal-delivery">
        <span class="close-modal1">&times;</span>
        <h2 class="modal-delivery__title">Выберите способ получения</h2>

        <!-- Табы -->
        <div class="tab-container1">
            <div class="modal-delivery__buttons">
                <button class="modal-delivery__btn tab-button1 active1" data-target="#tab1">Доставка</button>
                <button class="modal-delivery__btn tab-button1" data-target="#tab2">Самовывоз</button>
            </div>

            <!-- Содержимое табов -->
            <div id="tab1" class="modal-delivery__tab tab-content1 active1">
                <!-- <p>Укажите адрес и нажмите ниже кнопку ВЫБРАТЬ ДОСТАВКУ, чтобы мы показали доступные товары</p> -->
                <input class="modal-delivery__tab-inp" type="text" placeholder="Поиск" id="suggest1">
                <ul style="margin-bottom:0px" id="suggest-list1"></ul>
                <div class="VV_RWayChoiceModalDR__Note" id="delivery-message" style="    display: none;
            gap: 8px;
            color: #c31611;
            margin-bottom:0.rem">
                    <div class="VV_RWayChoiceModalDR__NoteCol _img">
                        <svg class="VV_RWayChoiceModalDR__NoteImg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="10" fill="#D21E19"></circle>
                            <path d="M11.0964 4.755C11.0964 4.155 10.6164 3.66 10.0014 3.66C9.40141 3.66 8.89141 4.155 8.89141 4.755C8.89141 5.37 9.40141 5.865 10.0014 5.865C10.6164 5.865 11.0964 5.37 11.0964 4.755ZM9.17641 15H10.8264V7.5H9.17641V15Z" fill="white"></path>
                        </svg>
                    </div>
                    <div class="VV_RWayChoiceModalDR__NoteCol _text">
                <span class="VV_RWayChoiceModalDR__NoteText rtext _desktop-sm _tablet-sm _mobile-sm">
                    К сожалению, доставка не работает в выбранном месте.
                </span>
                    </div>
                </div>
                <p style="display:none"><?if ( is_user_logged_in() ) {
                        $user_id = get_current_user_id();
                        $address2 = get_user_meta( $user_id, 'billing_delivery', true );
                        "Текущий выбранный адрес: " . $address2;
                    } else {
                        if (isset($_COOKIE['billing_delivery'])) {
                            $user_address_2 = $_COOKIE['billing_delivery'];
                            echo "Текущий выбранный адрес: " . $user_address_2;
                        }
                    }?></p>
                <div id="map"></div>
                <style>
                    .disabled-btn {
                        pointer-events: none;
                        cursor: default;
                    }
                    .enable-dev {
                        background: #4FBD01 !important;
                        color: #FFFFFF !important;
                    }
                    .mainblock_time ,  .mainblock_time1{
                        display: flex;
                        cursor: pointer;
                        width: fit-content;
                        align-items: center;
                        justify-content: center;
                    }
                    .mainblock_time_express {
                        grid-column: 1 / -1;
                        max-width: 100%;
                    }
                    .underblocktime {
                        height: fit-content;
                        display: block;
                        align-items: center;
                        font-size: 15px;
                        justify-content: center;
                    }
                    .underblocktime > p {
                        margin: 0;
                        text-align: center;
                    }
                </style>
                <p id="status_delivery" style="display:none"><?// проверяем, авторизован ли пользователь
                    if ( is_user_logged_in() ) {
                        // получаем ID текущего пользователя
                        $user_id = get_current_user_id();
                        // получаем значение мета-данных пользователя
                        $coords_in_map_delivery = get_user_meta( $user_id, 'coords', true );

                        // проверяем, что значение мета-данных не пустое
                        if ( ! empty( $coords_in_map_delivery ) ) {
                            echo $coords_in_map_delivery;
                        }
                    } else {
                        if ( isset( $_COOKIE['coords']) ) {
                            echo $_COOKIE['coords'];
                        }
                    }
                    ?></p>
                <style>
                    .notenable {
                        display: none;
                    }
                </style>
                <p id="user_authorize" style="display:none"><?if ( is_user_logged_in() ) {echo 1;} else {echo 0;}?></p>
                <div id="marker-coordinates"></div>
                <div id="coords" style="display:none"></div>
                <div id="infodev" style="display:none;"></div>

                <input type="text" id="comment_cur" placeholder="Комментарий курьеру" style="width: 100%;
        margin-top: 1em;
        display:none;
        border-radius: 10px;
        background: #f8f8fa;
        height: 47px;
        padding-left: 15px;">
                <input type="text" id="comment_cur1" placeholder="Комментарий по заказу" style="width: 100%;
        margin-top: 1em;
        display:none;
        border-radius: 10px;
        background: #f8f8fa;
        height: 47px;
        padding-left: 15px;">

                <p id="delivery-selected-text" class="delivery-selected-text" style="display:none; margin-top: 1rem; margin-bottom: 0.5rem;">
                    <!-- сюда будем подставлять текст -->
                </p>
                <button type="button" id="but_dev" class="modal-delivery__choice disabled-btn">Выбрать доставку</button>
                <style>
                    .ymaps-2-1-79-gototech, .ymaps-2-1-79-map-copyrights-promo{
                        display: none;
                    }
                </style>
                <script>
                    ymaps.ready(init);

                    function init() {
                        const deliveryMessage = document.getElementById('delivery-message');
                        var suggestView1 = new ymaps.SuggestView('suggest1');
                        let myPlacemark,
                            myMap = new ymaps.Map('map', {
                                center: [43.1056, 131.874],
                                zoom: 4,
                                controls: ['zoomControl', 'searchControl']

                            }, {
                                searchControlProvider: 'yandex#search'
                            });

                        function get_delivery_prices(lat, lon)
                        {
                            console.log('get_delivery_prices');
                            if (!myPlacemark) {
                                myPlacemark = new ymaps.Placemark([lat, lon], {}, {
                                    preset: 'islands#dotIcon',
                                    iconColor: '#0095b6'
                                });
                                myMap.geoObjects.add(myPlacemark);
                            } else {
                                myPlacemark.geometry.setCoordinates([lat, lon]);
                            }

                            myMap.setCenter([lat, lon], 17);

                            if(document.getElementById('status_delivery')) {
                                document.getElementById('status_delivery').innerHTML = lat + ',' + lon;
                            }

                            // Получаем адрес метки
                            ymaps.geocode([lat, lon]).then(function (res) {
                                console.log(res);
                                let firstGeoObject = res.geoObjects.get(0);
                                let firstGeoObject1 = res.geoObjects.get(0).getAddressLine();
                                if(res.geoObjects.get(0).getPremiseNumber() != null) {
                                    jQuery.ajax({
                                        type: "post",
                                        dataType: "json",
                                        url: "/wp-admin/admin-ajax.php",
                                        data: {
                                            action:'get_delivery_prices',
                                            coords: [lat, lon]
                                        },
                                        beforeSend: function() {
                                            const button = document.getElementById('but_dev');
                                            button.classList.remove('enable-dev');
                                            button.classList.add('disabled-btn');
                                            document.getElementById('flex_time').classList.remove('active');
                                        },
                                        success: function(data) {
                                            if(!data.success) {
                                                document.querySelector('.VV_RWayChoiceModalDR__NoteText').innerHTML = data.data.error;
                                                deliveryMessage.style.display = 'flex';
                                            } else {
                                                const infodev = document.getElementById('infodev');
                                                const fullAddress = firstGeoObject.getAddressLine();

                                                let parts = fullAddress.split(',').map(function (p) {
                                                    return p.trim();
                                                });

                                                if (parts.length > 2) {
                                                    parts = parts.slice(2);
                                                }

                                                const shortAddress = parts.join(', ');

                                                infodev.textContent = shortAddress;

                                                const infoTextEl = document.getElementById('delivery-selected-text');
                                                if (infoTextEl) {
                                                    infoTextEl.textContent = 'Вы выбрали доставку на адрес: ' + shortAddress;
                                                    infoTextEl.style.display = 'block';
                                                }
                                                const button = document.getElementById('but_dev');
                                                button.classList.remove('disabled-btn');
                                                button.classList.add('enable-dev');
                                                const times = document.getElementById('flex_time');

                                                let choices = '';

                                                <?php
                                                date_default_timezone_set("Asia/Vladivostok");
                                                $coords = ($_COOKIE['coords']) ? $_COOKIE['coords'] : '43.111787507251414,131.88327396290603';
                                                ?>

                                                if(typeof data.data.today != "undefined") {
                                                    $.each(data.data.today, function(type, price) {
                                                        if(price.price == 0) {
                                                            price.price = 'Бесплатно';
                                                        } else {
                                                            price.price = 'от 0 до ' + price.price + '&nbsp;₽';
                                                        }
                                                        let checked = '';
                                                        if(data.data.current == 'today_' + type) {
                                                            checked = ' enable';
                                                        }
                                                        choices = choices +
                                                            '<div class="mainblock_time' + checked + '" style="" data-day="today" data-time="' + type + '">' +
                                                            '<div class="underblocktime" style="">' +
                                                            '<p class="delivery-text">Сегодня</p>' +
                                                            '<p>' + price.description + '</p>' +
                                                            '<p class="mainblock_time__price">' + price.price + '</p>' +
                                                            '</div>' +
                                                            '</div>';
                                                    });
                                                }

                                                if(typeof data.data.tomorrow != "undefined") {
                                                    $.each(data.data.tomorrow, function(type,price) {
                                                        if(price.price == 0) {
                                                            price.price = 'Бесплатно';
                                                        } else {
                                                            price.price = 'от 0 до ' + price.price + '&nbsp;₽';
                                                        }
                                                        let checked = '';
                                                        if(data.data.current == 'tomorrow_' + type) {
                                                            checked = ' enable';
                                                        }
                                                        choices = choices +
                                                            '<div class="mainblock_time' + checked + '" style="" data-day="tomorrow" data-time="' + type + '">' +
                                                            '<div class="underblocktime" style="">' +
                                                            '<p class="delivery-text">Завтра</p>' +
                                                            '<p>' + price.description + '</p>' +
                                                            '<p class="mainblock_time__price">' + price.price + '</p>' +
                                                            '</div>' +
                                                            '</div>';
                                                    });
                                                }



                                                times.innerHTML = choices;

                                                if(data.data.current == "") {
                                                    $('.mainblock_time').first().addClass('enable');
                                                }

                                                times.classList.add('active');

                                            }
                                        },
                                        error: function (jqXHR, textStatus, errorThrown) {
                                            deliveryMessage.innerHTML = 'Ошибка';
                                            deliveryMessage.style.display = 'flex';
                                        }
                                    });
                                }
                            });
                        }

                        suggestView1.events.add('select', function (e) {
                            var item = e.get('item');
                            ymaps.geocode(item.value).then(function (res) {
                                var firstGeoObject = res.geoObjects.get(0);
                                if (firstGeoObject) {
                                    // Активируем кнопку сразу
                                    const button = document.getElementById('but_dev');
                                    button.classList.remove('disabled-btn');
                                    button.classList.add('enable-dev');

                                    // Обновляем информацию об адресе
                                    const infodev = document.getElementById('infodev');
                                    infodev.textContent = item.value;

                                    const infoTextEl = document.getElementById('delivery-selected-text');
                                    if (infoTextEl) {
                                        infoTextEl.textContent = 'Вы выбрали доставку на адрес: ' + item.value;
                                        infoTextEl.style.display = 'block';
                                    }
                                }
                            });
                        });

                        suggestView1.events.add('suggest', function (e) {
                            var suggestData = e.get('suggestData');
                            var suggestList = document.getElementById('suggest-list1');
                            var input = document.getElementById('suggest1');

                            // Очищаем список подсказок
                            while (suggestList.firstChild) {
                                suggestList.removeChild(suggestList.firstChild);
                            }

                            // Добавляем новые подсказки в список
                            for (var i = 0; i < suggestData.length; i++) {
                                (function (item) {
                                    var suggestItem = document.createElement('li');
                                    suggestItem.textContent = item.displayName;

                                    suggestItem.addEventListener('click', function () {
                                        // Подставляем текст в инпут
                                        if (input) {
                                            input.value = item.displayName;
                                        }

                                        // Прячем список
                                        suggestList.innerHTML = '';

                                        // Геокодим выбранный адрес и вызываем расчёт доставки
                                        ymaps.geocode(item.displayName).then(function (res) {
                                            var firstGeoObject = res.geoObjects.get(0);
                                            var coords = firstGeoObject.geometry.getCoordinates();
                                            var lat = coords[0];
                                            var lon = coords[1];

                                            get_delivery_prices(lat, lon);
                                        });
                                    });

                                    suggestList.appendChild(suggestItem);
                                })(suggestData[i]);
                            }
                        });


                        // Функция для изменения центра и масштаба карты
                        function setMapCenter1(coords) {
                            myMap.setCenter(coords, 16);
                        }
                        // Получение ссылки на поисковый контрол и установка опции openBalloon в true
                        var searchControl = myMap.controls.get('searchControl');
                        searchControl.options.set({ openBalloon: true, noPopup: true});

                        function open_modal() {
                            if (document.getElementById('status_delivery').innerHTML.trim() === '') {
                                navigator.geolocation.getCurrentPosition(function (position) {
                                    var lat = position.coords.latitude;
                                    var lon = position.coords.longitude;

                                    get_delivery_prices(lat, lon);
                                });
                            } else {
                                let coords = document.getElementById('status_delivery').innerHTML;
                                let arrCoords = coords.split(',');
                                var lat = arrCoords[0];
                                var lon = arrCoords[1];

                                get_delivery_prices(lat, lon);
                            }

                            var modal = document.querySelector(".modal1");
                            modal.style.display = "block";
                        }

                        const openModal1Buttons = document.querySelectorAll('.open-modal1');
                        openModal1Buttons.forEach(button => {
                            button.addEventListener('click', () => {
                                open_modal();
                            });
                        });

                        let openModalInput = document.getElementById('billing_delivery');
                        if(openModalInput) {
                            openModalInput.readOnly = true;

                            let change_address_link = document.createElement('a');
                            change_address_link.setAttribute('href', "javascript:;");
                            change_address_link.innerHTML = "Изменить адрес";

                            let AddressWrapper = document.getElementById('billing_delivery_field');
                            AddressWrapper.append(change_address_link);

                            change_address_link.addEventListener('click', function () {
                                open_modal();
                            });
                        }

                        myMap.events.add('click', function (e) {
                            var coords = e.get('coords');

                            // Проверяем, что координаты находятся в границах Владивостока, Артема или Уссурийска
                            ymaps.geocode(coords).then(function (res) {
                                if(document.getElementById('comment_cur')) {
                                    document.getElementById('comment_cur').style.display="none";
                                }
                                if(document.getElementById('comment_cur1')) {
                                    document.getElementById('comment_cur1').style.display="none";
                                }
                                if(document.getElementById('com_cur')) {
                                    document.getElementById('com_cur').style.display="none";
                                }

                                const button = document.getElementById('but_dev');
                                button.classList.remove('enable-dev');
                                button.classList.add('disabled-btn');
                                document.getElementById('flex_time').classList.remove('active');
                                get_delivery_prices(coords[0], coords[1]);
                                getAddress(coords);
                            });
                        });
                        // Создание метки.
                        function createPlacemark(coords) {
                            return new ymaps.Placemark(coords, {
                                iconCaption: 'поиск...'
                            }, {
                                preset: 'islands#violetDotIconWithCaption',
                                draggable: true
                            });
                        }

                        // Определяем адрес по координатам (обратное геокодирование).
                        function getAddress(coords) {
                            myPlacemark.properties.set('iconCaption', 'поиск...');
                            ymaps.geocode(coords).then(function (res) {
                                var firstGeoObject = res.geoObjects.get(0);

                                myPlacemark.properties
                                    .set({
                                        // Формируем строку с данными об объекте.
                                        iconCaption: [
                                            // Название населенного пункта или вышестоящее административно-территориальное образование.
                                            firstGeoObject.getLocalities().length ? firstGeoObject.getLocalities() : firstGeoObject.getAdministrativeAreas(),
                                            // Получаем путь до топонима, если метод вернул null, запрашиваем наименование здания.
                                            firstGeoObject.getThoroughfare() || firstGeoObject.getPremise()
                                        ].filter(Boolean).join(', '),
                                        // В качестве контента балуна задаем строку с адресом объекта.
                                        balloonContent: firstGeoObject.getAddressLine()
                                    });
                            });
                        }
                    }

                </script>
                <style>
                    .search-container {
                        position: relative;
                        display: none;
                    }

                    #search-results {
                        position: absolute;
                        top: 100%;
                        display: none;

                        left: 0;
                        z-index: 1;
                        background-color: #fff;
                        border: 1px solid #ccc;
                        max-height: 200px;
                        overflow-y: auto;
                    }
                </style>
            </div>
            <div id="tab2" class="modal-delivery__tab tab-content1">
                <button id="list_market" class="modal-delivery__tab-shop ...">
        <span class="VV_RWayModalMap__ListShopsShowerText b700">
            Список магазинов
        </span>
                </button>
                <p id="pickup-hint-text">Выберите магазин, чтобы посмотреть товары в наличии и оформить заказ</p>

                <input style="display:none" type="text" placeholder="Поиск" id="suggest">
                <ul style="display:none" id="suggest-list"></ul>

                <div id="map2"></div>


                <!-- ПЕРЕНЕСЁННАЯ КНОПКА -->
                <button type="button" id="but_dev2"
                        class="modal-delivery__choice disabled-btn">
                    Выбрать самовывоз
                </button>
            </div>
            <script>

            </script>
            <div id="selectedMarker"></div>
            <div id='geocoder' class='geocoder'></div>
            <p id="samoviziv" style="display:none"></p>


        </div>
    </div>
</div>
</div>
<style>
    .market_el p{
        margin-bottom: 0px !important;
    }
    .market_el {
        display: flex;
        border-bottom: 1px solid #afafaf;
        align-items: baseline;
        margin-bottom: 14px;
        padding-bottom: 14px;
    }
    .mainblock_time1 {
        margin-left:auto;
    }
</style>
<div id="modal2" class="modal1">
    <div class="modal-content modal-delivery__selfpickup">
        <span id="close_list_market" class="close">&times;</span>
        <h2 class="modal-delivery__selfpickup-title">Выбор магазина </h2>
        <h5 class="modal-delivery__selfpickup-city">Владивосток:</h5>
        <!-- FIX: исправлены дублирующиеся id="market1" на уникальные -->
        <div id="market_egersheld" class="market_el">
            <p class="modal-delivery__selfpickup-adress">Эгершельд, Верхнепортовая, 41в</p>
            <div class="mainblock_time1 enable1" data-market="11" style="">
                <div class="underblocktime1" style="">
                    <p class="delivery-text modal-delivery__selfpickup-btn" style="margin-bottom:0px;">Выбрать</p>
                </div>
            </div>
        </div>
        <script>
            // Получаем элементы DOM
            const closeButton2 = document.getElementById('close_list_market');
            const modal2 = document.getElementById('modal2');

            // Добавляем обработчик клика на кнопку
            closeButton2.addEventListener('click', function() {
                modal2.style.display = 'none';
            });

        </script>
        <div id="market_remicity" class="market_el">
            <p class="modal-delivery__selfpickup-adress">Реми-Сити (ул. Народный пр-т, 20)</p>
            <div class="mainblock_time1 enable1" data-market="1"  style="">
                <div class="underblocktime1" style="">
                    <p class="delivery-text modal-delivery__selfpickup-btn"style="margin-bottom:0px;">Выбрать</p>
                </div>
            </div>
        </div>

        <div id="market_sputnik" class="market_el">
            <p class="modal-delivery__selfpickup-adress">ул. Тимирязева,31 строение 1 (район Спутник)</p>
            <div class="mainblock_time1 enable1" data-market="3" style="">
                <div class="underblocktime1" style="">
                    <p class="delivery-text modal-delivery__selfpickup-btn"  style="margin-bottom:0px;">Выбрать</p>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    @media (max-width:500px) {
        .first_part_time {
            width:40% !important;
        }
    }
</style>
<script>
    ymaps.ready(init);

    function init() {
        var map2 = new ymaps.Map('map2', {
            center: [43.1798, 131.8869],
            zoom: 10,
            controls: ['zoomControl'] // Отключаем все элементы управления
        });
        var suggestView = new ymaps.SuggestView('suggest');

        suggestView.events.add('select', function (e) {
            var item = e.get('item');
            console.log(item.value); // Здесь можно обработать выбранный элемент
            // Определяем координаты выбранного объекта и передаем их в функцию для изменения карты
            ymaps.geocode(item.value).then(function (res) {
                var firstGeoObject = res.geoObjects.get(0);
                var coords = firstGeoObject.geometry.getCoordinates();
                setMapCenter(coords);
            });
        });

        suggestView.events.add('suggest', function (e) {
            var suggestData = e.get('suggestData');
            var suggestList = document.getElementById('suggest-list');

            // Очищаем список подсказок
            while (suggestList.firstChild) {
                suggestList.removeChild(suggestList.firstChild);
            }

            // Добавляем новые подсказки в список
            for (var i = 0; i < suggestData.length; i++) {
                var suggestItem = document.createElement('li');
                suggestItem.textContent = suggestData[i].displayName;
                suggestList.appendChild(suggestItem);
            }
        });

        // Функция для изменения центра и масштаба карты
        function setMapCenter(coords) {
            map2.setCenter(coords, 16);
        }
        var myPlacemark11 = new ymaps.Placemark([43.09968, 131.863907], {
            hintContent: 'Эгершельд, Верхнепортовая, 41в'
        }, {
            balloonContentLayout: null
        });

        var myPlacemark1 = new ymaps.Placemark([43.128381, 131.919746], {
            hintContent: 'Реми-Сити (ул. Народный пр-т, 20)'
        }, {
            balloonContentLayout: null
        });



        var myPlacemark3 = new ymaps.Placemark([43.24827778336888, 132.02109573106299], {
            hintContent: 'ул. Тимирязева,31 строение 1 (район Спутник)'
        }, {
            balloonContentLayout: null
        });

        var myPlacemark8 = new ymaps.Placemark(
            [43.132657, 131.905418],
            {
                hintContent: 'Океанский проспект 108'
            },
            {
                balloonContentLayout: null
            }
        );
        var myGroup = new ymaps.GeoObjectCollection({}, {
            draggable: false,
            preset: 'islands#blueIcon',
            iconColor: '#3caa3c'
        });

        myGroup.add(myPlacemark11)
            .add(myPlacemark1)
            .add(myPlacemark3)
            .add(myPlacemark8);

        map2.geoObjects.add(myGroup);
        // Вешаем обработчик на клик по метке
        myGroup.events.add('click', function (e) {
            // Получаем объект метки
            var target = e.get('target');
            ymaps.geocode(target.geometry.getCoordinates()).then(function (res) {
                var fullAddress = res.geoObjects.get(0).getAddressLine();
                // Отображаем полный адрес магазина
                var addressElem = document.getElementById('suggest');
                if (addressElem) {
                    addressElem.value = fullAddress;
                }
                // Удаляем выбранный класс у предыдущей метки
                myGroup.each(function (el) {
                    el.options.set('iconColor', '#3caa3c');
                });
                // Добавляем класс выбранной метке
                target.options.set('iconColor', '#ff0000');
                // Получаем подсказку магазина
                document.getElementById('samoviziv').innerHTML = target.properties.get('hintContent');
                const button = document.getElementById('but_dev2');
                button.classList.remove('disabled-btn');
                button.classList.add('enable-dev');
                var hintEl = document.getElementById('pickup-hint-text');
                if (hintEl) {
                    hintEl.innerHTML = '📍 Вы выбрали: <b>' + target.properties.get('hintContent') + '</b>';
                    hintEl.style.color = '#4FBD01';
                }
            });
        });

        const buttons = document.querySelectorAll('.mainblock_time1');
        buttons.forEach((button) => {
            button.addEventListener('click', () => {
                // Получаем data-market соответствующий кнопке
                const dataMarket = button.getAttribute('data-market');
                myGroup.each(function (el) {
                    el.options.set('iconColor', '#3caa3c');
                });
                if (dataMarket == 11) {
                    myPlacemark11.options.set('iconColor', '#ff0000');
                    document.getElementById('samoviziv').innerHTML = myPlacemark11.properties.get('hintContent');
                    ymaps.geocode(myPlacemark11.geometry.getCoordinates()).then(function (res) {
                        var fullAddress = res.geoObjects.get(0).getAddressLine();
                        document.getElementById('suggest').value = fullAddress;
                        console.log(fullAddress);
                    });
                    map2.setCenter(myPlacemark11.geometry.getCoordinates());
                    // Устанавливаем масштаб
                    map2.setZoom(15);
                }
                if (dataMarket == 1) {
                    myPlacemark1.options.set('iconColor', '#ff0000');
                    document.getElementById('samoviziv').innerHTML = myPlacemark1.properties.get('hintContent');
                    ymaps.geocode(myPlacemark1.geometry.getCoordinates()).then(function (res) {
                        var fullAddress = res.geoObjects.get(0).getAddressLine();
                        document.getElementById('suggest').value = fullAddress;
                        console.log(fullAddress);
                    });
                    map2.setCenter(myPlacemark1.geometry.getCoordinates());
                    // Устанавливаем масштаб
                    map2.setZoom(15);

                }
                if (dataMarket == 3) {
                    myPlacemark3.options.set('iconColor', '#ff0000');
                    document.getElementById('samoviziv').innerHTML = myPlacemark3.properties.get('hintContent');
                    ymaps.geocode(myPlacemark3.geometry.getCoordinates()).then(function (res) {
                        var fullAddress = res.geoObjects.get(0).getAddressLine();
                        document.getElementById('suggest').value = fullAddress;
                        console.log(fullAddress);
                    });
                    map2.setCenter(myPlacemark3.geometry.getCoordinates());
                    // Устанавливаем масштаб
                    map2.setZoom(15);

                }

                if (dataMarket == 8) {
                    myPlacemark8.options.set('iconColor', '#ff0000');
                    document.getElementById('samoviziv').innerHTML = myPlacemark8.properties.get('hintContent');
                    ymaps.geocode(myPlacemark8.geometry.getCoordinates()).then(function (res) {
                        var fullAddress = res.geoObjects.get(0).getAddressLine();
                        document.getElementById('suggest').value = fullAddress;
                        console.log(fullAddress);
                    });
                    map2.setCenter(myPlacemark8.geometry.getCoordinates());
                    map2.setZoom(15);
                }
                const button2 = document.getElementById('but_dev2');
                button2.classList.remove('disabled-btn');
                button2.classList.add('enable-dev');
                document.getElementById('modal2').style.display = 'none';

                var selectedName = document.getElementById('samoviziv').innerHTML;
                var hintEl = document.getElementById('pickup-hint-text');
                if (hintEl && selectedName) {
                    hintEl.innerHTML = '📍 Вы выбрали: <b>' + selectedName + '</b>';
                    hintEl.style.color = '#4FBD01';
                }
            });

        });
    }



</script>
<style>

    /* Модальное окно */
    .modal1 , .modal2 {
        display: none; /* скрыто по умолчанию */
        position: fixed;
        z-index: 11111111111;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4);
    }

    /* Контент модального окна */
    .modal-content1 , .modal-content2, .modal-content{
        background-color: white;
        margin: 10% auto;
        margin-top:2% !important;
        padding: 32px;
        border-radius: 12px;
        min-width: 300px;
        max-width: 875px;
        position: relative;
    }

    /* Кнопка закрытия модального окна */
    .close-modal1, .close-modal2,  .close{
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 30px;
        font-weight: bold;
        cursor: pointer;
    }

    /* Табы */
    .tab-container1 {
        margin-top: 5px;
    }

    .tab-content1 {
        display: none;
    }

    .tab-content1.active1 {
        display: block;
    }
    @media (max-width: 500px) {
        .tab-content1 {
            padding:5px;
        }
    }
</style>
<script>


    // добавляем обработчик события на нажатие кнопки
    const updateAddressButton = document.getElementById('but_dev');

    updateAddressButton.addEventListener('click', () => {
        const comment         = document.getElementById('comment_cur').value;
        const comment1        = document.getElementById('comment_cur1').value;
        const status_delivery = document.getElementById('status_delivery').innerHTML;
        const addressString   = document.getElementById('infodev').innerHTML;

        const address1 = {
            billing_delivery:      addressString,
            billing_comment:       comment,
            billing_comment_zakaz: comment1,
            coords:                status_delivery,

            // если бэкенд жёстко ждёт поля – шлём пустые строки
            time_type:     '',
            time:          '',
            delivery_day:  '',
            delivery_time: ''
        };

        $.ajax({
            type: 'POST',
            url: 'https://ferma-dv.ru/wp-admin/admin-ajax.php',
            data: {
                action: 'update_user_address',
                address: address1,
            },
            success: function(response) {
                window.location.reload();
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    });
</script>
<p id="data_of_samoviviz" style="display:none;"><?echo $_COOKIE['data_of_samoviviz'];?></p>
<script>
    document.getElementById('list_market').addEventListener('click', () => {
        document.getElementById('modal2').style.display = 'block';
    });
</script>

<script>

    // получаем ссылку на кнопку
    const updateAddressButton1 = document.getElementById('but_dev2');


    // добавляем обработчик события на нажатие кнопки
    updateAddressButton1.addEventListener('click', () => {
        // получаем адрес пользователя из элемента на странице
        const addressString = document.getElementById('samoviziv').innerHTML;

        const address1 = {
            'billing_samoviziv': addressString,
        };

        // отправляем AJAX-запрос на сервер
        $.ajax({
            type: 'POST',
            url: 'https://ferma-dv.ru/wp-admin/admin-ajax.php',
            data: {
                action: 'update_user_address1',
                address: address1,
            },
            success: function(response) {
                window.location.reload();
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    });
</script>
<!-- Старый код === конец -->

<div class="wrapper">
    <div class="overlay"></div>

    <?php if ( is_single() && ! ( function_exists( 'is_product' ) && is_product() ) ) : ?>
        <style>
            .ferma-debug-style-test {
                position: fixed;
                top: 8px;
                right: 8px;
                z-index: 999999;
                background: #111;
                color: #fff;
                font-size: 11px;
                line-height: 1;
                padding: 6px 8px;
                border-radius: 6px;
                opacity: .9;
                pointer-events: none;
            }
            body.single.single-post .header__logo div p { margin: 0 !important; line-height: 1.1 !important; letter-spacing: 0 !important; color: #1a1a1a !important; font-size: 48px !important; }
            body.single.single-post .header__logo div span { display: block !important; margin-top: 2px !important; line-height: 1.1 !important; letter-spacing: 0 !important; color: #1a1a1a !important; font-size: 20px !important; }
            body.single.single-post .header__phone { color: #1a1a1a !important; font-size: 18px !important; font-weight: 700 !important; text-decoration: none !important; }
            body.single.single-post .header__socials a img { opacity: 1 !important; filter: none !important; }
            body.single.single-post .header__desktop-menu { margin: 40px 0 !important; padding: 26px !important; min-height: 94px !important; background-color: #fdf5e5 !important; border-radius: 12px !important; }
            body.single.single-post .header__desktop-menu nav ul { justify-content: center !important; gap: 64px !important; }
            body.single.single-post .header__desktop-menu nav ul li a { color: #1a1a1a !important; font-size: 18px !important; font-weight: 400 !important; text-decoration: none !important; }
            body.single.single-post .header__desktop-bot { display: flex !important; align-items: center !important; justify-content: space-between !important; gap: 24px !important; margin-top: 0 !important; }
            body.single.single-post .header__desktop-bot .header__catalog { width: 200px !important; min-width: 200px !important; height: 56px !important; border-radius: 12px !important; }
            body.single.single-post .header__desktop-bot .header__catalog img { margin-right: 16px !important; }

            body.single.single-post .header__follow .header__desktop-menu { margin: 0 !important; padding: 16px 0 !important; min-height: auto !important; background: transparent !important; border-radius: 0 !important; }
            body.single.single-post .header__follow .header__desktop-menu nav ul { gap: 16px !important; justify-content: space-between !important; }
            body.single.single-post .header__follow .header__desktop-menu nav ul li a { color: #1a1a1a !important; font-size: 16px !important; }
            body.single.single-post .header__follow .header__desktop-bot { margin-top: 0 !important; gap: 24px !important; }
            body.single.single-post .header__follow .header__desktop-bot .header__catalog { width: 56px !important; min-width: 56px !important; height: 56px !important; border-radius: 12px !important; }
            body.single.single-post .header__follow .header__desktop-bot .header__catalog img { margin: 0 !important; }
            body.single.single-post .header__follow .header__desktop-bot .header__delivery { max-width: 424px !important; }
            body.single.single-post .header__follow .header__desktop-bot .header__delivery .header__delivery-result { display: inline-block !important; max-width: 260px !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important; }
        </style>
        <div class="ferma-debug-style-test">STYLE-TEST-5ef6fc7</div>
    <?php endif; ?>
    <header class="header header__product">
        <div class="header__follow">
            <div class="container">
                <div class="header__follow-inner">
                    <div class="header__follow-top">
                        <div class="header__follow-buttons">
                            <button class="header__cart cart-btn btn-grey btn-to-top">
                                <span class="cart-count"><?php echo count( WC()->cart->get_cart() ); ?></span>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/cart.svg" alt="Корзина">
                            </button>
                            <a class="header__bonus btn-grey btn-to-top" href="<? echo get_home_url(); ?>/bonusnaya-programma/">
                                <img src="<?php bloginfo('template_url') ?>/assets/img/cow.svg" alt="Бонусы">
                                <span>
                                        <?php
                                        echo esc_html( do_shortcode( '[user_bonus_count]' ) );
                                        ?>
                                    </span>
                            </a>
                            <a class="header__profile btn-grey btn-to-top" href="<? echo get_home_url(); ?>/my-account/">
                                <img src="<?php bloginfo('template_url') ?>/assets/img/profile.svg" alt="Профиль">
                            </a>
                        </div>
                    </div>
                    <a class="header__logo" href="<? echo get_home_url(); ?>">
                        <img src="<?php bloginfo('template_url') ?>/assets/img/logo.svg" alt="">
                        <div>
                            <p>ФЕРМА DВ</p>
                            <span>фермерский магазин</span>
                        </div>
                    </a>
                    <div class="header__desktop-menu">
                        <nav>
                            <ul>
                                <li>
                                    <a href="<? echo get_home_url(); ?>/about/">О нас</a>
                                </li>
                                <li>
                                    <a href="<? echo get_home_url(); ?>/blog/">Блог</a>
                                </li>
                                <li>
                                    <a href="<? echo get_home_url(); ?>/recipes/">Рецепты</a>
                                </li>
                                <li>
                                    <a href="<? echo get_home_url(); ?>/dostavka/">Доставка и оплата</a>
                                </li>
                                <li>
                                    <a class="text-red" href="<? echo get_home_url(); ?>/stock/">Акции</a>
                                </li>
                                <li>
                                    <a href="<? echo get_home_url(); ?>/nashi-magaziny/">Наши магазины</a>
                                </li>
                                <li>
                                    <a href="<? echo get_home_url(); ?>/bonusnaya-programma/">Бонусы</a>
                                </li>
                                <li>
                                    <a href="<? echo get_home_url(); ?>/horeca/">HoReCa</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div class="header__desktop-bot">
                        <button class="header__catalog btn-green" id="catalog-follow-btn">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/menu.svg" alt="Каталог">
                        </button>
                        <div class="header__search" id="follow-search-btn">
                            <button class="header__search-to-open">
                                <img class="header-follow-search-icon active" src="<?php bloginfo('template_url') ?>/assets/img/search.svg" alt="Поиск">
                                <img class="header-follow-close-icon" src="<?php bloginfo('template_url') ?>/assets/img/close-follow.svg" alt="Поиск">
                            </button>
                            <div class="header__search-content">
                                <?php  echo do_shortcode('[fibosearch]'); ?>
                            </div>
                        </div>
                        <button class="header__delivery btn-grey open-modal1 open-modal__st">
                            <? if ( is_user_logged_in() ) {
                                $user_id = get_current_user_id();
                                $row = get_user_meta( $user_id, 'delivery', true );
                                if($row == '') {
                                    unset($row);
                                }
                                if (isset($row)) {
                                    if ($row == 1) {
                                        echo 'Самовывоз:';
                                        $resultArray = get_user_meta( $user_id, 'billing_samoviziv', true );

                                    }
                                    if ($row == 0) {
                                        if(isset($_COOKIE['delivery_time']) && isset($_COOKIE['delivery_day'])) {
                                            if($_COOKIE['delivery_day'] == "today") {
                                                echo '';
                                            } else if($_COOKIE['delivery_day'] == "tomorrow") {
                                                echo '';
                                            }
                                        } else {
                                            echo '';
                                        }
                                        $cookieValue = get_user_meta( $user_id, 'billing_delivery', true );
                                        $cookieArray = explode(',', $cookieValue);
                                        $resultArray = array_slice($cookieArray, 2);
                                        $resultArray = implode(',', $resultArray);
                                    }
                                } else {
                                    $resultArray = 'Выберите способ получения';
                                }
                            } else {
                                $row = $_COOKIE['delivery'];
                                if (isset($row)) {
                                    if ($row == 1) {
                                        echo 'Самовывоз:';
                                        $resultArray = $_COOKIE['billing_samoviziv'];
                                    }
                                    if ($row == 0) {
                                        if(isset($_COOKIE['delivery_time']) && isset($_COOKIE['delivery_day'])) {
                                            if($_COOKIE['delivery_day'] == "today") {
                                                echo '';
                                            } else if($_COOKIE['delivery_day'] == "tomorrow") {
                                                echo '';
                                            }
                                        } else {
                                            echo '';
                                        }
                                        $cookieValue = $_COOKIE['billing_delivery'];
                                        $cookieArray = explode(',', $cookieValue);
                                        $resultArray = array_slice($cookieArray, 2);
                                        $resultArray = implode(',', $resultArray);
                                    }
                                } else {
                                    $resultArray = 'Выберите способ получения';

                                }
                            }
                            ?><br>
                            <span class="header__delivery-result"><?echo $resultArray;?></span>
                        </button>

                        <button class="header__cart cart-btn btn-grey btn-to-top header__follow-hidden">
                            <span class="cart-count"><?php echo count( WC()->cart->get_cart() ); ?></span>
                            <img src="<?php bloginfo('template_url') ?>/assets/img/cart.svg" alt="Корзина">
                        </button>
                        <a class="header__bonus btn-grey btn-to-top header__follow-hidden" href="<? echo get_home_url(); ?>/bonusnaya-programma/">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/cow.svg" alt="Бонусы">
                            <span>
                                    <?php
                                    echo esc_html( do_shortcode( '[user_bonus_count]' ) );
                                    ?>
                                </span>
                        </a>
                        <a class="header__profile btn-grey btn-to-top header__follow-hidden" href="<? echo get_home_url(); ?>/my-account/">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/profile.svg" alt="Профиль">
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="header__inner">
                <div class="header__desktop">
                    <div class="header__desktop-top">
                        <a class="header__logo" href="<? echo get_home_url(); ?>">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/logo.svg" alt="">
                            <div>
                                <p>ФЕРМА DВ</p>
                                <span>фермерский магазин</span>
                            </div>
                        </a>
                        <div class="header__contacts">
                            <a class="header__phone" href="tel:+79084411110">+7-908-441-1110</a>
                            <div class="header__socials">
                                <a href="https://www.youtube.com/@FermaDV" target="_blank">
                                    <img src="<?php bloginfo('template_url') ?>/assets/img/youtube.svg" alt="youtube">
                                </a>
                                <a href="https://wa.me/79084411110" target="_blank">
                                    <img src="<?php bloginfo('template_url') ?>/assets/img/whatsapp.svg" alt="whatsapp">
                                </a>
                                <a href="https://vk.com/fermadv25/" target="_blank">
                                    <img src="<?php bloginfo('template_url') ?>/assets/img/vk.svg" alt="vk">
                                </a>
                                <a href="https://t.me/fermadv">
                                    <img style="width: 31px" src="<?php bloginfo('template_url') ?>/assets/img/tg.svg" alt="telegram">
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="header__tablet-top">
                        <a class="header__logo" href="<? echo get_home_url(); ?>">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/logo.svg" alt="">
                            <div>
                                <p>ФЕРМА DВ</p>
                                <span>фермерский магазин</span>
                            </div>
                        </a>
                        <div class="header__tablet-buttons">
                            <button class="header__cart cart-btn btn-grey mob_hidden">
                                <span class="cart-count"><?php echo count( WC()->cart->get_cart() ); ?></span>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/cart.svg" alt="Корзина">
                            </button>
                            <a class="header__bonus btn-grey mob_hidden" href="<? echo get_home_url(); ?>/bonusnaya-programma/">
                                <img src="<?php bloginfo('template_url') ?>/assets/img/cow.svg" alt="Бонусы">
                                <span>
                                        <?php
                                        echo esc_html( do_shortcode( '[user_bonus_count]' ) );
                                        ?>
                                    </span>
                            </a>
                            <a class="header__profile btn-grey mob_hidden" href="<? echo get_home_url(); ?>/my-account/">
                                <img src="<?php bloginfo('template_url') ?>/assets/img/profile.svg" alt="Профиль">
                            </a>
                        </div>
                        <a class="header__phone btn-grey" href="tel:+79084411110">+7-908-441-1110</a>
                        <div class="header__contacts">
                            <div class="header__socials">
                                <a href="https://www.youtube.com/@FermaDV" target="_blank">
                                    <img src="<?php bloginfo('template_url') ?>/assets/img/youtube.svg" alt="youtube">
                                </a>
                                <a href="https://wa.me/79084411110" target="_blank">
                                    <img src="<?php bloginfo('template_url') ?>/assets/img/whatsapp.svg" alt="whatsapp">
                                </a>
                                <a href="https://vk.com/fermadv25/" target="_blank">
                                    <img src="<?php bloginfo('template_url') ?>/assets/img/vk.svg" alt="vk">
                                </a>
                                <a href="https://t.me/fermadv">
                                    <img style="width: 31px" src="<?php bloginfo('template_url') ?>/assets/img/tg.svg" alt="telegram">
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="header__desktop-menu">
                        <nav>
                            <ul>
                                <li>
                                    <a href="<? echo get_home_url(); ?>/about/">О нас</a>
                                </li>
                                <li>
                                    <a href="<? echo get_home_url(); ?>/blog/">Блог</a>
                                </li>
                                <li>
                                    <a href="<? echo get_home_url(); ?>/recipes/">Рецепты</a>
                                </li>
                                <li>
                                    <a href="<? echo get_home_url(); ?>/dostavka/">Доставка и оплата</a>
                                </li>
                                <li>
                                    <a href="<? echo get_home_url(); ?>/stock/">Акции</a>
                                </li>
                                <li>
                                    <a href="<? echo get_home_url(); ?>/nashi-magaziny/">Наши магазины</a>
                                </li>
                                <li>
                                    <a href="<? echo get_home_url(); ?>/bonusnaya-programma/">Бонусы</a>
                                </li>
                                <li>
                                    <a href="<? echo get_home_url(); ?>/horeca/">HoReCa</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div class="header__tablet-menu">
                        <nav>
                            <ul>
                                <li>
                                    <a href="<? echo get_home_url(); ?>/about/">О нас</a>
                                </li>
                                <li>
                                    <a href="<? echo get_home_url(); ?>/blog/">Блог</a>
                                </li>
                                <li>
                                    <a href="<? echo get_home_url(); ?>/recipes/">Рецепты</a>
                                </li>
                                <li>
                                    <a href="<? echo get_home_url(); ?>/dostavka/">Доставка и оплата</a>
                                </li>
                            </ul>
                        </nav>
                        <nav>
                            <ul>
                                <li>
                                    <a class="text-red" href="<? echo get_home_url(); ?>/stock/">Акции</a>
                                </li>
                                <li>
                                    <a href="<? echo get_home_url(); ?>/nashi-magaziny/">Наши магазины</a>
                                </li>
                                <li>
                                    <a href="<? echo get_home_url(); ?>/bonusnaya-programma/">Бонусы</a>
                                </li>
                                <li>
                                    <a href="<? echo get_home_url(); ?>/horeca/">HoReCa</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div class="header__mobile">
                        <a class="header__logo" href="<? echo get_home_url(); ?>">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/logo.svg" alt="">
                            <div>
                                <p>ФЕРМА DВ</p>
                                <span>фермерский магазин</span>
                            </div>
                        </a>
                        <div class="header__mobile-top">
                            <a class="header__phone  btn-grey" href="tel:+79084411110">
                                <p>+7-908-441-1110</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/phone_2.svg" alt="Телефон">
                            </a>
                            <a class="header__bonus btn-grey mob_hidden" href="<? echo get_home_url(); ?>/bonusnaya-programma/">
                                <img src="<?php bloginfo('template_url') ?>/assets/img/cow.svg" alt="Бонусы">
                                <span>
                                        <?php
                                        echo esc_html( do_shortcode( '[user_bonus_count]' ) );
                                        ?>
                                    </span>
                            </a>
                            <a class="header__profile btn-grey mob_hidden" href="<? echo get_home_url(); ?>/my-account/">
                                <img src="<?php bloginfo('template_url') ?>/assets/img/profile.svg" alt="Профиль">
                            </a>
                            <button class="header__profile btn-grey" id="open-search">
                                <img src="<?php bloginfo('template_url') ?>/assets/img/search.svg" alt="Поиск">
                            </button>
                        </div>
                        <div class="header__mobile-banner">
                            <div class="swiper bannerSwiper">
                                <div class="swiper-wrapper">
                                    <div class="mobile__banner-item swiper-slide">
                                        <img src="<?php bloginfo('template_url') ?>/assets/img/banner_sale.svg" alt="Молочная продукция">
                                        <p>Молочный понедельник - скидка 10% на Молочку</p>
                                    </div>
                                    <div class="mobile__banner-item swiper-slide">
                                        <img src="<?php bloginfo('template_url') ?>/assets/img/myaso-akcii.svg" alt="Ферма ДВ">
                                        <p>Мясное воскресенье - скидка 10% на всю категорию Мясо</p>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-button-next bannerSwiper-next"></div>
                            <div class="swiper-button-prev bannerSwiper-prev"></div>
                        </div>
                        <div class="header__mobile-bot">
                            <button class="header__delivery btn-grey open-modal1 open-modal__st">
                                <? if ( is_user_logged_in() ) {
                                    $user_id = get_current_user_id();
                                    $row = get_user_meta( $user_id, 'delivery', true );
                                    if($row == '') {
                                        unset($row);
                                    }
                                    if (isset($row)) {
                                        if ($row == 1) {
                                            echo 'Самовывоз:';
                                            $resultArray = get_user_meta( $user_id, 'billing_samoviziv', true );

                                        }
                                        if ($row == 0) {
                                            if(isset($_COOKIE['delivery_time']) && isset($_COOKIE['delivery_day'])) {
                                                if($_COOKIE['delivery_day'] == "today") {
                                                    echo '';
                                                } else if($_COOKIE['delivery_day'] == "tomorrow") {
                                                    echo '';
                                                }
                                            } else {
                                                echo '';
                                            }
                                            $cookieValue = get_user_meta( $user_id, 'billing_delivery', true );
                                            $cookieArray = explode(',', $cookieValue);
                                            $resultArray = array_slice($cookieArray, 2);
                                            $resultArray = implode(',', $resultArray);
                                        }
                                    } else {
                                        $resultArray = 'Выберите способ получения';
                                    }
                                } else {
                                    $row = $_COOKIE['delivery'];
                                    if (isset($row)) {
                                        if ($row == 1) {
                                            echo 'Самовывоз:';
                                            $resultArray = $_COOKIE['billing_samoviziv'];
                                        }
                                        if ($row == 0) {
                                            if(isset($_COOKIE['delivery_time']) && isset($_COOKIE['delivery_day'])) {
                                                if($_COOKIE['delivery_day'] == "today") {
                                                    echo '';
                                                } else if($_COOKIE['delivery_day'] == "tomorrow") {
                                                    echo '';
                                                }
                                            } else {
                                                echo '';
                                            }
                                            $cookieValue = $_COOKIE['billing_delivery'];
                                            $cookieArray = explode(',', $cookieValue);
                                            $resultArray = array_slice($cookieArray, 2);
                                            $resultArray = implode(',', $resultArray);
                                        }
                                    } else {
                                        $resultArray = 'Выберите способ получения';

                                    }
                                }
                                ?><br>
                                <span class="header__delivery-result">
                                            <?echo $resultArray;?>
                                        </span>
                            </button>
                            <div class="header__search" id="hidden-mobile-search">
                                <div class="header__search-container" id="search-mob">
                                    <?php echo do_shortcode('[fibosearch]'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="header__desktop-bot">
                        <button class="header__catalog btn-green header__catalog-test-btn" id="catalog-desc-btn">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/menu.svg" alt="Каталог">Каталог
                        </button>
                        <div class="header__search">
                            <?php echo do_shortcode('[fibosearch]'); ?>
                        </div>
                        <button class="header__delivery btn-grey open-modal1 open-modal__st">
                            <? if ( is_user_logged_in() ) {
                                $user_id = get_current_user_id();
                                $row = get_user_meta( $user_id, 'delivery', true );
                                if($row == '') {
                                    unset($row);
                                }
                                if (isset($row)) {
                                    if ($row == 1) {
                                        echo 'Самовывоз:';
                                        $resultArray = get_user_meta( $user_id, 'billing_samoviziv', true );

                                    }
                                    if ($row == 0) {
                                        if(isset($_COOKIE['delivery_time']) && isset($_COOKIE['delivery_day'])) {
                                            if($_COOKIE['delivery_day'] == "today") {
                                                echo '';
                                            } else if($_COOKIE['delivery_day'] == "tomorrow") {
                                                echo '';
                                            }
                                        } else {
                                            echo '';
                                        }
                                        $cookieValue = get_user_meta( $user_id, 'billing_delivery', true );
                                        $cookieArray = explode(',', $cookieValue);
                                        $resultArray = array_slice($cookieArray, 2);
                                        $resultArray = implode(',', $resultArray);
                                    }
                                } else {
                                    $resultArray = 'Выберите способ получения';
                                }
                            } else {
                                $row = $_COOKIE['delivery'];
                                if (isset($row)) {
                                    if ($row == 1) {
                                        echo 'Самовывоз:';
                                        $resultArray = $_COOKIE['billing_samoviziv'];
                                    }
                                    if ($row == 0) {
                                        if(isset($_COOKIE['delivery_time']) && isset($_COOKIE['delivery_day'])) {
                                            if($_COOKIE['delivery_day'] == "today") {
                                                echo '';
                                            } else if($_COOKIE['delivery_day'] == "tomorrow") {
                                                echo '';
                                            }
                                        } else {
                                            echo '';
                                        }
                                        $cookieValue = $_COOKIE['billing_delivery'];
                                        $cookieArray = explode(',', $cookieValue);
                                        $resultArray = array_slice($cookieArray, 2);
                                        $resultArray = implode(',', $resultArray);
                                    }
                                } else {
                                    $resultArray = 'Выберите способ получения';

                                }
                            }
                            ?><br>
                            <span class="header__delivery-result"><?echo $resultArray;?></span>
                        </button>
                        <div class="catalog-menu" id="catalog-menu-desc">
                            <nav>
                                <ul class="catalog-menu__container">
                                    <li>
                                        <ul class="catalog-menu__list">
                                            <li><a href="<? echo get_home_url(); ?>/product-category/bady">Бады</a></li>
                                            <li><a href="<? echo get_home_url(); ?>/product-category/product-category/bakaleya/">Бакалея</a></li>
                                            <li><a href="<? echo get_home_url(); ?>/product-category/domashnie-syry/">Домашние сыры</a></li>
                                            <li><a href="<? echo get_home_url(); ?>/product-category/varene/">Варенье, соки и компоты</a></li>
                                            <li><a href="<? echo get_home_url(); ?>/product-category/domashnyaya-konservacziya/">Домашняя консервация</a></li>
                                            <li><a href="<? echo get_home_url(); ?>/product-category/kolbasy/">Колбасные изделия</a></li>
                                            <li><a href="<? echo get_home_url(); ?>/product-category/kopchenosti/">Мясные деликатесы</a></li>
                                            <li><a href="<? echo get_home_url(); ?>/product-category/med">Мёд</a></li>
                                            <li><a href="<? echo get_home_url(); ?>/product-category/molochnaya-produkcziya/">Молочная продукция</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <ul class="catalog-menu__list">
                                            <li><a href="<? echo get_home_url(); ?>/product-category/myaso/">Мясо и рыба</a></li>
                                            <li><a href="<? echo get_home_url(); ?>/product-category/ovoshhi/">Овощи, фрукты, ягоды</a></li>
                                            <li><a href="<? echo get_home_url(); ?>/product-category/podarochnye-nabory/">Подарочные наборы</a></li>
                                            <!-- FIX: исправлен </li на </li> -->
                                            <li><a href="<? echo get_home_url(); ?>/product-category/makaronnye-izdeliya/">Макаронные изделия</a></li>
                                            <li><a href="<? echo get_home_url(); ?>/product-category/polufabrikaty-domashnie/">Полуфабрикаты домашние</a></li>
                                            <li><a href="<? echo get_home_url(); ?>/product-category/remeslennyj-hleb-i-vypechka/">Ремесленный хлеб и выпечка</a></li>
                                            <li><a href="<? echo get_home_url(); ?>/product-category/sladosti-i-deserty/">Сладости и десерты</a></li>
                                            <li><a href="<? echo get_home_url(); ?>/product-category/chaj-travy-i-dikorosy/">Чай, травы и дикоросы</a></li>
                                            <li><a href="<? echo get_home_url(); ?>/product-category/tushenka-i-kashi-sobstvennoe-proizvodstvo/">Тушенки и каши собственного производства</a></li>
                                            <li><a href="<? echo get_home_url(); ?>/product-category/yajczo/">Яйцо домашнее</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </nav>
                            <div class="catalog-menu__new">
                                <h5 class="catalog-menu__new-title">НОВИНКИ</h5>
                                <div class="catalog-menu__new-container">
                                    <a href="#" class="catalog-menu__new-item">
                                        <img src="<?php bloginfo('template_url') ?>/assets/img/catalog_new/new_1.jpg">
                                    </a>
                                    <a href="#" class="catalog-menu__new-item">
                                        <img src="<?php bloginfo('template_url') ?>/assets/img/catalog_new/new_2.jpg">
                                    </a>
                                    <a href="#" class="catalog-menu__new-item">
                                        <img src="<?php bloginfo('template_url') ?>/assets/img/catalog_new/new_3.jpg">
                                    </a>
                                    <a href="#" class="catalog-menu__new-item">
                                        <img src="<?php bloginfo('template_url') ?>/assets/img/catalog_new/new_4.jpg">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <button class="header__cart cart-btn btn-grey mob_hidden">
                            <span class="cart-count"><?php echo count( WC()->cart->get_cart() ); ?></span>
                            <img src="<?php bloginfo('template_url') ?>/assets/img/cart.svg" alt="Корзина">
                        </button>
                        <a class="header__bonus btn-grey mob_hidden" href="<? echo get_home_url(); ?>/bonusnaya-programma/">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/cow.svg" alt="Бонусы">
                            <span>
                                    <?php
                                    echo esc_html( do_shortcode( '[user_bonus_count]' ) );
                                    ?>
                                </span>
                        </a>
                        <a class="header__profile btn-grey mob_hidden" href="<? echo get_home_url(); ?>/my-account/">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/profile.svg" alt="Профиль">
                        </a>
                    </div>
                </div>
                <div class="cart" id="mini-cart">
                    <div class="cart__container">
                        <?php woocommerce_mini_cart(); ?>
                    </div>
                </div>
                <div class="mobile__nav">
                    <button class="mobile__nav-menu" id="mob-menu">
                        <div class="mobile__nav-menu--line">
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                        меню
                    </button>
                    <button type="button" class="header__catalog"
                            onclick="window.location.href='<?php echo get_home_url(); ?>/stock/';">
                        <img src="<?php bloginfo('template_url') ?>/assets/img/discount.svg" alt="акции">
                        акции
                    </button>
                    <button class="header__catalog" id="catalog-mob">
                        <img src="<?php bloginfo('template_url') ?>/assets/img/catalog.svg"
                             alt="Каталог">
                        каталог
                    </button>
                    <button class="header__catalog" id="to-open-contacts">
                        <img src="<?php bloginfo('template_url') ?>/assets/img/phone.svg" alt="контакты">
                        контакты
                    </button>
                    <button class="header__cart cart-btn">
                        <span class="cart-count"><?php echo count( WC()->cart->get_cart() ); ?></span>
                        <img src="<?php bloginfo('template_url') ?>/assets/img/cart.svg"
                             alt="Корзина">
                        корзина
                    </button>
                </div>
                <div class="mob-menu" id="mob-menu-container">
                    <nav>
                        <a class="mob-menu__title" href="<? echo get_home_url(); ?>">
                            <p>ФЕРМА DВ</p>
                            <span>фермерский магазин</span>
                        </a>
                        <ul class="mob-menu__list">
                            <li class="mob-menu__list-item">
                                <a href="<? echo get_home_url(); ?>/">Главная</a>
                            </li>
                            <li class="mob-menu__list-item">
                                <a href="<? echo get_home_url(); ?>/about/">О нас</a>
                            </li>
                            <li>
                                <a href="<? echo get_home_url(); ?>/blog/">Блог</a>
                            </li>
                            <li>
                                <a href="<? echo get_home_url(); ?>/recipes/">Рецепты</a>
                            </li>
                            <li>
                                <a href="<? echo get_home_url(); ?>/dostavka/">Доставка и оплата</a>
                            </li>
                            <li>
                                <a class="text-red" href="<? echo get_home_url(); ?>/stock/">Акции</a>
                            </li>
                            <li>
                                <a href="<? echo get_home_url(); ?>/nashi-magaziny/">Наши магазины</a>
                            </li>
                            <li>
                                <a href="<? echo get_home_url(); ?>/bonusnaya-programma/">Бонусы</a>
                            </li>
                            <li>
                                <a href="<? echo get_home_url(); ?>/horeca/">HoReCa</a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <div class="catalog-menu-mob">
                    <nav>
                        <ul class="catalog-menu-mob__list">
                            <li><a href="<? echo get_home_url(); ?>/product-category/bady">Бады</a></li>
                            <li><a
                                        href="<? echo get_home_url(); ?>/product-category/product-category/bakaleya/">Бакалея</a>
                            </li>
                            <li><a href="<? echo get_home_url(); ?>/product-category/domashnie-syry/">Домашние
                                    сыры</a></li>
                            <li><a href="<? echo get_home_url(); ?>/product-category/varene/">Варенье, соки
                                    и компоты</a></li>
                            <li><a href="<? echo get_home_url(); ?>/product-category/domashnyaya-konservacziya/">Домашняя
                                    консервация</a></li>
                            <li><a href="<? echo get_home_url(); ?>/product-category/kolbasy/">Колбасные
                                    изделия</a></li>
                            <li><a href="<? echo get_home_url(); ?>/product-category/kopchenosti/">Мясные
                                    деликатесы</a></li>
                            <li><a href="<? echo get_home_url(); ?>/product-category/med/">Мёд</a></li>
                            <li><a href="<? echo get_home_url(); ?>/product-category/molochnaya-produkcziya/">Молочная
                                    продукция</a></li>
                            <li><a href="<? echo get_home_url(); ?>/product-category/myaso/">Мясо и рыба</a>
                            </li>
                            <li><a href="<? echo get_home_url(); ?>/product-category/ovoshhi/">Овощи,
                                    фрукты, ягоды</a></li>
                            <li><a href="<? echo get_home_url(); ?>/product-category/podarochnye-nabory/">Подарочные
                                    наборы</a></li>
                            <li><a href="<? echo get_home_url(); ?>/product-category/polufabrikaty-domashnie/">Полуфабрикаты
                                    домашние</a></li>
                            <li><a href="<? echo get_home_url(); ?>/product-category/remeslennyj-hleb-i-vypechka/">Ремесленный
                                    хлеб и выпечка</a></li>
                            <li><a href="<? echo get_home_url(); ?>/product-category/sladosti-i-deserty/">Сладости
                                    и десерты</a></li>
                            <li><a href="<? echo get_home_url(); ?>/product-category/chaj-travy-i-dikorosy/">Чай,
                                    травы и дикоросы</a></li>
                            <li><a
                                        href="<? echo get_home_url(); ?>/product-category/tushenka-i-kashi-sobstvennoe-proizvodstvo/">Тушенки
                                    и каши собственного производства</a></li>
                            <li><a href="<? echo get_home_url(); ?>/product-category/yajczo/">Яйцо
                                    домашнее</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="mob-contacts">
                    <div class="header__socials">
                        <a href="https://www.youtube.com/@FermaDV" target="_blank">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/youtube.svg" alt="youtube">
                        </a>
                        <a href="https://wa.me/79084411110" target="_blank">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/whatsapp.svg" alt="whatsapp">
                        </a>
                        <a href="https://vk.com/fermadv25/" target="_blank">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/vk.svg" alt="vk">
                        </a>
                        <a href="https://t.me/fermadv">
                            <img style="width: 31px" src="<?php bloginfo('template_url') ?>/assets/img/tg.svg"
                                 alt="telegram">
                        </a>
                    </div>
                    <div class="mob-contacts__phone">
                        <a class="header__phone" href="tel:+79084411110">+7-908-441-1110</a>
                        <a class="header__phone" href="mailto:zakaz@ferma-dv.ru">zakaz@ferma-dv.ru</a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <style>
        /* Полностью скрыть карты в обеих вкладках */
        #map,
        #map2 {
            display: none !important;
        }

    </style>
    <style>
        /* полностью убираем выбор времени доставки с глаз пользователя */
        #time_change {
            display: none !important;
        }
    </style>