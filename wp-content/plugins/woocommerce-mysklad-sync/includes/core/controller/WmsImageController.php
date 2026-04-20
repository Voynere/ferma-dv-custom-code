<?php
/**
 * Created by PhpStorm.
 * User: aqw
 * Date: 29.01.2018
 * Time: 19:23
 */

class WmsImageController
{

    /**
     * @var
     */
    private $settings;

    /**
     * @var mixed|void
     */
    private $limit;

    /**
     * WmsImageController constructor.
     */
    public function __construct()
    {
        /*$settings = get_option('wms_settings_product');
        $this->set_settings($settings);

        $limit = 5;
        if (isset($this->settings['wms_product_limit_img']) and $this->settings['wms_product_limit_img'] > 5) {
            $limit = $this->settings['wms_product_limit_img'];
        }
        $this->limit = apply_filters('wms_product_limit_img', $limit);

        if (wp_doing_ajax()) {
            add_action('wp_ajax_wms_image_load_loop', array($this, 'sync'));
            add_action('wp_ajax_nopriv_wms_image_load_loop', array($this, 'sync'));

            add_action('wp_ajax_wms_checking_for_hang_ups_image', array($this, 'sync'));
            add_action('wp_ajax_nopriv_wms_checking_for_hang_ups_image', array($this, 'sync'));
        }

        add_action('wms_assortment_end_sync', array($this, 'start_sync_image'));
        add_action('wms_walker_hook_image', array($this, 'sync'));*/


    }

    /**
     * @param null $settings
     */
    public function set_settings($settings)
    {
        $this->settings = $settings;
    }



    /**
     *
     */
    public function start_sync_image()
    {
        if ($this->settings['wms_load_image'] == 'on' or $this->settings['wms_load_image'] == 'all') {

            $sStartTimeLoop = WmsWalkerFactory::get_walker('image')->get_start_loop();

            if ((time() - $sStartTimeLoop) < (60 * 5 * $this->limit )) {

                WmsLogs::set_logs('рано еше', true);
                return;
            }

            WmsLogs::set_logs('Стартуем (синхронизация изображений)', true);

            WmsWalkerFactory::get_walker('image')->cron_init();
            WmsWalkerFactory::get_walker('image')->start_walker();

            update_option('wms_image_update_start', array('load' => 'start', 'message' => 'Начало синхронизации...'));

            $this->sync();
        }
    }

    /**
     * @return void
     */
    public function sync()
    {
        $aoProductMetaImages = $this->get_products();

        if (!$aoProductMetaImages) {
            WmsWalkerFactory::get_walker('image')->delete_walker();
            update_option('wms_image_update_start', array('load' => 'stop', 'size' => 0, 'time' => current_time('d-m-Y H:i:s'), 'message' => 'Полная синхронизация'));

            wp_die();
        }

        WmsWalkerFactory::get_walker('image')->start_loop(1);

        $this->update_product_images($aoProductMetaImages);

        if (isset($this->settings['wms_product_type_load_img']) and $this->settings['wms_product_type_load_img'] == 'speed') {
            WmsWalkerFactory::get_walker('image')->end_loop('', true);
            WmsHelper::wms_ajax('admin-ajax.php?action=wms_image_load_loop');
            wp_die();
        }

        WmsWalkerFactory::get_walker('image')->end_loop(1);
        wp_die();
    }


    /**
     * @param $aoProductMetaImages
     */
    public function update_product_images($aoProductMetaImages)
    {
        foreach ($aoProductMetaImages as $oProductImage) {
            try {
                $oImageStore = new WmsImageApi();

                update_option('wms_image_update_start', array('load' => 'load', 'message' => 'Идет обновление изображения у ID ' . $oProductImage->post_id));

                $aImages = maybe_unserialize($oProductImage->meta_value);

                if(!isset($aImages['rows'])){
                    if(!$aImages = $this->get_images($aImages['meta']['href'])){
                        delete_post_meta($oProductImage->post_id, '_ms_product_image_href');
                        continue;
                    }
                }

                if ($this->settings['wms_load_image'] == 'all') {

                    $oImageStore->update_image($oProductImage->post_id, $aImages['rows'][0]);
                    unset($aImages['rows'][0]);

                    if (count($aImages['rows']) > 0) {

                        $aProductGallery = [];
                        foreach ($aImages['rows'] as $aImage) {
                            $iImageId = $oImageStore->update_gallery($oProductImage->post_id, $aImage);
                            if ($iImageId) {
                                $aProductGallery[] = $iImageId;
                            }
                        }
                        // TODO:Реализовать удаление картинок которые уже не используются
                        if (count($aProductGallery) > 0) {
                            update_post_meta($oProductImage->post_id, '_product_image_gallery', implode(",", $aProductGallery));
                        }
                    }

                    delete_post_meta($oProductImage->post_id, '_ms_product_image_href');

                } else {
                    $oImageStore->update_image($oProductImage->post_id, $aImages['rows'][0]);
                }
                update_post_meta($oProductImage->post_id, '_ms_image_update_hash', md5(serialize($aImages)));

                unset($oImageStore);
            } //Перехватываем (catch) исключение, если что-то идет не так.
            catch (Exception $ex) {
                WmsLogs::set_logs($ex->getMessage(), true);

            }

        }
    }

    /**
     * @param $href
     * @return bool|mixed
     */
    private function get_images($href)
    {
        $aImages = WmsConnectApi::get_instance()->send_request($href);
        //echo '<pre>';
        //print_r($products);
        if ($aImages === false) {
            update_option('wms_image_update_start', array('time' => 'Ошибка'));
            return  false;
        }

        return $aImages;

    }

    /**
     * @return array|false|object
     */
    public function get_products()
    {

        global $wpdb;
        $aoProductIds =
            $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * 
                        FROM $wpdb->postmeta 
                        WHERE meta_key = %s 
                        LIMIT %d",
                    '_ms_product_image_href', $this->limit
                ));

        if (!empty($aoProductIds) and count($aoProductIds) > 0) {
            return $aoProductIds;
        }

        return false;
    }

}

new WmsImageController();


