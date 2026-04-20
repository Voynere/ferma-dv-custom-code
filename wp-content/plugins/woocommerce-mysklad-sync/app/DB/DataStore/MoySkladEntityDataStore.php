<?php


namespace WCSTORES\WC\MS\DB\DataStore;


use WCSTORES\WC\MS\Facades\DB;

/**
 * Class MoySkladEntityDataStore
 * @package WCSTORES\WC\MS\DB\DataStore
 */
class MoySkladEntityDataStore extends DataStore
{

    /**
     * @var string
     */
    protected $tableName = 'wcstores_woo_moysklad_entities';

    /**
     * @var array
     */
    protected $allowedProps = ['id',  'data', 'uuid', 'type', 'create_time', 'update_time'];

    /**
     * @var string
     */
    protected $fieldIdName = 'id';

    /**
     * @param $Uuids
     * @param int $limit
     * @return array
     * @throws \Exception
     */
    public function getEntitiesByUuids($Uuids, $limit = 100)
    {
        if(empty($Uuids)){
            throw new \Exception('$Uuids не должен быть пустым');
        }


        return $this->get([
            'where' => [
                ['uuid', 'IN', $Uuids]
            ],
            'limit' => $limit
        ]);
    }

    /**
     * @param $Uuid
     * @return array
     * @throws \Exception
     */
    public function getEntityByUuid($Uuid)
    {
        if(empty($Uuid)){
            throw new \Exception('$Uuid не должен быть пустым');
        }


        return $this->get([
            'where' => [
                ['uuid',  $Uuid]
            ]
        ]);
    }


    /**
     * @param $type
     * @param int $limit
     * @return array
     * @throws \Exception
     */
    public function getEntitiesByType($type, $limit = 100): array
    {
        return $this->get([
            'where' => [
                ['type', $type]
            ],
            'limit' => $limit
        ]);
    }


    /**
     * @param $data
     * @return array|mixed|string
     * @throws \Exception
     */
    public function saveData($data)
    {
        if(md5(json_encode($this->data)) == md5(json_encode($data))){
            return 'Обновлять не нужно';
        }

        $this->data = $data;
        return $this->save();
    }

    /**
     * @param $type
     * @return mixed
     */
    public function clearTableByType($type)
    {
        $table = DB::prefix() . $this->tableName;

        return DB::query(
            DB::wpdb()->prepare(
                "DELETE FROM  $table WHERE type = %s",
                $type
            )
        );
    }

}