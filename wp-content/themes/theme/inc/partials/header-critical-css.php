<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

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
