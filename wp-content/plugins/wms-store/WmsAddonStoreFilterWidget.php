<?php

/**
 * Class WmsAddonStoreFilterWidget
 */
class WmsAddonStoreFilterWidget extends WP_Widget
{

    /*
     * создание виджета
     */
    /**
     * WmsAddonStoreFilterWidget constructor.
     */
    function __construct()
    {
        parent::__construct(
            'wms_addon_store_widget',
            'Фильтр по складам', // заголовок виджета
            array('description' => 'Позволяет фильтровать товары по складам.') // описание
        );
    }

    /*
     * фронтэнд виджета
     */
    /**
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {
        if (!isset($_REQUEST['wms-addon-store-filter-form'])) {
            $_REQUEST['wms-addon-store-filter-form'] = array('false');
        }

        $title = apply_filters('widget_title', $instance['title']); // к заголовку применяем фильтр (необязательно)
        echo $args['before_widget'];
        $settings = get_option('wms_settings_stock');
        if (isset($settings['wms_stock_store'])) {
            $option = $settings['wms_stock_store'];
        } else {
            $option = array();
        }

        $arr = apply_filters('wms_addon_product_store_filter_select', WmsStoreApi::get_instance()->get_stores());
        ?>
        <div class="wms-addon-store-filter">
            <form class="wms-addon-store-filter-form" action="" method="get" id="wms-addon-store-filter-form-id">
                <div class="wms-addon-store-filter-form-h"><?php echo apply_filters('wms_addon_store_filter_form_h', 'Товары в наличии на складах:'); ?></div>


                <?php foreach ($arr as $k => &$v1) {
                    if (in_array($k, $option) and $k != 'all') { ?>

                        <div class="form-group">
                            <input type="checkbox" id="<?php echo $k; ?>"
                                   class="wms-addon-store-filter-form-select-widget"
                                   name="wms-addon-store-filter-form[]"
                                   value="<?php echo $k; ?>" <?php if (in_array($k, $_REQUEST['wms-addon-store-filter-form'])) echo 'checked'; ?> >
                            <label for="<?php echo $k; ?>"> <?php print_r($v1['name']); ?></label>
                        </div>
                    <?php }

                } ?>
                <button class="wms-addon-store-filter-form-select-button-widget"
                        onclick="document.getElementById('wms-addon-store-filter-form-id').submit()"><?php echo apply_filters('wms-addon-store-filter-form-select-button', 'Выбрать'); ?></button>
            </form>
        </div>
        <?php

        echo $args['after_widget'];
    }

    /*
     * бэкэнд виджета
     */
    /**
     * @param array $instance
     * @return string|void
     */
    public function form($instance)
    {
        $title = '';
        if (isset($instance['title'])) {
            $title = $instance['title'];
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Заголовок</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/>
        </p>
        <?php
    }

    /*
     * сохранение настроек виджета
     */
    /**
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['posts_per_page'] = (is_numeric($new_instance['posts_per_page'])) ? $new_instance['posts_per_page'] : '5'; // по умолчанию выводятся 5 постов
        return $instance;
    }
}