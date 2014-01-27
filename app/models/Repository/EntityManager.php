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

        $this->dbConn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $this->dbConn->Execute($sql);

        if ($rs === false) {
            return false;
        }

        $contents = array();
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
        $rs = $this->dbConn->Execute($sql, $value);

        if ($rs) {
            $contentID = $rs->fields['pk_content'];
            $this->cache->save($cacheKey, $contentID);
        }

        return $contentID;
    }
}
