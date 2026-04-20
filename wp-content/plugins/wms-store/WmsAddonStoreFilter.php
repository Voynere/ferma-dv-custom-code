<?php
/**
 * Created by PhpStorm.
 * User: aqw
 * Date: 09.02.2018
 * Time: 21:29
 */

class WmsAddonStoreFilter
{

    /**
     * @var mixed|void
     */
    private $settings;
    /**
     * @var
     */
    private static $instance;


    /**
     * WmsAddonStoreFilter constructor.
     */
    private function __construct()
    {
        $this->settings = get_option('wms_settings_stock');
        if (isset($this->settings['wms_addon_store_visible_filter_archive']) and $this->settings['wms_addon_store_visible_filter_archive'] == 'on') {
            add_action('woocommerce_archive_description', array($this, 'wms_addon_store_filter'), 10);
            add_action('woocommerce_before_subcategory', array($this, 'wms_addon_store_filter'), 11);


        }
		
        add_filter('posts_where', array($this, 'wms_addon_store_filter_where'), 10, 2);

    }


    /**
     * @return WmsAddonStoreFilter
     */
    public static function get_instance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Клонирование запрещено
     */
    private function __clone()
    {
    }

    /**
     * Сериализация запрещена
     */
    public function __sleep()
    {
    }

    /**
     * Десериализация запрещена
     */
    public function __wakeup()
    {
    }


    /**
     *
     */
    function wms_addon_store_filter()
    {
        if (!is_admin()) { ?>
            <div class="wms-addon-store-filter">
                <form class="wms-addon-store-filter-form" action="" method="get" id="wms-addon-store-filter-form-id">
                    <div class="wms-addon-store-filter-form-h"><?php echo apply_filters('wms_addon_store_filter_form_h', 'Товары в наличии на складах:'); ?></div>
                    <div class="wms-addon-store-filter-form-s"><?php $this->wms_addon_product_store_filter(); ?></div>
                </form>
            </div>
            <?php
        }
    }


    /**
     *
     */
    function wms_addon_product_store_filter()
    {
        if (!isset($_REQUEST['wms-addon-store-filter-form'])) {
            $_REQUEST['wms-addon-store-filter-form'] = array('false');
        }

        if (isset($this->settings['wms_stock_store'])) {
            $option = $this->settings['wms_stock_store'];
        } else {
            $option = array();
        }

        $arr = apply_filters('wms_addon_product_store_filter_select', WmsStoreApi::get_instance()->get_stores());
        ?>
        <a class="wms-main-item" href="javascript:void(0);" tabindex="1">Выбрать</a>

        <ul class="wms-sub-menu">
            <?php foreach ($arr as $k => &$v1) {
                if (in_array($k, $option) and $k != 'all') { ?>
                    <div class="form-group">
                        <input type="checkbox" id="<?php echo $k; ?>" class="wms-addon-store-filter-form-select"
                               name="wms-addon-store-filter-form[]"
                               value="<?php echo $k; ?>" <?php if (in_array($k, $_REQUEST['wms-addon-store-filter-form'])) echo 'checked'; ?> >
                        <label for="<?php echo $k; ?>"><?php print_r($v1['name']); ?></label>
                    </div>
                <?php }

            } ?>
            <button class="wms-addon-store-filter-form-select-button"
                    onclick="document.getElementById('wms-addon-store-filter-form-id').submit()"><?php echo apply_filters('wms-addon-store-filter-form-select-button', 'Выбрать'); ?></button>
        </ul>
        <?php

    }


    /**
     * @param string $where
     * @return string
     */
    private function should_filter_query($query, $where)
    {
        if (is_admin() || !($query instanceof WP_Query) || !$query->is_main_query()) {
            return false;
        }

        if (strpos($where, "wp_posts.post_type = 'product'") === false) {
            return false;
        }

        $wc_query = $query->get('wc_query');
        if ($wc_query === 'product_query') {
            return true;
        }

        $post_type = $query->get('post_type');
        if ($post_type === 'product' || (is_array($post_type) && in_array('product', $post_type, true))) {
            return true;
        }

        $taxonomy = $query->get('taxonomy');

        return in_array($taxonomy, array('product_cat', 'product_tag'), true);
    }

    private function resolve_filter_shops()
    {
        $shops = isset($_REQUEST['wms-addon-store-filter-form']) ? (array) $_REQUEST['wms-addon-store-filter-form'] : array();

        global $uss_shops,
               $vl_shops,
               $art_shops;

        if (isset($_COOKIE['wms_city']) && $_COOKIE['wms_city'] == "vl") {
            $shops = (array) $vl_shops;
        }

        if (isset($_COOKIE['wms_city']) && $_COOKIE['wms_city'] == "uss") {
            $shops = (array) $uss_shops;
        }

        if (isset($_COOKIE['wms_city']) && $_COOKIE['wms_city'] == "art") {
            $shops = (array) $art_shops;
        }

        if (isset($_COOKIE['wms_city']) && is_array($_COOKIE['wms_city'])) {
            $shops = $_COOKIE['wms_city'];
        }

        if (isset($_COOKIE['wms_city']) && is_string($_COOKIE['wms_city'])) {
            $decoded = base64_decode($_COOKIE['wms_city'], true);
            if ($decoded !== false) {
                $shops_serialize = @unserialize($decoded);
                if (is_array($shops_serialize)) {
                    $shops = $shops_serialize;
                }
            }
        }

        if (isset($_COOKIE['key_market']) && $_COOKIE['key_market'] != '' && isset($_COOKIE['delivery']) && $_COOKIE['delivery'] == 1) {
            $shops[] = $_COOKIE['key_market'];
        }

        $shops = array_values(array_unique(array_filter(array_map('sanitize_text_field', (array) $shops))));

        return $shops;
    }

    private function get_filtered_product_ids($shops)
    {
        if (class_exists('\Wdc\Addition\Stores\StockTable') && \Wdc\Addition\Stores\StockTable::is_ready_for_reads()) {
            $product_ids = \Wdc\Addition\Stores\StockTable::get_product_ids_by_stores($shops);
            if (is_array($product_ids)) {
                return $product_ids;
            }
        }

        return $this->get_filtered_product_ids_from_postmeta($shops);
    }

    private function get_filtered_product_ids_from_postmeta($shops)
    {
        $shops = array_values(array_unique(array_filter((array) $shops)));
        sort($shops);

        $cache_key = 'wms_store_filter_ids_' . md5(wp_json_encode($shops));
        $cached_ids = wp_cache_get($cache_key, 'wms_store');
        if ($cached_ids !== false) {
            return $cached_ids;
        }

        $cached_ids = get_transient($cache_key);
        if ($cached_ids !== false) {
            wp_cache_set($cache_key, $cached_ids, 'wms_store', 300);
            return $cached_ids;
        }

        global $wpdb;

        $placeholders = implode(', ', array_fill(0, count($shops), '%s'));
        $sql = "
            SELECT DISTINCT filtered.parent_id
            FROM (
                SELECT pm.post_id AS parent_id
                FROM $wpdb->postmeta pm
                WHERE pm.meta_key IN ($placeholders)
                  AND pm.meta_value > '0'

                UNION

                SELECT p.post_parent AS parent_id
                FROM $wpdb->posts p
                INNER JOIN $wpdb->postmeta pm ON pm.post_id = p.ID
                WHERE p.post_type = 'product_variation'
                  AND pm.meta_key IN ($placeholders)
                  AND pm.meta_value > '0'
            ) filtered
        ";

        $prepared_sql = $wpdb->prepare($sql, array_merge($shops, $shops));
        $product_ids = array_map('intval', $wpdb->get_col($prepared_sql));

        set_transient($cache_key, $product_ids, 300);
        wp_cache_set($cache_key, $product_ids, 'wms_store', 300);

        return $product_ids;
    }

    function wms_addon_store_filter_where($where = '', $query = null)
    {
        if (!$this->should_filter_query($query, $where)) {
            return $where;
        }

        $shops = $this->resolve_filter_shops();
        if (empty($shops) || in_array('all', $shops, true)) {
            return $where;
        }

        global $wpdb;

		$product_is_kulich = false;
		$uri = explode("/", $_SERVER['REQUEST_URI']);

		if(isset($uri[2]) && $uri[2] != '') {
			$product_obj = get_page_by_path( $uri[2], OBJECT, 'product' );
			if($product_obj) {
				$terms = get_the_terms( $product_obj->ID, 'product_cat' );
				
				if(isset($terms[0]) && $terms[0]->term_id == 301) {
					$product_is_kulich = true;
				}
			}
		}
		
		//echo $where;

        if (!is_product_category('kulichi') && !$product_is_kulich) {
            $product_ids = $this->get_filtered_product_ids($shops);
            if (empty($product_ids)) {
                return $where . " AND 1 = 0";
            }

            $where .= " AND $wpdb->posts.ID IN (" . implode(',', $product_ids) . ")";

            return $where;

        }

        return $where;
    }

}