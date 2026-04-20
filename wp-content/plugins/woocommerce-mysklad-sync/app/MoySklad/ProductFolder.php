<?php


namespace WCSTORES\WC\MS\MoySklad;


class ProductFolder extends Entity
{

    /**
     * @var string
     */
    protected $path = '/entity/productfolder/';

    /**
     * @var string
     */
    protected $type = 'productfolder';


    /**
     * @param int $limit
     * @return array|\string[][]
     */
    public function getByData($limit = 100)
    {
        $data = parent::getByData($limit);

        if(empty($data)){
            return [];
        }

        return $data;
    }

}