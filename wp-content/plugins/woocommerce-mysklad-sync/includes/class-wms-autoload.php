<?php


/**
 * Class Wms_Autoload
 */
class Wms_Autoload
{
    /**
     * @var
     */
    public static $class_map;
    /**
     * @var array
     */
    public static $add_map = array();
    /**
     * @var array
     */
    public static $dir = [
        'core/class_api',
        'core/abstracts',
        'connect',
        'core',
    ];

    //Добавить класс к карте классов

    /**
     * @param array $class
     */
    public static function add_class_map($class = array())
    {
        self::$add_map = array_merge(self::$add_map, $class);
    }

    /**
     * @param $class_name
     */
    public static function autoload($class_name)
    {

        //подключаем и сохраняем карту классов. Добавляем пользовательские классы.
        self::$class_map = array_merge(require(__DIR__ . '/classes.php'), self::$add_map);

        //Ищем в карте классов
        if (isset(self::$class_map[$class_name])) {
            $filename = self::$class_map[$class_name];
            include_once WMS_ROOT_DIR . $filename;
            //Ищем в папках
        } else {
            self::library($class_name);
        }

        //Проверка был ли объявлен класс
        if (!class_exists($class_name, false) && !interface_exists($class_name, false) && !trait_exists($class_name, false)) {
            //throw new Exception('Невозможно найти класс '.$class_name);
        }
    }

    /**
     * @param $class_name
     */
    public static function library($class_name)
    {
        foreach (self::$dir as $d) {
            $filename = WMS_ROOT_DIR . $d . '/' . $class_name . ".php";
            if (is_readable($filename)) {
                require_once $filename;
            }
        }
    }

}