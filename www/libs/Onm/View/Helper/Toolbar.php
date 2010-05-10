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
 

class Onm_View_Helper_Toolbar implements Iterator, ArrayAccess
{    
    private static $toolbarInstances = array();
    
    private $buttons = array();
    private $name    = null;
    
    /**
     * SEE: toolbar.css
     * @var array   Template array of tags
     */
    private $theme = array(
        'openToolbar'  => '<ul class="toolbar" id="%s">',
        'closeToolbar' => '</ul>',
        
        'openItemToolbar'  => '<li>',
        'closeItemToolbar' => '</li>',
    );
    
    private function __construct($name)
    {
        $this->name = $name;
    }
    
    public static function getInstance($name)
    {
        if(!isset(self::$toolbarInstances[$name])) {
            self::$toolbarInstances[$name] = new Onm_View_Helper_Toolbar($name);
        }
        
        return self::$toolbarInstances[$name];
    }
    
    /**
     * Get name of toolbar instance
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Get internal array of buttons
     *
     * @return array    Array of Onm_View_Helper_Toolbar_Item
     */
    public function getButtons()
    {
        return $this->buttons;
    }
    
    /**
     * Set internal array of buttons
     * 
     * @param array $buttons    Array of Onm_View_Helper_Toolbar_Item
     */
    public function setButtons($buttons)
    {
        $this->buttons = $buttons;
        return $this;
    }
    
    /**
     * Get a iterator
     * 
     * @return Iterator
     */
    public function getIterator()
    {
        $obj = new ArrayObject($this->buttons);        
        return $obj->getIterator();
    }
    
    /**
     * Get number of buttons
     * 
     * @return int  Return number of buttons
     */
    public function count()
    {
        return count($this->buttons);
    }
    
    /**
     * Append button
     *
     * @param Onm_View_Helper_Toolbar_Item $button
     * @return Onm_View_Helper_Toolbar  Return instance to chain methods
     */
    public function append(Onm_View_Helper_Toolbar_Item  $button)
    {
        $this->buttons[] = $button;
        return $this;
    }
    
    /**
     * Prepend a button
     *
     * @param Onm_View_Helper_Toolbar_Item $button
     * @return Onm_View_Helper_Toolbar  Return instance to chain methods
     */
    public function prepend(Onm_View_Helper_Toolbar_Item $button)
    {
        array_unshift($this->buttons, $button);
        return $this;
    }
    
    
    /**
     * Change theme to render toolbar
     * 
     * <code>
     * $themeDefault = array(
     *   'openToolbar'  => '<ul class="toolbar" id="%s">',
     *   'closeToolbar' => '</ul>',
     *   'openItemToolbar'  => '<li>',
     *   'closeItemToolbar' => '</li>',
     * );
     * </code>
     * @param array $theme
     * @return Onm_View_Helper_Toolbar  Return instance to chain methods
     */
    public function changeTheme($theme)
    {
        $this->theme = $theme;
        return $this;
    }
    
    /**
     * Helper to load multiple buttons
     * 
     * <code>
     * array(
     *   array('Link', 'text', 'css class name', array('href' => 'http://...')),
     *   array('Route', 'text', 'css class name', array('route' => 'controller-action')),
     *   array('Button', 'text', 'css class name', array('type' => 'submit')),
     * )
     * </code>
     * @param array $buttons
     * @return Onm_View_Helper_Toolbar  Return instance
     */
    public static function loadFromArray($name, $buttons)
    {
        $instance = Onm_View_Helper_Toolbar::getInstance($name);
        foreach($buttons as $btn) {
            $properties = (isset($btn[3]) && is_array($btn[3]))? $btn[3]: array();
            $instance->append( Onm_View_Helper_Toolbar_Item::_($btn[0], $btn[1], $btn[2], $properties) );
        }
        
        return $instance;
    }
    
    
    /**
     * Render a toolbar
     * 
     * @see Onm_View_Helper_Toolbar::$theme
     * @return string
     */
    public function render()
    {
        $output = sprintf($this->theme['openToolbar'], $this->name);        
        
        foreach($this->buttons as $i => $btn) {                        
            $output .= $this->theme['openItemToolbar'];
            
            // Set tabindex
            $btn->setTabindex($i + 1);
            
            $output .= $btn->render();
            $output .= $this->theme['closeItemToolbar'];
        }
        
        $output .= $this->theme['closeToolbar'];
        
        return $output;
    }
    
    /* Implements Iterator {{{ */
    function rewind() {
        reset($this->buttons);
    }

    function current() {
        return current($this->buttons);
    }

    function key() {
        return key($this->buttons);
    }

    function next() {
        next($this->buttons);
    }

    function valid() {
        return key($this->buttons) !== null;
    }
    /* }}} */
    
    
    /* Implements ArrayAccess {{{ */
    public function offsetSet($offset, $value) {
        if( !($value instanceof Onm_View_Helper_Toolbar_Item) ) {
            throw new Exception('Onm_View_Helper_Toolbar only works with Onm_View_Helper_Toolbar_Item instances.');
        }
        
        $this->buttons[$offset] = $value;        
    }
    
    public function offsetExists($offset) {
        return isset($this->buttons[$offset]);
    }
    
    public function offsetUnset($offset) {
        unset($this->buttons[$offset]);
    }
    
    public function offsetGet($offset) {
        return isset($this->buttons[$offset]) ? $this->buttons[$offset] : null;
    }
    /* }}} */
    
}