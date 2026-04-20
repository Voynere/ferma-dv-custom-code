<?php


namespace WCSTORES\WC\MS\Facades;


/**
 * Class DB
 * @package WCSTORES\WC\MS\Facades
 */
class DB extends Facade
{

    /**
     * @return string|void
     */
    protected static function getFacadeAccessor()
    {
        return '\WCSTORES\WC\MS\DB\DB';
    }

}