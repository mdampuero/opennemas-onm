<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Cache\Core;

use Common\Cache\File\File;
use Common\ORM\Core\Exception\InvalidCacheException;

/**
 * The CacheManager class manages the cache configuration and creates
 * connections for different cache types.
 */
class CacheManager
{
    /**
     * The array of caches.
     *
     * @var array
     */
    protected $caches;

    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Initializes the EntityManager.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;

        $internal = $this->getInternalConnection();
        $caches   = $internal->get('cache_' . DEPLOYED_AT);

        if (empty($caches)) {
            $caches = $container->get('cache.loader')->load();
        }

        if (!empty($caches)) {
            $this->caches = array_merge($this->caches, $caches);
        }

        foreach ($this->caches as $cache) {
            $this->container->set('cache.connection.' . $cache->name, $cache);
        }

        $internal->set('cache_' . DEPLOYED_AT, $this->caches);
    }

    /**
     * Returns a cache connection by name.
     *
     * @param string $name The cache connection name.
     *
     * @return Cache The cache connection.
     *
     * @throws InvalidCacheException  If the cache connection does not exist.
     */
    public function getConnection($name)
    {
        $name = preg_replace('/@?cache.connection/', '', $name);

        if (!array_key_exists($name, $this->caches)) {
            throw new InvalidCacheException($name);
        }

        return $this->caches[$name];
    }

    /**
     * Returns an internal filesystem cache.
     *
     * @return File The filesystem cache instance.
     */
    public function getInternalConnection()
    {
        $this->caches['internal'] =
            new File($this->container->getParameter('cache.default')['file']);

        return $this->caches['internal'];
    }
}
