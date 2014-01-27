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

/**
 * Handles common operations with users
 *
 * @package Repository
 */
class UserManager extends BaseManager
{
    /**
     * {@inherit_doc}
     **/
    public function find($id)
    {
        $user = null;

        // $cacheId = $this->cachePrefix . "_user_" . $id.microtime(true);

        // if (!$this->hasCache()
        //     || ($user = $this->cache->fetch($cacheId)) === false
        //     || !is_object($user)
        // ) {
        $user = new \User($id);

            // if ($this->hasCache()) {
            //     $this->cache->save($cacheId, $user);
            // }
        // }

        return $user;
    }

    /**
     * Searches for users given a criteria
     *
     * @param array $criteria        the criteria used to search the users
     * @param array $order           the order applied in the search
     * @param int   $elementsPerPage the max number of elements to return
     * @param int   $page            the offset to start with
     *
     * @return array the matched elements
     **/
    public function findBy($criteria, $order = null, $elementsPerPage = null, $page = null)
    {
        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($criteria);

        $orderBySQL  = '`pk_user` DESC';
        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }
        $limitSQL   = $this->getLimitSQL($elementsPerPage, $page);

        // Executing the SQL
        $sql = "SELECT * FROM `users` WHERE $filterSQL ORDER BY $orderBySQL $limitSQL";

        $this->dbConn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $this->dbConn->Execute($sql);

        if ($rs === false) {
            return false;
        }

        $users = array();
        while (!$rs->EOF) {
            $user = new \User();
            $user->setValues($rs->fields);

            $users []= $user;
            $rs->MoveNext();
        }

        return $users;
    }

    /**
     * Returns the number of comments given a filter
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
        $sql = "SELECT count(id) FROM `users` WHERE $filterSQL";
        $rs = $this->dbConn->GetOne($sql);

        if ($rs === false) {
            return false;
        }

        return $rs;
    }
}
