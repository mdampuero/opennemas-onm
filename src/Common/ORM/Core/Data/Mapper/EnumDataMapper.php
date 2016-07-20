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

class EnumDataMapper
{
    /**
     * Converts between database and object values if no custom conversion
     * exists.
     *
     * @param string $method The method name.
     * @param array  $params The method parameters.
     *
     * @return string The converted string.
     */
    public function __call($method, $params)
    {
        if (empty($params)
            || empty($params[0])
            || is_array($params[0])
            || is_object($params[0])
        ) {
            return null;
        }

        return (string) $params[0];
    }
}
