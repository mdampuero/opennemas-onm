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
 * @package    Core
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: mask.class.php 1 2010-03-30 11:23:23Z vifito $
 */
class Mask
{    
    private $name    = null;
    private $output  = null;
    private $content = null;    
    private $page    = null;
    
    
    /**
     * 
     * @param string $mask
     */
    public function __construct($name=null)
    {
        if( !is_null($name) ) {
            $this->setName($name);
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
        $this->content = $content;
        
        return $this;
    }
    
    
    /**
     *
     * @return Content
     */
    public function getContent()
    {
        return $this->content;
    }
    
    
    /**
     * Set name of mask
     * 
     * @param string $name
     * @return Mask
     */
    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }
    
    
    /**
     * Get name of mask
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    
    /**
     * Set page
     *
     * @param Page $page
     * @return Mask
     */
    public function setPage($page)
    {
        $this->page = $page;
        
        return $this;
    }
    
    
    /**
     * Get page
     *
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }
    
    
    // FIXME: implement mechanism to assign mask default
    public function getDefaultMask($pkcontent_type)
    {
        return null;
    }
    
    /**
     * Apply template to contentbox
     * 
     * @return string
     */
    //public function apply($args=array())
    //{
    //    $maskName = $this->name;
    //    
    //    if(!is_null($maskName) && !empty($maskName)) {            
    //        $template = 'masks/' . $maskName . '.tpl';
    //        $filename = SITE_PATH . '/themes/' . $this->page->theme . '/tpl/' . $template;
    //        
    //        // TODO: improve mask flow
    //        if(!isset($args['renderMask']) && file_exists($filename)) {
    //            
    //            $tpl = new Template($this->page->theme);
    //            
    //        } elseif(isset($args['renderMask'])) {
    //            
    //            $template = 'masks/' . $args['renderMask'] . '.tpl';
    //            $tpl = new TemplateAdmin(TEMPLATE_ADMIN);                
    //        }            
    //        
    //        if(!empty($args)) {
    //            $tpl->assign('args', $args);
    //        }        
    //        
    //        // Assign object mask
    //        $tpl->assign('mask', $this);
    //        
    //        // Helpers to get objects page and content
    //        $tpl->assign('content', $this->getContent());
    //        $tpl->assign('page',    $this->getPage());
    //        
    //        $this->output = $tpl->fetch($template);
    //        
    //    } else {
    //        $this->output = $this->content->__toString();
    //    }
    //    
    //    return $this->output;
    //}
    
    public function apply($args=array())
    {        
        $maskName = $this->name;
        
        // Frontend
        if(!is_null($maskName) && !empty($maskName)) {            
            $template = 'masks/' . $maskName . '.tpl';
            $filename = SITE_PATH . '/themes/' . $this->page->theme . '/tpl/' . $template;
            $tpl = new Template($this->page->theme);
            
            if(!empty($args)) {
                $tpl->assign('args', $args);
            }        
            
            // Assign object mask
            $tpl->assign('mask', $this);
            
            // Helpers to get objects page and content
            $tpl->assign('content', $this->getContent());
            $tpl->assign('page',    $this->getPage());
            
            $this->output = $tpl->fetch($template);
            
        } else {
            $this->output = $this->content->__toString();
        }
        
        // Backend
        if(isset($args['renderMask'])) {                
            $template = 'masks/' . $args['renderMask'] . '.tpl';
            $tplBe = new TemplateAdmin(TEMPLATE_ADMIN);            
            
            // Assign object mask
            $tplBe->assign('mask', $this);
            
            // Helpers to get objects page and content
            $tplBe->assign('content', $this->getContent());
            $tplBe->assign('page',    $this->getPage());
            
            $args['preview'] = $this->output;
            if(!empty($args)) {
                $tplBe->assign('args', $args);
            }
            
            $this->output = $tplBe->fetch($template);
        }
        
        return $this->output;
    }
}