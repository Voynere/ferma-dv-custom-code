<?php


namespace WCSTORES\WC\MS\Providers;


use WCSTORES\WC\MS\Init\Init;

/**
 * Class InitProvider
 * @package WCSTORES\WC\MS\Providers
 */
class InitProvider extends Providers
{


    public function boot()
    {
        add_action('init', [new Init(), 'boot'], 100000000000000);
    }

}