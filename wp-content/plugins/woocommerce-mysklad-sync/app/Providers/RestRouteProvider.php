<?php


namespace WCSTORES\WC\MS\Providers;


use WCSTORES\WC\MS\Wordpress\Rest\RestRoute;

/**
 * Class RestRouteProvider
 * @package WCSTORES\WC\MS\Providers
 */
class RestRouteProvider extends Providers
{

    /**
     *
     */
    public function boot()
    {
        add_action('init', function () {
            RestRoute::getInstance()->setProps(['baseUrl' => 'wcstores/wc/moysklad/']);;
        });

        add_action('rest_api_init', function () {
            RestRoute::getInstance()->registerRoute();
        });
    }

}