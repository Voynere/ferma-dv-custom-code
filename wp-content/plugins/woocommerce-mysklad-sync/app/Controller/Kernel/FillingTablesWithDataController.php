<?php

namespace WCSTORES\WC\MS\Controller\Kernel;

/**
 * Class FillingTablesWithDataController
 * @package WCSTORES\WC\MS\Controller\Kernel
 */
class FillingTablesWithDataController
{

    /**
     * @param $handlerData
     * @param $parameters
     * @return mixed
     * @throws \Exception
     */
    public function handler($handlerData, $parameters)
    {

        if(!class_exists($handlerData)){
            throw new \Exception('Class ' . $handlerData . ' not');
        }

        return (new $handlerData)->fillingTablesWithData($parameters);

    }

}