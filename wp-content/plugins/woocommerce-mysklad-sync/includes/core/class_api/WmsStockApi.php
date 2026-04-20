<?php


/**
 * Class WmsStockApi
 */
class WmsStockApi
{
    /**
     * @var
     */
    private $settings;

    /**
     * @var mixed|null
     */
    private $product;


    /**
     * WmsStockApi constructor.
     *
     * @param null $aSettings
     * @param null $oProduct
     */
    public function __construct($aSettings = null, $oProduct = null)
    {
        $this->set_settings($aSettings);

        if ($this->settings == null) {
            $aSettings = get_option('wms_settings_stock');
            $this->set_settings($aSettings);
        }

        $this->product = $oProduct;
    }

    /**
     * @return mixed
     */
    public function get_settings()
    {
        return $this->settings;
    }


    /**
     * @return mixed|void
     */
    public function set_settings($settings)
    {
        $this->settings = $settings;
    }


    /**
     * @param $stock
     * @return bool|int|string
     */
    private function get_assortment_id_stock($stock)
    {
        return wms_get_product_id('_id_ms', WmsHelper::get_id_ms_explode($stock['meta']['href']));
    }


    /**
     * @param $product_id
     * @param $stock
     * @param int $quantity
     *
     * @return bool|int
     */
    public function update_stock($product_id, $stock, $quantity = 0)
    {
        if ($quantity < 0) {
            $quantity = 0;
        }

        do_action('wms_before_stock_update_action', $product_id, $stock, $this->settings, $quantity);

        $product = ($this->product) ? $this->product : wc_get_product($product_id);

        if (!is_object($product)) {
            WmsLogs::set_logs('Не удалось получить товар  id ' . $product_id, true);
            return false;
        }

        $quantity = apply_filters('wms_stock_quantity_update', $quantity, $product, $this->settings, $this);

        if (!$this->is_update_stock($product, $quantity)) {
            return false;
        }


        if (isset($this->settings['wms_stock_type_status']) and $this->settings['wms_stock_type_status'] === 'stock_status') {
            $product->set_manage_stock('no');
        } else {

            if ($product->is_type('variable') and $quantity <= 0) {
                $product->set_manage_stock('no');
            } else {
                $product->set_manage_stock('yes');
            }

            $product->set_stock_quantity($quantity);
        }

        if ($quantity <= 0) {
            $product->set_stock_status('outofstock');
        } else {
            $product->set_stock_status('instock');
        }

        wp_cache_delete($product_id, 'post_meta');
        delete_transient('wc_product_children_' . ($product->is_type('variation') ? $product->get_parent_id() : $product->get_id()));
        wp_cache_delete('product-' . $product_id, 'products');

        $product->set_date_modified(current_time('timestamp', true));

        $product = apply_filters('wms_stock_update_filter', $product, $stock, $this->settings, $quantity);
        $product->save();

        do_action('wms_stock_update_action', $product->get_id(), $stock, $this->settings, $quantity);

        return $product->get_id();


    }

    /**
     * @param $product
     * @param $quantity
     * @return bool|null|string
     */
    public function is_update_stock($product, $quantity)
    {
        $stockStatusCurrent = $product->get_stock_status();
        $quantityCurrent = $product->get_stock_quantity();

        if ($quantity <= 0 and $product->is_type('variable') and $product->get_manage_stock()) {
            WmsLogs::set_logs('У вариативных товаров родители не должны управлять остатками ' . $product->get_id(), true);
            return true;
        }

        if ($quantity != $quantityCurrent) {
            WmsLogs::set_logs('Остатки не совпадают ' . $product->get_id() . ' текуший остаток ' . $quantityCurrent . ' новый ' . $quantity, true);
            return true;
        }

        if ($quantity <= 0 and $stockStatusCurrent == 'instock' and !$product->is_type('variable')) {
            WmsLogs::set_logs('на остатки ноль но в наличии ' . $product->get_id(), true);
            return true;
        }

        if ($quantity > 0 and $stockStatusCurrent == 'outofstock') {
            WmsLogs::set_logs('на остатки больше 0 но нет в наличии ' . $product->get_id(), true);
            return true;
        }

        //WmsLogs::set_logs('смысла нет менять ' . $product->get_id(), true);

        return false;
    }

    /**
     * @param $id
     * @return bool|null|string
     */
    public function get_meta($id)
    {
        if (!is_numeric($id)) {
            return false;
        }

        global $wpdb;
        $value =
            $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT meta_key, meta_value 
                        FROM $wpdb->postmeta 
                        WHERE post_id = %s 
                        AND meta_key IN('_stock', '_stock_status')",
                    $id
                ), OBJECT_K);


        if (isset($value) and !empty($value)) {
            return $value;
        }

        return false;
    }


    /**
     * @param $product_id
     * @param $stocks
     * @return int
     */
    protected function get_stock_quantity_bystore($product_id, $stocks)
    {

        $quantity = 0;

        $stocks = apply_filters('wms_get_stock_quantity_bystore', $stocks['stockByStore'], $product_id, $this->settings);

        foreach ($stocks as $stock){

            $store_id = (explode('/', $stock['meta']['href']));
            $store_id = array_pop($store_id);

            if (in_array($store_id, $this->settings['wms_stock_store'])) {

                $quantity += apply_filters('wms_stock_quantity_filter', (float)$stock['stock'] - (float)$stock['reserve'], $stock, $product_id, $this->settings);

                do_action('wms_stock_bystore_action', $product_id, $store_id, $stock, $this->settings, $quantity);
            }

        }

        return (float)$quantity;

    }


    /**
     * @param $stock
     *
     * @return bool
     */
    public function update_stock_all($stock)
    {

        if (!$product_id = $this->get_product_id($stock)) {
            return false;
        }

        $quantity = (float)$stock['stock'] - (float)$stock['reserve'];

        $this->update_stock($product_id, $stock, $quantity);
        do_action('wms_stock_allstore_action', $product_id, $stock, $this->settings);
    }


    /**
     * @param $stock
     *
     * @return bool
     */
    public function update_stock_bystore($stock)
    {
        if (!$product_id = $this->get_product_id($stock)) {
            return false;
        }

        $stock_quantity = $this->get_stock_quantity_bystore($product_id, $stock);
        $this->update_stock($product_id, $stock, $stock_quantity);
        do_action('wms_stock_bystore_quantity_action', $product_id, $stock, $this->settings, $stock_quantity, $this);
    }


    /**
     * @return mixed|null
     */
    public function get_product()
    {
        return $this->product;
    }


    /**
     * @param $stock
     * @param null $key
     * @param null $href
     * @return bool|int|string
     */
    public function get_product_id($stock, $key = null, $href = null)
    {
        if ($this->product) {
            return $this->product->get_id();
        }

        $product_id = $this->get_assortment_id_stock($stock, $key, $href);

        if ($product_id === false) {
            return false;
        }

        return $product_id;
    }


}
