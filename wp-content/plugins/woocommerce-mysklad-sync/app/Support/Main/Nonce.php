<?php


namespace WCSTORES\WC\MS\Support\Main;


/**
 * Class Nonce
 * @package WCSTORES\WC\MS\Support\Main
 */
class Nonce
{
    /**
     * @param $oRequest
     * @return bool
     */
    public static function isValidate($oRequest)
    {
        $aParams = (is_object($oRequest)) ? $oRequest->get_query_params() : [];

        if(!isset($aParams['_nonce'])){
            return false;
        }

        if (!$option = static::getOption()) {
            return false;
        }

        if ($option != $aParams['_nonce']) {
            return false;
        }

        return true;

    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public static function get()
    {
        if ($option = static::getOption()) {
            return $option;
        }

        throw new \Exception("Not nonce");

    }

    /**
     * @return false|mixed
     */
    public static function getOption()
    {
        $option = get_option('wms_settings_auth');

        if (isset($option['wms_nonce']) and !empty($option['wms_nonce'])) {
            return $option['wms_nonce'];
        }

        return 123456;

    }
}