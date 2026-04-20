<?php


namespace WCSTORES\WC\MS\Init\Tables;

use WCSTORES\WC\MS\Queues\Queues;
use WCSTORES\WC\MS\Wordpress\Actions\Filters;

/**
 * Class FillingTablesWithData
 * @package WCSTORES\WC\MS\Init\Tables
 */
class FillingTablesWithData
{
    /**
     *
     */
    public static function boot()
    {
        $handlerTablesWithData = Filters::apply('filling_tables_with_data_handlers', [
            [
                'handlerData' => \WCSTORES\WC\MS\MoySklad\CustomerOrderStates::class,
                'parameters' => ['limit' => 100, 'offset' => 0]
            ],
            [
                'handlerData' => \WCSTORES\WC\MS\MoySklad\PriceType::class,
                'parameters' => ['limit' => 100, 'offset' => 0]
            ],
            [
                'handlerData' => \WCSTORES\WC\MS\MoySklad\ProductFolder::class,
                'parameters' => ['limit' => 1000, 'offset' => 0]
            ],
            [
                'handlerData' => \WCSTORES\WC\MS\MoySklad\Store::class,
                'parameters' => ['limit' => 1000, 'offset' => 0]
            ],
            [
                'handlerData' => \WCSTORES\WC\MS\MoySklad\Organization::class,
                'parameters' => ['limit' => 1000, 'offset' => 0]
            ]

        ]);

        foreach ($handlerTablesWithData as $item){
            static::queueFillingTablesWithData($item['handlerData'], $item['parameters']);
        }

        return ['ok'];

    }

    /**
     * @param $handlerData
     * @param array $parameters
     * @throws \Exception
     */
    public static function queueFillingTablesWithData($handlerData, $parameters = [])
    {
        Queues::addAsync(
            'filling_tables_with_data',
            [
                'handlerData' => $handlerData,
                'parameters' => $parameters
            ]
        );

    }




}