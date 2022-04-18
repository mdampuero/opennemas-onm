<?php

namespace Common\Model\Database\Persister;

use Common\Model\Entity\User;
use Opennemas\Cache\Core\Cache;
use Opennemas\Orm\Core\Connection;
use Opennemas\Orm\Core\Entity;
use Opennemas\Orm\Core\Metadata;
use Opennemas\Orm\Database\Data\Converter\BaseConverter;
use Opennemas\Orm\Database\Persister\BasePersister;

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
    public function __construct(Connection $conn, Metadata $metadata, ?Cache $cache)
    {
        $this->cache    = $cache;
        $this->conn     = $conn;
        $this->metadata = $metadata;

        $class = $metadata->getConverter()['class'];

        $this->converter = new $class($metadata);
    }

    /**
     * {@inheritdoc}
     */
    public function create(Entity &$entity)
    {
        if (empty($entity->starttime) && !empty($entity->content_status)) {
            $entity->starttime = new \DateTime();
        }

        // Don't allow changed date to be earlier than starttime
        if ($entity->starttime > $entity->changed) {
            $entity->changed = $entity->starttime;
        }

        $categories = [];
        if (!empty($entity->categories)) {
            $categories = $entity->categories;
            unset($entity->categories);
        }

        $tags = [];
        if (!empty($entity->tags)) {
            $tags = $entity->tags;
            unset($entity->tags);
        }

        $relatedContents = [];
        if (!empty($entity->related_contents)) {
            $relatedContents = $entity->related_contents;
            unset($entity->related_contents);
        }

        $live_blog_updates = [];
        if (!empty($entity->live_blog_updates)) {
            $live_blog_updates = $entity->live_blog_updates;
            unset($entity->live_blog_updates);
        }

        $this->conn->beginTransaction();

        try {
            parent::create($entity);

            $id = $this->metadata->getId($entity);

            $this->persistCategories($id, $categories);
            $entity->categories = $categories;

            $this->persistTags($id, $tags);
            $entity->tags = $tags;

            $this->persistRelations($id, $relatedContents);
            $entity->related_contents = $relatedContents;

            $this->persistLiveBlogUpdates($id, $live_blog_updates);
            $entity->live_blog_updates = $live_blog_updates;

            $this->conn->commit();
        } catch (\Throwable $e) {
            $this->conn->rollback();

            throw $e;
        }

        $entity->refresh();
    }

    /**
     * {@inheritdoc}
     */
    public function update(Entity $entity)
    {
        if (empty($entity->starttime) && !empty($entity->content_status)) {
            $entity->starttime = new \DateTime();
        }

        // Don't allow changed date to be earlier than starttime
        if ($entity->starttime > $entity->changed) {
            $entity->changed = $entity->starttime;
        }
        $changes         = $entity->getChanges();
        $categories      = $entity->categories;
        $tags            = $entity->tags;
        $relations       = $entity->related_contents;
        $live_blog_updates = $entity->live_blog_updates;

        // Categories change
        if (array_key_exists('categories', $changes)) {
            $categories = $changes['categories'];
        }

        // Ignore categories, persist them later
        unset($entity->categories);
        $entity->setNotStored('categories');

        // Ignore tags, persist them later
        unset($entity->tags);
        $entity->setNotStored('tags');

        $this->conn->beginTransaction();

        try {
            parent::update($entity);

            $id = $this->metadata->getId($entity);

            if (array_key_exists('categories', $changes)) {
                $this->persistCategories($id, $categories);
            }

            $entity->categories = $categories;

            if (array_key_exists('tags', $changes)) {
                $this->persistTags($id, $tags);
            }

            $entity->tags = $tags;

            if (array_key_exists('related_contents', $changes)) {
                $this->persistRelations($id, $relations);
            }

            $entity->relations = $relations;
            if (array_key_exists('live_blog_updates', $changes)) {
                $this->persistLiveBlogUpdates($id, $live_blog_updates);
            }

            $entity->live_blog_updates = $live_blog_updates;

            $this->conn->commit();

            if ($this->hasCache()) {
                $this->cache->remove($this->metadata->getPrefixedId($entity));
            }
        } catch (\Throwable $e) {
            $this->conn->rollback();

            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Entity $entity)
    {
        $this->conn->beginTransaction();

        try {
            parent::remove($entity);

            $id = $this->metadata->getId($entity);

            $this->removeCategories($id);

            $this->removeTags($id);

            $this->conn->commit();

            if ($this->hasCache()) {
                $this->cache->remove($this->metadata->getPrefixedId($entity));
            }
        } catch (\Throwable $e) {
            $this->conn->rollback();

            throw $e;
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
        $sql      = "delete from content_category where content_id = ?";
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

        $sql = "replace into content_category"
            . "(content_id, category_id) values "
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
        $this->removeTags($id, $tags);

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
        $this->removeRelations($id);

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
        $sql      = "delete from content_content where source_id = ?";
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

        $relations = $this->converter->databasifyRelated($relations);

        $sql = "insert into content_content"
            . "(source_id, target_id, type, content_type_name, caption, position) values "
            . str_repeat(
                '(?,?,?,?,?,?),',
                count($relations)
            );

        $id     = array_values($id);
        $sql    = rtrim($sql, ',');
        $params = [];
        $types  = [];
        foreach ($relations as $value) {
            $params = array_merge($params, array_merge($id, [
                (int) $value['target_id'],
                $value['type'],
                $value['content_type_name'],
                $value['caption'],
                (int) $value['position'],
            ]));

            $types = array_merge($types, [
                \PDO::PARAM_INT,
                \PDO::PARAM_INT,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                empty($value['caption']) ? \PDO::PARAM_NULL : \PDO::PARAM_STR,
                \PDO::PARAM_INT
            ]);
        }

        $this->conn->executeQuery($sql, $params, $types);
    }

        /**
     * Persits the content liveBlogUpdates.
     *
     * @param integer $id         The entity id.
     * @param array   $liveBlogUpdates  The list of liveBlogUpdates.
     */
    protected function persistLiveBlogUpdates($id, $live_blog_updates)
    {
        // Ignore metas with value = null
        if (!empty($live_blog_updates)) {
            $live_blog_updates = array_filter($live_blog_updates, function ($relation) {
                return !is_null($relation);
            });
        }

        // Remove old relations
        $this->removeLiveBlogUpdates($id);

        // Update relations
        $this->saveLiveBlogUpdates($id, $live_blog_updates);
    }

    /**
     * Deletes old LiveBlogUpdates.
     *
     * @param array $id   The entity id.
     */
    protected function removeLiveBlogUpdates($id)
    {
        $sql      = "delete from content_updates where content_id = ?";
        $params[] = $id['pk_content'];
        $types[]  = is_string($id['pk_content']) ?
            \PDO::PARAM_STR : \PDO::PARAM_INT;

        $this->conn->executeQuery($sql, $params, $types);
    }

    /**
     * Saves new LiveBlogUpdates.
     *
     * @param array $id         The entity id.
     * @param array $liveBlogUpdates The list of liveBlogUpdates to save.
     */
    protected function saveLiveBlogUpdates($id, $live_blog_updates)
    {
        if (empty($live_blog_updates)) {
            return;
        }

        $sql = "insert into content_updates"
            . "(content_id, title, body, caption, image_id, created, modified) values "
            . str_repeat(
                '(?,?,?,?,?,?,?),',
                count($live_blog_updates)
            );

        $id     = array_values($id);
        $sql    = rtrim($sql, ',');
        $params = [];
        $types  = [];

        foreach ($live_blog_updates as $value) {
            $value['created']  = empty($value['created']) ? gmdate("Y-m-d H:i:s") : $value['created'];
            $value['modified'] = empty($value['modified']) ? gmdate("Y-m-d H:i:s") : $value['modified'];
            $value['image_id'] = empty($value['image_id']) ? null : $value['image_id'];

            $params = array_merge($params, array_merge($id, [
                $value['title'],
                $value['body'],
                $value['caption'],
                $value['image_id'],
                $value['created'],
                $value['modified'],
            ]));
            $types = array_merge($types, [
                \PDO::PARAM_INT,
                empty($value['title']) ? \PDO::PARAM_NULL : \PDO::PARAM_STR,
                empty($value['body']) ? \PDO::PARAM_NULL : \PDO::PARAM_STR,
                empty($value['caption']) ? \PDO::PARAM_NULL : \PDO::PARAM_STR,
                empty($value['image_id']) ? \PDO::PARAM_NULL : \PDO::PARAM_INT,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
            ]);
        }
        $this->conn->executeQuery($sql, $params, $types);
    }
}
