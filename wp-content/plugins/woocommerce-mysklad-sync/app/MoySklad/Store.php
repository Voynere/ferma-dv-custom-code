<?php


namespace WCSTORES\WC\MS\MoySklad;


class Store extends Entity
{

    /**
     * @var string
     */
    protected $path = '/entity/store';

    /**
     * @var string
     */
    protected $type = 'store';


    /**
     * @param int $limit
     * @return array|\string[][]
     */
    public function getByData($limit = 100)
    {
        $data = array_merge(
            ['all' => ['name' => 'Все склады']],
            parent::getByData($limit)
        );

        if(empty($data)){
            return ['all' => ['name' => 'Склады отсутствуют']];
        }

        return $data;
    }

}