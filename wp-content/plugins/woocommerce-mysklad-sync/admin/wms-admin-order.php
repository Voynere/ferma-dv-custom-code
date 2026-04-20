<?php
if (!defined('ABSPATH')) exit;


add_action('admin_init', 'wms_settings_order', 100);
//add_action('wms_order', 'wms_order_callback');

$option = new Wdc_Options('wms_order_page', 'wms_order');
$option->set_section('section_order');
$option->set_option_id('wms_settings_order');
$option->set_sanitize_callback('wdce_sanitize_callback');
$fields = array(
    array(
        'label' => 'Включить выгрузку заказов',
        'id' => 'wms_active_order',
        'option_id' => 'wms_settings_order',
        'type' => 'checkbox',
        'desc' => '',
        'placeholder' => '',
        'desing' => 'wdc-checkbox',
        'options' => array('on' => ''
        ),
    ),
    array(
        'label' => 'Отправление заказов',
        'id' => 'wms_order_type_send',
        'option_id' => 'wms_settings_order',
        'type' => 'select',
        'desc' => '',
        'placeholder' => '',
        'options' => array(
            'auto' => 'Автоматически при заказе Клиента',
            'autotime' => 'Автоматически по расписанию',
            'offauto' => 'Только в ручную',
        ),
    ),
    array(
        'label' => 'Группа для создания Контрагентов в МС',
        'id' => 'wms_order_counterparty_tags',
        'option_id' => 'wms_settings_order',
        'type' => 'text',
        'desc' => 'Можно несколько групп через запятую(Сайт, Покупатели, Интернет) После запятой обязательно пробел',
        'placeholder' => 'Укажите группы',
    ),
    array(
        'label' => 'Расписание(проверять каждые)',
        'id' => 'wms_order_auto_time',
        'option_id' => 'wms_settings_order',
        'type' => 'number',
        'desc' => '',
        'placeholder' => '',
    ),
    array(
        'label' => 'Время создания заказа',
        'id' => 'wms_order_date',
        'option_id' => 'wms_settings_order',
        'type' => 'select',
        'desc' => '',
        'placeholder' => '',
        'options' => array(
            'date_send' => 'Время отправки',
            'date_create' => 'Время создания',
        ),
    ),
    array(
        'label' => 'Префикс',
        'id' => 'wms_prefix',
        'option_id' => 'wms_settings_order',
        'type' => 'text',
        'desc' => '',
        'placeholder' => '',
    ),
    array(
        'label' => 'Постфикс',
        'id' => 'wms_postfix',
        'option_id' => 'wms_settings_order',
        'type' => 'text',
        'desc' => '',
        'placeholder' => '',
    ),
    array(
        'label' => 'Организация для работы с заказами',
        'id' => 'wms_organization',
        'option_id' => 'wms_settings_order',
        'type' => 'select',
        'desc' => '',
        'placeholder' => '',
        'options' => wms_organization(),
    ),
    array(
        'label' => 'Контрагент по умолчанию',
        'id' => 'wms_order_counterparty_by_default',
        'option_id' => 'wms_settings_order',
        'type' => 'text',
        'desc' => 'Позволяет указывать Контрагента который будет подствлятся в случае ошибок.</br>Если оставить пустым или указать false то контрагент не будет установлен',
        'placeholder' => 'Укажите email, телефон, имя контрагента',
    ),
    array(
        'label' => 'Выбор склада(Обязательно)',
        'id' => 'wms_stock_store_order',
        'option_id' => 'wms_settings_order',
        'type' => 'radio',
        'desc' => '',
        'placeholder' => '',
        'desing' => 'wdc-radio',
        'options' => wms_stock_store(false),
    ),
    array(
        'label' => 'Включить резерв товаров',
        'id' => 'wms_order_reserv',
        'option_id' => 'wms_settings_order',
        'type' => 'checkbox',
        'desc' => '',
        'placeholder' => '',
        'desing' => 'wdc-checkbox',
        'options' => array('on' => ''
        ),
    ),
    array(
        'label' => 'Статус заказа Выполнен',
        'id' => 'wms_states_wc_completed',
        'option_id' => 'wms_settings_order',
        'type' => 'select',
        'desc' => '',
        'placeholder' => '',
        'options' => wms_states_wc_completed(),
    ),
    array(
        'label' => 'Статус заказа Отменен',
        'id' => 'wms_states_wc_cancelled',
        'option_id' => 'wms_settings_order',
        'type' => 'select',
        'desc' => '',
        'placeholder' => '',
        'options' => wms_states_wc_completed(),
    ),
    array(
        'label' => 'Статус заказа при успешной оплате заказа на сайте',
        'id' => 'wms_states_ms_successful_payment',
        'option_id' => 'wms_settings_order',
        'type' => 'select',
        'desc' => 'Позволяет изменить статус заказа в мс при успешной оплате на сайте.',
        'placeholder' => '',
        'options' => wms_states_wc_completed(),
    ),
    array(
        'label' => 'Разрешить передачу статуса при создании заказ',
        'id' => 'wms_create_order_state',
        'option_id' => 'wms_settings_order',
        'type' => 'checkbox',
        'desc' => 'Позволяет передавать статус заказа если он настрен и разрешен в настройках по умолчанию статусы передаеться как новый.',
        'placeholder' => '',
        'desing' => 'wdc-checkbox',
        'options' => array('on' => ''
        ),
    ),
    array(
        'label' => 'Разрешить смену статуса заказа в МС при смене статуса на на сайте',
        'id' => 'wms_update_order_ms',
        'option_id' => 'wms_settings_order',
        'type' => 'checkbox',
        'desc' => 'Позволяет менять статус заказа в сервисе МС при смене статуса на сайте. Статус меняеться только если он разрешен в настройках плагина',
        'placeholder' => '',
        'desing' => 'wdc-checkbox',
        'options' => array('on' => ''
        ),
    ),
    array(
        'label' => 'Отправлять уведомления о смене статуса заказа',
        'id' => 'wms_email_send_state_order',
        'option_id' => 'wms_settings_order',
        'type' => 'checkbox',
        'desc' => '',
        'placeholder' => '',
        'desing' => 'wdc-checkbox',
        'options' => array('on' => ''
        ),
    ),
    array(
        'label' => 'Доставка',
        'id' => 'wms_order_delivery',
        'option_id' => 'wms_settings_order',
        'type' => 'text',
        'desc' => 'Позволяет указывать товар(услугу) которая передается как отдельная позиция доставки.</br>Если оставить пустым или указать false то даставка передаватся не будет.',
        'placeholder' => 'Укажите код товара',
    ),


);
$option->set_fields($fields);
$option->create();


// Очистка данных
function wdce_sanitize_callback($options)
{
    // очищаем
    foreach ($options as $name => & $val) {
        if ($name == 'wms_order_delivery' and !empty($val) and $val !== false and $val !== 'false') {
            $url = WMS_URL_API_V2 . '/entity/assortment/?filter=search=' . $val;
            $assortment = WmsConnectApi::get_instance()->send_request($url);
            if (!empty($assortment['rows'])) {
                $options['wms_order_delivery_product']['id'] = $assortment['rows'][0]['id'];
                $options['wms_order_delivery_product']['type'] = $assortment['rows'][0]['meta']['type'];

            } else {
                $options['wms_order_delivery_product'] = [];
                die('Товар с кодом ' . $val . ' не найден.');
            }
        }

    }

    //die(print_r( $options )); // Array ( [input] => aaaa [checkbox] => 1 )

    return $options;
}


/**
 * @version  1.0.5
 */
function wms_settings_order()
{
    add_settings_field('wms_states_wc_ms', 'Разрешеные статусы для выгрузки из МС:', 'wms_states_wc_ms', 'wms_order_page', 'section_order');
}

/**
 *
 */
function wms_organization()
{
    $array = array();
    $org_array = WmsOrg::get_org();

    if (!is_array($org_array) and empty($org_array)) {
        return $org_array;
    }

    foreach ($org_array as $k => &$v1) {
        $array[$k] = $v1['name'];

    }
    return $array;
}


/**
 *
 */
function wms_states_wc_completed()
{
    $array = array();
    $states_array = WmsOrderStatusApi::get_instance()->get_states();

    if (!is_array($states_array) and empty($states_array)) {
        return $array;
    }

    foreach ($states_array as $k => &$v1) {
        $array[$v1['id']] = $v1['name'];

    }
    return $array;
}


/**
 *
 */
function wms_states_wc_ms()
{
    $option['wms_states_wc_ms'] = array();
    $option = get_option('wms_settings_order');
    if (isset($option['wms_states_wc_ms'])) {
        $option = $option['wms_states_wc_ms'];
    }
    ?>
    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModalstates">
        Статусы
    </button>
    <div class="modal fade bd-example-modal-lg" id="myModalstates" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Настройка статусов</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="wms-table" id="wms-states-table">

                        <thead>
                        <tr class="wms-table-tr">
                            <th width="2%">Активация</th>
                            <th width="70%">Имя</th>
                            <th width="28%">Label(ID)<span class="woocommerce-help-tip" data-toggle="tooltip"
                                                           data-placement="right"
                                                           title="<?php _e('Введите метку статуса в нижнем регистре латинскими буквами и цифрами, который вы хотите добавить, должно быть уникальным для каждого статуса не более 17 символов', ''); ?>"</span>
                            </th>
                        </tr>
                        </thead>

                        <tbody>

                        <?php $states = WmsOrderStatusApi::get_instance()->get_states(); ?>
                        <?php if (!is_array($states)) return; ?>

                        <?php foreach ($states as $k => &$v1) {
                            $value = !empty($option[$v1['id']]['label']) ? $option[$v1['id']]['label'] : WmsHelper::translit($v1['name']);
                            $name = !empty($option[$v1['id']]['name']) ? $option[$v1['id']]['name'] : $v1['name'];
                            ?>

                            <tr class="wms-table-tr">

                                <td class="wms-buttom-activation"><input type="checkbox" class="wdc-checkbox"
                                                                         id="<?php echo $v1['id']; ?>"
                                                                         name="wms_settings_order[wms_states_wc_ms][<?php echo $v1['id']; ?>][activate]" <?php if (isset($option[$v1['id']]['activate']) and $option[$v1['id']]['activate'] == 'on') echo 'checked'; ?> >
                                    <label for="<?php echo $v1['id']; ?>"></label></td>

                                <td class="wms-table-name">
                                    <mark class="order-status status-<?php echo $value ?>">
                                        <span><input type="text"
                                                     name="wms_settings_order[wms_states_wc_ms][<?php echo $v1['id']; ?>][name]"
                                                     value="<?php echo $name ?>"></span></mark>
                                </td>

                                <td class="wms-buttom-edit-td"><input type="text"
                                                                      name="wms_settings_order[wms_states_wc_ms][<?php echo $v1['id']; ?>][label]"
                                                                      value="<?php echo $value ?>" maxlength="17">
                                </td>
                            </tr>
                            <?php

                        } ?>

                        </tbody>
                    </table>
                    <?php submit_button('', 'btn btn-success', 'order-status_submit_button'); ?>
                    <span class="btn  btn-success" id="add-states" data-toggle="tooltip" data-placement="right"
                          title="Загружает цвета статусов из МС и добавляет их для заказов"
                          onclick="wms_add_style_states()">Загрузить цвета статусов</span>

                </div>

            </div>
        </div>
    </div>
    <?php

}

