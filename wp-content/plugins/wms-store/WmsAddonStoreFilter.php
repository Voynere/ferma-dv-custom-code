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
		
        add_filter('posts_where', array($this, 'wms_addon_store_filter_where'));

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
    private function __sleep()
    {
    }

    /**
     * Десериализация запрещена
     */
    private function __wakeup()
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
    function wms_addon_store_filter_where($where = '')
    {
		//$where = str_replace("wp_posts.ID NOT IN", "wp_posts.ID IN", $where);
		
        $bIsProductQuery = strpos($where, "wp_posts.post_type = 'product'");

        if ($bIsProductQuery === false) {
            return $where;
        }
		
		$shops = ($_REQUEST['wms-addon-store-filter-form']) ?? false;
		
		global $uss_shops,
			   $vl_shops,
			   $art_shops;
			   
		if(isset($_COOKIE['wms_city']) && $_COOKIE['wms_city'] == "vl") {
			$shops = $vl_shops;
		}
		
		if(isset($_COOKIE['wms_city']) && $_COOKIE['wms_city'] == "uss") {
			$shops = $uss_shops;
		}
		
		if(isset($_COOKIE['wms_city']) && $_COOKIE['wms_city'] == "art") {
			$shops = $art_shops;
		}
		
		if(isset($_COOKIE['wms_city']) && is_array($_COOKIE['wms_city'])) {
			$shops = $_COOKIE['wms_city'];
		}
		
		$shops_serialize = unserialize(base64_decode( $_COOKIE['wms_city']));
		
		if($shops_serialize) {
			$shops = $shops_serialize;
		}
		if(isset($_COOKIE['key_market']) && $_COOKIE['key_market'] != '' && isset($_COOKIE['delivery']) && $_COOKIE['delivery'] == 1) {
			$shops[] = $_COOKIE['key_market'];
		}
		
		if (!isset($shops)) return $where;
        if ($shops == 'all' or empty($shops)) return $where;
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

        if (!is_admin() && !is_product_category( 'kulichi' ) && !$product_is_kulich) {

            if (!isset($shops) and empty($shops)) return $where;

            $where .= " AND (";
            $count = count($shops);
            $i = 0;
			
			foreach ($shops as $k => $v) {
                ++$i;
                $where .= " $wpdb->posts.ID IN (SELECT $wpdb->postmeta.post_id FROM $wpdb->postmeta WHERE meta_key = '$v' AND meta_value > '0' ) OR  $wpdb->posts.ID  IN (SELECT $wpdb->posts.post_parent FROM $wpdb->posts WHERE $wpdb->posts.post_type = 'product_variation' AND $wpdb->posts.ID  IN (SELECT $wpdb->postmeta.post_id FROM $wpdb->postmeta WHERE meta_key = '$v' AND meta_value > '0' ) )";
                if ($i !== $count) $where .= " OR ";
            }
			
			$where .= " )";
			
            return $where;

        }

        return $where;
    }

}