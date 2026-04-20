<?php


namespace WCSTORES\WC\MS\Support\Main;


/**
 * Trait Props
 * @package WCSTORES\WC\MS\Support\Main
 */
trait Props
{
    /**
     * @var array
     */
    protected  $props = [];

    /**
     * @var
     */
    protected $allowedProps;



    /**
     * @param $name
     * @return array|mixed|null
     */
    public function __get($name)
    {
        return $this->getProps($name);
    }

    /**
     * @param $name
     * @param $value
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        $this->setProps([$name => $value], true);
    }

    /**
     * @param $name
     * @param $arguments
     * @return array|mixed|null
     */
    public function __call($name, $arguments) {
       if(stripos( 'get', $name) !== false){
           $name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
           return $this->getProps(str_replace('get_', '', $name));
       }

       return false;
    }


    /**
     * @param $props
     * @param bool $single
     * @throws \Exception
     */
    public function setProps($props, $single = false)
    {
        if(!is_array($props)){
            throw new \Exception('Props not array');
        }

        foreach ($props as $prop => $value){

            if(property_exists($this, $prop)){
                $this->$prop = $value;
            }

            if (!$single and is_array($this->getAllowedProps())){

                if(!in_array($prop, $this->allowedProps)){
                    throw new \Exception('Not Allowed prop');
                }

            }

            $this->props[$prop] = $value;

        }
    }

    /**
     * @return false
     */
    protected function getAllowedProps()
    {
        return false;
    }

    /**
     * @param null $prop
     * @return array|mixed
     */
    public function getProps($prop = null)
    {
        if($prop == 'all'){
            return $this->props;
        }elseif (property_exists($this, $prop)){
            return $this->$prop;
        }elseif (isset($this->props[$prop])){
            return $this->props[$prop];
        }

        return null;
    }


}