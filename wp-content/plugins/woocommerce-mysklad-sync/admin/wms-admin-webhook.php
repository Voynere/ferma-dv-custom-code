<?php

use WCSTORES\WC\MS\Wordpress\Rest\RestRoute;

if (!defined('ABSPATH')) exit;


add_action('admin_init', 'wms_settings_webhook');
add_action('wms_webhook', 'wms_webhook_callback');
add_action('wms_webhook', 'get_webhook_ms', 55);
add_action('wms_webhook', 'get_button_webhook_create', 56);


function wms_webhook_callback()
{
    ?>
    <form action="options.php" method="POST">
        <?php

        settings_fields('option_groups_webhook');     // скрытые защитные поля
        do_settings_sections('wms_webhook_page');
        submit_button('Сохранить', 'btn btn-success', 'wms-webhook-btn');

        ?>

    </form>

    <?php

}


/**
 *
 */
function wms_settings_webhook()
{
    register_setting('option_groups_webhook', 'wms_settings_webhook');

    add_settings_section('section_webhook', '', '', 'wms_webhook_page');

    add_settings_field('wms_active_webhook_product', 'Включить webhook товаров', 'wms_active_webhook_product', 'wms_webhook_page', 'section_webhook');
    add_settings_field('wms_active_webhook_stock', 'Включить webhook остатки', 'wms_active_webhook_stock', 'wms_webhook_page', 'section_webhook');


}


/**
 *
 */
function wms_active_webhook_product()
{
    $option = get_option('wms_settings_webhook');
    if (isset($option['wms_active_webhook_product'])) {
        $option = $option['wms_active_webhook_product'];
    } ?>


    <input type="checkbox" class="wdc-checkbox" id="wms_active_webhook_product"
           name="wms_settings_webhook[wms_active_webhook_product]" <?php if ($option === 'on') echo 'checked'; ?> >
    <label for="wms_active_webhook_product"></label>

<?php }

/**
 *
 */
function wms_active_webhook_stock()
{
    $option = get_option('wms_settings_webhook');
    if (isset($option['wms_active_webhook_stock'])) {
        $option = $option['wms_active_webhook_stock'];
    } ?>


    <input type="checkbox" class="wdc-checkbox" id="wms_active_webhook_stock"
           name="wms_settings_webhook[wms_active_webhook_stock]" <?php if ($option === 'on') echo 'checked'; ?> >
    <label for="wms_active_webhook_stock"></label>

<?php }


function get_webhook_ms()
{
    $WebHook = new  \WCSTORES\WC\MS\MoySklad\WebHook();
    $result = $WebHook->get();

    if (!is_array($result) and empty($result)) {
        echo '<div class="alert alert-primary" role="alert">Нет данных</div>';
        return ;
    }

    if(count($result['rows']) == 0){
        echo '<div class="alert alert-primary" role="alert">Хуки не созданы</div>';
        return ;
    }

    ?>
    <table class="table">
        <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Type</th>
            <th scope="col">Url</th>
            <th scope="col">Active</th>
            <th scope="col">Action</th>
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($result['rows'] as $key => $value) : ?>
            <tr>
                <th scope="row"><?php echo $value['id'] ?></th>
                <td><?php echo $value['entityType'] ?></td>
                <td><?php echo $value['url'] ?></td>
                <td>
                    <?php echo $value['enabled'] == 1 ? 'Да' : 'Нет'; ?>
                </td>
                <td><?php echo $value['action'] ?></td>
                <td><a href="<?php echo RestRoute::getUrlRoute('v1/webhook/delete/'.$value['id']); ?>" class="btn btn-danger" target="_blank">удалить</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}

function get_button_webhook_create()
{
    $create = RestRoute::getUrlRoute('v1/webhook/create');
    $delete = RestRoute::getUrlRoute('v1/webhook/delete');

    printf('<form action="" method="POST">
                 <a href="%s" class="btn btn-primary" target="_blank">Создать</a>
                 <a href="%s" class="btn btn-danger" target="_blank">удалить(удаляться только созданые на этом сайте)</a>

         </form>', $create, $delete);

}

