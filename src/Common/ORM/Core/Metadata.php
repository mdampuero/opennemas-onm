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
use Common\ORM\Core\Exception\InvalidDataSetException;
use Common\ORM\Core\Exception\InvalidPersisterException;
use Common\ORM\Core\Exception\InvalidRepositoryException;
use Common\ORM\Core\Validation\Validable;

/**
 * The Metadata class defines an Entity from data model.
 */
class Metadata extends DataObject implements Validable
{
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
        if (empty($this->converters)) {
            throw new InvalidConverterException($this->name, $converter);
        }

        if (empty($converter)) {
            $converter = array_keys($this->converters);
            $converter = array_shift($converter);
        }

        if (array_key_exists($converter, $this->converters)) {
            return $this->converters[$converter];
        }

        throw new InvalidConverterException($this->name, $converter);
    }

    /**
     * Returns the configuration for dataset.
     *
     * @param string $dataset The dataset name.
     *
     * @return array The dataset configuration.
     *
     * @throws InvalidDataSetException If the dataset does not exists.
     */
    public function getDataSet($dataset = null)
    {
        if (empty($this->datasets)) {
            throw new InvalidDataSetException($this->name);
        }

        $dataset = $this->getDataSetName($dataset);

        if (array_key_exists($dataset, $this->datasets)) {
            return $this->datasets[$dataset];
        }

        throw new InvalidDataSetException($this->name, $dataset);
    }

    /**
     * Returns the key name for the data set.
     *
     * @return string The key name.
     */
    public function getDataSetKey()
    {
        if (!array_key_exists('database', $this->mapping)
            || !array_key_exists('dataset', $this->mapping['database'])
            || !array_key_exists('key', $this->mapping['database']['dataset'])
            || empty($this->mapping['database']['dataset']['key'])
        ) {
            return 'name';
        }

        return $this->mapping['database']['dataset']['key'];
    }

    /**
     * Returns the dataset name basing on the parameters.
     *
     * @param string $dataset The dataset name.
     *
     * @return string The valid dataset name.
     */
    public function getDataSetName($dataset = null)
    {
        if (empty($dataset) && $this->datasets) {
            $dataset = array_keys($this->datasets);
            $dataset = array_shift($dataset);
        }

        return $dataset;
    }

    /**
     * Returns the value name for the data set.
     *
     * @return string The value name.
     */
    public function getDataSetValue()
    {
        if (!array_key_exists('database', $this->mapping)
            || !array_key_exists('dataset', $this->mapping['database'])
            || !array_key_exists('value', $this->mapping['database']['dataset'])
            || empty($this->mapping['database']['dataset']['value'])
        ) {
            return 'value';
        }

        return $this->mapping['database']['dataset']['value'];
    }

    /**
     * Returns the entity id.
     *
     * @param mixed $entity The entity object or entity data.
     *
     * @return string The entity id.
     */
    public function getId($entity)
    {
        if (is_object($entity)) {
            $entity = $entity->getData();
        }

        return array_intersect_key($entity, array_flip($this->getIdKeys()));
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
        if (!array_key_exists('database', $this->mapping)
            || !array_key_exists('index', $this->mapping['database'])
            || empty($this->mapping['database']['index'])
        ) {
            return [];
        }

        foreach ($this->mapping['database']['index'] as $index) {
            if (array_key_exists('primary', $index) && $index['primary']) {
                return $index['columns'];
            }
        }

        return [];
    }

    /**
     * Returns the list of properties defined as l10n_string.
     *
     * @return array The list of properties defined as l10n_string.
     */
    public function getL10nKeys()
    {
        if (empty($this->properties)) {
            return [];
        }

        return array_keys(array_filter($this->properties, function ($a) {
            return $a === 'l10n_string';
        }));
    }

    /**
     * Returns the meta key name.
     *
     * @return string The meta key name.
     */
    public function getMetaKeyName()
    {
        if ($this->hasMetas()
            && array_key_exists('key', $this->mapping['database']['metas'])
        ) {
            return $this->mapping['database']['metas']['key'];
        }

        return 'meta_key';
    }

    /**
     * Returns an array with the correspondence between the keys of the entity
     * table and the keys of the table of metas.
     *
     * @return array Array of key correspondences.
     */
    public function getMetaKeys()
    {
        if ($this->hasMetas()
            && array_key_exists('ids', $this->mapping['database']['metas'])
        ) {
            return $this->mapping['database']['metas']['ids'];
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
     * @return string The name of the table of metas.
     */
    public function getMetaTable()
    {
        if ($this->hasMetas()
            && array_key_exists('table', $this->mapping['database']['metas'])
        ) {
            return $this->mapping['database']['metas']['table'];
        }

        return $this->getTable() . '_meta';
    }

    /**
     * Returns the list of all columns in relations.
     *
     * @return array The list of all columns in relations.
     */
    public function getRelationColumns()
    {
        if (!$this->hasRelations()) {
            return [];
        }

        $columns = [];

        foreach ($this->mapping['database']['relations'] as $relation) {
            if (array_key_exists('columns', $relation)) {
                $columns = array_merge($columns, array_keys($relation['columns']));
            }
        }

        return $columns;
    }

    /**
     * Returns the list of relations.
     *
     * @return array The list of relations.
     */
    public function getRelations()
    {
        if ($this->hasRelations()) {
            return $this->mapping['database']['relations'];
        }

        return [];
    }

    /**
     * Returns the meta value name.
     *
     * @return string The meta value name.
     */
    public function getMetaValueName()
    {
        if ($this->hasMetas()
            && array_key_exists('value', $this->mapping['database']['metas'])
        ) {
            return $this->mapping['database']['metas']['value'];
        }

        return 'meta_value';
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
        if (empty($this->persisters)) {
            throw new InvalidPersisterException($this->name);
        }

        if (empty($persister)) {
            $persister = array_keys($this->persisters);
            $persister = array_shift($persister);
        }

        if (array_key_exists($persister, $this->persisters)) {
            return $this->persisters[$persister];
        }

        throw new InvalidPersisterException($this->name, $persister);
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
     * Returns the prefixed id for an entity.
     *
     * @param mixed $entity The entity object or entity data.
     *
     * @return string The prefixed id.
     */
    public function getPrefixedId($entity)
    {
        return $this->getPrefix()
            . implode($this->getSeparator(), $this->getId($entity));
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
        if (empty($this->repositories)) {
            throw new InvalidRepositoryException($this->name);
        }

        if (empty($repository)) {
            $repository = array_keys($this->repositories);
            $repository = array_shift($repository);
        }

        if (array_key_exists($repository, $this->repositories)) {
            return array_merge(
                [ 'name' => $repository ],
                $this->repositories[$repository]
            );
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
        if (array_key_exists('database', $this->mapping)
            && array_key_exists('table', $this->mapping['database'])
        ) {
            return $this->mapping['database']['table'];
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
        return array_key_exists('database', $this->mapping)
            && array_key_exists('metas', $this->mapping['database'])
            && !empty($this->mapping['database']['metas']);
    }

    /**
     * Checks if the current entity has relations with other entities.
     *
     * @return boolean True if the current entity has metas. Otherwise, returns
     *                 false.
     */
    public function hasRelations()
    {
        return array_key_exists('database', $this->mapping)
            && array_key_exists('relations', $this->mapping['database'])
            && !empty($this->mapping['database']['relations']);
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
        $definedKeys = $this->getIdKeys();

        $keys = !is_array($id) ? $definedKeys : array_keys($id);
        $id   = !is_array($id) ? [ $id ] : $id;

        // Keep order defined in metadata
        return array_merge(array_flip($definedKeys), array_combine($keys, $id));
    }
}
