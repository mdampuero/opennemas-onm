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
        $this->caches    = $container->get('cache.loader')->load();
        $this->container = $container;

        foreach ($this->caches as $cache) {
            $this->container->set('cache.connection.' . $cache->name, $cache);
        }
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
}
