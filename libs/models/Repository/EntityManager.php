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
     * @param boolean $isMulti
     *
     * @return Content The found content.
     */
    public function find($contentType, $id)
    {
        return $this->findOne($contentType, $id);
    }

    /**
     * Finds one content from the given content type and content id.
     *
     * @param string  $contentType Content type name.
     * @param integer $id          Content id.
     * @param boolean $isMulti     Indicates if it is called from findMulti and
     *                             several are to be ordered and it is not
     *                             necessary to cache
     *
     * @return Content The found content.
     */
    private function findOne($contentType, $id, $isMulti = false)
    {
        $entity = null;

        $cacheId = \underscore($contentType) . $this->cacheSeparator . $id;

        if (!empty($id)
            && class_exists($contentType)
            && ($isMulti
            || !$this->hasCache()
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

            if (!$isMulti) {
                $contentsToLoad            = [$entity];
                $contentsToLoadExtraData   = [];
                $contentsToLoadExtraData[] = $entity->id;
                $this->loadExtraDataToContents($contentsToLoadExtraData, $contentsToLoad);
                if ($this->hasCache()) {
                    $this->cache->save($cacheId, $entity);
                }
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

        $saveInCacheIds = [];
        foreach ($missedIds as $id) {
            list($contentType, $contentId) = explode($this->cacheSeparator, $id);

            $content = $this->findOne(\classify($contentType), $contentId, true);
            if (!is_null($content) && $content->id) {
                $saveInCacheIds[$id] = $content->id;
                $contents[$id]       = $content;
            }
        }

        $elementToCache = $this->loadExtraDataToContents($saveInCacheIds, $contents);
        if ($this->hasCache()) {
            $this->cache->save($elementToCache);
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
     * @param integer $count           Number of results for the query
     *
     * @return array The matched elements.
     */
    public function findBy($criteria, $order = null, $elementsPerPage = null, $page = null, $offset = 0, &$count = null)
    {
        $sql = 'SELECT ' . (($count) ? 'SQL_CALC_FOUND_ROWS  ' : '') . 'content_type_name, pk_content'
            . ' FROM contents ';

        if (is_array($criteria) && array_key_exists('join', $criteria)) {
            $join = $criteria['join'];
            unset($criteria['join']);
            $sql .= $this->getJoinSQL($join);
        }

        $criteriaAux = $this->getFilterSQL($criteria);

        $haveContentCategory = strpos($criteriaAux, 'pk_fk_content_category') !== false;

        if ($haveContentCategory) {
            $sql .= ' LEFT JOIN contents_categories'
            . ' ON contents.pk_content = contents_categories.pk_fk_content ';
        }

        if (strpos($criteriaAux, 'content_type_name') !== false) {
            $this->removeContentTypeNameFromCriteria($criteriaAux);
        }

        $sql .= " WHERE " . $criteriaAux;

        $orderBySQL = '`pk_content` ASC';
        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }

        $limitSQL = $this->getLimitSQL($elementsPerPage, $page, $offset);

        if ($haveContentCategory) {
            $sql .= " GROUP BY `pk_content`";
        }
        $sql .= " ORDER BY $orderBySQL $limitSQL";

        $rs = $this->dbConn->fetchAll($sql);
        if ($count) {
            $count = $this->getSqlCount();
        }

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
            . ' FROM ' . $fromSQL;

        if (is_array($criteria) && array_key_exists('join', $criteria)) {
            $join = $criteria['join'];
            unset($criteria['join']);
            $sql .= $this->getJoinSQL($join);
        }

        $criteriaAux = $this->getFilterSQL($criteria);

        if (strpos($criteriaAux, 'content_type_name') !== false) {
            $this->removeContentTypeNameFromCriteria($criteriaAux);
        }

        $haveContentCategory = strpos($criteriaAux, 'pk_fk_content_category') !== false;

        if ($haveContentCategory) {
            $sql .= ' LEFT JOIN contents_categories'
            . ' ON contents.pk_content = contents_categories.pk_fk_content ';
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

        $contentMap = array_filter($contentMap, "is_object");

        $searchMap = array_map(function ($a) {
            return 'content-meta-' . $a->id;
        }, $contentMap);

        // Fetch all content metas in one request
        $contentMetaMap  = $this->cache->fetch($searchMap);
        $missingContents = [];

        foreach ($searchMap as $key) {
            if (!array_key_exists($key, $contentMetaMap)) {
                // This substr is for remove the "content-meta-" of the key
                $missingContents[] = substr($key, 13);
            }
        }
        $missingContents = $this->populateContentMetasFromDB($missingContents);

        // Populate contents with fetched content metas

        foreach ($contentMap as $content) {
            $contentMeta = [];
            // If content metas weren't in cache fetch them from mysql
            if (array_key_exists('content-meta-' . $content->id, $contentMetaMap)) {
                $contentMeta = $contentMetaMap['content-meta-' . $content->id];
            } elseif (array_key_exists($content->id, $missingContents)) {
                $contentMeta = $missingContents[$content->id];
            }
            foreach ($contentMeta as $key => $value) {
                $content->{$key} = $value;
            }
        }

        return $contentMap;
    }

    /**
     *  Populates content meta for a given array of content objects from DB
     *
     * @param mixed $contents the list of content ids
     *
     * @return mixed the list of contents with populated metadata
     */
    private function populateContentMetasFromDB($contents)
    {
        if (is_null($contents)) {
            return null;
        }

        $contentsToRetrieve = (is_array($contents)) ? $contents : [$contents];

        if (count($contentsToRetrieve) == 0) {
            return [];
        }

        $contentProperties = [];

        $sqlAux = substr(str_repeat(',?', count($contentsToRetrieve)), 1);
        $sql    = 'SELECT `fk_content`, `meta_name`, `meta_value` FROM `contentmeta` WHERE fk_content IN ('
            . $sqlAux . ')';

        $properties = $this->dbConn->fetchAll(
            $sql,
            $contentsToRetrieve
        );
        if (!is_null($properties) && is_array($properties)) {
            foreach ($properties as $property) {
                if (!array_key_exists($property['fk_content'], $contentProperties)) {
                    $contentProperties[$property['fk_content']] = [];
                }
                $contentProperties[$property['fk_content']][$property['meta_name']] = $property['meta_value'];
            }
        }


        $cacheValues = [];
        foreach ($contentProperties as $id => $content) {
            $cacheValues['content-meta-' . $id] = serialize($content);
        }

        $this->cache->save($cacheValues);

        if (!is_array($contents) && count($contentProperties) > 0) {
            return array_values($contentProperties)[0];
        }
        return $contentProperties;
    }

    /**
     *  Retrieve from database the number of queryes from the request before.
     *
     *  @return integer The number of found contents.
     */
    protected function getSqlCount()
    {
        $rs = $this->dbConn->fetchAll('SELECT FOUND_ROWS() as count');

        if (is_array($rs)
            && array_key_exists(0, $rs)
            && array_key_exists('count', $rs[0])
        ) {
            return $rs[0]['count'];
        }

        return 0;
    }

    /**
     *  Replace criterias with content_type_name for fk_content_type
     *
     *   @param String $criterias The criteria used to search.
     */
    protected function removeContentTypeNameFromCriteria(&$criterias)
    {
        preg_match_all(
            "/content_type_name\s*=\s*'{1}([A-Za-z0-9 ]*)'{1}|content_type_name\s*=\s*\"{1}([A-Za-z0-9 ]*)\"{1}/",
            $criterias,
            $result
        );
        $count = 0;

        for ($count = 0; $count < count($result[0]); $count++) {
            $value = $result[1][$count];

            if (empty($value)) {
                $value = $result[2][$count];
            }

            $contentTypeName = \ContentManager::getContentTypeIdFromName($value);
            if ($contentTypeName !== false) {
                $criterias = str_replace($result[0][$count], 'fk_content_type=' . $contentTypeName . ' ', $criterias);
            }
        }
    }

    /**
     * Returns a multidimensional array with the images related to this album
     *
     * @param int $albumID the album id
     *
     * @return mixed array of array(pk_photo, position, description)
     */
    public function getAttachedPhotos($albumID)
    {
        if (is_null($albumID)) {
            return false;
        }

        $attachPhotosToRetrieve = (is_array($albumID)) ? $albumID : [$albumID];

        if (count($attachPhotosToRetrieve) == 0) {
            return [];
        }

        $sqlAux = substr(str_repeat(',?', count($attachPhotosToRetrieve)), 1);

        $photosAlbum = [];
        $sql         = 'SELECT DISTINCT pk_album, pk_photo, description, position'
            . ' FROM albums_photos WHERE pk_album IN (' . $sqlAux . ') ORDER BY position ASC';
        $rs          = $this->dbConn->fetchAll($sql, $albumID);
        foreach ($rs as $photo) {
            if (!array_key_exists($photo['pk_album'], $photosAlbum)) {
                $photosAlbum[$photo['pk_album']] = [];
            }

            $photosAlbum[$photo['pk_album']][] = [
                'pk_photo'    => $photo['pk_photo'],
                'position'    => $photo['position'],
                'description' => $photo['description'],
            ];
        }

        if (!is_array($albumID) && count($photosAlbum) > 0) {
            return array_values($photosAlbum)[0];
        }

        return $photosAlbum;
    }

    /**
     *  Method to load extra data to list of contents
     *
     * @param array $saveInCacheIds list of contents ids to load extra data
     * @param array $contents       List of contents where load the extra data
     */
    private function loadExtraDataToContents($saveInCacheIds, &$contents)
    {
        if (empty($saveInCacheIds)) {
            return [];
        }

        $tagsByContent    = array_values($contents)[0]->getContentTags(array_values($saveInCacheIds));
        $contentsForCache = [];
        foreach ($saveInCacheIds as $cacheKey => $contentId) {
            if (array_key_exists($contentId, $tagsByContent)) {
                $contents[$cacheKey]->tag_ids = $tagsByContent[$contentId];
            } else {
                $contents[$cacheKey]->tag_ids = [];
            }
            $contentsForCache[$cacheKey] = $contents[$cacheKey];
        }

        return $contentsForCache;
    }
}
