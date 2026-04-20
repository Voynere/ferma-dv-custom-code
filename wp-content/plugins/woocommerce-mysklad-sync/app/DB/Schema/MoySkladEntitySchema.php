<?php


namespace WCSTORES\WC\MS\DB\Schema;


class MoySkladEntitySchema extends Schema
{

    protected static $tableName = 'wcstores_woo_moysklad_entities';

    /**
     * @return string
     */
    public static function getSchema()
    {
        return "
               CREATE TABLE " . static::tableName() . " (
               id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
               uuid VARCHAR(100) NOT NULL,
               data longtext NOT NULL,
               type VARCHAR(100) NOT NULL,
               create_time datetime NOT NULL default '0000-00-00 00:00:00',
               update_time datetime NOT NULL default '0000-00-00 00:00:00',  
               PRIMARY KEY  (id),
               UNIQUE KEY id (id),
               UNIQUE KEY uuid (uuid)
               ) " . static::collate() . ";";

    }

}