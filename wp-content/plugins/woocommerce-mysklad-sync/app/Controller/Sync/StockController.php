<?php


namespace WCSTORES\WC\MS\Controller\Sync;

use WCSTORES\WC\MS\Queues\Queues;

/**
 * Class StockController
 * @package WCSTORES\WC\MS\Controller\Sync
 */
class StockController extends SyncController
{
    /**
     * @param $type
     * @param $action
     * @param $href
     * @return mixed
     */
    public function webhook($type, $action, $href)
    {
        $wms_webhook_settings = get_option('wms_settings_webhook');

        if ($wms_webhook_settings['wms_active_webhook_stock'] == 'on') {
            $assortment = new \WmsStockController();
            $assortment->stock_webhook($href, $action);
        }

        return $type;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function settingAutomaticStartupSettings(): bool
    {

        if (!isset($_REQUEST['wms_settings_stock'])) {
            return false;
        }

        $settings = $_REQUEST['wms_settings_stock'];

        if (Queues::getQueuesCountToStatusPending('starting_stock_synchronization_automatic') > 0) {
            Queues::unscheduleAllActions('starting_stock_synchronization_automatic');
        }

        if (!isset($settings['wms_load_auto_stock']) || $settings['wms_load_auto_stock'] !== 'on') {
            return false;
        }

        if (!isset($settings['wms_load_auto_stock_time'])) {
            return false;
        }

        switch ($settings['wms_load_auto_stock_time']) {
            case 'hourly':
                $event_time = HOUR_IN_SECONDS;
                $m = 'один раз в час';
                break;
            case 'three_hourly':
                $event_time = HOUR_IN_SECONDS * 3;
                $m = 'каждые 3 часа';
                break;
            case 'six_hourly':
                $event_time = HOUR_IN_SECONDS * 6;
                $m = 'каждые 6 часов';
                break;
            case 'twicedaily':
                $event_time = (DAY_IN_SECONDS / 2);
                $m = 'два раза в день';
                break;
            default:
                $event_time = DAY_IN_SECONDS;
                $m = 'один раз в день';
                break;

        }


        \WmsLogs::set_logs('Остатки будут загружаться автоматически ' . $m, true);
        Queues::addRecurring(time() + $event_time, $event_time, 'starting_stock_synchronization_automatic');

        return true;
    }

    /**
     * @return int|void
     * @throws \Exception
     */
    public function startingStockSynchronizationAutomatic(): int
    {
        return Queues::addAsync('stock_synchronization_automatic');
    }

}