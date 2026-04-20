<?php
if (!defined('ABSPATH')) exit;

$admin = new Wdc_Admin();
$admin->run();

$page = new Wdc_Settings_Page(array(
    'page_title' => 'Настройка МойСклад',
    'menu' => 'МойСклад',
    'capability' => 'manage_options',
    'slug' => 'wms-settings-page',
    'icon' => 'dashicons-cart',
    'position' => 56,
));

$page->set_menu(array(
    array(
        'name' => 'Основные настройки',
        'href' => 'wmsauth',
        'class' => '',
    ),
    array(
        'name' => 'Мониторинг',
        'href' => 'wmsmonitor',
        'class' => '',
    ),
    array(
        'name' => 'Товары',
        'href' => 'wmsproduct',
        'class' => '',
    ),
    array(
        'name' => 'Остатки',
        'href' => 'wmsstock',
        'class' => '',
    ),
    array(
        'name' => 'Заказы',
        'href' => 'wmsorder',
        'class' => '',
    ),
    array(
        'name' => 'Контрагенты',
        'href' => 'counterparty',
        'class' => '',
    ),
    array(
        'name' => 'Хуки',
        'href' => 'wmswebhook',
        'class' => '',
    ),
    array(
        'name' => 'Журнал',
        'href' => 'wmslogs',
        'class' => '',
    ),
));

$page->set_content(array(
    array(
        'name' => 'Основные настройки',
        'href' => 'wmsauth',
        'action' => 'wms_auth',
        'class' => 'show active',
    ),
    array(
        'name' => 'Мониторинг',
        'href' => 'wmsmonitor',
        'action' => 'wcstores_monitor',
        'class' => '',
    ),
    array(
        'name' => 'Настройка загрузки/обновления товара',
        'href' => 'wmsproduct',
        'action' => 'wms_product',
        'class' => '',
    ),
    array(
        'name' => 'Настройка загрузки/обновления остатков товара',
        'href' => 'wmsstock',
        'action' => 'wms_stock',
        'class' => '',
    ),
    array(
        'name' => 'Настройка заказов',
        'href' => 'wmsorder',
        'action' => 'wms_order',
        'class' => '',
    ),
    array(
        'name' => 'Настройка пользователей',
        'href' => 'counterparty',
        'action' => 'wms_counterparty',
        'class' => '',
    ),
    array(
        'name' => 'Настройка webhook',
        'href' => 'wmswebhook',
        'action' => 'wms_webhook',
        'class' => '',
    ),
    array(
        'name' => 'Журнал',
        'href' => 'wmslogs',
        'action' => 'wms_logs',
        'class' => '',
    ),

));

$page->create();