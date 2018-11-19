<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Database\Data\Converter;

use Common\ORM\Core\Data\Converter\Converter;
use Common\ORM\Core\Entity;

/**
 * The BaseConverter class converts entity data before and after persisting them
 * to the database.
 */
class BaseConverter extends Converter
{
    /**
     * Converts entity data or an array of entities data to valid database
     * values.
     *
     * @param mixed $data The data to convert.
     *
     * @return array The converted data.
     *
     * @throws \Exception
     */
    public function databasify($source)
    {
        if (empty($source)) {
            return [ [], [], [], [] ];
        }

        if ($this->isArray($source)) {
            return $this->mDatabasify($source);
        }

        return $this->sDatabasify($source);
    }

    /**
     * Converts database values or an array of database values to valid entity
     * values basing on mapping information.
     *
     * @param mixed $data The data to convert.
     *
     * @return array The converted data.
     */
    public function objectifyStrict($source)
    {
        if ($this->isArray($source)) {
            return $this->mObjectifyStrict($source);
        }

        return $this->sObjectifyStrict($source);
    }

    /**
     * Converts an array of entities data to array of database values.
     *
     * @param array $items The items to convert.
     *
     * @return array The converted items.
     *
     * @throws \Exception
     */
    protected function mDatabasify($items)
    {
        foreach ($items as &$item) {
            $item = $this->sDatabasify($item);
        }

        return $items;
    }

    /**
     * Converts an array of database values to an array of entity values basing
     * on mapping information.
     *
     * @param array $items The items to convert.
     *
     * @return array The converted items.
     */
    protected function mObjectifyStrict($items)
    {
        foreach ($items as &$item) {
            $item = $this->sObjectifyStrict($item);
        }

        return $items;
    }

    /**
     * Convert entity data to valid database values.
     *
     * @param mixed $source The entity or entity data.
     *
     * @return array The converted data and metas.
     *
     * @throws \Exception
     */
    protected function sDatabasify($source)
    {
        if (empty($this->metadata->mapping)
            || !array_key_exists('database', $this->metadata->mapping)
            || !array_key_exists('columns', $this->metadata->mapping['database'])
        ) {
            throw new \Exception();
        }

        if ($source instanceof Entity) {
            $source = $source->getData();
        }

        $mapping = $this->metadata->mapping['database'];
        $data    = [];
        foreach ($source as $key => $value) {
            $from   = strtolower(gettype($value));
            $to     = 'String';
            $params = [];

            if (array_key_exists($key, $this->metadata->properties)) {
                $params = explode('::', $this->metadata->properties[$key]);
                $from   = array_shift($params);
            }

            if (array_key_exists($key, $mapping['columns'])) {
                $to = \classify($mapping['columns'][$key]['type']);
            }

            if (array_key_exists($key, $this->metadata->getRelations())) {
                $to = 'array';
            }

            $data[$key] = $this->convertTo($from, $to, $value, $params);
        }

        // Null non-present values
        $missing = array_diff(
            array_keys($this->metadata->properties),
            array_keys($data)
        );

        foreach ($missing as $key) {
            $data[$key] = null;

            if (array_key_exists($key, $mapping['columns'])
                && array_key_exists('options', $mapping['columns'][$key])
                && array_key_exists('default', $mapping['columns'][$key]['options'])
            ) {
                $data[$key] = $mapping['columns'][$key]['options']['default'];
            }
        }

        $relations = array_intersect_key(
            $data,
            $this->metadata->getRelations()
        );

        $data = array_diff_key($data, $this->metadata->getRelations());

        // Meta keys (unknown properties)
        $unknown = array_diff(
            array_keys($data),
            array_keys($this->metadata->mapping['database']['columns']),
            array_keys($this->metadata->getRelations())
        );

        $metas = array_intersect_key($data, array_flip($unknown));
        $data  = array_diff_key($data, array_flip($unknown));
        $types = array_map(function ($a) {
            if (is_bool($a)) {
                return \PDO::PARAM_BOOL;
            }

            if (is_integer($a)) {
                return \PDO::PARAM_INT;
            }

            return \PDO::PARAM_STR;
        }, $data);

        return [ $data, $metas, $types, $relations ];
    }

    /**
     * Convert database values to valid entity values.
     *
     * @param array $source The data from database.
     *
     * @return array The converted data.
     */
    protected function sObjectifyStrict($source)
    {
        if (empty($this->metadata->mapping)
            || !array_key_exists('database', $this->metadata->mapping)
            || !array_key_exists('columns', $this->metadata->mapping['database'])
        ) {
            return $source;
        }

        $data = [];
        foreach ($source as $key => $value) {
            $from   = \classify(strtolower(gettype($value)));
            $to     = 'String';
            $params = [];

            if ($from === 'Object') {
                $from = \classify(strtolower(get_class($value)));
            }

            if (array_key_exists($key, $this->metadata->properties)) {
                $params = explode('::', $this->metadata->properties[$key]);
                $to     = \classify(array_shift($params));
            }

            if (array_key_exists($key, $this->metadata->mapping['database']['columns'])) {
                $from = \classify($this->metadata->mapping['database']['columns'][$key]['type']);
            }

            $data[$key] = $this->convertFrom($to, $from, $value, $params);
        }

        return $data;
    }
}
