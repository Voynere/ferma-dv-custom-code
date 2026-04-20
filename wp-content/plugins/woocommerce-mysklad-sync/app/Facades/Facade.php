<?php


namespace WCSTORES\WC\MS\Facades;


/**
 * Class Facade
 * @package WCSTORES\WC\MS\Facades
 */
abstract class Facade
{

    /**
     * @return mixed
     * @throws \Exception
     */
    public static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    /**
     * @throws \Exception
     */
    protected static function getFacadeAccessor()
    {
        throw new \Exception('Facade does not implement getFacadeAccessor method.');
    }

    /**
     * @param $name
     * @return mixed
     */
    protected static function resolveFacadeInstance($name)
    {
        if (class_exists($name)) {
            return (method_exists($name, 'getInstance') ) ?  $name::getInstance() : new $name();
        }
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     * @throws \Exception
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();

        if (!$instance) {
            throw new \Exception('A facade root has not been set.');
        }

        if(!method_exists($instance, $method)){
            throw new \Exception('not method ' . $method);
        }

        return ($args) ? $instance->$method(...$args) : $instance->$method();
    }

}