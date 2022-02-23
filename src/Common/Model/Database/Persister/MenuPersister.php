<?php

namespace Common\Model\Database\Persister;

use Opennemas\Cache\Core\Cache;
use Opennemas\Orm\Core\Connection;
use Opennemas\Orm\Core\Entity;
use Opennemas\Orm\Core\Metadata;
use Opennemas\Orm\Database\Persister\BasePersister;

/**
 * The InstancemenuPersister class defines actions to persist Menus.
 */
class MenuPersister extends BasePersister
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
        $menuItems = [];
        if (!empty($entity->menu_items)) {
            $menuItems = $entity->menu_items;
            unset($entity->menu_items);
        }
        $this->conn->beginTransaction();

        try {
            parent::create($entity);

            $id = $this->metadata->getId($entity)['pk_menu'];

            $this->persistMenuItems($id, $menuItems);
            $entity->menu_items = $menuItems;

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
        $changes   = $entity->getChanges();
        $menuItems = $entity->menu_items;

        // Menu items change
        if (array_key_exists('menu_items', $changes)) {
            $menuItems = $changes['menu_items'];
        }

        // Ignore menu_items, persist them later
        unset($entity->menu_items);
        $entity->setNotStored('menu_items');

        $this->conn->beginTransaction();

        try {
            parent::update($entity);

            $id = $this->metadata->getId($entity)['pk_menu'];

            if (array_key_exists('menu_items', $changes)) {
                $this->persistMenuItems($id, $menuItems);
            }

            $entity->menu_items = $menuItems;

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
            $this->conn->commit();
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
    protected function persistMenuItems($id, $menuItems)
    {
        // Ignore metas with value = null
        if (!empty($menuItems)) {
            $menuItems = array_filter($menuItems);
        }

        // Remove old
        $this->removeMenuItems($id);

        // Update
        $this->saveMenuItems($id, $menuItems);
    }

    /**
     * Deletes old categories.
     *
     * @param array $id   The entity id.
     */
    protected function removeMenuItems($id)
    {
        $sql      = "delete from menu_items where pk_menu = ?";
        $params[] = (int) $id;
        $types[]  = is_string($id) ?
            \PDO::PARAM_STR : \PDO::PARAM_INT;

        $this->conn->executeQuery($sql, $params, $types);
    }

    /**
     * Saves new categories.
     *
     * @param array $id         The entity id.
     * @param array $categories The list of category ids to save.
     */
    protected function saveMenuItems($id, $menuItems)
    {
        if (empty($menuItems)) {
            return;
        }

        $menuItems = $this->converter->databasifyMenuItems($menuItems);

        $sql = "insert into menu_items"
            . "(pk_item, pk_menu, title, link_name, type, position, pk_father) values "
            . str_repeat(
                '(?,?,?,?,?,?,?),',
                count($menuItems)
            );

        $sql    = rtrim($sql, ',');
        $params = [];
        $types  = [];
        foreach ($menuItems as $value) {
            $params = array_merge($params, [
                (int) $value['pk_item'],
                (int) $id,
                $value['title'] ?? '',
                $value['link_name'] ?? '',
                $value['type'],
                (int) $value['position'],
                (int) $value['pk_father']
            ]);

            $types = array_merge($types, [
                \PDO::PARAM_INT,
                \PDO::PARAM_INT,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,

                \PDO::PARAM_STR,
                \PDO::PARAM_INT,
                \PDO::PARAM_INT
            ]);
        }
        $this->conn->executeQuery($sql, $params, $types);
    }
}
