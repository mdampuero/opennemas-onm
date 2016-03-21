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
            $from = $this->metadata->properties[$key];
            $to   = 'String';

            if (array_key_exists($key, $this->metadata->mapping['columns'])) {
                $to = \classify($this->metadata->mapping['columns'][$key]['type']);
            }

            $mapper = 'Common\\ORM\\Database\\Data\\Mapper\\'
                . ucfirst(strtolower($from)) . 'DataMapper';

            $mapper = new $mapper();
            $method = 'to' . ucfirst($to);

            $data[$key] = $this->convertTo($from, $to, $value);
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
     * @param array  $source The data from database.
     * @param strict $strict Whether to perform a strict conversion basing on
     *                       metadata.
     *
     * @return array The converted data.
     */
    public function objectify($source, $strict = false)
    {
        if (!array_key_exists('columns', $this->metadata->mapping)) {
            return $source;
        }

        $data = [];
        foreach ($source as $key => $value) {
            $data[$key] = $value;

            $from = \classify(strtolower(gettype($value)));
            $to   = 'String';
            if (array_key_exists($key, $this->metadata->properties)) {
                $to = \classify($this->metadata->properties[$key]);
            }

            if ($strict && array_key_exists($key, $this->metadata->mapping['columns'])) {
                $from = \classify($this->metadata->mapping['columns'][$key]['type']);
            }

            if ($from !== $to) {
                $data[$key] = $this->convertFrom($to, $from, $value);
            }
        }

        return $data;
    }

    /**
     * Converts object values to response values.
     *
     * @param array $data The data from object.
     *
     * @return array The converted data.
     */
    public function responsify($source)
    {
        $data = [];
        foreach ($source as $key => $value) {
            $data[$key] = $value;

            if ($value instanceOf Entity) {
                $data[$key] = $this->responsify($value->getData());
            }

            if ($value instanceOf \Datetime) {
                $data[$key] = $value->format('Y-m-d H:i:s');
            }

            if (is_bool($value)) {
                $data[$key] = $value ? 1 : 0;
            }
        }

        return $data;
    }

    /**
     * Converts a value calling the given method from a data mapper.
     *
     * @param string $mapper The data mapper name.
     * @param string $method The method to use to convert the value.
     * @param mixed  $value  The value to convert.
     *
     * @return type Description
     */
    protected function convert($mapper, $method, $value)
    {
        $mapper = 'Common\\ORM\\Database\\Data\\Mapper\\'
            . ucfirst(strtolower($mapper)) . 'DataMapper';

        $mapper = new $mapper($this->metadata);

        return $mapper->{$method}($value);
    }

    /**
     * Converts a value from type using a data mapper.
     *
     * @param string $mapper The data mapper name.
     * @param string $type   The type to convert from.
     * @param mixed  $value  The value to convert.
     *
     * @return mixed The converted value.
     */
    protected function convertFrom($mapper, $type, $value)
    {
        $method = 'from' . ucfirst($type);

        return $this->convert($mapper, $method, $value);
    }

    /**
     * Converts a value to type using a data mapper.
     *
     * @param string $mapper The data mapper name.
     * @param string $type   The type to convert to.
     * @param mixed  $value  The value to convert.
     *
     * @return mixed The converted value.
     */
    protected function convertTo($mapper, $type, $value)
    {
        $method = 'to' . ucfirst($type);

        return $this->convert($mapper, $method, $value);
    }
}
