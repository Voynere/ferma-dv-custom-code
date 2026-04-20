<?php


namespace WCSTORES\WC\MS\Controller;


use WCSTORES\WC\Exception\ControllerException;
use WmsLogs;

/**
 * Class Controller
 * @package WCSTORES\WC\MS\Controller
 */
class Controller
{

    /**
     * @param string $sMessage
     * @param string $iObjectId
     * @throws ControllerException
     */
    protected function exception($sMessage = '', $iObjectId = '')
    {
        throw new  ControllerException('controller_invalid', $sMessage, 400, array('resource_id' => $iObjectId));
    }


    /**
     * @param $message
     */
    protected function log($message)
    {
        WmsLogs::set_logs($message, true);
    }

}