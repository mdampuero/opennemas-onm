<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core\Data\Mapper;

class ArrayDataMapper
{
    /**
     * Converts between database and object values if no custom conversion
     * exists.
     *
     * @param string $method The method name.
     * @param array  $params The method parameters.
     *
     * @return array The converted array.
     */
    public function __call($method, $params)
    {
        if (empty($params[0]) || !is_array($params[0])) {
            return [];
        }

        return $params[0];
    }

    /**
     * Converts an array to another array basing on a format.
     *
     * @param array $value  The array to convert.
     * @param array $params The format for the items in the array.
     *
     * @return array The converted array.
     */
    public function fromArray($value, $params = null)
    {
        if (empty($params) || !is_string($params[0])) {
            return $value;
        }

        $map = explode('=>', $params[0]);
        $key = $map[0];

        $values = [];
        foreach ($value as $v) {
            $values[$v[$key]] = $v;
        }

        return $values;
    }

    /**
     * Returns an array from a JSON string.
     *
     * @param string $value The array as JSON string.
     *
     * @return string The array.
     */
    public function fromArrayJson($value)
    {
        if (empty($value)) {
            return [];
        }

        return json_decode($value, true);
    }

    /**
     * Returns an empty array.
     *
     * @return array The empty array.
     */
    public function fromNull()
    {
        return [];
    }

    /**
     * Converts an array to a comma-separated string.
     *
     * @param string $value The comma-separated string.
     *
     * @return array The array.
     */
    public function fromSimpleArray($value)
    {
        if (empty($value)) {
            return [];
        }

        return explode(',', $value);
    }

    /**
     * Unserializes an array.
     *
     * @param string $value The serialized array.
     *
     * @return string The array.
     */
    public function fromString($value)
    {
        if (empty($value)) {
            return [];
        }

        return @unserialize($value);
    }

    /**
     * Unserializes an array.
     *
     * @param string $value The serialized array.
     *
     * @return string The array.
     */
    public function fromText($value)
    {
        return $this->fromString($value);
    }

    /**
     * Converts an array to a JSON string.
     *
     * @param array $value The array to convert.
     *
     * @return string The array as a JSON string.
     */
    public function toArrayJson($value)
    {
        if (empty($value) || !is_array($value)) {
            return null;
        }

        return json_encode($value);
    }

    /**
     * Converts an array to a comma-separated string.
     *
     * @param array $value The array to convert.
     *
     * @return string The comma-separated string.
     */
    public function toSimpleArray($value)
    {
        if (empty($value) || !is_array($value)) {
            return null;
        }

        return implode(',', $value);
    }

    /**
     * Serializes an array.
     *
     * @param array $value The array to serialize.
     *
     * @return string The serialized array.
     */
    public function toString($value)
    {
        if (empty($value) || !is_array($value)) {
            return null;
        }

        return @serialize($value);
    }

    /**
     * Serializes an array.
     *
     * @param array $value The array to serialize.
     *
     * @return string The serialized array.
     */
    public function toText($value)
    {
        return $this->toString($value);
    }
}
