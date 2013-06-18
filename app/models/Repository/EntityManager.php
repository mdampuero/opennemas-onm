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
 * An EntityRepository serves as a repository for entities with generic as well as
 * business specific methods for retrieving entities.
 *
 * This class is designed for inheritance and users can subclass this class to
 * write their own repositories with business-specific methods to locate entities.
 *
 * @package Repository
 **/
class EntityManager extends BaseManager
{
    /**
     * Initializes a new <tt>EntityRepository</tt>.
     *
     * @param DatabaseConnection $em The EntityManager to use.
     * @param ClassMetadata $classMetadata The class descriptor.
     **/
    public function __construct(CacheInterface $cacheHandler, $cachePrefix)
    {
        // $this->dbConn = $databaseConnection;
        $this->cache       = $cacheHandler;
        $this->cachePrefix = $cachePrefix;
    }

    public function find($contentType, $id)
    {
        $entity = null;

        $cacheId = $this->cachePrefix . "_" . \underscore($contentType) . "_" . $id;

        if (!$this->hasCache()
            || ($entity = $this->cache->fetch($cacheId)) === false
            || !is_object($entity)
        ) {
            $entity = new $contentType($id);

            if ($this->hasCache()) {
                $this->cache->save($cacheId, $entity);
            }
        }

        return $entity;
    }

    /**
     * Indicates if the EntityRepository has the cache handler enabled
     *
     * @return boolean true if it has cache
     **/
    protected function hasCache()
    {
        return $this->cache != null;
    }
}
