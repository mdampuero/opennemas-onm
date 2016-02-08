<?php

namespace Framework\ORM\Database\Persister;

use Framework\ORM\Core\Connection;
use Framework\ORM\Core\Entity;
use Framework\ORM\Core\Metadata;
use Framework\ORM\Core\Persister;
use Onm\Cache\CacheInterface;

abstract class DatabasePersister extends Persister
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
     * Convert entity data to valid database values.
     *
     * @param Entity $entity The entity.
     *
     * @return array The converted data.
     */
    protected function databasify(Entity $entity)
    {
        if (!array_key_exists('columns', $this->metadata->mapping)) {
            throw new \Exception();
        }

        $data = [];
        foreach ($entity->getData() as $key => $value) {
            if (array_key_exists($key, $this->metadata->properties)
                && array_key_exists($key, $this->metadata->mapping['columns'])
            ) {
                $from = $this->metadata->properties[$key];
                $to   = \classify($this->metadata->mapping['columns'][$key]['type']);

                $mapper = '\\Framework\\ORM\\Core\\DataMapper\\' . ucfirst($from)
                    . 'DataMapper';

                $mapper = new $mapper();
                $method = 'to' . ucfirst($to);

                $data[$key] = $mapper->{$method}($value);
            }
        }

        return $data;
    }
}
