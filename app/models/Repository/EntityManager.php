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
     * @param  string  $contentType Content type name.
     * @param  integer $id          Content id
     * @return Content
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
     * @param  array $contentsData Array of preprocessed content ids.
     * @return array               Array of contents.
     */
    public function findMulti(array $data)
    {
        $keys = array();
        $ordered = array();

        $ids = array();
        $i = 0;
        foreach ($data as $value) {
            $ids[] = $value[0] . $this->cacheSeparator . $value[1];
            $keys[$value[1]] = $i++;
        }
        $contents = $this->cache->fetch($ids);

        $cachedIds = array();
        foreach ($contents as $content) {
            $ordered[$keys[$content->id]] = $content;
            $cachedIds[] = $content->content_type_name . $this->cacheSeparator.$content->id;
        }

        $missedIds = array_diff($ids, $cachedIds);

        foreach ($missedIds as $content) {
            list($contentType, $contentId) = explode($this->cacheSeparator, $content);

            $content = $this->find(\classify($contentType), $contentId);
            if ($content->id) {
                $ordered[$keys[$content->pk_content]] = $content;
            }
        }

        ksort($ordered);
        return array_values($ordered);
    }

    /**
     * Searches for content given a criteria
     *
     * @param  array|string $criteria        The criteria used to search.
     * @param  array        $order           The order applied in the search.
     * @param  integer      $elementsPerPage The max number of elements.
     * @param  integer      $page            The current page.
     * @param  integer      $offset          The offset to start with.
     * @return array                         The matched elements.
     */
    public function findBy($criteria, $order = null, $elementsPerPage = null, $page = null, $offset = 0)
    {
        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($criteria);

        $orderBySQL  = '`pk_content` DESC';
        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }
        $limitSQL = $this->getLimitSQL($elementsPerPage, $page, $offset);

        // Executing the SQL
        $sql = "SELECT content_type_name, pk_content FROM `contents` "
            ."WHERE $filterSQL ORDER BY $orderBySQL $limitSQL";

        // var_dump($sql);die();

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

    /**
     * Counts contents given a criteria.
     *
     * @param  array|string $criteria The criteria used to search.
     * @return integer                The number of found contents.
     */
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
}
