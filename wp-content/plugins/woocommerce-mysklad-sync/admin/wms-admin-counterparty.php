<?php
if (!defined('ABSPATH')) exit;

//add_action('wms_counterparty', 'wms_load_counterparty_callback');
add_action('wms_counterparty', 'wms_counterparty_button',100);
//add_action('admin_init', 'wms_load_settings_counterparty');

$option = new Wdc_Options('wms_load_counterparty','wms_counterparty');
$option->set_section('wms_section_counterparty');
$option->set_option_id('wms_settings_counterparty');
$fields = array(
    array(
        'label' => 'Вариант синхронизации',
        'id' => 'wms_counterparty_variant_load',
        'option_id' => 'wms_settings_counterparty',
        'type' => 'select',
        'desc' => 'Выбор варианта синхронизации. 
                Полный: синхронизирует всех контрагентов.</br>
                Только обновленные: синхронизирует только обновленных контрагентов с последней синхронизации
                (Синхронизации через вебхуки не  учитываются)',
        'placeholder' => '',
        'options' => array(
            'full' => 'Полный',
            'updated' => 'Только обновленные',
        ),
    ),
    array(
        'label' => 'Тип синхронизации',
        'id' => 'wms_counterparty_type_load',
        'option_id' => 'wms_settings_counterparty',
        'type' => 'select',
        'desc' => 'Выбор типа синхронизации, позволяет настроить скорость синхронизации.</br>
                Если выбран Стандартный то раз в минуту будет происходить синхронизация выбраного контрагентов , пока не закончаться контрагенты.</br>
                Если выбран Ускоренный  синхронизация выбраного количества контрагентов будет происходить без остановки, пока не закончаться контрагенты.',
        'placeholder' => '',
        'options' => array(
            'standart' => 'Стандартный',
            'speed' => 'Ускоренный(Нагрузка на сайт)',
        ),
    ),
    array(
        'label' => 'Товаров за проход',
        'id' => 'wms_counterparty_limit',
        'option_id' => 'wms_settings_counterparty',
        'type' => 'number',
        'desc' => 'Позволяет указать количество контрагентов за один запрос, по умолчанию если не чего не указано то 50 товаров (min 5 max 100)',
        'placeholder' => ' Укажите количество контрагентов за проход(min 5 max 100)',
    ),
    array(
        'label' => 'Создавать контрагентов в МС при регистрации на сайте',
        'id' => 'wms_load_register_counterparty',
        'option_id' => 'wms_settings_counterparty',
        'type' => 'checkbox',
        'desc' => '',
        'placeholder' => '',
        'desing' => 'wdc-checkbox',
        'options' => array('on' => ''
        ),
    ),
    array(
        'label' => 'Группа для создания Контрагентов в МС',
        'id' => 'wms_counterparty_tags',
        'option_id' => 'wms_settings_counterparty',
        'type' => 'text',
        'desc' => 'Можно несколько групп через запятую(Сайт, Покупатели, Интернет) После запятой обязательно пробел',
        'placeholder' => 'Укажите группы',
    ),
    array(
        'label' => 'Разрешить дополнительные цены',
        'id' => 'wms_load_price_counterparty',
        'option_id' => 'wms_settings_counterparty',
        'type' => 'checkbox',
        'desc' => '',
        'placeholder' => '',
        'desing' => 'wdc-checkbox',
        'options' => array('on' => ''
        ),
    ),

);
$option->set_fields($fields);
$option->create();




/**
 *
 */
function wms_counterparty_button()
{
    $disable = '';
    if (WmsWalkerFactory::get_walker('counterparty')->get_start_walker()) {
        $disable = 'disabled';
    }
    printf('<button class="btn  btn-primary loadcounterparty" %s name="loadcounterparty" onclick="wms_start_counterparty()">Синхронизировать по email</button>', $disable);

}
