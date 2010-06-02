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
 * Privilege
 * 
 * @package    Core
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: privilege.class.php 1 2010-05-31 10:20:09Z vifito $
 */
class Privilege
{
    
    /**
     * @var int
     * @deprecated 0.8 Use pk_privilege
    */ 
    public $id           = null;
    
    /**
     * @var int
     */
    public $pk_privilege = null;
    
    
    /**#@+
     * @access public
     * @var string
    */    
    public $description = null;
    public $name        = null;
    public $module      = null;
    /**#@-*/
    
    
    /**
     * @var ADOConnection
     */
    private $conn = null;
    
    
    /**
     * Constructor
     *
     * @see Privilege::Privilege
     * @param int $id Privilege Id
    */
    public function __construct($id=null)
    {
        if(Zend_Registry::isRegistered('conn')) {
            $this->conn = Zend_Registry::get('conn');
        }
        
        if(!is_null($id)) {
            $this->read($id);
        }
    }
    
    
    /**
     * Create a new Privilege
     * 
     * @param array $data Data values to insert into database
     * @return boolean
     */
    public function create($data)
    {
        $fields = array('name', 'module', 'description');
        
        try {
            $this->pk_privilege = SqlHelper::bindAndInsert('privileges', $fields, $data);
        } catch(Exception $e) {
            return false;
        }
        
        return true;
    }
    
    
    /**
     * Read a privilege
     *
     * @param int $id Privilege Id
     * @return 
     */
    function read($id)
    {
        $sql = 'SELECT * FROM `privileges` WHERE `pk_privilege`=?';
        
        // Set fetch method to ADODB_FETCH_ASSOC
        $this->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        
        $rs  = $this->conn->Execute($sql, array($id));
        if (!$rs) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return;
        }
        
        $this->load($rs->fields);
        
        return $this;
    }
    
    
    /**
     * Load properties in this instance
     *
     * @param array|stdClass $data
     * @return Privilege Return this instance to chaining of methods
     */
    public function load($data)
    {
        $properties = $data;
        if(!is_array($data)) {
            $properties = get_object_vars($data);
        }
        
        foreach($properties as $k => $v) {
            $this->{$k} = $v;
        }
        
        return $this; // chaining methods
    }
    
    
    /**
     * Update privilege
     *
     * @param array $data
     * @return boolean|Privilege Return this instance or false if update operation fail
     */
    public function update($data)
    {
        $fields = array('name', 'module', 'description');
        $where  = '`pk_privilege` = ' . $data['id'];
        
        try {
            SqlHelper::bindAndUpdate('privileges', $fields, $data, $where);
        } catch(Exception $e) {
            return false;
        }
        
        $this->load($data);
        
        return $this;
    }
    
    
    /**
     * Remove a privilege
     *
     * @param int $id Privilege Id
     * @return boolean
     */
    public function delete($id)
    {
        $sql = 'DELETE FROM `privileges` WHERE `pk_privilege` = ?';
        
        if($this->conn->Execute($sql, array($id))===false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return false;
        }
        
        return true;
    }
    

    /**
     * Get privileges of system
     *
     * @param array Array of Privileges
     */
    public function get_privileges($filter=null)
    {
        $privileges = array();
        if(is_null($filter)) {
            $sql = 'SELECT * FROM `privileges` ORDER BY `module`';
        } else {
            $sql = 'SELECT * FROM `privileges` WHERE '.$filter.' ORDER BY `module`';
        }
        
        // Set fetch method to ADODB_FETCH_ASSOC
        $this->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $this->conn->Execute($sql);
        
        if($rs !== false) {
            while(!$rs->EOF) {
                $privilege = new Privilege();
                $privilege->load( $rs->fields );
                
                $privileges[]  = $privilege;
                $rs->MoveNext();
            }
        }
        
        return $privileges;
    }
    
    
    /**
     * Get modules name
     *
     * @return array
     */
    public function getModuleNames()
    {
        $modules = array();
        $sql = 'SELECT `module` FROM `privileges` WHERE (`module` IS NOT NULL) AND (`module`<> "") GROUP BY `module`';
        
        // Set fetch method to ADODB_FETCH_ASSOC
        $this->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        
        $rs = $this->conn->Execute($sql);
        
        if($rs !== false) {
            while(!$rs->EOF) {
                $modules[] = $rs->fields['module'];
                $rs->MoveNext();
            }
        }
        
        return $modules;
    }
    
    
    /**
     * Get privileges by pk_user
     * 
     * @see User::loadSession()
     * @param int $pk_user
     * @return array
    */
    public static function getPrivilegesByUser($pk_user)
    {
        $privileges = array();        
        $conn = Zend_Registry::get('conn');
        
        $sql = 'SELECT t3.pk_privilege, t3.description, t3.name FROM `users` AS t1
                    INNER JOIN `user_groups_privileges` AS t2 on t2.pk_fk_user_group = t1.fk_user_group
                    INNER JOIN `privileges` AS t3 on t3.pk_privilege = t2.pk_fk_privilege
                WHERE t1.pk_user = ?';
        $rs = $conn->Execute($sql, array($pk_user));
        
        if($rs !== false) {
            while(!$rs->EOF) {
                $privileges[] = $rs->fields['name'];
                $rs->MoveNext();
            }
        } else {
            $error_msg = $conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
        }
        
        return $privileges;
    }
    
}
