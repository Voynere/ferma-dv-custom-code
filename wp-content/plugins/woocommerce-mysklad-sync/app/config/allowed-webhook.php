<?php


return [
    'product' => [
        'actions' => ['CREATE', 'UPDATE', 'DELETE'],
        'handler' => WCSTORES\WC\MS\Controller\Sync\AssortmentController::class
    ],//товары
    'variant' => [
        'actions' => ['CREATE', 'UPDATE', 'DELETE'],
        'handler' => WCSTORES\WC\MS\Controller\Sync\AssortmentController::class
    ],//модификации
    'service' => [
        'actions' => ['CREATE', 'UPDATE', 'DELETE'],
        'handler' => WCSTORES\WC\MS\Controller\Sync\AssortmentController::class
    ],//услуги
    'supply' => [
        'actions' => ['CREATE', 'UPDATE', 'DELETE'],
        'handler' => WCSTORES\WC\MS\Controller\Sync\StockController::class
    ],//приемка
    'demand' => [
        'actions' => ['CREATE', 'UPDATE', 'DELETE'],
        'handler' => WCSTORES\WC\MS\Controller\Sync\StockController::class
    ],//отгрузка
    'enter' => [
        'actions' => ['CREATE', 'UPDATE', 'DELETE'],
        'handler' => WCSTORES\WC\MS\Controller\Sync\StockController::class
    ],//оприходование
    'loss' => [
        'actions' => ['CREATE', 'UPDATE', 'DELETE'],
        'handler' => WCSTORES\WC\MS\Controller\Sync\StockController::class
    ],//списание
    'retaildemand' => [
        'actions' => ['CREATE', 'UPDATE', 'DELETE'],
        'handler' => WCSTORES\WC\MS\Controller\Sync\StockController::class
    ],//розница продажа
    'move' => [
        'actions' => ['CREATE', 'UPDATE', 'DELETE'],
        'handler' => WCSTORES\WC\MS\Controller\Sync\StockController::class
    ],//перемещение
    'customerorder' => [
        'actions' => ['CREATE', 'UPDATE', 'DELETE'],
        'handler' => [
            WCSTORES\WC\MS\Controller\Sync\StockController::class,
            WCSTORES\WC\MS\Controller\Sync\OrderController::class
        ]
    ],//заказы
];