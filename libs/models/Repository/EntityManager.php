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

use Api\Exception\GetItemException;
use \Common\Model\Entity\Content;
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
    const ORM_CONTENT_TYPES = [
        'album',
        'article',
        'attachment',
        'company',
        'event',
        'kiosko',
        'letter',
        'obituary',
        'opinion',
        'photo',
        'poll',
        'video',
        'widget'
    ];

    /**
     * Initializes the entity manager.
     *
     * @param Connection     $dbConn      The custom DBAL wrapper.
     * @param CacheInterface $cache       The cache instance.
     * @param Monolog        $error       The monolog error instance
     * @param string         $cachePrefix The cache prefix.
     */
    public function __construct($dbConn, CacheInterface $cache, $error, $cachePrefix)
    {
        $this->dbConn      = $dbConn;
        $this->cache       = $cache;
        $this->error       = $error;
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

        if (in_array(\underscore($contentType), self::ORM_CONTENT_TYPES)) {
            try {
                $entity = getService('api.service.' . \underscore($contentType))->getItem($id);
            } catch (GetItemException $e) {
                return null;
            }

            return $entity;
        }

        $contentType = \classify($contentType);
        $cacheId     = \underscore($contentType) . $this->cacheSeparator . $id;

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
     *
     * @return array The matched elements.
     */
    public function findBy($criteria, $order = null, $elementsPerPage = null, $page = null, $offset = 0)
    {
        $sql = 'SELECT content_type_name, pk_content FROM contents ';

        if (is_array($criteria) && array_key_exists('join', $criteria)) {
            $join = $criteria['join'];
            unset($criteria['join']);
            $sql .= $this->getJoinSQL($join);
        }

        $filters = $this->getFilterSQL($criteria);

        if (strpos($filters, 'content_type_name') !== false) {
            $this->parseContentTypeName($filters);
        }

        if ($this->hasCategoryFilter($filters)) {
            $this->parseCategory($filters);
        }

        if ($this->hasTagFilter($filters)) {
            $this->parseTag($filters);
        }

        $sql .= " WHERE " . $filters;

        $limitSQL = $this->getLimitSQL($elementsPerPage, $page, $offset);

        $orderBySQL = empty($order)
            ? '`pk_content` ASC'
            : $this->getOrderBySQL($order);

        $sql .= " ORDER BY $orderBySQL $limitSQL";

        $rs  = $this->dbConn->fetchAll($sql);
        $ids = [];

        foreach ($rs as $item) {
            $ids[] = [ $item['content_type_name'], $item['pk_content'] ];
        }

        return $this->findMulti($ids);
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

        $sql = 'SELECT COUNT(*) FROM ' . $fromSQL . ' ';

        if (is_array($criteria) && array_key_exists('join', $criteria)) {
            $join = $criteria['join'];
            unset($criteria['join']);
            $sql .= $this->getJoinSQL($join);
        }

        $filters = $this->getFilterSQL($criteria);

        if (strpos($filters, 'content_type_name') !== false) {
            $this->parseContentTypeName($filters);
        }

        if ($this->hasCategoryFilter($filters)) {
            $this->parseCategory($filters);
        }

        $sql .= " WHERE " . $filters;

        $rs = $this->dbConn->fetchArray($sql);

        if (!$rs) {
            return 0;
        }

        return $rs[0];
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
     * Checks if there is a category condition in filters
     *
     * @param string $filters The filters.
     *
     * @return bool True if there is a category condition. False otherwise.
     */
    protected function hasCategoryFilter($filters)
    {
        return strpos($filters, 'category_id') !== false;
    }

    /**
     * Checks if there is a tag condition in filters
     *
     * @param string $filters The filters.
     *
     * @return bool True if there is a tag condition. False otherwise.
     */
    protected function hasTagFilter($filters)
    {
        return strpos($filters, 'tag_id') !== false;
    }

    /**
     * Parse category condition and transforms it in a condition with a subquery
     * in the content_category table.
     *
     * @param string $filters The filters to parse.
     */
    protected function parseCategory(string &$filters)
    {
        $pattern = '/category_id\s*=\s*[\'"]{0,1}[0-9]+[\'"]{0,1}'
            . '|category_id\s*((not|NOT)\s+)?(in|IN)\s*\([\'"0-9, ]+\)/';

        preg_match_all($pattern, $filters, $matches);

        foreach ($matches[0] as $match) {
            $filters = str_replace(
                $match,
                sprintf(
                    'pk_content IN (SELECT content_id FROM content_category WHERE %s)',
                    $match
                ),
                $filters
            );
        }
    }

    /**
     * Parse tag condition and transforms it in a condition with a subquery
     * in the contents_tags table.
     *
     * @param string $filters The filters to parse.
     */
    protected function parseTag(string &$filters)
    {
        $pattern = '/tag_id\s*=\s*[\'"]{0,1}[0-9]+[\'"]{0,1}'
            . '|tag_id\s*((not|NOT)\s+)?(in|IN)\s*\([\'"0-9, ]+\)/';

        preg_match_all($pattern, $filters, $matches);

        foreach ($matches[0] as $match) {
            $filters = str_replace(
                $match,
                sprintf(
                    'pk_content IN (SELECT content_id FROM contents_tags WHERE %s)',
                    $match
                ),
                $filters
            );
        }
    }

    /**
     *  Replace criterias with content_type_name for fk_content_type
     *
     *   @param String $criterias The criteria used to search.
     */
    protected function parseContentTypeName(&$criterias)
    {
        preg_match_all(
            "/content_type_name\s*=\s*'{1}([A-Za-z0-9 ]*)'{1}|content_type_name\s*=\s*\"{1}([A-Za-z0-9 ]*)\"{1}/",
            $criterias,
            $result
        );

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
     *
     * @return array
     */
    private function loadExtraDataToContents($saveInCacheIds, &$contents)
    {
        if (empty($saveInCacheIds)
            || in_array(\underscore(array_values($contents)[0]->content_type_name), self::ORM_CONTENT_TYPES)
        ) {
            return [];
        }

        $tagsByContent    = array_values($contents)[0]->getContentTags(array_values($saveInCacheIds));
        $contentsForCache = [];
        foreach ($saveInCacheIds as $cacheKey => $contentId) {
            if (array_key_exists($contentId, $tagsByContent)) {
                $contents[$cacheKey]->tags = $tagsByContent[$contentId];
            } else {
                $contents[$cacheKey]->tags = [];
            }

            if (!in_array(\underscore($contents[$cacheKey]->content_type_name), self::ORM_CONTENT_TYPES)) {
                $contentsForCache[$cacheKey] = $contents[$cacheKey];
            }
        }

        return $contentsForCache;
    }
}
