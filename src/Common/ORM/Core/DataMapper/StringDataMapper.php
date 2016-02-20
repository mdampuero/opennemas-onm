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

class StringDataMapper
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

        return (string) $params[0];
    }
}
