<?php

if (!defined('ABSPATH')) exit;

$option = new Wdc_Options('wms_page', 'wms_auth');
$option->set_section('section_id');
$option->set_option_id('wms_settings_auth');
$option->set_sanitize_callback('wdce_sanitize_callback_auth');

$fields = array(
    array(
        'label' => 'Логин',
        'id' => 'wms_login',
        'option_id' => 'wms_settings_auth',
        'type' => 'text',
        'desc' => 'Логин сервиса Мой склад',
        'placeholder' => 'Введите логин',
    ),
    array(
        'label' => 'Пароль',
        'id' => 'wms_pass',
        'option_id' => 'wms_settings_auth',
        'type' => 'password',
        'desc' => 'Пароль сервиса Мой склад',
        'placeholder' => 'Введите пароль',
    ),
    array(
        'label' => 'Nonce',
        'id' => 'wms_nonce',
        'option_id' => 'wms_settings_auth',
        'type' => 'text',
        'desc' => 'Придумайте и укажите код защиты для запросов.',
        'placeholder' => 'Укажите nonce',
    ),
    array(
        'label' => 'Лицензионый ключ',
        'id' => 'wms_license_key',
        'option_id' => 'wms_settings_auth',
        'type' => 'text',
        'desc' => 'Лицензионый ключ',
        'placeholder' => ' Введите Лицензионый ключ',
    ),
    array(
        'label' => 'Email',
        'id' => 'wms_license_email',
        'option_id' => 'wms_settings_auth',
        'type' => 'email',
        'desc' => 'Email',
        'placeholder' => 'Введите Email',
    ));
$option->set_fields($fields);
$option->create();

add_action('wms_auth', 'wms_delete_cashe');

/**
 *
 */
function wms_delete_cashe()
{
    printf('<form action="" method="POST">
						 <button class="btn  btn-primary" name="delete_cashe">Удалить кэш</button>
					</form>');
    if (isset($_POST['delete_cashe'])) {
        delete_transient('wms_cache');
        delete_transient('wcstores_ms_check_' . md5(WCSTORES_MS_VERSION));
        WmsLogs::set_logs('Кэш удален');

    }
}


/**
 * @param $options
 * @return mixed
 */
function wdce_sanitize_callback_auth($options)
{
    delete_transient('wms_cache');
    delete_transient('wcstores_ms_check_' . md5(WCSTORES_MS_VERSION));
    delete_transient('wcstores_ms_check_curl_connect');
    return $options;
}

add_action('wms_auth', 'wms_nonce');

$option->set_sanitize_callback('wdce_sanitize_callback');


/**
 *
 */
function wms_nonce()
{
    printf('<script> var wms_nonce = "' . $GLOBALS['wms_nonce'] . '"</script>');
}
