<?php /** @noinspection ALL */


namespace WCSTORES\WC\MS\Woocommerce\Stores;


use WCSTORES\WC\Exception\StoreException;

/**
 * Class Stores
 * @package WCSTORES\WC\MS\Woocommerce\Stores
 */
class Stores
{

    /**
     * @var
     */
    protected $iObjectId;

    /**
     * @var null
     */
    protected $oObject;

    /**
     * @var
     */
    protected $aSettings;

    /**
     * @var
     */
    protected $sSettingsName;

    /**
     * @var
     */
    protected $aData;


    /**
     * Stores constructor.
     * @param null $aData
     * @param null $oObject
     * @param null $aSettings
     * @throws StoreException
     */
    public function __construct($aData = null, $oObject = null, $aSettings = null)
    {
        if(!$aData){
            $this->exception('Пустой массив данных для сохранения');
        }

        $this->oObject = $oObject;
        $this->setSettings($aSettings);
        $this->setData($aData);


    }


    /**
     * @param string $sMessage
     * @param string $iObjectId
     * @throws StoreException
     */
    protected function exception($sMessage = '', $iObjectId = '')
    {
        throw new  StoreException('store_invalid', $sMessage, 400, array('resource_id' => $iObjectId));
    }

    /**
     * @return mixed
     */
    public function getSettings()
    {
        return $this->aSettings;
    }

    /**
     * @return mixed
     */
    public function getSettingsByName($sName)
    {
        return (isset($this->aSettings[$sName])) ? $this->aSettings[$sName] : null;
    }



    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->aData;
    }

    public function getObject()
    {
        return $this->oObject;
    }

    /**
     * @return mixed
     */
    public function getObjectId()
    {
        return $this->iObjectId;
    }

    /**
     * @param $iObjectId
     */
    public function setObjectId($iObjectId = null)
    {
        $this->iObjectId = $iObjectId;
    }


    /**
     * @param mixed $aData
     */
    public function setData($aData): void
    {
        $this->aData = $aData;
    }

    /**
     * @param $aSettings
     */
    public function setSettings($aSettings)
    {
        if ($aSettings == null) {
            $this->setSettings(get_option($this->sSettingsName));
        }

        $this->aSettings = $aSettings;
    }


}