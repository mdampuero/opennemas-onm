<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core\Data\Converter;

use Common\ORM\Core\Entity;
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
     * Convert database values to valid entity values.
     *
     * @param array $source The data from database.
     *
     * @return array The converted data.
     */
    public function objectify($source)
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

            if (array_key_exists($key, $this->metadata->properties)) {
                $params = explode('::', $this->metadata->properties[$key]);
                $to     = \classify(array_shift($params));
            }

            $data[$key] = $this->convertFrom($to, $from, $value, $params);
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

            if ($value instanceof Entity) {
                $data[$key] = $value->getData();
            }

            if ($value instanceof \Datetime) {
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
     * @param mixed  $params The parameters for conversion.
     *
     * @return type Description
     */
    protected function convert($mapper, $method, $value, $params = [])
    {
        $mapper = 'Common\\ORM\\Core\\Data\\Mapper\\'
            . ucfirst(strtolower($mapper)) . 'DataMapper';

        $mapper = new $mapper($this->metadata);

        return $mapper->{$method}($value, $params);
    }

    /**
     * Converts a value from type using a data mapper.
     *
     * @param string $mapper The data mapper name.
     * @param string $type   The type to convert from.
     * @param mixed  $value  The value to convert.
     * @param mixed  $params The parameters for conversion.
     *
     * @return mixed The converted value.
     */
    protected function convertFrom($mapper, $type, $value, $params = [])
    {
        $method = 'from' . ucfirst($type);

        return $this->convert($mapper, $method, $value, $params);
    }

    /**
     * Converts a value to type using a data mapper.
     *
     * @param string $mapper The data mapper name.
     * @param string $type   The type to convert to.
     * @param mixed  $value  The value to convert.
     * @param mixed  $params The parameters for conversion.
     *
     * @return mixed The converted value.
     */
    protected function convertTo($mapper, $type, $value, $params = [])
    {
        $method = 'to' . ucfirst($type);

        return $this->convert($mapper, $method, $value, $params);
    }
}
