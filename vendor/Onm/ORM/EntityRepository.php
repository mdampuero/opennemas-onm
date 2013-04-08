<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\ORM;

/**
 * An EntityRepository serves as a repository for entities with generic as well as
 * business specific methods for retrieving entities.
 *
 * This class is designed for inheritance and users can subclass this class to
 * write their own repositories with business-specific methods to locate entities.
 *
 * @package Onm_ORM
 **/
class EntityRepository
{
    /**
     * Initializes a new <tt>EntityRepository</tt>.
     *
     * @param DatabaseConnection $em The EntityManager to use.
     * @param ClassMetadata $classMetadata The class descriptor.
     **/
    public function __construct($cacheHandler = null)
    {
        // $this->dbConn = $databaseConnection;
        $this->cache  = $cacheHandler;
    }

    public function find($contentType, $id)
    {
        $entity = null;
        if ($this->hasCache()) {
            $cacheId = INSTANCE_UNIQUE_NAME . "_" . $contentType . "_" . $id;
            $entity = $this->cache->fetch($cacheId);
        }

        if (!is_object($entity)) {
            $entity = new $contentType($id);

            if ($this->hasCache()) {
                $this->cache->save($cacheId, $entity);
            }
        }

        return $entity;
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
    }

    public function findOneBy(array $criteria)
    {

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
