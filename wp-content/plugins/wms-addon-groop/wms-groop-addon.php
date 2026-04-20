<?php
/*  Plugin Name: Интеграция МойСклад и WooCommerce Дополнение Группа
    Plugin URI: http://mswoo.ru
    Description: Дополнение Группы
    Version: 0.5
    Author:MsWoo
    Author URI: http://mswoo.ru

*/
if (!defined( 'ABSPATH' )) exit;

define( 'WMS_ADDON_GROOP_PLUGIN', __FILE__ );
define( 'WMS_ADDON_GROOP_PATH', plugin_dir_path( __FILE__ ) );

require_once ABSPATH . "wp-admin/includes/plugin.php";

if (!defined( '__DIR__' )) define( '__DIR__', dirname( __FILE__ ) );
//Проверяем установлен woocmmerce
//Ели нет то выводим сообщение
//Если да то подключаем файлы
if (!is_plugin_active( 'woocommerce-mysklad-sync/woocommerce-mysklad-sync.php' )) {

    function wms_groop_addon_woocommerce_admin_notices()
    {
        $plugin_data = get_plugin_data( __FILE__ );
        $message = sprintf( __( "Плагин <strong>%s</strong> требует установки и активации плагина <strong>Интеграция МойСклад и WooCommerce</strong>.", 'Интеграция МойСклад и WooCommerce Дополнение Группа' ), $plugin_data['Name'] );
        printf( '<div class="updated"><p>%s</p></div>', $message );
    }

    add_action( 'admin_notices', 'wms_groop_addon_woocommerce_admin_notices' );
} else {
    require_once 'wms-groop-addon-class.php';
    require_once 'wms-groop-update.php';
    new WmsAddonGroop();
    $update = new WmsUpdateGroop( 'wms-addon-groop', 'Интеграция МойСклад и WooCommerce Дополнение Группа', 'Интеграция МойСклад и WooCommerce Дополнение Группа', 'https://mswoo.ru/', 'plugin', WMS_ADDON_GROOP_PLUGIN );
}