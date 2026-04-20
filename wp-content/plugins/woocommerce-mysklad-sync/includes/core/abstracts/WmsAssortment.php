<?php

use WCSTORES\WC\MS\Facades\ImageUpdatesQueuesDataStore;
use WCSTORES\WC\MS\MoySklad\Images;
use WCSTORES\WC\MS\Queues\Queues;


/**
 * Class WmsAssortment
 */
abstract class WmsAssortment extends WmsData
{

    /**
     * @var
     */
    protected $assortment;

    /**
     * @var
     */
    protected $publish = 'publish';

    /**
     * @var
     */
    protected $product = null;

    /**
     * @var mixed
     */
    protected $product_id = false;

    /**
     * @var int
     */
    protected $parent_id = 0;

    /**
     * @var
     */
    protected $type;

    /**
     * @var
     */
    protected $product_type = 'product';


    /**
     * @var null
     */
    protected $product_current_hash = null;


    /**
     * @var array
     */
    protected $data = array(
        'post_title' => '',
        'post_type' => '',
        'post_status' => 'publish',
        'post_parent' => 0,
    );


    /**
     * WmsAssortment constructor.
     *
     * @param $assortment
     * @param string $settings
     * @param bool $oProduct
     * @throws Exception
     */
    public function __construct($assortment, $settings = 'wms_settings_product', $oProduct = false)
    {
        $this->set_settings($settings);

        if(is_object($oProduct)){
            $this->product = $oProduct;
            $this->product_id = $oProduct->get_id();
        }else{
            $this->product_id = $oProduct;
        }      

        do_action('before_wms_assortment_action', $this, $assortment, $settings);

        if (!$this->product_id) {
            $var_sync = wms_var_sync($this->settings['wms_product_select_var']);
            $this->product_id = wms_get_product_id($this->settings['wms_product_select_var'], $assortment[$var_sync]);
        }

        $this->set_id($this->product_id);
        $this->set_product($this->product_id);

        $assortment = apply_filters('wms_assortment_ms_array', $assortment, $this->settings, $this->product_id, $this);

        $this->set_assortment($assortment);


    }

    /**
     * @return bool|int|mixed|string|WP_Error|null
     * @throws Exception
     */
    public function add()
    {

        if (isset($this->assortment['archived']) and $this->assortment['archived'] == true) {
            $this->exception($this->product_id, 'Товар находится в архиве');
        }

        if(is_object($this->product) and $this->settings['wms_product_variant_sync'] == 'full'){
            $sProductHash = $this->product->get_meta('_ms_product_hash');

            if($sProductHash and $sProductHash === md5(serialize($this->assortment))){                
                return $this->product;
            }
        } 


        if ($this->product_id == 'empty') {
            return false;
        }

        $products = apply_filters('wms_load_array_products', $this->assortment, $this->settings, $this->product_id, $this);
        if ($products == false) return false;

        $this->set_name($this->assortment['name']);
        $this->set_type($this->product_type);

        if (!isset($this->settings['wms_product_variant_sync']) or $this->settings['wms_product_variant_sync'] == 'fullno') {
            $this->settings['wms_product_variant_sync'] = 'full';
        }

        if ($this->product_id == false and $this->settings['wms_product_variant_sync'] == 'full') {
            $this->product = $this->get_object_product();
            return $this->insert();
        }

        if (apply_filters('wms_load_array_products_ms_updated', false)) {
            $meta_values = get_post_meta($this->product_id, '_ms_updated', true);

            if (!empty($meta_values) and isset($this->assortment['updated']) and $meta_values === $this->assortment['updated']) {
                return false;
            }
        }

        if ($this->product_id > 0) {
            $this->product = $this->get_product($this->product_id);

            if ($this->settings['wms_product_variant_sync'] == 'updated' or $this->settings['wms_product_variant_sync'] == 'full') {
                return $this->update();
            }

            if ($this->settings['wms_product_variant_sync'] == 'updated_meta') {
                return $this->update_only_the_meta();
            }

            return $this->update_only_the_price();
        }

        return false;

    }

    /**
     * @param $product_id
     * @throws Exception
     */
    public function set_product($product_id)
    {
        if ($product_id > 0) {
            $this->product = $this->get_product($product_id);
        }
    }


    /**
     * @return WC_Product
     */
    public function get_object_product()
    {
        return new WC_Product();
    }


    /**
     * @return mixed
     * @throws Exception
     */
    public function update_only_the_price()
    {
        return ($this->update_price()) ? $this->save_product() : false;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function update_only_the_meta()
    {
        return ($this->update_meta()) ? $this->save_product() : false;
    }


    /**
     * @param mixed $product_id
     */
    public function setProductId($product_id)
    {
        $this->product_id = $product_id;
    }


    /**
     * @param bool $publish
     *
     * @return int|mixed|null|WP_Error
     * @throws Exception
     */
    protected function insert($publish = false)
    {

        do_action('before_wms_assortment_insert_action', $this->assortment, $this);

        if (!is_object($this->product)) {
            $this->product = new WC_Product();
        }

        if (isset($this->settings['wms_post_status']) and $publish === false) {
            $this->product->set_status($this->settings['wms_post_status']);
            $this->publish = $this->settings['wms_post_status'];
        } else {
            $this->product->set_status('publish');
        }

        $this->product->set_name($this->assortment['name']);
        $this->product->set_stock_status('instock');
        $this->product->set_catalog_visibility('visible');

        $this->product->update_meta_data('_id_ms', $this->assortment['id']);
        $this->product->update_meta_data('_product_attributes', array());
        $this->updateSku();

        $this->product_id = $this->product->save();

        if ($this->product_id > 0) {

            $this->update_product_type();

            do_action('wms_product_insert_action', $this->product_id, $this->assortment, $this);

            $this->product = $this->get_product($this->product_id);
            $this->update();

            do_action('after_wms_assortment_insert_action', $this->assortment, $this->product_id, $this);

            return $this->product_id;
        } else {
            $this->exception("ОШИБКА создания продукта ");
        }

        return $this->product_id;

    }

    /**
     *
     */
    public function updateSku()
    {
        if (isset($this->settings['wms_product_sku']) and !empty($this->assortment[$this->settings['wms_product_sku']])) {
            $sku = $this->assortment[$this->settings['wms_product_sku']];
        }
        if (isset($sku) and !empty(trim($sku)) and $this->product->get_sku() !== $sku) {
            $this->product->set_sku($sku);
        }

    }


    /**
     * @return bool|mixed|string
     * @throws Exception
     */
    protected function update()
    {
        do_action('before_wms_assortment_update_action', $this->product_id, $this->assortment, $this);

        if (!isset($this->settings['wms_name']) or $this->settings['wms_name'] !== 'on') {
            if ($this->product->get_name() != $this->assortment['name']) {
                $this->product->set_name($this->assortment['name']);
            }
        }

        if (!isset($this->settings['wms_description']) or $this->settings['wms_description'] !== 'on') {
            if (isset($this->assortment['description'])) {
                if (isset($this->settings['wms_description_type'])) {
                    switch ($this->settings['wms_description_type']) {
                        case 'description':
                            $this->product->set_description($this->assortment['description']);
                            break;
                        case 'short_description' :
                            $this->product->set_short_description($this->assortment['description']);
                            break;
                    }
                } else {
                    $this->product->set_description($this->assortment['description']);
                }
            }
        }

        if (isset($this->assortment['weight']) and $this->product->get_weight() !== $this->assortment['weight']) {
            $this->product->set_weight($this->assortment['weight']);
        }

        $this->updateSku();
        $this->update_price();
        $this->update_meta();
        $this->update_product_image();
        $this->update_custom_function($this->product_id);

        $publish = apply_filters('wms_product_update_action_publish', false, $this->assortment, $this->settings, $this->product_id, $this);

        if ($publish) {
            $this->product->set_status($publish);
        }

        do_action('wms_assortment_update_action', $this->product_id, $this->assortment, $this);        

        return $this->save_product();


    }

    /**
     * @return mixed
     * @throws Exception
     */
    protected function save_product()
    {
        if(!is_object($this->product)){
            $this->exception("Товара нет для сохранения");            
        }

        $date_modified = $this->product->get_date_modified();

        if (isset($date_modified) and !empty($date_modified) and $date_modified !== false) {
            $this->product->set_date_modified(current_time('timestamp', true));
        }

        $this->product = apply_filters('wms_assortment_update_filter', $this->product, $this->assortment, $this);

        $this->product->update_meta_data('_ms_product_hash', md5(serialize($this->assortment)));

        if(!$iProductId = $this->product->save()){
            $this->exception($this->product->get_id(), "ОШИБКА сохранение товара ");
        }

        do_action('after_wms_assortment_update_action', $iProductId, $this->assortment, $this);

        return  $iProductId;

    }


    /**
     * @param string $id
     * @param string $message
     * @throws Exception
     */
    protected function exception($id = '', $message = '')
    {
        throw new WC_Data_Exception('product_invalid', $message, 400, array('resource_id' => $id));
    }


    /**
     * @param $product_id
     *
     * @return null
     * @throws Exception
     */
    public function get_product($product_id = 0)
    {
        if (is_object($this->product)) {
            return $this->product;
        }

        if($product_id == 0){
            $this->exception('Не удалось получить товар  id равно 0');
        }

        $this->product = wc_get_product($product_id);

        if (!is_object($this->product)) {
            $this->exception($product_id, 'Не удалось получить товар  id ' . $this->product_id);
        }

        return $this->product;
    }

    /**
     * добавить настройку про обновление изображений
     * @throws Exception
     */
    protected function update_product_image()
    {
        if (isset($this->assortment['images']) && $this->assortment['images']['meta']['size'] > 0 && ($this->settings['wms_load_image'] == 'on' || $this->settings['wms_load_image'] == 'all')) {

            if($data = Images::getDataImage($this->assortment['images'])) {

                if ($this->product->get_image_id() > 0 && $this->product->get_meta('_ms_image_update_hash') == $data['hash']) {
                    return;
                }

                if(ImageUpdatesQueuesDataStore::getQueuesByProductId($this->product->get_id())){
                    return;
                }

                ImageUpdatesQueuesDataStore::create([
                    'product_id' => $this->product->get_id(),
                    'data' => $data
                ]);

                $this->product->update_meta_data('_ms_image_update_hash', $data['hash']);

                do_action('wms_assortment_update_images_action', $this->product->get_id(), $this->assortment['images'], $this->assortment, $this);
            }

        }

    }


    /**
     * @return mixed
     */
    public function get_prices()
    {

        $price = new WmsPriceApi($this->product_id, $this->assortment, $this->settings);

        $assortment_price = $price->price;

        if (empty($assortment_price) and $this->parent_id > 0) {
            $assortment_price = get_post_meta($this->parent_id, '_price', true);
        }

        $assortment_price = floatval(apply_filters('wms_product_price', $assortment_price, $this->settings));
        $product_price['price'] = $assortment_price;
        $product_price['regular_price'] = $assortment_price;


        $product_sale_price = $price->sale_price;

        if (empty($product_sale_price) and $this->parent_id > 0) {
            $product_sale_price = get_post_meta($this->parent_id, '_sale_price', true);
        }

        if ($product_sale_price <= 0) {
            $product_sale_price = '';
        }

        $product_price['sale_price'] = apply_filters('wms_product_sale_price', $product_sale_price, $this->settings);

        return $product_price;
    }


    /**
     * @param $assortment
     *
     * @return mixed
     */
    protected function set_assortment($assortment)
    {
        return $this->assortment = $assortment;
    }


    /**
     * @param $name
     *
     * @return mixed
     */
    protected function set_name($name)
    {
        return $this->data['post_title'] = $name;
    }


    /**
     * @param $type
     *
     * @return mixed
     */
    protected function set_type($type)
    {
        return $this->data['post_type'] = $type;
    }


    /**
     * @param $status
     *
     * @return mixed
     */
    protected function set_status($status)
    {
        return $this->data['post_status'] = $status;
    }

    /**
     * @param $parent
     *
     * @return mixed
     */
    protected function set_parent($parent)
    {
        return $this->data['post_parent'] = $parent;
    }


    /**
     *
     */
    protected function update_product_type()
    {
        $product_type = $this->assortment['meta']['type'] == 'variant' ? 'variable' : 'simple';

        $product_id = $product_type == 'variable' ? $this->parent_id : $this->product_id;

        wp_set_object_terms($product_id, $product_type, 'product_type', false);
    }


    /**
     */
    protected function update_price()
    {
        $price = $this->get_prices();
        $update = false;

        if ($this->product->get_regular_price() != $price['price']) {

            $this->product->set_price($price['price']);
            $this->product->set_regular_price($price['regular_price']);
            $update = true;
        }


        if (isset($this->settings['wms_sale_price_on']) and $this->settings['wms_sale_price_on'] == 'on') {
            if ($this->product->get_sale_price() != $price['price']) {
                $this->product->set_sale_price($price['sale_price']);
                $update = true;
            }
        }


        if($update){
            do_action('wms_assortment_update_price_action', $this, $price);
        }

        return $update;

    }


    /**
     *
     * @return bool
     */
    protected function update_meta()
    {
        $meta_data = array(
            '_id_ms' => $this->assortment['id'],
            '_externalCode' => $this->assortment['externalCode'],
            '_ms_updated' => $this->assortment['updated'],
            '_ms_uuid' => $this->assortment['meta']['uuidHref'],
            '_ms_type' => $this->assortment['meta']['type'],
            'ms_externalCode' => $this->assortment['externalCode'],
            'ms_article' => (isset($this->assortment['article'])) ? $this->assortment['article'] : false,
            'ms_code' => (isset($this->assortment['code'])) ? $this->assortment['code'] : false,
        );

        $update = false;

        foreach ($meta_data as $key => $value) {
            if ($value and $this->is_meta_data_update($key, $value)) {
                $this->product->update_meta_data($key, $value);
                $update = true;
            }
        }

        return  $update;

    }


    /**
     * @param string $key
     * @param string $value
     * @return bool
     */
    protected function is_meta_data_update($key = '', $value = '')
    {
        $key = $this->product->get_meta($key);

        if ($key != $value) {
            return true;
        }

        return false;
    }


    /**
     *
     */
    protected function update_groop()
    {

        if (isset($this->settings['wms_load_groops']) and $this->settings['wms_load_groops'] == 'on') {
            if (isset($this->assortment['productFolder'])) {

                $groop = new WmsGroopApi();
                $groopIds = $groop->update_product_groop($this->product_id, $this->assortment);

                $groopIds = apply_filters('wms_assortment_groop_ids', $groopIds, $this->assortment, $this->settings, $this->product_id);

                if ($groopIds) {
                    $this->product->set_category_ids($groopIds);
                }
            }
            return;
        }


    }


    /**
     * @param $product_id
     * @return mixed
     */
    protected function update_custom_function($product_id)
    {
        return $product_id;
    }


}