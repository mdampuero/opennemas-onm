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
    
    /**
     * 
     * @param Content|null $content
     */
    public function __construct($content=null)
    {
        if( !is_null($content) ) {
            $this->setContent($content);
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
    
    /**
     * Apply template to contentbox
     * 
     * @return string
     */
    public function apply($args=null)
    {
        $template = 'masks/' . $this->_content->mask . '.tpl';
        
        if(file_exists(TEMPLATE_USER_PATH . '/tpl/' . $template)) {
            $tpl = new Template(TEMPLATE_USER);
        } else {
            $tpl = new TemplateAdmin(TEMPLATE_ADMIN);
        }                
        
        
        if(!is_null($args)) {
            foreach($args as $k => $v) {
                $tpl->assign($k, $v);
            }    
        }        
        
        $tpl->assign('item', $this->_content);
        $this->_output = $tpl->fetch($template);
        
        return $this->_output;
    }
    
}