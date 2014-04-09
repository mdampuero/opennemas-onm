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
use Onm\Database\DbalWrapper;

/**
 * Handles common actions in Menus
 *
 * @package Repository
 **/
class CategoryManager extends BaseManager
{
    /**
     * Initializes the entity manager
     *
     * @param CacheInterface $cache the cache instance
     **/
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

        $cacheId = "category_" . $id;

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
        $keys    = array();
        $ordered = array();

        $ids = array();
        $i = 0;


        foreach ($data as $value) {
            $ids[] = $value;
            $keys[$value] = $i++;
        }

        $categories = $this->cache->fetch($ids);

        $cachedIds = array();
        foreach ($categories as $category) {
            $ordered[$keys[$category->pk_content_category]] = $category;
            $cachedIds[] = 'category_'. $category->pk_content_category;
        }

        $missedIds = array_diff($ids, $cachedIds);

        foreach ($missedIds as $id) {
            $category = $this->find($id);
            if ($category->pk_content_category) {
                $ordered[$keys[$category->pk_content_category]] = $category;
            }
        }

        return array_values($ordered);
    }

     /**
     * Searches for content given a criteria
     *
     * @param  array $criteria        the criteria used to search the comments.
     * @param  array $order           the order applied in the search.
     * @param  int   $elementsPerPage the max number of elements to return.
     * @param  int   $page            the offset to start with.
     * @return array                  the matched elements.
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
            $ids[]= $result['pk_content_category'];
        }

        $categories = $this->findMulti($ids);

        return $categories;
    }

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
