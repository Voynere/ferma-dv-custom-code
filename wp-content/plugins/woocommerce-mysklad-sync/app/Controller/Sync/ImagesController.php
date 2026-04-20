<?php

//Todo: одиночные изображения

namespace WCSTORES\WC\MS\Controller\Sync;

use WCSTORES\WC\MS\Facades\ImageUpdatesQueuesDataStore;
use WCSTORES\WC\MS\Queues\Queues;

/**
 * Class ImagesController
 * @package WCSTORES\WC\MS\Controller\Sync
 */
class ImagesController extends SyncController
{

    /**
     * @param $id
     * @param $data
     * @param array $settings
     */
    public function update($id, $data, $settings = [])
    {
        try {

            //update_option('wms_image_update_start', array('load' => 'load', 'message' => 'Идет обновление изображения у ID ' . $id));

            $settings = (!empty($settings)) ? $settings : get_option('wms_settings_product');

            $oImageStore = new \WmsImageApi();

            if ($settings['wms_load_image'] == 'all') {
                $oImageStore->update_image($id, $data['images']);
            } else {
                $oImageStore->update_image($id, [$data['images'][0]]);
            }

            update_post_meta($id, '_ms_image_update_hash', $data['hash']);

            unset($oImageStore);

        } //Перехватываем (catch) исключение, если что-то идет не так.
        catch (\Exception $ex) {
            delete_post_meta($id, '_ms_image_update_hash');
            \WmsLogs::set_logs($ex->getMessage(), true);
        }


    }


    /**
     *
     */
    public function sync()
    {
        $settings = get_option('wms_settings_product');
        $limit = (isset($settings['wms_product_limit_img']) and $settings['wms_product_limit_img'] > 0) ? $settings['wms_product_limit_img'] : 5;

        if($ImageUpdatesQueuesDataStores = ImageUpdatesQueuesDataStore::getQueuesByStatusPending($limit)) {
            foreach ($ImageUpdatesQueuesDataStores as $ImageUpdatesQueuesDataStore) {
                try {

                    $ImageUpdatesQueuesDataStore->inProgress();

                    Queues::addAsync('update_image', [
                        'id' => $ImageUpdatesQueuesDataStore->product_id,
                        'data' => $ImageUpdatesQueuesDataStore->data,
                        'settings' => ['wms_load_image' => $settings['wms_load_image']]
                    ], 'mswoo');

                    $ImageUpdatesQueuesDataStore->deleteById();

                } //Перехватываем (catch) исключение, если что-то идет не так.
                catch (\Exception $ex) {
                    \WmsLogs::set_logs($ex->getMessage(), true);
                    $ImageUpdatesQueuesDataStore->failed();
                }
            }
        }

        $count = ($ImageCountQueuesDataStores = ImageUpdatesQueuesDataStore::getCountQueuesByStatusPending()) ? $ImageCountQueuesDataStores : 0;
        update_option('wms_image_update_start', array('load' => 'load', 'message' => 'Товаров в очереди на обновление изображений ' . $count));

    }

}