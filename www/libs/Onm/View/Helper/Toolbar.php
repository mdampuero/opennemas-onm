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
 

class Onm_View_Helper_Toolbar
{    
    private static $toolbarInstances = array();
    
    private $buttons = array();
    private $name    = null;
    
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
    
    public function getName()
    {
        return $this->name;
    }
    
    public function appendButton(Onm_View_Helper_Toolbar_Item $button)
    {
        $this->buttons[] = $button;
        return $this;
    }
    
    public function prependButton(Onm_View_Helper_Toolbar_Item $button)
    {
        array_unshift($this->buttons, $button);
        return $this;
    }
    
    public function changeTheme($theme)
    {
        $this->theme = $theme;
        return $this;
    }
    
    public function getButtons()
    {
        return $this->buttons;
    }    
    
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
}