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
 * Handles common actions in UserGroups
 *
 * @package Repository
 **/
class UserGroupManager
{
    /**
     * Initializes the menu manager
     *
     * @param CacheInterface $cache the cache instance
     **/
    public function __construct(CacheInterface $cache, $cachePrefix)
    {
        $this->cache = $cache;
        $this->cachePrefix = $cachePrefix;
    }
}
