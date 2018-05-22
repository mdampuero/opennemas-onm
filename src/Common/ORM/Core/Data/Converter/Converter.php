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
    public function __construct(Metadata $metadata, $locale = null)
    {
        $this->locale   = $locale;
        $this->metadata = $metadata;
    }

    /**
     * Converts data or an array of data to valid entity values.
     *
     * @param mixed $data The data to convert.
     *
     * @return array The converted data.
     */
    public function objectify($source)
    {
        if ($this->isArray($source)) {
            return $this->mObjectify($source);
        }

        return $this->sObjectify($source);
    }

    /**
     * Converts an entity or an array of entities to response values.
     *
     * @param mixed $data The data to convert.
     *
     * @return array The converted data.
     */
    public function responsify($source, $translate = false)
    {
        if ($this->isArray($source)) {
            return $this->mResponsify($source, $translate);
        }

        return $this->sResponsify($source, $translate);
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
            . \classify($mapper) . 'DataMapper';

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

    /**
     * Checks if the data are an array of entities.
     *
     * @param mixed $data The data to check.
     *
     * @return boolean True if the data are an array. False, otherwise.
     */
    protected function isArray($data)
    {
        if (is_object($data)
            || !is_array($data)
            || empty($this->metadata->properties)
        ) {
            return false;
        }

        $keys       = array_keys($data);
        $properties = array_keys($this->metadata->properties);

        // Some properties are recognized
        if (count(array_diff($properties, $keys)) < count($properties)) {
            return false;
        }

        return true;
    }

    /**
     * Converts an array database values to an array of entity values.
     *
     * @param array $items The items to convert.
     *
     * @return array The converted items.
     */
    protected function mObjectify($items)
    {
        foreach ($items as &$item) {
            $item = $this->sObjectify($item);
        }

        return $items;
    }

    /**
     * Converts an array of entities to response values.
     *
     * @param array   $items     The items to convert.
     * @param boolean $translate Whether to translate the properties.
     *
     * @return array The converted items.
     */
    protected function mResponsify($items, $translate)
    {
        foreach ($items as &$item) {
            $item = $this->sResponsify($item, $translate);
        }

        return $items;
    }

    /**
     * Convert database values to valid entity values.
     *
     * @param array $source The data from database.
     *
     * @return array The converted data.
     */
    protected function sObjectify($source)
    {
        if (!is_array($source)
            || empty($this->metadata->mapping)
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

            $data[$key] = $this->convertFrom($to, $from, $value, $params);
        }

        return $data;
    }

    /**
     * Converts entity values to response values.
     *
     * @param array   $data      The data from entity.
     * @param boolean $translate Whether to translate the properties.
     *
     * @return array The converted data.
     */
    protected function sResponsify($source, $translate)
    {
        if ($source instanceof Entity) {
            $source = $source->getData();
        }

        $data = [];
        foreach ($source as $key => $value) {
            $data[$key] = $value;

            if ($translate && !empty($this->locale)) {
                $data[$key] = $this->translate($key, $value);
            }

            if (is_null($value)
                && array_key_exists($key, $this->metadata->properties)
                && $this->metadata->properties[$key] === 'array'
            ) {
                $data[$key] = [];
                continue;
            }

            if ($value instanceof Entity) {
                $data[$key] = $value->getData();
                continue;
            }

            if ($value instanceof \Datetime) {
                $data[$key] = $value->format('Y-m-d H:i:s');
                continue;
            }

            if (is_bool($value)) {
                $data[$key] = $value ? 1 : 0;
                continue;
            }
        }

        return $data;
    }

    /**
     * Returns the translated value of the property.
     *
     * @param string $key   The property name.
     * @param string $value The property value.
     *
     * @return mixed The translated value.
     */
    protected function translate($key, $value)
    {
        if (!is_array($value) || !in_array($key, $this->metadata->translate)) {
            return $value;
        }

        if (array_key_exists($this->locale->getLocaleShort(), $value)) {
            return $value[$this->locale->getLocaleShort()];
        }

        return array_values($value)[0];
    }
}
