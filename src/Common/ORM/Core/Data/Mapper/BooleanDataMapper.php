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

class BooleanDataMapper
{
    /**
     * Converts between database and object values if no custom conversion
     * exists.
     *
     * @param string $method The method name.
     * @param array  $params The method parameters.
     *
     * @return boolean The converted value.
     */
    public function __call($method, $params)
    {
        if (empty($params[0])) {
            return false;
        }

        return true;
    }

    /**
     * Unserializes an boolean.
     *
     * @param string $value The serialized boolean.
     *
     * @return string The boolean.
     */
    public function fromInteger($value)
    {
        if (empty($value)) {
            return false;
        }

        return $value === 1;
    }

    /**
     * Returns false.
     *
     * @return boolean False.
     */
    public function fromNull()
    {
        return false;
    }

    /**
     * Unserializes an boolean.
     *
     * @param string $value The serialized boolean.
     *
     * @return string The boolean.
     */
    public function fromString($value)
    {
        if (empty($value) || $value === '0' || $value === 'false') {
            return false;
        }

        return $value === '1' || $value === 'true';
    }

    /**
     * Serializes an boolean.
     *
     * @param array $value The boolean to serialize.
     *
     * @return string The serialized boolean.
     */
    public function toBoolean($value)
    {
        if (empty($value) || !is_bool($value)) {
            return false;
        }

        return true;
    }

    /**
     * Unserializes an boolean.
     *
     * @param string $value The serialized boolean.
     *
     * @return string The boolean.
     */
    public function toInteger($value)
    {
        if (empty($value)) {
            return 0;
        }

        return 1;
    }

    /**
     * Unserializes an boolean.
     *
     * @param string $value The serialized boolean.
     *
     * @return string The boolean.
     */
    public function toString($value)
    {
        if (empty($value)) {
            return '0';
        }

        return '1';
    }
}
