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
     * Searches for content given a criteria
     *
     * @param array $criteria        the criteria used to search the comments
     * @param array $order           the order applied in the search
     * @param int   $elementsPerPage the max number of elements to return
     * @param int   $page            the offset to start with
     *
     * @return array the matched elements
     **/
    public function findBy($criteria, $order, $elementsPerPage = null, $page = null)
    {
        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($criteria);

        $orderBySQL  = '`pk_content` DESC';
        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }
        $limitSQL   = $this->getLimitSQL($elementsPerPage, $page);

        // Executing the SQL
        $sql = "SELECT * FROM `contents` WHERE $filterSQL ORDER BY $orderBySQL $limitSQL";

        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if ($rs === false) {
            \Application::logDatabaseError();

            return false;
        }

        $content = array();
        while (!$rs->EOF) {
            $content = new \Content();
            $content->load($rs->fields);

            $contents[]= $content;
            $rs->MoveNext();
        }

        return $contents;
    }

    /**
     * Returns the number of contents given a filter
     *
     * @param string|array $filter the filter to apply
     *
     * @return int the number of comments
     **/
    public function count($filter)
    {
        // Building the SQL filter
        $filterSQL = $this->getFilterSQL($filter);

        // Executing the SQL
        $sql = "SELECT  count(pk_content) FROM `contents` WHERE $filterSQL";
        $rs = $GLOBALS['application']->conn->GetOne($sql);

        if ($rs === false) {
            \Application::logDatabaseError();

            return false;
        }

        return $rs;
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
