<?php


use WCSTORES\WC\MS\Controller\Admin\Metaboxes\OrderMetaBoxController;
use WCSTORES\WC\MS\Controller\Kernel\FillingTablesWithDataMonitorController;
use WCSTORES\WC\MS\Controller\Sync\AssortmentController;
use WCSTORES\WC\MS\Wordpress\Actions\Filters;
use WCSTORES\WC\MS\Wordpress\Actions\Actions;
use WCSTORES\WC\MS\Controller\Sync\StockController;

Filters::add('wms_monitor_alert_data', [new FillingTablesWithDataMonitorController(), 'alerts']);

Actions::add('wcstores_monitor', function (){ echo '<div id="wcstores_monitor" style="min-width: 100%"></div>';},1000);

if (is_admin()) {
    Actions::add('init', array(new StockController(), 'settingAutomaticStartupSettings'));
    Actions::add('init', array(new AssortmentController(), 'settingAutomaticStartupSettings'));

    $OrderMetaBoxController = new OrderMetaBoxController();

    add_action('add_meta_boxes', array($OrderMetaBoxController, 'addMetaBox'));

    add_filter('manage_edit-shop_order_columns', array($OrderMetaBoxController, 'manageEditColumns'));
    add_filter('manage_woocommerce_page_wc-orders_columns', array($OrderMetaBoxController, 'manageEditColumns'));

    add_action('manage_shop_order_posts_custom_column', array($OrderMetaBoxController, 'manageColumn'), 25, 2);
    add_action('manage_woocommerce_page_wc-orders_custom_column', array($OrderMetaBoxController, 'manageColumn'), 25, 2);
}
