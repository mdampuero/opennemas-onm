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


class Inner extends Content
{
    /**
     * @var string
     */
    public $value = null;
    
    public function __construct($pk_content=null)
    {        
        parent::__construct($pk_content);
    }
    
    public function read($pk_content)
    {
        // Discard pk_content provided
        $pk_content = $this->_getPkUniqueContainer();
        
        parent::read($pk_content);
    }
    
    public function create()
    {
        throw new Exception('This content can not be created');
    }
    
    public function update()
    {
        throw new Exception('This content can not be updated');
    }
    
    public function internalToString()
    {
        return '<h1>Inner content will go here.</h1>';
    }
    
    private function _getPkUniqueContainer()
    {
        $contentType = $this->getNameOfContentType();
        
        $sql = 'SELECT `contents`.`pk_content` FROM `contents`, `content_types` ' . 
               'WHERE `content_types`.name = "' . $contentType . '" AND ' .
               '`contents`.`fk_content_type`=`content_types`.`pk_content_type`';
        $pkContent = $this->conn->GetOne($sql);
        
        return $pkContent;
    }
    
    /**
     * Set innerContent
     *
     * @param string $value
     * @return InnerContent
     */
    public function setInnerContent($value)
    {
        $this->value = $value;        
        return $this;
    }
    
    /**
     *
     * @return string
     */
    public function __toString()
    {
        $output = $this->internalToString();
        if($this->value != null) {
            $output = $this->value;
        }
        
        return $output;
    }
    
}