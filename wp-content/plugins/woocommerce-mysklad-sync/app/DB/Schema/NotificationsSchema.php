<?php


namespace WCSTORES\WC\MS\DB\Schema;


class NotificationsSchema extends Schema
{

    /**
     * @var string
     */
    protected static $tableName = 'wcstores_woo_moysklad_notifications';

    /**
     * @return string
     */
    public static function getSchema()
    {
        return "
               CREATE TABLE " . static::tableName() . " (
               id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
               data longtext NOT NULL,
               status VARCHAR(20) NOT NULL default 'new',
               type VARCHAR(20) NOT NULL default 'plugin',
               create_time datetime NOT NULL default '0000-00-00 00:00:00',
               update_time datetime NOT NULL default '0000-00-00 00:00:00',  
               PRIMARY KEY  (id),
               UNIQUE KEY id (id)
               ) " . static::collate() . ";";

    }

}