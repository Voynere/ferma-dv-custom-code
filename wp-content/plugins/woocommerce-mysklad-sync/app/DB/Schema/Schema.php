<?php


namespace WCSTORES\WC\MS\DB\Schema;


use WCSTORES\WC\MS\Facades\DB;

/**
 * Class Schema
 * @package WCSTORES\WC\MS\DB\Schema
 */
class Schema
{
    /**
     * @var
     */
    protected static $tableName;

    /**
     * @return mixed
     */
    public static function prefix()
    {
        return DB::prefix();
    }

    /**
     * @return mixed
     */
    public static function collate()
    {
        return DB::collate();
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return static::prefix() . static::$tableName;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public static function createTable()
    {
        return DB::createTable(static::tableName(), static::getSchema());
    }

    /**
     * @return string
     * @throws \Exception
     */
    public static function getSchema()
    {
        throw new \Exception('Not Schema');
    }

}