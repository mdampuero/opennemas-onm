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
     * Returns a list where key is the user group id and value is the number of
     * users assigned to the category.
     *
     * @param mixed $ids A user group id or a list of user group ids.
     *
     * @return array The list where keys are the user group ids and values are
     *               the number of users.
     */
    public function countUsers($ids)
    {
        if (empty($ids)) {
            throw new \InvalidArgumentException();
        }

        if (!is_array($ids)) {
            $ids = [ $ids ];
        }

        $sql = 'SELECT user_group_id AS "id", COUNT(1) AS "users" '
            . 'FROM user_user_group '
            . 'LEFT JOIN users ON user_id = id '
            . 'WHERE user_group_id IN (?) AND activated = 1 '
            . 'GROUP BY user_group_id';

        $data = $this->conn->fetchAll(
            $sql,
            [ $ids ],
            [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );

        $contents = [];

        foreach ($data as $value) {
            $contents[$value['id']] = $value['users'];
        }

        return $contents;
    }

    /**
     * Returns a list of activated users assigned to a user group id or a list
     * of user group ids.
     *
     * @param mixed $ids A user group id or a list of user group ids.
     *
     * @return array The list of activated users.
     */
    public function findUsers($ids)
    {
        if (empty($ids)) {
            throw new \InvalidArgumentException();
        }

        if (!is_array($ids)) {
            $ids = [ $ids ];
        }

        $sql = 'SELECT id, name, email FROM users'
            . ' LEFT JOIN user_user_group ON user_id = id'
            . ' WHERE user_group_id IN (?) AND activated = 1';

        return $this->conn->fetchAll(
            $sql,
            [ $ids ],
            [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function refresh($ids)
    {
        $entities   = parent::refresh($ids);
        $privileges = $this->getPrivileges($ids);

        foreach ($entities as $key => &$value) {
            $value->privileges = [];

            if (array_key_exists($key, $privileges)) {
                $value->privileges = $privileges[$key];
            }

            $value->refresh();
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
