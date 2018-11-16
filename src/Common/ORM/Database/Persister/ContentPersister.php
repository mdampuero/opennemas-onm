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

use Common\Cache\Core\Cache;
use Common\ORM\Core\Connection;
use Common\ORM\Core\Entity;
use Common\ORM\Core\Metadata;
use Common\ORM\Entity\User;
use Common\ORM\Database\Data\Converter\BaseConverter;

/**
 * The InstancecontentPersister class defines actions to persist contents.
 */
class ContentPersister extends BasePersister
{
    /**
     * Initializes a new DatabasePersister.
     *
     * @param Connection $conn     The database connection.
     * @param Metadata   $metadata The entity metadata.
     * @param Cache      $cache    The cache service.
     */
    public function __construct(Connection $conn, Metadata $metadata, Cache $cache = null, User $user)
    {
        $this->cache     = $cache;
        $this->conn      = $conn;
        $this->converter = new BaseConverter($metadata);
        $this->metadata  = $metadata;
        $this->user      = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function create(Entity &$entity)
    {
        if (empty($entity->starttime)) {
            $entity->starttime = new \DateTime();
        }

        $entity->fk_fk_user_last_editor = $this->user->id;
        $entity->fk_publisher           = $this->user->id;

        $categories = [];
        if (!empty($entity->categories)) {
            $categories = $entity->categories;
            unset($entity->categories);
        }

        $tagIds = [];
        if (!empty($entity->tag_ids)) {
            $tagIds = $entity->tag_ids;
            unset($entity->tag_ids);
        }

        $relations = [];
        if (!empty($entity->relations)) {
            $tagIds = $entity->relations;
            unset($entity->relations);
        }

        parent::create($entity);

        $id = $this->metadata->getId($entity);

        $this->persistCategories($id, $categories);

        $entity->categories = $categories;

        $this->persistTags($id, $categories);

        $entity->tag_ids = $tagIds;

        $this->persistRelations($id, $relations);

        $entity->relations = $relations;

        $entity->refresh();
    }

    /**
     * {@inheritdoc}
     */
    public function update(Entity $entity)
    {
        $entity->fk_user_last_editor = $this->user->id;
        $entity->fk_publisher = $this->user->id;

        $changes    = $entity->getChanges();
        $categories = $entity->categories;
        $tagIds     = $entity->tag_ids;

        // Categories change
        if (array_key_exists('categories', $changes)) {
            $categories = $changes['categories'];
        }

        // Ignore categories, persist them later
        unset($entity->categories);
        $entity->setNotStored('categories');

        // Ignore tag_ids, persist them later
        unset($entity->tag_ids);
        $entity->setNotStored('tag_ids');

        parent::update($entity);

        $id = $this->metadata->getId($entity);

        if (array_key_exists('categories', $changes)) {
            $this->persistCategories($id, $categories);
        }

        $entity->categories = $categories;


        if (array_key_exists('tag_ids', $changes)) {
            $this->persistTags($id, $tagIds);
        }

        $entity->tag_ids = $tagIds;

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

        $this->removeTags($id);

        if ($this->hasCache()) {
            $this->cache->remove($this->metadata->getPrefixedId($entity));
        }
    }

    /**
     * Persits the content categories.
     *
     * @param integer $id         The entity id.
     * @param array   $categories The list of category ids.
     */
    protected function persistCategories($id, $categories)
    {
        // Ignore metas with value = null
        if (!empty($categories)) {
            $categories = array_filter($categories, function ($category) {
                return !is_null($category);
            });
        }

        // Remove old categories
        $this->removeCategories($id, array_values($categories));

        // Update categories
        $this->saveCategories($id, $categories);
    }

    /**
     * Deletes old categories.
     *
     * @param array $id   The entity id.
     */
    protected function removeCategories($id)
    {
        $sql      = "delete from contents_categories where pk_fk_content = ?";
        $params[] = $id['pk_content'];
        $types[]  = is_string($id['pk_content']) ?
            \PDO::PARAM_STR : \PDO::PARAM_INT;

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

        $sql = "replace into contents_categories"
            . "(pk_fk_content, pk_fk_content_category) values "
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
     * Persits the content tags.
     *
     * @param integer $id   The entity id.
     * @param array   $tags The list of tag ids.
     */
    protected function persistTags($id, $tags)
    {
        // Ignore metas with value = null
        if (!empty($tags)) {
            $tags = array_filter($tags, function ($tag) {
                return !is_null($tag);
            });
        }

        // Remove old tags
        $this->removeTags($id, array_values($tags));

        // Update tags
        $this->saveTags($id, $tags);
    }

    /**
     * Deletes old tags.
     *
     * @param array $id   The entity id.
     */
    protected function removeTags($id)
    {
        $sql      = "delete from contents_tags where content_id = ?";
        $params[] = $id['pk_content'];
        $types[]  = is_string($id['pk_content']) ?
            \PDO::PARAM_STR : \PDO::PARAM_INT;

        $this->conn->executeQuery($sql, $params, $types);
    }

    /**
     * Saves new tags.
     *
     * @param array $id         The entity id.
     * @param array $categories The list of category ids to save.
     */
    protected function saveTags($id, $tags)
    {
        if (empty($tags)) {
            return;
        }

        $sql = "replace into contents_tags"
            . "(content_id, tag_id) values "
            . str_repeat(
                '(?,?),',
                count($tags)
            );

        $sql    = rtrim($sql, ',');
        $params = [];
        $types  = [];
        foreach ($tags as $value) {
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
     * Persits the content relations.
     *
     * @param integer $id         The entity id.
     * @param array   $relations  The list of relations.
     */
    protected function persistRelations($id, $relations)
    {
        // Ignore metas with value = null
        if (!empty($relations)) {
            $relations = array_filter($relations, function ($relation) {
                return !is_null($relation);
            });
        }

        // Remove old relations
        $this->removeRelations($id, array_values($relations));

        // Update relations
        $this->saveRelations($id, $relations);
    }

    /**
     * Deletes old relations.
     *
     * @param array $id   The entity id.
     */
    protected function removeRelations($id)
    {
        $sql      = "delete from related_contents where pk_content1 = ?";
        $params[] = $id['pk_content'];
        $types[]  = \PDO::PARAM_INT;

        $this->conn->executeQuery($sql, $params, $types);
    }

    /**
     * Saves new tags.
     *
     * @param array $id         The entity id.
     * @param array $categories The list of category ids to save.
     */
    protected function saveRelations($id, $relations)
    {
        if (empty($relations)) {
            return;
        }

        $sql = "replace into related_contents"
            . "(pk_content1, pk_content2, relationship) values "
            . str_repeat(
                '(?,?,?),',
                count($relations)
            );

        $sql    = rtrim($sql, ',');
        $params = [];
        $types  = [];
        foreach ($relations as $value) {
            $params = array_merge(
                $params,
                array_merge(array_values($id), [ $value ])
            );

            $types = array_merge(
                $types,
                [ \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_STR ]
            );
        }

        $this->conn->executeQuery($sql, $params, $types);
    }
}
