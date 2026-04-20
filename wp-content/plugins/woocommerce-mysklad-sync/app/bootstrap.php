<?php


require WMS_PATH . '/vendor/autoload.php';

try {
    WCSTORES\WC\MS\Kernel\Kernel::getInstance()->boot();
}catch (Exception $e){
    WmsLogs::set_logs($e->getMessage());
}


