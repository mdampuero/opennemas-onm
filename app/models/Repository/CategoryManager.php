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
 * Handles common actions in Menus
 *
 * @package Repository
 **/
class CategoryManager extends BaseManager
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

    public function find($id)
    {
        $category = null;

        $cacheId = $this->cachePrefix . "_comment_" . $id;

        if (!$this->hasCache()
            || ($category = $this->cache->fetch($cacheId)) === false
            || !is_object($category)
        ) {
            $category = new \ContentCategory($id);

            if ($this->hasCache()) {
                $this->cache->save($cacheId, $category);
            }
        }

        return $category;
    }



    /**
     * Searches for categories given a criteria
     *
     * @param array $criteria        the criteria used to search the categories
     * @param array $order           the order applied in the search
     * @param int   $elementsPerPage the max number of elements to return
     * @param int   $page            the offset to start with
     *
     * @return array the matched elements
     **/
    public function findBy($criteria, $order = null, $elementsPerPage = null, $page = null)
    {
        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($criteria);

        $orderBySQL  = '`pk_content_category` DESC';
        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }
        $limitSQL   = $this->getLimitSQL($elementsPerPage, $page);

        // Executing the SQL
        $sql = "SELECT * FROM `content_categories` WHERE $filterSQL ORDER BY $orderBySQL $limitSQL";
        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if ($rs === false) {
            return false;
        }

        // Prepare result array
        $categories = array();
        while (!$rs->EOF) {
            $category = new \ContentCategory();
            $category->load($rs->fields);

            $categories []= $category;
            $rs->MoveNext();
        }

        return $categories;
    }
}
