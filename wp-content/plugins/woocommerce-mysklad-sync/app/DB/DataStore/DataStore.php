<?php


namespace WCSTORES\WC\MS\DB\DataStore;

use WCSTORES\WC\MS\Facades\DB;
use WCSTORES\WC\MS\Support\Main\Props;

/**
 * Class DataStore
 * @package WCSTORES\WC\MS\DB\DataStore
 */
class DataStore
{
    use Props;

    /**
     * @var
     */
    protected $tableName;


    /**
     * @var int[]
     */
    protected $baseArgsQuery = ['limit' => 5];

    /**
     * @var bool
     */
    protected $time = true;

    /**
     * @var
     */
    protected $fieldIdName;

    /**
     * @param $args
     * @return array
     * @throws \Exception
     */
    public function get($args = [])
    {
        try {
            $args = array_merge($this->baseArgsQuery, $args);

            DB::select();
            DB::from($this->tableName);

            if (isset($args['where'])) {
                foreach ($args['where'] as $where) {
                    DB::where(...$where);
                }
            }

            DB::limit($args['limit']);

            if ($this->time) {
                // DB::limit($args['limit']);
                //$data['update_time'] = $data['create_time'] = date("Y-m-d H:i:s");
            }

            return $this->getDataStores(DB::get($this->tableName));

        } catch (\Exception $e) {
            \WmsLogs::set_logs(get_class($this) . ' ' . $e->getMessage(), true);
        }

        return [];

    }

    /**
     * @param $args
     * @return mixed
     */
    public function getCount($args)
    {
        return DB::getCount($this->tableName, $args);
    }


    /**
     * @param $results
     * @return array
     * @throws \Exception
     */
    public function getDataStores($results)
    {
        $dataStores = [];

        if ($results) {
            $currentClassName = get_class($this);
            foreach ($results as $result) {
                $object = new  $currentClassName();
                $object->setProps(
                    array_map(function ($result) {
                        return maybe_unserialize($result);
                    },
                        get_object_vars($result)
                    ));

                $dataStores[] = $object;
            }

        }

        return $dataStores;

    }

    /**
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function create($data)
    {

        try {

            if ($this->time and is_array($data)) {
                $data['update_time'] = $data['create_time'] = date("Y-m-d H:i:s");
            }

            $this->setProps($data);

            if (!empty($this->props)) {
                return DB::insert($this->tableName, $this->props);
            }
        } catch (\Exception $e) {
            \WmsLogs::set_logs(get_class($this) . ' ' . $e->getMessage(), true);
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function save()
    {
        try {

            if ($this->fieldIdName) {

                $this->setProps(['update_time' => date("Y-m-d H:i:s")]);
                $data = $this->props;

                if (isset($data[$this->fieldIdName])) {
                    $id = $data[$this->fieldIdName];
                    unset($data[$this->fieldIdName]);

                    if (!DB::update($this->tableName, $data, [$this->fieldIdName => $id])) {
                        throw new \Exception('error save');
                    }

                    return $this->get(['where' => [
                        [$this->fieldIdName, $id]
                    ]]);


                }
            }

        } catch (\Exception $e) {
            \WmsLogs::set_logs(get_class($this) . ' ' . $e->getMessage(), true);
        }

        return [];
    }

    /**
     * @return mixed
     */
    public function deleteById()
    {
        try {
            return DB::deleteById($this->tableName, $this->fieldIdName, (int)$this->{$this->fieldIdName});
        } catch (\Exception $e) {
            \WmsLogs::set_logs(get_class($this) . ' ' . $e->getMessage(), true);
        }

    }

    /**
     * @return mixed
     */
    public function clearTable()
    {
        return DB::clearTable($this->tableName);
    }


    /**
     * @return array
     */
    protected function getAllowedProps()
    {
        return ($this->allowedProps and is_array($this->allowedProps) and !empty($this->allowedProps)) ? $this->allowedProps : [];
    }


    /**
     * @return object
     */
    public static function make(): object
    {
        return (new static);
    }



    /**
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return static::$method(...$parameters);
    }

}