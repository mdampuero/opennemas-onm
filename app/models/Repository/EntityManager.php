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
     * Finds one content from the given content type and content id.
     *
     * @param  string  $contentType Content type name.
     * @param  integer $id          Content id
     * @return Content
     */
    public function find($contentType, $id)
    {
        $entity = null;

        $cacheId = \underscore($contentType) . "_" . $id;

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
     * Find multiple contents from a given array of content ids.
     *
     * @param  array $contentsData Array of preprocessed content ids.
     * @return array               Array of contents.
     */
    public function findMulti(array $data)
    {
        $ordered = array();

        $ids = array();
        $i = 0;
        foreach ($data as $value) {
            $ids[] = $value[0] . '-' . $value[1];
            $ordered[$value[1]] = $i++;
        }

        $contents = $this->cache->fetch($ids);

        $cachedIds = array();
        foreach ($contents as $content) {
            $ordered[$content->id] = $content;
            $cachedIds[] = $content->content_type_name.'-'.$content->id;
        }

        $missedIds = array_diff($ids, $cachedIds);
        foreach ($missedIds as $content) {
            list($contentType, $contentId) = explode('-', $content);

            $content = $this->find(\classify($contentType), $contentId);
            $ordered[$content->id] = $content;
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

        $orderBySQL  = '`pk_content` DESC';
        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }
        $limitSQL   = $this->getLimitSQL($elementsPerPage, $page);

        // Executing the SQL
        $sql = "SELECT content_type_name, pk_content FROM `contents` "
            ."WHERE $filterSQL ORDER BY $orderBySQL $limitSQL";

        $this->dbConn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $this->dbConn->fetchAll($sql);

        $contentIdentifiers = array();
        foreach ($rs as $resultElement) {
            $contentIdentifiers[]= array(
                $resultElement['content_type_name'],
                $resultElement['pk_content']
            );
        }

        $contents = $this->findMulti($contentIdentifiers);

        return $contents;
    }

    public function countBy($criteria)
    {
        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($criteria);

        // Executing the SQL
        $sql = "SELECT COUNT(pk_content) FROM `contents` WHERE $filterSQL";
        $rs = $this->dbConn->fetchArray($sql);

        if (!$rs) {
            return 0;
        }

        return $rs[0];
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
        $rs  = $this->dbConn->GetOne($sql);

        if ($rs === false) {
            return false;
        }

        return $rs;
    }

    /**
     * Clean id and search if exist in content table.
     * If not found search in refactor_id table. (used for translate old format ids)
     *
     * @param string $dirtyID Vble with date in first 14 digits
     *
     * @return int id in table content or forward to 404
     */
    public function resolveId($id)
    {
        $cacheKey = 'content_resolve_id_'.$id;
        $resolvedID = (int) $this->cache->fetch($cacheKey);

        if (!empty($resolvedID)) {
            return $resolvedID;
        }

        $contentID = 0;
        if (preg_match('@tribuna@', INSTANCE_UNIQUE_NAME)
            || preg_match('@retrincos@', INSTANCE_UNIQUE_NAME)
            || preg_match('@cronicas@', INSTANCE_UNIQUE_NAME)
        ) {
            $sql = "SELECT pk_content FROM `refactor_ids` WHERE pk_content_old = ?";
            $contentID = $GLOBALS['application']->conn->GetOne($sql, array($id));

            if (!empty($contentID)) {
                $content = $this->find('Content', $contentID)->get($contentID);

                forward301('/'.$content->uri);
            }
        }

        preg_match("@(?P<dirtythings>\d{1,14})(?P<digit>\d+)@", $id, $matches);

        $sql       = "SELECT pk_content FROM `contents` WHERE pk_content = ? LIMIT 1";
        $value     = array((int) $matches["digit"]);
        $rs = $this->dbConn->executeQuery($sql, $value);
        $rs = $rs->fetch(ADODB_FETCH_ASSOC);

        if ($rs) {
            $contentID = $rs['pk_content'];
            $this->cache->save($cacheKey, $contentID);
        }

        return $contentID;
    }

    /**
     * Builds the SQL WHERE filter given an array or string with the desired filter
     *
     * @param string|array $filter the filter params
     *
     * @return string the SQL WHERE filter
     */
    protected function getFilterSQL($filters)
    {
        if (empty($filters)) {
            $filterSQL = ' 1=1 ';
        } elseif (is_array($filters)) {
            $filterSQL = array();

            foreach ($filters as $field => $values) {
                $fieldFilters = array();

                foreach ($values as $filter) {
                    $operator = "=";
                    $value    = "";
                    if ($filter['value'][0] == '%'
                        && $filter['value'][strlen($filter['value']) - 1] == '%'
                    ) {
                        $operator = "LIKE";
                    }

                    // Check operator
                    if (array_key_exists('operator', $filter)) {
                        $operator = $filter['operator'];
                    }

                    // Check value
                    if (array_key_exists('value', $filter)) {
                        $value = $filter['value'];
                    }

                    $fieldFilters[] = "`$field` $operator '$value'";
                }

                // Add filters for the current $field
                $filterSQL[] = implode(' OR ', $fieldFilters);
            }

            // Build filters
            $filterSQL = implode(' AND ', $filterSQL);
        } else {
            $filterSQL = $filter;
        }

        return $filterSQL;
    }
}
