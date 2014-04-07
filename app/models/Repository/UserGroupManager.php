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
 * Handles common actions in UserGroups
 *
 * @package Repository
 **/
class UserGroupManager extends BaseManager
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
     * Counts searched users given a criteria
     *
     * @param  array $criteria        the criteria used to search the comments.
     * @return int                    the amount of elements.
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
     * @param  integer $id Menu id
     * @return Menu
     */
    public function find($id)
    {
        $entity = null;

        $cacheId = "usergroup_" . $id;

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
     * @param  array $criteria        the criteria used to search the comments.
     * @param  array $order           the order applied in the search.
     * @param  int   $elementsPerPage the max number of elements to return.
     * @param  int   $page            the offset to start with.
     * @return array                  the matched elements.
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

        $users = $this->findMulti($ids);

        return $users;
    }

    /**
     * Find multiple users from a given array of content ids.
     *
     * @param  array $data Array of preprocessed content ids.
     * @return array       Array of contents.
     */
    public function findMulti(array $data)
    {
        $ordered = array_flip($data);

        $ids = array();
        foreach ($data as $value) {
            $ids[] = 'user_' . $value;
        }

        $users = array_values($this->cache->fetch($ids));

        $cachedIds = array();
        foreach ($users as $user) {
            $ordered[$user->id] = $user;
            $cachedIds[] = 'usergroup_' . $user->id;
        }

        $missedIds = array_diff($ids, $cachedIds);

        foreach ($missedIds as $content) {
            list($contentType, $contentId) = explode('_', $content);
            $user = $this->find($contentId);
            $ordered[$user->id] = $user;
        }

        return array_values($ordered);
    }

    /**
     * Deletes a usergroup
     *
     * @param integer $id User id.
     */
    public function delete($id)
    {
        $this->dbConn->transactional(function ($em) use ($id) {
            $em->executeQuery('DELETE FROM `user_groups` WHERE `pk_user_group`= ' . $id);
        });

        $this->cache->delete('usergroup_' . $id);
    }
}
