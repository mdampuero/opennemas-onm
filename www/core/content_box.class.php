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
 * @category   OpenNemas
 * @package    OpenNemas
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * ContentBox
 * 
 * @package    Core
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
     * @param Content $content
     * @param string $mask
     * @param Page $page
     * @param array $params
     */
    public function __construct($content, $mask=null, $page=null, $params=null)
    {
        $this->setContent($content);
        
        if(!is_null($mask)) {
            $this->setMask($mask);
        }
        
        if(!is_null($page)) {
            $this->setPage($page);
        }
        
        if(!is_null($params)) {
            $this->setParams($params);
        }
    }

    public function getMask()
    {
        return $this->mask;
    }
    
    public function setMask($mask)
    {
        $this->mask = $mask;
    }
    
    public function getPage()
    {
        return $this->page;
    }    
    
    /**
     *
     */
    public function setPage($page)
    {
        $this->page = $page;
        
        return $this;
    }
    
    public function setContent($content)
    {
        $this->content = $content;
        
        return $this;
    }
    
    public function setParams($params)
    {
        $this->params = $params;
    }
    
    public function render($args=array())
    {        
        $mask = new Mask($this->mask);
        
        $mask->setContent($this->content);
        $mask->setPage($this->page);
        
        $args = array_merge($this->params, $args);
        
        $this->html = $mask->apply($args);
        
        return $this->html;
    }
    
}