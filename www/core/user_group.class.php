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
 * UserGroup
 * 
 * @package    Onm
 * @subpackage 
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: user_group.class.php 1 2010-05-31 09:42:50Z vifito $
 */ 
class UserGroup
{
    public $id = null;
    
    public $name = null;
    
    public $privileges = null;
    
    private $conn = null;

    public function __construct($id=NULL)
    {
        if(Zend_Registry::isRegistered('conn')) {
            $this->conn = Zend_Registry::get('conn');
        }
        
        if(!is_null($id)) {
            $this->read($id);
        }
    }    

    public function create($data)
    {        
        $fields = array('name');
        
        try {
            $id = SqlHelper::bindAndInsert('user_groups', $fields, $data);
        } catch(Exception $e) {
            return false;
        }        
        
        $this->id   = $id;
        $this->name = $data['name'];
        
        if((!is_null($data['privileges'])) && (count($data['privileges'] > 0))){
            return $this->insertPrivileges($data['privileges']);
        }
        
        return true;
    }
    
    
    public function read($id)
    {
        $sql = 'SELECT * FROM `user_groups` WHERE `pk_user_group` = ?';
        $rs  = $this->conn->Execute($sql, array($id));
        
        if ($rs === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return false;
        }
        
        $this->loadProperties($rs->fields);
        
        $sql = 'SELECT `pk_fk_privilege` FROM `user_groups_privileges`
                    WHERE `pk_fk_user_group` = ?';
        $rs = $this->conn->Execute( $sql, array($id) );
        
        if ($rs === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return false;
        }
        
        while(!$rs->EOF) {
            $this->privileges[] = $rs->fields['pk_fk_privilege'];
            
            $rs->MoveNext();
        }
        
    }
    
    
    public function update($data)
    {
        if(!is_null($data['id'])) {
            $this->id = $data['id'];
            
            $fields = array('name');
            $where  = '`pk_user_group` = ' . $data['id'];
            
            try {
                SqlHelper::bindAndUpdate('user_groups', $fields, $data, $where);
            } catch(Exception $e) {
                return false;
            }
            
            $this->deletePrivileges($data['id']);
            
            if((!is_null($data['privileges'])) && (count($data['privileges'] > 0))) {
                return $this->insertPrivileges($data['privileges']);
            }
        }
        
        return false;
    }
    
    
    public function delete($id)
    {
        $sql = 'DELETE FROM `user_groups` WHERE `pk_user_group` = ?';
        
        if($this->conn->Execute($sql, array($id))===false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return;
        }
        
        $this->deletePrivileges($id);
    }
    
    
    public static function getGroupName($fk_user_group)
    {
        $conn = Zend_Registry::get('conn');
        
        $sql = 'SELECT `name` FROM `user_groups` WHERE `pk_user_group`=?';
        $rs  = $conn->GetOne($sql, $fk_user_group);        
        
        return $rs;
    }
    
    
    public function getUserGroups()
    {
        $types = array();
        $sql = 'SELECT `pk_user_group`, `name` FROM `user_groups`';
        $rs  = $this->conn->Execute($sql);
        
        if($rs !== false) {                    
            while(!$rs->EOF) {
                $user_group = new User_Group();
                
                $user_group->loadProperties($rs->fields);
                $types[] = $user_group;
                
                $rs->MoveNext();
            }
        }
        
        return $types;
    }
    
    
    public function containsPrivilege($privilege)
    {
        if(isset($this->privileges)){
            return in_array(intval($privilege), $this->privileges);
        }
        
        return false;
    }
    
    
    private function insertPrivileges($data)
    {
        $sql = "INSERT INTO `user_groups_privileges` (`pk_fk_user_group`, `pk_fk_privilege`)
                    VALUES (?, ?)";
        
        for($i=0; $i < count($data); $i++) {
            $values = array($this->id, $data[$i]);
            
            if($this->conn->Execute($sql, $values) === false) {
                $error_msg = $this->conn->ErrorMsg();
                Zend_Registry::get('logger')->emerg($error_msg);
                
                return false;
            }
        }
        
        return true;
    }
    

    private function deletePrivileges($id)
    {
        $sql = 'DELETE FROM `user_groups_privileges` WHERE `pk_fk_user_group` = ?';
        
        if($this->conn->Execute($sql, array($id)) === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return false;
        }
        
        return true;
    }
    
    
    public function loadProperties($properties)
    {
        if(is_array($properties)) {
            foreach($properties as $k => $v) {
                if(!is_numeric($k)) {
                    $this->{$k} = $v;
                }
            }
        } elseif(is_object($properties)) {
            $properties = get_object_vars($properties);
            foreach($properties as $k => $v) {
                if(!is_numeric($k)) {
                    $this->{$k} = $v;
                }
            }
        }
        
        return $this;
    }
    
}
