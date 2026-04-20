<?php

namespace Wdc\Addition\Stores;

class StockTable
{
    const DB_VERSION = '1.0.0';
    const DB_VERSION_OPTION = 'wms_store_stock_table_version';
    const BACKFILL_STATE_OPTION = 'wms_store_stock_backfill_state';
    const BACKFILL_HOOK = 'wms_store_stock_backfill_event';
    const BACKFILL_LOCK_KEY = 'wms_store_stock_backfill_lock';
    const CACHE_GROUP = 'wms_store';
    const BACKFILL_BATCH_SIZE = 500;

    private static $table_exists = null;

    public static function bootstrap($settings)
    {
        self::maybe_create_table();

        add_action(self::BACKFILL_HOOK, array(__CLASS__, 'process_backfill_batch'));

        self::maybe_prepare_backfill($settings);
    }

    public static function get_table_name()
    {
        global $wpdb;

        return $wpdb->prefix . 'wms_store_stock';
    }

    public static function table_exists()
    {
        if (self::$table_exists !== null) {
            return self::$table_exists;
        }

        global $wpdb;

        $table_name = self::get_table_name();
        $exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table_name));
        self::$table_exists = ($exists === $table_name);

        return self::$table_exists;
    }

    public static function is_ready_for_reads()
    {
        if (!self::table_exists()) {
            return false;
        }

        $state = self::get_backfill_state();

        return isset($state['status']) && $state['status'] === 'done';
    }

    public static function get_filter_read_source()
    {
        return self::is_ready_for_reads() ? 'stock_table' : 'postmeta_fallback';
    }

    public static function get_backfill_status()
    {
        return self::get_backfill_state();
    }

    public static function get_diagnostics($settings = null)
    {
        $store_ids = self::get_store_ids_from_settings($settings);
        $backfill_state = self::get_backfill_state();
        $stats = self::get_cached_diagnostics_stats($store_ids);
        $next_run = wp_next_scheduled(self::BACKFILL_HOOK);

        $completed = (int) ($stats['products_in_table'] ?? 0);
        $total = (int) ($stats['source_products_total'] ?? 0);
        $progress = 0;
        if ($total > 0) {
            $progress = min(100, round(($completed / $total) * 100, 1));
        }

        return array(
            'table_exists' => self::table_exists(),
            'table_name' => self::get_table_name(),
            'db_version' => get_option(self::DB_VERSION_OPTION),
            'backfill' => array(
                'status' => $backfill_state['status'] ?? 'unknown',
                'last_post_id' => (int) ($backfill_state['last_post_id'] ?? 0),
                'stores_hash' => $backfill_state['stores_hash'] ?? '',
                'updated_at_unix' => (int) ($backfill_state['updated_at'] ?? 0),
                'updated_at_gmt' => !empty($backfill_state['updated_at']) ? gmdate('c', (int) $backfill_state['updated_at']) : null,
                'next_run_unix' => $next_run ? (int) $next_run : null,
                'next_run_gmt' => $next_run ? gmdate('c', (int) $next_run) : null,
                'progress_percent' => $progress,
            ),
            'reads' => array(
                'ready_for_reads' => self::is_ready_for_reads(),
                'filter_source' => self::get_filter_read_source(),
            ),
            'stores' => array(
                'configured_count' => count($store_ids),
                'configured_ids' => $store_ids,
            ),
            'counts' => $stats,
        );
    }

    public static function get_product_stocks($product_id, $store_ids = array())
    {
        global $wpdb;

        if (!self::table_exists()) {
            return array();
        }

        $product_id = (int) $product_id;
        if ($product_id <= 0) {
            return array();
        }

        $table_name = self::get_table_name();
        $sql = "SELECT store_id, quantity FROM {$table_name} WHERE product_id = %d";
        $params = array($product_id);

        $store_ids = self::normalize_store_ids($store_ids);
        if (!empty($store_ids)) {
            $placeholders = implode(', ', array_fill(0, count($store_ids), '%s'));
            $sql .= " AND store_id IN ({$placeholders})";
            $params = array_merge($params, $store_ids);
        }

        $rows = $wpdb->get_results($wpdb->prepare($sql, $params), ARRAY_A);
        if (empty($rows)) {
            return array();
        }

        $stocks = array();
        foreach ($rows as $row) {
            $stocks[$row['store_id']] = (float) $row['quantity'];
        }

        return $stocks;
    }

    public static function get_product_ids_by_stores($store_ids)
    {
        global $wpdb;

        if (!self::table_exists()) {
            return null;
        }

        $store_ids = self::normalize_store_ids($store_ids);
        if (empty($store_ids)) {
            return array();
        }

        $cache_key = 'stock_ids_' . md5(wp_json_encode($store_ids));
        $cached_ids = wp_cache_get($cache_key, self::CACHE_GROUP);
        if ($cached_ids !== false) {
            return $cached_ids;
        }

        $cached_ids = get_transient($cache_key);
        if ($cached_ids !== false) {
            wp_cache_set($cache_key, $cached_ids, self::CACHE_GROUP, 300);
            return $cached_ids;
        }

        $table_name = self::get_table_name();
        $placeholders = implode(', ', array_fill(0, count($store_ids), '%s'));
        $sql = "
            SELECT DISTINCT product_id
            FROM {$table_name}
            WHERE store_id IN ({$placeholders})
              AND quantity > 0
        ";

        $prepared_sql = $wpdb->prepare($sql, $store_ids);
        $product_ids = array_map('intval', (array) $wpdb->get_col($prepared_sql));

        set_transient($cache_key, $product_ids, 300);
        wp_cache_set($cache_key, $product_ids, self::CACHE_GROUP, 300);

        return $product_ids;
    }

    public static function upsert_stocks($product_id, $store_quantities)
    {
        global $wpdb;

        if (!self::table_exists()) {
            return false;
        }

        $product_id = (int) $product_id;
        if ($product_id <= 0 || empty($store_quantities) || !is_array($store_quantities)) {
            return false;
        }

        $rows = array();
        foreach ($store_quantities as $store_id => $quantity) {
            $store_id = sanitize_text_field($store_id);
            if ($store_id === '') {
                continue;
            }

            $rows[] = array(
                'product_id' => $product_id,
                'store_id' => $store_id,
                'quantity' => (float) $quantity,
                'updated_at' => current_time('mysql', true),
            );
        }

        if (empty($rows)) {
            return false;
        }

        $values_sql = array();
        $values = array();
        foreach ($rows as $row) {
            $values_sql[] = '(%d, %s, %f, %s)';
            $values[] = $row['product_id'];
            $values[] = $row['store_id'];
            $values[] = $row['quantity'];
            $values[] = $row['updated_at'];
        }

        $table_name = self::get_table_name();
        $sql = "
            INSERT INTO {$table_name} (product_id, store_id, quantity, updated_at)
            VALUES " . implode(', ', $values_sql) . "
            ON DUPLICATE KEY UPDATE
                quantity = VALUES(quantity),
                updated_at = VALUES(updated_at)
        ";

        $wpdb->query($wpdb->prepare($sql, $values));

        return true;
    }

    public static function process_backfill_batch()
    {
        if (!self::acquire_backfill_lock()) {
            return;
        }

        try {
            if (!self::table_exists()) {
                return;
            }

            $store_ids = self::get_store_ids_from_settings();
            if (empty($store_ids)) {
                self::mark_backfill_done(self::get_store_hash());
                return;
            }

            $state = self::get_backfill_state();
            $last_post_id = isset($state['last_post_id']) ? (int) $state['last_post_id'] : 0;

            $post_ids = self::get_post_ids_for_backfill($store_ids, $last_post_id, self::BACKFILL_BATCH_SIZE);
            if (empty($post_ids)) {
                self::mark_backfill_done(self::get_store_hash());
                return;
            }

            $rows = self::get_postmeta_rows_for_backfill($post_ids, $store_ids);
            $grouped_rows = array();
            foreach ($rows as $row) {
                $pid = (int) $row['post_id'];
                if (!isset($grouped_rows[$pid])) {
                    $grouped_rows[$pid] = array();
                }

                $grouped_rows[$pid][$row['meta_key']] = (float) $row['meta_value'];
            }

            foreach ($grouped_rows as $product_id => $stock_map) {
                self::upsert_stocks($product_id, $stock_map);
            }

            $new_state = array(
                'status' => 'running',
                'last_post_id' => max($post_ids),
                'stores_hash' => self::get_store_hash(),
                'updated_at' => time(),
            );
            update_option(self::BACKFILL_STATE_OPTION, $new_state, false);

            wp_schedule_single_event(time() + 15, self::BACKFILL_HOOK);
        } finally {
            self::release_backfill_lock();
        }
    }

    private static function maybe_create_table()
    {
        if (self::table_exists() && get_option(self::DB_VERSION_OPTION) === self::DB_VERSION) {
            return;
        }

        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $table_name = self::get_table_name();
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE {$table_name} (
            product_id BIGINT(20) UNSIGNED NOT NULL,
            store_id VARCHAR(64) NOT NULL,
            quantity DECIMAL(18,3) NOT NULL DEFAULT 0,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY  (product_id, store_id),
            KEY store_quantity (store_id, quantity),
            KEY store_product (store_id, product_id)
        ) {$charset_collate};";

        dbDelta($sql);

        self::$table_exists = null;
        self::table_exists();
        update_option(self::DB_VERSION_OPTION, self::DB_VERSION, false);
    }

    private static function maybe_prepare_backfill($settings)
    {
        $store_ids = self::get_store_ids_from_settings($settings);
        if (empty($store_ids)) {
            return;
        }

        $current_hash = self::get_store_hash($settings);
        $state = self::get_backfill_state();

        if (($state['stores_hash'] ?? '') !== $current_hash) {
            update_option(self::BACKFILL_STATE_OPTION, array(
                'status' => 'pending',
                'last_post_id' => 0,
                'stores_hash' => $current_hash,
                'updated_at' => time(),
            ), false);
            $state = self::get_backfill_state();
        }

        if (($state['status'] ?? 'pending') === 'done') {
            return;
        }

        if (!wp_next_scheduled(self::BACKFILL_HOOK)) {
            wp_schedule_single_event(time() + 10, self::BACKFILL_HOOK);
        }
    }

    private static function get_cached_diagnostics_stats($store_ids)
    {
        $store_ids = self::normalize_store_ids($store_ids);
        $cache_key = 'stock_diag_' . md5(wp_json_encode($store_ids));

        $cached = wp_cache_get($cache_key, self::CACHE_GROUP);
        if ($cached !== false) {
            return $cached;
        }

        $cached = get_transient($cache_key);
        if ($cached !== false) {
            wp_cache_set($cache_key, $cached, self::CACHE_GROUP, 60);
            return $cached;
        }

        global $wpdb;

        $stats = array(
            'rows_in_table' => 0,
            'products_in_table' => 0,
            'source_products_total' => 0,
            'last_table_update_gmt' => null,
        );

        if (!empty($store_ids)) {
            $store_placeholders = implode(', ', array_fill(0, count($store_ids), '%s'));

            if (self::table_exists()) {
                $table_name = self::get_table_name();
                $stats['rows_in_table'] = (int) $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT COUNT(*) FROM {$table_name} WHERE store_id IN ({$store_placeholders})",
                        $store_ids
                    )
                );
                $stats['products_in_table'] = (int) $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT COUNT(DISTINCT product_id) FROM {$table_name} WHERE store_id IN ({$store_placeholders})",
                        $store_ids
                    )
                );
                $stats['last_table_update_gmt'] = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT MAX(updated_at) FROM {$table_name} WHERE store_id IN ({$store_placeholders})",
                        $store_ids
                    )
                );
            }

            $source_sql = "
                SELECT COUNT(DISTINCT pm.post_id)
                FROM {$wpdb->postmeta} pm
                INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                WHERE pm.meta_key IN ({$store_placeholders})
                  AND p.post_type IN ('product', 'product_variation')
            ";
            $stats['source_products_total'] = (int) $wpdb->get_var($wpdb->prepare($source_sql, $store_ids));
        }

        set_transient($cache_key, $stats, 60);
        wp_cache_set($cache_key, $stats, self::CACHE_GROUP, 60);

        return $stats;
    }

    private static function get_post_ids_for_backfill($store_ids, $last_post_id, $limit)
    {
        global $wpdb;

        $store_ids = self::normalize_store_ids($store_ids);
        if (empty($store_ids)) {
            return array();
        }

        $placeholders = implode(', ', array_fill(0, count($store_ids), '%s'));
        $limit = (int) $limit;
        $sql = "
            SELECT DISTINCT pm.post_id
            FROM {$wpdb->postmeta} pm
            INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            WHERE pm.meta_key IN ({$placeholders})
              AND pm.post_id > %d
              AND p.post_type IN ('product', 'product_variation')
            ORDER BY pm.post_id ASC
            LIMIT {$limit}
        ";

        $params = array_merge($store_ids, array((int) $last_post_id));

        return array_map('intval', (array) $wpdb->get_col($wpdb->prepare($sql, $params)));
    }

    private static function get_postmeta_rows_for_backfill($post_ids, $store_ids)
    {
        global $wpdb;

        $post_ids = array_map('intval', array_filter((array) $post_ids));
        $store_ids = self::normalize_store_ids($store_ids);
        if (empty($post_ids) || empty($store_ids)) {
            return array();
        }

        $post_placeholders = implode(', ', array_fill(0, count($post_ids), '%d'));
        $store_placeholders = implode(', ', array_fill(0, count($store_ids), '%s'));
        $sql = "
            SELECT post_id, meta_key, meta_value
            FROM {$wpdb->postmeta}
            WHERE post_id IN ({$post_placeholders})
              AND meta_key IN ({$store_placeholders})
        ";

        $params = array_merge($post_ids, $store_ids);

        return (array) $wpdb->get_results($wpdb->prepare($sql, $params), ARRAY_A);
    }

    private static function get_store_ids_from_settings($settings = null)
    {
        if ($settings === null) {
            $settings = get_option('wms_settings_stock');
        }

        if (!isset($settings['wms_stock_store'])) {
            return array();
        }

        return self::normalize_store_ids($settings['wms_stock_store']);
    }

    private static function get_store_hash($settings = null)
    {
        return md5(wp_json_encode(self::get_store_ids_from_settings($settings)));
    }

    private static function normalize_store_ids($store_ids)
    {
        $store_ids = array_map('sanitize_text_field', (array) $store_ids);

        return array_values(array_filter(array_unique($store_ids), function ($store_id) {
            return $store_id !== '' && $store_id !== 'all';
        }));
    }

    private static function get_backfill_state()
    {
        $state = get_option(self::BACKFILL_STATE_OPTION, array());

        return is_array($state) ? $state : array();
    }

    private static function mark_backfill_done($store_hash)
    {
        update_option(self::BACKFILL_STATE_OPTION, array(
            'status' => 'done',
            'last_post_id' => 0,
            'stores_hash' => $store_hash,
            'updated_at' => time(),
        ), false);
    }

    private static function acquire_backfill_lock()
    {
        if (get_transient(self::BACKFILL_LOCK_KEY)) {
            return false;
        }

        set_transient(self::BACKFILL_LOCK_KEY, 1, 60);

        return true;
    }

    private static function release_backfill_lock()
    {
        delete_transient(self::BACKFILL_LOCK_KEY);
    }
}
