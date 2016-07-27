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
 * The BasePersister class defines actions to persist User.
 */
class UserPersister extends BasePersister
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

        parent::create($entity);

        $id = $this->metadata->getId($entity);

        $this->persistCategories($id, $categories);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Entity $entity)
    {
        $categories = [];

        if (!empty($entity->categories)) {
            $categories = $entity->categories;
            unset($entity->categories);
        }

        parent::update($entity);

        $id = $this->metadata->getId($entity);

        try {
            $this->persistCategories($id, $categories);
        } catch (\Exception $e) {
        }
    }

    /**
     * Persits the user group categories.
     *
     * @param integer $id         The entity id.
     * @param array   $categories The user group categories.
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
     * Deletes old categories.
     *
     * @param array $id         The entity id.
     * @param array $categories The categories keys to keep.
     */
    protected function removeCategories($id, $keep)
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
     * Saves new categories.
     *
     * @param array $id         The entity id.
     * @param array $categories The categories to save.
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
}
