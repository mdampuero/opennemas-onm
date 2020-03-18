<?php

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
            if (is_array($item)) {
                $map[$item[$property]] = $item;

                continue;
            }

            $map[$item->{$property}] = $item;
        }

        return $map;
    }
}
