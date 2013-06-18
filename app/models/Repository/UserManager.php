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
class UsersManager
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

    /**
     * Fetches a Users instance given its id
     *
     * @param int $id the user id
     *
     * @return Users the object instance
     **/
    public function find($id)
    {
        // search in cache
        // - if not in cache fetch from database
        //   * store new User object filled with information into cache
        // return the User object
    }
}
