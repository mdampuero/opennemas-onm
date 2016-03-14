<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core;

use Framework\Component\Data\DataObject;
use Common\ORM\Core\Validation\Validable;

/**
 * The Metadata class defines an Entity from data model.
 */
class Metadata extends DataObject implements Validable
{
    /**
     * Returns the cache id for an entity.
     *
     * @param Entity $entity The entity.
     *
     * @return string The cache id.
     */
    public function getCacheId(Entity $entity)
    {
        return $this->getCachePrefix()
            . implode('_', $this->getId($entity));
    }

    /**
     * Returns the cache prefix for the current entity.
     *
     * @return string The cache prefix.
     */
    public function getCachePrefix()
    {
        if (!empty($this->cachePrefix)) {
            return $this->cachePrefix . $this->getCacheSeparator();
        }

        return \underscore($this->name) . $this->getCacheSeparator();
    }

    /**
     * Returns the cache separator for the current entity.
     *
     * @return string The cache separator.
     */
    public function getCacheSeparator()
    {
        if (!empty($this->cacheSeparator)) {
            return $this->cacheSeparator;
        }

        return '-';
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        return 'Metadata';
    }

    /**
     * Returns the entity id.
     *
     * @param Entity $entity The entity.
     *
     * @return string The entity id.
     */
    public function getId(Entity $entity)
    {
        return array_intersect_key(
            $entity->getData(),
            array_flip($this->getIdKeys())
        );
    }

    /**
     * Returns the key names for the current entity.
     *
     * @param type variable Description
     *
     * @return type Description
     */
    public function getIdKeys()
    {
        if (!array_key_exists('index', $this->mapping)
            || empty($this->mapping['index'])
        ) {
            return false;
        }

        foreach ($this->mapping['index'] as $index) {
            if (array_key_exists('primary', $index) && $index['primary']) {
                return $index['columns'];
            }
        }

        return false;
    }

    /**
     * Returns an array with the correspondence between the keys of the entity
     * table and the keys of the table of metas.
     *
     * @return array Array of key correspondences.
     */
    public function getMetaKeys()
    {
        if (array_key_exists('metas', $this->mapping)
            && array_key_exists('ids', $this->mapping['metas'])
        ) {
            return $this->mapping['metas']['ids'];
        }

        $keys = [];
        foreach ($this->getIdKeys() as $key) {
            $keys[$key] = $this->getTable() . '_' . $key;

        }

        return $keys;
    }

    /**
     * Returns the name of the table of metas.
     *
     * @return string The name of the table of metas
     */
    public function getMetaTable()
    {
        if (array_key_exists('metas', $this->mapping)
            && array_key_exists('table', $this->mapping['metas'])
        ) {
            return $this->mapping['metas']['table'];
        }

        return $this->getTable() . '_meta';
    }

    /**
     * Returns the name of the table of metas.
     *
     * @return string The name of the table of metas
     */
    public function getTable()
    {
        if (array_key_exists('table', $this->mapping)) {
            return $this->mapping['table'];
        }

        return \underscore($this->name);
    }

    /**
     * Checks if the current entity has metas.
     *
     * @return boolean True if the current entity has metas. Otherwise, returns
     *                 false.
     */
    public function hasMetas()
    {
        return array_key_exists('metas', $this->mapping)
            && !empty($this->mapping['metas']);
    }

    /**
     * Returns the normalized id.
     *
     * @param mixed $id The entity id as string or array.
     *
     * @return array The normalized id.
     */
    public function normalizeId($id)
    {
        $keys = !is_array($id) ? $this->getIdKeys() : array_keys($id);
        $id   = !is_array($id) ? [ $id ] : $id;

        return array_combine($keys, $id);
    }
}
