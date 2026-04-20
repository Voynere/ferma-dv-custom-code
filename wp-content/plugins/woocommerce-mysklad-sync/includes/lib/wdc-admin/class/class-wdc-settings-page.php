<?php
/**
 * Created by PhpStorm.
 * User: mswoo
 * Date: 23.05.2018
 * Time: 9:36
 */

class Wdc_Settings_Page
{

    /**
     * @var array
     */
    private $page = array(
        'page_title' => 'Custom Settings Page',
        'menu' => 'Custom Settings Page',
        'capability' => 'manage_options',
        'slug' => 'wph_custom',
        'icon' => 'dashicons-admin-settings',
        'position' => 2,
    );

    /**
     * @var array
     */
    private $menu = array(
        array(
            'name' => 'Custom',
            'href' => 'Custom',
            'class' => 'active',
        ),
        array(
            'name' => 'Custom2',
            'href' => 'Custom2',
        ),
        array(
            'name' => 'Custom3',
            'href' => 'Custom3',
        ),
    );


    /**
     * @var array
     */
    private $content = array(
        array(
            'name' => 'Custom Settings Page',
            'href' => 'Custom',
            'action' => 'manage_options',
            'class' => 'show active',
        ),
        array(
            'name' => 'Custom Settings Page2',
            'href' => 'Custom2',
            'action' => 'manage_options2',
        ),
        array(
            'name' => 'Custom Settings Page3',
            'href' => 'Custom3',
            'action' => 'manage_options3',
        ),
    );


    /**
     * Wdc_Settings_Page constructor.
     *
     * @param array $page
     */
    public function __construct($page = array())
    {
        $this->page = $page;
    }

    /**
     *
     */
    public function create()
    {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('wdc_admin_page_menu', array($this, 'settings_menu'));
        add_action('wdc_admin_page_content', array($this, 'settings_content'));
    }

    /**
     *
     */
    public function add_settings_page()
    {
        $page_title = $this->page['page_title'];
        $menu_title = $this->page['menu'];
        $capability = $this->page['capability'];
        $slug = $this->page['slug'];
        $callback = array($this, 'settings_page');
        $icon = $this->page['icon'];
        $position = $this->page['position'];
        $page = add_menu_page($page_title, $menu_title, $capability, $slug, $callback, $icon, $position);

        add_action('admin_print_scripts-' . $page, array($this, 'enqueue_styles_scripts'));
    }

    /**
     *
     */
    public function settings_page()
    { ?>

        <div class="container">

            <?php do_action('wdc_admin_page_before'); ?>

            <h1><?php echo $this->page['page_title'] ?></h1>

            <?php settings_errors(); ?>

            <div class="row">

                <?php do_action('wdc_admin_page_menu_before'); ?>

                <?php do_action('wdc_admin_page_menu'); ?>

                <?php do_action('wdc_admin_page_menu_after'); ?>
            </div>

            <div class="row">

                <?php do_action('wdc_admin_page_content_before'); ?>

                <?php do_action('wdc_admin_page_content'); ?>

                <?php do_action('wdc_admin_page_content_after'); ?>
            </div>

            <?php do_action('wdc_admin_page_before_script'); ?>

            <script>
                wms_tab();
                wms_button_add_style();
                setInterval(function () {

                    jQuery.ajax({
                        url: "/wp-admin/admin-ajax.php",
                        method: 'post',
                        data: {
                            action: 'wms_monitor',


                        },
                        success: function (response) {
                            jQuery('#wms-main-monitor').html(response.substr(response.length - 1, 1) === '0' ? response.substr(0, response.length - 1) : response);
                        }
                    });
                }, 1000 * 60);
            </script>

            <?php do_action('wdc_admin_page_after_script'); ?>

            <?php do_action('wdc_admin_page_after'); ?>

        </div>

        <?php
    }


    /**
     *
     */
    public function settings_menu()
    { ?>
        <nav>
            <div class="tab-active"></div>
            <div class="tab-previous"></div>
            <ul class="nav nav-pills mb-3" id="v-pills-tab" role="tablist">

                <?php foreach ($this->get_menu() as $key => $value) : ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $value['class']; ?>" id="pills-<?php echo $value['href']; ?>-tab"
                           data-toggle="pill"
                           href="#<?php echo $value['href']; ?>" role="tab"
                           aria-controls="<?php echo $value['href']; ?>"
                           aria-selected="false"><?php echo $value['name']; ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <?php
    }


    /**
     *
     */
    public function settings_content()
    { ?>

        <div class="tab-content" id="pills-tabContent">
            <?php foreach ($this->get_content() as $key => $value) : ?>
                <div class="tab-pane <?php echo $value['class']; ?>" id="<?php echo $value['href']; ?>"
                     role="tabpanel" aria-labelledby="<?php echo $value['href']; ?>-tab">
                    <h4><?php echo $value['name'] ?></h4>
                    <?php do_action($value['action']); ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php
    }

    /**
     *
     * @since    2.0.0
     */
    public function enqueue_styles_scripts()
    {
    
        if (isset($_REQUEST['page']) and $_REQUEST['page'] == 'wms-settings-page') {

            wp_enqueue_style('wdc-bootstrap-grid-min-css', WDC_ADMIN_DIR . 'css/bootstrap-grid.min.css');
            wp_enqueue_style('wdc-bootstrap-min-css', WDC_ADMIN_DIR . 'css/bootstrap.min.css');

            wp_enqueue_script('wdc-bootstrap-min-js', WDC_ADMIN_DIR . 'js/bootstrap.min.js', array('jquery'));
        }


    }

    /**
     * @return array
     */
    public function get_menu()
    {
        return apply_filters('wdc_admin_menu', $this->menu);
    }

    /**
     * @param array $menu
     */
    public function set_menu($menu)
    {
        $this->menu = $menu;
    }

    /**
     * @return array
     */
    public function get_content()
    {
        return apply_filters('wdc_admin_content', $this->content);
    }

    /**
     * @param array $content
     */
    public function set_content($content)
    {
        $this->content = $content;
    }

}