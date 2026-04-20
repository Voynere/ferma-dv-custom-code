<?php

use WCSTORES\WC\MS\WooCommerce\Utilities\OrderUtil;

/**
 * Created by PhpStorm.
 * User: aqw
 * Date: 23.01.2018
 * Time: 19:48
 */

class WmsOrderController
{

    /**
     * @var mixed|void
     */
    private $settings;


    /**
     * @version  1.8
     * WmsOrderController constructor.
     */

    public function __construct()
    {

        $this->settings = get_option('wms_settings_order');

    }


    /**
     *
     */
    public function init()
    {
        $this->settings = get_option('wms_settings_order');

        if (isset($this->settings['wms_active_order']) and $this->settings['wms_active_order'] == 'on') {

            if (wp_doing_ajax()) {
                add_action('wp_ajax_wms_send_order', array($this, 'send_order_ms'));
                add_action('wp_ajax_nopriv_wms_send_order', array($this, 'send_order_ms'));
            }


        }

    }



    /**
     * @param $order_id
     * @return mixed|string
     * @throws Exception
     * @version  1.0.0
     */
    public function send_order_ms($order_id = '')
    {
        $order_id = empty($order_id) ? $_POST['order_id'] : $order_id;

        $order = OrderUtil::getOrderObject($order_id);
        $order_ms_id = $order->get_meta('_ms_order_id');

        if (!$order_ms_id) {
            $message = $this->create_order_ms($order_id, true);

            if (wp_doing_ajax()) {
                wp_send_json($message);
            }

            return $order_id;
        }

        return $this->update_order_ms($order_id);
    }

    /**
     * @param $order_id
     * @param bool $return
     * @return string
     * @throws Exception
     * @version  1.0.1
     */
    public function create_order_ms($order_id, $return = false)
    {
        $order_wc = new WmsOrderApi($order_id);
        $create = $order_wc->create_order_ms($order_id);

        if ($return) {
            return $create;
        }
    }

    /**
     * @param $order_id
     * @return mixed
     * @throws Exception
     * @version  1.0.1
     */
    public function update_order_ms($order_id)
    {
        $order = OrderUtil::getOrderObject($order_id);
        $order_ms_id = $order->get_meta('_ms_order_id');

        if (empty($order_ms_id)) {
            $this->create_order_ms($order_id);
            return $order_id;
        }

        $order_wc = new WmsOrderApi($order_id);
        $order_wc->update_order_ms(array('state' => 'yes'));

    }

    /**
     * @param $order_id
     * @throws Exception
     * @version  1.0.1
     */
    public function update_order_ms_state($order_id)
    {
        $order_wc = new WmsOrderApi($order_id);
        $order_wc->update_order_ms(array('state' => 'yes'));

    }

    /**
     * @param $order_id
     * @throws Exception
     * @version  1.9.1
     */
    public function update_order_ms_state_successful_payment($order_id)
    {
        $order_wc = new WmsOrderApi($order_id);
        $order_wc->update_order_ms(array('state' => 'yes', 'stateId' => $this->settings['wms_states_ms_successful_payment']));
    }

    /**
     * @param $url
     * @param $action
     * @throws Exception
     */
    public function update_order_wc($url, $action = '')
    {
        $order = WmsConnectApi::get_instance()->send_request($url . '?expand=state');
        $order_wc = new WmsOrderApi();
        $order_wc->update_order_wc($order);
    }


}

$o = new WmsOrderController();
$o->init();