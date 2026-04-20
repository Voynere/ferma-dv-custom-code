<?php

use WCSTORES\WC\MS\Wordpress\Actions\Filters;
use WCSTORES\WC\MS\Controller\Plugins\WpmlController;

Filters::add('wms_product_order_position_ms_uuid', [new WpmlController(), 'getOrderItemProductUuid'], 10, 2);
Filters::add('wms_product_order_position_total', [new WpmlController(), 'getOrderItemTotal'], 10, 2);

