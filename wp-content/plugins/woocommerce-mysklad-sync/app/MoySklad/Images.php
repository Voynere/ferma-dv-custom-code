<?php


namespace WCSTORES\WC\MS\MoySklad;


/**
 * Class Images
 * @package WCSTORES\WC\MS\MoySklad
 */
class Images
{
    /**
     * @param $href
     * @return mixed
     * @throws \Exception
     */
    static function getImages($href)
    {
       return \WmsConnectApi::get_instance()->send_request($href);
    }

    /**
     * @param $images
     * @return array|mixed
     */
    static function getUuidsImage($images)
    {
        $imagesUuids = [];

        if(is_array($images) and isset($images['rows'])){
            $imagesUuids = array_map(function ($image){
                return [
                    'filename' => $image['filename'],
                    'uuid' => \WmsHelper::get_id_ms_explode($image['meta']['href'])
                ];
            }, $images['rows']);

        }

        return $imagesUuids;
    }

    /**
     * @param $images
     * @return array|mixed
     * @throws \Exception
     */
    static function getDataImage($images)
    {
        $data = [];

        if(is_array($images) and isset($images['rows'])){

            $data = [
                'images' => self::getUuidsImage($images),
                'hash' => md5(serialize($images))
            ];

        }elseif (is_string($images) and $images = \WmsConnectApi::get_instance()->send_request($images)){
            $data = self::getDataImage($images);
        }

        return (!empty($data)) ? $data : false;
    }


}