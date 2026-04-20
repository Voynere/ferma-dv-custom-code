<?php
if (!defined('ABSPATH')) exit;

/**
 *
 */
class WmsHelper
{


    /**
     * @param string $value
     * @return mixed
     */
    static public function decode($value = '')
    {
        return json_decode($value, true);
    }

    /**
     * @param string $value
     * @return false|string
     */
    static public function encode($value = '')
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $value
     * @param $value_sync
     * @return mixed
     */
    static public function var_sync($value = '', $value_sync = '')
    {
        switch ($value) {
            case '_id_ms':
                $value_sync = $value_sync['id'];
                break;
            case '_sku':
                $value_sync = $value_sync['code'];
                break;
            case '_externalCode':
                $value_sync = $value_sync['externalCode'];
                break;
        }
        return $value_sync;
    }

    /**
     * @param string $ajax_query
     * @return array|WP_Error
     */
    static public function wms_ajax($ajax_query = '')
    {
        return wp_remote_get(
            admin_url($ajax_query . '&wms_nonce' . $GLOBALS['wms_nonce']),
            apply_filters(
                'wms_ajax_wp_remote_get_config',
                array('timeout' => 5, 'redirection' => 0, 'blocking' => false, 'sslverify' => false)
            )
        );
    }

    /**
     * @param $key
     * @param $code
     * @return mixed
     */
    static function get_product_id($key, $code)
    {
        $args = array(
            'post_type' => array('product', 'product_variation'),
            'meta_query' => array(
                array(
                    'key' => $key,
                    'value' => $code
                )
            )
        );

        $product_id = get_posts($args);

        if (empty($product_id[0]->ID)) {
            return false;
        } else {
            return $product_id[0]->ID;
        }
    }


    /**
     * @param $products
     * @param $wms_product_settings
     * @return false|mixed
     */
    static function get_product_ids($products, $wms_product_settings)
    {
        return WmsHelper::get_product_id(
            $wms_product_settings['wms_product_select_var'],
            WmsHelper::var_sync($wms_product_settings['wms_product_select_var'], $products)
        );
    }


    /**
     * @param $s
     * @return string|string[]
     */
    static function translit($s)
    {
        $s = strip_tags($s); // убираем HTML-теги
        $s = str_replace(array("\n", "\r"), " ", $s); // убираем перевод каретки
        $s = trim($s); // убираем пробелы в начале и конце строки
        $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
        $s = strtr($s, array('а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'j', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ы' => 'y', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', 'ъ' => '', 'ь' => ''));
        $s = str_replace(" ", "-", $s); // заменяем пробелы знаком минус
        return $s; // возвращаем результат
    }

    /**
     *
     */
    static function wms_hide_stock_yes()
    {
        if (get_option('woocommerce_hide_out_of_stock_items') === 'yes') {
            update_option('woocommerce_hide_out_of_stock_items', 'no');
            update_option('wms_hide_out_of_stock_items', 'yes');
        }
    }

    /**
     *
     */
    static function wms_hide_stock_no()
    {
        if (get_option('wms_hide_out_of_stock_items') === 'yes') {
            update_option('woocommerce_hide_out_of_stock_items', 'yes');
            update_option('wms_hide_out_of_stock_items', 'no');
        }
    }

    /**
     * @param $id
     * @return mixed|string
     */
    static function get_id_ms_explode($id)
    {
        $id_ms = (explode('/', $id));
        $id_ms = array_pop($id_ms);
        $id_ms = (explode('?', $id_ms));
        $id_ms = array_shift($id_ms);
        return $id_ms;
    }

    /**
     * @param $color
     * @return string
     */
    static function wms_color($color)
    {
        $r = floor($color / 65536);
        $color = $color - $r * 65536;
        $g = floor($color / 256);
        $b = $color - $g * 256;
        return $r . " , " . $g . " , " . $b;

    }


    /**
     * @param $sLabel
     * @return string|string[]
     */
    static function get_attribute_label($sLabel)
    {
        $aoTaxonomies = get_taxonomies([], 'objects ');

        foreach ($aoTaxonomies as $oTaxonomy) {
            if (is_object($oTaxonomy) and isset($oTaxonomy->labels->singular_name) and $oTaxonomy->labels->singular_name == $sLabel) {
                return str_replace("pa_", "", $oTaxonomy->name);
            }
        }

        return self::get_translit_label($sLabel);
    }

    /**
     * @param $sLabel
     * @return string
     */
    static function get_translit_label($sLabel)
    {
        if (strlen(wc_sanitize_taxonomy_name($sLabel)) > 20) {
            $sLabel = self::translit($sLabel);
            $sLabel = substr($sLabel, 0, 20);
            $sLabel = rtrim($sLabel, "!,.-");
        }

        return $sLabel;
    }

    /**
     * @param $label
     * @param $value
     * @return string
     */
    static function get_attribute_value($label, $value)
    {
        $oTerm = get_term_by('name', $value, 'pa_' . $label);
        if( is_object($oTerm) and isset($oTerm->slug)){
            return  $oTerm->slug;
        }

        return $value;// slug без pa_

    }

    static function build_tree(array $elements, $parent_key = 'ms'): array {
        $tree = [];
        $map = [];

        foreach ($elements as &$element) {
            $element['children'] = [];

            if(isset($element[$parent_key]['meta']['href'])){
                $element['parent'] = WmsHelper::get_id_ms_explode($element[$parent_key]['meta']['href']);
            }else{
                $element['parent'] = 0;
            }

            $map[$element['id']] = &$element;
        }

        foreach ($elements as &$element) {
            if ($element['parent'] && isset($map[$element['parent']])) {
                $map[$element['parent']]['children'][] = &$element;
            } else {
                $tree[] = &$element;
            }
        }

        return $tree;
    }

    public static function render_select_options(array $tree, $selected = 0, string $prefix = ''): string {
        $html = '';

        foreach ($tree as $node) {
            $html .= sprintf(
                '<option value="%s" %s>%s</option>',
                $node['id'],
                selected($node['id'], $selected, false),
                $prefix . $node['name']
            );

            if (!empty($node['children'])) {
                $html .= self::render_select_options($node['children'], $selected, $prefix . '— ');
            }
        }

        return $html;
    }


}
