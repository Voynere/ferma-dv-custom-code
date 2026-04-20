<?php


namespace WCSTORES\WC\MS\Kernel;


class Config
{

    /**
     * @param $key
     * @param $group
     * @return mixed|string
     * @throws \Exception
     */
    public static function item($key, $group)
    {
        $groupItems = static::file($group);

        return isset($groupItems[$key]) ? $groupItems[$key] : '';
    }

    /**
     * @param $group
     * @return mixed
     * @throws \Exception
     */
    public static function file($group)
    {
        $path = WMS_PATH . '/app/config/' . $group . '.php';

        if (file_exists($path)) {

            $items = include $path;

            if (is_array($items)) {
                return $items;
            } else {
                throw new \Exception('No array ' . $group);
            }

        } else {
            throw new \Exception('No file ' . $group);
        }
    }

}