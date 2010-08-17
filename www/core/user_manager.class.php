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
 * UserManager
 * 
 * @package    Core
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: user_manager.class.php 1 2010-05-28 11:32:24Z vifito $
 */
class UserManager
{
    private static $_instance = null;
    
    private $users = array();
    
    private $conn  = null;
    
    public $cache = null;
    
    
    private function __construct()
    {
        $this->cache = new MethodCacheManager($this, array('ttl' => 30));
        
        if(Zend_Registry::isRegistered('conn')) {
            $this->conn = Zend_Registry::get('conn');
        }
        
        // TODO: evaluate «lazy loading» ¿?
        $this->users = $this->populate();
    }
    
    
    /**
     * Get instance (singleton)
     *
     * @return PageManager
    */
    public static function getInstance()
    {
        if(self::$_instance == null) {
            self::$_instance = new UserManager();
        }
        
        return self::$_instance;
    }
    
    
    /**
     * Populate array of pages
     *
     * @return array
     */
    public function populate()
    {        
        $sql = 'SELECT * FROM `users`';
        $rs  = $this->conn->Execute( $sql );
        
        if ($rs === false) {            
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return null;
        }
        
        // Clear previous users
        $this->users = array();
        
        if($rs !== false) {
            while(!$rs->EOF) {
                $user = new User();                
                $user->loadProperties($rs->fields);
                
                $this->users[ $user->pk_user ] = $user;
                
                $rs->MoveNext();
            }
        }        
        
        // Return internal array to use cache
        return $this->users;
    }
    
    
    /**
     * Get user by pk_user
     *
     * @param int $pk_user
     * @return User|null
    */
    public function getUserById($pk_user)
    {
        
        if(isset($this->users[$pk_user])) {
            return $this->users[$pk_user];
        }
        
        return null;
    }
    
    
    /**
     * Get user by login name
     *
     * @param string $login
     * @return User|null
    */
    public function getUserByLogin($login)
    {
        foreach($this->users as $user) {
            if($user->login == $login) {
                return $user;
            }
        }
        
        return null;
    }
    
}