<?php
if (!defined('ABSPATH')) exit;

//add_action('wms_stock', 'wms_load_stock_callback');
//add_action('admin_init', 'wms_load_settings_stock');
add_action('wms_stock', 'wms_button_stock', 56);
$option = new Wdc_Options('wms_load_stock', 'wms_stock');
$option->set_section('wms_section_stock');
$option->set_option_id('wms_settings_stock');
$fields = array(
    array(
        'label' => 'Тип синхронизации',
        'id' => 'wms_stock_type_load',
        'option_id' => 'wms_settings_stock',
        'type' => 'select',
        'desc' => 'Выбор типа синхронизации, позволяет настроить скорость синхронизации.</br>
                Если выбран Стандартный то раз в минуту будет происходить синхронизация выбраного количества товара, пока не закончаться товары.</br>
                Если выбран Ускоренный  синхронизация выбраного количества товара будет происходить без остановки, пока не закончаться товары.',
        'placeholder' => '',
        'options' => array(
            'standart' => 'Стандартный',
            'speed' => 'Ускоренный(Нагрузка на сайт)',
        ),
    ),
    array(
        'label' => 'Передача остатков',
        'id' => 'wms_stock_type_status',
        'option_id' => 'wms_settings_stock',
        'type' => 'select',
        'desc' => '',
        'placeholder' => '',
        'options' => array(
            'stock_quantity' => 'Передача количества',
            'stock_status' => 'Передача только в наличии или нет',
        ),
    ),
    array(
        'label' => 'Товаров за проход',
        'id' => 'wms_stock_limit',
        'option_id' => 'wms_settings_stock',
        'type' => 'number',
        'desc' => 'Позволяет указать количество товаров за один запрос, по умолчанию если не чего не указано то 50 товаров (min 5 max 100)',
        'placeholder' => ' Укажите количество товаров за проход(min 5 max 100)',
    ),
    array(
        'label' => 'Выбор склада',
        'id' => 'wms_stock_store',
        'option_id' => 'wms_settings_stock',
        'type' => 'checkbox',
        'desc' => 'Позволяет указать склады с которых загружать остатки.',
        'placeholder' => '',
        'desing' => 'wdc-checkbox',
        'options' => wms_stock_store(),
    ),
    array(
        'label' => 'Загружать остатки автоматически',
        'id' => 'wms_load_auto_stock',
        'option_id' => 'wms_settings_stock',
        'type' => 'checkbox',
        'desc' => '',
        'placeholder' => '',
        'desing' => 'wdc-checkbox',
        'options' => array('on' => '',
        ),
        ),
        array(
        'label' => 'Интервал загрузки остатков',
        'id' => 'wms_load_auto_stock_time',
        'option_id' => 'wms_settings_stock',
        'type' => 'select',
        'desc' => 'Работает на основе WP Cron. <br>
<a href=" https://wp-kama.ru/handbook/codex/wp-cron" target="_blank">Прочитать на WP Kama</a>',
        'placeholder' => '',
        'options' => array(
            'hourly' => 'раз в час',
            'three_hourly' => 'каждые 3 часа',
            'six_hourly' => 'каждые 6 часов',
            'twicedaily' => 'дважды в день',
            'daily' => 'раз в день',
        ),
    ),

);
$option->set_fields($fields);
$option->create();


function wms_stock_store($all = true)
{
    $array = array();
    $stores_array = WmsStoreApi::get_instance()->get_stores($all);
    foreach ($stores_array as $k => &$v1) {
        $array[$k] = $v1['name'];

    }
    return $array;
}


function wms_button_stock()
{
    $disable = '';
    if (WmsWalkerFactory::get_walker('stock')->get_start_walker()) {
        $disable = 'disabled';
    }
    printf('<button class="btn btn-primary loadstock" %s name="loadstock" onclick="wms_start_stock()">Синхронизация остатков</button>', $disable);

}
