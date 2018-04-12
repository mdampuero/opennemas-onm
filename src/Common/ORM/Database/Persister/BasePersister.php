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
use Common\ORM\Core\Persister;
use Common\ORM\Database\Data\Converter\BaseConverter;

/**
 * The BasePersister class defines basic actions for database persisters.
 */
class BasePersister extends Persister
{
    /**
     * The cache service.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * The database connection.
     *
     * @var Connection
     */
    protected $conn;

    /**
     * The entity converter.
     *
     * @var BaseConverter
     */
    protected $converter;

    /**
     * The entity metadata.
     *
     * @var Metadata.
     */
    protected $metadata;

    /**
     * Initializes a new DatabasePersister.
     *
     * @param Connection $conn     The database connection.
     * @param Metadata   $metadata The entity metadata.
     * @param Cache      $cache    The cache service.
     */
    public function __construct(Connection $conn, Metadata $metadata, Cache $cache = null)
    {
        $this->cache     = $cache;
        $this->conn      = $conn;
        $this->converter = new BaseConverter($metadata);
        $this->metadata  = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function create(Entity &$entity)
    {
        list($data, $metas, $types) = $this->converter->databasify($entity);

        $types = $types;
        $keys  = $this->metadata->getIdKeys();

        $this->conn->insert($this->metadata->getTable(), $data, $types);

        if (count($keys) === 1 && empty($entity->{$keys[0]})) {
            $entity->{$keys[0]} = $this->conn->lastInsertId();
        }

        if ($this->metadata->hasMetas()) {
            $id = $this->metadata->getId($entity);

            $this->persistMetas($id, $metas);
        }

        $entity->refresh();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Entity $entity)
    {
        $id = $this->metadata->getId($entity);

        $this->conn->delete($this->metadata->getTable(), $id);

        // TODO: Remove when using foreign keys
        if ($this->metadata->hasMetas()) {
            $this->removeMetas($id);
        }

        if ($this->hasCache()) {
            $this->cache->remove($this->metadata->getPrefixedId($entity));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function update(Entity $entity)
    {
        list($data, $metas, $types) = $this->converter->databasify($entity);

        $id = $this->metadata->getId($entity);

        // Remove ids from data and types
        $data  = array_diff_key($data, $id);
        $types = array_diff_key($types, $id);

        $changes = $entity->getChanges();

        // Ignore non-changed data
        $data  = array_intersect_key($data, $changes);
        $types = array_intersect_key($types, $changes);
        $metas = array_intersect_key($metas, $changes);

        if (!empty($data)) {
            $this->conn->update($this->metadata->getTable(), $data, $id, $types);
        }

        if ($this->metadata->hasMetas() && !empty($metas)) {
            $this->persistMetas($id, $metas);
        }

        if ($this->hasCache() && (!empty($data) || !empty($metas))) {
            $this->cache->remove($this->metadata->getPrefixedId($entity));
        }
    }

    /**
     * Checks if the current repository has cache.
     *
     * @return boolean True if the repository has cache. Otherwise, returns
     *                 false.
     */
    protected function hasCache()
    {
        return !empty($this->cache);
    }

    /**
     * Persits the entity metas.
     *
     * @param integer $id    The entity id.
     * @param array   $metas The entity metas.
     */
    protected function persistMetas($id, $metas)
    {
        $toSave = array_filter($metas, function ($a) {
            return !empty($a);
        });

        $toDelete = array_filter($metas, function ($a) {
            return empty($a);
        });

        // Update metas
        $this->saveMetas($id, $toSave);

        // Remove old metas
        $this->removeMetas($id, array_keys($toDelete));
    }

    /**
     * Deletes old metas.
     *
     * @param array $id   The entity id.
     * @param array $keep The meta keys to remove.
     */
    protected function removeMetas($id, $metas = [])
    {
        if (empty($metas)) {
            return;
        }

        $sql  = "delete from {$this->metadata->getMetaTable()} where ";
        $keys = $this->metadata->getMetaKeys();

        $joins  = [];
        $params = [];
        $types  = [];
        foreach ($id as $key => $value) {
            $joins[]  = $keys[$key] . ' = ?';
            $params[] = $value;
            $types[]  = is_string($value) ? \PDO::PARAM_STR : \PDO::PARAM_INT;
        }

        $sql .= implode(' and ', $joins);

        $sql      .= " and {$this->metadata->getMetaKeyName()} in (?)";
        $params[]  = $metas;
        $types[]   = \Doctrine\DBAL\Connection::PARAM_STR_ARRAY;

        $this->conn->executeQuery($sql, $params, $types);
    }

    /**
     * Saves new metas.
     *
     * @param array $id    The entity id.
     * @param array $metas The metas to save.
     */
    protected function saveMetas($id, $metas)
    {
        if (empty($metas)) {
            return;
        }

        $sql = "replace into {$this->metadata->getMetaTable()} values "
            . str_repeat(
                '(' . str_repeat('?,', count($id)) . '?,?),',
                count($metas)
            );

        $sql    = rtrim($sql, ',');
        $params = [];
        $types  = [];
        foreach ($metas as $key => $value) {
            $params = array_merge(
                $params,
                array_merge(array_values($id), [ $key, $value ])
            );

            $types = array_merge(
                $types,
                [ \PDO::PARAM_INT, \PDO::PARAM_STR, \PDO::PARAM_STR ]
            );
        }

        $this->conn->executeQuery($sql, $params, $types);
    }
}
