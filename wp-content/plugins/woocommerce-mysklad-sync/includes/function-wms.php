<?php
// регистрируем файл стилей и добавляем его в очередь
use WCSTORES\WC\MS\Support\Main\Nonce;
use WCSTORES\WC\MS\Wordpress\Rest\RestRoute;

function wms_styles()
{
    $version = (is_user_logged_in() && current_user_can('administrator') ) ? time() : WCSTORES_MS_VERSION;


    wp_register_style('wms-style', plugins_url('assets/style.css', WMS_PLUGIN), array(), $version);

    wp_register_script('wms-script', plugins_url('assets/js/mswooscript.js', WMS_PLUGIN), array('jquery'), $version);
    wp_register_script('wms-script2', plugins_url('assets/js/wms-script.js', WMS_PLUGIN), array('jquery'), $version);


    wp_enqueue_style('wms-style');
    wp_enqueue_script('wms-script');
    wp_enqueue_script('wms-script2');

}


function wms_styles_admin()
{
    $version = (is_user_logged_in() && current_user_can('administrator') ) ? time() : WCSTORES_MS_VERSION;

    wp_enqueue_style('wms-style-states', plugins_url('assets/css/wms-style-states.css', WMS_PLUGIN), array(), $version);
    wp_enqueue_style('wms-style-admin', plugins_url('assets/css/wms-style-admin.css', WMS_PLUGIN), array() ,$version);

    wp_register_style('wms-style', plugins_url('assets/css/style.css', WMS_PLUGIN), array() , $version);

    wp_register_script('wms-script', plugins_url('assets/js/mswooscript.js', WMS_PLUGIN), array('jquery'), $version);
    wp_register_script('wms-script2', plugins_url('assets/js/wms-script.js', WMS_PLUGIN), array('jquery'), $version);
    wp_register_script('wms-script-admin', plugins_url('assets/js/wms-script-admin.js', WMS_PLUGIN), array('jquery'), $version);
    wp_enqueue_script('wcstores-ms-core-admin', plugins_url('assets/build/index.js', WMS_PLUGIN), array('wp-element'), $version,true);
    wp_enqueue_script('wms-script-admin');
    wp_enqueue_style('wms-style');


    if (isset($_REQUEST['page']) and $_REQUEST['page'] == 'wms-settings-page') {
        wp_enqueue_script('wms-script');
        wp_enqueue_script('wms-script2');
    }

    wp_localize_script(
        'wcstores-ms-core-admin',
        'wcstoresMsCoreAdmin',
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'home_url' => home_url(),
            'ajax_nonce' => wp_create_nonce('wcstores_admin_nonce'),
            'rest_base_url' => RestRoute::getUrlRoute(),
            'rest_start_sync_product_url' => RestRoute::getUrlRoute('v1/products/sync'),
            'rest_nonce' => Nonce::get(),
            'urls' => array(),
            'i18n' => array(
                'request_error' => __( 'Request Error. Try Again.', 'woocommerce-gateway-beamcheckout' ),
            ),
        )
    );
}


function wms_styles_front()
{
    $version = (is_user_logged_in() && current_user_can('administrator') ) ? time() : WCSTORES_MS_VERSION;

    if (apply_filters('wms_style_front', true)) {
        wp_enqueue_style('wms-style-front', plugins_url('assets/public/css/wms-styles-front.css', WMS_PLUGIN), array(), $version);

    }

    if (apply_filters('wms_script_front', true)) {
        wp_enqueue_script('wms-script-front', plugins_url('assets/public/js/wms-script-front.js', WMS_PLUGIN), array('jquery'), $version, true);

    }
}


function wms_monitor_buttom()
{
    printf('<button class="btn  btn-danger rtsync"  name="rtsync" onclick="wms_rsync()">Сбросить</button>');
    printf('<button class="btn  btn-danger stopsync"  name="stopsync" onclick="wms_stopsync()" style="margin-left: 15px;">Остановить</button>');
    //printf('<button class="btn  btn-success startsync"  name="startsync" onclick="wms_startsync()">Ручной перезапуск</button>');

}


function add_style_states()
{
    unlink(WMS_PATH . "assets/wms-style-states.css");
    $states_array = WmsOrderStatusApi::get_instance()->get_states();
    $option['wms_states_wc_ms'] = array();
    $option = get_option('wms_settings_order');
    if (isset($option['wms_states_wc_ms'])) {
        $option = $option['wms_states_wc_ms'];
    }
    foreach ($states_array as $k => &$v1) {
        WmsOrderStatusApi::get_instance()->wreate_style($option[$v1['id']]['label'], WmsHelper::wms_color($v1['color']));

    }

    echo 'Цвета установлены';
}

function wms_start_sync()
{
    if ($assortment = get_transient('wms_offset_assortment')) {
        WmsWalkerFactory::get_walker('assortment')->cron_init();
        WmsWalkerFactory::get_walker('assortment')->start_walker('start');
        WmsLogs::set_logs('Ручной перезапуск товаров', true);

    }

    if ($stock = get_transient('wms_offset_stock')) {
        WmsWalkerFactory::get_walker('stock')->cron_init();
        WmsWalkerFactory::get_walker('stock')->start_walker('start');
        WmsLogs::set_logs('Ручной перезапуск остатков', true);

    }

    if ($image = get_transient('wms_offset_image')) {
        WmsWalkerFactory::get_walker('image')->cron_init();
        WmsWalkerFactory::get_walker('image')->start_walker('start');
        WmsLogs::set_logs('Ручной перезапуск картинок', true);

    }

    if ($counterparty = get_transient('wms_offset_counterparty')) {
        WmsWalkerFactory::get_walker('counterparty')->cron_init();
        WmsWalkerFactory::get_walker('counterparty')->start_walker('start');
        WmsLogs::set_logs('Ручной перезапуск контрагентов', true);

    }

}


function wms_stop_sync()
{
    $product = get_option('wms_product_update_start');
    if ($product['load'] == 'load' or $product['load'] == 'start') {
        WmsWalkerFactory::get_walker('assortment')->delete_walker('stop');
        update_option('wms_product_update_start', array('size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Остановлено'));
    }

    $stock = get_option('wms_stock_update_start');
    if ($stock['load'] == 'load' or $stock['load'] == 'start') {
        WmsWalkerFactory::get_walker('stock')->delete_walker('stop');
        update_option('wms_stock_update_start', array('size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Остановлено'));
    }

    $image = get_option('wms_image_update_start');
    if ($image['load'] == 'load' or $image['load'] == 'start') {
        WmsWalkerFactory::get_walker('image')->delete_walker('stop');
        update_option('wms_image_update_start', array('size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Остановлено'));
    }

    $counterparty = get_option('wms_counterparty_update_start');
    if ($counterparty['load'] == 'load' or $counterparty['load'] == 'start') {
        WmsWalkerFactory::get_walker('counterparty')->delete_walker('stop');
        update_option('wms_counterparty_update_start', array('size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Остановлено'));
    }

}

function wms_r_sync()
{

    WmsWalkerFactory::get_walker('assortment')->delete_walker();
    WmsWalkerFactory::get_walker('stock')->delete_walker();
    WmsWalkerFactory::get_walker('image')->delete_walker();
    WmsWalkerFactory::get_walker('counterparty')->delete_walker();

}


function wms_order_hook_order_statuses()
{

    $state = new WmsOrderStatusApi();
    add_filter('init', array($state, 'wc_register_post_statuses'));
    add_filter('wc_order_statuses', array($state, 'wc_add_order_statuses'));
}