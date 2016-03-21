<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Database\Data\Mapper;

class BooleanDataMapper
{
    /**
     * Unserializes an boolean.
     *
     * @param string $value The serialized boolean.
     *
     * @return string The boolean.
     */
    public function fromBoolean($value)
    {
        if (empty($value)) {
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
        if (empty($value) || $value === '0') {
            return false;
        }

        return $value === '1';
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
