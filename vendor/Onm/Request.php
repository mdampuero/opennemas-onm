<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm;
/**
 * Class for handling request parameters.
 *
 * @package    Onm
 * @subpackage Request
 * @author     Fran Dieguez <fran@openhost.es>
 **/
class Request
{
    
    /**
     * @var Onm\Request Singleton instance
     */
    protected static $_instance;
    
    /*
     * Initializes the Request object
     * 
     */
    public function __construct()
    {
        return $this;
    }
        /**
     * Retrieve singleton instance
     *
     * @return Zend_Loader_Autoloader
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /*
     * Gets all the request parameters
     * 
     */
    public function getParams()
    {
        return array_merge($_GET,$_POST);
    }
    
    /*
     * Get param given its name, first pick it in $_GET and after that from $_POST
     * 
     * @param $name
     */
    public function getParam($name="")
    {
        $return = $this->getGetParam($name);
        if (!is_null($return)) {
            $return = $this->getPostParam($name);
        }
        return $return;
    }
    
    /*
     * Gets one $_POST param given its name
     *
     * @param string $name,   the name of the arg to get info from
     * @param const  $filter,  the constant or multiple constants for filter the param
     * @param mixed  $default, the default value to return if $arg is not found
     *
     * @return mixed, the value for the key
     */
    public function getPostParam($name="", $filter = FILTER_DEFAULT, $default = null)
    {
        $options = array('options' => array( 'default' => $default));
        return filter_input ( INPUT_POST, $name , $filter, $options );
    }
    
    /*
     * Gets one param from $_GET array given its name, filtering it with PHP functions
     *
     * @param string $name, the name of the arg to get info from
     * @param const $filter, the constant or multiple constants for filter the param
     * @param mixed $default, the default value to return if $arg is not found
     *
     * @return mixed, the value for the key
     */
    public function getGetParam($name="", $filter = FILTER_DEFAULT, $default = null)
    {
        $options = array('options' => array( 'default' => $default));
        return filter_input ( INPUT_GET, $name , $filter, $options );
    }
}