<?php
/**
 * Created by PhpStorm.
 * User: aqw
 * Date: 10.04.2018
 * Time: 21:44
 * @property mixed|void limit
 */

class WmsCounterpartyController
{

    /**
     * @version 1.0.3
     * @var mixed|void
     */
    private $limit;
    /**
     * @version 1.0.3
     * @var mixed|null|void
     */
    private $settings = null;

    /**
     * @version 1.1.1
     * WmsCounterpartyController constructor.
     */
    public function __construct()
    {
        $this->settings = get_option('wms_settings_counterparty');

        $limit = 50;
        if (isset($this->settings['wms_counterparty_limit']) and $this->settings['wms_counterparty_limit'] > 5) {
            $limit = $this->settings['wms_counterparty_limit'];
        }
        $this->limit = apply_filters('wms_counterparty_product', $limit);

        if (isset($this->settings['wms_load_price_counterparty']) and $this->settings['wms_load_price_counterparty'] == 'on') {
            add_action('user_register', array($this, 'register_user_price'));
            add_filter('woocommerce_product_get_price', array($this, 'price_product_html'), 10, 2);
            add_filter('woocommerce_product_variation_get_price', array($this, 'price_product_html'), 10, 2);
            add_filter('woocommerce_available_variation', array($this, 'woocommerce_variable_price_html'), 100, 3);
            //add_filter( 'woocommerce_product_variation_price_html', array('WmsCounterparty', 'wms_woocommerce_variable_price_html' ), 100, 2);
        }
        if (isset($this->settings['wms_load_register_counterparty']) and $this->settings['wms_load_register_counterparty'] == 'on') {
            add_action('user_register', array($this, 'register_user'));
        }
        if (wp_doing_ajax()) {
            add_action('wp_ajax_wms-load-start-counterparty-syn', array($this, 'start_counterparty_syn'));
            add_action('wp_ajax_nopriv_wms-load-start-counterparty-syn', array($this, 'start_counterparty_syn'));
            add_action('wp_ajax_wms_counterparty_load_loop', array($this, 'sync'));
            add_action('wp_ajax_nopriv_wms_counterparty_load_loop', array($this, 'sync'));
        }

        add_action('wms_walker_hook_counterparty', array($this, 'sync'));


    }


    /**
     * @version 1.0.3
     *
     * @param $value
     * @param $object
     * @param $variation
     *
     * @return mixed
     */
    public function woocommerce_variable_price_html($value, $object, $variation)
    {
        $cur_user_id = get_current_user_id();
        $option = get_user_meta($cur_user_id, 'wms_price', true);
        if (empty($option) or $option == false) {
            return $value;
        }
        if ($value['price_html'] == '') {

            $value['price_html'] = '<span class="price">' . $variation->get_price_html() . '</span>';

        }

        return $value;
    }


    /**
     * @version 1.0.3
     *
     * @param $price_html
     * @param $product
     *
     * @return mixed
     */
    public function price_product_html($price_html, $product)
    {
        $cur_user_id = get_current_user_id();
        $option = get_user_meta($cur_user_id, 'wms_price', true);
        if (empty($option) or $option == false) {
            return $price_html;
        }
        $product_id = isset($product->variation_id) ? $product->variation_id : $product->get_id();

        $sale_price = get_post_meta($product_id, '_sale_price', true);
        if (!empty($sale_price)) {
            return $price_html;
        }

        $unit_price = get_post_meta($product_id, $option['id'], true);
        if (!empty($unit_price)) {
            $price_html = $unit_price;
        }
        return $price_html;
    }


    /**
     * @param string $user_id
     * @throws Exception
     * @version 1.1.1
     *
     */
    public function register_user($user_id = '')
    {
        $user = get_user_by('ID', $user_id);

        $counterparty = new WmsCounterpartyApi();
        $resultSearch = $counterparty->search_counterparty(array('email' => $user->user_email));

        if ($resultSearch['meta']['size'] > 0) {
            return;
        }

        $name = $this->get_user_name($user_id, $user);;
        $email = isset($user->user_email) ? $user->user_email : '';


        $array = array('name' => $name, 'phone' => '', 'address' => '', 'email' => $email, 'tags' => $this->get_counterparty_tags());

        $array = apply_filters('wms_new_register_user', $array, $user_id);

        $counterparty->counterparty($array);

    }

    /**
     * @version 1.1.1
     * @return mixed|void
     */
    private function get_user_name($user_id, $user = '')
    {
        if ($user_id) {
            $meta_user = array_map(function ($a) {
                return $a[0];
            }, get_user_meta($user_id));
            $name = trim($meta_user['last_name'] . ' ' . $meta_user['first_name']);
        }
        if (!$name) {
            $name = isset($user->user_nicename) ? $user->user_nicename : $user->user_email;
        }

        return trim($name);

    }

    /**
     * @version 1.1.1
     * @return mixed|void
     */
    private function get_counterparty_tags()
    {
        if (isset($this->settings['wms_counterparty_tags'])) {
            return $this->settings['wms_counterparty_tags'];
        }
    }


    /**
     * @version 1.1.1
     *
     * @param string $user_id
     */
    public function register_user_price($user_id = '')
    {

        $option = get_option('wms_price_product_dop');
        if (empty($option) or $option == false) {
            return;
        }

        $user = get_user_by('ID', $user_id);

        $counterparty = new WmsCounterpartyApi();
        $resultSearch = $counterparty->search_counterparty(array('email' => $user->user_email));

        if ($resultSearch['meta']['size'] == 0) {
            return;
        }

        foreach ($option as $key => $value) {
            if ($value['type'] == $resultSearch['rows'][0]['priceType']) {
                // Вернет false, если предыдущее значение совпадает с $resultSearch['priceType'].
                if (update_user_meta($user_id, 'wms_price', array('id' => $value['id'], 'type' => $resultSearch['rows'][0]['priceType']))) {
                    WmsLogs::set_logs('Контрагенту email ' . $user->user_email . ' установлена цена ' . $resultSearch['rows'][0]['priceType'], true);

                }
            }
        }
    }


    /**
     * @version 1.0.3
     *
     */
    public function start_counterparty_syn()
    {
        if (WmsWalkerFactory::get_walker('counterparty')->get_start_walker()) {
            WmsLogs::set_logs('Уже идет синхронизация контрагентов', true);
            return;
        }

        WmsWalkerFactory::get_walker('counterparty')->delete_walker();
        WmsWalkerFactory::get_walker('counterparty')->cron_init();
        WmsWalkerFactory::get_walker('counterparty')->start_walker();

        WmsLogs::set_logs('Стартуем (синхронизация контрагентов)', true);
        update_option('wms_counterparty_update_start_time', current_time("Y-m-d H:i:s"));
        update_option('wms_counterparty_update_start', array('load' => 'start', 'message' => 'Начало полной синхронизации...'));
        $this->sync();
    }


    /**
     * @version 1.0.3
     * @return bool
     */
    public function sync()
    {
        $offset = get_transient('wms_offset_counterparty');
        $offset = $offset === false ? 0 : $offset;

        WmsWalkerFactory::get_walker('counterparty')->start_loop($offset);

        $counterparty = $this->get_counterparty($offset);

        $count = count($counterparty['rows']);
        foreach ($counterparty['rows'] as $key => $value) {
            try {
                if (isset($value['email'])) {
                    $user = get_user_by('email', $value['email']);
                    if ($user !== false) {
                        update_user_meta($user->ID, '_ms_user_id', $value['id']);
                        $this->update_price($user, $value);
                        do_action('wms_counterparty_sync', $user->ID, $value);
                    }
                }
            }
                //Перехватываем (catch) исключение, если что-то идет не так.
            catch (Exception $ex) {
                WmsLogs::set_logs($ex->getMessage(), true);

            }
        }

        if ($count < $this->limit or empty($counterparty['rows'])) {
            $count_product = $offset + $count;
            WmsLogs::set_logs('Синхронизация закончилась ' . $count_product . ' конрагентов было синхронизировано', true);
            update_option('wms_counterparty_update_start', array('load' => 'stop', 'size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Полная синхронизация'));

            WmsWalkerFactory::get_walker('counterparty')->delete_walker();

            do_action('wms_counterparty_end_sync');
            wp_die();
        }

        update_option('wms_counterparty_update_start', array('size' => $counterparty['meta']['size'], "count" => $offset, "time" => 0, 'load' => 'load'));

        if (isset($this->settings['wms_counterparty_type_load']) and $this->settings['wms_counterparty_type_load'] == 'speed') {
            WmsWalkerFactory::get_walker('counterparty')->end_loop($offset + $this->limit, true);
            WmsHelper::wms_ajax('admin-ajax.php?action=wms_counterparty_load_loop');
            wp_die();
        }

        WmsWalkerFactory::get_walker('counterparty')->end_loop($offset + $this->limit);
        wp_die();
 }

    /**
     * @version 1.0.3
     *
     * @param $offset
     *
     * @return bool|mixed
     */
    private function get_counterparty($offset)
    {
        $args = apply_filters('wms_get_counterparty_url_args', [
            'offset' => $offset,
            'limit' => $this->limit,
        ]);

        if (isset($this->settings['wms_counterparty_variant_load']) and $this->settings['wms_counterparty_variant_load'] == 'updated') {
            $updated = get_option('wms_counterparty_update_start_time');
            if ($updated) {
                $args['filter'] = urlencode('updated>' . $updated);
            }
        }

        $url = add_query_arg($args, apply_filters('wms_get_counterparty_url', WMS_URL_API_V2 . 'entity/counterparty/'));

        $counterparty = WmsConnectApi::get_instance()->send_request($url);
        //echo '<pre>';
        //print_r($products);
        if ($counterparty === false) {
            WmsWalkerFactory::get_walker('counterparty')->delete_walker();
            update_option('wms_counterparty_update_start', array('time' => 'Ошибка'));
            wp_die();
        }

        return $counterparty;

    }

    /**
     * @version 1.0.3
     *
     * @param $user
     * @param $value
     */
    public function update_price($user, $value)
    {
        $prices = get_option('wms_price_product_dop');
        if (empty($prices) or $prices == false) {
            return;
        }
        foreach ($prices as $key => $price) {
            if ($price['type'] == $value['priceType']) {
                // Вернет false, если предыдущее значение совпадает с $resultSearch['priceType'].
                if (update_user_meta($user->ID, 'wms_price', array('id' => $price['id'], 'type' => $value['priceType']))) {
                    WmsLogs::set_logs('Контрагенту email ' . $user->user_email . ' установлена цена ' . $value['priceType'], true);
                    break;
                }
            }
            delete_user_meta($user->ID, 'wms_price');

        }

    }


}

new WmsCounterpartyController();