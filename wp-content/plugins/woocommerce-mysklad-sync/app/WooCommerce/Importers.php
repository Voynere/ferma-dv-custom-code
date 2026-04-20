<?php


namespace WCSTORES\WC\MS\WooCommerce;

use ActionScheduler_Action;
use Exception;
use WCSTORES\WC\MS\Queues\Queues;
use WCSTORES\WC\MS\WooCommerce\Utilities\QueueUtil;


/**
 * Class Importers
 * @package WCSTORES\WC\MS\WooCommerce
 */
class Importers
{
    /**
     * @return array
     */
    public static function get_importer_default_args(): array
    {
        return array(
            'uuid' => null,
            'time_start' => null,
            'time_end' => null,
            'name' => 'import',
            'class_data_processing' => '',
            'file' => null,
            'link' => null,
            'language' => 'ru',
            'limit' => 20,
            'offset' => 0,
            'hash' => wp_hash(time()),
            'count_product' => 0,
            'sync_product' => 0,
            'imported' => 0,
            'failed' => 0,
            'updated' => 0,
            'skipped' => 0,
            'localization' => 0,
        );
    }

    /**
     * @param $enqueue_uuid
     * @param $args
     * @return array|object|string
     */
    protected static function getImporterArgs($enqueue_uuid, $args)
    {
        if (!$enqueue_uuid) {
            $enqueue_uuid = wp_generate_uuid4();
            $default_args = self::get_importer_default_args();
            $args = wp_parse_args($args, $default_args);
        }

        $args['uuid'] = $enqueue_uuid;

        return $args;
    }

    /**
     * @param array $args
     * @param null $enqueue_uuid
     * @return mixed|string
     */
    public static function setImporters($args = [], $enqueue_uuid = null)
    {
        $importers = self::getImporters();

        if (!$enqueue_uuid || !isset($importers[$enqueue_uuid])) {
            $importer = self::getImporterArgs(null, $args);
        } else {
            $importer = $importers[$enqueue_uuid];
        }

        if (!$importer) {
            return false;
        }

        foreach ($args as $key => $value) {
            $importer[$key] = $value;
        }

        $importers[$importer['uuid']] = $importer;
        update_option('wcstores_woocommerce_importers', array_keys($importers));
        set_transient('wcstores_woocommerce_importer_' . $importer['uuid'], $importer, (DAY_IN_SECONDS * 3));

        return $importer['uuid'];
    }

    /**
     * @return false|mixed|void|null
     */
    public static function getImporters()
    {

        $importers = [];
        $importer_uuids = get_option('wcstores_woocommerce_importers', []);

        if (!$importer_uuids) {
            return $importers;
        }

        foreach ($importer_uuids as $importer_uuid) {
            if ($importer = get_transient('wcstores_woocommerce_importer_' . $importer_uuid)) {
                $importers[$importer_uuid] = self::getImporterArgs($importer_uuid, $importer);
            }
        }

        return array_reverse($importers);
    }

    /**
     * @param $importer_uuid
     * @param bool $un_schedule_all
     * @param string $name
     * @return array
     * @throws Exception
     */
    public static function getImporter($importer_uuid, $un_schedule_all = false, $name = ''): array
    {
        $importers = self::getImporters();

        if ((isset($importers[$importer_uuid]))) {
            return $importers[$importer_uuid];
        }

        if($un_schedule_all){
            Queues::unscheduleAllActions($name, array($importer_uuid), 'wc_ms_queues');
            self::uninstallImportVerification($name, $importer_uuid);
        }

        return [];
    }


    /**
     * @return bool
     */
    public static function deleteAll(): bool
    {
        $importer_uuids = get_option('wcstores_woocommerce_importers', []);

        foreach ($importer_uuids as $importer_uuid) {
            delete_transient('wcstores_woocommerce_importer_' . $importer_uuid);
        }

        return delete_option('wcstores_woocommerce_importers');
    }

    /**
     * @param $importer_uuid
     * @return false
     */
    public static function deleteImporter($importer_uuid): bool
    {
        $importers = get_option('wcstores_woocommerce_importers', array());
        $importer = array_search($importer_uuid, $importers);

        if ($importer === false) {
            return false;
        }

        delete_transient('wcstores_woocommerce_importer_' . $importer_uuid);
        unset($importers[$importer]);

        return update_option('wcstores_woocommerce_importers', $importers);

    }


    /**
     * @param $name
     * @param array $args
     * @return false|int
     * @throws Exception
     */
    public static function startImport($name, $args = array())
    {
        sleep(rand(1, 10));

        $default_args = array(
            'name' => $name,
            'link' => null,
            'hash' => wp_hash($name),
            'language' => 'ru',
            'time_start' => time(),
            'class_data_processing' => '',
        );

        $args = wp_parse_args($args, $default_args);

        if(!self::checkStartImport($name, $args)){
            return false;
        }

        if ($queue_uuid = self::setImporters($args)) {
            return Queues::addAsync($name, array($queue_uuid), 'wc_ms_queues', true);
        }

        return false;
    }


    /**
     * @param $name
     * @param $args
     * @return bool
     * @throws Exception
     */
    public static function checkStartImport($name, $args): bool
    {

        $queues = Queues::get(
            array(
                'hook' => $name,
                'status' => array('pending'),
            )
        );

        if(!$queues){
            return true;
        }

        foreach ($queues as $queue){
            if($queue instanceof ActionScheduler_Action){

                $queue_args = $queue->get_args();

                if(!isset($queue_args[0]) || !$importer = self::getImporter($queue_args[0], false)){
                    continue;
                }

                if($importer['language'] === $args['language'] && $importer['file'] === $args['file']){
                    return false;
                }

            }
        }

        return true;
    }

    /**
     * @param $name
     * @param $importer_uuid
     * @param int $interval
     * @return false|int
     * @throws Exception
     */
    public static function installImportVerification($name, $importer_uuid, int $interval = 0): bool|int
    {
        Queues::unscheduleAllActions($name, array($importer_uuid));

        $interval = $interval > 0 ? $interval : (60 * 20);

        return Queues::addSingle(
            time() + $interval,
            $name . '_check',
            array($importer_uuid),
            'wc_ms_queues',
            true
        );
    }

    /**
     * @param $name
     * @param $importer_uuid
     * @return false|mixed
     * @throws Exception
     */
    public static function uninstallImportVerification($name, $importer_uuid): mixed
    {
        return Queues::unscheduleAllActions($name . '_check', array($importer_uuid), 'wc_ms_queues');
    }

    /**
     * @param $name
     * @param $importer_uuid
     * @return false|mixed|void
     * @throws Exception
     */
    public static function endImportStep($name, $importer_uuid)
    {
        return Queues::unscheduleAllActions($name . '_check', [$importer_uuid], 'wc_ms_queues');
    }

    /**
     * @param $name
     * @param $importer_uuid
     * @param $offset
     * @param float|int $interval
     * @return int
     * @throws Exception
     */
    public static function nextImportStep($name, $importer_uuid, $offset, float|int $interval = (60 * 2))
    {
        return Queues::addSingle(time() + $interval, $name, array($importer_uuid, $offset), 'wc_ms_queues');
    }


    public static function endCurrentImportStep($queue_id, $name, $importer_uuid, $run = false)
    {
        Importers::endImportStep($name, $importer_uuid);

        if($run){
            QueueUtil::runQueueAsync($queue_id);
        }

        return $queue_id;

    }

    /**
     * @param $name
     * @param $importer_uuid
     * @throws Exception
     */
    public static function endImport($name, $importer_uuid)
    {
        $args = array(
            'time_end' => time(),
        );

        self::setImporters($args, $importer_uuid);
        self::endImportStep($name, $importer_uuid);
        self::uninstallImportVerification($name, $importer_uuid);
    }

}
