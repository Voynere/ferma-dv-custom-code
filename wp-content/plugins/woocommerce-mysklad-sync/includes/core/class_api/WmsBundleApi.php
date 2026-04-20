<?php
/**
 * Created by PhpStorm.
 * User: aqw
 * Date: 24.01.2018
 * Time: 17:07
 */

class WmsBundleApi extends WmsProductApi
{

    /**
     * @param $product_id
     * @throws Exception
     */
    protected function update_custom_function($product_id): void
    {
        $this->update_components();
    }


    /**
     * @throws Exception
     */
    private function update_components(): void
    {

        $components = array();
        $components_ms = $this->get_bundle_components();

        if (!$components_ms || !isset($components_ms['rows']) || !count($components_ms['rows']) > 0) {
            $this->exception($this->product->get_id(), "ошибка получения товаров для комплекта ");
        }

        foreach ($components_ms['rows'] as $component) {

            $components[] = apply_filters(
                'wms_bundle_component',
                [
                    'id'       => $component['assortment']['id'],
                    'href'     => $component['assortment']['meta']['href'],
                    'article'  => $component['assortment']['article'],
                    'code'     => $component['assortment']['code'],
                    'name'     => $component['assortment']['name'],
                    'type'     => $component['assortment']['meta']['type'],
                    'quantity' => $component['quantity'],
                ],
                $component
            );

        }

        $this->product->update_meta_data('_components_ms', $components);

    }

    /**
     * @return bool|mixed
     * @throws Exception
     */
    private function get_bundle_components(): mixed
    {
        return WmsConnectApi::get_instance()
            ->send_request(
                add_query_arg(
                    [
                        'expand' => 'assortment',
                    ],
                    $this->assortment['components']['meta']['href'])
            );

    }

}
