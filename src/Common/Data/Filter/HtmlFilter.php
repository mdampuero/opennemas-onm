<?php

namespace Common\Data\Filter;

class HtmlFilter extends Filter
{
    /**
     * Converts a string to valid HTML paragraphs.
     *
     * @param string $str The string to filter.
     *
     * @return string The converted string.
     */
    public function filter($str)
    {
        return htmlentities($str, ENT_IGNORE, 'UTF-8');
    }
}
