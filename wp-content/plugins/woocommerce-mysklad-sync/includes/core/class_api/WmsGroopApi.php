<?php

use WCSTORES\WC\MS\MoySklad\ProductFolder;

/**
 * Created by PhpStorm.
 * User: aqw
 * Date: 16.01.2018
 * Time: 20:41
 */

class WmsGroopApi
{

    /**
     * @var
     */
    private $product_id;
    /**
     * @var
     */
    private $product_array;

    /**
     * @var null
     */
    private $cache = null;
    /**
     * @var WmsCache
     */
    private $cache_object;


    /**
     */
    function __construct()
    {
        $this->cache_object = WmsCache::get_instance();
        $this->cache = $this->cache_object->get_cache('groop');

    }

    /**
     * @param null $product_id
     * @param null $product_array
     * @return bool[]|false|int[]|object[]|string[]
     */
    function update_product_groop($product_id = null, $product_array = null)
    {
        $this->product_id = $product_id;
        $this->product_array = $product_array;
        $groop_id = $this->get_groop_id($product_array['productFolder']);

        if($groop_id and $groop_id > 0){
            return  [$groop_id];
        }

        return  false;
    }

    /**
     * @param $groop
     *
     * @return bool|int|null|object|string
     */
    function get_groop_id($groop)
    {
        $groop_id = $this->get_wc_groop_id($groop);
        if ($groop_id == false or $this->get_wc_groop_path_name() == false or $groop['updated'] !== $this->get_wc_groop_update($groop_id)) {
            $groop_id = $this->update_v2($groop, $groop_id);
        }

        return $groop_id;
    }


    /**
     * @param $groop
     *
     * @return bool
     */
    protected function get_wc_groop_id($groop)
    {
        global $wpdb;
        $term_id = apply_filters('wms_get_wc_groop_id',
            intval(
                $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT term_id 
                        FROM $wpdb->termmeta 
                        WHERE meta_key = %s 
                        AND meta_value = %s",
                        'ms_cat_id', $groop['id']
                    )))
            , $groop);

        if (isset($term_id) and !empty($term_id) and is_numeric($term_id)) {
            if(get_term((int)$term_id)){
                return $term_id;
            }
            delete_term_meta($term_id, 'ms_cat_id');
        }

        return false;


    }


    /**
     * @return bool
     */
    protected function get_wc_groop_path_name()
    {

        if ($this->product_id == null) {
            return true;
        }

        if ($this->product_array == null) {
            return true;
        }

        $pathName = get_post_meta($this->product_id, '_ms_pathName', true);

        if ($pathName == $this->product_array['pathName']) {
            return true;

        }


        return false;

    }


    /**
     * @param $id
     *
     * @return mixed
     */
    protected function get_wc_groop_update($id)
    {
        $date = get_term_meta($id, 'ms_cat_update', true);

        return $date;

    }


    /**
     * @param $url
     *
     * @return bool|mixed
     * @throws Exception
     */
    protected function get_groop_ms($url)
    {
        return WmsConnectApi::get_instance()->send_request($url);
    }


    /**
     * @return bool|mixed
     */
    public function get_groops()
    {
        return [ 'rows' => ProductFolder::make()->getByData(10000)];
    }

    public function get_groups_tree() : array
    {
        $groups = $this->get_groops();

        if (!is_array($groups['rows']) || empty($groups['rows'])) {
            return [];
        }

        return  WmsHelper::build_tree($groups['rows'], 'productFolder');

    }


    /**
     * @return bool|mixed
     */
    public function get_groops_array()
    {
        return $this->get_groops();

    }


    /**
     * @return bool|mixed
     */
    public function groops_merge($group, $group2)
    {

        foreach ($group2['rows'] as $key) {
            $group['rows'][] = $key;
        }

        return $group;
    }


    /**
     * @param $group
     *
     * @return bool|int|null|object|string
     */
    protected function get_parent_id($group)
    {
        $group_parent = ProductFolder::make()->getByUuid($group['meta']['href']);

        if ($group_parent !== false) {

            return apply_filters('wms_get_parent_id', $this->get_groop_id($group_parent), $group_parent);
        }

        return false;
    }



    /**
     * @param $group
     * @param null $id
     *
     * @return bool|int|null|object
     */
    private function update_v2($group, $id = null)
    {
        $name = $group['name'];
        $description = '';
        if (isset($group['description'])) {
            $description = $group['description'];
        }

        $parent = 0;

        if (isset($group['productFolder']['meta']['href'])) {
            $parent_id = $this->get_parent_id($group['productFolder']);
            if ($parent_id > 0) {
                $parent = $parent_id;
            } else {
                $parent = 0;
            }
        }

        if ($id > 0) {
            if (!isset($description) or empty($description)) {
                $term = get_term($id);
                $description = $term->description;
            }
        }

        $update = !empty ($id);
        $args = apply_filters('wms_product_cat_args', compact('name', 'parent', 'description'));

        if ($args === false) {
            return false;
        }

        if ($update) {
            $id = wp_update_term($id, $taxonomy = 'product_cat', $args);
        } else {
            $id = wp_insert_term($group['name'], $taxonomy = 'product_cat', $args);
        }

        if (is_wp_error($id)) {
            WmsLogs::set_logs($id->get_error_message(), true);
            WmsLogs::set_logs($group, true);
            return 0;
        }


        update_term_meta($id['term_id'], 'ms_cat_id', $group['id']);
        update_term_meta($id['term_id'], 'ms_cat_update', $group['updated']);
        return $id['term_id'];

    }


}