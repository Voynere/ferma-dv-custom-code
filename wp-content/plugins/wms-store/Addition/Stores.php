<?php


namespace Wdc\Addition\Stores;


/**
 * Class Stores
 * @package Wdc\Addition
 */
class Stores
{

    /**
     * @var
     */
    private $settings;

    /**
     * @var
     */
    private $stores;


    /**
     * Stores constructor.
     * @param $settings
     */
    public function __construct($settings)
    {
        $this->setSettings($settings);
        $this->setStores(\WmsStoreApi::get_instance()->get_stores());
    }


    /**
     * @return mixed
     */
    public function getAllStores()
    {
        return $this->getStores();
    }

    /**
     * @return array
     */
    public function getAllowStores()
    {
        if (isset($this->settings['wms_stock_store'])) {
            $option = $this->settings['wms_stock_store'];
        } else {
            $option = array();
        }

        $stores = array();

        foreach ($this->getStores() as $k => $v) {
            if (in_array($k, $option) and $k !== 'all') {
                $stores[$k] = $v;
            }
        }

        return $stores;
    }


    /**
     * @param $stocks
     * @return string
     */
    public function getHtmlStores($stocks)
    {
        $html = '';

        foreach ($this->getAllowStores() as $k => $v) {

            if (isset($stocks[$k])) {

                if ($stocks[$k] > 0) {

                    //$html_stock = apply_filters('wms_addon_product_store_html_instock', "В наличии " . $stocks[$k], $stocks[$k]);
					$html_stock = apply_filters('wms_addon_product_store_html_instock', "В наличии ");

                    $html .= apply_filters(
                        'wms_addon_product_store_html',
                        '<p class="stock in-stock">' . $v['name'] . ':&nbsp;' . $html_stock . '</p> ',
                        $v,
                        $stocks[$k]
                    );

                }

                if ($stocks[$k] <= 0 and $this->settings['wms_addon_store_product_qyt_stock_null'] === 'on') {

                    $html_stock = apply_filters('wms_addon_product_store_html_outofstock', "Нет в наличии", $stocks[$k]);

                    $html .= apply_filters(
                        'wms_addon_product_store_html',
                        '<p class="stock out-of-stock">' . $v['name'] . ': ' . $html_stock . '</p>',
                        $v,
                        $stocks[$k]
                    );
                }

            }
        }
		//if($_SERVER['REMOTE_ADDR'] != "217.150.75.150") {
			//$html = '<p class="stock in-stock">В наличии </p>';
			$html = ' ';
		//}

        return $html;
    }

    /**
     * @return mixed
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @return mixed
     */
    public function getStores()
    {
        return $this->stores;
    }

    /**
     * @param mixed $stores
     */
    public function setStores($stores)
    {
        $this->stores = $stores;
    }

    /**
     * @param mixed $settings
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }


}