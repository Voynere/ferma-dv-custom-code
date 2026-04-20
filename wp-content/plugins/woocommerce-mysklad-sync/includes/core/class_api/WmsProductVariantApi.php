<?php
if (!defined('ABSPATH')) exit;


/**
 * Class WmsProductVariantApi
 */
class WmsProductVariantApi extends WmsAssortment
{

    /**
     * @var string
     */
    protected $product_type = 'product_variation';



    /**
     * @return bool
     * @throws Exception
     */
    public function add()
    {

        $this->settings['wms_post_status'] = 'publish';

        if (!isset($this->assortment['product']) and !isset($this->assortment['characteristics'])) {
            return false;
        }

        $this->parent_id = get_wms_product_id(WmsHelper::get_id_ms_explode($this->assortment['product']['meta']['href']));

        if ($this->parent_id === false) {
            return false;
        }

        $this->set_parent($this->parent_id);

        return parent::add();
    }

    /**
     * @return WC_Product|WC_Product_Variation
     */
    public function get_object_product()
    {
        return new WC_Product_Variation();
    }

    /**
     * @param false $publish
     * @return int|mixed|WP_Error|null
     * @throws Exception
     */
    protected function insert($publish = false)
    {
        $this->product->set_parent_id($this->parent_id);

        return parent::insert(true);
    }


    /**
     * @param $characteristics
     *
     * @return mixed
     */
    protected function get_attribute_variant($characteristics)
    {

        $attribute = new WmsAttributeApi($this->parent_id);
        $attribute->set_attribute_variant($characteristics);

        $variations = [];

        foreach ($characteristics as $characteristic){

            $attribute =
                apply_filters('wms_attribute_before_update',
                    array(
                        'name' => $characteristic['name'],
                        'label' => WmsHelper::get_attribute_label($characteristic['name']),
                        'value' => $characteristic['value']
                    ),
                    $characteristic,
                    $this->product_id
                );

            $variations['attribute_' . sanitize_title('pa_' . $attribute['label'])]
                = sanitize_title(WmsHelper::get_attribute_value($attribute['label'], $attribute['value']));

        }

        return $variations;

    }


    /**
     * @return null
     */
    protected function update()
    {
        $this->product->set_attributes($this->get_attribute_variant($this->assortment['characteristics']));
        parent::update();

    }


}