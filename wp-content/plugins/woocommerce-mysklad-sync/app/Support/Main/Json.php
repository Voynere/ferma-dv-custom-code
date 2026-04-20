<?php


namespace WCSTORES\WC\MS\Support\Main;


/**
 * Class Json
 * @package WCSTORES\WC\MS\Support\Main
 */
class Json
{
    /**
     * @param string $value
     * @return mixed
     */
    static public function decode($aValue = '')
    {
        return json_decode($aValue, true);
    }

    /**
     * @param string $value
     * @return false|string
     */
    static public function encode($sValue = '')
    {
        return json_encode($sValue, JSON_UNESCAPED_UNICODE);
    }

}