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
class UserGroupManager extends BaseManager
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
     * Counts searched users given a criteria.
     *
     * @param array $criteria The criteria used to search the comments.
     *
     * @return integer The amount of elements.
     */
    public function countBy($criteria)
    {
        // Building the SQL filter
        $whereSQL  = $this->getFilterSQL($criteria);

        // Executing the SQL
        $sql = "SELECT COUNT(pk_user_group) FROM `user_groups` WHERE $whereSQL";

        $this->dbConn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $this->dbConn->fetchArray($sql);

        if (!$rs) {
            return 0;
        }

        return $rs[0];
    }

    /**
     * Finds one usergroup from the given a user id.
     *
     * @param integer $id User group id.
     *
     * @return UserGroup The matched user group.
     */
    public function find($id)
    {
        $entity = null;

        $cacheId = "usergroup" . $this->cacheSeparator . $id;

        if (!$this->hasCache()
            || ($entity = $this->cache->fetch($cacheId)) === false
            || !is_object($entity)
        ) {
            $entity = new \UserGroup($id);

            if ($this->hasCache()) {
                $this->cache->save($cacheId, $entity);
            }
        }

        return $entity;
    }

    /**
     * Searches for users given a criteria
     *
     * @param array   $criteria        The criteria used to search the comments.
     * @param array   $order           The order applied in the search.
     * @param integer $elementsPerPage The max number of elements to return.
     * @param integer $page            The offset to start with.
     *
     * @return array The matched elements.
     */
    public function findBy($criteria, $order, $elementsPerPage = null, $page = null)
    {
        // Building the SQL filter
        $whereSQL = $this->getFilterSQL($criteria);

        $orderSQL = '`pk_user_group` DESC';
        if (!empty($order)) {
            $orderSQL = $this->getOrderBySQL($order);
        }
        $limitSQL = $this->getLimitSQL($elementsPerPage, $page);

        // Executing the SQL
        $sql = "SELECT pk_user_group FROM `user_groups` WHERE $whereSQL ORDER BY $orderSQL $limitSQL";

        $this->dbConn->setFetchMode(ADODB_FETCH_ASSOC);
        $rs = $this->dbConn->fetchAll($sql);

        $ids = array();
        foreach ($rs as $resultElement) {
            $ids[] = $resultElement['pk_user_group'];
        }

        $groups = $this->findMulti($ids);

        return $groups;
    }

    /**
     * Find multiple users from a given array of user groups ids.
     *
     * @param array $data Array of preprocessed user groups ids.
     *
     * @return array Array of user groups.
     */
    public function findMulti(array $data)
    {
        $ids = array();
        $keys = array();
        foreach ($data as $value) {
            $ids[] = 'usergroup' . $this->cacheSeparator . $value;
            $keys[] = $value;
        }

        $groups = array_values($this->cache->fetch($ids));

        $cachedIds = array();

        foreach ($groups as $group) {
            $cachedIds[] = 'usergroup' . $this->cacheSeparator . $group->id;
        }

        $missedIds = array_diff($ids, $cachedIds);

        foreach ($missedIds as $content) {
            list($contentType, $contentId) = explode($this->cacheSeparator, $content);
            $group = $this->find($contentId);
            $groups[] = $group;

        }

        $ordered = array();
        foreach ($keys as $id) {
            $i = 0;
            while ($i < count($groups) && $groups[$i]->id != $id) {
                $i++;
            }

            if ($i < count($groups)) {
                $ordered[] = $groups[$i];
            }
        }

        return array_values($ordered);
    }

    /**
     * Deletes a usergroup from database and cache.
     *
     * @param integer $id User id.
     */
    public function delete($id)
    {
        $this->dbConn->transactional(function ($em) use ($id) {
            $em->executeQuery('DELETE FROM `user_groups` WHERE `pk_user_group`= ' . $id);
        });

        $this->cache->delete('usergroup' . $this->cacheSeparator . $id);
    }
}
