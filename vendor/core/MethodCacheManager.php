<?php
/*
 * Defines the MethodCacheManager class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Handles caching functionality over function and class calling.
 *
 * @package    Core
 * @subpackage Cache
 * @author     Fran Dieguez <fran@openhost.es>
 **/

class MethodCacheManager
{
    /**
     * How much time the cache will be valid
     *
     * @var int
     **/
    private $ttl       = 300;

    /**
     * The object to interact with
     *
     * @var mixed
     **/
    private $object    = null;

    /**
     * The object with cache service
     *
     * @var mixed
     **/
    private $cache    = null;

    /**
     * A list of methods that the object has
     *
     * @var array
     **/
    private $methods   = null;

    /**
     * The class name of the referenced object
     *
     * @var string
     **/
    private $classname = null;

    /**
     * Initializes the instance
     *
     * @param mixed $object  the object to interact with
     * @param array $options some options to change the behaviour of this class
     *                       like ttl, ...
     *
     * @return MethodCacheManager
     **/
    public function __construct($object, $options = array())
    {

        global $sc;
        $this->cache = $sc->get('cache');

        $this->object = $object;

        if (isset($options['ttl'])) {
            $this->ttl = $options['ttl'];
        }
    }

    /**
     * Proxy method that performs all the calls to the object and caches its results
     * if the result was previously cached returns the result directly from the cache
     *
     * @param string $method the method to call
     * @param array  $args   the arguments to pass to the method
     *
     * @return mixed the result of the called
     **/
    public function __call($method, $args)
    {
        $class_methods = $this->getInternalObjectMethods();

        if (in_array($method, $class_methods)) {

            $key = $this->classname.$method.md5(serialize($args));
            if (defined('CACHE_PREFIX')) {
                $key = CACHE_PREFIX . $key;
            }

            if (false === ($result = $this->cache->fetch($key))) {

                $result = call_user_func_array(array($this->object, $method), $args);
                $this->cache->save($key, serialize($result), $this->ttl);

                return $result;
            }

            return unserialize($result);
        } else {
            throw new Exception(
                " Method ".$method." does not exist in this class ".get_class($this->object)."."
            );
        }
    }

    /**
     * Sets the time to life of caches for this instance
     *
     * @param int $ttl the amount of seconds the cache will be valid
     *
     * @return MethodCacheManager
     **/
    public function setCacheLife($ttl)
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * Deletes an APC cache given its key
     *
     * @param string $key the name of the cache to delete
     *
     * @return MethodCacheManager
     **/
    public function clearCache($key)
    {
        $this->cache->delete($key);

        return $this;
    }

    /**
     * Deletes all the APC caches.
     *
     * @return MethodCacheManager
     **/
    public function clearAllCaches()
    {
        $this->cache->deleteAll();

        return $this;
    }

    /**
     * Return the list of class methods for the object that this instance is
     * interacting with
     *
     * @return array
     **/
    protected function getInternalObjectMethods()
    {
        if ($this->methods === null && $this->object !== null) {
            $this->classname = get_class($this->object);
            $this->methods   = get_class_methods($this->classname);
        }

        return $this->methods;
    }
}
