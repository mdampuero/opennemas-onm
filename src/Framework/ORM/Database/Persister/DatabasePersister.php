<?php

namespace Framework\ORM\Database\Persister;

use Framework\ORM\Entity\Entity;
use Framework\ORM\Persister\Persister;
use Onm\Database\DbalWrapper;
use Onm\Cache\CacheInterface;

abstract class DatabasePersister extends Persister
{
    /**
     * The cache service for instance.
     *
     * @var DbalWrapper
     */
    protected $icache;

    /**
     * The database connection for instance.
     *
     * @var DbalWrapper
     */
    protected $iconn;

    /**
     * The cache service for manager.
     *
     * @var DbalWrapper
     */
    protected $mcache;

    /**
     * The database connection for manager.
     *
     * @var DbalWrapper
     */
    protected $mconn;

    /**
     * The source name.
     *
     * @var string
     */
    protected $source = 'Database';

    /**
     * Initializes a new DatabasePersister.
     *
     * @param CacheInterface $icache The cache service for instance.
     * @param DbalWrapper    $iconn  The dabase connection for instance.
     * @param CacheInterface $mcache The cache service for manager.
     * @param DbalWrapper    $mconn  The dabase connection for manager
     * @param DbalWrapper    $source The source name.
     */
    public function __construct(CacheInterface $icache, DbalWrapper $iconn, CacheInterface $mcache, DbalWrapper $mconn, $source)
    {
        $this->icache = $icache;
        $this->iconn  = $iconn;
        $this->mcache = $mcache;
        $this->mconn  = $mconn;
        $this->source = $source;
    }

    /**
     * Convert entity data to valid database values.
     *
     * @param Entity $entity The entity.
     *
     * @return array The converted data.
     */
    public function dbfy(Entity $entity)
    {
        $data = [];

        foreach ($entity->getData() as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $value = @serialize($value);
            }

            $data[$key] = $value;
        }

        return $data;
    }
}
