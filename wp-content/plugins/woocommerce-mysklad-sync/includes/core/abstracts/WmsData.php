<?php


/**
 * Class WmsData
 */
abstract class WmsData
{

    /**
     * @version  1.0.0
     *
     */
    protected $id;
    /**
     * @version  1.0.0
     * @var array
     */
    protected $settings = array();

    /**
     * @version  1.0.0
     * @var null
     */
    protected $cache = null;

    /**
     * @version  1.0.0
     * @var array
     */
    protected $data = array();

    /**
     * @version  1.0.0
     * @var array
     */
    protected $meta = array();


    /**
     * @version  1.0.0
     * @param $cache
     * @return null
     */
    public function get_cache($cache){
		return $this->cache = WmsCache::get_instance()->get_cache( $cache );
	}

    /**
     * @version  1.0.0
     * @param $cache
     * @param $data
     * @return bool|null
     */
    public function save_cache($cache, $data){
		return  WmsCache::get_instance()->save_cache($cache, $data );
	}


    /**
     * @version  1.0.0
     * @return array
     */
    public function get_settings(){
		return $this->settings;
	}


    /**
     * @version  1.0.0
     * @param $settings
     * @return array|mixed|void
     */
    public function set_settings($settings){
		if (!is_array($settings)) {
			$settings = get_option($settings);
		}

		return $this->settings = $settings;
	}

    /**
     * @param mixed $id
     */
    public function set_id($id)
    {
        $this->id = $id;
    }


    /**
     * @version  1.0.0
     * @param $name
     * @param $value
     * @return mixed
     */
    protected function set_meta($name, $value){
		return $this->meta[$name] = $value;
	}


    /**
     * @version  1.0.0
     * @param $product_id
     */
    protected function save_meta()
    {
        foreach ($this->meta as $key => $value) {
            update_post_meta($this->id, $key, $value);

        }

        $meta = $this->meta;
        unset($this->meta);

        return $meta;

    }


    /**
     * @param $url
     * @return bool|mixed
     * @throws Exception
     * @version  1.0.0
     */
    public function get($url){
		return WmsConnectApi::get_instance()->send_request($url);
	}


    /**
     * @param $url
     * @param $data
     * @param bool $block
     * @return bool|mixed
     * @throws Exception
     * @version  1.0.0
     */
    public function post($url, $data, $block = true){
		return WmsConnectApi::get_instance()->send_request($url, 'POST', $data, $block);
	}


    /**
     * @param $url
     * @param $data
     * @param bool $block
     * @return bool|mixed
     * @throws Exception
     * @version  1.0.0
     */
    public function put($url, $data, $block = true){
		return WmsConnectApi::get_instance()->send_request($url, 'PUT', $data, $block);
	}


    /**
     * @param $url
     * @return bool|mixed
     * @throws Exception
     * @version  1.0.0
     */
    public function delete($url){
		return WmsConnectApi::get_instance()->send_request($url, 'DELETE');
	}



}