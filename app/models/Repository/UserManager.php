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
     * @param CacheInterface $cache The cache instance.
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
     * @param  array|string $criteria The criteria used to search the comments.
     * @return integer                The amount of elements.
     */
    public function countBy($criteria)
    {
        // Building the SQL filter
        $whereSQL  = $this->getFilterSQL($criteria);

        // Executing the SQL
        $sql = "SELECT COUNT(id) FROM `users` WHERE $whereSQL";

        $this->dbConn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $this->dbConn->fetchArray($sql);

        if (!$rs) {
            return 0;
        }

        return $rs[0];
    }

    /**
     * Finds one user from the given a user id.
     *
     * @param  integer $id User id.
     * @return User
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
     * @param  array|string $criteria        The criteria used to search.
     * @param  array        $order           The order applied in the search.
     * @param  integer      $elementsPerPage The max number of elements.
     * @param  integer      $page            The offset to start with.
     * @return array                         The matched elements.
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

        $this->dbConn->setFetchMode(ADODB_FETCH_ASSOC);
        $rs = $this->dbConn->fetchAll($sql);

        $ids = array();
        foreach ($rs as $resultElement) {
            $ids[] = $resultElement['id'];
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
     * Deletes a user and its metas.
     *
     * @param integer $id User id.
     */
    public function delete($id)
    {
        $this->dbConn->transactional(function ($em) use ($id) {
            $em->executeQuery('DELETE FROM `users` WHERE `id`= ' . $id);
            $em->executeQuery('DELETE FROM `usermeta` WHERE `user_id`= ' . $id);
        });

        $this->cache->delete('user' . $this->cacheSeparator . $id);
    }
}
