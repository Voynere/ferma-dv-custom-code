<?php

use WCSTORES\WC\MS\MoySklad\PriceType;


/**
 * Class WmsPriceApi
 */
class WmsPriceApi
{


    /**
     * @var
     */
    public $price;
    /**
     * @var
     */
    public $sale_price;


    /**
     * @var mixed
     */
    private $prices_array;
    /**
     * @var null
     */
    private $product_id;
    /**
     * @var null
     */
    private $settings;


    /**
     * @param null $product_id
     * @param null $products
     * @param null $settings
     * @version  1.0.3
     * WmsPriceApi constructor.
     *
     */
    public function __construct($product_id = null, $products = null, $settings = null)
    {
        $this->product_id = $product_id;
        $this->settings = $settings;

        $this->prices_array = $this->set_price_array();
        $this->set_prices($products);


    }


    /**
     * @return mixed
     * @version  1.0.3
     */
    public function get_prices()
    {
        return $this->prices_array;
    }

    /**
     * @return bool|mixed
     * @throws Exception
     */
    protected function get_prices_ms()
    {
        return WmsConnectApi::get_instance()->send_request(WMS_URL_API_V2 . 'context/companysettings/pricetype');
    }


    /**
     * @return mixed
     */
    private function set_price_array()
    {
        return PriceType::make()->getByData();
    }

    /**
     * @param $products
     * @return void
     */
    private function set_prices($products)
    {
        if(isset($products['salePrices'])) {

            $main_price = 0;
            $sale_price = 0;

            if (!isset($this->settings['wms_price'])) {
                $main_price = intval($products['salePrices'][0]['value']);
            }else{

                foreach ($products['salePrices'] as $price) {
                    if ($price['priceType']['id'] == $this->settings['wms_price']) {
                        $main_price = intval($price['value']);
                    } elseif (isset($this->settings['wms_sale_price']) && $price['priceType']['id'] == $this->settings['wms_sale_price']) {
                        $sale_price = intval($price['value']);
                    }
                }

                if (!$main_price > 0 && $sale_price > 0) {
                    $main_price = $sale_price;
                    $sale_price = 0;
                }

            }

            $this->set_price($main_price);
            $this->set_sale_price($sale_price);
        }

    }

    /**
     * @param mixed $price
     * @return float|int
     */
    private function set_price($price)
    {
        return $this->price = $price / 100;
    }

    /**
     * @param mixed $sale_price
     * @return float|int|string
     */
    private function set_sale_price($sale_price)
    {
        if (!$sale_price > 0) {
            return $this->sale_price = '';
        }
        return $this->sale_price = $sale_price / 100;
    }


}