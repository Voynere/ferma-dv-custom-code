<?php


namespace WCSTORES\WC\MS\Init;

use WCSTORES\WC\MS\Init\Queues\CheckingForImageUpdatesQueues;
use WCSTORES\WC\MS\Init\Tables\FillingTablesWithData;
use WCSTORES\WC\MS\Init\Tables\TableUpdate;

/**
 * Class Init
 * @package WCSTORES\WC\MS\Init
 */
class Init
{


    /**
     *
     */
    public function boot()
    {
        $this->initQueues();

        if (($check = get_transient('wcstores_ms_check_' . md5(WCSTORES_MS_VERSION))) != 'completed'){
            delete_transient( 'wms_cache' );
            $this->initTables();
            $this->fillingTablesWithData();
            set_transient( 'wcstores_ms_check_' . md5(WCSTORES_MS_VERSION), 'completed', DAY_IN_SECONDS  );
        }

    }


    /**
     *
     */
    public function initQueues()
    {
        return CheckingForImageUpdatesQueues::boot();
    }

    /**
     *
     */
    public function initTables()
    {
        return TableUpdate::boot();
    }

    /**
     *
     */
    public function fillingTablesWithData()
    {
        return FillingTablesWithData::boot();
    }

}