<?php
//add_action( 'wms_logs',  array('WmsLogs',  'get_button'));
//add_action( 'wp_ajax_my_action_logs',  array('WmsLogs',  'get_log'));
//add_action( 'wp_ajax_nopriv_my_action_logs',  array('WmsLogs',  'get_log' ));

class WmsLogs
{

    static public function init()
    {
        add_action('wms_logs', array('WmsLogs', 'wms_get_logs_page'));

    }

    static function wms_get_logs_page()
    {
        printf(
                '<div><a href="%s" target="_blank">%s</a></div>',
                esc_url(admin_url( 'admin.php?page=wc-status&tab=logs&source=wc-ms-sync&paged=1' )),
            esc_html('Посмотреть журнал')
        );

    }



    static public function set_logs($data = '', $type = 'info'): void
    {

        if ( !function_exists( 'wc_get_logger' ) || empty($data)) {
            return;
        }

        $logger = wc_get_logger();
        $logger_type = (method_exists($logger, $type)) ? $type : 'info';

        if ( $type === 'debug' ) {
            $backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 2 )[1]; //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace

            $code_source  = isset( $backtrace['class'] ) ? $backtrace['class'] . '::' : '';
            $code_source .= $backtrace['function'] ?? '';


            $data = array(
                'source' => $code_source,
                'data'   => $data,
            );

        }

        $logger->{$logger_type}(
            print_r( $data, true ), // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
            array( 'source' => 'wc-ms-sync' )
        );
    }

}
