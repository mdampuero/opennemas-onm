<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Database\Repository;

use Common\ORM\Core\OQL\OQLTranslator;
use Common\ORM\Database\Data\Converter\BaseConverter;
use Common\ORM\Core\Connection;
use Common\ORM\Core\Entity;
use Common\ORM\Core\Metadata;
use Common\ORM\Core\Repository;
use Common\ORM\Core\Exception\EntityNotFoundException;
use Onm\Cache\CacheInterface;

/**
 * The BaseRepository class defines basic actions for database repositories.
 */
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
     * The value to save in cache when the entity is not found.
     *
     * @var string
     */
    protected $miss = '-miss-';

    /**
     * The OQL translator.
     *
     * @var OQLTranslator
     */
    protected $translator;

    /**
     * Initializes a new DatabaseRepository.
     *
     * @param CacheInterface $cache    The cache service.
     * @param Connection     $conn     The database connection.
     * @param Metadata       $metadata The entity metadata.
     */
    public function __construct(Connection $conn, Metadata $metadata, CacheInterface $cache = null)
    {
        $this->cache      = $cache;
        $this->conn       = $conn;
        $this->converter  = new BaseConverter($metadata);
        $this->metadata   = $metadata;
        $this->translator = new OQLTranslator($metadata);
    }

    /**
     * {@inheritdoc}
     */
    public function countBy($oql = '')
    {
        $keys = $this->metadata->getIdKeys();

        list($tables, $filter, $params, $types) =
            $this->translator->translate(trim($oql), true);

        $sql = sprintf(
            'select count(%s) from %s',
            implode(',', $keys),
            implode(',', $tables)
        );

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
     * {@inheritdoc}
     */
    public function find($id)
    {
        if (empty($id)) {
            throw new \Exception();
        }

        $id      = $this->metadata->normalizeId($id);
        $cacheId = $this->metadata->getCachePrefix()
            . implode($this->metadata->getCacheSeparator, $id);

        $entity = null;

        if ($this->hasCache() && $this->cache->contains($cacheId)) {
            $entity = $this->cache->fetch($cacheId);
        }

        if (empty($entity)) {
            $class  = 'Common\\ORM\\Entity\\' . $this->metadata->name;

            $entity = new $class($id);
            $this->refresh($entity);

            if ($this->hasCache()) {
                $this->cache->save($cacheId, $entity);
            }
        }

        if ($entity === $this->miss) {
            throw new EntityNotFoundException($this->metadata->name, $id);
        }

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function findBy($oql = '')
    {
        $keys = $this->metadata->getIdKeys();

        list($tables, $filter, $params, $types) =
            $this->translator->translate(trim($oql));

        $sql = sprintf(
            'select %s from %s',
            implode(',', $keys),
            implode(',', $tables)
        );

        if (!empty($filter)) {
            if (!preg_match('/^\s*(order|limit|offset)/', $filter)) {
                $sql .= " where";
            }

            $sql .= " $filter";
        }

        $rs = $this->conn->fetchAll($sql, $params, $types);

        $ids = array_map(function ($a) use ($keys) {
            return array_intersect_key($a, array_flip($keys));
        }, $rs);

        return $this->findMulti($ids);
    }

    /**
     * Finds entities that match a criteria.
     *
     * @param string $criteria The criteria.
     * @param string $tables   The comma-separated list of tables.
     *
     * @return array The list of entities.
     */
    public function findBySQL($criteria = '', $tables = '')
    {
        if (empty($tables)) {
            $tables = $this->metadata->getTable();
        }

        $keys = $this->metadata->getIdKeys();

        $sql = sprintf('select %s from %s', implode(',', $keys), $tables);

        if (!empty($criteria)) {
            $sql .= " where $criteria";
        }

        $rs = $this->conn->fetchAll($sql);

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
     * @return array The array of entities.
     */
    public function findMulti($data)
    {
        // Build cache ids
        $ids = array_map(function ($a) {
            return $this->metadata->getCachePrefix()
                . implode($this->metadata->getCacheSeparator(), $a);
        }, $data);

        $entities = [];
        $keys     = [];
        if ($this->hasCache()) {
            $entities = $this->cache->fetch($ids);
            $keys     = array_keys($entities);
        }

        $miss = array_diff($ids, $keys);

        // Get missed entities from database
        foreach ($miss as $cacheId) {
            $id = str_replace($this->metadata->getCachePrefix(), '', $cacheId);

            $entities[$cacheId] = $this->find($id);
        }

        // Keep original order
        return array_values(array_merge(array_flip($ids), $entities));
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy($oql = '')
    {
        $oql = preg_replace('/limit\s*\d+/', '', $oql) . ' limit 1';
        $rs  = $this->findBy($oql);

        if (!empty($rs)) {
            return array_pop($rs);
        }

        throw new EntityNotFoundException($this->metadata->name);
    }

    /**
     * Refresh an entity with fresh data from database.
     *
     * @param Entity $entity The entity to refresh.
     */
    public function refresh(Entity &$entity)
    {
        $filters = [];
        $params  = [];
        $types   = [];
        foreach ($entity->getData() as $key => $value) {
            $params[]  = $value;
            $filters[] = "$key = ?";
            $types[]   = is_string($value) ? \PDO::PARAM_STR : \PDO::PARAM_INT;
        }

        if (empty($filters)) {
            $entity = $this->miss;
            return;
        }

        $sql = 'select * from ' . $this->metadata->getTable() . ' where '
            .  implode(' and ', $filters);

        $rs = $this->conn->fetchAssoc($sql, $params, $types);

        if (!$rs) {
            $entity = $this->miss;
            return;
        }

        if ($this->metadata->hasMetas()) {
            $rs = array_merge($rs, $this->getMetas($entity));

            if (!$rs) {
                $entity = $this->miss;
                return;
            }
        }

        $entity->setData($this->converter->objectify($rs, true));
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
        $metaKeys = $this->metadata->getMetaKeys();
        $filters = [];

        foreach ($metaKeys as $key => $value) {
            $filters[] = $value . '=' . $entity->{$key};
        }

        $sql = 'select * from ' . $this->metadata->getMetaTable()
            . ' where ' . implode(' and ', $filters);

        $rs = $this->conn->fetchAll($sql);

        foreach ($rs as $value) {
            $metas[$value['meta_key']] = $value['meta_value'];
        }

        return $metas;
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
}
