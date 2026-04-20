<?php

use WCSTORES\WC\MS\MoySklad\CustomerOrderStates;

/**
 * Created by PhpStorm.
 * User: aqw
 * Date: 21.01.2018
 * Time: 21:47
 */

class WmsOrderStatusApi
{

    private $cache = null;

    private $states = array();

    private $settings = null;

    private $cache_object;

    private static $instance;

    public function __construct()
    {
        $this->settings = get_option('wms_settings_order');
        $this->cache_object = WmsCache::get_instance();
        $this->cache = $this->cache_object->get_cache('states');
        $this->states = $this->set_states_array();
    }


    public static function get_instance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @return mixed
     */
    public function get_states()
    {

        return $this->states;
    }


    /**
     * @return false|mixed|void
     */
    public function get_settings()
    {
        return $this->settings;
    }


    private function get_states_ms()
    {
        $states = WmsConnectApi::get_instance()->send_request(WMS_URL_API_V2 . '/entity/customerorder/metadata');

        return ($states and is_array($states) and isset($states['states'])) ? $states['states'] : false;
    }


    /**
     * @return mixed
     */
    private function set_states_array()
    {
        return $this->states = CustomerOrderStates::make()->getByData(10000);
    }


    /**
     *
     */
    public function wc_register_post_statuses()
    {
        $array = WmsOrderStatusApi::get_instance()->get_states();
        $settings = WmsOrderStatusApi::get_instance()->get_settings();
        if (!is_array($array) or !isset($settings['wms_states_wc_ms']) or empty($settings['wms_states_wc_ms'])) {
            return;
        }

        foreach ($array as $k => $v) {

            if(!isset($v['id'])){
                continue;
            }


            if (isset($settings['wms_states_wc_ms'][$v['id']]['activate']) and $settings['wms_states_wc_ms'][$v['id']]['activate'] == 'on') {
                $name = !empty($settings['wms_states_wc_ms'][$v['id']]['name']) ? $settings['wms_states_wc_ms'][$v['id']]['name'] : $v['name'];

                $name = apply_filters('wms_states_label_action', $name, 'wc-' . $settings['wms_states_wc_ms'][$v['id']]['label']);
                register_post_status(
                    'wc-' . $settings['wms_states_wc_ms'][$v['id']]['label'],
                    array(
                        'label' => _x($name, 'WooCommerce Order status', 'text_domain'),
                        'public' => false,
                        'exclude_from_search' => false,
                        'show_in_admin_all_list' => true,
                        'show_in_admin_status_list' => true,
                        'label_count' => _n_noop(
                            $name . ' <span class="count">(%s)</span>',
                            $name . ' <span class="count">(%s)</span>',
                            'text_domain')
                    )
                );
            }
        }
    }

    /**
     * @param $order_statuses
     * @return mixed
     */
    public function wc_add_order_statuses($order_statuses)
    {
        $array = WmsOrderStatusApi::get_instance()->get_states();
        $settings = WmsOrderStatusApi::get_instance()->get_settings();
        if (!is_array($array) or !isset($settings['wms_states_wc_ms']) or empty($settings['wms_states_wc_ms'])) {
            return $order_statuses;
        }


        foreach ($array as $k => $v) {
            if (isset($settings['wms_states_wc_ms'][$v['id']]['activate']) and $settings['wms_states_wc_ms'][$v['id']]['activate'] == 'on') {
                $name = !empty($settings['wms_states_wc_ms'][$v['id']]['name']) ? $settings['wms_states_wc_ms'][$v['id']]['name'] : $v['name'];

                $name = apply_filters('wms_states_label_action', $name, 'wc-' . $settings['wms_states_wc_ms'][$v['id']]['label']);
                $order_statuses['wc-' . $settings['wms_states_wc_ms'][$v['id']]['label']] = _x($name, 'WooCommerce Order status', 'text_domain');
            }
        }
        return $order_statuses;
    }

    /**
     * @param $name
     * @param $color
     */
    public function wreate_style($name, $color)
    {

        $message = '.order-status.status-' . $name . '{background: rgb(' . $color . ');color:#fff;}';
        $filename = WMS_PATH . "assets/css/wms-style-states.css";

        if (!$handle = fopen($filename, 'a')) {
            echo "Не могу открыть файл ( $filename )";

            exit;
        }

        if (fwrite($handle, $message . "\r\n") === FALSE) {

            exit;
        }


        fclose($handle);

    }


}