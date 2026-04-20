<?php


namespace WCSTORES\WC\MS\Kernel;


use WCSTORES\WC\MS\Support\Main\Props;

/**
 * Class Singleton
 * @package WCSTORES\WC\MS\Kernel
 */
class Singleton
{

    use Props;

    /**
     * @var array
     */
    protected static $instances = [];




    public function __construct()
    {

    }


    /**
     *
     */
    public function __clone()
    {
        throw new \Exception("Cannot clone a singleton.");
    }

    /**
     * @throws \Exception
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }


    /**
     * @return mixed|static
     */
    public static function getInstance()
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }





}