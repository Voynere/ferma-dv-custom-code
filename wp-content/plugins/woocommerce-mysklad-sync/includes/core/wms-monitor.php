<?php
if (!defined('ABSPATH')) exit;

/**
 *
 */
class WmsMonitor
{

    static public function init()
    {
        add_action('wdc_admin_page_menu_before', array('WmsMonitor', 'get_monitor_div'), 10);
        add_action('wms_monitor', array('WmsMonitor', 'get_monitor_html'), 10);
        add_action('wp_ajax_wms_monitor', array('WmsMonitor', 'get_monitor_html_ajax'));
        add_action('wp_ajax_nopriv_wms_monitor', array('WmsMonitor', 'get_monitor_html_ajax'));

    }


    static function get_monitor_load($type, $button = '')
    {
        $start = get_option($type);

        if (!is_array($start)) {
            return ' <span class = "$type"> Нет даных</span>';
        }

        $load = (isset($start['load'])) ? $start['load'] : '';
        $message = (isset($start['message'])) ? $start['message'] : 'Нет даных';
        $size = (isset($start['size']) and $start['size'] !== 0) ? $start['size'] : 100;
        $sTime = (isset($start['time'])) ? $start['time'] : 'Нет данных';
        $printMessage = ' <span class = "$type">' . $message . '</span>';

        if ($load== 'start' or $load == 'load') { ?>
            <script> wms_load_button_sync('<?php echo $button;?>'); </script>
        <?php }

        if ($load === 'load' and $type === 'wms_image_update_start') {
            echo $printMessage;
            return true;
        }


        if ($load === 'load' and $type !== 'wms_image_update_start' or isset($_POST['start']) and $_POST['start'] == 'start') {

            $message = '<span> Обработано записей ' . $start['count'] . '</span><progress value="' . $start['count'] . '" max="' . $size . '"></progress>';
            $message .=  'Обновновляется... ' .  round($start['count'] * 100 / $start['size']) . '% <span> Всего записей ' . $size . '</span>';
            echo $message;
            return true;
        }

        if ($load === 'start') {
            echo $printMessage;
            return true;

        }

        echo $sTime . $printMessage;

        if ($load === 'stop') { ?>
            <script> wms_stop_button_sync('<?php echo $button;?>', 'Синхронизировать'); </script>
        <?php }

        return true;
    }

    static function get_monitor_div()
    {
        ?>
        <div class="wms-monitor">
            <?php do_action('wms_monitor'); ?>
        </div>
        <?php
    }

    static function get_monitor_html()
    {
        ?>
        <div class="wms-main-monitor" id="wms-main-monitor">

            <?php $alerts = apply_filters('wms_monitor_alert_data', []); ?>

            <?php if (!empty($alerts)) : ?>

                <?php foreach($alerts as $alert) : ?>

                    <div class="wms-main-load-data" id="wms-main-load-data">
                        <div class="alert alert-<?php echo $alert['type']; ?>" role="alert">
                            <?php echo $alert['message']; ?>
                        </div>
                    </div>

                <?php endforeach; ?>

            <?php endif; ?>


            <div class="product-monitor">
                <div> Последняя синхронизация товаров была: <span class="wms-monitor-data "
                                                                  id="product-monitor-id"><?php self::get_monitor_load('wms_product_update_start', '.loadproduct'); ?></span>
                </div>

            </div>
            <div class="stock-monitor">
                <div> Последняя синхронизация остатков была: <span class="wms-monitor-data "
                                                                   id="stock-monitor-id"><?php self::get_monitor_load('wms_stock_update_start', '.loadstock'); ?></span>
                </div>

            </div>

            <div class="image-monitor">
                <div> Последняя синхронизация изображений была: <span class="wms-monitor-data "
                                                                      id="image-monitor-id"><?php self::get_monitor_load('wms_image_update_start'); ?></span>
                </div>

            </div>
            <div class="counterparty-monitor">
                <div> Последняя синхронизация контрагентов была: <span class="wms-monitor-data "
                                                                       id="counterparty-monitor-id"><?php self::get_monitor_load('wms_counterparty_update_start', '.loadcounterparty'); ?></span>
                </div>

            </div>
        </div>


        <?php
    }

    static function get_monitor_html_ajax()
    {
        self::get_monitor_html();
        wp_die();
    }


}
