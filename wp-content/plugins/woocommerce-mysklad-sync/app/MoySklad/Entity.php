<?php


namespace WCSTORES\WC\MS\MoySklad;


use WCSTORES\WC\MS\DB\DataStore\MoySkladEntityDataStore;
use WCSTORES\WC\MS\Init\Tables\FillingTablesWithData;


class Entity
{

    /**
     * @var
     */
    protected $path;

    /**
     * @var
     */
    protected $type;

    /**
     * @var int
     */
    protected $limit = 25;

    /**
     * @param null $href
     * @param int $limit
     * @param int $offset
     * @return bool|mixed
     * @throws \Exception
     */
    public function getDataByMoySklad($href = null, $limit = 1000, $offset = 0)
    {
        $href = ($href) ?? WMS_URL_API_V2 . $this->path . '?limit=' . $limit . '&offset=' . $offset;

        if (!$responseData = \WmsConnectApi::get_instance()->send_request($href)) {
            throw new \Exception('Нет данных');
        }

        return (isset($responseData['rows'])) ? $responseData['rows'] : $responseData;
    }

    /**
     * @param $parameters
     * @return string
     */
    public function fillingTablesWithData($parameters)
    {
        try {
            $limit = (isset($parameters['limit'])) ? $parameters['limit'] : $this->limit;
            $offset = (isset($parameters['offset'])) ? $parameters['offset'] : 0;

            if($offset === 0){
                MoySkladEntityDataStore::make()->clearTableByType($this->type);
            }

            $data = $this->getDataByMoySklad(null, $limit, $offset);
            $countData = count($data);

            if (empty($data)) {
                \WmsLogs::set_logs(get_class($this) .' Все данные обновлены', true);
                return 'Все данные обновлены';
            }

            if ($countData == $limit) {
                FillingTablesWithData::queueFillingTablesWithData(
                    get_class($this),
                    [
                        'limit' => $limit,
                        'offset' => $offset + $limit
                    ]
                );
            }

            if (empty($data)) {
                \WmsLogs::set_logs(get_class($this) .' Все данные обновлены', true);
                return 'Все данные обновлены';
            }

            foreach ($data as $item) {
               MoySkladEntityDataStore::make()->create([
                    'data' => $item,
                    'uuid' => $item['id'],
                    'type' => $this->type
                ]);
            }

        } catch (\Exception $e) {
            \WmsLogs::set_logs(get_class($this) . ' fillingTablesWithData ' . $e->getMessage(), true);
        }


    }

    /**
     * @param int $limit
     * @return mixed
     */
    public function getByStore($limit = 100)
    {
        return MoySkladEntityDataStore::make()->getEntitiesByType($this->type, $limit);
    }

    /**
     * @param $uuid
     * @return false
     */
    public function getByUuid($uuid)
    {
        if (preg_match("/^((https?|ftp)\:\/\/)?/i", $uuid)) {
            $uuid = \WmsHelper::get_id_ms_explode($uuid);
        }

        if (!$MoySkladEntityDataStore = MoySkladEntityDataStore::make()->getEntityByUuid($uuid)) {
            return false;
        }

        return (is_object($MoySkladEntityDataStore[0])) ? $MoySkladEntityDataStore[0]->data : false;
    }


    /**
     * @param int $limit
     * @return array
     */
    public function getByData($limit = 100)
    {
        $data = [];

        try {

            if ($dataStore = $this->getByStore($limit)) {
                foreach ($dataStore as $item) {
                    $data[$item->uuid] = $item->data;
                }
            }

        } catch (\Exception $e) {
            \WmsLogs::set_logs(get_class($this) . ' getByData ' . $e->getMessage(), true);
        }

        return $data;
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