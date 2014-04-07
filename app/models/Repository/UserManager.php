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
 * Handles common operations with users
 *
 * @package Repository
 */
class UserManager extends BaseManager
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
     * @param  integer $id Menu id
     * @return Menu
     */
    public function find($id)
    {
        $entity = null;

        $cacheId = "user_" . $id;

        if (!$this->hasCache()
            || ($entity = $this->cache->fetch($cacheId)) === false
            || !is_object($entity)
        ) {
            $entity = new \User($id);

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
        $keys = array();
        $ordered = array();

        $ids = array();
        $i = 0;
        foreach ($data as $value) {
            $ids[] = 'user_'.$value;
            $keys[$value] = $i++;
        }

        $users = $this->cache->fetch($ids);

        $cachedIds = array();
        foreach ($users as $user) {
            $ordered[$keys[$user->id]] = $user;
            $cachedIds[] = 'user_'.$user->id;
        }

        $missedIds = array_diff($ids, $cachedIds);

        foreach ($missedIds as $user) {
            list($contentType, $contentId) = explode('_', $user);

            $user = $this->find($contentId);
            if ($user->id) {
                $ordered[$keys[$user->id]] = $user;
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

        $this->cache->delete('user_' . $id);
    }

    /**
     * Builds the SQL WHERE filter given an array or string with the desired filter
     *
     * @param string|array $filter the filter params
     *
     * @return string the SQL WHERE filter
     */
    protected function getFilterSQL($filters)
    {
        if (empty($filters)) {
            $filterSQL = ' 1=1 ';
        } elseif (is_array($filters)) {
            $filterSQL = array();

            foreach ($filters as $field => $values) {
                $fieldFilters = array();

                foreach ($values as $filter) {
                    $operator = "=";
                    $value    = "";
                    if ($filter['value'][0] == '%'
                        && $filter['value'][strlen($filter['value']) - 1] == '%'
                    ) {
                        $operator = "LIKE";
                    }

                    // Check operator
                    if (array_key_exists('operator', $filter)) {
                        $operator = $filter['operator'];
                    }

                    // Check value
                    if (array_key_exists('value', $filter)) {
                        $value = $filter['value'];
                    }

                    $fieldFilters[] = "`$field` $operator '$value'";
                }

                // Add filters for the current $field
                $filterSQL[] = implode(' OR ', $fieldFilters);
            }

            // Build filters
            $filterSQL = implode(' AND ', $filterSQL);
        } else {
            $filterSQL = $filter;
        }

        return $filterSQL;
    }
}
