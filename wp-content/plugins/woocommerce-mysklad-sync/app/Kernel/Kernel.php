<?php


namespace WCSTORES\WC\MS\Kernel;


/**
 * Class Kernel
 * @package WCSTORES\WC\MS\Kernel
 */
class Kernel extends Singleton
{
    /**
     * @var
     */
    protected $settings;

    /**
     * @throws \Exception
     */
    public function boot()
    {
        $this->settings = Config::file('app');
        if($this->settings){
            $this->initProviders();
        }

    }

    /**
     *
     */
    protected function initProviders()
    {
        foreach ($this->settings['providers'] as $provider) {
            try {
                $Provider = new $provider();
                $Provider->boot();
            }catch (\Exception $e){

            }

        }
    }

}