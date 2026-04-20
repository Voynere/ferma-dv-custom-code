<?php


class Wms_Routes
{


    function rest_init()
    {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    /**
     * This is our callback function that embeds our resource in a WP_REST_Response
     */
    function cron($request)
    {
        $parameters = $request->get_query_params();

        if(!isset($parameters['nonce'])){
            exit('error');
        }
        
        $auth = $this->auth($parameters['nonce']);

        if($auth !== true){
            exit('error code s');
        }
        
        
        if(isset($parameters['start']) and $parameters['start'] === true){
            exit("start");
        }
        
       exit("success");
    }


    function auth($nonce)
    {
        if(!isset($nonce) or $nonce !== 'nonce'){
            return false;
        }
        return true;
    }

    function register_routes()
    {
        //// Обратимся по адресу http://wp-test.ru/wp-json/
        // register_rest_route() handles more arguments but we are going to stick to the basics for now.
        register_rest_route('wms/v1', '/cron', array(
            // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
            'methods' => WP_REST_Server::READABLE,
            // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array($this, 'cron'),
        ));
    }


} $route = new Wms_Routes();
    $route->rest_init();


 
