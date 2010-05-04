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
 * Onm_View_Helper_Button_Javascript
 * 
 * @package    Onm
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Javascript.php 1 2010-05-03 21:08:49Z vifito $
 */
class Onm_View_Helper_Button_Javascript extends Onm_View_Helper_Button
{
    private static $allowEvents = array(
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
    private $events = array();
    
    private $route  = null; // use to unobstructive code
    private $query = array();
    
    
    public function __construct($name, $properties=array())
    {
        parent::__construct('Javascript', $name, $properties);                
    }
    
    /**
     *
     * @return string
     */
    private function _buildEvents()
    {
        $html = '';
        
        foreach($this->events as $name => $code) {
            if(in_array($name, $this->allowEvents)) {
                $html .= 'on' . $name . '="' . $code . '"';
            }
        }
        
        return $html;
    }
    
    /**
     * 
     */
    public function render()
    {
        $uri = $this->_assembleRoute($this->route, $this->query);        
        $html = '<a href="' . $uri . '"' ;
        
        // Build common attributes
        $html .= $this->_buildCommons(); // Do use "target" attribute?
        
        // Check and build javascript attributes onXxx
        $html .= $this->_buildEvents();
        
        $html .= '> <span class="' . $this->name . '">&nbsp;</span> ' . $this->text . ' </a>';
        
        return $html;

    }
}