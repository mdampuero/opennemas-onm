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
 * Category
 * 
 * @package    Onm
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: category.class.php 1 2010-05-18 10:15:29Z vifito $
 */
class Category
{
    
    /**#@+
     * Content properties
    */
    public $pk_category = null;
    public $fk_category = null;    
    public $title = null;
    public $name  = null;
    /**#@-*/
    
    
    /**
     * @var ADOConnection
     */
    private $conn = null;
    
    /**
     * Constructor
     *
     * @param int|null $pk_category
     */
    public function __construct($pk_category=null)
    {
        $this->conn = Zend_Registry::get('conn');
        
        if(!is_null($pk_category)) {       
            $this->read($pk_category);
        }
    }
    
    // Getters {{{
    /**
     * Get pk_category
     *
     * @return int
     */
    public function getPkCategory()
    {
        return $this->pk_category;
    }
    
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Get name of category
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    // }}}
    
    
    /**
     * Create a new category (Insert into database)
     *
     * @throws Exception
     * @throws SqlHelperException
     * @param array $data
     * @return int  Last insert ID
     */
    public function create($data)
    {        
        $fields = array('fk_category', 'title', 'name');        
        $pk_category = SqlHelper::bindAndInsert('categories', $fields, $data);
        
        return $pk_category;        
    }


    /**
     * Read category and load current object
     * 
     * @param int $pk_category
     * @return boolean  Return false if category could not be read, otherwise true
     */
    public function read($pk_category)
    {        
        $sql = 'SELECT * FROM `categories` WHERE `pk_category` = ?';
        $rs = $this->conn->Execute( $sql, array($pk_category) );
        
        if (!$rs) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return false;
        }
        
        $this->load($rs->fields);
        return true;
    }
    
    
    /**
     * Load properties to current object
     *
     * @param array $properties
     */
    public function load($properties)
    {
        if(is_array($properties)) {
            foreach($properties as $k => $v) {
                if( !is_numeric($k) ) {
                    $this->{$k} = $v;
                }
            }
        } elseif(is_object($properties)) {
            $properties = get_object_vars($properties);
            foreach($properties as $k => $v) {
                if( !is_numeric($k) ) {
                    $this->{$k} = $v;
                }
            }
        }
    }    
    
    
    /**
     * Update Category values (update database)
     * 
     * @throws Exception
     * @throws SqlHelperException
     * @param array $data
     */
    public function update($data)
    {
        $fields = array('fk_category', 'title', 'name');
        
        SqlHelper::bindAndUpdate('categories', $fields, $data, 'pk_category = ' . $data['pk_category']);        
    }
    
    
    /**
     * Delete category
     * 
     * @param int $pk_category
     * @return boolean
    */
    public function delete($pk_category)
    {
        $sql = 'DELETE FROM `categories` WHERE `pk_category` = ?';
        
        if($this->conn->Execute($sql, array($pk_category)) === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return false;            
        }
        
        return true;
    }    

    
    /**
     * Check if category has children
     * 
     * @param int $pk_category
     * @return boolean
    */
    public function hasChildren($pk_category)
    {
        $sql = 'SELECT count(*) AS total FROM `categories` WHERE `fk_category` = ?';
        
        $total = $this->conn->GetOne($sql, array($pk_category));
        if($total === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return false;            
        }
        
        return intval($total) > 0;
    }
    
    
    /**
     * Count items in this category
     *
     * <code>
     * $categ = new Category($valid_pk_category);
     * $numContentsAvailableInCategory = $categ->count( 'contents.status="AVAILABLE"' );
     * </code>
     * 
     * @param string $condition
     * @return int  Total items in category
     */
    public function count($condition=null)
    {
        $where = '';
        
        if(!is_null($condition)) {
            $where = $condition . ' AND';
        }
        
        $sql = 'SELECT count(pk_content) AS total FROM `contents`, `contents_categories`
                WHERE ' . $where . '
                    `contents_categories`.`pk_fk_category` = ' . $this->pk_category . ' AND
                    `contents`.`pk_content`=`contents_categories`.`pk_fk_content`';
        $total = $this->conn->GetOne( $sql );
        
        if($rs === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return 0;
        }
        
        return $total;
    }
    
    
    /**
     * Create a new directory, if it don't exists
     *
     * 
     * @throws Exception
     * @param string $dir Directory to create
     */
    public function createDirectory($path) // FIXME: Is this method neccessary?
    {
        if( !file_exists($path) ) {
            $created =  @mkdir($path, 0777, true);
            
            if(!$created) {
                $msg = "Error creating directory: " . $path;
                Zend_Registry::get('logger')->emerg($msg);
                
                throw new Exception($msg);
            }
        }
    }
    
}