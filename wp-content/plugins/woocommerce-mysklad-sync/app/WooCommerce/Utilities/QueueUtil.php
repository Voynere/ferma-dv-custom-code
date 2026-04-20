<?php


namespace WCSTORES\WC\MS\WooCommerce\Utilities;


use ActionScheduler;
use Exception;
use WmsLogs;

/**
 * Class QueueUtil
 * @package WCSTORES\WC\MS\WooCommerce\Utilities
 */
class QueueUtil
{
    /**
     *
     */
    const URL_ACTION = 'wcstores_start_run_queue';

    /**
     * @param $queue_id
     */
    public static function runQueueAsync($queue_id)
    {
        $response = wp_remote_post(
            WC()->api_request_url( self::URL_ACTION),
            array(
                'body' => json_encode([ 'queue_id' => $queue_id]),
                'timeout' => 2,
                'redirection' => 0,
                'blocking' => false,
                'sslverify' => false,
            )
        );

        if ( is_wp_error( $response ) ){
            WmsLogs::set_logs($response->get_error_message(), 'error');
        }

    }

    /**
     *
     */
    public static function actionSchedulerRun()
    {
        $data = file_get_contents('php://input');

        $data = json_decode($data, true);

        if (!is_array($data)) {

            WmsLogs::set_logs(__('actionSchedulerRun Wrong data', 'woocommerce'), 'error');

            wp_send_json_error(
                array(
                    'success' => false,
                    'message' => __(' actionSchedulerRun Wrong data', 'woocommerce'),
                )
            );

        }

        if (!isset($data['queue_id'])) {

            WmsLogs::set_logs(__('actionSchedulerRun is not queue_id', 'woocommerce'), 'error');

            wp_send_json_error(
                array(
                    'success' => false,
                    'message' => __('actionSchedulerRun is not queue_id', 'woocommerce'),
                ),
                401
            );
        }

        try {

            ActionScheduler::runner()->process_action($data['queue_id']);

            wp_send_json_success(
                array(
                    'success' => true,
                    'message' => 'actionSchedulerRun ok',
                )
            );

        } catch (Exception $e) {

            WmsLogs::set_logs(__($e->getMessage(), 'woocommerce'), 'error');

            wp_send_json_error(
                array(
                    'success' => true,
                    'message' => $e->getMessage(),
                )
            );

        }

    }

}