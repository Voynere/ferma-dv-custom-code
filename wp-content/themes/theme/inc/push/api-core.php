<?php
/**
 * Push API core endpoints and transport helpers.
 *
 * @package Theme
 */

add_action('rest_api_init', function () {
    register_rest_route('ferma/v1', '/push/register', [
        'methods'  => 'POST',
        'callback' => 'ferma_push_register_device',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('ferma/v1', '/push/test', [
        'methods'  => 'POST',
        'callback' => 'ferma_push_test_send',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('ferma/v1', '/push/cart-sync', [
        'methods'  => 'POST',
        'callback' => 'ferma_push_cart_sync',
        'permission_callback' => '__return_true',
    ]);
});
function ferma_push_register_device(WP_REST_Request $request) {
    global $wpdb;

    $table = $wpdb->prefix . 'ferma_push_devices';

    if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) !== $table) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Push devices table not found',
            'table' => $table,
        ], 500);
    }

    $token = trim((string) $request->get_param('token'));
    $platform = trim((string) $request->get_param('platform'));
    $phone = ferma_normalize_phone($request->get_param('phone'));
    $user_id = absint($request->get_param('user_id'));
    $app_version = trim((string) $request->get_param('app_version'));

    if ($token === '' || strlen($token) < 50) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Invalid token',
        ], 400);
    }

    if ($platform === '') {
        $platform = 'android';
    }

    $now = current_time('mysql');

    $existing = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT id, token FROM {$table} WHERE token = %s LIMIT 1",
            $token
        )
    );

    if (!empty($wpdb->last_error)) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'DB select error',
            'db_error' => $wpdb->last_error,
        ], 500);
    }

    $data = [
        'user_id'      => $user_id ?: null,
        'phone'        => $phone ?: null,
        'platform'     => $platform,
        'app_version'  => $app_version ?: null,
        'is_active'    => 1,
        'last_seen_at' => $now,
        'updated_at'   => $now,
    ];

    if ($existing) {
        $updated = $wpdb->update(
            $table,
            $data,
            ['id' => (int) $existing->id],
            ['%d', '%s', '%s', '%s', '%d', '%s', '%s'],
            ['%d']
        );

        if ($updated === false) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'DB update error',
                'db_error' => $wpdb->last_error,
            ], 500);
        }

        return new WP_REST_Response([
            'success'   => true,
            'message'   => 'Device updated',
            'device_id' => (int) $existing->id,
        ], 200);
    }

    $inserted = $wpdb->insert(
        $table,
        [
            'user_id'      => $user_id ?: null,
            'phone'        => $phone ?: null,
            'token'        => $token,
            'platform'     => $platform,
            'app_version'  => $app_version ?: null,
            'is_active'    => 1,
            'last_seen_at' => $now,
            'created_at'   => $now,
            'updated_at'   => $now,
        ],
        ['%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s']
    );

    if ($inserted === false) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'DB insert error',
            'db_error' => $wpdb->last_error,
        ], 500);
    }

    $device_id = (int) $wpdb->get_var(
        $wpdb->prepare(
            "SELECT id FROM {$table} WHERE token = %s LIMIT 1",
            $token
        )
    );

    return new WP_REST_Response([
        'success'   => true,
        'message'   => 'Device registered',
        'device_id' => $device_id,
    ], 200);
}


function ferma_fcm_get_access_token() {
    $service_account_path = WP_CONTENT_DIR . '/uploads/ferma/firebase-service-account.json';

    if (!file_exists($service_account_path)) {
        return new WP_Error('missing_service_account', 'Firebase service account file not found');
    }

    $json = json_decode(file_get_contents($service_account_path), true);

    if (
        !$json ||
        empty($json['client_email']) ||
        empty($json['private_key']) ||
        empty($json['project_id'])
    ) {
        return new WP_Error('invalid_service_account', 'Invalid Firebase service account JSON');
    }

    $header = ['alg' => 'RS256', 'typ' => 'JWT'];
    $now = time();

    $claims = [
        'iss'   => $json['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud'   => 'https://oauth2.googleapis.com/token',
        'exp'   => $now + 3600,
        'iat'   => $now,
    ];

    $base64UrlEncode = function ($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    };

    $jwt_header = $base64UrlEncode(json_encode($header));
    $jwt_claims = $base64UrlEncode(json_encode($claims));
    $signature_input = $jwt_header . '.' . $jwt_claims;

    $private_key = $json['private_key'];
    $signature = '';

    $ok = openssl_sign($signature_input, $signature, $private_key, 'sha256WithRSAEncryption');
    if (!$ok) {
        return new WP_Error('jwt_sign_error', 'Failed to sign JWT');
    }

    $jwt = $signature_input . '.' . $base64UrlEncode($signature);

    $response = wp_remote_post('https://oauth2.googleapis.com/token', [
        'timeout' => 20,
        'body' => [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ],
    ]);

    if (is_wp_error($response)) {
        return $response;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (empty($body['access_token'])) {
        return new WP_Error('access_token_error', 'Failed to get Firebase access token', $body);
    }

    return [
        'access_token' => $body['access_token'],
        'project_id'   => $json['project_id'],
    ];
}

function ferma_send_fcm_push($token, $title, $body, $data = []) {
    $auth = ferma_fcm_get_access_token();

    if (is_wp_error($auth)) {
        return $auth;
    }

    $access_token = $auth['access_token'];
    $project_id = $auth['project_id'];

    $payload = [
        'message' => [
            'token' => $token,
            'notification' => [
                'title' => $title,
                'body'  => $body,
            ],
            'data' => array_map('strval', $data),
            'android' => [
                'priority' => 'high',
            ],
        ],
    ];

    $response = wp_remote_post(
        "https://fcm.googleapis.com/v1/projects/{$project_id}/messages:send",
        [
            'timeout' => 20,
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type'  => 'application/json',
            ],
            'body' => wp_json_encode($payload),
        ]
    );

    if (is_wp_error($response)) {
        return $response;
    }

    $status = wp_remote_retrieve_response_code($response);
    $resp_body = json_decode(wp_remote_retrieve_body($response), true);

    if ($status < 200 || $status >= 300) {
        return new WP_Error('fcm_send_error', 'FCM send failed', [
            'status' => $status,
            'body'   => $resp_body,
        ]);
    }

    return $resp_body;
}

function ferma_push_test_send(WP_REST_Request $request) {
    global $wpdb;

    $table = $wpdb->prefix . 'ferma_push_devices';

    $phone = ferma_normalize_phone($request->get_param('phone'));
    $user_id = absint($request->get_param('user_id'));

    if (!$phone && !$user_id) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'phone or user_id required',
        ], 400);
    }

    if ($user_id) {
        $devices = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table WHERE user_id = %d AND is_active = 1",
                $user_id
            )
        );
    } else {
        $devices = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table WHERE phone = %s AND is_active = 1",
                $phone
            )
        );
    }

    if (!$devices) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'No active devices found',
        ], 404);
    }

    $results = [];

    foreach ($devices as $device) {
        $result = ferma_send_fcm_push(
            $device->token,
            'Тестовый пуш',
            'Проверка отправки уведомлений',
            [
                'type' => 'test_push',
                'screen' => 'catalog',
            ]
        );

        $results[] = [
            'device_id' => (int) $device->id,
            'phone' => $device->phone,
            'result' => is_wp_error($result)
                ? [
                    'error' => $result->get_error_code(),
                    'message' => $result->get_error_message(),
                    'data' => $result->get_error_data(),
                ]
                : $result,
        ];
    }

    return [
        'success' => true,
        'results' => $results,
    ];
}

function ferma_normalize_phone($phone) {
    $phone = preg_replace('/\D+/', '', (string) $phone);

    if ($phone === '') {
        return '';
    }

    if (strpos($phone, '8') === 0) {
        $phone = '7' . substr($phone, 1);
    }

    if (strpos($phone, '7') !== 0) {
        $phone = '7' . $phone;
    }

    return $phone;
}

function ferma_push_install_table() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $devices = $wpdb->prefix . 'ferma_push_devices';
    $logs = $wpdb->prefix . 'ferma_push_logs';
    $carts = $wpdb->prefix . 'ferma_push_carts';

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $sql_devices = "CREATE TABLE $devices (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT UNSIGNED NULL,
        phone VARCHAR(32) NULL,
        token TEXT NOT NULL,
        platform VARCHAR(20) NOT NULL DEFAULT 'android',
        app_version VARCHAR(32) NULL,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        last_seen_at DATETIME NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        PRIMARY KEY (id),
        KEY user_id (user_id),
        KEY phone (phone),
        KEY is_active (is_active)
    ) $charset_collate;";

    $sql_logs = "CREATE TABLE $logs (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        phone VARCHAR(32) NULL,
        user_id BIGINT UNSIGNED NULL,
        push_type VARCHAR(64) NOT NULL,
        dedupe_key VARCHAR(191) NULL,
        title VARCHAR(255) NOT NULL,
        body TEXT NOT NULL,
        payload LONGTEXT NULL,
        sent_at DATETIME NOT NULL,
        status VARCHAR(32) NOT NULL DEFAULT 'sent',
        PRIMARY KEY (id),
        KEY phone (phone),
        KEY user_id (user_id),
        KEY push_type (push_type),
        KEY dedupe_key (dedupe_key)
    ) $charset_collate;";

    $sql_carts = "CREATE TABLE $carts (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        phone VARCHAR(32) NULL,
        user_id BIGINT UNSIGNED NULL,
        cart_hash VARCHAR(191) NULL,
        items LONGTEXT NULL,
        updated_at DATETIME NOT NULL,
        PRIMARY KEY (id),
        KEY phone (phone),
        KEY user_id (user_id),
        KEY cart_hash (cart_hash)
    ) $charset_collate;";

    dbDelta($sql_devices);
    dbDelta($sql_logs);
    dbDelta($sql_carts);
}

add_action('init', function () {
    if (get_option('ferma_push_tables_installed') !== '1') {
        ferma_push_install_table();
        update_option('ferma_push_tables_installed', '1');
    }
});
function ferma_push_cart_sync(WP_REST_Request $request) {
    global $wpdb;

    $table = $wpdb->prefix . 'ferma_push_carts';

    $phone = ferma_normalize_phone($request->get_param('phone'));
    $user_id = absint($request->get_param('user_id'));
    $items = $request->get_param('items');

    if (!$phone && !$user_id) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'phone or user_id required',
        ], 400);
    }

    if (!is_array($items)) {
        $items = [];
    }

    $now = current_time('mysql');
    $items_json = wp_json_encode($items);
    $cart_hash = md5($items_json . '|' . $phone . '|' . $user_id);

    $existing = null;

    if ($user_id) {
        $existing = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id FROM {$table} WHERE user_id = %d LIMIT 1",
                $user_id
            )
        );
    } elseif ($phone) {
        $existing = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id FROM {$table} WHERE phone = %s LIMIT 1",
                $phone
            )
        );
    }

    if ($existing) {
        $updated = $wpdb->update(
            $table,
            [
                'phone' => $phone ?: null,
                'user_id' => $user_id ?: null,
                'cart_hash' => $cart_hash,
                'items' => $items_json,
                'updated_at' => $now,
            ],
            ['id' => (int) $existing->id],
            ['%s', '%d', '%s', '%s', '%s'],
            ['%d']
        );

        if ($updated === false) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'DB update error',
                'db_error' => $wpdb->last_error,
            ], 500);
        }

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Cart updated',
        ], 200);
    }

    $inserted = $wpdb->insert(
        $table,
        [
            'phone' => $phone ?: null,
            'user_id' => $user_id ?: null,
            'cart_hash' => $cart_hash,
            'items' => $items_json,
            'updated_at' => $now,
        ],
        ['%s', '%d', '%s', '%s', '%s']
    );

    if ($inserted === false) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'DB insert error',
            'db_error' => $wpdb->last_error,
        ], 500);
    }

    return new WP_REST_Response([
        'success' => true,
        'message' => 'Cart saved',
    ], 200);
}
