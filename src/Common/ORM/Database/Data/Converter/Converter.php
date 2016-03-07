<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Database\Data\Converter;

use Common\ORM\Core\Metadata;

/**
 * The Converter class converts entity data before and after persisting them to
 * the database.
 */
class Converter
{
    /**
     * Initializes the Converter.
     *
     * @param Metadata $metadata The entity metadata.
     */
    public function __construct(Metadata $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * Convert entity data to valid database values.
     *
     * @param array $source The entity data.
     *
     * @return array The converted data and metas.
     */
    public function databasify($source)
    {
        if (!array_key_exists('columns', $this->metadata->mapping)) {
            throw new \Exception();
        }

        $data = [];
        foreach ($source as $key => $value) {
            $from = gettype($value);
            $to   = 'string';

            if (array_key_exists($key, $this->metadata->properties)
                && $this->metadata->properties[$key] !== 'enum'
            ) {
                $from = $this->metadata->properties[$key];
            }

            if (array_key_exists($key, $this->metadata->mapping['columns'])) {
                $to = \classify($this->metadata->mapping['columns'][$key]['type']);
            }

            if (empty($to) && $from === 'array') {
                $to = 'array';
            }

            $mapper = 'Common\\ORM\\Database\\Data\\Mapper\\'
                . ucfirst(strtolower($from)) . 'DataMapper';

            $mapper = new $mapper();
            $method = 'to' . ucfirst($to);

            $data[$key] = $mapper->{$method}($value);
        }

        // Meta keys (unknown properties)
        $unknown = array_diff(
            array_keys($data),
            array_keys($this->metadata->mapping['columns'])
        );

        $metas = array_intersect_key($data, array_flip($unknown));
        $data  = array_diff_key($data, array_flip($unknown));

        return [ $data, $metas ];
    }

    /**
     * Convert database values to valid entity values.
     *
     * @param array $source The data from database.
     *
     * @return array The converted data.
     */
    public function objectify($source)
    {
        if (!array_key_exists('columns', $this->metadata->mapping)) {
            return $source;
        }

        $data = [];
        foreach ($source as $key => $value) {
            if (array_key_exists($key, $this->metadata->properties)) {
                $from = \classify(gettype($value));
                $to   = $this->metadata->properties[$key];

                if (array_key_exists($key, $this->metadata->mapping['columns'])) {
                    $from = \classify($this->metadata->mapping['columns'][$key]['type']);
                }

                $mapper = 'Common\\ORM\\Database\\Data\\Mapper\\'
                    . ucfirst(strtolower($to)) . 'DataMapper';

                $mapper = new $mapper($this->metadata);
                $method = 'from' . ucfirst($from);

                $data[$key] = $mapper->{$method}($value);
            }
        }

        return $data;
    }
}
