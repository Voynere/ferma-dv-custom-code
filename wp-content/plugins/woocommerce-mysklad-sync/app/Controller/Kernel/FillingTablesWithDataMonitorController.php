<?php


namespace WCSTORES\WC\MS\Controller\Kernel;


use Exception;
use WCSTORES\WC\MS\DB\DataStore\MoySkladEntityDataStore;
use WCSTORES\WC\MS\Queues\Queues;
use WmsLogs;

class FillingTablesWithDataMonitorController
{

    /**
     * @param $alerts
     * @return mixed
     * @throws \Exception
     */
    public function alerts($alerts)
    {
        try {
            $alerts[] = $this->getData();
        } catch (Exception $e) {
            WmsLogs::set_logs(get_class($this) . ' ' . $e->getMessage(), true);
        }

        return $alerts;

    }

    /**
     * @return string[]
     * @throws \Exception
     */
    public function getData(): array
    {
        $FillingTablesWithData = Queues::get([
            'hook' => 'wcstores_moysklad_queues_filling_tables_with_data',
            'status' => 'pending',
            'per_page' => 100
        ]);//in-progress


        if (is_array($FillingTablesWithData) and !empty($FillingTablesWithData)) {
            return [
                'type' => 'danger',
                'message' => 'Подождите идет загрузка данных с Мой Склад. Не закрывайте страницу.'
            ];
        }

        if (!$MoySkladEntitiesDataStore = MoySkladEntityDataStore::make()->get()) {
            return [
                'type' => 'danger',
                'message' => 'Внимание у вас не загружены данные с Мой склад.</br>
                               Вам нужно обязательно их прогрузить для работы плагина.</br>
                               Для загрузки вам нужно нажать кнопку Удалить кэш, пару раз.'
            ];
        }

        return [
            'type' => 'primary',
            'message' => 'Данные с Мой Склад загружены'
        ];

    }

    /**
     * @return array|string[]
     * @throws \Exception
     */
    public function getDataMonitoring(): array
    {
        $response = [];
        $allowed_types = [
            'pricetype' => 'Цены',
            'productfolder' => 'Категории',
            'store' => 'Склады',
            'organization' => 'Организации',
            'customerorder_state' => 'Статусы заказов',
            'product_attributes' => 'Атрибуты товаров'
        ];

        foreach ($allowed_types as $type => $title){

            try {
                if ($MoySkladEntitiesDataStore = MoySkladEntityDataStore::make()->getEntitiesByType($type,1000)) {
                    $response[] = [
                        'title' => $title,
                        'count' => count($MoySkladEntitiesDataStore)
                    ];
                }

            } catch (Exception $e) {
                WmsLogs::set_logs(get_class($this) . ' ' . $e->getMessage(), true);
                $response[] = [
                    'title' => $title,
                    'count' => 0
                ];
            }

        }


        return $response;

    }

}