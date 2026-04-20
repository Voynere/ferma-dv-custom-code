<?php
/*  Plugin Name: Интеграция МойСклад и WooCommerce Дополнение склад
    Plugin URI: http://mswoo.ru
    Description: Склады.
    Version: 0.2.8
    Author:MsWoo
    Author URI: http://mswoo.ru
    WC tested up to: 3.3.1

*/
if (!defined('ABSPATH')) exit;

define('WMS_ADDON_PLUGIN', __FILE__);
define('WMS_ADDON_PATH', plugin_dir_path(__FILE__));

require_once ABSPATH . "wp-admin/includes/plugin.php";

if (!defined('__DIR__')) define('__DIR__', dirname(__FILE__));
//Проверяем установлен woocmmerce
//Ели нет то выводим сообщение
//Если да то подключаем файлы
if (!is_plugin_active('woocommerce-mysklad-sync/woocommerce-mysklad-sync.php')) {

    function wms_store_addon_woocommerce_admin_notices()
    {
        $plugin_data = get_plugin_data(__FILE__);
        $message = sprintf(__("Плагин <strong>%s</strong> требует установки и активации плагина <strong>Интеграция МойСклад и WooCommerce</strong>.", 'Интеграция МойСклад и WooCommerce Адон склад'), $plugin_data['Name']);
        printf('<div class="updated"><p>%s</p></div>', $message);
    }

    add_action('admin_notices', 'wms_store_addon_woocommerce_admin_notices');
} else {

    require_once plugin_dir_path(__FILE__) . 'Addition/Stores.php';
    require_once plugin_dir_path(__FILE__) . 'Addition/StockTable.php';
    require_once plugin_dir_path(__FILE__) . 'Addition/AdditionController.php';
    require_once plugin_dir_path(__FILE__) . 'Addition/Core.php';
    require_once plugin_dir_path(__FILE__) . 'WmsAddonStoreFilterWidget.php';
    require_once plugin_dir_path(__FILE__) . 'WmsAddonStoreFilter.php';
    require_once plugin_dir_path(__FILE__) . 'wms-store-update.php';

    add_action('init', function (){
        $core = new Wdc\Addition\Stores\Core();
        $core->start();
    });

    $update = new WmsUpdateStore(
        'wms-store',
        'Интеграция МойСклад и WooCommerce Адон склад',
        'Интеграция МойСклад и WooCommerce Адон склад',
        'https://mswoo.ru/',
        'plugin',
        WMS_ADDON_PLUGIN
    );
}


// регистрируем файл стилей и добавляем его в очередь
function wms_addon_store_styles()
{

    wp_register_style('wms-addon-store-styles', plugins_url('style.css', __FILE__));
    wp_enqueue_style('wms-addon-store-styles');
}


/*
* регистрация виджета
*/
function wms_addon_store_widget_load()
{
    register_widget('WmsAddonStoreFilterWidget');
}

