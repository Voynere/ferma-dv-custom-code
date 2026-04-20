<?php

declare(strict_types=1);

namespace WCSTORES\WC\MS\Controller\Sync;

use WCSTORES\WC\MS\Queues\Queues;
use WCSTORES\WC\MS\WooCommerce\Importers;
use WmsAssortmentController;
use WmsLogs;

/**
 * Controller for product (assortment) synchronization.
 *
 * @package WCSTORES\WC\MS\Controller\Sync
 */
class AssortmentController extends SyncController
{
    public const IMPORTER_NAME = 'sync_products';

    public const QUEUE_STARTING_AUTOMATIC = 'starting_products_synchronization_automatic';

    public const QUEUE_PRODUCTS_AUTOMATIC = 'products_synchronization_automatic';

    private const OPTION_UPDATE_START = 'wms_product_update_start';

    private const OPTION_UPDATE_START_TIME = 'wms_product_update_start_time';

    private const OPTION_WEBHOOK = 'wms_settings_webhook';

    private const OPTION_PRODUCT_SETTINGS = 'wms_settings_product';


    /**
     * Configures automatic product sync schedule from request settings.
     *
     * @throws \Exception
     */
    public function settingAutomaticStartupSettings()
    {
        if (!isset($_REQUEST[self::OPTION_PRODUCT_SETTINGS])) {
            return false;
        }

        $settings = wp_unslash($_REQUEST[self::OPTION_PRODUCT_SETTINGS]);

        if (Queues::getQueuesCountToStatusPending(self::QUEUE_STARTING_AUTOMATIC) > 0) {
            Queues::unscheduleAllActions(self::QUEUE_STARTING_AUTOMATIC);
        }

        if (!isset($settings['wms_load_auto_product']) || $settings['wms_load_auto_product'] !== 'on') {
            return false;
        }

        WmsLogs::set_logs('Товары будут загружаться автоматически один раз в день', true);

        Queues::addRecurring(
            time() + DAY_IN_SECONDS,
            DAY_IN_SECONDS,
            self::QUEUE_STARTING_AUTOMATIC
        );

        return true;
    }

    /**
     * Schedules one-off automatic products synchronization.
     *
     * @throws \Exception
     */
    public function startingSynchronizationAutomatic(): int
    {
        return Queues::addAsync(self::QUEUE_PRODUCTS_AUTOMATIC);
    }

    /**
     * Handles webhook for product/assortment changes.
     * @throws \Exception
     */
    public function webhook( $type,  $action,  $href): string
    {
        $wms_webhook_settings = get_option(self::OPTION_WEBHOOK, []);

        if (
            is_array($wms_webhook_settings)
            && isset($wms_webhook_settings['wms_active_webhook_product'])
            && $wms_webhook_settings['wms_active_webhook_product'] === 'on'
        ) {
            $assortment = new WmsAssortmentController();
            $assortment->assortment_webhook($href, $action, false);
        }

        return $type;
    }

    /**
     * Starts product synchronization (creates importer and first queue job).
     *
     * @return array{success: bool, data: array, errors: array}
     * @throws \Exception
     */
    public function start(): array
    {
        $importer = Importers::startImport(self::IMPORTER_NAME);

        if (!$importer) {
            return $this->responseError(['Not start sync']);
        }

        WmsLogs::set_logs('Стартуем (синхронизация товаров)', true);
        update_option(self::OPTION_UPDATE_START_TIME, current_time('Y-m-d'));
        update_option(self::OPTION_UPDATE_START, [
            'load' => 'start',
            'message' => 'Начало полной синхронизации...',
        ]);

        return $this->responseOk(['message' => 'Sync ok']);
    }

    /**
     * Processes one batch of assortment sync (called by queue).
     *
     * @param string $importerUuid Importer UUID from queue args.
     * @param int    $offset       Offset for this batch (or 0 to use importer's offset).
     * @return array|false|int Array ['ok'] when sync finished, int queue_id when step done, false if importer invalid.
     * @throws \Exception
     */
    public function sync(string $importerUuid, int $offset = 0): array|false|int
    {
        $importer = $this->getAndValidateImporter($importerUuid);
        if ($importer === false) {
            return false;
        }

        $assortment = new WmsAssortmentController();
        $settings = $assortment->get_settings();
        $limit = $assortment->get_limit();
        $offset = $offset > 0 ? $offset : (int) $importer['offset'];

        Importers::installImportVerification($importer['name'], $importerUuid);

        $assortments = $assortment->get_assortment($offset);
        $count = isset($assortments['rows']) ? count($assortments['rows']) : 0;

        if ($count <= 0 || empty($assortments['rows'])) {
            return $this->finishSync($offset + $count, $importer['name'], $importerUuid);
        }

        $queueId = $this->scheduleNextStep(
            $importer,
            $importerUuid,
            $assortments['meta']['size'] ?? 0,
            $offset + $limit
        );

        do_action('wms_assortment_start_sync_loop');

        $this->processBatch($assortment, $assortments);

        update_option(self::OPTION_UPDATE_START, [
            'size' => $assortments['meta']['size'] ?? 0,
            'count' => $offset,
            'time' => 0,
            'load' => 'load',
        ]);

        do_action('wms_assortment_end_sync_loop');

        $runImmediately = isset($settings['wms_product_type_load'])
            && $settings['wms_product_type_load'] === 'speed';

        return Importers::endCurrentImportStep(
            $queueId,
            $importer['name'],
            $importerUuid,
            $runImmediately
        );
    }

    /**
     * @param array<string, mixed> $data
     * @return array{success: bool, data: array, errors: array}
     */
    public function responseOk(array $data = []): array
    {
        return $this->response(true, $data);
    }

    /**
     * @param array<int|string, mixed> $errors
     * @return array{success: bool, data: array, errors: array}
     */
    public function responseError(array $errors = []): array
    {
        return $this->response(false, [], $errors);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<int|string, mixed> $errors
     * @return array{success: bool, data: array, errors: array}
     */
    public function response(bool $success = true, array $data = [], array $errors = []): array
    {
        return ['success' => $success, 'data' => $data, 'errors' => $errors];
    }

    /**
     * @return array<string, mixed>|false
     * @throws \Exception
     */
    private function getAndValidateImporter(string $importerUuid): array|false
    {
        return Importers::getImporter($importerUuid, true, self::IMPORTER_NAME);
    }

    /**
     * Process one batch: get_products -> update_products -> create_products.
     *
     * @param WmsAssortmentController $assortment
     * @param array<string, mixed> $assortments Response from get_assortment().
     * @throws \Exception
     */
    private function processBatch(WmsAssortmentController $assortment, array $assortments): void
    {
        $products = $assortment->get_products($assortments);
        if (!$products) {
            return;
        }

        $products = $assortment->update_products($products);
        if (!empty($products)) {
            $assortment->create_products($products);
        }
    }

    /**
     * Finalize sync when no more rows: log, update option, end import, fire action.
     *
     * @return array{0: string}
     * @throws \Exception
     */
    private function finishSync(int $countProduct, string $importerName, string $importerUuid): array
    {
        Importers::endImport($importerName, $importerUuid);

        WmsLogs::set_logs(
            'Синхронизация закончилась ' . $countProduct . ' товаров было синхронизировано',
            true
        );
        update_option(self::OPTION_UPDATE_START, [
            'load' => 'stop',
            'size' => 0,
            'time' => current_time('d-m-Y H:i:s'),
            'message' => 'Полная синхронизация',
        ]);

        do_action('wms_assortment_end_sync');

        return ['ok'];
    }

    /**
     * Update importer state and schedule next queue step.
     *
     * @param array<string, mixed> $importer     Importer data (will be updated with count_product, offset).
     * @param int                  $totalSize   Total size from meta.
     * @param int                  $newOffset   Next offset (current offset + limit).
     * @return int Queue action id.
     * @throws \Exception
     */
    private function scheduleNextStep(
        array $importer,
        string $importerUuid,
        int $totalSize,
        int $newOffset
    ): int {
        $importer['count_product'] = $totalSize;
        $importer['offset'] = $newOffset;

        Importers::setImporters($importer, $importerUuid);

        return Importers::nextImportStep(
            $importer['name'],
            $importerUuid,
            $newOffset
        );
    }
}
