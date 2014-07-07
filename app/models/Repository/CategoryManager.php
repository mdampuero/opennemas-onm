<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Repository;

use Onm\Cache\CacheInterface;
use Onm\Database\DbalWrapper;

/**
 * An EntityRepository serves as a repository for entities with generic as well
 * as business specific methods for retrieving entities.
 *
 * This class is designed for inheritance and users can subclass this class to
 * write their own repositories with business-specific methods to locate
 * entities.
 *
 * @package Repository
 */
class CategoryManager extends BaseManager
{
    /**
     * Initializes the entity manager.
     *
     * @param CacheInterface $cache the cache instance.
     */
    public function __construct(DbalWrapper $dbConn, CacheInterface $cache, $cachePrefix)
    {
        $this->dbConn      = $dbConn;
        $this->cache       = $cache;
        $this->cachePrefix = $cachePrefix;
    }

    /**
     * Finds one category from the given category id.
     *
     * @param  integer         $id Content id
     * @return ContentCategory
     */
    public function find($id)
    {
        $entity = null;

        $cacheId = 'category' . $this->cacheSeparator . $id;

        if (!$this->hasCache()
            || ($entity = $this->cache->fetch($cacheId)) === false
            || !is_object($entity)
        ) {
            $entity = new \ContentCategory($id);

            if ($this->hasCache()) {
                $this->cache->save($cacheId, $entity);
            }
        }

        return $entity;
    }

    /**
     * Find multiple categories from a given array of category ids.
     *
     * @param  array $data Array of preprocessed category ids.
     * @return array       Array of contents.
     */
    public function findMulti(array $data)
    {

        $ids  = array();
        $keys = array();
        foreach ($data as $value) {
            $ids[] = 'category' . $this->cacheSeparator . $value;
            $keys[] = $value;
        }

        $categories = array_values($this->cache->fetch($ids));

        $cachedIds = array();
        foreach ($categories as $category) {
            $cachedIds[] = 'category'. $this->cacheSeparator
                . $category->pk_content_category;
        }

        $missedIds = array_diff($ids, $cachedIds);

        foreach ($missedIds as $id) {
            list($contentType, $categoryId) = explode($this->cacheSeparator, $id);
            $category = $this->find($categoryId);
            if ($category->pk_content_category) {
                $categories[] = $category;
            }
        }

        $ordered = array();
        foreach ($keys as $id) {
            $i = 0;
            while ($i < count($categories)
                && $categories[$i]->pk_content_category != $id
            ) {
                $i++;
            }

            if ($i < count($categories)) {
                $ordered[] = $categories[$i];
            }
        }

        return $ordered;
    }

    /**
     * Searches for content categories given a criteria.
     *
     * @param  array|string $criteria        The criteria used to search.
     * @param  array        $order           The order applied in the search.
     * @param  int          $elementsPerPage The max number of elements.
     * @param  int          $page            The offset to start with.
     * @return array                         The matched elements.
     */
    public function findBy($criteria, $order, $elementsPerPage = null, $page = null)
    {
        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($criteria);

        $orderBySQL  = '`pk_content_category` DESC';
        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }
        $limitSQL   = $this->getLimitSQL($elementsPerPage, $page);

        // Executing the SQL
        $sql = "SELECT pk_content_category FROM `content_categories` "
            ."WHERE $filterSQL ORDER BY $orderBySQL $limitSQL";

        $this->dbConn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $this->dbConn->fetchAll($sql);

        $ids = array();
        foreach ($rs as $result) {
            $ids[] = $result['pk_content_category'];
        }

        $categories = $this->findMulti($ids);

        return $categories;
    }

    /**
     * Counts content categories given a criteria.
     *
     * @param  array|string $criteria The criteria used to search.
     * @return array                  The number of content categories.
     */
    public function countBy($criteria)
    {
        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($criteria);

        // Executing the SQL
        $sql = "SELECT COUNT(pk_content_category) FROM `content_categories`"
            ." WHERE $filterSQL";
        $rs = $this->dbConn->fetchArray($sql);

        if (!$rs) {
            return 0;
        }

        return $rs[0];
    }
}
