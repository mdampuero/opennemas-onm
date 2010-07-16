<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNemas project
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
 * ContentBox
 * 
 * @package    Core
 * @subpackage FrontManager
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: content_box.class.php 1 2010-06-30 10:50:40Z vifito $
 */
class ContentBox
{
    /**
     * @var string
     */
    private $html = null;
    
    /**
     * @var Content
     */
    private $content = null;
    
    /**
     * @var string
     */
    private $mask = null;
    
    /**
     * @var int
     */
    private $weight = null;
    
    /**
     * @var string
     */
    private $placeholder = null;
    
    /**
     * @var array
     */
    private $params = array();
    
    /**
     * @var Page
     */
    private $page = null;
    
    
    /**
     * Contructor
     * 
     * @param array $properties
     */
    public function __construct($properties=null)
    {
        if(!is_null($properties)) {
            $this->loadProperties($properties, $this);
        }
    }

    
    /**
     * Get mask name
     *
     * @return string
     */
    public function getMask()
    {
        return $this->mask;
    }
    
    /**
     * Set mask
     *
     * @param string $mask
     * @return ContentBox
     */
    public function setMask($mask)
    {
        $this->mask = $mask;
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
     * @param Page
     * @return ContentBox
     */
    public function setPage($page)
    {
        $this->page = $page;
        
        return $this;
    }
    
    
    /**
     * Get content in ContentBox
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
     * @return ContentBox
     */
    public function setContent($content)
    {
        $this->content = $content;        
        return $this;
    }
    
    
    /**
     * Get ContentBox params 
     *
     * @return array    Array of params
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /**
     * Set ContentBox params
     *
     * @param array $params
     * @return ContentBox
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }
    
    
    /**
     * Get weight of ContentBox into placeholder
     *
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }
    
    /**
     * Set weight of ContentBox into placeholder
     *
     * @param int $weight
     * @return ContentBox
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }
    
    
    /**
     * Get name of placeholder
     * 
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }
    
    /**
     * Set placeholder
     *
     * @param string $placeholder
     * @return ContentBox
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;
        return $this;
    }
    
    
    /**
     * Get content rendered previously
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }
    
    /**
     * Set ContentBox content
     *
     * @param string $html
     * @return ContentBox
     */
    public function setHtml($html)
    {
        $this->html = $html;
        return $this;
    }
    
    
    /**
     * Render ContentBox using arguments ($args)
     *
     * @param array $args
     * @return string 
     */
    public function render($args=array())
    {
        $mask = new Mask($this->mask);        
        $mask->setContentBox($this);
        
        // Helper properties in Mask instance
        $mask->setContent($this->content);
        $mask->setPage($this->page);        
        
        if(!is_null($this->params)) {
            $args = array_merge($this->params, $args);
        }
        
        $this->html = $mask->apply($args);
        
        return $this->html;
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
    
} // END: class ContentBox