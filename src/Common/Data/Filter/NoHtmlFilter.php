<?php

namespace Common\Data\Filter;

class NoHtmlFilter extends Filter
{
    /**
     * Remove HTML tags from string.
     *
     * @param string $str The string to filter.
     *
     * @return string The converted string.
     */
    public function filter($str)
    {
        return strip_tags(html_entity_decode($str));
    }
}
