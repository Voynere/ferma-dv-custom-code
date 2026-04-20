<?php
/**
 * Created by PhpStorm.
 * User: aqw
 * Date: 05.01.2018
 * Time: 19:43
 */

class WmsAttributeApi
{

    /**
     * @var
     */
    private $product;

    /**
     * @var
     */
    private $product_id;

    /**
     * @var int
     */
    private $is_visible = 1;

    /**
     * @var int
     */
    private $is_taxonomy = 1;

    /**
     * @var int
     */
    private $is_variation = 0;

    /**
     * @var bool
     */
    private $append = false;

    /**
     * @var null
     */
    private $attributes_array = null;

    /**
     * @var mixed
     */
    private $product_attributes;

    private $prev_product_attributes;

    /**
     * WmsAttributeApi constructor.
     *
     * @param $product_id
     */
    public function __construct($product_id)
    {
        $this->product_id = $product_id;
        $product_attributes = get_post_meta($this->product_id, '_product_attributes', true);
        $this->product_attributes = (is_array($product_attributes)) ? $product_attributes: [];
        $this->prev_product_attributes = $this->product_attributes;


    }

    /**
     * @param $attribute
     *
     * @return mixed
     */
    private function get_value($attribute)
    {
        if (isset($attribute['type']) and $attribute['type'] == 'customentity') {
            return $attributes_value = $attribute['value']['name'];
        }

        return (string)$attribute['value'];

    }

    /**
     * @param $attribute
     *
     * @return mixed
     */
    public function set_attribute_variant($attribute)
    {
        $this->is_variation = 1;
        $this->append = true;

        foreach ( $this->product_attributes as $product_key => $product_attribute ) {
            if (isset($product_attribute['is_variation'])) {
                $this->product_attributes[$product_key]['is_variation'] = 0;
            }
        }

        $this->set_attributes($attribute);

        update_post_meta($this->product_id, '_product_attributes', $this->product_attributes, $this->prev_product_attributes);
    }


    /**
     * @param string $attributes
     * @return mixed
     */
    public function set_attributes($attributes = '')
    {
        $attributes = apply_filters('wms_attribute_action', $attributes, $this->product_id);
        $this->attributes_array = $attributes;

        foreach ($attributes as $attribute){
            if (isset($attribute['name']) and $attribute['name'] !== 'wms-no') {
                $this->attribute_update($this->product_id, $attribute['name'], $this->get_value($attribute));
            }
        }

        update_post_meta($this->product_id, '_product_attributes', $this->product_attributes, $this->prev_product_attributes);
        return $this->product_attributes;

    }

    /**
     * @param $product_id
     * @param $attribute_name
     * @param string $attribute_value
     *
     * @return mixed
     */
    private function attribute_update($product_id, $attribute_name, $attribute_value = '')
    {
        $attr_before =
            apply_filters('wms_attribute_before_update',
                array(
                    'name' => $attribute_name,
                    'label' => WmsHelper::get_attribute_label($attribute_name),
                    'value' => $attribute_value,
                    'is_visible' => $this->is_visible,
                    'is_variation' => $this->is_variation,
                    'is_taxonomy' => $this->is_taxonomy,
                ),
                $this->attributes_array,
                $product_id
            );


        if ($attr_before['is_taxonomy'] === 1) {
            $attr_name = $attr_before['label'];
            $attr_before['value'] = '';

            $attribute_name_pa = wc_attribute_taxonomy_name($attr_name);
            if (taxonomy_exists($attribute_name_pa) == false) {

                $return = $this->register_attribute($attr_before['name'], $attr_name);
                if (!$return) {
                    return false;
                }
            }
            wp_set_object_terms($product_id, WmsHelper::get_attribute_value($attr_name, $attribute_value), $attribute_name_pa, $this->append);
        } else {
            $attribute_name_pa = $attr_before['name'];
        }


        $this->product_attributes[sanitize_title($attribute_name_pa)] =
            apply_filters('wms_attribute_product_name_action',
                array(
                    'name' => $attribute_name_pa,
                    'value' => $attr_before['value'],
                    'is_visible' => $attr_before['is_visible'],
                    'is_variation' => $attr_before['is_variation'],
                    'is_taxonomy' => $attr_before['is_taxonomy'],
                ),
                $this->attributes_array,
                $product_id
            );

        //$this->product_attributes = array_merge($this->product_attributes, $product_attributes);

        return $this->product_attributes;
    }

    /**
     * @param $label
     *
     * @return bool
     */
    private function register_attribute($label, $attr_name)
    {
        global $wpdb;

        $permalinks = get_option('woocommerce_permalinks');

        $attribute = array('attribute_label' => wc_clean(stripslashes($label)), 'attribute_name' => wc_sanitize_taxonomy_name(wc_clean(stripslashes($attr_name))), 'attribute_type' => 'select', 'attribute_orderby' => '', 'attribute_public' => 0);

        $wpdb->insert($wpdb->prefix . 'woocommerce_attribute_taxonomies', $attribute);

        $name = wc_attribute_taxonomy_name($attribute['attribute_name']);
        $wc_product_attributes[$name] = (object)$attribute;

        $taxonomy_data = array('hierarchical' => true, 'update_count_callback' => '_update_post_term_count', 'labels' => array('name' => $label, 'singular_name' => $label, 'search_items' => sprintf(__('Search %s', 'woocommerce'), $label), 'all_items' => sprintf(__('All %s', 'woocommerce'), $label), 'parent_item' => sprintf(__('Parent %s', 'woocommerce'), $label), 'parent_item_colon' => sprintf(__('Parent %s:', 'woocommerce'), $label), 'edit_item' => sprintf(__('Edit %s', 'woocommerce'), $label), 'update_item' => sprintf(__('Update %s', 'woocommerce'), $label), 'add_new_item' => sprintf(__('Add New %s', 'woocommerce'), $label), 'new_item_name' => sprintf(__('New %s', 'woocommerce'), $label), 'not_found' => sprintf(__('No &quot;%s&quot; found', 'woocommerce'), $label),), 'show_ui' => true, 'show_in_quick_edit' => false, 'show_in_menu' => false, 'show_in_nav_menus' => true, 'meta_box_cb' => false, 'query_var' => true, 'sort' => false, 'public' => true, 'capabilities' => array('manage_terms' => 'manage_product_terms', 'edit_terms' => 'edit_product_terms', 'delete_terms' => 'delete_product_terms', 'assign_terms' => 'assign_product_terms',), 'rewrite' => array('slug' => empty($permalinks['attribute_base']) ? '' : trailingslashit($permalinks['attribute_base']) . sanitize_title($attribute['attribute_name']), 'with_front' => false, 'hierarchical' => true));

        $return = register_taxonomy($name, apply_filters("woocommerce_taxonomy_objects_{$name}", array('product')), apply_filters("woocommerce_taxonomy_args_{$name}", $taxonomy_data));
        // проверяем переменную на наличие ошибки
        if (is_wp_error($return)) {
            // выводим сообщение ошибки
            WmsLogs::set_logs($return->get_error_message(), true);
            WmsLogs::set_logs($label . ':' . $attr_name, true);
            $wpdb->delete($wpdb->prefix . 'woocommerce_attribute_taxonomies', array('ID' => $wpdb->insert_id));
            return false;
        }

        do_action('wms_attribute_added_action', $wpdb->insert_id, $attribute);
        flush_rewrite_rules();
        delete_transient('wc_attribute_taxonomies');
        WmsLogs::set_logs('Атрибут ' . $label . ' создан', true);
        return true;
    }


}

