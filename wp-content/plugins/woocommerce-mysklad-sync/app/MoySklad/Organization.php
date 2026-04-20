<?php


namespace WCSTORES\WC\MS\MoySklad;


class Organization extends Entity
{

    /**
     * @var string
     */
    protected $path = '/entity/organization';

    /**
     * @var string
     */
    protected $type = 'organization';


    /**
     * @param int $limit
     * @return array|\string[][]
     */
    public function getByData($limit = 100)
    {
        $data = parent::getByData($limit);

        if(empty($data)){
            return [ ['id' => 'not', 'name' => 'Отсутствуют Организации']];
        }

        return $data;
    }

}