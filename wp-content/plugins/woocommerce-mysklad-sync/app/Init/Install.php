<?php


namespace WCSTORES\WC\MS\Init;


class Install
{

    public static function init()
    {
        delete_transient( 'wms_cache' );
    }

}