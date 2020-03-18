<?php

namespace Common\Data\Filter;

class JoinFilter extends Filter
{
    /**
     * Converts a string basing on a map.
     *
     * @param string $str The string to convert.
     *
     * @return string The converted string.
     */
    public function filter($str)
    {
        $glue = $this->getParameter('glue', ',');

        if (is_array($str) && !empty($str)) {
            return implode($glue, $str);
        }

        return $str;
    }
}
