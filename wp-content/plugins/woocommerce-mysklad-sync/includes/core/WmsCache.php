<?php
/**
 * Created by PhpStorm.
 * User: aqw
 * Date: 24.01.2018
 * Time: 16:07
 */

class WmsCache
{
    /**
     * @var
     */
    private static $instance;
    /**
     * @var mixed|void|null
     */
    private $cache = null;
    /**
     * @var
     */
    private $cache_array;

    /**
     * WmsCache constructor.
     */
    private function __construct()
    {
        $this->cache = $this->get_all_cache();
    }

    /**
     * Возвращает экземпляр себя
     *
     * @return self
     */
    public static function get_instance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
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
     * @param $type
     * @return null
     */
    public function get_cache($type)
    {
        if (empty( $this->cache[$type] ) or !isset( $this->cache[$type] ) or $this->cache[$type] == null)
        {
            return null;
        }

        if (!is_array( $this->cache[$type] )) {
            return null;
        }

        return $this->cache[$type];

    }

    /**
     * @return mixed|null|void
     */
    public function get_all_cache()
    {
        $cache = get_transient( 'wms_cache' );
        if (!isset($cache) or $cache == false)
        {
            return null;
        }
        return $cache;
    }

    /**
     * @param $type
     * @param bool $data
     * @return bool|null
     */
    public function save_cache($type, $data = false)
    {
        if($data !== false)
        {
            if ($this->cache  == null) {
                $this->cache[$type] = $data;
            }

            if (is_array( $this->cache)) {
                $this->cache = array_merge( $this->cache, [$type => $data] );
            }
            set_transient( 'wms_cache', $this->cache, 24 * HOUR_IN_SECONDS );
            return  $data;
        }

        return $this->cache = null;

    }


    /**
     * @param $type
     * @return mixed|void|null
     */
    public function delete_cache($type)
    {
        $this->cache = $this->get_all_cache();

        if (isset($this->cache[$type])) {
            unset($this->cache[$type]);

            set_transient( 'wms_cache', $this->cache, 24 * HOUR_IN_SECONDS );
        }

        return $this->cache;

    }


}