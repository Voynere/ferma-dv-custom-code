<?php


namespace WCSTORES\WC\MS\DB\DataStore;


/**
 * Class ImageUpdatesQueuesDataStore
 * @package WCSTORES\WC\MS\DB\DataStore
 */
class ImageUpdatesQueuesDataStore extends DataStore
{

    /**
     * @var string
     */
    protected $tableName = 'wcstores_woo_moysklad_image_update_queues';

    /**
     * @var array
     */
    protected $allowedProps = ['queue_id', 'product_id', 'data', 'status', 'create_time', 'update_time'];

    /**
     * @var string
     */
    protected $fieldIdName = 'queue_id';


    /**
     * @param int $limit
     * @return array
     * @throws \Exception
     */
    public function getQueuesByStatusPending($limit = 5)
    {
        return $this->get([
            'where' => [
                ['status', 'pending']
            ],
            'limit' => $limit
        ]);
    }

    public function getQueuesByProductId($product_id, $status = 'pending', $limit = 1): array
    {
        return $this->get([
            'where' => [
                ['product_id', $product_id],
                ['status', $status],
            ],
            'limit' => $limit
        ]);
    }

    /**
     * @return mixed
     */
    public function getCountQueuesByStatusPending()
    {
        return $this->getCount([
                ['field' => 'status', 'operator' => '=', 'value' => 'pending']
            ]
        );
    }

    /**
     * @return array|mixed|DataStore
     * @throws \Exception
     */
    public function inProgress()
    {
        return $this->updateStatus('inProgress');
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
     * @return array|mixed
     * @throws \Exception
     */
    public function failed()
    {
        return $this->updateStatus('failed');
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