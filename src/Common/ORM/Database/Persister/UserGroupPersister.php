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
 * The UserGroupPersister class defines actions to persist UserGroups.
 */
class UserGroupPersister extends BasePersister
{
    /**
     * {@inheritdoc}
     */
    public function create(Entity &$entity)
    {
        $changes    = $entity->getChanges();
        $privileges = [];

        // Privileges change
        if (array_key_exists('privileges', $changes)) {
            $privileges = $changes['privileges'];
        }

        if (!empty($entity->privileges)) {
            $privileges = $entity->privileges;
            unset($entity->privileges);
        }

        // Ignore privileges, persist them later
        unset($entity->privileges);
        $entity->setNotStored('privileges');

        parent::create($entity);

        $id = $this->metadata->getId($entity);

        $this->persistPrivileges($id, $privileges);
        $entity->privileges = $privileges;

        $entity->refresh();
    }

    /**
     * {@inheritdoc}
     */
    public function update(Entity $entity)
    {
        $changes    = $entity->getChanges();
        $privileges = [];

        // Privileges change
        if (array_key_exists('privileges', $changes)) {
            $privileges = $changes['privileges'];
        }

        unset($entity->privileges);
        $entity->setNotStored('privileges');

        parent::update($entity);

        $id = $this->metadata->getId($entity);

        if (array_key_exists('privileges', $changes)) {
            $this->persistPrivileges($id, $privileges);

            if ($this->hasCache()) {
                $this->cache->remove($this->metadata->getPrefixedId($entity));
            }
        }

        $entity->privileges = $privileges;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Entity $entity)
    {
        parent::remove($entity);

        $id = $this->metadata->getId($entity);

        $this->removePrivileges($id);

        if ($this->hasCache()) {
            $this->cache->remove($this->metadata->getPrefixedId($entity));
            $this->cache->removeByPattern('*user*');
        }
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
    protected function removePrivileges($id, $keep = [])
    {
        $sql      = "delete from user_groups_privileges where pk_fk_user_group = ?";
        $params[] = $id['pk_user_group'];
        $types[]  = is_string($id['pk_user_group']) ?
            \PDO::PARAM_STR : \PDO::PARAM_INT;

        if (!empty($keep)) {
            $sql     .= " and pk_fk_privilege not in (?)";
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

        $sql = "replace into user_groups_privileges values "
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
