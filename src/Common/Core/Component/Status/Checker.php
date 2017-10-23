<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Status;

use Symfony\Component\Filesystem\Filesystem;

/**
 * The Checker class provides methods to check database, cache and NFS
 * connections.
 */
class Checker
{
    /**
     * The cache connection.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * The database connection.
     *
     * @var Connection
     */
    protected $conn;

    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * The filesystem component.
     *
     * @var Filesystem
     */
    protected $fs;

    /**
     * Initializes the Checker.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;

        $this->cache = $this->container->get('cache.manager')
                ->getConnection('manager');
        $this->conn  = $this->container->get('orm.manager')
            ->getConnection('manager');
        $this->fs    = new Filesystem();
    }

    /**
     * Checks if connection to cache was successful.
     *
     * @return boolean True if connection to cache was successful. False
     *                 otherwise.
     */
    public function checkCacheConnection()
    {
        $cacheId = 'framework.cache.check';

        try {
            $this->cache->set($cacheId, 'bar');

            if ($this->cache->get($cacheId) !== 'bar'
                || ($this->cache->remove($cacheId)
                    && $this->cache->get($cacheId))
            ) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Checks if connection to database was successful.
     *
     * @return boolean True if connection to database was successful. False
     *                 otherwise.
     */
    public function checkDatabaseConnection()
    {
        try {
            $rs = $this->conn->fetchAll('SHOW VARIABLES LIKE "version"');

            if (count($rs) === 1
                && array_key_exists('Variable_name', $rs[0])
                && $rs[0]['Variable_name'] == 'version'
            ) {
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * Checks if connection to NFS was successful.
     *
     * @return boolean True if connection to NFS was successful. False
     *                 otherwise.
     */
    public function checkNfs()
    {
        $path = $this->container->getParameter('kernel.root_dir')
            . '/../tmp/cache/common';
        $filename = $path . '/framework.nfs.check';

        try {
            if (!$this->fs->exists($path)) {
                $this->fs->mkdir($path);
            }

            $this->fs->dumpFile($filename, 'bar');
            $this->fs->remove($filename);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Returns the configuration of the used cache connection.
     *
     * @return array The cache configuration.
     */
    public function getCacheConfiguration()
    {
        return $this->cache->getData();
    }

    /**
     * Returns the configuration of the used database connection.
     *
     * @return array The database configuration.
     */
    public function getDatabaseConfiguration()
    {
        return $this->conn->getData();
    }
}
