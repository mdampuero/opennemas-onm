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
class EntityManager extends BaseManager
{
    /**
     * Initializes the entity manager.
     *
     * @param DbalWrapper    $dbConn      The custom DBAL wrapper.
     * @param CacheInterface $cache       The cache instance.
     * @param string         $cachePrefix The cache prefix.
     */
    public function __construct(DbalWrapper $dbConn, CacheInterface $cache, $cachePrefix)
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

        if (!$this->hasCache()
            || ($entity = $this->cache->fetch($cacheId)) === false
            || !is_object($entity)
        ) {
            $entity = new $contentType($id);

            if (!is_object($entity)
                || (is_object($entity)
                    && property_exists($entity, 'id')
                    && is_null($entity->id)
                )
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
        $ids  = array();
        $keys = array();
        foreach ($data as $value) {
            $ids[] = underscore($value[0]) . $this->cacheSeparator . $value[1];
            $keys[] = $value[1];
        }

        $contents = array_values($this->cache->fetch($ids));

        $cachedIds = array();
        foreach ($contents as $content) {
            $cachedIds[] = $content->content_type_name . $this->cacheSeparator . $content->id;
        }

        $missedIds = array_diff($ids, $cachedIds);

        foreach ($missedIds as $content) {
            list($contentType, $contentId) = explode($this->cacheSeparator, $content);

            $content = $this->find(\classify($contentType), $contentId);
            if (!is_null($content) && $content->id) {
                $contents[] = $content;
            }
        }

        $ordered = array();
        foreach ($keys as $id) {
            $i = 0;
            while ($i < count($contents) && $contents[$i]->id != $id) {
                $i++;
            }

            if ($i < count($contents)) {
                $ordered[] = $contents[$i];
            }
        }

        return $ordered;
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
            $fromSQL .= ', '.implode(',', $criteria['tables']);
            unset($criteria['tables']);
        }

        $sql = "SELECT content_type_name, pk_content FROM $fromSQL ";

        if (is_array($criteria) && array_key_exists('join', $criteria)) {
            $join = $criteria['join'];
            unset($criteria['join']);
            $sql .= $this->getJoinSQL($join);
        }

        $sql .= " WHERE " . $this->getFilterSQL($criteria);

        $orderBySQL  = '`pk_content` ASC';
        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }

        $limitSQL = $this->getLimitSQL($elementsPerPage, $page, $offset);

        $sql .= " ORDER BY $orderBySQL $limitSQL";

        $rs = $this->dbConn->fetchAll($sql);

        $ids = array();
        foreach ($rs as $item) {
            $ids[] = array($item['content_type_name'], $item['pk_content']);
        }

        $contents = $this->findMulti($ids);

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
            $fromSQL .= ', '.implode(',', $criteria['tables']);
            unset($criteria['tables']);
        }

        $sql = "SELECT COUNT(pk_content) FROM $fromSQL ";

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
     **/
    public function populateContentMetasInContents(&$contentMap)
    {
        foreach ($contentMap as $content) {
            $searchMap []= 'content-meta-'.$content->id;
        }

        // Fetch all content metas in one request
        $contentMetaMap = $this->cache->fetch($searchMap);

        // Populate contents with fetched content metas
        foreach ($contentMap as $content) {
            // If content metas weren't in cache fetch them from mysql
            if (!array_key_exists('content-meta-'.$content->id, $contentMetaMap)) {
                $content->loadAllContentProperties();
            } else {
                $contentMeta = $contentMetaMap['content-meta-'.$content->id];

                foreach ($contentMeta as $key => $value) {
                    $content->{$key} = $value;
                }
            }

        }

        return $contentMap;
    }
}
