<?php


namespace WCSTORES\WC\MS\Facades;


class ImageUpdatesQueuesDataStore extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return '\WCSTORES\WC\MS\DB\DataStore\ImageUpdatesQueuesDataStore';
    }

}