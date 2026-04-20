<?php

namespace WCSTORES\WC\MS\Init\Tables;


use WCSTORES\WC\MS\DB\Schema\ImageQueuesSchema;
use WCSTORES\WC\MS\DB\Schema\MoySkladEntitySchema;

/**
 * Class ImageUpdatesQueues
 * @package WCSTORES\WC\MS\Init\Tables
 */
class TableUpdate
{
    public static function boot()
    {
        ImageQueuesSchema::createTable();
        MoySkladEntitySchema::createTable();
    }

}