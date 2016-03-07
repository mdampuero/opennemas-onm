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

        $this->conn->insert(\underscore($entity->getClassName()), $data);

        $entity->id = $this->conn->lastInsertId();

        if ($this->metadata->mapping['metas']) {
            $this->persistMetas($entity->id, $metas);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Entity $entity)
    {
        $this->conn->delete(
            \underscore($entity->getClassName()),
            [ 'id' => $entity->id ]
        );

        $this->cache->delete($entity->getCachedId());
    }

    /**
     * {@inheritdoc}
     */
    public function update(Entity $entity)
    {
        list($data, $metas) = $this->converter->databasify($entity->getData());
        unset($data['id']);

        $this->conn->update(
            \underscore($entity->getClassName()),
            $data,
            [ 'id' => $entity->id ]
        );

        $this->cache->delete($entity->getCachedId());

        if ($this->metadata->mapping['metas']) {
            $this->persistMetas($entity->id, $metas);
        }
    }

    /**
     * Persits the entity metas.
     *
     * @param integer $id    The entity id.
     * @param array   $metas The entity metas.
     */
    protected function persistMetas($id, $metas = [])
    {
        $entity = \underscore($this->metadata->name);

        // Update metas
        if (!empty($metas)) {
            $sql = rtrim("REPLACE INTO {$entity}_meta VALUES "
                . str_repeat('(?,?,?),', count($metas)), ',');

            $params = [];
            $types  = [];

            foreach ($metas as $key => $value) {
                $params = array_merge($params, [ $id, $key, $value ]);
                $types  = array_merge(
                    $types,
                    [ \PDO::PARAM_INT, \PDO::PARAM_STR, \PDO::PARAM_STR ]
                );
            }

            $this->conn->executeQuery($sql, $params, $types);
        }

        // Remove old metas
        $sql    = "DELETE FROM {$entity}_meta WHERE {$entity}_id=?";
        $params = [ $id ];
        $types  = [ \PDO::PARAM_INT ];

        if (!empty($metas)) {
            $sql .= " AND meta_key NOT IN (?)";
            $params[] = array_keys($metas);
            $types[]  = \Doctrine\DBAL\Connection::PARAM_STR_ARRAY;
        }

        $this->conn->executeQuery($sql, $params, $types);
    }
}
