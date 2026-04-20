<?php
if (!defined('ABSPATH')) exit;

/**
 *
 */
class WmsWebhook
{


    static public function init()
    {
        add_action('wp_ajax_wms_webhook', array('WmsWebhook', 'init_webhook'));
        add_action('wp_ajax_nopriv_wms_webhook', array('WmsWebhook', 'init_webhook'));
    }


    static public function init_webhook()
    {
        $wms_webhook_settings = get_option('wms_settings_webhook');
        $json = file_get_contents('php://input');
        $values = json_decode($json, true);

        foreach ($values['events'] as $event) {

            do_action('wms_webhook_action_' . $event['meta']['type'], $json);
            //WmsLogs::set_logs($event, true);
            $href = apply_filters('wms_webhook_href', str_replace("/1.2/", "/1.1/", $event['meta']['href']));

            switch ($event['meta']['type']) {
                case 'product':
                case 'variant':
                case 'service':
                    if ($wms_webhook_settings['wms_active_webhook_product'] == 'on') {
                        //WmsLogs::set_logs(2323, true);
                        $assortment = new WmsAssortmentController();
                        $assortment->assortment_webhook($event['meta']['href'], $event['action']);

                    }
                    break;
                case 'supply':
                case 'demand':
                case 'enter':
                case 'loss':
                case 'retaildemand':
                case 'move':
                    if ($wms_webhook_settings['wms_active_webhook_stock'] == 'on') {
                        $stock = new WmsStockController();
                        $stock->stock_webhook($href, $event['action']);
                    }
                    break;
                case 'customerorder':
                    $customerorder = new WmsOrderController();
                    $customerorder->update_order_wc($href, $event['action']);
                    break;
            }

        }
        wp_die();

    }


    static public function webhook_array()
    {
        $array = array(
            'product' => array('CREATE', 'UPDATE', 'DELETE'),//товары
            'variant' => array('CREATE', 'UPDATE', 'DELETE'),//модификации
            'service' => array('CREATE', 'UPDATE', 'DELETE'),//услуги
            'supply' => array('CREATE', 'UPDATE', 'DELETE'),//приемка
            'demand' => array('CREATE', 'UPDATE', 'DELETE'),//отгрузка
            'enter' => array('CREATE', 'UPDATE', 'DELETE'),//оприходование
            'loss' => array('CREATE', 'UPDATE', 'DELETE'),//списание
            'retaildemand' => array('CREATE', 'UPDATE', 'DELETE'),//розница продажа
            'move' => array('CREATE', 'UPDATE', 'DELETE'),//перемещение
            'customerorder' => array('UPDATE'),//заказы
        );

        return $array = apply_filters('wms_webhook_array', $array);

    }


    static public function webhook_save_option($wms_webhook_option = 'wms_webhook', $type = 'product', $hook = 'CREATE')
    {

        $wms_webhook = get_option($wms_webhook_option);

        $result = WmsWebhook::ms_create(admin_url('admin-ajax.php?action=' . $wms_webhook_option . '&' . $hook), $hook, $type);
        if (isset($result['errors'])) {
            WmsLogs::set_logs($result['errors'][0]['error'], true);
            $result = 'FALSE';
        } else {
            WmsLogs::set_logs(' Вебхук создан ' . $hook . ' ' . $type, true);
        }

        if ($wms_webhook != false) {
            $webhook_array = array_merge_recursive($wms_webhook, array($type => array($hook => $result)));
        } else {
            $webhook_array = array($type => array($hook => $result));
        }
        update_option($wms_webhook_option, $webhook_array);

    }


    static public function create()
    {
        $result = WmsWebhook::webhook_array();
        foreach ($result as $key => $value) {
            foreach ($value as $key2 => $value2) {

                WmsWebhook::webhook_save_option('wms_webhook', $key, $value2);

            }

        }
    }

    static public function get()
    {
        return WmsConnectApi::get_instance()->send_request(WMS_URL_API_V2 . '/entity/webhook');
    }


    static public function ms_create($url, $action, $entityType)
    {
        $data = array("url" => $url, "action" => $action, "entityType" => $entityType);

        $result = WmsConnectApi::get_instance()->send_request(WMS_URL_API_V2 . '/entity/webhook', 'POST', $data);
        //$result = WmsConnect::post_connect('/entity/webhook', WmsHelper::encode($data));
        return $result;
    }

    static public function delete($all = false)
    {
        $result = WmsWebhook::get();

        foreach ($result['rows'] as $key => $value) {
            if (!$all and preg_match('/(wms)/', $value['url'])) {
                WmsWebhook::delete_webhook($value['id']);
            } else {
                WmsWebhook::delete_webhook($value['id']);
            }

        }

        delete_option('wms_webhook');

    }

    static public function delete_webhook($id)
    {
        $result = WmsConnectApi::get_instance()->send_request(WMS_URL_API_V2 . '/entity/webhook/' . $id, 'DELETE', '');
        WmsLogs::set_logs($result, true);
    }

}