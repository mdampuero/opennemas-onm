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
 * The InstanceUserPersister class defines actions to persist Users.
 */
class InstanceUserPersister extends BasePersister
{
    /**
     * {@inheritdoc}
     */
    public function create(Entity &$entity)
    {
        $categories = [];

        if (!empty($entity->categories)) {
            $categories = $entity->categories;
            unset($entity->categories);
        }

        if (!empty($entity->user_groups)) {
            $userGroups = $entity->user_groups;
            unset($entity->user_groups);
        }

        parent::create($entity);

        $id = $this->metadata->getId($entity);

        $this->persistCategories($id, $categories);
        $this->persistUserGroups($id, $userGroups);

        $entity->categories  = $categories;
        $entity->user_groups = $userGroups;

        $entity->refresh();
    }

    /**
     * {@inheritdoc}
     */
    public function update(Entity $entity)
    {
        $changes    = $entity->getChanges();
        $categories = $entity->categories;
        $userGroups = $entity->user_groups;

        // Categories change
        if (array_key_exists('categories', $changes)) {
            $categories = $changes['categories'];
        }

        // User groups change
        if (array_key_exists('user_groups', $changes)) {
            $userGroups = $changes['user_groups'];
        }

        // Ignore categories, persist them later
        unset($entity->categories);
        $entity->setNotStored('categories');

        // Ignore user groups, persist them later
        unset($entity->user_groups);
        $entity->setNotStored('user_groups');

        parent::update($entity);

        $id = $this->metadata->getId($entity);

        if (array_key_exists('categories', $changes)) {
            $this->persistCategories($id, $categories);
        }

        if (array_key_exists('user_groups', $changes)) {
            $this->persistUserGroups($id, $userGroups);
        }

        $entity->categories  = $categories;
        $entity->user_groups = $userGroups;

        if ($this->hasCache()) {
            $this->cache->remove($this->metadata->getPrefixedId($entity));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Entity $entity)
    {
        parent::remove($entity);

        $id = $this->metadata->getId($entity);

        $this->removeCategories($id);

        if ($this->hasCache()) {
            $this->cache->remove($this->metadata->getPrefixedId($entity));
        }
    }

    /**
     * Persits the user categories.
     *
     * @param integer $id         The entity id.
     * @param array   $categories The list of category ids.
     */
    protected function persistCategories($id, $categories)
    {
        // Ignore metas with value = null
        if (!empty($categories)) {
            $categories = array_filter($categories, function ($a) {
                return !is_null($a);
            });
        }

        // Update categories
        $this->saveCategories($id, $categories);

        // Remove old categories
        $this->removeCategories($id, array_values($categories));
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
     * Deletes old categories.
     *
     * @param array $id   The entity id.
     * @param array $keep The list of category ids to keep.
     */
    protected function removeCategories($id, $keep = [])
    {
        $sql      = "delete from users_content_categories where pk_fk_user = ?";
        $params[] = $id['id'];
        $types[]  = is_string($id['id']) ?
            \PDO::PARAM_STR : \PDO::PARAM_INT;

        if (!empty($keep)) {
            $sql .= " and pk_fk_content_category not in (?)";

            $params[] = $keep;
            $types[]  = \Doctrine\DBAL\Connection::PARAM_STR_ARRAY;
        }

        $this->conn->executeQuery($sql, $params, $types);
    }

    /**
     * Deletes old categories.
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
     * Saves new categories.
     *
     * @param array $id         The entity id.
     * @param array $categories The list of category ids to save.
     */
    protected function saveCategories($id, $categories)
    {
        if (empty($categories)) {
            return;
        }

        $sql = "replace into users_content_categories values "
            . str_repeat(
                '(?,?),',
                count($categories)
            );

        $sql    = rtrim($sql, ',');
        $params = [];
        $types  = [];
        foreach ($categories as $value) {
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

    /**
     * Saves user groups.
     *
     * @param array $id         The entity id.
     * @param array $userGroups The list of category ids to save.
     */
    protected function saveUserGroups($id, $userGroups)
    {
        if (empty($userGroups)) {
            return;
        }

        $sql = "replace into user_user_group values "
            . str_repeat(
                '(?,?,?),',
                count($userGroups)
            );

        $sql    = rtrim($sql, ',');
        $params = [];
        $types  = [];
        foreach ($userGroups as $value) {
            $params = array_merge(
                $params,
                array_merge(
                    array_values($id),
                    [ $value['user_group_id'], $value['status'] ]
                )
            );

            $types = array_merge(
                $types,
                [ \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT ]
            );
        }

        $this->conn->executeQuery($sql, $params, $types);
    }
}
