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
class EntityManager extends BaseManager
{
    /**
     * Initializes the entity manager.
     *
     * @param Connection     $dbConn      The custom DBAL wrapper.
     * @param CacheInterface $cache       The cache instance.
     * @param string         $cachePrefix The cache prefix.
     */
    public function __construct($dbConn, CacheInterface $cache, $cachePrefix)
    {
        $this->dbConn      = $dbConn;
        $this->cache       = $cache;
        $this->cachePrefix = $cachePrefix;
    }

    /**
     * Finds one content from the given content type and content id.
     *
     * @param string  $contentType Content type name.
     * @param integer $id          Content id.
     *
     * @return Content The found content.
     */
    public function find($contentType, $id)
    {
        $entity = null;

        $cacheId = \underscore($contentType) . $this->cacheSeparator . $id;

        if (!empty($id)
            && class_exists($contentType)
            && (!$this->hasCache()
            || ($entity = $this->cache->fetch($cacheId)) === false
            || !is_object($entity))
        ) {
            $entity = new $contentType($id);

            if (!is_object($entity)
                || $entity->content_type_name !== \underscore($contentType)
                || (property_exists($entity, 'id') && is_null($entity->id))
            ) {
                return null;
            }

            if ($this->hasCache()) {
                $this->cache->save($cacheId, $entity);
            }
        }

        return $entity;
    }

    /**
     * Find multiple contents from a given array of content ids.
     *
     * @param array $data Array of preprocessed content ids.
     *
     * @return array Array of contents.
     */
    public function findMulti(array $data)
    {
        $ids = array_map(function ($a) {
            return \underscore($a[0]) . $this->cacheSeparator . $a[1];
        }, $data);

        $contents = $this->cache->fetch($ids);

        $cachedIds = [];
        foreach ($contents as $content) {
            $cachedIds[] = $content->content_type_name . $this->cacheSeparator . $content->id;
        }

        $missedIds = array_diff($ids, $cachedIds);

        foreach ($missedIds as $id) {
            list($contentType, $contentId) = explode($this->cacheSeparator, $id);

            $content = $this->find(\classify($contentType), $contentId);
            if (!is_null($content) && $content->id) {
                $contents[$id] = $content;
            }
        }

        $ids = array_intersect($ids, array_keys($contents));

        return array_values(array_merge(array_flip($ids), $contents));
    }

    /**
     * Searches for content given a criteria.
     *
     * @param array   $criteria        The criteria used to search.
     * @param array   $order           The order applied in the search.
     * @param integer $elementsPerPage The max number of elements.
     * @param integer $page            The current page.
     * @param integer $offset          The offset to start with.
     *
     * @return array The matched elements.
     */
    public function findBy($criteria, $order = null, $elementsPerPage = null, $page = null, $offset = 0)
    {
        $fromSQL = 'contents';

        if (is_array($criteria) && array_key_exists('tables', $criteria)) {
            $fromSQL .= ', ' . implode(',', $criteria['tables']);
            unset($criteria['tables']);
        }

        $sql = 'SELECT content_type_name, pk_content'
            . ' FROM ' . $fromSQL
            . ' LEFT JOIN contents_categories'
            . ' ON pk_content = pk_fk_content';

        if (is_array($criteria) && array_key_exists('join', $criteria)) {
            $join = $criteria['join'];
            unset($criteria['join']);
            $sql .= $this->getJoinSQL($join);
        }

        $sql .= " WHERE " . $this->getFilterSQL($criteria);

        $orderBySQL = '`pk_content` ASC';
        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }

        $limitSQL = $this->getLimitSQL($elementsPerPage, $page, $offset);

        $sql .= " ORDER BY $orderBySQL $limitSQL";

        $rs = $this->dbConn->fetchAll($sql);

        $ids = [];
        foreach ($rs as $item) {
            $ids[$item['pk_content']] =
                [ $item['content_type_name'], $item['pk_content']];
        }

        $contents = $this->findMulti(array_values($ids));

        return $contents;
    }

    /**
     * Counts contents given a criteria.
     *
     * @param array $criteria The criteria used to search.
     *
     * @return integer The number of found contents.
     */
    public function countBy($criteria)
    {
        $fromSQL = 'contents';

        if (is_array($criteria) && array_key_exists('tables', $criteria)) {
            $fromSQL .= ', ' . implode(',', $criteria['tables']);
            unset($criteria['tables']);
        }

        $sql = 'SELECT COUNT(DISTINCT content_type_name, pk_content)'
            . ' FROM ' . $fromSQL
            . ' LEFT JOIN contents_categories'
            . ' ON pk_content = pk_fk_content';

        if (is_array($criteria) && array_key_exists('join', $criteria)) {
            $join = $criteria['join'];
            unset($criteria['join']);
            $sql .= $this->getJoinSQL($join);
        }

        $sql .= " WHERE " . $this->getFilterSQL($criteria);

        $rs = $this->dbConn->fetchArray($sql);

        if (!$rs) {
            return 0;
        }

        return $rs[0];
    }

    /**
     * Populates content meta for a given array of content objects
     *
     * @return array the list of contents with populated metadata
     */
    public function populateContentMetasInContents(&$contentMap)
    {
        if (empty($contentMap)) {
            return [];
        }

        $searchMap = array_map(function ($a) {
            return 'content-meta-' . $a->id;
        }, $contentMap);

        // Fetch all content metas in one request
        $contentMetaMap = $this->cache->fetch($searchMap);

        $contentMap = array_filter($contentMap, "is_object");
        // Populate contents with fetched content metas
        foreach ($contentMap as $content) {
            // If content metas weren't in cache fetch them from mysql
            if (!array_key_exists('content-meta-' . $content->id, $contentMetaMap)) {
                $content->loadAllContentProperties();
            } else {
                $contentMeta = $contentMetaMap['content-meta-' . $content->id];

                if (!empty($contentMeta)) {
                    foreach ($contentMeta as $key => $value) {
                        $content->{$key} = $value;
                    }
                }
            }
        }

        return $contentMap;
    }
}
