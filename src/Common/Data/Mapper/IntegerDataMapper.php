<?php

namespace Common\Data\Mapper;

class IntegerDataMapper
{
    /**
     * Converts between database and object values if no custom conversion
     * exists.
     */
    public function __call($method, $params)
    {
        if (empty($params) || is_null($params[0]) || $params[0] === '') {
            return null;
        }

        return (int) $params[0];
    }
}
