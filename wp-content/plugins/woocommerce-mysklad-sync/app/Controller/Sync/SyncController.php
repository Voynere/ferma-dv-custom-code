<?php


namespace WCSTORES\WC\MS\Controller\Sync;
use WCSTORES\WC\MS\Controller\Controller;

/**
 * Class SyncController
 * @package WCSTORES\WC\MS\Controller\Sync
 */
class SyncController extends Controller
{

    /**
     * @param $type
     * @param $action
     * @param $href
     * @return mixed
     */
    public function webhook($type, $action, $href)
    {
        return $type;
    }


}