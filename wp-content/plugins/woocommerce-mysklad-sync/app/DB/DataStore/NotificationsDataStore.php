<?php


namespace WCSTORES\WC\MS\DB\DataStore;

/**
 * Class NotificationsDataStore
 * @package WCSTORES\WC\MS\DB\DataStore
 */
class NotificationsDataStore extends DataStore
{
    /**
     * @var string
     */
    protected $tableName = 'wcstores_woo_moysklad_notifications';

    /**
     * @var array
     */
    protected $allowedProps = ['id',  'data', 'status', 'type', 'create_time', 'update_time'];

    /**
     * @var string
     */
    protected $fieldIdName = 'id';


    /**
     * @param int $limit
     * @return array
     * @throws \Exception
     */
    public function getNotificationsByStatusNew($limit = 5)
    {
        return $this->get([
            'where' => [
                ['status', 'new']
            ],
            'limit' => $limit
        ]);
    }

    /**
     * @return array|mixed
     * @throws \Exception
     */
    public function complete()
    {
        return $this->updateStatus('complete');
    }


    /**
     * @param $status
     * @return array|mixed
     * @throws \Exception
     */
    public function updateStatus($status)
    {
        $this->status = $status;
        return $this->save();
    }

}