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
 * SessionManager
 * 
 * @package    Onm
 * @subpackage Core
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: sessionmanager.class.php 1 2010-04-26 12:02:24Z vifito $
 */
class SessionManager implements ArrayAccess
{
    
    /**
     * @var string Default path to save session files
     */
    protected $_dirSess = '/var/lib/php5/';
    
    
    /**
     * @var string Zend_Session namespace
     */
    protected $_namespace = 'Default';
    
    
    /**
     * @var SessionManager
     */
    protected static $_singleton = null;
    
    
    /**
     * Constructor for SessionManager
     *
     * @param string $sessionSavePath
     * @throws Exception
     */
    private function __construct($sessionSavePath)
    {
        // Detect suhosin patch
        if (extension_loaded('suhosin') &&
                ini_get('suhosin.session.encrypt') == 1) {
            throw new Exception('Error: suhosin.session.encrypt is enabled.');
        }
        
        $this->_dirSess = $sessionSavePath;
    }
    
    
    /**
     * Get singleton instance
     * 
     * @todo: implement multiton using $sessionSavePath
     * @param string $sessionSavePath
     * @return SessionManager
     */
    public static function getInstance($sessionSavePath)
    {
        if (is_null(self::$_singleton)) {
            self::$_singleton = new SessionManager($sessionSavePath);
        }
        
        return self::$_singleton;
    }
    
    
    /**
     * Bootstrap session
     * 
     * @todo implement support to life time
     * @param int $lifetime
     * @uses Zend_Session::setOptions()
     * @uses Zend_Session::start()
     */
    public function bootstrap($lifetime=null)
    {
        $options = array(
            'save_path'     => $this->_dirSess,
            'strict'        => false,
            'cache_limiter' => 'nocache',
        );
        
        Zend_Session::setOptions($options);
        Zend_Session::start();
    }
    
    
    /**
     * Get namespace for Zend_Session
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }
    
    
    /**
     * Set namespace
     *
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->_namespace = $namespace;
    }
    
    
    /**
     * Magic method __set
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $session = new Zend_Session_Namespace();
        $session->{$name} = $value;
    }
    
    
    /**
     * Magic method __get
     *
     * @param string $name
     * @return mixed|null
     */
    public function __get($name)
    {
        $session = new Zend_Session_Namespace();
        if (!property_exists($session, $name)) {
            return null;
        }
        
        return $session->$name;
    }
    
    
    /** 
    * Defined by ArrayAccess interface 
    * Set a value given it's key e.g. $A['title'] = 'foo'; 
    * @param mixed key (string or integer) 
    * @param mixed value 
    * @return void 
    */ 
    public function offsetSet($key, $value)
    {
        $session = new Zend_Session_Namespace();
        $session->{$key} = $value; 
    }
    
    
    /** 
    * Defined by ArrayAccess interface 
    * Return a value given it's key e.g. echo $A['title']; 
    * @param mixed key (string or integer) 
    * @return mixed value 
    */ 
    public function offsetGet($key)
    {
        $session = new Zend_Session_Namespace();
        return $session->{$key};
    }
    
    
    /** 
    * Defined by ArrayAccess interface 
    * Unset a value by it's key e.g. unset($A['title']); 
    * @param mixed key (string or integer) 
    * @return void 
    */ 
    public function offsetUnset($key)
    {
        $session = new Zend_Session_Namespace();
        unset($session->{$key}); 
    }
    
    
    /**
    * Defined by ArrayAccess interface 
    * Check value exists, given it's key e.g. isset($A['title']) 
    * @param mixed key (string or integer) 
    * @return boolean 
    */ 
    public function offsetExists($key)
    {
        $session = new Zend_Session_Namespace();
        return isset($session->{$key}); 
    }
    
    
    /**
     * Parse current sessions
     *
     * Output:
     * <code>
     * array
     *  0 => 
     *     array
     *       'userid' => string '5' (length=1)
     *       'username' => string 'vifito' (length=6)
     *       'isAdmin' => boolean true
     *       'expire' => int 1282046386
     *       'authMethod' => string 'database' (length=8)
     *  ...
     * </code>
     * 
     * @return array    Array of sessions
     */
    public function getSessions()
    {
        $dirSess = $this->_dirSess;
        $sessions = array();
        
        if (file_exists($dirSess) && is_dir($dirSess)) {
            if ($dh = opendir($dirSess)) {
                while (($file = readdir($dh)) !== false) {
                    if (preg_match('/^sess_/', $file)) {
                        $contents = file_get_contents($dirSess.$file);
                        if (!empty($contents)) {
                            $session = self::unserializesession($contents);
                            
                            if (isset($session[$this->_namespace])) {
                                $session = $session[$this->_namespace];
                            }
                            
                            if (isset($session['userid'])) {
                                $sessions[] = array(
                                    'userid'     => $session['userid'],
                                    'username'   => $session['username'],
                                    'isAdmin'    => $session['isAdmin'],
                                    'expire'     => $session['expire'],
                                    'authMethod' => $session['authMethod'],
                                );
                            }
                        }
                    }
                }
                closedir($dh);
            }
        }
        
        return $sessions;
    }
    
    
    /**
     * Remove file session for a user specified
     *
     * @param int $userid
     */
    public function purgeSession($userid)
    {
        $dirSess = $this->_dirSess;
        
        if (file_exists($dirSess) && is_dir($dirSess)) {
            if ($dh = opendir($dirSess)) {
                while (($file = readdir($dh)) !== false) {
                    if (preg_match('/^sess_/', $file)) {
                        $contents = file_get_contents($dirSess.$file);
                        if (!empty($contents)) {
                            $session = self::unserializesession($contents);
                            
                            if (isset($session[$this->_namespace])) {
                                $session = $session[$this->_namespace];
                            }
                           
                            if (isset($session['userid']) &&
                                    ($session['userid'] == $userid)) {
                                @unlink($dirSess.$file);
                            }
                        }
                    }
                }
                closedir($dh);
            }
        }
    }
    
    
    /**
     * Utility to unserialize contents of a session file
     *
     * @link http://es2.php.net/manual/en/function.session-decode.php#79244 
     * @param string $data
     * @return mixed
     */
    public static function unserializesession($data)
    {
        $vars = preg_split(
            '/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff^|]*)\|/',
            $data,
            -1,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
        );
        
        for ($i=0; $vars[$i]; $i++) {
            $result[$vars[$i++]] = @unserialize($vars[$i]);
        }
        
        return $result;
    }
    
}