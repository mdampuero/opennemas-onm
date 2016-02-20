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

class ObjectDataMapper
{
    /**
     * Unserializes an object.
     *
     * @param string $value The serialized object.
     *
     * @return string The object.
     */
    public function fromObject($value)
    {
        if (empty($value)) {
            return null;
        }

        return @unserialize($value);
    }

    /**
     * Serializes an object.
     *
     * @param array $value The object to serialize.
     *
     * @return string The serialized object.
     */
    public function toObject($value)
    {
        if (empty($value) || !is_object($value)) {
            return null;
        }

        return @serialize($value);
    }
}
