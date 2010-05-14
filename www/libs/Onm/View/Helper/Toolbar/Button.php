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
 * Onm_View_Helper_Toolbar_Link
 * 
 * @package    Onm
 * @subpackage 
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Button.php 1 2010-05-03 21:08:49Z vifito $
 */
class Onm_View_Helper_Toolbar_Button extends Onm_View_Helper_Toolbar_Item
{
    /**#@+
     * optional properties
     * 
     * @access public
     * @var string
     */
    public $type   = null;    
    public $events = array();
    public $name   = null;
    public $value  = null;
    /**#@-*/
    
    
    /**
     * Construct
     *
     * @param string $text  Text of item
     * @param string $icon  Name of css class
     * @param array  $properties    Properties of item
     */    
    public function __construct($text, $icon=null, $properties=array())
    {
        parent::__construct('Button', $text, $icon, $properties);                
    }        
    
    /**
     * Return a HTML representation of a button
     *
     * @return string 
     */
    public function render()
    {
        // Translate properties (text, title)
        $this->translateProperties();        
        
        $html = '<button ' ;
        
        $html .= $this->_buildAttrs(array('type', 'name', 'value'));
        
        $html .= $this->_buildEvents();
        
        $html .= ' class="' . $this->icon . '"> ' . $this->text . ' </button>';
        
        return $html;
    }
    
}