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

use Common\Cache\Core\Cache;
use Common\ORM\Core\Oql\Sql\SqlTranslator;
use Common\ORM\Database\Data\Converter\BaseConverter;
use Common\ORM\Core\Connection;
use Common\ORM\Core\Entity;
use Common\ORM\Core\Metadata;
use Common\ORM\Core\Repository;
use Common\ORM\Core\Exception\EntityNotFoundException;

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
     * @var SqlTranslator
     */
    protected $translator;

    /**
     * Initializes a new DatabaseRepository.
     *
     * @param Connection $conn     The database connection.
     * @param Metadata   $metadata The entity metadata.
     * @param Cache      $cache    The cache service.
     */
    public function __construct(Connection $conn, Metadata $metadata, Cache $cache = null)
    {
        $this->cache      = $cache;
        $this->conn       = $conn;
        $this->converter  = new BaseConverter($metadata);
        $this->metadata   = $metadata;
        $this->translator = new SqlTranslator($metadata);
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
            throw new \InvalidArgumentException();
        }

        $entity  = null;
        $id      = $this->metadata->normalizeId($id);
        $cacheId = $this->metadata->getPrefix()
            . implode($this->metadata->getSeparator(), $id);

        if ($this->hasCache() && $this->cache->exists($cacheId)) {
            $entity = $this->cache->get($cacheId);
        }

        if (empty($entity)) {
            $entity   = $this->miss;
            $entities = $this->refresh([ $id ]);

            if (!empty($entities)) {
                $entity = array_pop($entities);
            }

            if ($this->hasCache()) {
                $this->cache->set($cacheId, $entity);
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

        return $this->getEntities($ids);
    }

    /**
     * Finds entities that match a criteria.
     *
     * @param string $criteria The criteria.
     * @param string $tables   The comma-separated list of tables.
     *
     * @return array The list of entities.
     */
    public function findBySql($criteria = '', $tables = '')
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

        return $this->getEntities($ids);
    }

    /**
     * Find entities from an array of entity ids.
     *
     * @param array $ids Array of entity ids.
     *
     * @return array The array of entities.
     */
    public function getEntities($ids)
    {
        // Prefix ids
        $prefixedIds = array_map(function ($a) {
            return $this->metadata->getPrefixedId($a);
        }, $ids);

        $entities = [];
        $keys     = [];
        if ($this->hasCache()) {
            $entities = $this->cache->get($prefixedIds);
            $keys     = array_keys($entities);
        }

        $missed = array_diff($prefixedIds, $keys);

        // Get missed entities from database
        if (!empty($missed)) {
            $notCached = [];

            // Remove prefix from missed ids
            foreach ($missed as $cacheId) {
                $id = str_replace($this->metadata->getPrefix(), '', $cacheId);
                $id = explode($this->metadata->getSeparator(), $id);

                $notCached[] = array_combine($this->metadata->getIdKeys(), $id);
            }

            $notCached = $this->refresh($notCached);

            foreach ($notCached as $entity) {
                if ($this->hasCache()) {
                    $this->cache->set($this->metadata->getPrefixedId($entity), $entity);
                }

                $entities[$this->metadata->getPrefixedId($entity)] = $entity;
            }
        }

        // Keep original order
        return array_values(array_merge(array_flip($prefixedIds), $entities));
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
     * Refreshes data from database for the given ids.
     *
     * @param array $ids The ids of the entities to refresh.
     *
     * @return array The array of entities.
     */
    protected function refresh($ids)
    {
        $oql = [];
        foreach ($ids as $id) {
            $filter = [];
            foreach ($id as $key => $value) {
                $filter[] = sprintf('%s = "%s"', $key, $value);
            }

            $oql[] = '(' . implode(' and ', $filter) . ')';
        }

        $oql = implode(' or ', $oql);

        list($tables, $filter, $params, $types) =
            $this->translator->translate(trim($oql));

        $sql = sprintf('select * from %s', implode(',', $tables))
            . ' where ' . $filter;

        $rs = $this->conn->fetchAll($sql, $params, $types);

        $values = [];
        foreach ($rs as $data) {
            $key = implode(
                $this->metadata->getSeparator(),
                $this->metadata->getId($data)
            );

            $values[$key] = $data;
        }

        if ($this->metadata->hasMetas() && !empty($values)) {
            $key = $this->metadata->getIdKeys()[0];

            $ids = array_map(function ($a) use ($key) {
                return $a[$key];
            }, $values);

            $metas = $this->getMetas($ids);

            // Merge values and metas
            foreach ($metas as $id => $meta) {
                $values[$id] = array_merge($values[$id], $meta);
            }
        }

        // Build entities from values
        $class    = 'Common\\ORM\\Entity\\' . $this->metadata->name;
        $entities = [];

        foreach ($values as $key => $value) {
            $entity = new $class($this->converter->objectifyStrict($value));
            $entity->refresh();

            $entities[$key] = $entity;
        }

        return $entities;
    }

    /**
     * Returns an array of metas grouped by entity id.
     *
     * @param array $ids The entity ids.
     *
     * @return array The array of metas.
     */
    protected function getMetas($ids)
    {
        $filters  = [];
        $metas    = [];
        $metaKeys = $this->metadata->getMetaKeys();
        $metaId   = array_pop($metaKeys);

        foreach ($ids as $id) {
            $filters[] = $metaId . '=' . $id;
        }

        $sql = 'select * from ' . $this->metadata->getMetaTable()
            . ' where ' . implode(' or ', $filters);

        $rs = $this->conn->fetchAll($sql);

        foreach ($rs as $value) {
            $metas[$value[$metaId]][$value['meta_key']] = $value['meta_value'];
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
