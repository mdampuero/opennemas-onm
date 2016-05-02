<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core;

use Framework\Component\Data\DataObject;
use Common\ORM\Core\Exception\InvalidConverterException;
use Common\ORM\Core\Exception\InvalidPersisterException;
use Common\ORM\Core\Exception\InvalidRepositoryException;
use Common\ORM\Core\Validation\Validable;

/**
 * The Metadata class defines an Entity from data model.
 */
class Metadata extends DataObject implements Validable
{
    /**
     * Returns the prefixed id for an entity.
     *
     * @param Entity $entity The entity.
     *
     * @return string The prefixed id.
     */
    public function getPrefixedId(Entity $entity)
    {
        return $this->getPrefix() . implode('_', $this->getId($entity));
    }

    /**
     * Returns the prefix for the current entity.
     *
     * @return string The prefix.
     */
    public function getPrefix()
    {
        if (!empty($this->prefix)) {
            return $this->prefix . $this->getSeparator();
        }

        return \underscore($this->name) . $this->getSeparator();
    }

    /**
     * Returns the separator for the current entity.
     *
     * @return string The separator.
     */
    public function getSeparator()
    {
        if (!empty($this->separator)) {
            return $this->separator;
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
     * Returns the configuration for converter.
     *
     * @param string $converter The converter name.
     *
     * @return array The converter configuration.
     *
     * @throws InvalidConverterException If the converter does not exists.
     */
    public function getConverter($converter = null)
    {
        if (!array_key_exists('converters', $this->mapping)) {
            throw new InvalidConverterException($this->name, $converter);
        }

        if (empty($converter)) {
            $converter = array_keys($this->mapping['converters']);
            $converter = array_pop($converter);
        }

        if (array_key_exists($converter, $this->mapping['converters'])) {
            return $this->mapping['converters'][$converter];
        }

        throw new InvalidConverterException($this->name, $converter);
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
            return [];
        }

        foreach ($this->mapping['index'] as $index) {
            if (array_key_exists('primary', $index) && $index['primary']) {
                return $index['columns'];
            }
        }

        return [];
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
            && !empty($this->mapping['metas']['ids'])
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
     * Returns the configuration for persister.
     *
     * @param string $persister The persister name.
     *
     * @return array The persister configuration.
     *
     * @throws InvalidPersisterException If the persister does not exists.
     */
    public function getPersister($persister = null)
    {
        if (!array_key_exists('persisters', $this->mapping)) {
            throw new InvalidPersisterException($this->name);
        }

        if (empty($persister)) {
            $persister = array_keys($this->mapping['persisters']);
            $persister = array_pop($persister);
        }

        if (array_key_exists($persister, $this->mapping['persisters'])) {
            return $this->mapping['persisters'][$persister];
        }

        throw new InvalidPersisterException($this->name, $persister);
    }

    /**
     * Returns the configuration for repository.
     *
     * @param string $repository The repository name.
     *
     * @return array The repository configuration.
     *
     * @throws InvalidRepositoryException If the repository does not exists.
     */
    public function getRepository($repository = null)
    {
        if (!array_key_exists('repositories', $this->mapping)) {
            throw new InvalidRepositoryException($this->name);
        }

        if (empty($repository)) {
            $repository = array_keys($this->mapping['repositories']);
            $repository = array_pop($repository);
        }

        if (array_key_exists($repository, $this->mapping['repositories'])) {
            return $this->mapping['repositories'][$repository];
        }

        throw new InvalidRepositoryException($this->name, $repository);
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
