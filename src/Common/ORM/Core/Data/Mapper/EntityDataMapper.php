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

class EntityDataMapper
{
    /**
     * Unserializes an object.
     *
     * @param string $value  The serialized object.
     * @param array  $params The conversion parameters.
     *
     * @return string The object.
     */
    public function fromString($value, $params = [])
    {
        if (empty($value)) {
            return null;
        }

        $class = 'Common\\ORM\\Entity\\' . ucfirst(strtolower($params[0]));
        $data  = @unserialize($value);

        return new $class($data);
    }

    /**
     * Unserializes an object.
     *
     * @param string $value  The serialized object.
     * @param array  $params The conversion parameters.
     *
     * @return string The object.
     */
    public function fromText($value, $params = [])
    {
        return $this->fromString($value, $params);
    }

    /**
     * Serializes an object.
     *
     * @param array $value The object to serialize.
     *
     * @return string The serialized object.
     */
    public function toString($value)
    {
        if (empty($value) || !is_object($value)) {
            return null;
        }

        return @serialize($value->getData());
    }

    /**
     * Serializes an object.
     *
     * @param array $value The object to serialize.
     *
     * @return string The serialized object.
     */
    public function toText($value)
    {
        return $this->toString($value);
    }
}
