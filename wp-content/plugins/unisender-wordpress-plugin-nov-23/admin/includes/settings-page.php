<?php
// create custom plugin settings menu
add_action('admin_menu', 'unisender_create_menu');

function unisender_create_menu()
{

    add_submenu_page('wpunisender',
        __('Настройки Unisender', 'unisender'),
        __('Настройки', 'unisender'),
        'wpunisender_manage_options',
        'wpunisender-settings',
        'unisender_settings_page'
    );

    //call register settings function
    add_action('admin_init', 'register_unisender_settings');
}


function register_unisender_settings()
{
    //register our settings
    register_setting('unisender-settings-group', 'wpunisender_api_key');
}

function unisender_settings_page()
{
    ?>
    <div class="wrap">
        <h1><?php echo __('Настройки Unisender', 'unisender') ?></h1>

        <form method="post" action="options.php">
            <?php settings_fields('unisender-settings-group'); ?>
            <?php do_settings_sections('unisender-settings-group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">API key</th>
                    <td>
                        <input type="text" name="wpunisender_api_key" value="<?php echo esc_attr(get_option('wpunisender_api_key')); ?>" class="large-text"/>
                        <a href="https://www.unisender.com/ru/support/api/common/api-key/" target="_blank" style="display: inline-block; margin-top: 10px;">Где взять API-ключ Unisender</a>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>

        </form>
    </div>
<?php } ?>