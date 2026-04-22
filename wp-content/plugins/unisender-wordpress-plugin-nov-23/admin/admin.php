<?php

require_once WPUNISENDER_PLUGIN_DIR . '/admin/includes/admin-functions.php';
require_once WPUNISENDER_PLUGIN_DIR . '/admin/includes/settings-page.php';
require_once WPUNISENDER_PLUGIN_DIR . '/admin/includes/builder-assets.php';


add_action(
    'admin_menu',
    'wpunisender_admin_menu',
    9, 0
);

function wpunisender_admin_menu()
{
    do_action('wpunisender_admin_menu');

    add_menu_page(
        __('Unisender', 'unisender'),
        __('Unisender', 'unisender'),
        'wpunisender_read_forms',
        'wpunisender',
        'wpunisender_admin_management_page',
        'dashicons-email',
        30
    );

    $edit = add_submenu_page('wpunisender',
        __('Редактировать форму', 'unisender'),
        __('Формы', 'unisender'),
        'wpunisender_read_forms',
        'wpunisender',
        'wpunisender_admin_management_page'
    );

    add_action('load-' . $edit, 'wpunisender_load_form_admin', 10, 0);

    $addnew = add_submenu_page('wpunisender',
        __('Добавить новую форму', 'unisender'),
        __('Добавить новую', 'unisender'),
        'wpunisender_edit_forms',
        'wpunisender-new',
        'wpunisender_admin_add_new_page'
    );

    add_action('load-' . $addnew, 'wpunisender_load_form_admin', 10, 0);
}

add_action(
    'admin_enqueue_scripts',
    'wpunisender_admin_enqueue_scripts',
    10, 1
);

function wpunisender_admin_enqueue_scripts($hook_suffix)
{
    if (false === strpos($hook_suffix, 'wpunisender')) {
        return;
    }

    wp_enqueue_style('unisender-admin',
        wpunisender_plugin_url('admin/css/styles.css'),
        array(), WPUNISENDER_VERSION, 'all'
    );

    $css_urls = wpunisender_get_builder_css_urls();
    foreach ($css_urls as $name => $css_url) {
        wp_enqueue_style($name, $css_url, [], '1.0.0', 'all');
    }

    $js_urls = wpunisender_get_builder_js_urls();
    foreach ($js_urls as $name => $js_url) {
        wp_enqueue_script($name, $js_url, [], '1.0.0', 'all');
    }
}


add_filter(
    'set_screen_option_wpunisender_forms_per_page',
    function ($result, $option, $value) {
        $wpunisender_screens = array(
            'wpunisender_forms_per_page',
        );

        if (in_array($option, $wpunisender_screens)) {
            $result = $value;
        }

        return $result;
    },
    10, 3
);


function wpunisender_load_form_admin()
{
    global $plugin_page;

    $action = wpunisender_current_action();

    do_action('wpunisender_admin_load',
        isset($_GET['page']) ? trim($_GET['page']) : '',
        $action
    );

    if ('save' == $action) {
        $id = isset($_POST['post_ID']) ? $_POST['post_ID'] : '-1';
        check_admin_referer('wpunisender-save-form_' . $id);

        if (!current_user_can('wpunisender_edit_form', $id)) {
            wp_die(
                __("Вам не разрешено редактировать этот элемент.", 'unisender')
            );
        }

        $args = $_REQUEST;
        $args['id'] = $id;

        $args['title'] = isset($_POST['post_title'])
            ? $_POST['post_title'] : null;

        $args['form'] = isset($_POST['wpunisender-form'])
            ? $_POST['wpunisender-form'] : '';

        $args['contact_list'] = isset($_POST['wpunisender-contact-list'])
            ? $_POST['wpunisender-contact-list'] : '';


        $form = wpunisender_save_form($args);

        $query = array(
            'post' => $form ? $form->id() : 0,
        );

        if (!$form) {
            $query['message'] = 'failed';
        } elseif (-1 == $id) {
            $query['message'] = 'created';
        } else {
            $query['message'] = 'saved';
        }

        $redirect_to = add_query_arg($query, menu_page_url('wpunisender', false));
        wp_safe_redirect($redirect_to);
        exit();
    }

    if ('copy' == $action) {
        $id = empty($_POST['post_ID'])
            ? absint($_REQUEST['post'])
            : absint($_POST['post_ID']);

        check_admin_referer('wpunisender-copy-form_' . $id);

        if (!current_user_can('wpunisender_edit_form', $id)) {
            wp_die(
                __("Вам не разрешено редактировать этот элемент.", 'unisender')
            );
        }

        $query = array();

        if ($form = wpunisender_form($id)) {
            $new_form = $form->copy();
            $new_form->save();

            $query['post'] = $new_form->id();
            $query['message'] = 'created';
        }

        $redirect_to = add_query_arg($query, menu_page_url('wpunisender', false));

        wp_safe_redirect($redirect_to);
        exit();
    }

    if ('delete' == $action) {
        if (!empty($_POST['post_ID'])) {
            check_admin_referer('wpunisender-delete-form_' . $_POST['post_ID']);
        } elseif (!is_array($_REQUEST['post'])) {
            check_admin_referer('wpunisender-delete-form_' . $_REQUEST['post']);
        } else {
            check_admin_referer('bulk-posts');
        }

        $posts = empty($_POST['post_ID'])
            ? (array)$_REQUEST['post']
            : (array)$_POST['post_ID'];

        $deleted = 0;

        foreach ($posts as $post) {
            $post = WPUNISENDER_Form::get_instance($post);

            if (empty($post)) {
                continue;
            }

            if (!current_user_can('wpunisender_delete_form', $post->id())) {
                wp_die(
                    __("Вам не разрешено удалять этот элемент.", 'unisender')
                );
            }

            if (!$post->delete()) {
                wp_die(__("Ошибка при удалении.", 'unisender'));
            }

            $deleted += 1;
        }

        $query = array();

        if (!empty($deleted)) {
            $query['message'] = 'deleted';
        }

        $redirect_to = add_query_arg($query, menu_page_url('wpunisender', false));

        wp_safe_redirect($redirect_to);
        exit();
    }

    $post = null;

    if ('wpunisender-new' == $plugin_page) {
        $post = WPUNISENDER_Form::get_template(array(
            'locale' => isset($_GET['locale']) ? $_GET['locale'] : null,
        ));
    } elseif (!empty($_GET['post'])) {
        $post = WPUNISENDER_Form::get_instance($_GET['post']);
    }

    $current_screen = get_current_screen();

    if (!$post) {

        if (!class_exists('WPUNISENDER_Form_List_Table')) {
            require_once WPUNISENDER_PLUGIN_DIR . '/admin/includes/class-forms-list-table.php';
        }

        add_filter(
            'manage_' . $current_screen->id . '_columns',
            array('WPUNISENDER_Form_List_Table', 'define_columns'),
            10, 0
        );

        add_screen_option('per_page', array(
            'default' => 20,
            'option' => 'wpunisender_forms_per_page',
        ));
    }
}


function wpunisender_admin_management_page()
{
    if ($post = wpunisender_get_current_form()) {
        $post_id = $post->initial() ? -1 : $post->id(); //uses in edit-form.php
        require_once WPUNISENDER_PLUGIN_DIR . '/admin/edit-form.php';
        return;
    }

    $list_table = new WPUNISENDER_Form_List_Table();
    $list_table->prepare_items();

    ?>
    <div class="wrap" id="wpunisender-form-list-table">

        <h1 class="wp-heading-inline"><?php
            echo esc_html(__('Формы', 'unisender'));
            ?></h1>

        <?php
        if (current_user_can('wpunisender_edit_forms')) {
            echo wpunisender_link(
                menu_page_url('wpunisender-new', false),
                __('Добавить новую', 'unisender'),
                array('class' => 'page-title-action')
            );
        }

        if (!empty($_REQUEST['s'])) {
            echo sprintf(
                '<span class="subtitle">'
                /* translators: %s: search keywords */
                . __('Результаты поиска по запросу &#8220;%s&#8221;', 'unisender')
                . '</span>',
                esc_html($_REQUEST['s'])
            );
        }
        ?>

        <hr class="wp-header-end">

        <?php
        do_action('wpunisender_admin_warnings',
            'wpunisender', wpunisender_current_action(), null
        );

        do_action('wpunisender_admin_notices',
            'wpunisender', wpunisender_current_action(), null
        );
        ?>

        <form method="get" action="">
            <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>"/>
            <?php $list_table->search_box(__('Формы поиска', 'unisender'), 'wpunisender-form'); ?>
            <?php $list_table->display(); ?>
        </form>

    </div>
    <?php
}


function wpunisender_admin_add_new_page()
{
    $post = wpunisender_get_current_form();

    if (!$post) {
        $post = WPUNISENDER_Form::get_template();
    }

    $post_id = -1;

    require_once WPUNISENDER_PLUGIN_DIR . '/admin/edit-form.php';
}


add_action('wpunisender_admin_notices', 'wpunisender_admin_updated_message', 10, 3);

function wpunisender_admin_updated_message($page, $action, $object)
{
    if (!in_array($page, array('wpunisender', 'wpunisender-new'))) {
        return;
    }

    if (empty($_REQUEST['message'])) {
        return;
    }

    if ('created' == $_REQUEST['message']) {
        $updated_message = __("Форма создана.", 'unisender');
    } elseif ('saved' == $_REQUEST['message']) {
        $updated_message = __("Форма сохранена.", 'unisender');
    } elseif ('deleted' == $_REQUEST['message']) {
        $updated_message = __("Форма удалена.", 'unisender');
    }

    if (!empty($updated_message)) {
        echo sprintf(
            '<div id="message" class="notice notice-success"><p>%s</p></div>',
            esc_html($updated_message)
        );

        return;
    }

    if ('failed' == $_REQUEST['message']) {
        $updated_message =
            __("Произошла ошибка при удалении формы.", 'unisender');

        echo sprintf(
            '<div id="message" class="notice notice-error"><p>%s</p></div>',
            esc_html($updated_message)
        );

        return;
    }
}

add_action('admin_notices', 'wpunisender_admin_settings_notice');

/**
 * Displays an admin notice when WordPress is set to discourage search engines from indexing the site.
 *
 * @return void
 */
function wpunisender_admin_settings_notice()
{
    // settings updated success notice
    if ($GLOBALS['pagenow'] === 'admin.php' && filter_input(INPUT_GET, 'page') === 'wpunisender-settings'
    && $_GET['settings-updated'] === 'true') {
        printf('<div id="settingsupdated" class="notice notice-success"><p>%1$s</p></div>',
        __('Настройки сохранены.', 'unisender')
        );
    }

    // settings api key error notice
    if (!wpunisender_admin_should_display_settings_notice()) {
        return;
    }

    printf(
        '<div id="apikeymessage" class="notice notice-error"><p>%1$s</p></div>',
        sprintf(
        /* translators: 1: Link start tag to the Unisender Settings page, 2: Link closing tag. */
            esc_html__('Для начала работы с Unisender, вам необходимо указать API Key в %1$sнастройках плагина%2$s.',
                'unisender'),
            '<a href="' . esc_url(menu_page_url('wpunisender-settings', false)) . '">',
            '</a>'
        )
    );
}

function wpunisender_admin_should_display_settings_notice()
{
    $discouraged_pages = [
        'index.php',
        'plugins.php',
        'update-core.php',
    ];

    $unisender_pages = [
        'wpunisender',
        'wpunisender-new',
    ];

    return (
        empty(get_option('wpunisender_api_key'))
        && current_user_can('wpunisender_manage_options')
        && (
            $GLOBALS['pagenow'] === 'admin.php' && in_array(filter_input(INPUT_GET, 'page'), $unisender_pages)
            || in_array($GLOBALS['pagenow'], $discouraged_pages, true)
        )
    );
}

add_filter('plugin_action_links', 'wpunisender_plugin_action_links', 10, 2);

function wpunisender_plugin_action_links($links, $file)
{
    if ($file != WPUNISENDER_PLUGIN_BASENAME) {
        return $links;
    }

    if (!current_user_can('wpunisender_read_forms')) {
        return $links;
    }

    $settings_link = wpunisender_link(
        menu_page_url('wpunisender-settings', false),
        __('Настройки', 'unisender')
    );

    array_unshift($links, $settings_link);

    return $links;
}


add_action('wpunisender_admin_warnings', 'wpunisender_old_wp_version_error', 10, 3);

function wpunisender_old_wp_version_error($page, $action, $object)
{
    $wp_version = get_bloginfo('version');

    if (!version_compare($wp_version, WPUNISENDER_REQUIRED_WP_VERSION, '<')) {
        return;
    }

    ?>
    <div class="notice notice-warning">
        <p><?php
            echo sprintf(
            /* translators: 1: version of Unisender, 2: version of WordPress, 3: URL */
                __('<strong>Unisender %1$s требует WordPress %2$s или новее.</strong> Пожалуйста, сначала <a href="%3$s">обновите WordPress</a>.',
                    'unisender'),
                WPUNISENDER_VERSION,
                WPUNISENDER_REQUIRED_WP_VERSION,
                admin_url('update-core.php')
            );
            ?></p>
    </div>
    <?php
}


add_action('wpunisender_admin_warnings', 'wpunisender_not_allowed_to_edit', 10, 3);

function wpunisender_not_allowed_to_edit($page, $action, $object)
{
    if ($object instanceof WPUNISENDER_Form) {
        $form = $object;
    } else {
        return;
    }

    if (current_user_can('wpunisender_edit_form', $form->id())) {
        return;
    }

    $message = __("Вам не разрешено редактировать эту форму.", 'unisender');

    echo sprintf(
        '<div class="notice notice-warning"><p>%s</p></div>',
        esc_html($message)
    );
}
