<?php
if (!defined('ABSPATH')) exit;

/**
 * Class Wms
 */
final class Wms
{

    /**
     * @var string
     */
    public $version = '1.10.21';


    /**
     * @var null
     */
    protected static $_instance = null;

    /**
     * @var bool
     */
    protected $isConnect = false;


    /**
     * @return Wms|null
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     *
     */
    public function __clone()
    {
    }

    /**
     *
     */
    public function __wakeup()
    {
    }

    /**
     * Wms constructor.
     */
    public function __construct()
    {
        $this->isConnect = get_option('wms_moysklad_is_connect');

        $this->define_constants();
        $this->includes();
        $this->update();
        $this->walker_register();
        $this->init_hooks();


        do_action('wms_loaded');
        do_action('wms_core_init_action');
    }

    /**
     *
     */
    private function init_hooks()
    {

        add_action('init', array($this, 'init'), 0);

        $option = get_option('wms_settings_auth');
        if (isset($option['wms_nonce'])) {
            $GLOBALS['wms_nonce'] = $option['wms_nonce'];
        } else {
            $GLOBALS['wms_nonce'] = 'wms_nonce_false';
        }

        add_action('wp_enqueue_scripts', 'wms_styles_front');

        if (isset($_REQUEST['page']) and $_REQUEST['page'] == 'wms-settings-page') {
            add_action('wms_monitor', 'wms_monitor_buttom', 50);
            WmsMonitor::init();
            WmsLogs::init();
        }



        if (isset($_REQUEST['wms_nonce'])) {
            if (isset($_REQUEST['action']) and $_REQUEST['action'] == 'wms_monitor' and $_REQUEST['wms_nonce'] == $GLOBALS['wms_nonce']) {
                WmsMonitor::init();
            }
        }

        if ($this->is_request('admin')) {
            add_action('admin_enqueue_scripts', 'wms_styles_admin');
        }

        if (wp_doing_ajax()) {
            add_action('wp_ajax_wms_start_sync', 'wms_start_sync');
            add_action('wp_ajax_nopriv_wms_start_sync', 'wms_start_sync');

            add_action('wp_ajax_wms_stop_sync', 'wms_stop_sync');
            add_action('wp_ajax_nopriv_wms_stop_sync', 'wms_stop_sync');

            add_action('wp_ajax_wms_add_style_states', 'add_style_states');
            add_action('wp_ajax_nopriv_wms_add_style_states', 'add_style_states');

            add_action('wp_ajax_wms_r_sync', 'wms_r_sync');
            add_action('wp_ajax_nopriv_wms_r_sync', 'wms_r_sync');
        }

        WmsWebhook::init();

    }


    /**
     *
     */
    private function define_constants()
    {
        $this->define("WMS_ROOT_DIR", dirname(__FILE__) . '/');
        $this->define("WMS_ADMIN_DIR", plugin_dir_url(__FILE__));
        $this->define('WMS_ABSPATH', dirname(WMS_PLUGIN) . '/');
        $this->define('WMS_PLUGIN_BASENAME', plugin_basename(WMS_PLUGIN));
        $this->define('WMS_VERSION', $this->version);
        $this->define("WMS_URL_API", 'https://api.moysklad.ru/api/remap/1.1/');
        $this->define("WMS_URL_API_V2", 'https://api.moysklad.ru/api/remap/1.2/');


    }

    /**
     * @param $name
     * @param $value
     */
    private function define($name, $value)
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    /**
     * @param $type
     * @return bool
     */
    private function is_request($type)
    {
        switch ($type) {
            case 'admin':
                return is_admin();
            case 'ajax':
                return defined('DOING_AJAX');
            case 'cron':
                return defined('DOING_CRON');
            case 'frontend':
                return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON');
        }

        return $type;
    }


    /**
     *
     */
    public function includes()
    {
        /**
         * Class autoloader.
         */
        include_once WMS_ABSPATH . 'includes/class-wms-autoload.php';

        include_once WMS_ABSPATH . 'includes/core/wms-helper.php';
        include_once WMS_ABSPATH . 'includes/core/wms-logs.php';

        $this->autoload_register();

        /**
         * Main.
         */
        include_once WMS_ABSPATH . 'includes/function-wms.php';
        include_once WMS_ABSPATH . 'includes/class-wms-update.php';


        /**
         * lib.
         */
        //include_once WMS_ABSPATH . 'includes/lib/mysklad/class-mysklad.php';


        include_once WMS_ABSPATH . 'includes/core/wms-org.php';
        include_once WMS_ABSPATH . 'includes/core/metabox/WmsGroupMetabox.php';
        include_once WMS_ABSPATH . 'includes/core/hook/wms-webhooks.php';

        include_once WMS_ABSPATH . 'includes/core/wms-product-function.php';

        include_once WMS_ABSPATH . 'includes/core/WmsWalker.php';

        include_once WMS_ABSPATH . 'includes/core/controller/WmsOrderController.php';
        include_once WMS_ABSPATH . 'includes/core/controller/WmsAssortmentController.php';
        include_once WMS_ABSPATH . 'includes/core/controller/WmsStockController.php';
        include_once WMS_ABSPATH . 'includes/core/controller/WmsImageController.php';
        include_once WMS_ABSPATH . 'includes/core/controller/WmsCounterpartyController.php';

        /**
         * Public.
         */
        include_once WMS_ABSPATH . 'public/WmsBundlePublic.php';

//OLD FILE
        include_once WMS_ABSPATH . 'includes/connect/wms-connect.php';
        //include_once WMS_ABSPATH . 'includes/core/old_file/wms-price.php';

        /**
         * Admin.
         */
        if ($this->is_request('admin')) {

            include_once WMS_ABSPATH . 'includes/lib/wdc-admin/class-wdc-admin.php';
            include_once WMS_ABSPATH . 'admin/wms-admin.php';
            include_once WMS_ABSPATH . 'admin/wms-auth.php';
            include_once WMS_ABSPATH . 'includes/core/wms-monitor.php';

            if(isset($_REQUEST['wms_page_button']) and isset($_REQUEST['wms_settings_auth']['wms_login']) and !empty($_REQUEST['wms_settings_auth']['wms_login'])){
                delete_option('wms_moysklad_is_connect');
                $this->isConnect = false;
            }

            if (isset($_REQUEST['page']) and $_REQUEST['page'] == 'wms-settings-page' and !$this->isConnect) {
                if($this->isConnect = WmsConnectApi::get_instance()->send_request(WMS_URL_API_V2 . '/entity/assortment/?limit=1')){
                    update_option('wms_moysklad_is_connect', true);
                }
            }


            if ($this->isConnect) {
                include_once WMS_ABSPATH . 'admin/wms-admin-product.php';
                include_once WMS_ABSPATH . 'admin/wms-admin-stock.php';
                include_once WMS_ABSPATH . 'admin/wms-admin-order.php';
                include_once WMS_ABSPATH . 'admin/wms-admin-counterparty.php';
                include_once WMS_ABSPATH . 'admin/wms-admin-webhook.php';
            } else {
                add_action('wdc_admin_page_before', function () {
                    echo '<div class="alert alert-danger" role="alert">';
                    echo 'Внимание указаны не верные доступы к Мой Склад!!! </br>';
                    echo 'Вам не доступны все настройки плагина.</br>';
                    echo 'Полный функционал будет доступен после указания верных доступов к Мой Склад';
                    echo '</div>';
                });
                delete_transient('wms_cache');
            }

        }
    }


    /**
     *
     */
    public function init()
    {
        // Before init action.
        do_action('before_wms_init');

        wms_order_hook_order_statuses();
        // Init action.
        do_action('wms_init');
    }


    /**
     * @return string
     */
    public function plugin_url()
    {
        return untrailingslashit(plugins_url('/', WC_PLUGIN_FILE));
    }


    /**
     * @return string
     */
    public function plugin_path()
    {
        return untrailingslashit(plugin_dir_path(WC_PLUGIN_FILE));
    }

    /**
     * @return string|void
     */
    public function ajax_url()
    {
        return admin_url('admin-ajax.php', 'relative');
    }


    /**
     *
     */
    public function autoload_register()
    {
        spl_autoload_register(['Wms_Autoload', 'autoload']);
    }

    /**
     *
     */
    public function walker_register()
    {
        WmsWalkerFactory::push_walker(new WmsWalker('assortment'));
        WmsWalkerFactory::push_walker(new WmsWalker('stock'));
        WmsWalkerFactory::push_walker(new WmsWalker('webhook_stock'));
        WmsWalkerFactory::push_walker(new WmsWalker('image'));
        WmsWalkerFactory::push_walker(new WmsWalker('counterparty'));
    }

    /**
     *
     */
    public function update()
    {
        new WmsUpdate('woocommerce-mysklad-sync', 'Интеграция МойСклад и WooCommerce', 'Интеграция МойСклад и WooCommerce', 'https://mswoo.ru/', 'plugin', WMS_PLUGIN);
    }

}

