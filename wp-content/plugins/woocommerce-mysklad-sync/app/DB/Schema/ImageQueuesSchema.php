<?php


namespace WCSTORES\WC\MS\DB\Schema;


/**
 * Class ImageQueuesSchema
 * @package WCSTORES\WC\MS\DB\Schema
 */
class ImageQueuesSchema extends Schema
{

    /**
     * @var string
     */
    protected static $tableName = 'wcstores_woo_moysklad_image_update_queues';

    /**
     * @return string
     */
    public static function getSchema()
    {
        return "
               CREATE TABLE " . static::tableName() . " (
               queue_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
               product_id BIGINT NOT NULL,
               data longtext NOT NULL,
               status VARCHAR(20) NOT NULL default 'pending',
               create_time datetime NOT NULL default '0000-00-00 00:00:00',
               update_time datetime NOT NULL default '0000-00-00 00:00:00',  
               PRIMARY KEY  (queue_id),
               UNIQUE KEY queue_id (queue_id)
               ) " . static::collate() . ";";

    }

}