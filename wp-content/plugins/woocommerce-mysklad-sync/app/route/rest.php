<?php

use WCSTORES\WC\MS\Controller\Kernel\FillingTablesWithDataMonitorController;
use WCSTORES\WC\MS\Controller\MoySklad\WebHookController;
use WCSTORES\WC\MS\Controller\Sync\AssortmentController;
use WCSTORES\WC\MS\Init\Tables\FillingTablesWithData;
use WCSTORES\WC\MS\Wordpress\Rest\RestRoute;


RestRoute::get('/test', function($oRequest){
    return ['OK'];
});

RestRoute::get('/webhook/create', [new WebHookController(), 'create'],'nonce');
RestRoute::get('/webhook/delete/(?P<uuid>.+)', [new WebHookController(), 'delete'],'nonce');
RestRoute::get('/webhook/delete', [new WebHookController(), 'deleteAll'],'nonce');
RestRoute::post('/webhook/(?P<type>.+)/(?P<action>.+)', [new WebHookController(), 'boot'],'nonce');



RestRoute::post('/products/sync', [new AssortmentController(), 'start'],'nonce');


RestRoute::get('/stock/sync', function (){
    do_action('wcstores_moysklad_queues_sync_stocks_updates');
    return ['ok'];
},'nonce');


RestRoute::get('/FillingTablesWithData/start', [new FillingTablesWithData(), 'boot'],'nonce');
RestRoute::get('/monitoring', [new FillingTablesWithDataMonitorController(), 'getDataMonitoring'],'nonce');