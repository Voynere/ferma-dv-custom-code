<?php


namespace WCSTORES\WC\MS\WooСommerce\Stores\Stocks;


use WCSTORES\WC\MS\WooСommerce\Stores\Stores;

/**
 * Class Stocks
 * @package WCSTORES\WC\MS\Woocommerce\Stores\Stocks
 */
class StocksStore extends Stores
{

     /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->getObjectId();
    }

    protected function getProductIdByMeta($stock, $key = null, $href = null)
    {

        if (!$key) {
            $var_sync = wms_var_sync($this->getSettingsByName('wms_stock_select'));

            if ($var_sync == 'id') {
                $stock['id'] = WmsHelper::get_id_ms_explode($stock['meta']['href']);
            }

            $key = $this->getSettingsByName('wms_stock_select');
        }

        if ($href) {
            return wms_get_product_id($key, WmsHelper::get_id_ms_explode($href));
        }


        return wms_get_product_id($key, $stock[$var_sync]);

    }


    /**
     * @param $aData
     * @return mixed|void
     */
    public function getQuantityByData($aData)
    {
        $iQuantity = (isset($aData['stock'])) ? $aData['stock'] : 0;

        if(isset($aData['reserve'])){
            $iQuantity = (float)$iQuantity - (float)$aData['reserve'];
        }

        return apply_filters( WCSTORES_PREFIX_PLUGINS . 'get_stock_quantity_bystore', (float)$iQuantity, $this);;
    }


    public function add()
    {

        $this->update($this->getData(), $this->getQuantityByData($this->getData()));

        do_action(WCSTORES_PREFIX_PLUGINS . 'stock_allstore_action', $this->getObjectId(), $this->getData(), $this->getSettings());
    }

}