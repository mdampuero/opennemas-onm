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
 * Onm_View_Helper_Toolbar_Item
 * 
 * @package    Onm
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Button.php 1 2010-05-03 21:08:49Z vifito $
 */
class Onm_View_Helper_Toolbar_Item
{
    protected $type  = null; // Link, Submit, Javascript
    
    /**#@+
     * Mandatory properties
     * 
     * @access private
     * @var string
     */
    protected $name  = null;
    protected $text  = null;
    /**#@-*/
    
    /**#@+
     * Optional properties
     * 
     * @access private
     * @var string
     */
    protected $title     = null;
    protected $accesskey = null;
    protected $tabindex  = null;
    protected $style     = null;
    protected $id        = null;
    protected $lang      = null;
    protected $rel       = null;
    /**#@-*/
    
    
    public function __construct($type,  $name, $text, $properties=array())
    {
        $this->type = $type;
        $this->loadProperties($properties);
        
        $this->name = $name; // used for attribute css class
        $this->text = $text;
    }
    
    
    /**
     * Load properties in this instance
     *
     * @param array $properties
     */
    public function loadProperties($properties)
    {        
        if( is_array($properties) ) {            
            $klassName = 'Onm_View_Helper_Toolbar_' . $this->type;
            
            foreach($properties as $k => $v) {                
                if( property_exists($klassName, $k) ) {
                    $this->{$k} = $v;
                }
            }
        }
    }
    
    public function translateProperties($properties=array())
    {
        if( empty($properties) ) {
            $properties = array('title', 'text');
        } else {
            if(!in_array('title', $properties)) {
                $properties[] = 'title';
            }
            
            if(!in_array('text', $properties)) {
                $properties[] = 'text';
            }
        }        
        
        if( Zend_Registry::isRegistered('Zend_Translate') ) {
            $translate = Zend_Registry::get('Zend_Translate');
            
            foreach($properties as $prop) {
                if( property_exists($this, $prop) && !empty($this->{$prop}) ) {
                    $this->{$prop} = $translate->_($this->{$prop});
                }
            }
        }
    }
    
    
    public static function _($type, $name, $properties=array())
    {
        $klass = 'Onm_View_Helper_Toolbar_' . ucfirst($type);
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
        $baseUrl = $fc->getBaseUrl();
        $url = $baseUrl . '/' . $urlMvc;
        
        return $url;
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
        $commons = array_merge(array('title', 'rel', 'accesskey', 'tabindex', 'style', 'id'), $expand);        
        
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
     * 
     * 
     * @return string
     */
    protected function _buildEvents()
    {
        $html = '';
        
        if(isset($this->events) && is_array($this->events)) {
            foreach($this->events as $name => $code) {
                if(in_array($name, Onm_View_Helper_Toolbar_Javascript::$allowEvents)) {
                    $html .= 'on' . $name . '="' . $code . '"';
                }
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
    
    
    // Getters & Setters    
    /**
     *
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
    }    
    
    public function getAccesskey()
    {
        return $this->accesskey;
    }
    
    public function setAccesskey($accesskey)
    {
        $this->accesskey = $accesskey;
    }    
    
    public function getTabindex()
    {
        return $this->tabindex;
    }

    public function setTabindex($tabindex)
    {
        $this->tabindex = $tabindex;
    }
    
    public function getStyle()
    {
        return $this->style;
    }
    
    public function setStyle($style)
    {
        $this->style = $style;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getLang()
    {
        return $this->lang;
    }
    
    public function setLang($lang)
    {
        $this->lang = $lang;
    }
    
    public function getRel()
    {
        return $this->rel;
    }
    
    public function setRel($rel)
    {
        $this->rel = $rel;
    }
    
}