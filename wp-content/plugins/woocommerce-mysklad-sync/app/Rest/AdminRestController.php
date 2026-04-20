<?php

namespace WCSTORES\WC\MS\Rest;

use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

/**
 * REST API Controller для админки
 */
class AdminRestController extends WP_REST_Controller
{
    protected $namespace = 'wms/v1';

    public function __construct()
    {
        $this->rest_base = 'settings';
    }

    /**
     * Регистрация роутов
     */
    public function register_routes()
    {
        // Settings
        register_rest_route($this->namespace, '/settings', [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'get_settings'],
                'permission_callback' => [$this, 'check_permissions'],
            ],
            [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => [$this, 'save_settings'],
                'permission_callback' => [$this, 'check_permissions'],
            ],
        ]);

        register_rest_route($this->namespace, '/settings/test-connection', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'test_connection'],
            'permission_callback' => [$this, 'check_permissions'],
        ]);

        // Sync
        register_rest_route($this->namespace, '/sync/(?P<type>[a-z]+)', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'start_sync'],
            'permission_callback' => [$this, 'check_permissions'],
            'args' => [
                'type' => [
                    'required' => true,
                    'type' => 'string',
                ],
            ],
        ]);

        // Webhooks
        register_rest_route($this->namespace, '/webhooks', [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'get_webhooks'],
                'permission_callback' => [$this, 'check_permissions'],
            ],
            [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => [$this, 'create_webhook'],
                'permission_callback' => [$this, 'check_permissions'],
            ],
        ]);

        register_rest_route($this->namespace, '/webhooks/(?P<id>\d+)', [
            'methods' => WP_REST_Server::DELETABLE,
            'callback' => [$this, 'delete_webhook'],
            'permission_callback' => [$this, 'check_permissions'],
            'args' => [
                'id' => [
                    'required' => true,
                    'type' => 'integer',
                ],
            ],
        ]);
    }

    /**
     * Проверка прав доступа
     */
    public function check_permissions()
    {
        return current_user_can('manage_options');
    }

    /**
     * Получить настройки
     */
    public function get_settings(WP_REST_Request $request)
    {
        $settings = get_option('wms_settings_auth', []);
        $is_connected = get_option('wms_moysklad_is_connect', false);

        return new WP_REST_Response([
            'wms_token' => $settings['wms_token'] ?? '',
            'is_connected' => $is_connected,
        ]);
    }

    /**
     * Сохранить настройки
     */
    public function save_settings(WP_REST_Request $request)
    {
        $params = $request->get_json_params();

        if (empty($params['wms_token'])) {
            return new WP_Error(
                'missing_token',
                'Токен API обязателен для работы',
                ['status' => 400]
            );
        }

        $settings = [
            'wms_token' => sanitize_text_field($params['wms_token']),
        ];

        update_option('wms_settings_auth', $settings);

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Настройки сохранены',
        ]);
    }

    /**
     * Проверить подключение
     */
    public function test_connection(WP_REST_Request $request)
    {
        $params = $request->get_json_params();
        
        if (empty($params['wms_token'])) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Токен не указан',
            ], 400);
        }

        try {
            // Проверка подключения через SDK
            $container = \WCSTORES\WC\MS\Kernel\Container::getInstance();
            $ms = $container->make(\Evgeek\Moysklad\MoySklad::class);
            
            // Пробуем получить список товаров (limit=1 для быстрой проверки)
            $result = $ms->query()
                ->entity()
                ->product()
                ->limit(1)
                ->get();
            
            $is_connected = true;
            update_option('wms_moysklad_is_connect', true);
            
            return new WP_REST_Response([
                'success' => true,
                'message' => 'Подключение успешно',
            ]);
        } catch (\Exception $e) {
            update_option('wms_moysklad_is_connect', false);
            
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Ошибка подключения: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Запустить синхронизацию
     */
    public function start_sync(WP_REST_Request $request)
    {
        $type = $request->get_param('type');
        $params = $request->get_json_params();

        // Здесь должна быть логика запуска синхронизации
        // Пока заглушка
        do_action('wms_start_sync', $type, $params);

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Синхронизация запущена',
        ]);
    }

    /**
     * Получить вебхуки
     */
    public function get_webhooks(WP_REST_Request $request)
    {
        // Здесь должна быть логика получения вебхуков
        // Пока заглушка
        return new WP_REST_Response([]);
    }

    /**
     * Создать вебхук
     */
    public function create_webhook(WP_REST_Request $request)
    {
        $params = $request->get_json_params();

        // Здесь должна быть логика создания вебхука
        return new WP_REST_Response([
            'success' => true,
            'message' => 'Вебхук создан',
        ]);
    }

    /**
     * Удалить вебхук
     */
    public function delete_webhook(WP_REST_Request $request)
    {
        $id = $request->get_param('id');

        // Здесь должна быть логика удаления вебхука
        return new WP_REST_Response([
            'success' => true,
            'message' => 'Вебхук удален',
        ]);
    }
}

