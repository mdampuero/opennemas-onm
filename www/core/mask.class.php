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
 * @package    Core
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * Mask
 * 
 * @package    Core
 * @subpackage FrontManager
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: mask.class.php 1 2010-03-30 11:23:23Z vifito $
 */
class Mask
{
    /**
     * @var string
     */
    private $name = null;
    
    /**
     * @var string
     */
    private $output = null;
    
    /**
     * @var Content
     */
    private $content = null;
    
    /**
     * @var Page
     */
    private $page = null;
    
    /**
     * @var ContentBox 
     */
    private $contentBox = null;
    
    
    /**
     * Constructor
     * 
     * @param string $name  Mask name 
     * @param array $properties
     */
    public function __construct($name=null, $properties=null)
    {
        if(!is_null($properties)) {
            $this->loadProperties($properties, $this);
        }        
        
        if( !is_null($name) ) {
            $this->setName($name);
        }
    }    
    
    
    /**
     * Get content
     * 
     * @return Content
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * Set content
     * 
     * @param Content $content
     * @return Mask
     */
    public function setContent(Content $content)
    {
        $this->content = $content;
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
     * Get page
     *
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }
    
    /**
     * Set page
     *
     * @param Page $page
     * @return Mask
     */
    public function setPage(Page $page)
    {
        $this->page = $page;
        return $this;
    }
    
    
    /**
     * Get html content rendered previously
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }
    
    /**
     * Set output mask
     *
     * @param string $output
     * @return Mask
     */
    public function setOutput($output)
    {
        $this->output = $output;
        return $this;
    }
    
    
    /**
     * Get ContentBox
     * 
     * @return ContentBox
     */
    public function getContentBox()
    {
        return $this->contentBox;
    }
    
    /**
     * Set ContentBox
     *
     * @param ContentBox $contentBox
     * @return Mask
     */
    public function setContentBox(ContentBox $contentBox)
    {        
        $this->contentBox = $contentBox;
        return $this;
    }
    
    
    /**
     * Apply mask to content
     *
     * @param array $args
     * @return string
     */
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
            $this->content->_invokeArgs = array(
                'page' => $this->getPage(),
                'mask' => $this,
            );
            $this->output = $this->content->__toString();
        }
        
        // Backend <div> wrapper
        if(isset($args['renderMask'])) {                
            $template = 'masks/' . $args['renderMask'] . '.tpl';
            $tplBe = new TemplateAdmin(TEMPLATE_ADMIN);            
            
            // Assign object mask
            $tplBe->assign('mask', $this);
            
            // Helpers to get objects page and content
            $tplBe->assign('content', $this->getContent());
            $tplBe->assign('page',    $this->getPage());
            
            // 
            $args['preview'] = $this->output;
            if(!empty($args)) {
                $tplBe->assign('args', $args);
            }
            
            $this->output = $tplBe->fetch($template);
        }
        
        return $this->output;
    }
    
    
    
    /* PRIVATE METHODS ******************************************************* */
    
    /**
     * Load values in associative array to current object ($this)
     * 
     * @param array $assocProps     Associative array 
     */
    private function loadProperties($assocProps, $object=null)
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
    
} // END: class Mask