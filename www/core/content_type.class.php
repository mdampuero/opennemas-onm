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
 * ContentType
 * 
 * @package    Onm
 * @subpackage 
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: content_type.class.php 1 2010-06-01 13:28:27Z vifito $
 */
class ContentType
{
    
    public $pk_content_type = null;
    public $name  = null;
    public $title = null;
    public $masks = array();
    
    private static $_instance = null;
    
    private $_types = null;
    
    private $conn = null;
    
    public function __construct($pk_content_type=null)
    {
        if(Zend_Registry::isRegistered('conn')) {
            $this->conn = Zend_Registry::get('conn');
        }        
        
        if(!is_null($pk_content_type)) {
            $this->read($pk_content_type);
        }
    }
    
    
    public function read($pk_content_type)
    {
        // ContentTypeManager
        $ctMgr = ContentTypeManager::getInstance();
        
        return $ctMgr->get($pk_content_type);
    }
    
    
    /**
     * Return available masks for a content type
     *
     * @see Mask
     * @param string $theme
     * @return array    Array of Mask objects
     */
    public function getMasksByTheme($theme)
    {
        if(is_null($this->pk_content_type)) {
            throw new Exception('You must load this object before to invoke this method.');
        }
        
        $masks = array();
        
        $dirname = realpath(SITE_PATH . '/themes/' . $theme . '/tpl/masks/' . $this->name . '/');        
        if(file_exists($dirname)) {        
            foreach (glob($dirname . '/*.tpl') as $filename) {
                $masks[] = array(
                    'title' => basename($filename, '.tpl'),
                    'value' => $this->name . '/' . basename($filename, '.tpl')
                );
            }
        }
        
        return $masks;
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
    
    
}




