<?php


namespace Wdc\Addition\Stores;

use Wdc\Addition\Stores\Stores as Stores;


/**
 * Class AdditionController
 * @package Wdc\Addition
 */
class AdditionController
{

    /**
     * @var \Wdc\Addition\Stores
     */
    private $stores;

    /**
     * @var
     */
    private $settings;

    /**
     * AdditionController constructor.
     * @param $settings
     */
    public function __construct($settings)
    {
        $this->setSettings($settings);
        $this->stores = new Stores($settings);
    }

    /**
     * @param $product_id
     * @param $stock
     * @return bool
     */
    public function saveStocksToStoresProduct($product_id, $stock)
    {
        $product = wc_get_product($product_id);
        if (!is_object($product)) {
            return false;
        }

        foreach ($stock['stockByStore'] as $aStores){
            $sStoreId = array_pop(explode('/', $aStores['meta']['href']));

            if (in_array($sStoreId, $this->getSettings()['wms_stock_store'])) {
                $quantity = (float)$aStores['stock'] - (float)$aStores['reserve'];
                $product->update_meta_data($sStoreId, $quantity);
            }
        }

        $product->save();

    }


    /**
     *
     */
    public function getInputStores()
    {
        foreach ($this->stores->getAllowStores() as $k => $v) {
            woocommerce_wp_text_input(array('id' => $k, 'class' => 'wc_input_price short', 'label' => __($v['name'], 'woocommerce')));
        }
    }

    /**
     * @param $loop
     * @param $variation_data
     * @param $variation
     */
    public function getInputVariationStores($loop, $variation_data, $variation)
    {
        print_r($this->getStocksToStores($variation->ID));
    }

    /**
     *
     */
    public function getStocksToStoresProduct()
    {
        global $product;
        if ($product->is_type('variable')) return;
        print_r($this->getStocksToStores($product->get_id()));
    }


    /**
     * @param $variation
     * @return mixed
     */
    public function getStocksToStoresVariation($variation)
    {
        $variation['availability_html'] .= $this->getStocksToStores($variation['variation_id']);

        return $variation;
    }


    /**
     * @param $html
     * @param $product
     * @return string
     */
    public function getStocksToStoresAllHidden($html, $product)
    {

        $stocks = $this->getStocksToStores($product->get_id());

        if (!empty($stocks)) {
            return $stocks;
        }

        return $html;

    }


    /**
     * @param $id
     * @return string
     */
    public function getStocksToStores($id)
    {

        $stocks = $this->getStocks($id);
        if (empty($stocks) or $stocks == false) {
            return;
        }

        $html_store = apply_filters('wms_addon_product_store_filter_select', $this->stores->getHtmlStores($stocks));

        if (!empty($html_store)) {
            return $html_store;
        }

        return '';

    }

    /**
     * @param $id
     * @return mixed
     */
    public function getStocks($id)
    {
        $aProductMeta = get_post_meta($id);

        if(!is_array($aProductMeta)){
            return false;
        }

        foreach ($aProductMeta as $key => $value){
            $aProductMeta[$key] = $value[0];
        }

        return $aProductMeta;
    }

    /**
     * @param $settings
     */
    private function setSettings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return mixed
     */
    private function getSettings()
    {
        return $this->settings;
    }

}