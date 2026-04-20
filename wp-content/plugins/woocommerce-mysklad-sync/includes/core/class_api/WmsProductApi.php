<?php


/**
 * Class WmsProductApi
 */
class WmsProductApi extends WmsAssortment
{

    /**
     * @return bool|mixed|string|void
     * @throws Exception
     */
    protected function update()
    {

        $this->update_groop();
        $this->update_product_attribute();

        do_action('wms_product_update_action', $this->product_id, $this->assortment);

        parent::update();
    }


    /**
     */
    protected function update_product_attribute()
    {
        $attributeApi = new WmsAttributeApi($this->product_id);

        if (isset($this->assortment['country'])) {
            $this->assortment['attributes'][] = array('name' => 'Страна', 'value' => $this->assortment['country']['name']);
        }

        if (isset($this->assortment['attributes'])) {
            $attributeApi->set_attributes($this->assortment['attributes']);
        }

    }


}
