<?php


namespace WCSTORES\WC\MS\Queues;


/**
 * Class Queues
 * @package WCSTORES\WC\MS\Queues
 */
class Queues
{


    /**
     * @param $callback
     * @param array $parameters
     * @return false|mixed
     * @throws \Exception
     */
    public static function initializedQueuesFunction($callback, ...$parameters)
    {
        if (!function_exists($callback)) {
            throw new \Exception($callback . ': update wordpress and woocommerce to the latest version');
        }

        return call_user_func($callback, ...$parameters);
    }

    /**
     * @param $tag
     * @param array $parameters
     * @param string $group
     * @param bool $unique
     * @param int $priority
     * @return int|void
     * @throws \Exception
     */
    public static function addAsync($tag, array $parameters = [], string $group = 'queues', $unique = false, $priority = 5)
    {
        return self::initializedQueuesFunction(
            'as_enqueue_async_action',
            WCSTORES_PREFIX . 'queues_' . $tag,
            $parameters,
            WCSTORES_PREFIX . $group,
            $unique,
            $priority

        );
    }


    /**
     * @param $timestamp
     * @param $tag
     * @param array $parameters
     * @param string $group
     * @return int
     * @throws \Exception
     */
    public static function addSingle($timestamp, $tag, array $parameters = [], string $group = 'queues', $unique = false, $priority = 5)
    {
        return self::initializedQueuesFunction(
            'as_schedule_single_action',
            $timestamp,
            WCSTORES_PREFIX . 'queues_' . $tag,
            $parameters,
            WCSTORES_PREFIX . $group,
            $unique,
            $priority

        );
    }


    /**
     * @param $timestamp
     * @param $interval_in_seconds
     * @param $tag
     * @param $parameters
     * @param string $group
     * @return int
     * @throws \Exception
     */
    public static function addRecurring($timestamp, $interval_in_seconds, $tag, $parameters = [], $group = 'queues')
    {
        return self::initializedQueuesFunction(
            'as_schedule_recurring_action',
            $timestamp,
            $interval_in_seconds,
            WCSTORES_PREFIX . 'queues_' . $tag,
            $parameters,
            WCSTORES_PREFIX . $group

        );

    }

    /**
     * @param $tag
     * @param $parameters
     * @param $group
     * @return mixed
     * @throws \Exception
     */
    public static function has($tag, $parameters = null, $group = '')
    {
        return self::initializedQueuesFunction(
            'as_has_scheduled_action',
            WCSTORES_PREFIX . 'queues_' . $tag,
            $parameters,
            WCSTORES_PREFIX . $group

        );

    }

    /**
     * @param $tag
     * @param $parameters
     * @param string $group
     * @return false|mixed
     * @throws \Exception
     */
    public static function unscheduleAllActions($tag, $parameters = [], $group = 'queues')
    {
        return self::initializedQueuesFunction(
            'as_unschedule_all_actions',
            WCSTORES_PREFIX . 'queues_' . $tag,
            $parameters,
            WCSTORES_PREFIX . $group

        );

    }


    /**
     * @param $args
     * @param $return_format
     * @return mixed
     * @throws \Exception
     */
    public static function get($args, $return_format = 'OBJECT')
    {
        return self::initializedQueuesFunction(
            'as_get_scheduled_actions',
            $args,
            $return_format

        );

    }

    /**
     * @param $tag
     * @return int
     * @throws \Exception
     */
    public static function getQueuesCountToStatusPending($tag)
    {
        $ids = self::get([
            'hook' => WCSTORES_PREFIX . 'queues_' . $tag,
            'status' => 'pending',
            'per_page' => 10000
        ], 'ids');

        return count($ids);
    }


}
