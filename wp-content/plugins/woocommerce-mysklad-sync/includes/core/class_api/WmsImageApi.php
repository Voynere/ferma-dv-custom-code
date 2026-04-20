<?php
/**
 * Created by PhpStorm.
 * User: aqw
 * Date: 24.01.2018
 * Time: 12:08
 */

class WmsImageApi
{

    /**
     * @param $product_id
     * @param $images
     * @return bool|void
     */
    public function update_image($product_id, $images)
    {
        if (is_array($images)) {

            $mainImage = array_shift($images);

            if(count($images) > 0) {

                $aProductGallery = [];

                foreach ($images as $aImage) {
                    if ($iImageId = $this->update_gallery($product_id, $aImage)) {
                        $aProductGallery[] = $iImageId;
                    }
                }
                // TODO:Реализовать удаление картинок которые уже не используются
                if (count($aProductGallery) > 0) {
                    update_post_meta($product_id, '_product_image_gallery', implode(",", $aProductGallery));
                    do_action('wms_product_add_product_image_gallery', $product_id, $aProductGallery);
                }
            }

        }else{
            return false;
        }


        if (!$id = $this->upload_image($product_id, $mainImage)) {
            return false;
        }

        update_post_meta($product_id, '_thumbnail_id', $id);
        do_action('wms_product_add_product_main_image', $product_id, $id);

        return true;
    }


    /**
     * @param $product_id
     * @param $image
     * @return bool|int
     */
    public function update_gallery($product_id, $image)
    {
        return $this->upload_image($product_id, $image);
    }

    /**
     * @param $product_id
     * @param $image
     * @return bool|int
     */
    private function upload_image($product_id, $image)
    {
        if (empty($image)) {
            return false;
        }

        $id = $this->get_exist_image_by_url($image['uuid']);

        if ($id === false) {
            $id = $this->sideload_image($image['uuid'], $product_id, $image['filename']);
        }


        if ($id === false) {
            WmsLogs::set_logs('Ошибка при загрузке картинки у товара ID ' . $product_id, true);

            delete_post_meta($product_id, '_ms_image_update_hash');
            delete_post_meta($product_id, '_ms_product_image_href');
            delete_post_meta($product_id, '_ms_product_image_data');

            return false;
        }

        return $id;

    }

    /**
     * Check exist image by URL
     * @param $id
     * @return array|false|int|object
     */
    function get_exist_image_by_url($id)
    {
        global $wpdb;
        $images =
            $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * 
                        FROM $wpdb->postmeta 
                        WHERE meta_key = %s 
                        AND meta_value LIKE %s 
                        LIMIT %d",
                    '_ms_image_href', "%$id%", 1
                ), ARRAY_A );

        if (!empty($images) and isset($images[0]['post_id'])) {
            return $images[0]['post_id'];
        }

        return false;

    }


    /**
     * @param $image
     * @param $product_id
     * @param $file_name
     * @return bool|int
     */
    private function sideload_image($image, $product_id, $file_name)
    {
        $href = WMS_URL_API_V2 . '/download/' . $image;
        $file_name = WmsHelper::translit($file_name);
        $desc = null;

        if (!function_exists('media_handle_sideload')) {
            require_once(ABSPATH . "wp-admin" . '/includes/image.php');
            require_once(ABSPATH . "wp-admin" . '/includes/file.php');
            require_once(ABSPATH . "wp-admin" . '/includes/media.php');
        }

        $file = $this->connect_image($href, $file_name);

        if ($file === false) {
            @unlink($file);
            return false;
        }
        $filesize = filesize($file);

        // Проверяем работу функции
        if ($filesize < 2048) {
            WmsLogs::set_logs('ошибка загрузки картинки для ID ' . $product_id . ' слишком маленький размер изображения возможно пришел пустой ответ', true);
            @unlink($file);
            return false;
        }

        $filetype = wp_check_filetype($file_name);
        // Устанавливаем переменные для размещения
        $file_array['name'] = $file_name;
        $file_array['type'] = $filetype['type'];
        $file_array['tmp_name'] = $file;
        $file_array['size'] = filesize($file);

        $id = media_handle_sideload($file_array, $product_id, $desc);

        // Проверяем работу функции
        if (is_wp_error($id)) {
            WmsLogs::set_logs($id->get_error_message(), true);
            WmsLogs::set_logs($file_array, true);
            @unlink($file_array['tmp_name']);
            return false;
        }

        if (absint($id) <= 0) {
            WmsLogs::set_logs($file_array, true);
            @unlink($file_array['tmp_name']);
            return false;
        }

        // удалим временный файл
        @unlink($file_array['tmp_name']);
        update_post_meta($id, '_ms_image_href', $image);

        return $id;

    }

    /**
     * @param $target_url
     * @param $target_url_name
     * @return string
     */
    private function connect_image($target_url, $target_url_name)
    {

        if ($image = $this->get_image_to_ms($target_url)) {
            $tmpfname = wp_tempnam($target_url_name);
            $fh = fopen($tmpfname, 'w');
            fwrite($fh, $image);
            fclose($fh);

            return $tmpfname;
        }

        return false;

    }

    /**
     * @param $url
     * @return bool|string
     * @throws Exception
     */
    public function get_image_to_ms($url)
    {
        return WmsConnectApi::get_instance()->download_image($url);
    }


}




