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
 * Onm_View_Helper_Toolbar_Javascript
 * 
 * @package    Onm
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Javascript.php 1 2010-05-03 21:08:49Z vifito $
 */
class Onm_View_Helper_Toolbar_Javascript extends Onm_View_Helper_Toolbar_Item
{
    public static $allowEvents = array(
        'blur', 'click', 'dblclick',
        'focus', 'mousedown', 'mousemove',
        'mouseout', 'mouseover', 'mouseup',
        'keydown', 'keypress', 'keyup'
    );
    
    /**
     * <code>
     * $events = array(
     *  'click'    => 'javascript:...',
     *  'dblclick' => 'javascript:...',
     * );
     * </code>
     *
     * @var
     */
    public $events = array();
    
    public $route  = null; // use to unobstructive code
    public $query  = array();
    
    public $href   = null;
    public $target = null;
    
    
    public function __construct($name, $text, $properties=array())
    {
        parent::__construct('Javascript', $name, $text, $properties);                
    }
    
    /**
     * 
     */
    public function render()
    {
        // Translate properties (text, title)
        $this->translateProperties();
        
        if(!is_null($this->route)) {
            $uri = $this->_assembleRoute($this->route, $this->query);
        } elseif(!is_null($this->href)) {
            $uri = $this->href;
        } else {
            $uri = '#';
        }
        
        $html = '<a href="' . $uri . '"' ;
        
        // Build common attributes
        $html .= $this->_buildAttrs(); // Do use "target" attribute?
        
        // Check and build javascript attributes onXxx
        $html .= $this->_buildEvents();
        
        $html .= '> <span class="' . $this->name . '">&nbsp;</span> ' . $this->text . ' </a>';
        
        return $html;

    }
}