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

class ManagerUserRepository extends BaseRepository
{
    /**
     * {@inheritdoc}
     */
    protected function refresh($ids)
    {
        $entities  = parent::refresh($ids);
        $instances = $this->getInstances($ids);

        foreach ($entities as $key => &$value) {
            $value->instances = [];

            if (array_key_exists($key, $instances)) {
                $value->instances = $instances[$key];
            }

            $value->refresh();
        }

        return $entities;
    }

    /**
     * Returns an array of instances grouped by entity id.
     *
     * @param array $ids The entity ids.
     *
     * @return array The array of instances.
     */
    protected function getInstances($ids)
    {
        $filters  = [];

        foreach ($ids as $id) {
            $filters[] = 'owner_id=' . $id['id'];
        }

        $sql = 'select id, internal_name, owner_id from instances where '
            . implode(' or ', $filters);

        $rs = $this->conn->fetchAll($sql);

        $instances = [];
        foreach ($rs as $value) {
            $instances[$value['owner_id']][] =
                $value['internal_name'];
        }

        return $instances;
    }
}
