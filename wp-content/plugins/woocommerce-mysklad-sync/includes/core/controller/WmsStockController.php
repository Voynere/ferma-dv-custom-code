<?php
/**
 * Created by PhpStorm.
 * User: aqw
 * Date: 28.01.2018
 * Time: 19:28
 */

//TODO:добавить возможность обновлять остатки товара из карточки
use WCSTORES\WC\MS\Queues\Queues;
use WCSTORES\WC\MS\Wordpress\Rest\RestRoute;


/**
 * Class WmsStockController
 */
class WmsStockController
{
    /**
     * @var mixed|void
     */
    private $limit;
    /**
     * @var null
     */
    private $settings = null;
    /**
     * @var null
     */
    private $offset = null;
    /**
     * @var
     */
    private $walker;

    /**
     * @var string
     */
    private $type = 'stock';


    /**
     * @version 1.0.3
     * WmsAssortmentController constructor.
     */

    public function __construct()
    {

        $settings = get_option('wms_settings_stock');
        $this->set_settings($settings);

        $limit = 50;
        if (isset($this->settings['wms_stock_limit']) and $this->settings['wms_stock_limit'] > 5) {
            $limit = $this->settings['wms_stock_limit'];
        }
        $this->limit = apply_filters('wms_limit_stock', $limit);

    }


    /**
     *
     */
    public function init()
    {

        add_action('wms_walker_hook_stock', array($this, 'sync'));

        if (wp_doing_ajax()) {
            add_action('wp_ajax_wms-load-start-stock-syn', array($this, 'start_stock_syn'));
            add_action('wp_ajax_nopriv_wms-load-start-stockt-syn', array($this, 'start_stock_syn'));
        }

        add_action('wcstores_moysklad_queues_stock_synchronization_automatic', array($this, 'start_stock_syn'));
        add_action('wcstores_moysklad_queues_sync_stocks_updates', array($this, 'sync'));
        add_action('wcstores_moysklad_queues_checking_for_stocks_updates', array($this, 'sync'));
    }


    /**
     * @param null $settings
     */
    public function set_settings($settings)
    {
        $this->settings = $settings;
    }


    /**
     * @param $url
     * @param $action
     * @throws Exception
     */
    public function stock_webhook($url, $action)
    {
        $stock = WmsConnectApi::get_instance()->send_request($url);

        if ($stock && isset($stock['positions']['meta']['href'])) {
            $this->stock_webhook_position($stock['positions']['meta']['href']);
        } else if ($stock && isset($stock['id']) && isset($stock['meta']['href'])) {
            $this->update_stock_product_by_href($stock['meta']['href'], $stock['meta']['type']);
        }

    }

    /**
     * @param $products
     * @return bool
     */
    public function update_stock_products_by_filter_href($products)
    {
        $count = count($products);

        $this->type = 'webhook_stock';

        if ($products) {

            $this->limit = $count + 10;

            foreach ($products as $product) {
                if($product['type'] === 'product' || $product['type'] === 'variant'){
                    $this->update(0, "&filter={$product['type']}={$product['href']}");
                }
            }

            update_option('wms_stock_update_start', array('load' => 'stop', 'time' => date('d-m-Y H:i:s'), 'message' => 'Сработал вебхук'));

        }

        return true;


    }

    /**
     * @param $href
     * @param $type
     * @return bool
     */
    public function update_stock_product_by_href($href, $type)
    {
        return $this->update_stock_products_by_filter_href(array(
            array(
                'type' => $type,
                'href' => $href
            )
        ));

    }


    /**
     * @param $url
     * @return false
     * @throws Exception
     */
    private function stock_webhook_position($url)
    {

        $stock_position = WmsConnectApi::get_instance()->send_request($url);

        if ($stock_position === false) {
            update_option('wms_stock_update_start', array('time' => 'Ошибка'));
            return false;
        }

        if (isset($stock_position['rows']) && !empty($stock_position['rows'])) {

            $products = array();

            foreach ($stock_position['rows'] as $key => $position) {

                $products[] = array(
                    'type' => $position['assortment']['meta']['type'],
                    'href' => $position['assortment']['meta']['href']
                );

            }

            if ($products) {
                return $this->update_stock_products_by_filter_href($products);
            }
        }


    }


    /**
     * Стартуем синхрон остатков
     */
    public function start_stock_syn()
    {

        $sStartTime = WmsWalkerFactory::get_walker($this->type)->get_start_walker();
        if ($sStartTime and is_numeric($sStartTime) and $sStartTime > 0) {
            if ((time() - $sStartTime) < (apply_filters('wms_stock_start_time_checking_for_hang_ups', 3600 * 6))) {
                WmsLogs::set_logs('Уже идет синхронизация остатков', true);
                return;
            }
        }

        WmsWalkerFactory::get_walker($this->type)->delete_walker();
        WmsWalkerFactory::get_walker($this->type)->cron_init();
        WmsWalkerFactory::get_walker($this->type)->start_walker();

        WmsLogs::set_logs('Стартуем(синхронизация остатков)', true);
        update_option('wms_stock_update_start', array('load' => 'start', 'message' => 'Начало полной синхронизации...'));

        wp_die(Queues::addAsync('sync_stocks_updates', [], 'mswoo'));
    }


    /**
     * @param string $typeSync
     * @param string $parameter_url
     * @param bool $webhook
     * @return int|string[]|void
     */
    public function sync($typeSync = 'full', $parameter_url = '', $webhook = false)
    {
        Queues::addSingle((time() + (60 * 5)), 'checking_for_stocks_updates', [], 'mswoo');

        if (isset($_REQUEST['type'])) {
            $this->type = $_REQUEST['type'];
        }

        $offset = get_transient('wms_offset_' . $this->type);
        $offset = $offset === false ? 0 : $offset;

        WmsWalkerFactory::get_walker($this->type)->start_loop($offset);

        $meta = $this->update($offset, $parameter_url);

        Queues::unscheduleAllActions('checking_for_stocks_updates', [], 'mswoo');


        if ($meta['count'] < $this->limit || !$meta['isProducts']) {

            $count_stock = $offset + $meta['count'];
            WmsLogs::set_logs('У ' . $count_stock . ' товаров остатки успешно загружены.', true);
            update_option('wms_stock_update_start', array('load' => 'stop', 'size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Полная синхронизация'));

            WmsWalkerFactory::get_walker($this->type)->delete_walker();
            unset($this->walker);

            delete_transient('wc_low_stock_count');
            delete_transient('wc_outofstock_count');

            do_action('wms_stock_end_sync');

            return ['ok'];

        }

        WmsWalkerFactory::get_walker($this->type)->end_loop($offset + $this->limit, true);
        update_option('wms_stock_update_start', array('load' => 'load', 'size' => $meta['size'], "count" => $offset, "time" => 0));

        if (isset($this->settings['wms_stock_type_load']) and $this->settings['wms_stock_type_load'] == 'speed') {

            wp_remote_get(
                RestRoute::getUrlRoute('v1/stock/sync'),
                apply_filters(
                    'wms_ajax_wp_remote_get_config',
                    array('timeout' => 5, 'redirection' => 0, 'blocking' => false, 'sslverify' => false)
                )
            );

            return ['ok'];
        }

        return Queues::addAsync('sync_stocks_updates', [], 'mswoo');

    }

    /**
     * @param $offset
     * @param $parameter_url
     * @return array
     */
    protected function update($offset, $parameter_url)
    {

        $stock = $this->get_stock($offset, $parameter_url);

        $count = count($stock['rows']);

        if ($aProducts = $this->get_products($stock)) {
            $aProducts = $this->update_products($aProducts);

            $this->update_single_product($aProducts);
        }

        return [
            'count' => $count,
            'size' => (isset($stock['meta']['size'])) ? $stock['meta']['size'] : 0,
            'isProducts' => (empty($stock['rows'])) ? false : true
        ];


    }


    /**
     * @param $products
     * @return array|null
     */
    protected function get_products($products)
    {
        $aProducts = [];

        if (!isset($products['rows'])) {
            return null;
        }


        if (empty($products['rows'])) {
            return null;
        }

        foreach ($products['rows'] as $product) {
            $sSearchFieldName = msw_get_assortment_search_fields($product, '_id_ms');
            $aProducts[$sSearchFieldName] = $product;
        }

        return $aProducts;

    }


    /**
     * @param $aProducts
     * @return mixed
     */
    protected function update_products($aProducts)
    {
        if ($aoProducts = wms_get_products_by_uuid_ms(array_keys($aProducts), $this->limit * 10)) {
            foreach ($aoProducts as $oProduct) {
                $uuid = $oProduct->get_meta('_id_ms');
                if (!empty($uuid) and isset($aProducts[$uuid])) {
                    $this->update_stock($aProducts[$uuid], $oProduct);
                    unset($aProducts[$uuid]);
                    continue;
                }
            }
        }

        return $aProducts;

    }


    /**
     * @param $aProducts
     * @return mixed
     */
    protected function update_single_product($aProducts)
    {
        if (!empty($aProducts)) {
            foreach ($aProducts as $aProduct) {
                $this->update_stock($aProduct);
            }

        }

        return $aProducts;

    }


    /**
     * @param $offset
     *
     * @param string $parametr_url
     * @return bool|mixed
     * @throws Exception
     */
    private function get_stock($offset, $parametr_url = '')
    {
        $args = array([
            'offset' => $offset,
            'limit' => $this->limit,
            'filter' => 'stockMode=all'
        ]);

        if (in_array('all', $this->settings['wms_stock_store'])) {
            $args['filter'] .= ';quantityMode=all';
            $url = WMS_URL_API_V2 . 'report/stock/all/';
        } else {
            $url = WMS_URL_API_V2 . 'report/stock/bystore/';
        }

        $args = apply_filters('wms_get_stock_url_args', $args);
        $url = apply_filters('wms_get_stock_url', add_query_arg($args, $url) . $parametr_url);

        $stock = WmsConnectApi::get_instance()->send_request($url);

        if (!$stock) {
            WmsWalkerFactory::get_walker($this->type)->delete_walker();
            update_option('wms_stock_update_start', array('time' => 'Ошибка'));
            wp_die();
        }

        return $stock;

    }


    /**
     *
     * @param $stock
     *
     * @param null $product
     * @return bool
     * @version 1.0.4
     */
    private function update_stock($stock, $product = null)
    {
        try {

            $stock = apply_filters('wms_stock_filter_controller', $stock, $this->settings);

            if ($stock === false) {
                return false;
            }

            $stocks = new WmsStockApi($this->settings, $product);

            if (in_array('all', $this->settings['wms_stock_store'])) {
                $stocks->update_stock_all($stock);
            } else {
                $stocks->update_stock_bystore($stock);
            }

            unset($stocks);

        } //Перехватываем (catch) исключение, если что-то идет не так.
        catch (Exception $ex) {
            WmsLogs::set_logs($ex->getMessage(), true);

        }
        return true;

    }

}

$o = new  WmsStockController();
$o->init();