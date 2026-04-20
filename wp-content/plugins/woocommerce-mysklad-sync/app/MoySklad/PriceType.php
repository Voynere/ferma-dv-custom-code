<?php


namespace WCSTORES\WC\MS\MoySklad;


class PriceType extends Entity
{

    /**
     * @var string
     */
    protected $path = 'context/companysettings/pricetype';

    /**
     * @var string
     */
    protected $type = 'pricetype';


    /**
     * @param int $limit
     * @return array|\string[][]
     */
    public function getByData($limit = 100)
    {
        $data = parent::getByData($limit);

        if(empty($data)){
            return ['not' => ['id' => '1', 'name' => 'Отсутствуют доступные цены']];
        }

        return $data;
    }

}