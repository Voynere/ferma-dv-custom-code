<?php


namespace Wdc\Addition\Stores;

use Wdc\Addition\Stores\AdditionController as AdditionController;

/**
 * Class Core
 * @package Wdc\Addition\Stores
 */
class Core
{
    /**
     * @var
     */
    private $settings;

    /**
     *
     */
    public function start()
    {
        $settings = get_option('wms_settings_stock');

        $this->setSettings($settings);
        $AdditionController = new AdditionController($settings);

        if (isset($settings['wms_addon_store_visible_filter']) and $settings['wms_addon_store_visible_filter'] == 'on') {
            \WmsAddonStoreFilter::get_instance();
            add_action('widgets_init', 'wms_addon_store_widget_load');
        }

        add_action('woocommerce_product_options_stock', array($AdditionController, 'getInputStores'));
        add_action('woocommerce_variation_options_inventory', array($AdditionController, 'getInputVariationStores'), 10, 3);
        add_action('wms_stock_bystore_quantity_action', array($AdditionController, 'saveStocksToStoresProduct'), 10, 3);


        add_action('wp_enqueue_scripts', 'wms_addon_store_styles');
        add_action('admin_init', array($this, 'getOptions'), 100);


        if (isset($settings['wms_addon_store_product_all_stock_hidden']) and $settings['wms_addon_store_product_all_stock_hidden'] == 'on') {
            add_filter('woocommerce_get_stock_html', array($AdditionController, 'getStocksToStoresAllHidden'), 10, 2);

        } else {
            add_action('woocommerce_single_product_summary', array($AdditionController, 'getStocksToStoresProduct'), 25);
            add_action('woocommerce_available_variation', array($AdditionController, 'getStocksToStoresVariation'), 25);
        }
    }


    /**
     *
     */
    public function getOptions()
    {

        add_settings_field('wms_addon_store_visible_filter', 'Включить фильтр по складам:', array($this, 'getOptionVisibleFilter'), 'wms_load_stock', 'wms_section_stock');
        add_settings_field('wms_addon_store_visible_filter_archive', 'Вывод фильтра на странице магазина :', array($this, 'getOptionVisibleFilterArchive'), 'wms_load_stock', 'wms_section_stock');

        add_settings_field('wms_addon_store_product_all_stock_hidden', 'Спрятать общие остатки :', array($this, 'getOptionAllStocksHidden'), 'wms_load_stock', 'wms_section_stock');
        add_settings_field('wms_addon_store_product_qyt_stock_null', 'Показывать склады с нулевыми остатками :', array($this, 'getOptionQytStockNull'), 'wms_load_stock', 'wms_section_stock');


    }

    /**
     * @param $option_name
     * @return string
     */
    public function getOptionHtmlCheckbox($option_name)
    {
        $option = '';
        if (isset($this->getSettings()[$option_name])) {
            $option = $this->getSettings()[$option_name];
        }

        $check = $option === 'on' ? 'checked' : '';

        $html = '<input 
        type="checkbox" 
        class="wdc-checkbox" 
        id="' . $option_name . '" 
        name="wms_settings_stock[' . $option_name . ']"' .
            $check . '>';

        $html .= '<label for="' . $option_name . '"></label>';

        return $html;
    }

    /**
     *
     */
    public function getOptionAllStocksHidden()
    {
        echo $this->getOptionHtmlCheckbox('wms_addon_store_product_all_stock_hidden');
    }

    /**
     *
     */
    public function getOptionQytStockNull()
    {
        echo $this->getOptionHtmlCheckbox('wms_addon_store_product_qyt_stock_null');
    }

    /**
     *
     */
    public function getOptionVisibleFilter()
    {
        echo $this->getOptionHtmlCheckbox('wms_addon_store_visible_filter');
    }

    /**
     *
     */
    public function getOptionVisibleFilterArchive()
    {
        echo $this->getOptionHtmlCheckbox('wms_addon_store_visible_filter_archive');
    }

    /**
     * @return mixed
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param mixed $settings
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

}