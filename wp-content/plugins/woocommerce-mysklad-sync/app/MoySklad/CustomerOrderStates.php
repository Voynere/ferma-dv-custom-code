<?php


namespace WCSTORES\WC\MS\MoySklad;


class CustomerOrderStates extends Entity
{

    /**
     * @var string
     */
    protected $path = '/entity/customerorder/metadata';

    /**
     * @var string
     */
    protected $type = 'customerorder_state';

    /**
     * @param null $href
     * @param int $limit
     * @param int $offset
     * @return bool|mixed
     * @throws \Exception
     */
    public function getDataByMoySklad($href = null, $limit = 1000, $offset = 0)
    {
        $data = parent::getDataByMoySklad($href, $limit, $offset);

        if(!isset($data['states'])){
            throw new \Exception('Нет доступных статусов');
        }

        return  $data['states'];
    }


    /**
     * @param int $limit
     * @return array|\string[][]
     */
    public function getByData($limit = 100)
    {
        $data = parent::getByData($limit);

        if(empty($data)){
            return [ ['id' => 'not', 'name' => 'Отсутствуют доступные статусы']];
        }

        return $data;
    }

}