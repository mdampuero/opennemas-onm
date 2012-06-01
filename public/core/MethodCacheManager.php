<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Handles caching functionality over function and class calling.
 *
 * @package    Onm
 * @subpackage Cache
 * @author     Fran Dieguez <fran@openhost.es>
 **/
class MethodCacheManager
{
    private $_ttl       = 300;
    private $_object    = null;
    private $_methods   = null;
    private $_classname = null;

    public function __construct($object, $options=array())
    {
        $this->_object = $object;

        if (isset($options['ttl'])) {
            $this->_ttl = $options['ttl'];
        }
    }

    public function __call($method, $args)
    {
        $class_methods = $this->getInternalObjectMethods();

        if (in_array($method, $class_methods)) {
            $key = $this->_classname.$method.md5(serialize($args));
            if (defined('APC_PREFIX')) {
                $key = APC_PREFIX . $key;
            }

            if (false === ($result = apc_fetch($key))) {
                $result = call_user_func_array(array($this->_object,
                    $method), $args);
                apc_store($key, serialize($result), $this->_ttl);

                return $result;
            }

            return unserialize($result);
        } else {
            throw new Exception(" Method " . $method
                . " does not exist in this class "
                . get_class($this->_object) . "."
            );
        }
    }

    public function set_cache_life($ttl)
    {
        $this->_ttl = $ttl;

        return $this;
    }

    public function setCacheLife($ttl)
    {
        $this->_ttl = $ttl;

        return $this;
    }

    public function clearCache($key)
    {
        apc_delete($key);

        return $this;
    }

    public function clearAllCaches()
    {
        apc_clear_cache('user');

        return $this;
    }

    protected function getInternalObjectMethods()
    {
        if ($this->_methods === null && $this->_object !== null) {
            $this->_classname = get_class($this->_object);
            $this->_methods   = get_class_methods($this->_classname);
        }

        return $this->_methods;
    }
}
