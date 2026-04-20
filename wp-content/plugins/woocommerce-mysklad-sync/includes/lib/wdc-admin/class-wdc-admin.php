<?php


class Wdc_Admin
{
    public function run(){
        $this->config();
        $this->Loader();
        //...
    }

    public function config(){
        define("WDC_ROOT_DIR",dirname(__FILE__).'/');
        define("WDC_ADMIN_DIR",plugin_dir_url(__FILE__));


        require_once "autoload.php"; //автозагрузчик классов
    }

    public function Loader(){
        spl_autoload_register(['Wdc_Class_Loader', 'autoload']);

        try{
            //Пример добавления каталога в автозагрузчик классов
            //ClassLoader::$dir[] = 'view';

            //Пример добавления класса MyClass к карте классов
            //ClassLoader::$addMap['MyClass'] = 'folder/MyClass.php';

        } catch (Exception $e){
            echo '<h2>Внимание! Обнаружена ошибка.</h2>'.
                '<h4>'.$e->getMessage().'</h4>'.
                '<pre>'.$e->getTraceAsString().'</pre>';
            exit;
        }
    }

}