<?php

if (!defined('ABSPATH')) exit;

//add_action( 'admin_init', 'wms_load_settings' );

add_action('wms_product', 'wms_product_button', 55);

$option = new Wdc_Options('wms_product', 'wms_product');
$option->set_section('section_load');
$option->set_option_id('wms_settings_product');
$fields = array(
      array(
        'label' => 'Вариант синхронизации товаров',
        'id' => 'wms_product_variant_sync',
        'option_id' => 'wms_settings_product',
        'type' => 'select',
        'desc' => 'Выбор варианта синхронизации.</br> 
                <b>Полный :</b> синхронизирует все товары. Создание новых, обновление существующих (обновляет только те товары что изменились)</br>
                <b>Только обновленние товаров:</b> Обновляет только сущестувушие товары (Новые товары несоздаются)</br>
                <b>Только обновленние цены:</b> Обновляет только цену</br>
                <b>Записываем только мета информацию Сервиса Мой Склад:</b></br>
                Эта опция позволяет прописать только те данные, что нужны для обновления остатков и отправки заказов без изменения информации о товаре</br>
                <b>Максимальный:</b>синхронизирует все товары. Создание новых, обновление существующих обновляет все товары независимо от того нужно это или нет</br>',
        'placeholder' => '',
        'options' => array(
            'full' => 'Полный рекомендовано',            
            'updated' => 'Только обновленние товаров(Новые несоздаются)',
            'updated_price' => 'Только обновленние цены',
            'updated_meta' => 'Записываем только мета информацию Сервиса Мой Склад',
            'fullno' => 'Максимальный',
        ),
    ),
    array(
        'label' => 'Вариант загрузки товаров с мой склад',
        'id' => 'wms_product_variant_load',
        'option_id' => 'wms_settings_product',
        'type' => 'select',
        'desc' => 'Выбор варианта получения товаров с мой склад. 
                Полный: запрашивает все товары.</br>
                Только обновленные: получает только обновленные товары с последней синхронизации
                (Синхронизации через вебхуки не  учитываются)',
        'placeholder' => '',
        'options' => array(
            'full' => 'Полный',
            'updated' => 'Только обновленные',
            'archived' => 'Только архивные'
        ),
    ),
    array(
        'label' => 'Тип синхронизации товаров',
        'id' => 'wms_product_type_load',
        'option_id' => 'wms_settings_product',
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
        'label' => 'Товаров за проход',
        'id' => 'wms_product_limit',
        'option_id' => 'wms_settings_product',
        'type' => 'number',
        'desc' => 'Позволяет указать количество товаров за один запрос, по умолчанию если не чего не указано то 25 товаров (min 5 max 100)',
        'placeholder' => ' Укажите количество товаров за проход(min 5 max 100)',
    ),
    array(
        'label' => 'Товаров за проход у которых нужно обновить картинки',
        'id' => 'wms_product_limit_img',
        'option_id' => 'wms_settings_product',
        'type' => 'number',
        'desc' => 'Позволяет указать количество товаров у которых нужно обновить картинки за один запрос, по умолчанию если не чего не указано то 5 картинок',
        'placeholder' => ' Укажите количество картинок за проход',
    ),
    array(
        'label' => 'Время через которое проверять обновление картинок',
        'id' => 'wms_load_image_time',
        'option_id' => 'wms_settings_product',
        'type' => 'number',
        'desc' => 'Позволяет указать время через которое проверять обновление картинок.Время указывается в минутах',
        'placeholder' => ' Укажите время в минутах',
    ),
    array(
        'label' => 'Выбор варианта синхронизации',
        'id' => 'wms_product_select_var',
        'option_id' => 'wms_settings_product',
        'type' => 'select',
        'desc' => 'Выбор варианта синхронизации, позволяет настроить сопостовление товара МС и WC(то есть по какому полю искать и обновлять товар на сайте).',
        'placeholder' => '',
        'options' => array(
            '_id_ms' => 'По ID товара(ID сервиса МС)(РЕКОМЕНДОВАНО)',
            '_sku' => 'По Коду(MC) Артиклу(WC)',
            '_sku#article' => 'По Артиклу(MC) Артиклу(WC)',
            '_externalCode' => 'По Внешнему коду',
        ),
    ),
    array(
        'label' => 'Статус нового товара',
        'id' => 'wms_post_status',
        'option_id' => 'wms_settings_product',
        'type' => 'select',
        'desc' => 'Позвалят указать статус нового товара',
        'placeholder' => '',
        'options' => array(
            'publish' => 'Опубликовано',
            'pending' => 'На утверждении',
            'draft' => 'Черновик',
        ),
    ),
    array(
        'label' => 'Данные из МС для записи в артикул WC',
        'id' => 'wms_product_sku',
        'option_id' => 'wms_settings_product',
        'type' => 'select',
        'desc' => 'Позволяет указать какие данные записывать в артикул WC(по умолчанию записывается код).',
        'placeholder' => '',
        'options' => array(
            'code' => 'Код',
            'article' => 'Артикул',
        ),
    ),
    array(
        'label' => 'Выбор цены загрузки',
        'id' => 'wms_price',
        'option_id' => 'wms_settings_product',
        'type' => 'select',
        'desc' => 'Позволяет указать тип цены для загрузки на сайт.',
        'placeholder' => '',
        'options' => wms_price(),
    ),
    array(
        'label' => 'Включить цену распродажи',
        'id' => 'wms_sale_price_on',
        'option_id' => 'wms_settings_product',
        'type' => 'checkbox',
        'desc' => '',
        'placeholder' => '',
        'desing' => 'wdc-checkbox',
        'options' => array('on' => '',
        ),
    ),
    array(
        'label' => 'Выбор цены распродажи',
        'id' => 'wms_sale_price',
        'option_id' => 'wms_settings_product',
        'type' => 'select',
        'desc' => 'Позволяет указать тип цены распродажи для загрузки на сайт.',
        'placeholder' => '',
        'options' => wms_price(),
    ),
    array(
        'label' => 'Загружать услуги',
        'id' => 'wms_load_service',
        'option_id' => 'wms_settings_product',
        'type' => 'checkbox',
        'desc' => '',
        'placeholder' => '',
        'desing' => 'wdc-checkbox',
        'options' => array('on' => '',
        ),
    ),
    array(
        'label' => 'Загружать группы',
        'id' => 'wms_load_groops',
        'option_id' => 'wms_settings_product',
        'type' => 'checkbox',
        'desc' => 'Позволяет загружать группы товаров с мой склад.</br>
                   При загрузке сохраняет вложеность',
        'placeholder' => '',
        'desing' => 'wdc-checkbox',
        'options' => array('on' => '',
        ),
    ),
    array(
        'label' => 'Загружать картинки',
        'id' => 'wms_load_image',
        'option_id' => 'wms_settings_product',
        'type' => 'select',
        'desc' => 'Позволяет загружать изображения товара.</br>Если выбрана опция Только основное то будет загруженно только первое изображение.',
        'placeholder' => '',
        'desing' => 'wdc-checkbox',
        'options' => array(
            'off' => 'Нет',
            'on' => 'Только основное',
            'all' => 'Все',
        ),
    ),
    array(
        'label' => 'Отключить обновления названия товара',
        'id' => 'wms_name',
        'option_id' => 'wms_settings_product',
        'type' => 'checkbox',
        'desc' => '',
        'placeholder' => '',
        'desing' => 'wdc-checkbox',
        'options' => array('on' => '',
        ),
    ),
    array(
        'label' => 'Отключить обновления описания товара',
        'id' => 'wms_description',
        'option_id' => 'wms_settings_product',
        'type' => 'checkbox',
        'desc' => '',
        'placeholder' => '',
        'desing' => 'wdc-checkbox',
        'options' => array('on' => '',
        ),
    ),
    array(
        'label' => 'Поле для переноса описания с МС',
        'id' => 'wms_description_type',
        'option_id' => 'wms_settings_product',
        'type' => 'select',
        'desc' => 'Позволяет указать куда записывать описание товара).',
        'placeholder' => '',
        'options' => array(
            'description' => 'Полное описание',
            'short_description' => 'Короткое описание',
        ),
    ),
    array(
        'label' => 'Синхронизировать раз в день',
        'id' => 'wms_load_auto_product',
        'option_id' => 'wms_settings_product',
        'type' => 'checkbox',
        'desc' => 'Работает на основе WP Cron. <br>
<a href=" https://wp-kama.ru/handbook/codex/wp-cron" target="_blank">Прочитать на WP Kama</a>',
        'placeholder' => '',
        'desing' => 'wdc-checkbox',
        'options' => array('on' => '',
        ),
    ),
);
$option->set_fields($fields);
$option->create();


/**
 * @version  1.0.3
 */
function wms_price()
{
    $array = array();

    $Prices = new WmsPriceApi();
    $allPrices = $Prices->get_prices();

    foreach ($allPrices as $price) {
        $array[$price['id']] = $price['name'];
    }

    return $array;
}



function wms_product_button()
{
    $disable = '';
    printf('<button class="btn  btn-primary loadproduct" %s name="loadproduct" onclick="wms_start_assortment()">Синхронизировать</button>', $disable);
}


