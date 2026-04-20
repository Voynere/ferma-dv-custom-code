<?php
if (!defined('ABSPATH')) exit;
if( ! defined('WP_UNINSTALL_PLUGIN') ) exit;

// проверка пройдена успешно. Начиная от сюда удаляем опции и все остальное.
delete_option( 'wms_settings_auth' );
delete_option( 'wms_settings_product' );
delete_option( 'wms_settings_stock' );
delete_option( 'wms_settings_order' );
