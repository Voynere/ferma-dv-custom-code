<?php
/**
 * One-time sync "fasovka" from MoySklad -> WooCommerce (ms_only)
 *
 * Run:
 *   https://your-site.tld/ferma_fasovka_sync_once.php
 */

// =====================
// CONFIG
// =====================

// Auth mode: 'basic' or 'bearer'
define('FERMA_MS_AUTH_MODE', 'basic');

// Basic auth
define('FERMA_MS_BASIC_LOGIN', 'fenikc@fermadv');
define('FERMA_MS_BASIC_PASSWORD', 'Wsd19862011!!');

// Bearer auth (only if FERMA_MS_AUTH_MODE === 'bearer')
define('FERMA_MS_BEARER_TOKEN', '');

// Paging
define('FERMA_MS_LIMIT', 100);

// ACF field key for "razbivka_vesa"
define('FERMA_ACF_RAZBIVKA_FIELD_KEY', 'field_627cbc0e2d6f3');

// Optional: stop after N processed rows (0 = no limit)
define('FERMA_MS_MAX_ROWS', 0);

// IMPORTANT: trust only MoySklad, overwrite everything
define('FERMA_MS_ONLY', true);

// =====================
// BOOTSTRAP WP
// =====================
$wp_load = __DIR__ . '/wp-load.php';
if (!file_exists($wp_load)) {
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: text/plain; charset=utf-8');
    echo "Cannot find wp-load.php next to this file. Put this script into WordPress root.\n";
    exit;
}
require_once $wp_load;

// =====================
// RUNTIME SETTINGS
// =====================
@ignore_user_abort(true);
@set_time_limit(0);
@ini_set('max_execution_time', '0');
@ini_set('memory_limit', '1024M');

header('Content-Type: text/plain; charset=utf-8');

// =====================
// LOG
// =====================
function ferma_sync_log(string $msg): void {
    $file = trailingslashit(WP_CONTENT_DIR) . 'fasovka_sync_once.log';
    @file_put_contents($file, date('[d-M-Y H:i:s] ') . $msg . "\n", FILE_APPEND | LOCK_EX);
}

// =====================
// LOCK
// =====================
function ferma_sync_lock_key(): string {
    return 'ferma_fasovka_sync_once_lock';
}

function ferma_sync_lock_acquire(): bool {
    $key = ferma_sync_lock_key();
    if (get_transient($key)) return false;
    set_transient($key, 1, 60 * 60); // 1 hour
    return true;
}

function ferma_sync_lock_release(): void {
    delete_transient(ferma_sync_lock_key());
}

// =====================
// MoySklad API
// =====================
function ferma_ms_base(): string {
    return 'https://api.moysklad.ru/api/remap/1.2';
}

function ferma_ms_headers(): array {
    $headers = [
        'Accept'       => 'application/json;charset=utf-8',
        'Content-Type' => 'application/json',
    ];

    if (FERMA_MS_AUTH_MODE === 'basic') {
        $basic = base64_encode(FERMA_MS_BASIC_LOGIN . ':' . FERMA_MS_BASIC_PASSWORD);
        $headers['Authorization'] = 'Basic ' . $basic;
        return $headers;
    }

    $headers['Authorization'] = 'Bearer ' . FERMA_MS_BEARER_TOKEN;
    return $headers;
}

/**
 * IMPORTANT:
 * - do not pass multiple "fields" params (MS validates that only one is allowed)
 * - here we do not use fields at all (safe)
 */
function ferma_ms_get_assortment_page(int $offset, int $limit): array {
    $url = ferma_ms_base() . '/entity/assortment'
        . '?limit=' . $limit
        . '&offset=' . $offset;

    $resp = wp_remote_get($url, [
        'timeout' => 60,
        'headers' => ferma_ms_headers(),
    ]);

    if (is_wp_error($resp)) {
        $m = 'MS_SYNC ERROR: ' . $resp->get_error_message();
        ferma_sync_log($m);
        return ['rows' => [], 'meta' => ['size' => 0], '_error' => $m];
    }

    $code = (int) wp_remote_retrieve_response_code($resp);
    $body = (string) wp_remote_retrieve_body($resp);

    if ($code < 200 || $code >= 300) {
        $m = "MS_SYNC ERROR HTTP={$code} BODY=" . mb_substr($body, 0, 3000);
        ferma_sync_log($m);
        return ['rows' => [], 'meta' => ['size' => 0], '_error' => $m];
    }

    $json = json_decode($body, true);
    if (!is_array($json)) {
        $m = "MS_SYNC ERROR: invalid JSON";
        ferma_sync_log($m);
        return ['rows' => [], 'meta' => ['size' => 0], '_error' => $m];
    }

    return $json;
}

// =====================
// Helpers
// =====================
function ferma_ensure_term_exists(string $term_name, string $taxonomy): void {
    if (!taxonomy_exists($taxonomy)) return;
    $term = get_term_by('name', $term_name, $taxonomy);
    if ($term && !is_wp_error($term)) return;
    @wp_insert_term($term_name, $taxonomy);
}

function ferma_set_razbivka_meta(int $product_id, string $value): void {
    $value = (mb_strtolower(trim($value)) === 'да') ? 'да' : 'нет';

    // Store in postmeta (ACF uses this)
    update_post_meta($product_id, 'razbivka_vesa', $value);
    update_post_meta($product_id, '_razbivka_vesa', FERMA_ACF_RAZBIVKA_FIELD_KEY);

    // If ACF is available, also write explicitly (safe and consistent)
    if (function_exists('update_field')) {
        // Update by field key to avoid name mismatch issues
        @update_field(FERMA_ACF_RAZBIVKA_FIELD_KEY, $value, $product_id);
    }

    clean_post_cache($product_id);
    wp_cache_delete($product_id, 'post_meta');

    if (function_exists('wc_delete_product_transients')) {
        wc_delete_product_transients($product_id);
    }
}

function ferma_resolve_parent_product_id(int $product_id): int {
    if (!function_exists('wc_get_product')) return $product_id;

    $p = wc_get_product($product_id);
    if ($p && $p->is_type('variation')) {
        $parent_id = (int) $p->get_parent_id();
        if ($parent_id > 0) return $parent_id;
    }
    return $product_id;
}

// =====================
// Apply one MS item -> Woo product (ms_only)
// Returns true if razbivka_vesa meta changed
// =====================
function ferma_apply_fasovka_from_ms_item(array $ms_item, int $product_id): bool {
    $product_id = ferma_resolve_parent_product_id($product_id);

    $before_meta = (string) get_post_meta($product_id, 'razbivka_vesa', true);
    $before_acf  = '';
    if (function_exists('get_field')) {
        $v = get_field('razbivka_vesa', $product_id);
        $before_acf = is_string($v) ? $v : '';
    }

    $ms_weighed = array_key_exists('weighed', $ms_item) ? (bool) $ms_item['weighed'] : false;
    $pathName   = isset($ms_item['pathName']) ? (string) $ms_item['pathName'] : '';

    // ms_only rule:
    // - fasovka strictly by weighed
    // - razbivka_vesa strictly by weighed
    $razbivka_value = $ms_weighed ? 'да' : 'нет';

    $taxonomy = 'pa_fasovka';
    if ($ms_weighed) {
        update_post_meta($product_id, '_ferma_fasovka', 'vesovaya');
        ferma_ensure_term_exists('Весовая', $taxonomy);
        @wp_set_object_terms($product_id, 'Весовая', $taxonomy, false);
        ferma_set_razbivka_meta($product_id, $razbivka_value);
    } else {
        update_post_meta($product_id, '_ferma_fasovka', 'shtuchnaya');
        ferma_ensure_term_exists('Штучная', $taxonomy);
        @wp_set_object_terms($product_id, 'Штучная', $taxonomy, false);
        ferma_set_razbivka_meta($product_id, $razbivka_value);
    }

    $after_meta = (string) get_post_meta($product_id, 'razbivka_vesa', true);
    $after_acf  = '';
    if (function_exists('get_field')) {
        $v = get_field('razbivka_vesa', $product_id);
        $after_acf = is_string($v) ? $v : '';
    }

    ferma_sync_log(
        "WRITE product_id={$product_id} ms_weighed=" . ($ms_weighed ? '1' : '0')
        . " path={$pathName}"
        . " meta {$before_meta}→{$after_meta}"
        . " acf {$before_acf}→{$after_acf}"
        . " ms_only=" . (FERMA_MS_ONLY ? '1' : '0')
    );

    return $after_meta !== $before_meta;
}

// =====================
// MAIN SYNC
// =====================
function ferma_sync_all_from_ms(): array {
    $offset = 0;
    $limit  = (int) FERMA_MS_LIMIT;

    $processed = 0;
    $matched   = 0;
    $changed   = 0;
    $skipped   = 0;

    if (!function_exists('wc_get_product_id_by_sku')) {
        return ['status' => 'error', 'message' => 'WooCommerce is not loaded (wc_get_product_id_by_sku missing).'];
    }

    while (true) {
        $page = ferma_ms_get_assortment_page($offset, $limit);
        if (!empty($page['_error'])) {
            return [
                'status'    => 'error',
                'message'   => $page['_error'],
                'processed' => $processed,
                'matched'   => $matched,
                'changed'   => $changed,
                'skipped'   => $skipped,
                'offset'    => $offset,
            ];
        }

        $rows = isset($page['rows']) && is_array($page['rows']) ? $page['rows'] : [];
        if (empty($rows)) break;

        foreach ($rows as $ms_item) {
            $processed++;

            if (FERMA_MS_MAX_ROWS > 0 && $processed > FERMA_MS_MAX_ROWS) {
                return [
                    'status'          => 'ok',
                    'processed'       => $processed,
                    'matched'         => $matched,
                    'changed'         => $changed,
                    'skipped'         => $skipped,
                    'offset'          => $offset,
                    'stopped_by_limit'=> FERMA_MS_MAX_ROWS
                ];
            }

            $code = isset($ms_item['code']) ? trim((string) $ms_item['code']) : '';
            if ($code === '') {
                $skipped++;
                continue;
            }

            $product_id = (int) wc_get_product_id_by_sku($code);
            if ($product_id <= 0) {
                $skipped++;
                continue;
            }

            $matched++;

            $is_changed = ferma_apply_fasovka_from_ms_item($ms_item, $product_id);
            if ($is_changed) $changed++;

            if (($processed % 200) === 0) {
                $m = "progress processed={$processed} matched={$matched} changed={$changed} skipped={$skipped} offset={$offset}";
                ferma_sync_log("MS_SYNC " . $m);
                echo $m . "\n";
                @flush();
                @ob_flush();
            }
        }

        $offset += $limit;
    }

    return [
        'status'    => 'ok',
        'processed' => $processed,
        'matched'   => $matched,
        'changed'   => $changed,
        'skipped'   => $skipped,
        'offset'    => $offset
    ];
}

// =====================
// RUN
// =====================
ferma_sync_log('MS_SYNC START');

if (!ferma_sync_lock_acquire()) {
    echo "LOCKED: sync is already running.\n";
    ferma_sync_log('MS_SYNC LOCKED');
    exit;
}

try {
    $res = ferma_sync_all_from_ms();
    ferma_sync_log('MS_SYNC END ' . json_encode($res, JSON_UNESCAPED_UNICODE));

    echo "DONE\n";
    echo json_encode($res, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
    echo "Log file: " . trailingslashit(WP_CONTENT_DIR) . "fasovka_sync_once.log\n";
} finally {
    ferma_sync_lock_release();
}
