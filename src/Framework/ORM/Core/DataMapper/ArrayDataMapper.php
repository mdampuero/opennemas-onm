<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\ORM\Core\DataMapper;

class ArrayDataMapper
{
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
}
