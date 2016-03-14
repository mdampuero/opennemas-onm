<?php

namespace Common\ORM\Database\Persister;

use Common\ORM\Core\Connection;
use Common\ORM\Core\Entity;
use Common\ORM\Core\Metadata;
use Common\ORM\Core\Persister;
use Common\ORM\Database\Data\Converter\Converter;
use Onm\Cache\CacheInterface;

class BasePersister extends Persister
{
    /**
     * The cache service.
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     * The entity converter.
     *
     * @var Converter
     */
    protected $converter;

    /**
     * The database connection.
     *
     * @var Connection
     */
    protected $conn;

    /**
     * The entity metadata.
     *
     * @var Metadata.
     */
    protected $metadata;

    /**
     * The source name.
     *
     * @var string
     */
    protected $source = 'Database';

    /**
     * Initializes a new DatabasePersister.
     *
     * @param CacheInterface $cache    The cache service.
     * @param Connection     $conn     The database connection.
     * @param Metadata       $metadata The entity metadata.
     */
    public function __construct(CacheInterface $cache, Connection $conn, Metadata $metadata)
    {
        $this->cache     = $cache;
        $this->conn      = $conn;
        $this->converter = new Converter($metadata);
        $this->metadata  = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function create(Entity &$entity)
    {
        list($data, $metas) = $this->converter->databasify($entity->getData());

        $this->conn->insert($this->metadata->getTable(), $data);

        $keys = $this->metadata->getIdKeys();

        if (count($keys) === 1) {
            $entity->{$keys[0]} = $this->conn->lastInsertId();
        }

        $keys = array_flip($keys);
        $id   = array_intersect_key($entity->getData(), $keys);

        if ($this->metadata->hasMetas()) {
            $this->persistMetas($id, $metas);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Entity $entity)
    {
        $id = $this->metadata->getId($entity);

        $this->conn->delete($this->metadata->getTable(), $id);
        $this->cache->delete($this->metadata->getCacheId($entity));
    }

    /**
     * {@inheritdoc}
     */
    public function update(Entity $entity)
    {
        list($data, $metas) = $this->converter->databasify($entity->getData());

        $keys = array_flip($this->metadata->getIdKeys());

        $id   = array_intersect_key($entity->getData(), $keys);
        $data = array_diff_key($data, $keys);

        $this->conn->update($this->metadata->getTable(), $data, $id);

        if ($this->metadata->hasMetas()) {
            $this->persistMetas($id, $metas);
        }

        $this->cache->delete($this->metadata->getCacheId($entity));
    }

    /**
     * Persits the entity metas.
     *
     * @param integer $id    The entity id.
     * @param array   $metas The entity metas.
     */
    protected function persistMetas($id, $metas)
    {
        // Ignore metas with value = null
        if (!empty($metas)) {
            $metas = array_filter($metas, function ($a) {
                return !is_null($a);
            });
        }

        // Update metas
        $this->saveMetas($id, $metas);

        // Remove old metas
        $this->removeMetas($id, array_keys($metas));
    }

    /**
     * Deletes old metas.
     *
     * @param array $id    The entity id.
     * @param array $metas The meta keys to keep.
     */
    protected function removeMetas($id, $keep)
    {
        $sql  = "delete from {$this->metadata->getMetaTable()} where ";
        $keys = $this->metadata->getMetaKeys();

        $joins  = [];
        $params = [];
        $types  = [];
        foreach ($id as $key => $value) {
            $joins[]  = $keys[$key] . '= ?';
            $params[] = $value;
            $types[]  = is_string($value) ? \PDO::PARAM_STR : \PDO::PARAM_INT;
        }

        $sql .= implode(' and ', $joins);

        if (!empty($keep)) {
            $sql .= " and meta_key not in (?)";
            $params[] = $keep;
            $types[]  = \Doctrine\DBAL\Connection::PARAM_STR_ARRAY;
        }

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
