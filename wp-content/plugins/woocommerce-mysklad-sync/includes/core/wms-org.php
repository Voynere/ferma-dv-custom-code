<?php

use WCSTORES\WC\MS\MoySklad\Organization;

if (!defined('ABSPATH')) exit;

/**
 *
 */
class WmsOrg
{

    /**
     * @var null
     */
    private $cache = null;

    /**
     * @var array|mixed
     */
    private $organization = array();

    /**
     * @var null
     */
    private $settings = null;

    /**
     * @var WmsCache
     */
    private $cache_object;


    /**
     * WmsOrg constructor.
     */
    public function __construct()
    {
        $this->cache_object = WmsCache::get_instance();
        $this->cache = $this->cache_object->get_cache('org');
        $this->organization = $this->set_org_array();
    }


    /**
     * @return mixed
     */
    private function set_org_array()
    {
        return $this->organization = Organization::make()->getByData();

    }

    /**
     * @return bool|mixed
     */
    private function get_org_ms()
    {
        return WmsConnectApi::get_instance()->send_request(WMS_URL_API_V2 . '/entity/organization');
    }

    /**
     * @return mixed
     */
    public function get_organization()
    {

        return $this->organization;
    }


    /**
     * @return array
     */
    static public function get_org()
    {
        return (new static)->get_organization();
    }

}
