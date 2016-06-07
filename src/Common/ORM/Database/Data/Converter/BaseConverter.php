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

/**
 * The Converter class converts entity data before and after persisting them to
 * the database.
 */
class BaseConverter extends Converter
{
    /**
     * Convert entity data to valid database values.
     *
     * @param array $source The entity data.
     *
     * @return array The converted data and metas.
     */
    public function databasify($source)
    {
        if (empty($this->metadata->mapping)
            || !array_key_exists('database', $this->metadata->mapping)
            || !array_key_exists('columns', $this->metadata->mapping['database'])) {
            throw new \Exception();
        }

        $mapping = $this->metadata->mapping['database'];
        $data    = [];
        foreach ($source as $key => $value) {
            $from = $this->metadata->properties[$key];
            $to   = 'String';

            if (array_key_exists($key, $mapping['columns'])) {
                $to = \classify($mapping['columns'][$key]['type']);
            }

            $mapper = 'Common\\ORM\\Core\\Data\\Mapper\\'
                . ucfirst(strtolower($from)) . 'DataMapper';

            $mapper = new $mapper();

            $data[$key] = $this->convertTo($from, $to, $value);
        }

        // Null non-present values
        $missing = array_diff(
            array_keys($this->metadata->properties),
            array_keys($data)
        );

        foreach ($missing as $key) {
            $data[$key] = null;

            if (array_key_exists($key, $mapping['columns'])
                && array_key_exists('default', $mapping['columns'][$key]['options'])
            ) {
                $data[$key] = $mapping['columns'][$key]['options']['default'];
            }
        }

        // Meta keys (unknown properties)
        $unknown = array_diff(
            array_keys($data),
            array_keys($this->metadata->mapping['database']['columns'])
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

        return [ $data, $metas, $types ];
    }

    /**
     * Convert database values to valid entity values.
     *
     * @param array $source The data from database.
     *
     * @return array The converted data.
     */
    public function objectifyStrict($source)
    {
        if (empty($this->metadata->mapping)
            || !array_key_exists('database', $this->metadata->mapping)
            || !array_key_exists('columns', $this->metadata->mapping['database'])
        ) {
            return $source;
        }

        $data = [];
        foreach ($source as $key => $value) {
            $from = \classify(strtolower(gettype($value)));
            $to   = 'String';
            if (array_key_exists($key, $this->metadata->properties)) {
                $to = \classify($this->metadata->properties[$key]);
            }

            if (array_key_exists($key, $this->metadata->mapping['database']['columns'])) {
                $from = \classify($this->metadata->mapping['database']['columns'][$key]['type']);
            }

            $data[$key] = $this->convertFrom($to, $from, $value);
        }

        return $data;
    }
}
