<?php
namespace WCSTORES\WC\MS\Controller\Plugins;

use function WCML\functions\getWooCommerceWpml;

/**
 * Class WpmlController
 * @package WCSTORES\WC\MS\Controller\Plugins
 */
class WpmlController
{

    /**
     * @param $item_product_uuid
     * @param $item
     * @return array|mixed|string
     */
    public function getOrderItemProductUuid($item_product_uuid, $item)
    {
        if(!function_exists('\WCML\functions\getWooCommerceWpml')){
            return $item_product_uuid;
        }

        if ($item_product_uuid && wp_is_uuid($item_product_uuid)) {
            return $item_product_uuid;
        }

        $item_product = $item->get_product();
        $item_product_id = $item_product->get_id();

        if(getWooCommerceWpml()->products->is_original_product($item_product_id)){
            return $item_product->get_meta('_id_ms');
        }

        $original_product_id = getWooCommerceWpml()->products->get_original_product_id($item_product_id);

        if(!$original_product = wc_get_product($original_product_id)){
            return $item_product_uuid;
        }

        return $original_product->get_meta('_id_ms');
    }

    /**
     * @param $total
     * @param $item
     * @return float|int|string
     */
    public function getOrderItemTotal($total, $item)
    {
        if(!function_exists('\WCML\functions\getWooCommerceWpml')){
            return $total;
        }

        if(!isset(getWooCommerceWpml()->multi_currency) || !isset(getWooCommerceWpml()->multi_currency->prices)){
            return $total;
        }

        $order = $item->get_order();

        if(!$order_currency = $order->get_currency()){
            return $total;
        }

        $default_currency = getWooCommerceWpml()->multi_currency->get_default_currency();

        if($order_currency === $default_currency){
            return $total;
        }

        return getWooCommerceWpml()
            ->multi_currency->prices
            ->convert_price_amount_by_currencies($total, $order_currency, $default_currency);
    }

}