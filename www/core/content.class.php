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
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * Content
 * 
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: content.class.php 1 2009-11-30 18:16:56Z vifito $
 */
class Content
{
    /**#@+
     * Content properties
    */
    public $pk_content = null;
    public $fk_content_type = null;
    public $title = null;
    public $description = null;
    public $metadata = null;
    public $starttime = null;
    public $endtime = null;
    public $created = null;
    public $changed = null;
    public $fk_author = null;
    public $fk_publisher = null;
    public $fk_user_last_editor = null;
    public $views = null;
    public $status = null;
    public $published = null;
    public $slug = null;
    /**#@-*/
    
    /**
     * @var int Number of version of content
     */
    public $version = null;
    
    /**
     * @var MethodCacheManager
     */
    public $cache = null;
    
    /**
     * @var ADOConnection
     */
    public $conn  = null;
    
    /**
     * @var array   Array of valid status
     * @static
     */
    public static $validStatus = array('PENDING', 'AVAILABLE', 'REMOVED');
    
    /**
     * Constructor
     *
     * @param null|int $id  Pk_content identifier
     */
    public function __construct($id=null)
    {
        $this->cache = new MethodCacheManager($this, array('ttl' => 30));
        
        if( Zend_Registry::isRegistered('conn') ) {
            $this->conn  = Zend_Registry::get('conn');
        }        
        
        if(!is_null($id)) {
            $this->read($id);
        }   
    }    
    
    /**
     * Create a new content
     * 
     * @param array $data   Values to save content (POST data, normally)
     * @return boolean
     */
    public function create( $data )
    {
        // Prepare array data with default values if it's necessary
        $this->prepareData(&$data);
        
        $fields = array('fk_content_type',
                        'title', 'slug', 'description', 'metadata',
                        'starttime', 'endtime', 'created', 'changed', 'published',
                        'fk_author', 'fk_publisher', 'fk_user_last_editor',
                        'views',
                        'status');
        
        $pk_content = SqlHelper::bindAndInsert('contents', $fields, $data);
        
        return $pk_content;
    }
    
    
    
    /**
     * Set init values
     *
     * @param array &$data  Reference to associative array with values
     */
    private function prepareData($data)
    {
        if(!isset($data['fk_content_type']) && is_null($this->content_type)) {
            throw new Exception(__CLASS__ . '::' . __METHOD__ . ' need content_type property.');
        }
        
        $defaults = array(
            'fk_content_type' => $this->getContentTypeId(),
            'slug' => String_Utils::get_title($data['title'], false),
            
            'starttime' => '0000-00-00 00:00:00',
            'endtime'   => '0000-00-00 00:00:00',
            
            'created'   => date('Y-m-d H:i:s'),
            'changed'   => date('Y-m-d H:i:s'),
            'published' => date('Y-m-d H:i:s'),
            
            // TODO: remove $_SESSION['userid'] by object to manage session, $sess->getUserId()
            'fk_author'    => $_SESSION['userid'],
            'fk_publisher' => $_SESSION['userid'],
            'fk_user_last_editor' => $_SESSION['userid'],
            
            'views'  => 0,
            'status' => 'PENDING',
        );                
        
        // Dont use array_merge. Use "+" operator for arrays to preserve keys.
        $data = $data + $defaults;
    }
    
    
    /**
     * Get content type identifier (fk_content_type)
     *
     * @param string $content_type  Name of content
     * @return int  Return content type identifier
     */
    public function getContentTypeId($content_type=null)
    {
        if(is_null($content_type)) {
            $content_type = $this->content_type;
        }
        
        $fk_content_type = $this->conn->
            GetOne('SELECT * FROM `content_types` WHERE name = "' . $content_type . '"');
        
        return $fk_content_type;
    }
    
    /**
     * Check if $status is a valid status string
     *
     * @param string $status
     */
    public function isValidStatus($status)
    {
        return in_array($status, Content::$validStatus);
    }
    
    public function read($pk_content)
    {        
        $sql = 'SELECT * FROM `contents` WHERE `pk_content` = ?';
        $rs = $this->conn->Execute( $sql, array($pk_content) );
        
        if ( false === $rs) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return false;
        }
        
        // Load object properties
        $this->load( $rs->fields );
    }    
    
    /**
     * Load properties for "this" object from $properties
     * 
     * @param stdClass|array $properties
     */
    protected function load($properties)
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
     *
     * @throws OptimisticLockingException
     * @param array $data
     */
    public function update($data)
    {
        // Prepare array data with default values if it's necessary
        $this->prepareData(&$data);
        
        // Check optimistic locking
        if( isset($data['version']) ) {
            if( !$this->isLastVersion($data['version'], $data['pk_content']) ) {
                throw new OptimisticLockingException();
            } else {
                // Increment version
                $data['version'] += 1;
            }
        }
        
        $fields = array('title', 'slug', 'description', 'metadata',
                        'starttime', 'endtime', 'changed', 'published',
                        'fk_user_last_editor', 'status', 'version');
        
        SqlHelper::bindAndUpdate('contents', $fields, $data, 'pk_content = ' . $data['pk_content']);
    }
    
    
    /**
     * Use field version for optimistic locking
     *
     * @param int $version
     * @param int $pk_content
     * @return boolean
     */
    public function isLastVersion($version, $pk_content)
    {
        $sql = 'SELECT version FROM `contents` WHERE `pk_content` = ' . intval($pk_content);
        $currentVersion = $this->conn->GetOne($sql);
        
        return $currentVersion == $version;
    }
    
    /**
     * Change status 
     *
     * @param int $pk_content
     * @param string $status
     */
    public function changeStatus($pk_content, $status)
    {
        if($this->isValidStatus($status)) {
            // TODO: incorporar auditoría
            $fields = array('status');
            $data   = array('status' => $status);
            
            SqlHelper::bindAndUpdate('contents', $fields, $data, 'pk_content = ' . $pk_content);
        } else {
            throw new Exception(__METHOD__ . ' "' . $status . '" is not a valid status');
        }
    }
    
    
    /**
     * Remove content of database
     * for logic delete you must use Content::changeStatus($pk_content, 'REMOVED');
     *
     * @see Content::changeStatus()
     * @param int $pk_content
     * @return boolean
    */
    public function delete($pk_content)
    {
        $sql = 'DELETE FROM `contents` WHERE `pk_content` = ?';
           
        if( $this->conn->Execute($sql, array($pk_content)) === false ) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return false;
        }
        
        return true;
    }
    
    
    /**
     * Check if a content is in time for publishing
     *
     * @param string $starttime
     * @param string $endtime
     * @param string $time
     * @return boolean
    */
    public function isInTime($starttime=null, $endtime=null, $time=null)
    {
        if(is_null($starttime)) {
            $start = strtotime($this->starttime);
            $end   = strtotime($this->endtime);
        }
        
        if(is_null($time)) {
            $now = time();
        } else {
            $now = strtotime($time);
        }        
        
        // If $start and $end not defined then return true
        if(empty($start) && empty($end)) {
            return true;
        }
        
        // only setted $end
        if(empty($start)) {
            return ($now < $end);
        } 
        
        // only setted $start
        if(empty($end)) {
            return ($now > $start);
        }
        
        // $start < $now < $end
        return (($now < $end) && ($now > $start));
    }
    
    /**
     * Check if a content start time for publishing
     * don't check Content::endtime
     *
     * @link https://redmine.openhost.es/issues/show/1058#note-8
     * @return boolean
    */
    public function isStarted()
    {
        $now = time();        
        $start = strtotime($this->starttime);
        
        // If $start isn't defined then return true
        if(empty($start)) {
            return true;
        }
        
        return ($now > $start);
    }    

    /**
     * Check if this content is obsolete
     *
     * @return boolean
     */
    public function isObsolete()
    {
        $end   = strtotime($this->endtime);
        $now   = time();

        if(!empty($end)) {
            return $end < $now;
        }

        return false;
    }
    
    /**
     * Check if a content is out of time for publishing
     *
     * @see Content::isInTime()
     * @return boolean
    */
    public function isOutTime($starttime=null, $endtime=null, $time=null)
    {
        return !$this->isInTime($starttime, $endtime, $time);
    }
    
    /**
     * Check if this content is scheduled
     * or, in others words, if this content has a starttime and/or endtime
     *
     * @return boolean
    */
    public function isScheduled()
    {
        return ((!empty($this->starttime) && !preg_match('/0000\-00\-00 00:00:00/', $this->starttime)) ||
                (!empty($this->endtime) && !preg_match('/0000\-00\-00 00:00:00/', $this->endtime)));
    }
    
    
    /**
     * 
     * TODO: review if it's neccessary static and multiple pk_contents
     * @param int|array $pk_contents
     * @return boolean
    */
    public static function incrementViews($pk_contents)
    {
        $conn = Zend_Registry::get('conn');
        
        if(is_int($pk_contents)) {
            $pk_contents = array($pk_contents);
        }
        
        if( !is_array($pk_contents) ) {
            throw new Exception('Illegal argument exception');
        }
        
        $sql = 'UPDATE `contents` SET `views` = `views` + 1
                WHERE `pk_content` IN (' . implode(',', $pk_contents) . ')';
        
        if($conn->Execute($sql) === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return false;
        }
        
        return true;
    }
    
    
    /**
     * Abstract factory method getter
     *
     * @param string $pk_content Content identifier
     * @return object Instance of an specific object in function of content type
    */
    public static function get($pk_content)
    {
        $sql = 'SELECT `content_types`.name FROM `contents`, `content_types`
                    WHERE `pk_content` = ? AND fk_content_type = pk_content_type';
        $type = $this->conn->GetOne($sql, array($pk_content));
        
        if($type === false) {
            return null;
        }
        
        $type = ucfirst( $type );
        
        return new $type($pk_content);        
    }

    
    /**
     * Attach content to category
     *
     * @param int $pk_content
     * @param int $pk_category
     * @return boolean
     */
    public function attachCategory($pk_content, $pk_category)
    {        
        $catName = $this->conn->GetOne('SELECT `name` FROM `categories` WHERE `pk_category` = ?',
                                 array($pk_category));
        
        $sql = 'INSERT INTO `contents_categories` (`pk_fk_content`, `pk_fk_category`, `catName`)
                    VALUES (?, ?, ?)';
        $values = array($pk_content, $pk_category, $catName);
        
        if($this->conn->Execute($sql, $values) === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return false;
        }
        
        return true;
    }
    
    /**
     * Bulk insert into categories to attach content
     *
     * @param int $pk_content
     * @param array $categories  Array of pk_category
     * @return boolean
     */
    public function attachCategories($pk_content, $categories)
    {
        $sql = 'SELECT `pk_category`, `name` FROM `categories`
                    WHERE `pk_category` IN (' . implode(',', $categories) . ')';
        
        $rs = $this->conn->Execute($sql);        
        if($rs === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return false;
        }
        
        $data = array();
        while(!$rs->EOF) {
            $data[] = array($pk_content, $rs->fields['pk_category'], $rs->fields['name']);
            
            $rs->MoveNext();
        }                
        
        $sql = 'INSERT INTO `contents_categories` (`pk_fk_content`, `pk_fk_category`, `catName`)
                    VALUES (?, ?, ?)';
        $rs = $this->conn->Execute($sql, $data);
        
        return $rs !== false;
    }

    
    /**
     * Detach content to a specific category
     *
     * @param int $pk_content
     * @param int $pk_category
     * @return boolean
     */
    public function detachCategory($pk_content, $pk_category)
    {        
        $sql = 'DELETE FROM `categories` WHERE
                    `pk_fk_content` = ? AND `pk_fk_content_category` = ?';
        $values = array($pk_content, $pk_category);
        
        if($this->conn->Execute($sql, $values) === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return false;
        }
        
        return true;
    }


    /**
     * Detach content to categories (all categories)
     *
     * @param int $pk_content
     * @return boolean
     */    
    public function detachCategories($pk_content)
    {
        $sql = 'DELETE FROM `categories` WHERE `pk_fk_content` = ?';
        $values = array($pk_content);
        
        if($this->conn->Execute($sql, $values) === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return false;
        }
        
        return true;
    }    
    
    
    /**
     * Magic method toString
     *
     * @return string
    */
    public function __toString()
    {
        return $this->title;
    }
    
}


