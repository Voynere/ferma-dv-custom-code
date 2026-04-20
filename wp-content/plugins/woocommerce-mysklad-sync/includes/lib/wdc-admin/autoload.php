<?php


class Wdc_Class_Loader
{
    public static $class_map;
    public static $add_map = array();
    public static $dir = [
        'class',
    ];

    //Добавить класс к карте классов
    public static function add_class_map($class = array()){
        self::$add_map = array_merge(self::$add_map, $class);
    }

    public static function autoload($class_name){

        //подключаем и сохраняем карту классов. Добавляем пользовательские классы.
        self::$class_map = array_merge(require(__DIR__ . '/classes.php'), self::$add_map);

        //Ищем в карте классов
        if (isset(self::$class_map[$class_name])) {
            $filename = self::$class_map[$class_name];
            include_once WDC_ROOT_DIR . $filename;
            //Ищем в папках
        } else {
            self::library($class_name);
        }

        //Проверка был ли объявлен класс
        if (!class_exists($class_name, false) && !interface_exists($class_name, false) && !trait_exists($class_name, false)) {
            //throw new Exception('Невозможно найти класс '.$class_name);
        }
    }

    public static function library($class_name){
        foreach (self::$dir as $d){
            $filename = WDC_ROOT_DIR . $d . '/'. $class_name . ".php";
            if (is_readable($filename)) {
                require_once $filename;
            }
        }
    }

}