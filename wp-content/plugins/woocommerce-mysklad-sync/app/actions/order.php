<?php

use WCSTORES\WC\MS\Controller\Sync\OrderController;
use WCSTORES\WC\MS\Wordpress\Actions\Actions;
use WCSTORES\WC\MS\Wordpress\Actions\Filters;


Actions::add('woocommerce_checkout_order_processed', [new OrderController(), 'queueUpTasksByCreateOrder'],1000);
Actions::add('woocommerce_store_api_checkout_order_processed', [new OrderController(), 'queueUpTasksByCreateOrderStoreApi'],1000);
Actions::add('woocommerce_payment_complete', [new OrderController(), 'queueUpTasksByPaymentComplete'],1000);
Actions::add('woocommerce_order_payment_status_changed', [new OrderController(), 'queueUpTasksByPaymentComplete'],1000);
Actions::add('woocommerce_order_status_changed', array(new OrderController(), 'queueUpTasksByStatusChanged'), 1000);

Filters::add(
    'woocommerce_order_data_store_cpt_get_orders_query',
    [new WCSTORES\WC\MS\Controller\Sync\OrderController(), 'handleCustomQueryVarOrderUuid'],
    1000, 2
);
