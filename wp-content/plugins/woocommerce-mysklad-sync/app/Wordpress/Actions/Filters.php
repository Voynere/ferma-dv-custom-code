<?php


namespace WCSTORES\WC\MS\Wordpress\Actions;


class Filters
{

    /**
     * @param $tag
     * @param $parameters
     * @return mixed|void
     */
    public static function apply($tag, ...$parameters)
    {
        return apply_filters(WCSTORES_PREFIX . $tag, ...$parameters);
    }

    /**
     * @param $tag
     * @param $callback
     * @param $priority
     * @param $countArgument
     * @return bool|true|void
     */
    public static function add($tag, $callback, $priority = 10, $countArgument = 1)
    {
        return add_filter(str_replace('%', WCSTORES_PREFIX, $tag), $callback, $priority, $countArgument);
    }

}