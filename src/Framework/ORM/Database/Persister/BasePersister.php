<?php

namespace Framework\ORM\Database\Persister;

use Framework\ORM\Core\Connection;
use Framework\ORM\Core\Entity;
use Framework\ORM\Core\Metadata;
use Framework\ORM\Core\Persister;
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
     * The database connection.
     *
     * @var Framework\ORM\Core\Connection
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
        $this->cache    = $cache;
        $this->conn     = $conn;
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function create(Entity &$entity)
    {
        list($data, $metas) = $this->databasify($entity->getData());

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
        list($data, $metas) = $this->databasify($entity->getData());
        unset($data['id']);

        $this->conn->update(
            \underscore($entity->getClassName()),
            $data,
            [ 'id' => $entity->id ]
        );

        $this->cache->delete($entity->getCachedId());

        if ($this->metadata[$entity->getClassName()]->mapping['metas']) {
            $this->persistMetas($entity->id, $metas);
        }
    }

    /**
     * Convert entity data to valid database values.
     *
     * @param array $source The entity data.
     *
     * @return array The converted data and metas.
     */
    protected function databasify($source)
    {
        if (!array_key_exists('columns', $this->metadata->mapping)) {
            throw new \Exception();
        }

        $data = [];
        foreach ($source as $key => $value) {
            $from = gettype($value);
            $to   = 'string';

            if (array_key_exists($key, $this->metadata->properties)
                && $this->metadata->properties[$key] !== 'enum'
            ) {
                $from = $this->metadata->properties[$key];
            }

            if (array_key_exists($key, $this->metadata->mapping['columns'])) {
                $to = \classify($this->metadata->mapping['columns'][$key]['type']);
            }

            if (empty($to) && $from === 'array') {
                $to = 'array';
            }

            $mapper = '\\Framework\\ORM\\Core\\DataMapper\\' . ucfirst($from)
                . 'DataMapper';

            $mapper = new $mapper();
            $method = 'to' . ucfirst($to);

            $data[$key] = $mapper->{$method}($value);
        }

        // Meta keys (unknown properties)
        $unknown = array_diff(
            array_keys($data),
            array_keys($this->metadata->mapping['columns'])
        );

        $metas = array_intersect_key($data, array_flip($unknown));
        $data  = array_diff_key($data, array_flip($unknown));

        return [ $data, $metas ];
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
