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
 * The ManagerUserPersister class defines actions to persist Users.
 */
class ManagerUserPersister extends BasePersister
{
    /**
     * {@inheritdoc}
     */
    public function create(Entity &$entity)
    {
        $instances  = [];
        $userGroups = [];

        if (!empty($entity->instances)) {
            $instances = $entity->instances;
            unset($entity->instances);
        }

        if (!empty($entity->user_groups)) {
            $userGroups = $entity->user_groups;
            unset($entity->user_groups);
        }

        parent::create($entity);

        $id = $this->metadata->getId($entity);

        $this->persistUserGroups($id, $userGroups);

        $entity->instances   = $instances;
        $entity->user_groups = $userGroups;

        $entity->refresh();
    }

    /**
     * {@inheritdoc}
     */
    public function update(Entity $entity)
    {
        $changes    = $entity->getChanges();
        $instances  = [];
        $userGroups = $entity->user_groups;

        // Instances change
        if (array_key_exists('instances', $changes)) {
            $instances = $changes['instances'];
        }

        // User groups change
        if (array_key_exists('user_groups', $changes)) {
            $userGroups = $changes['user_groups'];
        }

        // Ignore instances, persist them later
        unset($entity->instances);
        $entity->setNotStored('instances');

        // Ignore user groups, persist them later
        unset($entity->user_groups);
        $entity->setNotStored('user_groups');

        parent::update($entity);

        $id = $this->metadata->getId($entity);

        if (array_key_exists('user_groups', $changes)) {
            $this->persistUserGroups($id, $userGroups);
        }

        $entity->instances   = $instances;
        $entity->user_groups = $userGroups;

        if ($this->hasCache()) {
            $this->cache->remove($this->metadata->getPrefixedId($entity));
        }
    }

    /**
     * Persits the user user groups.
     *
     * @param integer $id         The entity id.
     * @param array   $userGroups The list of user groups.
     */
    protected function persistUserGroups($id, $userGroups)
    {
        // Ignore user groups with value = null
        if (!empty($userGroups)) {
            $userGroups = array_filter($userGroups, function ($a) {
                return !is_null($a);
            });
        }

        // Update user groups
        $this->saveUserGroups($id, $userGroups);

        // Remove old user groups
        $this->removeUserGroups($id, array_map(function ($a) {
            return $a['user_group_id'];
        }, $userGroups));
    }

    /**
     * Deletes old user groups.
     *
     * @param array $id   The entity id.
     * @param array $keep The list of user group ids to keep.
     */
    protected function removeUserGroups($id, $keep = [])
    {
        $sql      = "delete from user_user_group where user_id = ?";
        $params[] = $id['id'];
        $types[]  = is_string($id['id']) ?
            \PDO::PARAM_STR : \PDO::PARAM_INT;

        if (!empty($keep)) {
            $sql .= " and user_group_id not in (?)";

            $params[] = $keep;
            $types[]  = \Doctrine\DBAL\Connection::PARAM_STR_ARRAY;
        }

        $this->conn->executeQuery($sql, $params, $types);
    }

    /**
     * Saves user groups.
     *
     * @param array $id         The entity id.
     * @param array $userGroups The list of user groups to save.
     */
    protected function saveUserGroups($id, $userGroups)
    {
        if (empty($userGroups)) {
            return;
        }

        $sql = "replace into user_user_group values "
            . str_repeat(
                '(?,?,?,?),',
                count($userGroups)
            );

        $sql    = rtrim($sql, ',');
        $params = [];
        $types  = [];
        foreach ($userGroups as $value) {
            $params = array_merge(
                $params,
                array_merge(array_values($id), [
                    $value['user_group_id'],
                    $value['status'],
                    empty($value['expires']) ? null : $value['expires']
                ])
            );

            $types = array_merge(
                $types,
                [ \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT ]
            );
        }

        $this->conn->executeQuery($sql, $params, $types);
    }
}
