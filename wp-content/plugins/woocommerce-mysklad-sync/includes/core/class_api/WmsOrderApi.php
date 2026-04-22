<?php

use WCSTORES\WC\MS\Queues\Queues;

/**
 * Created by PhpStorm.
 * User: aqw
 * Date: 22.01.2018
 * Time: 20:42
 */

class WmsOrderApi extends WmsData
{

    /**
     * @var
     */
    private $order_wc;

    /**
     * @var
     */
    private $customerorder;

    /**
     * @var bool
     */
    private $is_error = false;

    /**
     * @var
     */
    private $error_message;

    /**
     * @var
     */
    private $positions = [];

    private $is_update_wc = false;

    private $is_update_ms = false;


    /**
     * @param string $id
     * @version  1.0.1
     * WmsOrderApi constructor.
     */
    public function __construct($id = '')
    {
        if (!empty($id) and $id > 0) {
            $this->set_id($id);
        }

        $this->set_settings('wms_settings_order');
        $this->order_wc = new WC_Order($id);

        return $this;
    }

    /**
     * @param $order_id
     * @return string
     * @throws Exception
     * @version  1.8.9
     */
    public function create_order_ms($order_id)
    {
        if ($this->order_wc->get_meta('_ms_order_id')) {
            return 'Заказ уже выгружен в Мой склад';
        }

        $this->set_name($this->order_wc);
        $this->set_organization($this->settings['wms_organization']);
        $this->set_counterparty($this->order_wc);
        $this->set_store($this->settings['wms_stock_store_order'][0]);
        $this->set_comment($this->order_wc);
        $this->set_shipment_address($this->order_wc);

        if (isset($this->settings['wms_create_order_state']) and $this->settings['wms_create_order_state'] == 'on') {
            $this->set_state($this->order_wc);
        }

        $this->customerorder['vatEnabled'] = false;

        if (isset($this->settings['wms_order_date']) and $this->settings['wms_order_date'] == 'date_create') {
            $this->customerorder['moment'] = $this->order_wc->order_date;
        }

        $this->customerorder = apply_filters('wms_order_action', $this->customerorder, $this->order_wc);

        if ($this->is_error) {
            WmsLogs::set_logs('Заказ не передан' . $order_id . '  ' . $this->error_message, true);
            $this->set_cron_marker();
            return 'Заказ не передан ' . $this->error_message;
        }

        $this->set_attribute($this->customerorder, $this->order_wc);
        $order_ms = $this->post(WMS_URL_API_V2 . 'entity/customerorder', $this->customerorder, false);

        if ($order_ms === false) {
            WmsLogs::set_logs('Заказ не передан' . $order_id, true);
            $this->set_cron_marker();

            Queues::addSingle(time() + (60 * 10), 'create_an_order_in_moysklad', array('order_id' => $order_id));

            return 'Заказ не передан';
        }

        $this->order_wc->update_meta_data('_ms_order_id', $order_ms['id']);
        $this->order_wc->update_meta_data('_ms_updated', $order_ms['updated']);

        $positions_result = $this->set_positions($this->order_wc, $order_ms);
        if ($positions_result === false) {
            $msg = 'Заказ выгружен в МойСклад без позиций: проверьте _id_ms/SKU у товаров.';
            WmsLogs::set_logs($msg . ' Order ID: ' . $order_id, true);
            $this->order_wc->add_order_note($msg);
            // Поставим ретрай на случай временной рассинхронизации ассортимента.
            Queues::addSingle(time() + (60 * 10), 'create_an_order_in_moysklad', array('order_id' => $order_id));
        }

        $this->order_wc->delete_meta_data('_ms_cron_marker');

        $this->order_wc->save();

        do_action('wms_customerorder_create', $order_id, $order_ms, $this->order_wc);

        WmsLogs::set_logs('Заказ №' . $order_id . ' Успешо выгружен в Мой склад', true);

        $this->order_wc->add_order_note('Заказ успешо выгружен в Мой склад');
        return 'Заказ успешо выгружен в Мой склад';


    }

    /**
     * @param array $arg
     * @param bool $is_ms_updated_marker
     * @return string|void
     * @throws Exception
     * @version  1.8.9
     */
    public function update_order_ms($arg = array(), $is_ms_updated_marker = true)
    {
        if ($is_ms_updated_marker && $this->order_wc->get_meta('_ms_updated_marker')) {
            return;
        }

        $customerorder_id = $this->get_customerorder_ms_id($this->order_wc);

        if (empty($customerorder_id) or $customerorder_id == false) {
            return 'Заказ не передан';
        }


        if (isset($arg['state'])) {
            $sStateMsId = isset($arg['stateId']) ? $arg['stateId'] : false;
            $this->set_state($this->order_wc, $sStateMsId);

            $this->is_update_ms = true;
        }

        if ($this->is_error) {
            WmsLogs::set_logs('Заказ не передан' . $this->id . '  ' . $this->error_message, true);
            return 'Заказ не передан';
        }

        if(!$this->is_update_ms){
            return '';
        }

        $order_ms = $this->put(WMS_URL_API_V2 . 'entity/customerorder/' . $customerorder_id, $this->customerorder);

        if (!$order_ms) {
            WmsLogs::set_logs('Заказ не передан' . $this->id, true);
            return 'Заказ не передан';
        }

        $this->order_wc->update_meta_data('_ms_updated', $order_ms['updated']);
        $this->order_wc->save();

        do_action('wms_customerorder_update', $this->id, $order_ms, $this->order_wc);

        WmsLogs::set_logs('Заказ №' . $this->id . ' Успешо обновлен в Мой склад', true);

        $this->order_wc->add_order_note('Заказ успешо обновлен в Мой склад');
        return 'Заказ успешо обновлен в Мой склад';


    }

    /**
     * @return array|void
     * @version  1.0.1
     */
    private function get_customerorder_ms_updated()
    {
        return $this->order_wc->get_meta('_ms_updated');

    }

    /**
     * @param $order
     *
     * @return array|void
     * @version  1.0.9
     *
     */
    private function get_customerorder_ms_id($order)
    {
        return $order->get_meta('_ms_order_id');

    }

    /**
     * @param $order
     *
     * @return array|void
     * @version  1.0.1
     *
     */
    private function get_state_id($order)
    {
        $array = WmsOrderStatusApi::get_instance()->get_states();
        if (!is_array($array) or !isset($this->settings['wms_states_wc_ms']) or empty($this->settings['wms_states_wc_ms'])) {
            return;
        }

        foreach ($array as $k => $v) {
            if ('wc-' . $this->settings['wms_states_wc_ms'][$v['id']]['label'] == $order->post_status and $this->settings['wms_states_wc_ms'][$v['id']]['activate'] == 'on') {
                return $v['id'];
            }
        }
        return false;

    }

    /**
     * @return mixed|void
     * @version 1.0.9
     */
    private function get_user_order_email($order)
    {
        $email = $order->get_billing_email();

        if (is_email($email)) {
            return $email;
        }

        $wc_user = $order->get_user();

        if (isset($wc_user->user_email) and !empty($wc_user->user_email)) {
            $email = $wc_user->user_email;
        }

        return apply_filters('wms_order_email', $email, $order);
    }


    /**
     * @return mixed|void
     * @version 1.0.9
     */
    private function get_user_order_name($order)
    {
        $name = trim(sprintf('%1$s %2$s', $order->get_billing_last_name(), $order->get_billing_first_name()));
        if (empty($name)) {
            $wc_user = $order->get_user();
            $meta_user = array_map(function ($a) {
                return $a[0];
            }, get_user_meta($wc_user->ID));
            $name = $meta_user['last_name'] . ' ' . $meta_user['first_name'];
        }

        return trim($name);

    }


    /**
     * @return mixed|void
     * @version 1.2
     */
    private function get_user_order_address($order)
    {
        $fields = apply_filters('wms_new_user_action', array('postcode', 'state', 'city', 'address_1', 'address_2'), $order, $this);

        $address = array();

        foreach ($fields as $field) {
            $value = '';
            if (method_exists($order, $field)) {
                $value = $order->{$field}();
            } elseif (method_exists($order, 'get_billing_' . $field)) {
                $callback = 'get_billing_' . $field;
                $value = $order->{$callback}();
            } elseif ($order->get_meta($field)) {
                $value = $order->get_meta($field);
            } elseif (function_exists($field)) {
                $value = call_user_func($field, [$order, $this]);
            }

            if ($value) {
                $address[] = trim($value, '  \t\n\r\0\x0B');
            }
        }

        if (!$address) {
            return '';
        }

        return implode(', ', $address);
    }


    /**
     * @param $order
     * @return mixed
     */
    public function set_shipment_address($order)
    {
        return $this->customerorder['shipmentAddress']
            = apply_filters(
            'wms_order_shipment_address_action',
            $this->get_user_order_address($order),
            $order
        );
    }

    /**
     * @return mixed|void
     * @version 1.1.1
     */
    private function get_counterparty_tags()
    {
        if (isset($this->settings['wms_order_counterparty_tags'])) {
            return $this->settings['wms_order_counterparty_tags'];
        }
    }


    /**
     * @param $order
     *
     * @return mixed|void
     * @throws Exception
     * @version  1.0.9
     *
     */
    private function get_counterparty_id($order)
    {
        $counterparty = new WmsCounterpartyApi();

        $counterparty->set_user($order->get_user());
        $email = $this->get_user_order_email($order);

        if (!$name = $this->get_user_order_name($order)) {
            $name = $email;
        }

        $address = $this->get_user_order_address($order);


        $array = array('name' => $name, 'email' => $email, 'phone' => $order->get_billing_phone(), 'address' => $address, 'tags' => $this->get_counterparty_tags());

        $array = apply_filters('wms_new_user_action', $array, $order);

        $counterparty->set_service($data['order'] = $order);

        $counterparty->counterparty($array);

        return $counterparty->get_ms_id();
    }


    /**
     * @param $item
     *
     * @return mixed|void
     * @throws Exception
     * @version 1.0.4
     */
    private function get_position($item)
    {
        if (!$item->is_type('line_item')) {
            return false;
        }

        $item_product = $item->get_product();
        if (!$item_product) {
            return false;
        }
        $product_uuid = apply_filters('wms_product_order_position_ms_uuid', $item_product->get_meta('_id_ms'), $item);

        // Fallback: если _id_ms пустой/битый, пробуем найти ассортимент в МС по SKU.
        if (!$product_uuid || !wp_is_uuid($product_uuid)) {
            $sku = (string)$item_product->get_sku();
            if (!empty($sku)) {
                $found_uuid = $this->search_position($sku);
                if ($found_uuid && wp_is_uuid($found_uuid)) {
                    $product_uuid = $found_uuid;
                    // Кэшируем найденную связку, чтобы следующие заказы не теряли позицию.
                    update_post_meta($item_product->get_id(), '_id_ms', $found_uuid);
                }
            }
        }

        if (!$product_uuid || !wp_is_uuid($product_uuid)) {
            WmsLogs::set_logs(
                'Пропуск позиции: товар без _id_ms и без совпадения по SKU. Product ID: ' . $item_product->get_id() .
                ', SKU: ' . $item_product->get_sku(),
                true
            );
            return false;
        }

        $type = apply_filters('wms_product_order_position_ms_type', $item_product->get_meta('_ms_type'), $item);

        if (!$type) {
            $type = ($item_product->is_type('product_variation')) ? "variant" : "product";
        }

        $item_quantity = floatval($item->get_quantity());
        $item_total = apply_filters('wms_product_order_position_total', floatval($item->get_total()), $item);
        $item_price = apply_filters('wms_product_order_position_ms_price', ($item_total / $item_quantity), $item);

        $position = array(
            "quantity" => $item_quantity,
            "price" => intval($item_price * 100),
            "assortment" => array(
                "meta" => array(
                    "href" => WMS_URL_API_V2 . "entity/{$type}/{$product_uuid}",
                    "type" => $type,
                    "mediaType" => "application/json"
                )
            )
        );

        if (isset($this->settings['wms_order_reserv']) && $this->settings['wms_order_reserv'] == 'on') {
            $position['reserve'] = $item_quantity;
        }

        return $position = apply_filters('wms_product_order_position', $position, $item, $item_product->get_id());

    }


    /**
     * @param $item
     * @param $order
     * @return mixed|void
     */
    private function get_position_delivery($item, $order)
    {
        //массив позиций
        $position = array(
            "quantity" => 1,
            "price" => $order->get_shipping_total() * 100,
            "assortment" => array(
                "meta" => array(
                    "href" => WMS_URL_API_V2 . "entity/" . $item['type'] . "/" . $item['id'],
                    "type" => $item['type'],
                    "mediaType" => "application/json"
                )
            )
        );

        return $position = apply_filters('wms_product_order_position_delivery', $position, $item);

    }


    /**
     * @param string $sku
     *
     * @return bool
     * @throws Exception
     */
    private function search_position($sku = '')
    {
        $args = array();
        if (empty($sku)) {
            return false;
        }

        $args['filter'] = urlencode('search=' . $sku);

        $url = add_query_arg($args, apply_filters('wms_get_assortment_position_url', WMS_URL_API_V2 . 'entity/assortment/'));

        $assortment = WmsConnectApi::get_instance()->send_request($url);

        $count = count($assortment['rows']);

        if ($count > 1 or $count <= 0) {
            return false;
        } elseif (!isset($assortment['rows'][0]['id']) or empty($assortment['rows'][0]['id']) or $assortment['rows'][0]['id'] === false) {
            return false;
        }

        return $assortment['rows'][0]['id'];
    }

    /**
     * @param $order
     *
     * @param $order_ms
     * @return mixed|void
     * @throws Exception
     */
    private function set_positions($order, $order_ms)
    {
        //позиции заказа
        $order_line_items = $order->get_items();
        $positions = [];
        //перебираем
        foreach ($order_line_items as $item) {

            if ($position = $this->get_position($item)) {
                $positions[] = $position;
            }


        }

        if (isset($this->settings['wms_order_delivery_product']) and !empty($this->settings['wms_order_delivery_product'])) {
            if ($position = $this->get_position_delivery($this->settings['wms_order_delivery_product'], $order)) {
                $positions[] = $position;
            }
        }

        $this->positions = apply_filters('wms_order_product_action', $positions, $order);

        if (!$this->positions) {
            WmsLogs::set_logs('Ошибка при выгрузке позиций заказа: нет доступных позиций. Order ID: ' . $order->get_id(), true);
            return false;
        }

        $positions_ms = $this->post(str_replace('1.1', '1.2', $order_ms['meta']['href']) . '/positions', $this->positions, false);

        if (!$positions_ms) {
            WmsLogs::set_logs('Ошибка при выгрузке позиций заказа ' . $order->get_id(), true);
            return $positions_ms;
        }

        WmsLogs::set_logs('Позиции у заказа ' . $order->get_id() . ' успешно выгружены', true);

        return $positions_ms;

    }


    /**
     * @param $order
     *
     * @return mixed|void
     * @version  1.0.9
     *
     */
    private function set_comment($order)
    {

        $comment = 'Данные клиента: ' . strip_tags(str_replace('<br/>', ' ', $order->get_formatted_billing_address())) . PHP_EOL;
        $comment .= "Телефон: " . $order->get_billing_phone() . PHP_EOL;
        $comment .= "email: " . $order->get_billing_email() . PHP_EOL;
        $comment .= "Метод оплаты: " . $order->get_payment_method_title() . PHP_EOL;
        $comment .= "Метод доставки: " . $order->get_shipping_method() . PHP_EOL;
        $comment .= "Стоимость доставки: " . $order->get_shipping_total() . PHP_EOL;
        $comment .= "Комментарий заказа: " . $order->get_customer_note() . PHP_EOL;

        return $this->customerorder['description'] = apply_filters('wms_order_comment_action', $comment, $order);

    }

    /**
     * @param $order
     *
     * @return mixed
     */
    private function set_name($order)
    {

        if (isset($this->settings['wms_prefix'])) {
            $prefix = $this->settings['wms_prefix'];
        } else {
            $prefix = 'wc';
        }

        $postfix = '';
        if (isset($this->settings['wms_postfix'])) {
            $postfix = $this->settings['wms_postfix'];
        }

        return $this->customerorder['name'] = apply_filters('wms_order_action_name', $prefix . $order->get_order_number() . $postfix, $order);
    }


    /**
     * @param $organization
     *
     * @return array
     */
    private function set_organization($organization)
    {
        $organization_array = array(
            "meta" => array(
                "href" => WMS_URL_API_V2 . 'entity/organization/' . $organization,
                "type" => "organization",
                "mediaType" => "application/json"
            )
        );

        return $this->customerorder['organization'] = $organization_array;
    }


    /**
     * @param $order
     *
     * @return array
     */
    private function set_counterparty($order)
    {

        $counterparty = $this->get_counterparty_id($order);

        if ($counterparty == false) {
            $this->is_error = true;
            $this->error_message = 'Ошибка при получении контрагента';
        }

        $counterparty_array = array(
            "meta" => array(
                "href" => WMS_URL_API_V2 . 'entity/' . $counterparty,
                "type" => "counterparty",
                "mediaType" => "application/json"
            )
        );

        return $this->customerorder['agent'] = $counterparty_array;
    }


    /**
     * @param $order
     *
     * @param bool $sStateMsId
     * @return false|string[][]
     * @version  1.0.3
     */
    private function set_state($order, $sStateMsId = false)
    {
        $sStateMsId = ($sStateMsId) ? $sStateMsId : $this->get_state_id($order);

        if ($sStateMsId) {

            $state = array(
                "meta" => array(
                    "href" => WMS_URL_API_V2 . 'entity/customerorder/metadata/states/' . $sStateMsId,
                    "type" => "state",
                    "mediaType" => "application/json"
                )
            );

            return $this->customerorder['state'] = $state;

        }

        return false;
    }


    /**
     * @param $store
     *
     * @return array
     * @version  1.0.3
     *
     */
    private function set_store($store)
    {

        $store_array = array(
            "meta" => array(
                "href" => WMS_URL_API_V2 . 'entity/store/' . $store,
                "type" => "store",
                "mediaType" => "application/json"
            )
        );

        return $this->customerorder['store'] = $store_array;
    }


    /**
     * @param $customerorder
     * @param WC_Order $order_wc
     */
    protected function set_attribute($customerorder, $order_wc)
    {
        if (isset($customerorder['attributes'])) {
            $attributes = [];

            foreach ($customerorder['attributes'] as $attribute) {
                if (isset($attribute['id'])) {
                    $attribute['meta'] = [
                        "href" => WMS_URL_API_V2 . "entity/customerorder/metadata/attributes/" . $attribute['id'],
                        "type" => "attributemetadata",
                        "mediaType" => "application/json"
                    ];

                    unset($attribute['id']);
                }

                $attributes[] = $attribute;

            }

            $this->customerorder['attributes'] = $attributes;
        }
    }

    /**
     * @param $order
     * @throws Exception
     * @version  1.0.4
     *
     */
    public function update_order_wc($order)
    {
        $order_id = false;
        $uuid = false;
        $orders = $this->get_order_id_wc_to_ms($order['id']);

        if($orders){
            $this->order_wc = current($orders);
            $order_id = $this->order_wc->get_id();
            $uuid = $this->order_wc->get_meta('_ms_order_id');
        }

        do_action('wms_order_update_wc_before', $order_id, $order, $this->order_wc);

        if($uuid !== $order['id']){
            throw new Exception($order['id'] . 'UUID Мой склад не соответсвует ' . $order_id);
        }

        if ($order_id === false) {
            throw new Exception('Нет такого заказа ' . $order_id);
        }

        $this->set_id($order_id);
        $this->set_update_marker();

        $this->update_order_status($order_id, $order, $this->order_wc);


        if($this->is_update_wc){

            do_action('wms_order_update_wc', $order_id, $order, $this->order_wc);

            $this->is_update_wc = false;

            $this->order_wc->delete_meta_data('_ms_updated_marker');
            $this->order_wc->save();

        }


    }


    /**
     * @param $order_id
     * @param $order
     * @param WC_Order $order_wc
     * @throws Exception
     * @version  1.0.4
     */
    public function update_order_status($order_id, $order, WC_Order $order_wc)
    {
        if (!isset($this->settings['wms_states_wc_ms'][$order['state']['id']]['activate'])) {
            $order_wc->delete_meta_data('_ms_updated_marker');
            $order_wc->save();

            throw new  \Exception('no status activate');
        }

        $status = false;

        if ( $order['state']['id'] === $this->settings['wms_states_wc_completed']) {
            $status = 'wc-completed';
        } else  if ($order['state']['id'] === $this->settings['wms_states_wc_cancelled']) {
            $status = 'wc-cancelled';

        } else  if ($this->settings['wms_states_wc_ms'][$order['state']['id']]['activate'] === 'on'){
            $status = 'wc-' . $this->settings['wms_states_wc_ms'][$order['state']['id']]['label'];
        }

        if(!$status || $order_wc->get_status() === $status){
            return;
        }

        $this->is_update_wc = true;
        $order_wc->update_status($status);

        if (isset($this->settings['wms_email_send_state_order']) && $this->settings['wms_email_send_state_order'] === 'on') {

            $name = !empty($this->settings['wms_states_wc_ms'][$order['state']['id']]['name'])
                ? $this->settings['wms_states_wc_ms'][$order['state']['id']]['name']
                : $order['state']['name'];

            $name = apply_filters('wms_states_label_action', $name, 'wc-' . $this->settings['wms_states_wc_ms'][$order['state']['id']]['label']);

            $this->send_email($order_wc, __('Статус вашего заказа изменен на ' . $name, 'woocommerce-mysklad-sync'));

        }


    }


    /**
     * @param string $value
     *
     * @return array|mixed
     * @version  1.0.0
     *
     */
    public function set_cron_marker($value = 'yes')
    {
        $this->order_wc->update_meta_data('_ms_cron_marker', $value);
        return $this->order_wc->save();
    }

    /**
     * @version  1.0.1
     */
    public function set_update_marker()
    {
        $this->order_wc->update_meta_data('_ms_updated_marker', 'yes');
        return $this->order_wc->save();
    }

    /**
     * @version  1.0.1
     */
    public function is_update_marker()
    {
        return $this->order_wc->get_meta('_ms_updated_marker');
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    private function get_order_id_wc_to_ms($value)
    {
        return wc_get_orders(
            array(
                'wcs_ms_order_uuid' => sanitize_text_field($value),
            )
        );

    }


    /**
     * @param $order
     * @param string $subject
     */
    public function send_email($order, $subject = '')
    {
        $mailer = WC()->mailer();

        $email = $this->get_user_order_email($order);
        $content = $this->get_email_content($order, $subject, $mailer);
        $headers = "Content-Type: text/html\r\n";

        $mailer->send($email, $subject, $content, $headers);

    }

    /**
     * @param $order
     * @param bool $heading
     * @param $mailer
     *
     * @return string
     */
    public function get_email_content($order, $heading = false, $mailer = '')
    {

        $template = 'emails/customer-invoice.php';

        return wc_get_template_html($template, array(
            'order' => $order,
            'email_heading' => $heading,
            'sent_to_admin' => true,
            'plain_text' => false,
            'email' => $mailer,
        ));
    }


}
