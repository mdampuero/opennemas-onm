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
 * MethodCacheManager
 * 
 * @package    Onm
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: method_cache_manager.class.php 1 2010-05-17 20:25:47Z vifito $
 */
class MethodCacheManager
{
    private $ttl       = 300; // cache life time in seconds, by default 5 minutes
    private $object    = null;
    private $methods   = null;
    private $classname = null;
    
    public function __construct($object, $options=array())
    {
        $this->object = $object;
        
        if(isset($options['ttl'])) {
            $this->ttl = $options['ttl'];
        }
    }
    
    public function __call($method, $args)
    {        
        $class_methods = $this->getInternalObjectMethods();
        
        if(in_array($method, $class_methods)) {
            $key = $this->classname.$method.md5(serialize($args));
            if(defined('APC_PREFIX')) {
                $key = APC_PREFIX . $key;
            }
            
            if(false === ($result = apc_fetch($key))) {
                $result = call_user_func_array(array($this->object, $method), $args);
                apc_store($key, serialize($result), $this->ttl);
                
                return( $result );
            }
            
            return( unserialize($result) );
        } else {        
            throw new Exception( " Method " . $method . " does not exist in this class " . get_class($this->object) . "." );
        }
    }
    
    public function set_cache_life($ttl)
    {        
        $this->ttl = $ttl;
        return $this;
    }
    
    public function clear_cache($key)
    {
        apc_delete( $key );
        return $this;
    }
    
    public function clear_all_cache()
    {
        apc_clear_cache('user');
        return $this;
    }
    
    protected function getInternalObjectMethods()
    {
        if ($this->methods === null && $this->object !== null) {
            $this->classname = get_class($this->object);
            $this->methods   = get_class_methods($this->classname);
        }
        
        return( $this->methods );
    }
}