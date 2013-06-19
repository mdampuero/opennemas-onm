<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Repository;

use Onm\Cache\CacheInterface;

/**
 * Handles common operations with users
 *
 * @package Repository
 */
class UserManager extends BaseManager
{
    /**
     * Initializes the Users Manager
     *
     * @param CacheInterface $cache the cache handler
     **/
    public function __construct(CacheInterface $cache, $cachePrefix)
    {
        $this->cache = $cache;
        $this->cachePrefix = $cachePrefix;
    }

    public function find($id)
    {
        $user = null;

        $cacheId = $this->cachePrefix . "_user_" . $id.microtime(true);

        if (!$this->hasCache()
            || ($user = $this->cache->fetch($cacheId)) === false
            || !is_object($entity)
        ) {
            $user = new \User($id);

            if ($this->hasCache()) {
                $this->cache->save($cacheId, $user);
            }
        }

        return $user;
    }
}
