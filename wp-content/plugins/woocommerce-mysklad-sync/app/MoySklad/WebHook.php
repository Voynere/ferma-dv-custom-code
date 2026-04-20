<?php


namespace WCSTORES\WC\MS\MoySklad;


use WCSTORES\WC\MS\Wordpress\Actions\Filters;
use WCSTORES\WC\MS\Kernel\Config;
use WCSTORES\WC\MS\Wordpress\Rest\RestRoute;

/**
 * Class WebHook
 * @package WCSTORES\WC\MS\MoySklad
 */
class WebHook
{

    /**
     *
     */
    public function create()
    {
        try {
            $allowedWebHook = $this->getAllowedWebHook();
            foreach ($allowedWebHook as $type => $data) {
                foreach ($data['actions'] as $method) {
                    $result = \WmsConnectApi::get_instance()->send_request(
                        WMS_URL_API_V2 . '/entity/webhook',
                        'POST',
                        [
                            "url" => RestRoute::getUrlRoute("v1/webhook/$type/$method"),
                            "action" => $method,
                            "entityType" => $type
                        ]
                    );

                    if ($result and isset($result['errors'])) {
                        \WmsLogs::set_logs($result['errors'][0]['error'], true);
                    } elseif($result) {
                        \WmsLogs::set_logs(' Вебхук создан ' . $method . ' ' . $type, true);
                    }
                }

            }
        }catch (\Exception $e){
            \WmsLogs::set_logs($e->getMessage(), true);
        }

        \WmsCache::get_instance()->delete_cache('webhook');


    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function delete($uuid)
    {
        \WmsConnectApi::get_instance()->send_request(WMS_URL_API_V2 . '/entity/webhook/' . $uuid, 'DELETE', '');

        return \WmsCache::get_instance()->delete_cache('webhook');
    }

    /**
     * Удаляем хуки что созданы через сайт
     */
    public function deleteAll()
    {
        $hook = $this->get();

        foreach ($hook['rows'] as $key => $value) {
            $homeUrl = str_replace(['http://', 'https://', '/', 'www.'], '', home_url());

            if (strpos($value['url'], $homeUrl) !== false) {
                $this->delete($value['id']);
            }

        }
    }

    /**
     * @return mixed
     */
    public function get()
    {
        $cache = \WmsCache::get_instance()->get_cache('webhook');

        if ($cache == null) {
            $webhook = \WmsConnectApi::get_instance()->send_request(WMS_URL_API_V2 . '/entity/webhook');

            if (!isset($webhook['rows'])) {
                $webhook = ['rows' => []];
            }

            $cache = \WmsCache::get_instance()->save_cache('webhook', $webhook);
        }

        return $cache;
    }

    /**
     * @return mixed|void
     * @throws \Exception
     */
    public function getAllowedWebHook()
    {
        $allowedWebHook = Config::file('allowed-webhook');

        if($allowedWebHook){
            return Filters::apply('allowed_webhook',  $allowedWebHook);
        }
    }

}