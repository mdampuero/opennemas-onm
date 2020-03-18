<?php

namespace Common\Data\Mapper;

class NullDataMapper
{
    /**
     * Converts between database and object values if no custom conversion
     * exists.
     */
    public function __call($method, $params)
    {
        return null;
    }

    /**
     * Converts null to an empty array.
     *
     * @return array Empty array.
     */
    public function toArray()
    {
        return [];
    }

    /**
     * Converts null to an empty integer.
     *
     * @return integer Empty integer (0).
     */
    public function toInteger()
    {
        return 0;
    }

    /**
     * Converts null to an empty string.
     *
     * @return integer Empty string.
     */
    public function toString()
    {
        return '';
    }
}
