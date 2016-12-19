<?php
/**
 * Defines the Onm\Framework\Check class
 *
 * This file is part of the Onm package.
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  Onm_Framework_Check
 **/
namespace Onm\Framework;

/**
 * Service that checks configuration and third-party services connection
 *
 * @package  Onm_Framework_Check
 */
class Status
{
    /**
     * Initializes the service
     *
     * @param Container $container The application container
     **/
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function checkCacheConnection()
    {
        $cacheId = 'framework.cache.check';

        try {
            $cache = $this->container->get('cache.manager')
                ->getConnection('manager');

            $cache->set($cacheId, 'bar');

            if ($cache->get($cacheId) !== 'bar'
                || ($cache->remove($cacheId) && $cache->get($cacheId))
            ) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * undocumented function
     *
     * @return void
     **/
    public function checkDatabaseConnection()
    {
        $conn = $this->container->get('orm.manager')
            ->getConnection('manager');

        try {
            $rs = $conn->executeQuery('SHOW VARIABLES LIKE "version"');

            if ($rs) {
                $rs = $rs->fetchAll();
                if (count($rs) === 1
                    && array_key_exists('Variable_name', $rs[0])
                    && $rs[0]['Variable_name'] == 'version'
                ) {
                    return true;
                }
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function checkNfs()
    {
        $dir      = APPLICATION_PATH . '/tmp/cache/common';
        $filename = $dir . '/framework.nfs.check';

        if ((!file_exists($dir) && mkdir($dir, 0777, true) === false)
            && (!file_put_contents($filename, 'bar', true))
            && (file_exists($filename) && !unlink($filename))
        ) {
            return false;
        }

        return true;
    }
}
