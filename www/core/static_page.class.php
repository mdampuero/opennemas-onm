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
 * Static_Page
 * 
 * @package    Core
 * @subpackage Content
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: static_page.class.php 1 2010-07-08 14:39:59Z vifito $
 */
class Static_Page extends Content
{    
    /**
     * @var pk_static_page Page identifier
     */
    public $pk_static_page = null;
    
    /**
     * @var string Content of body
     */
    public $body = null;    
    
    /**
     * @var MethodCacheManager Handler to call method cached
     */
    public $cache = null;        
    
    /**
     * constructor
     *
     * @param int $pk_content 
     */
    public function __construct($pk_content=null)
    {
        $this->cache = new MethodCacheManager($this, array('ttl' => 30));
        
        $this->content_type = 'Static_Page';        
        parent::__construct($pk_content);
        
        if(!is_null($pk_content)) {
            $this->read($pk_content);
        }
    }
    
    
    /**
     * 
     *
     */
    public function create($data)
    {
        // Clear  magic_quotes
        String_Utils::disabled_magic_quotes( &$data );        
        $pk_content = parent::create($data);
        
        if($pk_content === false) {
            return false;
        }
        
        $fields = array('pk_static_page', 'body');
        $data['pk_static_page'] = $pk_content;
        
        try {
            SqlHelper::bindAndInsert('static_pages', $fields, $data);
        } catch(Exception $e) {
            return false;
        }
        
        return $pk_content;
    }
    
    
    /**
     * Read, get a specific object
     *
     * @param int $pk_content Object ID
     * @return Static Return instance to chaining method
     */
    public function read($pk_content)
    {
        parent::read($pk_content);
        
        $sql = "SELECT * FROM `static_pages` WHERE `pk_static_page`=?";
        
        $rs = $this->conn->Execute($sql, array($pk_content));
        if($rs === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return null;
        }
        
        $this->load( $rs->fields );
        return $this;
    }    
    
    
    /**
     * Update
     * 
     * @param array $data Array values
     * @return boolean
     */
    public function update($data)
    {
        // Clear magic_quotes
        String_Utils::disabled_magic_quotes( &$data );
        
        parent::update($data);
        
        $fields = array('body');
        $where  = '`pk_static_page` = ' . $data['pk_content'];
        
        try {
            SqlHelper::bindAndUpdate('static_pages', $fields, $data, $where);
        } catch(Exception $e) {
            return false;
        }
        
        return true;
    }
    
    
    /**
     * Delete static page
     *
     * @see Content::remove()
     * @param int $pk_content Identifier
     * @return boolean
     */
    public function delete($pk_content)
    {
        parent::remove($pk_content);
        
        $sql = 'DELETE FROM `static_pages` WHERE `pk_static_page`=?';        
        $values = array($pk_content);
        
        if($this->conn->Execute($sql, array($pk_content)) === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return false;
        }
        
        return true;        
    }
    
    
    /**
     * Get a Static_Page by slug
     * 
     * @param string $slug
     * @return Content
     */
    public function getStaticPageBySlug($slug)
    {
        // Prevent SQL injection
        $slug = preg_replace('/\*%_\?/', '', $slug);
        $sql = 'SELECT `pk_content` FROM `contents` WHERE `slug` LIKE ?';        
        $pk_content = $this->conn->GetOne($sql, array($slug));        
        
        if($pk_content === false) {
            return null;
        }
        
        return Content::get($pk_content);
    }    
    
    
    
    /**
     * Magic method __toString()
     * 
     * @return String
     */
    public function __toString()
    {
        // Return HTML
        return $this->body;
    }
    
}
