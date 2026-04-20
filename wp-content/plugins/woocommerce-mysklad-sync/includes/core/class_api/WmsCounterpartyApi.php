<?php
/**
 * Created by PhpStorm.
 * User: aqw
 * Date: 16.03.2018
 * Time: 20:43
 */

class WmsCounterpartyApi extends WmsData
{


    /**
     * @var WmsConnectApi
     */
    private $connect;

    /**
     * @var array
     */
    private $new_counterparty = array(
        'name' => 'Интернет покупатель',
        'companyType' => 'individual',
    );


    /**
     * @var
     */
    private $service;

    /**
     * @var
     */
    private $ms_counterparty;


    /**
     * @var
     */
    protected $ms_id;

    /**
     * @var
     */
    private $user;


    /**
     * WmsCounterpartyApi constructor.
     */
    public function __construct()
    {
        $this->connect = WmsConnectApi::get_instance();
        $this->set_settings('wms_settings_order');
    }


    /**
     * @return mixed|void
     */
    public function get_ms_id()
    {
        return apply_filters('wms_get_counterparty_id', $this->ms_id);
    }

    /**
     * @return bool|mixed
     * @throws Exception
     */
    public function get_counterpartys()
    {
        return $this->get(WMS_URL_API_V2 . '/entity/counterparty');
    }


    /**
     * @param $search
     *
     * @return bool|mixed
     * @throws Exception
     */
    public function search_counterparty($search)
    {

        foreach($search as $field => $value){
            if ( !empty( trim( $value ) ) ) {

                if($field === 'phone'){
                    $value = preg_replace("/[^0-9]/", '', $value);
                }
                
                $result = $this->connect->send_request(WMS_URL_API_V2 . '/entity/counterparty/?search=' . urlencode($value));
                
                 if ($result === false || !$result['meta']['size'] > 0) {
                     continue;
                 }

                return $result;
            }
        }

        return false;
    }


    /**
     * @param $data
     *
     * @return bool|mixed
     * @throws Exception
     */
    public function counterparty($data)
    {

        $this->set_name($data['name']);
        $this->set_email($data['email']);
        $this->set_phone($data['phone']);
        $this->set_actualAddress($data['address']);
        $this->set_tags($data['tags']);

        $search['email'] = $data['email'] ?: '';
        $search['phone'] = $data['phone'] ?: '';

        $result = $this->search_counterparty(apply_filters('wms_counterparty_search', $search, $data));

        if ($result === false or $result['meta']['size'] == 0) {
            $counterparty = $this->create_ms_counterparty();
            $mes = 'создан';

        } else {
            $this->ms_counterparty = apply_filters('wms_counterparty_update_data', $result['rows'][0], $data, $result, $this);

            if($this->ms_counterparty) {
                $counterparty = $this->update_ms_counterparty($result['rows'][0]['id']);
                $mes = 'обновлен';
            }
        }

        if (!isset($counterparty) or $counterparty == false) {

            $settings = $this->get_settings();
           if( !isset($settings['wms_order_counterparty_by_default']) or $settings['wms_order_counterparty_by_default'] == false){
               return false;
           }

            $result = $this->search_counterparty(apply_filters('wms_counterparty_search_by_default', $settings['wms_order_counterparty_by_default'], $data));
            if ($result === false or $result['meta']['size'] > 0) {
                $this->set_ms_id($result['rows'][0]['id']);
                return $result['rows'][0];
            }
            return false;
        }


        $this->set_ms_id($counterparty['id']);

        WmsLogs::set_logs('Контрагент ' . $this->new_counterparty['name'] . ' Успешо ' . $mes . ' в Мой склад', true);

        if (isset($this->user->ID)) {
            update_user_meta($this->user->ID, '_ms_user_id', $counterparty['id']);
        }


        return $counterparty;
    }


    /**
     * @param $ms_id
     */
    public function set_ms_id($ms_id)
    {
        $this->ms_id = $ms_id;
    }

    /**
     * @param $user
     */
    public function set_user($user)
    {
        $this->user = $user;
    }


    /**
     * @param $name
     */
    public function set_name($name)
    {
        $this->new_counterparty['name'] = !empty(trim($name)) ? $name : $this->new_counterparty['name'];
    }


    /**
     * @param $email
     */
    public function set_email($email)
    {
        $this->new_counterparty['email'] = $email;
    }


    /**
     * @param $phone
     */
    public function set_phone($phone)
    {
        $phone = trim($phone);
        if(isset($phone) and !empty($phone)){
            $this->new_counterparty['phone'] = $phone;
        }

    }

    /**
     * @param $phone
     */
    public function set_tags($tags)
    {
        $tags = trim($tags);
        if(isset($tags) and !empty($tags)){
            $this->new_counterparty['tags'] = [$tags];
        }
    }


    /**
     * @param $actualAddress
     */
    public function set_actualAddress($actualAddress)
    {
        $actualAddress = trim($actualAddress);
        if(isset($actualAddress) and !empty($actualAddress)){
            $this->new_counterparty['actualAddress'] = $actualAddress;
        }
    }


    /**
     * @param string $data
     *
     * @return bool|mixed
     */
    public function create_ms_counterparty($data = '')
    {
        $msData = empty($data) ?
            apply_filters('wms_create_ms_counterparty', $this->new_counterparty, $this->get_service()) :
            $data;

        $msData = apply_filters('wms_before_create_ms_counterparty', $msData, $data, $this->get_service());

        return $this->post(WMS_URL_API_V2 . '/entity/counterparty', $msData);;

    }

    /**
     * @param bool $id
     * @param string $data
     *
     * @return bool|mixed
     */
    public function update_ms_counterparty($id = false, $data = '')
    {

        if ($id == false) {
            return $this->create_ms_counterparty($data);
        }

        
        $msData = empty($data) ? 
        apply_filters('wms_update_ms_counterparty', $this->new_counterparty, $this->get_service()) :
        $data;
        
        $unsetUpdateFields = apply_filters('wms_unset_fields_ms_counterparty', array('name', 'tags','companyType','actualAddress'));
        
        foreach($unsetUpdateFields as $unsetUpdateField){            
            if(isset($this->ms_counterparty[$unsetUpdateField]) and !empty($this->ms_counterparty[$unsetUpdateField])){
                unset($msData[$unsetUpdateField]);
            }
        }
        
        $msData = apply_filters('wms_before_update_ms_counterparty', $msData, $data, $this->get_service());

        return $this->put(WMS_URL_API_V2 . '/entity/counterparty/' . $id, $msData);


    }


    /**
     * @return mixed
     */
    public function get_service()
    {
        return $this->service;
    }

    /**
     * @param mixed $service
     */
    public function set_service($service)
    {
        $this->service = $service;
    }

}