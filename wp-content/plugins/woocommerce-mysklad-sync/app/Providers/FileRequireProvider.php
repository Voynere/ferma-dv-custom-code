<?php


namespace WCSTORES\WC\MS\Providers;


/**
 * Class FileRequireProvider
 * @package WCSTORES\WC\MS\Providers
 */
class FileRequireProvider extends Providers
{

    /**
     * @var
     */
    protected $dir;

    /**
     *
     */
    public function boot(): void
    {
        $dir = str_replace('//', '/', $this->dir);

        if(!file_exists($dir) || !is_dir($dir)){
            return;
        }

        $files = scandir( $dir);

        if(!$files){
            return;
        }

        foreach ($files as $file){
            if(strpos($file,'php') !== false){
                require_once $dir . $file;
            }
        }


    }

}
