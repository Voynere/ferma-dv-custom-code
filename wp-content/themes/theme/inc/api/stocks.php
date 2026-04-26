<?php
/**
 * Stocks REST API endpoints and helpers.
 *
 * @package Theme
 */

add_action('rest_api_init', function () {
    register_rest_route('ferma/v1', '/stocks', [
        'methods'  => 'GET',
        'callback' => 'ferma_get_stocks',
        'permission_callback' => '__return_true',
    ]);
    register_rest_route('ferma/v1', '/stock-migration-status', [
        'methods'  => 'GET',
        'callback' => 'ferma_get_stock_migration_status',
        'permission_callback' => '__return_true',
    ]);
});

function ferma_get_stocks(WP_REST_Request $req)
{
    $id = intval($req->get_param('product_id'));
    if (!$id) {
        return new WP_REST_Response(['error' => 'product_id is required'], 400);
    }

    return new WP_REST_Response(ferma_build_stock_payload($id), 200);
}

add_action('rest_api_init', function () {

    register_rest_route('ferma/v1', '/stocks-by-skus', [
        'methods' => 'GET',
        'callback' => 'ferma_get_stocks_by_skus',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('ferma/v1', '/stocks-by-sku', [
        'methods' => 'GET',
        'callback' => 'ferma_get_stocks_by_sku',
        'permission_callback' => '__return_true',
    ]);

});

function ferma_store_meta_map(): array {
    return [
        '028e05a7-b4fa-11ee-0a80-1198000442be' => 'Заря',
        '7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93' => 'Эгершельд',
        'a99d6fdf-0970-11ed-0a80-0ed600075845' => 'Космос',
        'b24e4c35-9609-11eb-0a80-0d0d008550c2' => 'Реми-Сити',
    ];
}

function ferma_get_store_stocks_with_fallback(int $product_id, array $store_ids): array {
    $stocks = [];

    if (class_exists('\Wdc\Addition\Stores\StockTable')) {
        $stocks = \Wdc\Addition\Stores\StockTable::get_product_stocks($product_id, $store_ids);
    }

    foreach ($store_ids as $store_id) {
        if (!array_key_exists($store_id, $stocks)) {
            $stocks[$store_id] = (float) get_post_meta($product_id, $store_id, true);
        } else {
            $stocks[$store_id] = (float) $stocks[$store_id];
        }
    }

    return $stocks;
}

function ferma_build_stock_payload(int $product_id): array {
    $store_meta = ferma_store_meta_map();
    $store_stocks = ferma_get_store_stocks_with_fallback($product_id, array_keys($store_meta));

    $data = [
        'product_id' => $product_id,
        'sku' => (string) get_post_meta($product_id, '_sku', true),
        'manage_stock' => get_post_meta($product_id, '_manage_stock', true) === 'yes',
        'stock_total' => (float) get_post_meta($product_id, '_stock', true),
        'stores' => [],
    ];

    foreach ($store_meta as $meta_key => $title) {
        $data['stores'][] = [
            'id' => $meta_key,
            'name' => $title,
            'stock' => (float) ($store_stocks[$meta_key] ?? 0),
        ];
    }

    return $data;
}

function ferma_get_stock_migration_status(WP_REST_Request $req) {
    if (!class_exists('\Wdc\Addition\Stores\StockTable')) {
        return new WP_REST_Response([
            'enabled' => false,
            'error' => 'StockTable service is not available',
        ], 200);
    }

    return new WP_REST_Response([
        'enabled' => true,
        'status' => \Wdc\Addition\Stores\StockTable::get_diagnostics(),
    ], 200);
}

function ferma_get_stocks_by_sku(WP_REST_Request $req) {
    $sku = (string) $req->get_param('sku');
    $sku = trim($sku);

    if ($sku === '') {
        return new WP_REST_Response(['error' => 'sku is required'], 400);
    }

    $q = new WP_Query([
        'post_type' => ['product', 'product_variation'],
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'meta_query' => [
            [
                'key' => '_sku',
                'value' => $sku,
                'compare' => '=',
            ]
        ],
    ]);

    if (empty($q->posts)) {
        return new WP_REST_Response([], 200);
    }

    $pid = (int) $q->posts[0];
    return new WP_REST_Response(ferma_build_stock_payload($pid), 200);
}

function ferma_get_stocks_by_skus(WP_REST_Request $req) {
    $raw = $req->get_param('skus');

    // принимает и "a,b,c", и skus[]=a&skus[]=b
    $skus = [];
    if (is_array($raw)) {
        $skus = $raw;
    } else {
        $skus = explode(',', (string)$raw);
    }

    $skus = array_values(array_unique(array_filter(array_map(function ($s) {
        $s = trim((string)$s);
        return $s !== '' ? $s : null;
    }, $skus))));

    if (empty($skus)) {
        return new WP_REST_Response(['error' => 'skus is required'], 400);
    }

    $q = new WP_Query([
        'post_type' => ['product', 'product_variation'],
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'meta_query' => [
            [
                'key' => '_sku',
                'value' => $skus,
                'compare' => 'IN',   // <-- ВОТ ЭТО КЛЮЧЕВО
            ]
        ],
    ]);

    $out = [];
    foreach ($q->posts as $pid) {
        $pid = (int)$pid;
        $sku = (string) get_post_meta($pid, '_sku', true);
        if ($sku === '') continue;
        $out[$sku] = ferma_build_stock_payload($pid);
    }

    return new WP_REST_Response($out, 200);
}
