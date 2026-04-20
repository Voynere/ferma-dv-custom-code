<?php


namespace WCSTORES\WC\MS\Controller\Sync;


use WCSTORES\WC\MS\MoySklad\WebHook;
use WCSTORES\WC\MS\Queues\Queues;
use WCSTORES\WC\MS\WooCommerce\Utilities\QueueUtil;

/**
 * Class WebHookSyncController
 * @package WCSTORES\WC\MS\Controller\Sync
 */
class WebHookSyncController
{

    /**
     * @param $type
     * @param $action
     * @param $body
     * @throws \Exception
     */
    public function sync($type, $action, $body)
    {
        \WmsLogs::set_logs("Сработал веб хук для $type событие $action", true);
        $this->events($type, $action, $body);
    }

    /**
     * @param $type
     * @param $action
     * @param $events
     * @throws \Exception
     */
    protected function events($type, $action, $events)
    {
        $WebHook = new WebHook();
        $allowedWebHook = $WebHook->getAllowedWebHook();

        foreach ($events as $event) {
            if(isset($allowedWebHook[$type]['handler'])){
                $handlers = (is_array($allowedWebHook[$type]['handler'])) ? $allowedWebHook[$type]['handler'] : [$allowedWebHook[$type]['handler']];

                foreach ($handlers as $handler){

                     Queues::addAsync(
                        'webhook_handler',
                        [
                            'type' => $type,
                            'action' => $action,
                            'href' => $event['meta']['href'],
                            'handler' => $handler
                        ],
                        'webhook',
                         false,
                         1
                     );

                }

            }

        }
    }

    /**
     * @param $type
     * @param $action
     * @param $href
     * @param $handler
     */
    public function handler($type, $action, $href, $handler)
    {
        if (class_exists($handler)){
            $handler = new $handler();
            $handler->webhook($type, $action, $href);
        }
    }

}
