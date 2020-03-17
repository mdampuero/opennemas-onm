<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Data\Mapper;

class StringDataMapper
{
    /**
     * Converts between database and object values if no custom conversion
     * exists.
     */
    public function __call($method, $params)
    {
        if (empty($params) || empty($params[0])) {
            return null;
        }

        return mb_convert_encoding(
            (string) $params[0],
            'UTF-8',
            mb_detect_encoding((string) $params[0])
        );
    }
}
