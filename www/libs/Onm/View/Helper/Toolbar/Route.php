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
 * Onm_View_Helper_Toolbar_Route
 * 
 * @package    Onm
 * @subpackage 
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Button.php 1 2010-05-03 21:08:49Z vifito $
 */
class Onm_View_Helper_Toolbar_Route extends Onm_View_Helper_Toolbar_Item
{
    public $route = null;
    public $query = array();
    
    public $target = null;
    
    /**
     * Construct
     *
     * @param string $text  Text of item
     * @param string $icon  Name of css class
     * @param array  $properties    Properties of item
     */
    public function __construct($text, $icon, $properties=array())
    {
        parent::__construct('Route', $text, $icon, $properties);                
    }        
    
    /**
     * 
     */
    public function render()
    {
        // Translate properties (text, title)
        $this->translateProperties();
        
        $uri = $this->_assembleRoute($this->route, $this->query);        
        $html = '<a href="' . $uri . '"' ;
        
        $html .= $this->_buildAttrs(array('target'));                
        
        $html .= ' class="' . $this->icon . '"> ' . $this->text . ' </a>';
        
        return $html;
    }
    
}