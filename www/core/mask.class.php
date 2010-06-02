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
 * Mask
 * 
 * @package    Onm
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: mask.class.php 1 2010-03-30 11:23:23Z vifito $
 */
class Mask
{    
    private $_output  = null;
    private $_content = null;
    private $_mask    = null;
    
    
    /**
     * 
     * @param string $mask
     */
    public function __construct($mask=null)
    {
        if( !is_null($mask) ) {
            $this->setMask($mask);
        }
    }
    
    
    /**
     * Load values in associative array to current object ($this)
     * 
     * @param array $assocProps     Associative array 
     */
    public function loadProperties($assocProps, $object=null)
    {
        if(is_null($object)) {
            $object = $this;
        }
        
        foreach($assocProps as $prop => $val) {
            if(property_exists($object, $prop)) {
                $object->{$prop} = $val;
            }
        }        
    }
    
    
    /**
     *
     * @param Content $content
     * @return Mask
     */
    public function setContent($content)
    {
        $this->_content = $content;
        
        return $this;
    }
    
    public function setMask($mask)
    {
        $this->_mask = $mask;
        
        return $this;
    }
    
    public function setPage($page)
    {
        $this->_page = $page;
        
        return $this;
    }
    
    public function getPage()
    {
        return $this->_page;
    }
    
    // FIXME: implement mechanism to assign mask default
    public function getDefaultMask($pk_content_type)
    {
        return null;
    }
    
    /**
     * Apply template to contentbox
     * 
     * @return string
     */
    public function apply($args=array())
    {
        $mask = $this->_mask;
        if(is_null($mask)) {
            $mask = $this->getDefaultMask($this->_content->fk_content_type);
        }
        
        if(!is_null($mask)) {
            
            // FIXME: arranxar esta chapuza
            if(isset($args['page'])) {
                $this->setPage($args['page']);
            }
            
            $template = 'masks/' . $mask . '.tpl';
            $filename = SITE_PATH . '/themes/' . $this->_page->theme . '/tpl/' . $template;
            
            // TODO: improve mask flow
            if(!isset($args['renderMask']) && file_exists($filename)) {
                $tpl = new Template($this->_page->theme);                
            } else {
                $template = 'masks/' . $args['renderMask'] . '.tpl';
                $tpl = new TemplateAdmin(TEMPLATE_ADMIN);
            }            
            
            if(!empty($args)) {
                $tpl->assign('args', $args);
            }        
            
            $tpl->assign('mask', $mask);
            $tpl->assign('item', $this->_content);
            
            $this->_output = $tpl->fetch($template);
            
        } else {
            $this->_output = $this->_content->__toString();
        }
        
        return $this->_output;
    }
    
}