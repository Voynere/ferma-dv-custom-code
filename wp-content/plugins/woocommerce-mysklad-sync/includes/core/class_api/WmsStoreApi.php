<?php

use WCSTORES\WC\MS\MoySklad\Store;

if (!defined( 'ABSPATH' )) exit;

class WmsStoreApi
{
    private $cache = null;
    private $cache_object;
    private $stores = array ();
    private static $instance;


    /**
     * WmsStoreApi constructor.
     */
    public function __construct()
    {
        $this->stores = $this->set_stores_array();
    }

    public static function get_instance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }



    /**
     * @return array
     */
    public function get_stores($all = true)
    {
        if($all === false)
        {
            unset($this->stores['all']);
            return $this->stores;
        }
        return $this->stores;

    }

    /**
     * @return bool|mixed
     */
    private function get_stores_ms_api()
    {        
        $limit = 100;
        $offset = 100;
        $url = apply_filters('wms_stores_url', WMS_URL_API_V2.'entity/store');
        $stores = WmsConnectApi::get_instance()->send_request($url . '?limit=' . $limit);
        
        if (!is_array($stores)) {
              return false;
          }
		
		   if( !isset($stores['rows'])){
			   return false;
		   }
           
        $count = count($stores['rows']);

        while ($count >= $limit) {
            $stores2 = WmsConnectApi::get_instance()->send_request($url . '?offset=' . $offset . '&limit=' . $limit);
            $count = count($stores2['rows']);
            if ($count == 0) {
                break;
            }
            $stores = $this->stores_merge($stores, $stores2);
            $offset = $offset + $limit;
        }

        return $stores;

    }
    
    
    /**
     * @return bool|mixed
     */
    public function stores_merge($stores, $stores2)
    {

        foreach ($stores2['rows'] as $key) {
            $stores['rows'][] = $key;
        }

        return $stores;
    }


    /**
     * @return array
     */
    private function set_stores_array()
    {
        return $this->stores = Store::make()->getByData();
    }


}
