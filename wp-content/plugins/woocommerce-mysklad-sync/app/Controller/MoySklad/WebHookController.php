<?php


namespace WCSTORES\WC\MS\Controller\MoySklad;


use WCSTORES\WC\MS\MoySklad\WebHook;
use WCSTORES\WC\MS\Queues\Queues;
use WCSTORES\WC\MS\WooCommerce\Utilities\QueueUtil;

/**
 * Class WebHookController
 * @package WCSTORES\WC\MS\Controller\MoySklad
 */
class WebHookController
{

    /**
     * @param $oRequest
     * @return string[]
     */
    public function create($oRequest)
    {
        $WebHook = new  WebHook();
        $WebHook->create();
        return ['ok' => 'the request has been processed, see the info in the log'];
    }

    /**
     * @param $oRequest
     * @return mixed|string[]
     */
    public function delete($oRequest)
    {
        if ($uuid = $oRequest->get_param('uuid')) {
            $WebHook = new  WebHook();
            $WebHook->delete($uuid);
        }


    }

    /**
     * @param $oRequest
     * @return string[]
     */
    public function deleteAll($oRequest)
    {
        $WebHook = new  WebHook();
        $WebHook->deleteAll();
        return ['ok' => 'the request has been processed, see the info in the log'];

    }

    /**
     * @param $oRequest
     * @return string[]
     * @throws \Exception
     */
    public function boot($oRequest)
    {
        $body = json_decode($oRequest->get_body(), true);
        $eventsChunk = array_chunk($body['events'], 5);

        foreach ($eventsChunk as $events) {
            Queues::addAsync(
                'webhook',
                [
                    'type' => $oRequest->get_param('type'),
                    'action' => $oRequest->get_param('action'),
                    'events' => $events
                ],
                'webhook',
                false,
                0
            );

        }

        return ['ok'];
    }

}