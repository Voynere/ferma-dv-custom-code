<?php

namespace WCSTORES\WC\MS\WooCommerce\Utilities;

use WC_Order;

/**
 * Class OrderUtil
 * @package WCSTORES\WC\MS\WooCommerce
 */
class OrderUtil
{

    /**
     * @param $orderOrOrderId
     * @return bool|WC_Order|\WC_Order_Refund
     */
    public static function getOrderObject($orderOrOrderId)
    {
        return $orderOrOrderId instanceof WC_Order
            ? $orderOrOrderId
            : wc_get_order($orderOrOrderId);
    }


}