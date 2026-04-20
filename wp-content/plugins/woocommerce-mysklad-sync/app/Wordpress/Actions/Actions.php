<?php


namespace WCSTORES\WC\MS\Wordpress\Actions;


/**
 * Class Actions
 * @package WCS\WC\MS\WP\Actions
 */
class Actions
{

    /**
     * @param $tag
     * @param mixed ...$parameters
     * @return void
     */
    public static function do($tag, ...$parameters)
    {
        return do_action(WCSTORES_PREFIX . $tag, ...$parameters);
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
        return add_action(str_replace('%', WCSTORES_PREFIX, $tag), $callback, $priority, $countArgument);
    }


    /**
     * @param $tag
     * @param $callback
     * @param int $priority
     * @param int $countArgument
     * @return bool|true|void
     */
    public static function queues($tag, $callback, $priority = 10, $countArgument = 1)
    {
        return self::add(str_replace('%', '%queues_', $tag), $callback, $priority, $countArgument);
    }

}