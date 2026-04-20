<?php


namespace WCSTORES\WC\MS\Wordpress\Rest;


use WCSTORES\WC\MS\Kernel\Singleton;
use WCSTORES\WC\MS\Support\Main\Nonce;

/**
 * Class Route
 * @package WCS\WC\MS\WP\Rest
 */
class RestRoute extends Singleton
{

    /**
     * @var array
     */
    protected $aRoutes = [];

    /**
     * @var string
     */
    protected $baseUrl;


    /**
     * Route constructor.
     * @param array $args
     */
    protected function __construct($args = [])
    {
        if(!isset($this->baseUrl)){
            $this->baseUrl = (isset($args['baseUrl'])) ? $args['baseUrl'] : 'wcstores/';
        }

    }



    /**
     * @param $sPath
     * @param $mCallback
     * @param false $mPermissionCallback
     * @param string $version
     * @return mixed
     */
    public static function get($sPath, $mCallback, $mPermissionCallback = false, $version = 'v1')
    {
        return self::getInstance()->setRoute($version, 'GET', $sPath, ['callback' => $mCallback, 'permission_callback' => $mPermissionCallback]);
    }

    /**
     * @param $sPath
     * @param $mCallback
     * @param false $mPermissionCallback
     * @return mixed
     */
    public static function post($sPath, $mCallback, $mPermissionCallback = false, $version = 'v1')
    {
        return self::getInstance()->setRoute($version, 'POST', $sPath, ['callback' => $mCallback, 'permission_callback' => $mPermissionCallback]);
    }

    /**
     * @param string $path
     * @param bool $nonce
     * @return string|void
     * @throws \Exception
     */
    public static function getUrlRoute($path = '', $nonce = true)
    {
        $baseUrl = home_url('wp-json/' . self::getInstance()->getProps('baseUrl'));

        if($path){
            $baseUrl .= $path;
        }

        if($nonce){

            $nonce = (strpos($baseUrl, '?') === false) ?  '?': '&';
            $nonce .= '_nonce=' . Nonce::get();

            $baseUrl .=  $nonce;

        }


        return $baseUrl;
    }


    /**
     *
     */
    public function registerRoute()
    {

        if (!empty($this->aRoutes)) {

            foreach ($this->aRoutes as $sVersion => $aRoutesByMethod) {

                foreach ($aRoutesByMethod as $sMethod => $aRoutes) {
                    foreach ($aRoutes as $sPath => $aRoute) {

                        if ($mMethod = $this->getMethod($sMethod)) {

                            register_rest_route($this->baseUrl . $sVersion, $sPath, array(
                                'methods' => $mMethod,
                                'callback' => $aRoute['callback'],
                                'permission_callback' => function ($oRequest) use ($aRoute) {
                                    if($aRoute['permission_callback'] and $aRoute['permission_callback'] == 'nonce'){
                                        return Nonce::isValidate($oRequest);
                                    }

                                    return ($aRoute['permission_callback']) ? call_user_func($aRoute['permission_callback'], [$oRequest, $aRoute]) : $this->permissionCallback($oRequest, $aRoute);
                                },
                            ));

                        }
                    }
                }

            }


        }


    }


    /**
     * @param $sMethod
     * @return false|string
     */
    protected function getMethod($sMethod)
    {

        //\WP_REST_Server::ALLMETHODS
        switch ($sMethod) {
            case  'GET':
                $sMethod = \WP_REST_Server::READABLE;
                break;
            case  'POST':
                $sMethod = \WP_REST_Server::CREATABLE;
                break;

            default:
                $sMethod = false;
                break;
        }

        return $sMethod;
    }

    /**
     * @param $sVersion
     * @param $sMethod
     * @param $sPath
     * @param $aRoute
     * @return mixed
     */
    public function setRoute($sVersion, $sMethod, $sPath, $aRoute)
    {
        return $this->aRoutes[$sVersion][$sMethod][$sPath] = $aRoute;
    }


    /**
     * @param $oRequest
     * @param $aRoute
     * @return bool
     */
    protected function permissionCallback($oRequest, $aRoute)
    {
        return true;
    }

}