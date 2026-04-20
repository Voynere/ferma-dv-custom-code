<?php

/**
 *   Plugin Name: Интеграция МойСклад и WooCommerce Wordpress
 *   Plugin URI: http://mswoo.ru
 *   Description: Синхронизация Woocommerce и сервиса Мой Склад.
 *   Version: 1.13.2
 *   Author:MsWoo
 *   Author URI: http://mswoo.ru
 *   Requires PHP: 8.1
 *   Tested up to: 6.9.0
 *   WC requires at least: 9.0
 *   WC tested up to: 10.4.3
 *
 */


if (!defined('ABSPATH')) exit;


add_action('plugins_loaded', 'wms_php_admin');

add_action( 'before_woocommerce_init', 'wcstores_ms_hpos_compatibility' );


/**
 * @since  1.1.2
 */
function wms_php_admin()
{

    if (version_compare(PHP_VERSION, 8.1, '<')) {
        add_action('admin_notices', 'wms_php_admin_notices');


        if (!function_exists('deactivate_plugins')) {
            require_once ABSPATH . '/wp-admin/includes/plugin.php';
        }

        deactivate_plugins(plugin_basename(__FILE__));
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
    }


}


/**
 * @since  1.1.2
 */
define('WMS_PLUGIN', __FILE__);


/**
 * @since  1.10.1
 */
define('WMS_PATH', plugin_dir_path(__FILE__));
define('WCSTORES_PREFIX', 'wcstores_moysklad_');
define('WCSTORES_MS_VERSION', '1.13.2');

require_once ABSPATH . "wp-admin/includes/plugin.php";


if (!defined('__DIR__')) define('__DIR__', dirname(__FILE__));
//Проверяем установлен woocmmerce
//Ели нет то выводим сообщение
//Если да то подключаем файлы
if (!is_plugin_active('woocommerce/woocommerce.php')) {

    add_action('admin_notices', 'wms_woocommerce_admin_notices');
} else {
    add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wms_plugin_action_links');
    add_filter('plugin_row_meta', 'wms_plugin_row_meta', 10, 4);

    // Include the main WooCommerce class.
    if (!class_exists('Wms')) {
        include_once dirname(__FILE__) . '/app/bootstrap.php';
        include_once dirname(__FILE__) . '/includes/class-wms.php';
    }


    wms();

}

function wms()
{
    return Wms::instance();
}


/**
 * @param $links
 *
 * @since  1.1.2
 * @return mixed
 */
function wms_plugin_action_links($links)
{
    $settings_link = '<a href="options-general.php?page=wms-settings-page">Настройка</a>';
    array_unshift($links, $settings_link);
    return $links;
}


/**
 * @since  1.1.2
 *
 * @param $meta
 * @param $plugin_file
 *
 * @return array
 */
function wms_plugin_row_meta($meta, $plugin_file)
{
    if (false === strpos($plugin_file, basename(__FILE__)))
        return $meta;

    $meta[] = '<a href="https://docs.mswoo.ru' . '">ДОКУМЕНТАЦИЯ</a>';
    return $meta;
}

/**
 * @since  1.1.2
 */
function wms_php_admin_notices()
{
    $plugin_data = get_plugin_data(__FILE__);
    $message = sprintf(__("Плагин <strong>%s</strong> работает только с версией PHP 5.6 и выше", 'Интеграция МойСклад и WooCommerce'), $plugin_data['Name']);
    printf('<div class="updated"><p>%s</p></div>', $message);
}


/**
 * @since  1.1.2
 */
function wms_woocommerce_admin_notices()
{
    $plugin_data = get_plugin_data(__FILE__);
    $message = sprintf(__("Плагин <strong>%s</strong> требует установки и активации плагина <strong>WooCommerce</strong>.", 'Интеграция МойСклад и WooCommerce'), $plugin_data['Name']);
    printf('<div class="updated"><p>%s</p></div>', $message);
}


function wcstores_ms_hpos_compatibility() {

    if( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
            'custom_order_tables',
            __FILE__,
            true // true (compatible, default) or false (not compatible)
        );
    }

}


