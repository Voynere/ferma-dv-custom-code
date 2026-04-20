<?php

//add_action('woocommerce_single_product_summary', array('WmsAddonStore',  'wms_addon_product_store_fieldvqq' ));

/**
 *
 */
class WmsAddonGroop
{

    /**
     * WmsAddonGroop constructor.
     */
    public function __construct()
    {
        add_action('admin_init', array($this, 'wms_load_settings_groop'));
        add_action('admin_enqueue_scripts', array($this, 'wms_addon_groop_styles'));
        add_filter('wms_load_array_products', array($this, 'filter_groop'), 10, 2);

        add_action('wp_ajax_wms_clear_groop', array($this, 'wms_clear_groop'));
        add_action('wp_ajax_nopriv_wms_clear_groop', array($this, 'wms_clear_groop'));

    }

    // регистрируем файл стилей и добавляем его в очередь

    /**
     *
     */
    function wms_addon_groop_styles()
    {

        wp_enqueue_style('wms-addon-groop-styles', plugins_url('style.css', __FILE__));
        wp_register_script('wms-script-groop', plugins_url('wms-script-groop.js', __FILE__), array('jquery'));
        wp_enqueue_script('wms-script-groop');
    }


    /**
     *
     */
    public function wms_load_settings_groop()
    {

        add_settings_field('wms_groop_filter', 'Группы:', array($this, 'wms_groop_filter'), 'wms_product', 'section_load');

    }


    /**
     *
     */
    public function wms_groop_filter()
    {
        $option = get_option('wms_settings_product');
        if (isset($option['wms_groop_filter'])) {
            $option = $option['wms_groop_filter'];
        } else {
            $option = array('all');
        }
        $groop = new WmsGroopApi();
        $groop_array = $groop->get_groops();
        ?>
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal2">
            Выберите группы
        </button>
        <div class="modal fade bd-example-modal-lg" id="myModal2" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="row">
                            <div class="col-md-12">

                                <h4 class="modal-title" id="myModalLabel">Выберите группы, товары которых будут
                                    загружаться на
                                    сайт</h4>
                            </div>
                            <div class="col-md-12">
                                <p>Выбирать нужно саму группу, если выбрать родительскую то такие товары
                                    проигнарируються</p>
                            </div>

                        </div>
                    </div>
                    <div class="modal-body">
                        <?php $this->wms_groop_filter_html_checkbox($groop_array, $option); ?>
                        <div>
                            <?php submit_button('Сохранить'); ?>
                            <?php $this->wms_buttom_clear_groop(); ?>
                        </div>

                    </div>

                </div>
            </div>
        </div>
        <?php

    }


    /**
     *
     */
    public function wms_buttom_clear_groop()
    {
        printf('<div class="btn  btn-danger wms-clear-groop"  name="wms-clear-groop" onclick="wms_clear_groop()">Очистить</div>');
    }


    /**
     *
     */
    public function wms_clear_groop()
    {
        $option = get_option('wms_settings_product');
        if (isset($option['wms_groop_filter'])) {
            unset($option['wms_groop_filter']);

        }

        update_option('wms_settings_product', $option);
        echo 'Очищено';

    }

    /**
     * @param $id
     * @param $groop_parent
     * @return bool
     */
    public function filter_parent_id($id, $groop_parent)
    {
        $option = get_option('wms_settings_product');
        if (isset($option['wms_groop_filter'])) {
            if (!in_array($groop_parent['meta']['href'], $option)) {
                return false;
            }

        }

        return $id;

    }


    /**
     * @param $groop_array
     * @param $option
     */
    public function wms_groop_filter_html_checkbox($groop_array, $option)
    {
        foreach ($groop_array['rows'] as $k => &$v1) {
            ?>
            <fieldset class="<?php echo $v1['id']; ?> ">
                <label>

                    <input type="checkbox"
                           data-wms-parent-id="<?php echo WmsHelper::get_id_ms_explode($v1['productFolder']['meta']['href']); ?>"
                           class="checkbox" id="<?php echo $v1['id']; ?>"
                           name="wms_settings_product[wms_groop_filter][<?php echo $v1['id']; ?>]"
                           value="<?php echo $v1['meta']['href']; ?>" <?php if (in_array($v1['meta']['href'], $option)) echo 'checked'; ?> ><label
                            for="<?php echo $v1['id']; ?>"></label> <?php echo $v1['name'] ?>

                </label>
            </fieldset>


        <?php }
    }


    /**
     * @param $products
     * @param $settings
     * @return bool
     */
    public function filter_groop($products, $settings)
    {
        if ($products['meta']['type'] === 'variant') {
            return $products;
        }

        if (isset($settings['wms_groop_filter']) and !empty($settings['wms_groop_filter'])) {
            if (array_key_exists($products['productFolder']['id'], $settings['wms_groop_filter'])) {
                return $products;
            }
        }

        return false;
    }

}
