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
 * Handles common actions in UserGroups
 *
 * @package Repository
 **/
class UserGroupManager extends BaseManager
{
    public function find($id)
    {
        $group = null;

        $cacheId = $this->cachePrefix . "_usergroup_" . $id.microtime(true);

        if (!$this->hasCache()
            || ($group = $this->cache->fetch($cacheId)) === false
            || !is_object($group)
        ) {
            $group = new \UserGroup($id);

            if ($this->hasCache()) {
                $this->cache->save($cacheId, $group);
            }
        }

        return $group;
    }

    /**
     * Searches for groups given a criteria
     *
     * @param array $filter          the criteria used to search the groups
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

        $orderBySQL  = '`pk_user_group` DESC';
        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }
        $limitSQL   = $this->getLimitSQL($elementsPerPage, $page);

        // Executing the SQL
        $sql = "SELECT * FROM `user_groups` WHERE $filterSQL ORDER BY $orderBySQL $limitSQL";
        $this->dbConn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $this->dbConn->Execute($sql);

        if ($rs === false) {
            return false;
        }

        $userGroups = array();
        while (!$rs->EOF) {
            $userGroup = new \UserGroup();
            $userGroup->load($rs->fields);

            $userGroups []= $userGroup;
            $rs->MoveNext();
        }

        // Load privileges for these matched groups
        $userGroupIds = array_map(
            function ($userGroup) {
                return $userGroup->id;
            },
            $userGroups
        );
        $sql =  "SELECT pk_fk_user_group, pk_fk_privilege"
                ." FROM user_groups_privileges"
                ." WHERE pk_fk_user_group IN (".implode(',', $userGroupIds).")";

        $rs = $this->dbConn->Execute($sql);
        $privileges = $rs->getArray();
        if (!$rs) {
            return;
        }

        foreach ($privileges as $privilege) {
            foreach ($userGroups as &$userGroup) {

                if ($privilege['pk_fk_user_group'] == $userGroup->id) {
                    $userGroup->privileges[] = $privilege['pk_fk_privilege'];
                }
            }
        }

        return $userGroups;
    }
}
