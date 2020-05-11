<?php

namespace Common\ORM\Database\Repository;

use Common\Cache\Core\Cache;
use Common\ORM\Core\Oql\Sql\SqlTranslator;
use Common\ORM\Database\Data\Converter\BaseConverter;
use Common\ORM\Core\Connection;
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
     * @param string     $name     The repository name.
     * @param Connection $conn     The database connection.
     * @param Metadata   $metadata The entity metadata.
     * @param Cache      $cache    The cache service.
     */
    public function __construct($name, Connection $conn, Metadata $metadata, Cache $cache = null)
    {
        $this->cache      = $cache;
        $this->conn       = $conn;
        $this->converter  = new BaseConverter($metadata);
        $this->metadata   = $metadata;
        $this->name       = $name;
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

        $rs = $this->conn->fetchArray($sql, $params, $types);

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

        $isComposited = $this->isCompositeKey($id);

        $entities = $this->getEntities(!is_array($id) || $isComposited ? [ $id ] : $id);

        if (($isComposited || !is_array($id))
            && (empty($entities[0])
                || $entities[0] === $this->miss)
        ) {
            throw new EntityNotFoundException($this->metadata->name, $id);
        }

        return is_array($id) && !$isComposited
            ? $entities
            : array_pop($entities);
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
     * @param string $sql The SQL query.
     *
     * @return array The list of entities.
     */
    public function findBySql($sql)
    {
        $keys = $this->metadata->getIdKeys();
        $rs   = $this->conn->fetchAll($sql);

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
        if (empty($ids)) {
            return [];
        }

        $ids = array_map(function ($id) {
            return $this->metadata->normalizeId($id);
        }, $ids);

        // Prefix ids
        $prefixedIds = array_map(function ($a) {
            return $this->metadata->getPrefixedId($a);
        }, $ids);

        $entities = [];
        $keys     = [];
        if ($this->hasCache()) {
            $entities = $this->cache->get($prefixedIds);
            $miss     = $this->miss;
            $entities = array_filter($entities, function ($a) use ($miss) {
                return $miss !== $a;
            });

            $keys = array_keys($entities);
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

            $missed = array_diff($missed, array_keys($entities));

            if ($this->hasCache() && !empty($missed)) {
                $this->cache->set(array_fill_keys($missed, $this->miss));
            }
        }

        $index = array_flip($prefixedIds);

        // Remove missing entities from index
        $index = array_intersect_key($index, $entities);

        // Keep original order
        return array_values(array_merge($index, $entities));
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
     * Returns an array of metas grouped by entity id.
     *
     * @param array $ids The entity ids.
     *
     * @return array The array of metas.
     */
    protected function getMetas($ids)
    {
        $metas     = [];
        $metaKeys  = $this->metadata->getMetaKeys();
        $metaKey   = $this->metadata->getMetaKeyName();
        $metaValue = $this->metadata->getMetaValueName();
        $metaId    = array_pop($metaKeys);

        $sql = 'select * from ' . $this->metadata->getMetaTable()
            . ' where ' . $metaId . ' in (' . implode(',', $ids) . ')';

        $rs = $this->conn->fetchAll($sql);

        foreach ($rs as $value) {
            $metas[$value[$metaId]][$value[$metaKey]] = $value[$metaValue];
        }

        return $metas;
    }

    /**
     * Returns the OQL to find contents by primary key when the primary key is
     * composited.
     *
     * @param array $ids The list of ids.
     *
     * @return string The OQL.
     */
    protected function getOqlForCompositeKey($ids)
    {
        $oql = [];

        foreach ($ids as $id) {
            $filters = [];

            foreach ($id as $key => $value) {
                $filters[] = "$key = $value";
            }

            $oql[] = "(" . implode(' and ', $filters) . ")";
        }

        return implode(' or ', $oql);
    }

    /**
     * Returns the OQL to find contents by primary key when the primary key is
     * simple.
     *
     * @param array $ids The list of ids.
     *
     * @return string The OQL.
     */
    protected function getOqlForSimpleKey($ids)
    {
        foreach ($ids as $id) {
            foreach ($id as $key => $value) {
                $filter[$key][] = $value;
            }
        }

        foreach ($filter as $key => $value) {
            $oql[] = $key . ' in [' . implode(',', $value) . ']';
        }

        return implode(' or ', $oql);
    }

    /**
     * Returns an array of data for all relations by entity id.
     *
     * @param array $ids The entity ids.
     *
     * @return type Description
     */
    protected function getRelations($ids)
    {
        $relations = $this->metadata->getRelations();
        $returnMap = [];

        foreach ($relations as $name => $relation) {
            $table = $relation['table'];
            $rid   = $relation['target_key'];
            $sql   = 'select * from ' . $table
                . ' where ' . $rid . ' in (' . implode(',', $ids) . ')';

            $rs = $this->conn->fetchAll($sql);

            foreach ($rs as $value) {
                if (!array_key_exists('return_fields', $relation)) {
                    $returnMap[$value[$rid]][$name][] = $value;
                    continue;
                }

                $returnMap[$value[$rid]][$name][] = !is_array($relation['return_fields'])
                    ? $value[$relation['return_fields']]
                    : array_intersect_key($value, array_flip($relation['return_fields']));
            }
        }

        return $returnMap;
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
     * Checks if the provided id is composited.
     *
     * @param array $id The id to check.
     *
     * @return True if the id is composited. False otherwise.
     */
    protected function isCompositeKey($id)
    {
        if (!is_array($id)) {
            return false;
        }

        foreach (array_keys($id) as $key) {
            if (!is_numeric($key)) {
                return true;
            }
        }

        return false;
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
        $oql = count($ids[0]) > 1
            ? $this->getOqlForCompositeKey($ids)
            : $this->getOqlForSimpleKey($ids);

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

            // Set relations to empty
            foreach ($values as $id => $value) {
                $values[$id] = array_merge(
                    $values[$id],
                    array_fill_keys(
                        array_keys($this->metadata->getRelations()),
                        []
                    )
                );
            }

            $metas     = $this->getMetas($ids);
            $relations = $this->getRelations($ids);

            // Merge values and metas
            foreach ($metas as $id => $meta) {
                $values[$id] = array_merge($values[$id], $meta);
            }

            foreach ($relations as $id => $relation) {
                $values[$id] = array_merge($values[$id], $relation);
            }
        }

        // Build entities from values
        $class    = $this->metadata->class;
        $entities = [];

        foreach ($values as $key => $value) {
            $entity = new $class($this->converter->objectifyStrict($value));
            $entity->refresh();
            $entity->setOrigin($this->name);

            $entities[$key] = $entity;
        }

        return $entities;
    }
}
