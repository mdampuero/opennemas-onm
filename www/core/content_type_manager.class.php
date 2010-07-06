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
 * ContentTypeManager
 * 
 * @package    Core
 * @subpackage Content
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: content_type_manager.class.php 1 2010-06-01 13:28:27Z vifito $
 */
class ContentTypeManager
{
    /**
     * @var ContentTypeManager  Singleton instance
     */
    private static $_instance = null;

    /**
     * Internal array to hold all content_types
     * @var array
     */
    private $_types = null;
    
    
    /**
     * @var ADOConnection
     */
    private $conn = null;
    
    
    /**
     * Constructor
     *
     * @uses ContentTypeManager::populate()
     */
    private function __construct()
    {
        if(Zend_Registry::isRegistered('conn')) {
            $this->conn = Zend_Registry::get('conn');
        }
        
        $this->populate();
    }
    
    
    /**
     * Get singleton instance
     *
     * @static
     * @return ContentTypeManager
     */
    public static function getInstance()
    {
        if(self::$_instance == null) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    
    /**
     * Save into instance a copy of content type availables
     *
     * @return boolean
     */
    private function populate()
    {
        $sql = 'SELECT * FROM `content_types` ORDER BY `title`';
        $rs = $this->conn->Execute($sql);
        
        if($rs !== false) {
            while(!$rs->EOF) {
                $obj = new ContentType();
                $obj->loadProperties($rs->fields);
                
                $this->_types[$rs->fields['pk_content_type']] = $obj;
                
                $rs->MoveNext();
            }
        } else {
            $error_msg = $this->conn->ErrorMsg();            
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return false;
        }        
        
        return true;
    }
    
    
    /**
     * Get ContentType object by $pk_content_type
     *
     * @param int $pk_content_type
     * @return ContentType
     */
    public function get($pk_content_type)
    {
        if(!isset($this->_types[$pk_content_type])) {
            return new ContentType();
        }
        
        return $this->_types[$pk_content_type];
    }    
    
    
    /**
     * populateMasksDb
     * @deprecated 0.8-alpha Load masks from filesystem
     */
    private function populateMasksDb()
    {
        $sql = 'SELECT * FROM `masks` ORDER BY `fk_content_type`, `name`';
        $rs = $this->conn->Execute($sql);
        
        if($rs !== false) {
            while(!$rs->EOF) {
                $obj = new Mask();
                $obj->loadProperties($rs->fields);
                
                $this->_types[$rs->fields['fk_content_type']]->masks[] = $obj;
                
                $rs->MoveNext();
            }
        } else {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return false;
        }
        
        return true;
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
     * Get options 
     *
     * @param array $config
     * @return string
     */
    public function getHtmlOptions($config=null)
    {
        $output = '';
        
        foreach($this->_types as $ctype) {
            $output .= '<option value="' . $ctype->pk_content_type . '"';
            
            if(isset($config['selected']) && ($config['selected'] == $ctype->pk_content_type)) {
                $output .= ' selected="selected"';
            }
            
            $output .= '>' . $ctype->title . '(' . $ctype->name . ')' .
                       '</option>' . "\n";
        }
        
        if(isset($config['id'])) {
            $output = '<select id="' . $config['id'] . '" name="' .
                            $config['id'] . '">' . "\n" . $output .
                      '</select>' . "\n";
        }
        
        return $output;
    }
    
    
    /**
     * Get array of content types
     *
     * @return array
     */
    public function getTypes()
    {
        return $this->_types;
    }
    
    
    /**
     * Return content types in json format
     *
     * @return string
     */
    public function toJSON()
    {
        return json_encode($this->_types);
    }
    
    
} // END: class ContentTypeManager