<?php


class WmsWalker
{

    protected $type;

    protected $speed = false;

    function __construct($type = '')
    {
        $this->type = $type;

        add_action('wms_walker_' . $this->type, array($this, 'walker'));
        add_filter('cron_schedules', array($this, 'add_schedule'));


    }

    public function get_type()
    {
        return $this->type;
    }

    /**
     *
     */
    public function walker($type)
    {
        if ($end = get_transient('wms_time_end_loop_' . $type)) {
            delete_transient('wms_time_end_loop_' . $type);
            delete_transient('wms_walker_start_' . $type);
            if (get_transient('wms_loop_speed_' . $type)) {
                return;
            }

            do_action('wms_walker_hook_' . $type, $type);

            wp_die();

        }

        if ($end = get_transient('wms_time_start_sync_' . $type)) {
            do_action('wms_walker_hook_' . $type, $type);
            return;
        }

        $transient = get_transient('wms_time_start_' . $type);
        if ($transient === false) {
            delete_transient('wms_walker_start_' . $type);
            return;
        }

        $walker = get_transient('wms_walker_start_' . $type);

        if ($walker === false) {
            set_transient('wms_walker_start_' . $type, 5);
            return;
        }

        if ($walker == 1) {

            WmsLogs::set_logs('Похоже завис. Подготовка к старту', true);
            set_transient('wms_walker_start_' . $type, 'start');
            return;
        }

        if ($walker == 'start') {

            WmsLogs::set_logs('Cнова стартуем ' . $type, true);
            delete_transient('wms_walker_start_' . $type);
            do_action('wms_walker_hook_' . $type, $type);
            return;

        }

        WmsLogs::set_logs('Зависание до старта ' . $walker, true);
        set_transient('wms_walker_start_' . $type, $walker - 1);
        return;
    }

    /**
     * Cron shedule setup for 1 minute interval
     */
    function add_schedule($schedules)
    {
        $schedules['wms_walker_shedule'] = array(
            'interval' =>  apply_filters('wms_walker_shedule_interval',60),
            'display' => 'S30 sec'
        );

        return $schedules;
    }

    /**
     * Cron task restart
     */
    function cron_init()
    {
        if (!wp_next_scheduled('wms_walker_' . $this->type, [$this->type])) {
            wp_schedule_event(time(), 'wms_walker_shedule', 'wms_walker_' . $this->type, [$this->type]);
        }
    }

    /**
     *
     */
    function delete_walker($stop = null)
    {
        if($stop === null)
        {
            delete_transient('wms_offset_' . $this->type);
        }
        delete_transient('wms_time_start_' . $this->type);
        delete_transient('wms_time_end_loop_' . $this->type);
        delete_transient('wms_walker_start_' . $this->type);
        delete_transient('wms_time_start_walker_' . $this->type);
        delete_transient('wms_time_start_sync_' . $this->type);
        delete_transient('wms_loop_speed_' . $this->type);
        delete_transient('wms_sync_id_loop_' . $this->type);
        wp_clear_scheduled_hook('wms_walker_' . $this->type,[$this->type]);
    }


    function end_loop($offset = '', $speed = false)
    {
        delete_transient('wms_time_start_' . $this->type);
        delete_transient('wms_sync_id_loop_' . $this->type);
        set_transient('wms_offset_' . $this->type, $offset);
        set_transient('wms_time_end_loop_' . $this->type, time());

        if ($speed === false){
            delete_transient('wms_loop_speed_' . $this->type);
        }elseif ($speed === true){
            set_transient('wms_loop_speed_' . $this->type, time());
        }
    }

    function loop($id = '')
    {
        set_transient('wms_sync_id_loop_' . $this->type,$id);
    }

    function get_id_loop()
    {
        $transient = get_transient('wms_sync_id_loop_' . $this->type);
        if ($transient === false) {
            return false;
        }
        return $transient;
    }


    function start_loop($offset = '')
    {

        set_transient('wms_offset_' . $this->type, $offset);
        set_transient('wms_time_start_' . $this->type, time());
        delete_transient('wms_time_start_sync_' . $this->type);
    }

    function start_sync()
    {
        set_transient('wms_time_start_sync_' . $this->type, time());
    }


    function start_walker($start = null)
    {
        set_transient('wms_time_start_walker_' . $this->type, time());
        if($start == 'start') {
            do_action('wms_walker_hook_' . $this->type, $this->type);
        }

    }

    function get_start_walker()
    {
        return get_transient('wms_time_start_walker_' . $this->type);
    }

    function get_start_loop()
    {
        return  get_transient('wms_time_start_' . $this->type);
    }


}

class WmsWalkerFactory
{
    protected static $walker = array();

    public static function push_walker(WmsWalker $walker)
    {
        self::$walker[$walker->get_type()] = $walker;
    }

    public static function get_walker($type)
    {
        return isset(self::$walker[$type]) ? self::$walker[$type] : null;
    }

    public static function remove_walker($type)
    {
        if (array_key_exists($type, self::$walker))
        {
            unset(self::$walker[$type]);
        }
    }
}