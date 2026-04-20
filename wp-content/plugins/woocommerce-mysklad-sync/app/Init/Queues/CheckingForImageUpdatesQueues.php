<?php


namespace WCSTORES\WC\MS\Init\Queues;


use WCSTORES\WC\MS\Queues\Queues;

/**
 * Class СheckingForImageUpdatesQueues
 * @package WCSTORES\WC\MS\Init
 */
class CheckingForImageUpdatesQueues
{

    /**
     *
     */
    public static function boot()
    {
        try {
            if (isset($_REQUEST['page']) and $_REQUEST['page'] == 'wms-settings-page') {

                $settings = get_option('wms_settings_product', []);

                $isCheckingForImageUpdates = false;
                $currentTime = $time = ((isset($settings['wms_load_image_time']) and $settings['wms_load_image_time'] > 0) ? $settings['wms_load_image_time'] : 5) * 60;

                $CheckingForImageUpdatesPending = Queues::get([
                    'hook' => WCSTORES_PREFIX . 'queues_' . 'checking_for_image_updates',
                    'status' => 'pending',
                    'per_page' => 1
                ]);//in-progress

                if(is_array($CheckingForImageUpdatesPending) and !empty($CheckingForImageUpdatesPending)) {
                    $CheckingForImageUpdates = $CheckingForImageUpdatesPending;
                }else{
                    $CheckingForImageUpdates = Queues::get([
                        'hook' => WCSTORES_PREFIX . 'queues_' . 'checking_for_image_updates',
                        'status' => 'in-progress',
                        'per_page' => 1
                    ]);

                }

                if(is_array($CheckingForImageUpdates) and !empty($CheckingForImageUpdates)) {
                    $CheckingForImageUpdates = array_shift($CheckingForImageUpdates);

                    if($CheckingForImageUpdates instanceof \ActionScheduler_Action) {
                        $isCheckingForImageUpdates = true;
                        $currentTime = $CheckingForImageUpdates->get_schedule()->get_recurrence();
                    }
                }

                if($currentTime != $time){
                    $isCheckingForImageUpdates = false;
                    Queues::unscheduleAllActions('checking_for_image_updates', [], 'image_updates');
                    \WmsLogs::set_logs('Перезапуск проверки изображений, изменено время.', true);
                }

                if(isset($settings['wms_load_image']) && ($settings['wms_load_image'] == 'on' || $settings['wms_load_image'] == 'all')){

                    if(!$isCheckingForImageUpdates){
                        Queues::addRecurring(time() + $time, $time, 'checking_for_image_updates', [], 'image_updates');
                    }

                }else{
                    Queues::unscheduleAllActions('checking_for_image_updates', [], 'image_updates');
                }
            }
        }catch (\Exception $e){
            \WmsLogs::set_logs($e->getMessage(), true);
        }
    }

}