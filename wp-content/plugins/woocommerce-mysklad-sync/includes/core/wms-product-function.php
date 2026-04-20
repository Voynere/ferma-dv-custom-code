<?php

add_filter('wms_load_array_products', 'wms_attrubute_product_no_load', 10, 3);
add_filter('wms_load_array_products', 'wms_service_product_no_load', 10, 2);
add_filter( 'woocommerce_product_data_store_cpt_get_products_query', 'wms_add_search_by_uuid_ms_to_meta_query', 10, 2);


if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('wms_product_all_sync', 'wms_product_all_sync_to_wp_cli');
}


if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('wms_stock_all_sync', 'wms_stock_all_sync_to_wp_cli');
}

function wms_product_all_sync_to_wp_cli()
{
    update_option('wms_product_update_start', array('load' => 'start', 'message' => 'Начало полной синхронизации...'));
    WP_CLI::line(current_time('d-m-Y H:i:s') . ' Стартуем синхрон товаров через WP CLI');
    $oWmsAssortmentController = new WmsAssortmentController();
    $oWmsAssortmentController->sync('wp_cli');
}



function wms_stock_all_sync_to_wp_cli()
{
    update_option('wms_stock_update_start', array('load' => 'start', 'message' => 'Начало полной синхронизации...'));
    WP_CLI::line(current_time('d-m-Y H:i:s') . ' Стартуем синхрон остатков через WP CLI');
    $oWmsStockController = new WmsStockController();
    $oWmsStockController->sync('wp_cli');
}


/**
 * @param $oObject
 * @param $mKey
 * @return mixed|null
 */
function wms_get_meta_by_object($oObject, $mKey)
{
    if (is_object($oObject)) {
        $aoMetaData = $oObject->get_meta_data();
        $array_keys = array_keys(wp_list_pluck($aoMetaData, 'key'), $mKey, true);
        return (isset($aoMetaData[current($array_keys)])) ? $aoMetaData[current($array_keys)]->value : null;
    }

    return null;
}


/**
 * @param $oQuery
 * @param $aQueryVars
 * @return mixed
 */
function wms_add_search_by_uuid_ms_to_meta_query($oQuery, $aQueryVars)
{

    if (!empty($oQuery['MsSearchField'])) {
        $oQuery['meta_query'][] = array(
            'key' => apply_filters('wms_add_search_by_uuid_ms_to_meta_query', ['_id_ms', '_sku', '_externalCode']),
            'value' => $oQuery['MsSearchField'],
            'compare' => 'IN'
        );

    }

    return $oQuery;
}





/**
 * @param $aMsSearchField
 * @param int $iLimit
 * @return array|stdClass
 */
function wms_get_products_by_uuid_ms($aMsSearchField, $iLimit = 500)
{
    $aArgs = [
        'type' => array_merge(array_keys(wc_get_product_types()), ['variation']),
        'limit' => $iLimit,
        'MsSearchField' => $aMsSearchField
    ];


    return wc_get_products($aArgs);

}


/**
 * @param $aData
 * @param $sFieldNameSettings
 * @return mixed|string
 */
function msw_get_assortment_search_fields($aData, $sFieldNameSettings)
{
    $sFieldName = wms_var_sync($sFieldNameSettings);
    return (isset($aData[$sFieldName])) ? $aData[$sFieldName] : WmsHelper::get_id_ms_explode($aData['meta']['href']);
}


/**
 * @param $products
 * @param $wms_product_settings
 * @param int $wms_post_id
 *
 * @return bool
 */
function wms_attrubute_product_no_load($products, $wms_product_settings, $wms_post_id = 0)
{
    if (isset($products['attributes']) and is_array($products['attributes']) and !empty($products['attributes'])) $key = array_search('wms-no', array_column($products['attributes'], 'name'));
    if (isset($key) and $key !== false and $products['attributes'][$key]['value'] == 1) {
        //WmsLogs::set_logs('Ключ '.  $attribute[$key]['name'] . ' найден и соответствует всем параметрам');
        if ($wms_post_id > 0) {
            wp_trash_post($wms_post_id);
        }
        return false;
    }
    return $products;

}


/**
 * @param $product
 * @param $wms_product_settings
 *
 * @return bool
 */
function wms_service_product_no_load($product, $wms_product_settings)
{
    if (isset($wms_product_settings['wms_load_service']) && $wms_product_settings['wms_load_service'] == 'on') return $product;
    if (isset($product['meta']['type']) && $product['meta']['type'] == 'service') return false;
    return $product;
}


/**
 * @param $id
 *
 * @return array|mixed
 */
function get_id_ms_explode($id)
{
    $id_ms = (explode('/', $id));
    $id_ms = array_pop($id_ms);
    $id_ms = (explode('?', $id_ms));
    $id_ms = array_shift($id_ms);
    return $id_ms;
}

/**
 * @param string $value
 * @param string $key
 *
 * @return mixed
 */
function get_wms_product_id($value = '', $key = '_id_ms')
{
    return wms_get_product_id($key, $value);
}


/**
 * @param $key
 * @param $value
 * @return false|int|string
 */
function wms_get_product_id($key, $value)
{
    if (!isset($key) or empty(trim($key))) {
        return 'empty';
    }

    if (!isset($value) or empty(trim($value))) {
        return 'empty';
    }

    if (strpos($key, '#')) {
        list($key,) = explode('#', $key, 2);
    }

    global $wpdb;
    $product_id = intval(
        $wpdb->get_var(
            $wpdb->prepare(
                "SELECT post_id 
                        FROM $wpdb->postmeta 
                        WHERE meta_key = %s 
                        AND meta_value = %s",
                $key, $value
            )));


    if (isset($product_id) and !empty($product_id) and is_numeric($product_id)) {
        return $product_id;
    }

    return false;
}

/**
 * @param $value
 * @return array|false|string
 */
function wms_get_product_ids($value)
{
    if (!isset($value) or empty(trim($value))) {
        return 'empty';
    }


    global $wpdb;

    $aProductIds = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT post_id 
                        FROM $wpdb->postmeta 
                        WHERE  meta_value = %s",
            $value

        )
    );


    if (!empty($aProductIds)) {
        return $aProductIds;
    }

    return false;
}


/**
 * @param string $value
 * @return false|string
 */
function wms_var_sync($value = '')
{
    switch ($value) {
        case '_id_ms':
            return 'id';
            break;
        case '_sku':
            return 'code';
            break;
        case '_sku#article':
            return 'article';
            break;
        case '_externalCode':
            return 'externalCode';
            break;
    }
    return false;
}