<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNeMas project
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   OpenNeMas
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

 
/**
 * Onm_View_Helper_Button
 * 
 * @package    Onm
 * @subpackage 
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Button.php 1 2010-05-03 21:08:49Z vifito $
 */
class Onm_View_Helper_Button
{
    private $type  = null; // Link, Submit, Javascript
    
    /**#@+
     * Mandatory properties
     * 
     * @access private
     * @var string
     */
    private $name  = null;
    private $text  = null;
    /**#@-*/
    
    /**#@+
     * Optional properties
     * 
     * @access private
     * @var string
     */
    private $title     = null;
    private $accesskey = null;
    private $tabindex  = null;
    private $style     = null;
    private $id        = null;
    private $lang      = null;
    private $rel       = null;
    /**#@-*/
    
    
    public function __construct($type, $name=null, $properties=array())
    {
        $this->loadProperties($properties);
        
        $this->type = $type;
        $this->name = $name; // used for attribute css class
    }
    
    
    /**
     * Load properties in this instance
     *
     * @param array $properties
     */
    public function loadProperties($properties)
    {
        foreach($properties as $k => $v) {
            if( property_exists($this, $k) ) {
                $this->{$k} = $v;
            }
        }
    }
    
    
    public static function _($type, $name, $properties=array())
    {
        $klass = 'Onm_View_Helper_Button_' . ucfirst($type);
        $instance = new $klass($name);
        $instance->loadProperties($properties);
        
        return $instance->render();
    }
    
    /**
     * Build uri using route name and query string parameters
     *
     * @param string $route
     * @param array $query
     * @return string 
     */
    protected function _assembleRoute($route, $query=array())
    {
        $fc = Zend_Controller_Front::getInstance();
        $router = $fc->getRouter();        
        $route  = $router->getRoute($route);
        
        $urlMvc = $route->assemble($query, $reset=true, $encode=true);
        
        return $urlMvc;
    }
    
    /**
     * Create a html representation of properties
     * 
     * @param array  $expand    List of specific attributes
     * @return string 
     */
    protected function _buildAttrs($expand=array())
    {
        $html = '';
        $commons = array('title', 'rel', 'accesskey', 'tabindex', 'style', 'id') + $expand;
        
        if(!is_null($this->lang)) {
            $html .= ' xml:lang="' . $this->lang . '"';
        }
        
        foreach($commons as $attr) {
            if( !is_null($this->{$attr}) ) {
                $html .= ' ' . $attr . '="' . $this->{$attr} . '"';
            }            
        }
        
        return $html;
    }
    
    
    /**
     * Extend this class and implement method render
     * otherwise throw an exception
     */
    public function render()
    {
        throw new Exception("Method render do not available");
    }
    
}