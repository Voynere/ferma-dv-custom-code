<?php
if (!defined('ABSPATH')) exit;

/**
 * Class WmsConnectApi
 */
class WmsConnectApi
{
    /**
     * @var
     */
    private $password = false;
    /**
     * @var bool
     */
    private $login = false;
    /**
     * @var
     */
    private static $instance;


    /**
     * WmsConnectApi constructor.
     *
     */
    private function __construct()
    {
        $option = $this->get_connect_option();
        $this->set_api_password($option['wms_pass']);
        $this->set_api_login($option['wms_login']);
    }

    /**
     * Возвращает экземпляр себя
     *
     * @return self
     */
    public static function get_instance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    public function __clone()
    {
        throw new \Exception("Cannot clone a singleton.");
    }

    /**
     * @throws \Exception
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    /**
     * @param null $url
     * @param string $type GET POST PUT DELETE
     * @param null $databody
     * @param bool $block
     * @return bool|mixed
     * @throws Exception
     */
    public function send_request($url = null, $type = 'GET', $databody = null, $block = true)
    {
        if ($databody != null) {
            $databody = json_encode($databody, JSON_UNESCAPED_UNICODE);
        }

        $data = $this->get_connect_api($url, $type, $databody, $block);

        if ($data === false) {
            return false;
        }

        $data = json_decode($data, true);

        if (isset($data['errors'])) {
            WmsLogs::set_logs($data['errors'][0]['error'], 'critical');
            return false;
        }

        return $data;

    }


    /**
     * @return array
     */
    private function get_connect_option()
    {
        $option = get_option('wms_settings_auth');

        if (!is_array($option)) {
            $option = [
                'wms_pass' => '1',
                'wms_login' => '1@1.ru'
            ];
        }

        return $option;
    }


    /**
     * @param $url
     * @param string $type GET, POST, PUT, DELETE
     * @param null $databody
     * @param bool $block
     * @param int $redirection
     * @param bool $is_auth
     * @return mixed
     * @throws Exception
     */
    public function get_connect_api($url, $type = 'GET', $databody = null, $block = true, $redirection = 5, $is_auth = true)
    {
        $curlConnectTime = apply_filters('wcstores_ms_check_curl_connect_time', MINUTE_IN_SECONDS * 3);

        if (apply_filters('wcstores_ms_is_block_connect', $block) && !$this->is_timeout($url, $curlConnectTime)) {
            return false;
        }

        $url = $this->clearUrl($url);
        $headers = $this->get_connect_api_headers($is_auth);

        if ($headers === false) {
            return false;
        }

        $default_args = array(
            'method' => $type,
            'timeout' => apply_filters('wms_get_connect_api_timeout', 120),
            'headers' => $headers,
            'decompress' => true,
            'sslverify' => false,
            'redirection' => $redirection
        );

        if ($type !== 'GET') {
            $default_args['body'] = $databody;
        }

        $response = wp_remote_request($url, $default_args);

        do_action('wms_get_curl_info', $response);

        if (is_wp_error($response)) {

            set_transient('wcstores_ms_check_curl_connect', time(), $curlConnectTime);
            throw new \Exception('Попытка подключения к ' . $url . ' провалилась из за ошибок ' . 'Ошибка curl: ' . $response->get_error_message());

        } elseif (wp_remote_retrieve_response_code($response) > 300 && wp_remote_retrieve_response_code($response) < 399) {

            $response_headers = wp_remote_retrieve_headers($response);

            if(isset($response_headers['location'])){
                return  $response_headers['location'];
            }

            return false;

        }elseif (wp_remote_retrieve_response_code($response) > 299) {

            WmsLogs::set_logs([
               'url' => $url,
               'args' => $default_args,
               'body' => wp_remote_retrieve_body($response)
            ], 'error');

            return false;
        }

        delete_transient('wcstores_ms_check_curl_connect');

        return wp_remote_retrieve_body($response);

    }

    /**
     * @param $url
     * @param $curlConnectTime
     * @return false
     */
    private function is_timeout($url, $curlConnectTime)
    {
        if (!$timeoutError = get_transient('wcstores_ms_check_curl_connect')) {
            return true;
        }

        $timeHasPassed = time() - $timeoutError;

        if ($timeHasPassed % 5 == 0) {
            WmsLogs::set_logs('Попытка подключения к ' . $url . ' Плодключения временно заблокированы из за таймаута, подождите');
        }

        return false;

    }

    /**
     * @param $url
     * @return bool|string
     * @throws Exception
     */
    public function download_image($url)
    {
        if($response = $this->get_connect_api($url, 'GET',  null, false, 0, true)){
            return $this->get_connect_api($response, 'GET',  null, false, 0, false);
        }

        return $response;
    }


    /**
     * @param $url
     * @return string|string[]
     */
    private function clearUrl($url)
    {

	    $aReplace = array(
		    '/1.1/' => '/1.2/',
		    '/1.2//' => '/1.2/',
		    '/1.2///' => '/1.2/',
		    'online.moysklad.ru' => 'api.moysklad.ru',
	    );

        foreach ($aReplace as $key => $value) {
            $url = str_replace($key, $value, $url);
        }

        return apply_filters('wcstores_ms_connect_url', $url);

    }


    /**
     * @param bool $is_auth
     * @return array|false
     */
    private function get_connect_api_headers($is_auth = true)
    {
        if(!$is_auth){
            return array();
        }

        if ($this->login === false or $this->password === false) {
            return false;
        }

        return array(
            "Authorization" => "Basic " . base64_encode($this->login . ":" . $this->password),
            "Content-Type" => "application/json",
        );

    }

    /**
     * @param $password
     */
    function set_api_password($password)
    {
        if (trim($password)) {
            $this->password = $password;
        }


    }

    /**
     * @param $login
     */
    function set_api_login($login)
    {
        if (trim($login)) {
            $this->login = $login;
        }
    }

    /**
     * @param mixed $url
     */
    public function set_url($url)
    {
        $this->url = $url;
    }


}
