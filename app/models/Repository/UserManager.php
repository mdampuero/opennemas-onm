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
class UserManager extends BaseManager
{
    /**
     * Initializes the entity manager.
     *
     * @param DbalWrapper    $conn      The custom DBAL wrapper.
     * @param CacheInterface $cache       The cache instance.
     * @param string         $cachePrefix The cache prefix.
     */
    public function __construct(DbalWrapper $conn, CacheInterface $cache, $cachePrefix)
    {
        $this->conn        = $conn;
        $this->cache       = $cache;
        $this->cachePrefix = $cachePrefix;
    }

    /**
     * Counts searched users given a criteria.
     *
     * @param array $criteria The criteria used to search the users.
     *
     * @return integer The amount of elements.
     */
    public function countBy($criteria)
    {
        // Building the SQL filter
        $whereSQL  = $this->getFilterSQL($criteria);

        // Executing the SQL
        $sql = "SELECT COUNT(id) FROM `users` WHERE $whereSQL";

        $this->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $this->conn->fetchArray($sql);

        if (!$rs) {
            return 0;
        }

        return $rs[0];
    }

    /**
     * Finds one user from the given a user id.
     *
     * @param integer $id User id.
     *
     * @return User The matched user.
     */
    public function find($id)
    {
        $entity = null;

        $cacheId = "user" . $this->cacheSeparator . $id;

        if (!$this->hasCache()
            || ($entity = $this->cache->fetch($cacheId)) === false
            || !is_object($entity)
        ) {
            $entity = new \User($id);

            if ($entity->id == null) {
                return null;
            }

            if ($this->hasCache()) {
                $this->cache->save($cacheId, $entity);
            }
        }

        return $entity;
    }

    /**
     * Searches for users given a criteria.
     *
     * @param array   $criteria        The criteria used to search.
     * @param array   $order           The order applied in the search.
     * @param integer $elementsPerPage The max number of elements.
     * @param integer $page            The offset to start with.
     *
     * @return array The matched elements.
     */
    public function findBy($criteria, $order, $elementsPerPage = null, $page = null)
    {
        // Building the SQL filter
        $whereSQL = $this->getFilterSQL($criteria);

        $orderSQL = '`id` DESC';
        if (!empty($order)) {
            $orderSQL = $this->getOrderBySQL($order);
        }
        $limitSQL = $this->getLimitSQL($elementsPerPage, $page);

        // Executing the SQL
        $sql = "SELECT id FROM `users` WHERE $whereSQL ORDER BY $orderSQL $limitSQL";

        $this->conn->setFetchMode(ADODB_FETCH_ASSOC);

        $rs = $this->conn->fetchAll($sql);

        $ids = array();
        foreach ($rs as $resultElement) {
            $ids[] = $resultElement['id'];
        }

        $users = $this->findMulti($ids);

        return $users;
    }

    /**
     * Searches for users given a criteria.
     *
     * @param array   $criteria        The criteria used to search.
     * @param array   $order           The order applied in the search.
     * @param integer $elementsPerPage The max number of elements.
     * @param integer $page            The offset to start with.
     *
     * @return array The matched elements.
     */
    public function findByUserMeta($criteria, $order, $elementsPerPage = null, $page = null)
    {
        // Building the SQL filter
        $whereSQL = $this->getFilterSQL($criteria);

        $orderSQL = '`id` DESC';
        if (!empty($order)) {
            $orderSQL = $this->getOrderBySQL($order);
        }
        $limitSQL = $this->getLimitSQL($elementsPerPage, $page);

        // Executing the SQL
        $sql = "SELECT id FROM `users`,`usermeta` "
            . "WHERE `users`.`id`=`usermeta`.`user_id` AND $whereSQL "
            . "ORDER BY $orderSQL $limitSQL";

        $this->conn->setFetchMode(ADODB_FETCH_ASSOC);
        $rs = $this->conn->fetchAll($sql);

        $ids = array();
        foreach ($rs as $resultElement) {
            $ids[] = $resultElement['id'];
        }

        $users = $this->findMulti($ids);

        return $users;
    }

    /**
     * Find multiple users from a given array of user ids.
     *
     * @param array $data Array of preprocessed user ids.
     *
     * @return array Array of users.
     */
    public function findMulti(array $data)
    {
        $ids = array();
        $keys = array();
        foreach ($data as $value) {
            $ids[] = 'user' . $this->cacheSeparator . $value;
            $keys[] = $value;
        }

        $users = array_values($this->cache->fetch($ids));

        $cachedIds = array();
        foreach ($users as $user) {
            $cachedIds[] = 'user' . $this->cacheSeparator . $user->id;
        }

        $missedIds = array_diff($ids, $cachedIds);

        foreach ($missedIds as $user) {
            list($contentType, $contentId) = explode($this->cacheSeparator, $user);

            $user = $this->find($contentId);
            if ($user && $user->id) {
                $users[] = $user;
            }
        }
        // Unused var $contentType
        unset($contentType);

        $ordered = array();
        foreach ($keys as $id) {
            $i = 0;
            while ($i < count($users) && $users[$i]->id != $id) {
                $i++;
            }

            if ($i < count($users)) {
                $ordered[] = $users[$i];
            }
        }

        return array_values($ordered);
    }

    /**
     * Deletes a user and its metas from database and cache.
     *
     * @param integer $id User id.
     */
    public function delete($id)
    {
        $this->conn->transactional(function ($em) use ($id) {
            $em->executeQuery('DELETE FROM `users` WHERE `id`= ' . $id);
            $em->executeQuery('DELETE FROM `usermeta` WHERE `user_id`= ' . $id);
        });

        $this->cache->delete('user' . $this->cacheSeparator . $id);
    }
}
