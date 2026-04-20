<?php
namespace WCSTORES\WC\MS\Controller\Admin\Metaboxes;

use WC_Order;
use WCSTORES\WC\MS\WooCommerce\Utilities\OrderUtil;
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

/**
 * Class OrderMetaBoxController
 * @package WCSTORES\WC\MS\Controller\Admin\Metaboxes
 */
class OrderMetaBoxController
{


    /**
     *
     */
    const COLUMN_KEY = 'wcsms_order';


    /**
     * @version  1.0.0
     */
    public function addMetaBox()
    {
        $screen = 'shop_order';

        if( class_exists( \Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController::class ) ) {
            try{

                if(wc_get_container()->get(CustomOrdersTableController::class)){
                    $CustomOrdersTableController = wc_get_container()->get(CustomOrdersTableController::class);

                    $screen = $CustomOrdersTableController->custom_orders_table_usage_is_enabled()
                        ? wc_get_page_screen_id('shop-order')
                        : 'shop_order';
                }

            } catch (\Exception $e){
                $screen = 'shop_order';
            }

        }


        add_meta_box(
            'wcstores_ms_capture_payment',
            __('Мой Склад'),
            array($this, 'generateMetaBox'),
            $screen,
            'side',
            'high'
        );
    }


    /**
     * @param  $order
     * @param array $args
     * @version  1.10.17
     */
    public function generateMetaBox( $order, array $args = array())
    {
        $this->infoOutput(wc_get_order($order));
    }


    /**
     * @param $columns
     * @return array|string[]
     */
    public function manageEditColumns($columns)
    {
        $columns = array_slice($columns, 0, 4, true) // 4 columns before
            + array(self::COLUMN_KEY => 'Мой Склад') // our column is going to be 5th
            + array_slice($columns, 4, NULL, true);

        return $columns;

    }

    /**
     * @param $column_name
     * @param int | WC_Order $order_or_order_id
     * @version  1.0.0
     */
    public function manageColumn($column_name, $order_or_order_id = 0)
    {

        if (self::COLUMN_KEY !== $column_name) {
            return;
        }

        $this->infoOutput($order_or_order_id);

    }


    /**
     * @param $order_or_order_id
     */
    public function infoOutput($order_or_order_id)
    {
        // legacy CPT-based order compatibility
        $order = OrderUtil::getOrderObject($order_or_order_id);

        $order_uuid = $order->get_meta('_ms_order_id');

        $status = $order_uuid
            ? sprintf(
                "<span><i class='wdc-check wdc-a'>✓</i>%s</span>",
                __('Отправлен', 'woocommerce-gateway-paylease'),
            )
            : sprintf(
                '<a class="button wcsms-send-order__js-button" data-order_id="%d">%s</a>',
                $order->get_id(),
                __('Отправить', 'woocommerce-gateway-paylease'),
            );

        echo sprintf(
            '<div id="wcsms-send-order-%s">%s</div>',
            $order->get_id(),
            $status,
        );

    }

}