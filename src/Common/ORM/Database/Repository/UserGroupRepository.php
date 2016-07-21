<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Database\Repository;

class UserGroupRepository extends BaseRepository
{
    /**
     * {@inheritdoc}
     */
    protected function refresh($ids)
    {
        $entities   = parent::refresh($ids);
        $privileges = $this->getPrivileges($ids);

        foreach ($entities as $key => $value) {
            $entities[$key]->privileges = [];

            if (array_key_exists($key, $privileges)) {
                $entities[$key]->privileges = $privileges[$key];
            }
        }

        return $entities;
    }

    /**
     * Returns an array of privileges grouped by entity id.
     *
     * @param array $ids The entity ids.
     *
     * @return array The array of privileges.
     */
    protected function getPrivileges($ids)
    {
        $filters  = [];

        foreach ($ids as $id) {
            $filters[] = 'pk_fk_user_group=' . $id['pk_user_group'];
        }

        $sql = 'select * from user_groups_privileges where '
            . implode(' or ', $filters);

        $rs = $this->conn->fetchAll($sql);

        $privileges = [];
        foreach ($rs as $value) {
            $privileges[$value['pk_fk_user_group']][] = (int) $value['pk_fk_privilege'];
        }

        return $privileges;
    }
}
