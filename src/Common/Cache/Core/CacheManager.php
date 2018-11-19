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

use Common\Cache\Core\Exception\InvalidConnectionException;

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
        $this->config    = $container->getParameter('cache');
        $this->container = $container;
        $this->defaults  = $container->getParameter('cache.default');

        $this->caches = $this->init();

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
     * @throws InvalidConnectionException If the cache connection does not exist.
     */
    public function getConnection($name)
    {
        if (!$this->hasConnection($name)) {
            throw new InvalidConnectionException($name);
        }

        $name = preg_replace('/@?cache.connection/', '', $name);

        return $this->caches[$name];
    }

    /**
     * Check if a connection exists.
     *
     * @param string $name The connection name.
     *
     * @return boolean True if a connection exists. False, otherwise.
     */
    public function hasConnection($name)
    {
        $name = preg_replace('/@?cache.connection/', '', $name);

        return array_key_exists($name, $this->caches);
    }

    /**
     * Initializes connections basing on configuration.
     *
     * @return array The list of connections.
     */
    protected function init()
    {
        $items = [];

        if (empty($this->config)) {
            return $items;
        }

        foreach ($this->config as $key => $config) {
            if (!array_key_exists('type', $config)) {
                $config['type'] = $this->defaults['type'];
            }

            if (array_key_exists($config['type'], $this->defaults)) {
                $config = array_merge($this->defaults[$config['type']], $config);
            }

            $class = \classify($config['type']);
            $class = sprintf('Common\\Cache\\%s\\%s', $class, $class);

            $items[$config['name']] = new $class($config);
        }

        return $items;
    }
}
