<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Data\Filter;

class MapifyFilter extends Filter
{
    /**
     * Converts an array of object to an associative array of objects where key
     * is a property of the object.
     *
     * @param array $list The list of objects to convert.
     *
     * @return array The converted array.
     */
    public function filter($list)
    {
        $property = $this->getParameter('key');
        $map      = [];

        foreach ($list as $item) {
            $map[$item->{$property}] = $item;
        }

        return $map;
    }
}
