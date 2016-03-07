<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Database\Repository;

use Common\ORM\Core\Criteria\OQLTranslator;
use Common\ORM\Database\Data\Converter\Converter;
use Common\ORM\Core\Connection;
use Common\ORM\Core\Entity;
use Common\ORM\Core\Metadata;
use Common\ORM\Core\Repository;
use Common\ORM\Core\Exception\EntityNotFoundException;
use Onm\Cache\CacheInterface;

class BaseRepository extends Repository
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
     * @var Connection
     */
    protected $conn;

    /**
     * The value to save in cache when the entity is not found.
     *
     * @var string
     */
    protected $miss = '-miss-';

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
        $this->cache      = $cache;
        $this->conn       = $conn;
        $this->converter  = new Converter($metadata);
        $this->metadata   = $metadata;
        $this->translator = new OQLTranslator($metadata);
    }

    /**
     * Returns the number of entities that match the criteria.
     *
     * @param array $criteria The criteria.
     *
     * @return integer The number of entities.
     */
    public function countBy($oql = '')
    {
        $sql = "select count(id) from `{$this->metadata->mapping['table']}`";
        list($filter, $params, $types) = $this->translator->translate(trim($oql));

        if (!empty($filter)) {
            $sql .= " where $filter";
        }

        $rs  = $this->conn->fetchArray($sql, $params, $types);

        if (!$rs) {
            return false;
        }

        return (integer) $rs[0];
    }

    /**
     * Finds an entity by id.
     *
     * @param mixed $id The entity id.
     *
     * @return Entity The entity.
     *
     * @throws EntityNotFoundException If the entity was not found.
     */
    public function find($id)
    {
        if (empty($id)) {
            throw new \Exception();
        }

        $id      = $this->normalizeId($id);
        $cacheId = $this->metadata->getCachePrefix()
            . implode($this->metadata->getCacheSeparator, $id);

        if (($entity = $this->cache->fetch($cacheId)) === false) {
            $class = 'Common\\ORM\\Entity\\' . $this->metadata->name;

            $entity = new $class($id);
            $this->refresh($entity);

            $this->cache->save($cacheId, $entity);
        }

        if ($entity === $this->miss) {
            throw new \Exception();
        }

        return $entity;
    }

    /**
     * Finds enties given a criteria.
     *
     * @param array   $criteria        The criteria.
     * @param array   $order           The order applied in the search.
     * @param integer $elementsPerPage The max number of elements.
     * @param integer $page            The current page.
     * @param integer $offset          The offset to start with.
     *
     * @return array The matched elements.
     */
    public function findBy($oql = '')
    {
        $keys = $this->metadata->getIdKeys();

        $sql = "select " . implode(',', $keys)
            . " from `{$this->metadata->mapping['table']}`";

        list($filter, $params, $types) = $this->translator->translate(trim($oql));

        if (!empty($filter)) {
            $sql .= ' where ' . $filter;
        }

        $rs = $this->conn->fetchAll($sql, $params, $types);

        $ids = array_map(function ($a) use ($keys) {
            return array_intersect_key($a, array_flip($keys));
        }, $rs);

        return $this->findMulti($ids);
    }

    /**
     * Find multiple contents from an array of entity ids.
     *
     * @param array $data Array of entity ids.
     *
     * @return array Array of entities.
     */
    public function findMulti($data)
    {
        // Build cache ids
        $ids = array_map(function ($a) {
            return $this->metadata->getCachePrefix()
                . implode($this->metadata->getCacheSeparator(), $a);
        }, $data);

        $entities = $this->cache->fetch($ids);
        $miss     = array_diff($ids, array_keys($entities));

        // Get missed entities from database
        foreach ($miss as $cacheId) {
            $id = str_replace($this->metadata->getCachePrefix(), '', $cacheId);

            $entities[$cacheId] = $this->find($id);
        }

        // Keep original order
        return array_merge(array_flip($ids), $entities);
    }

    /**
     * Refresh an entity with fresh data from database.
     *
     * @param Entity $entity The entity to refresh.
     */
    public function refresh(Entity &$entity)
    {
        $filters = [];
        foreach ($entity->getData() as $key => $value) {
            $filters[] = "$key = $value";
        }

        if (empty($filters)) {
            $entity = $this->miss;
            return;
        }

        $sql = 'select * from ' . $this->metadata->mapping['table'] . ' where '
            .  implode(' and ', $filters);

        $rs = $this->conn->fetchAssoc($sql);

        if (!$rs) {
            $entity = $this->miss;
            return;
        }

        if ($this->metadata->mapping['metas']) {
            $rs = array_merge($rs, $this->getMetas($entity));

            if (!$rs) {
                $entity = $this->miss;
                return;
            }
        }

        $entity->setData($this->converter->objectify($rs));
        $entity->refresh();
    }

    /**
     * Returns an array of metas for the current entity.
     *
     * @param Entity $entity The entity.
     *
     * @return array The array of metas.
     */
    protected function getMetas($entity)
    {
        $metas   = [];
        $keys    = $this->metadata->getIdKeys();
        $filters = [];

        // Build filters for SQL
        foreach ($keys as $key) {
            $filters[] = $this->metadata->mapping['table'] . '_' . $key
                . '=' . $entity->{$key};
        }

        $sql = 'select * from ' . $this->metadata->mapping['table']
            . '_meta where ' . implode(' and ', $filters);

        $rs = $this->conn->fetchAll($sql);

        foreach ($rs as $value) {
            $metas[$value['meta_key']] = $value['meta_value'];
        }

        return $metas;
    }

    /**
     * Returns the normalized id.
     *
     * @param mixed $id The entity id as string or array.
     *
     * @return array The normalized id.
     */
    protected function normalizeId($id)
    {
        $keys = !is_array($id) ? $this->metadata->getIdKeys() : array_keys($id);
        $id   = !is_array($id) ? [ $id ] : $id;

        return array_combine($keys, $id);
    }
}
