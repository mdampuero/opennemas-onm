<?php

namespace Common\Data\Filter;

class Utf8Filter extends Filter
{
    /**
     * Returns the string converted to UTF-8 encoding.
     *
     * @param string $str The string to filter.
     *
     * @return string The UTF-8 encoded string.
     */
    public function filter($str)
    {
        return utf8_encode($str);
    }
}
