<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Database\Persister;

use Common\ORM\Core\Entity;

/**
 * The BasePersister class defines actions to persist UserGroups.
 */
class UserGroupPersister extends BasePersister
{
    /**
     * {@inheritdoc}
     */
    public function create(Entity &$entity)
    {
        $privileges = [];

        if (!empty($entity->privileges)) {
            $privileges = $entity->privileges;
            unset($entity->privileges);
        }

        parent::create($entity);

        $id = $this->metadata->getId($entity);

        $this->persistPrivileges($id, $privileges);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Entity $entity)
    {
        $privileges = [];

        if (!empty($entity->privileges)) {
            $privileges = $entity->privileges;
            unset($entity->privileges);
        }

        parent::update($entity);

        $id = $this->metadata->getId($entity);

        $this->persistPrivileges($id, $privileges);
    }

    /**
     * Persits the user group privileges.
     *
     * @param integer $id         The entity id.
     * @param array   $privileges The user group privileges.
     */
    protected function persistPrivileges($id, $privileges)
    {
        // Ignore metas with value = null
        if (!empty($privileges)) {
            $privileges = array_filter($privileges, function ($a) {
                return !is_null($a);
            });
        }

        // Update privileges
        $this->savePrivileges($id, $privileges);

        // Remove old privileges
        $this->removePrivileges($id, array_values($privileges));
    }

    /**
     * Deletes old privileges.
     *
     * @param array $id         The entity id.
     * @param array $privileges The privileges keys to keep.
     */
    protected function removePrivileges($id, $keep)
    {
        $sql      = "delete from user_group_privileges where user_group_id = ?";
        $params[] = $id['id'];
        $types[]  = is_string($id['id']) ?
            \PDO::PARAM_STR : \PDO::PARAM_INT;

        if (!empty($keep)) {
            $sql .= " and privilege not in (?)";
            $params[] = $keep;
            $types[]  = \Doctrine\DBAL\Connection::PARAM_STR_ARRAY;
        }

        $this->conn->executeQuery($sql, $params, $types);
    }

    /**
     * Saves new privileges.
     *
     * @param array $id         The entity id.
     * @param array $privileges The privileges to save.
     */
    protected function savePrivileges($id, $privileges)
    {
        if (empty($privileges)) {
            return;
        }

        $sql = "replace into user_group_privileges values "
            . str_repeat(
                '(?,?),',
                count($privileges)
            );

        $sql    = rtrim($sql, ',');
        $params = [];
        $types  = [];
        foreach ($privileges as $value) {
            $params = array_merge(
                $params,
                array_merge(array_values($id), [ $value ])
            );

            $types = array_merge(
                $types,
                [ \PDO::PARAM_INT, \PDO::PARAM_INT ]
            );
        }

        $this->conn->executeQuery($sql, $params, $types);
    }
}
