<?php

namespace Common\Data\Filter;

class DateFilter extends Filter
{
    /**
     * Converts a string to a date given a format.
     *
     * @param string $str    The string to convert.
     *
     * @return string The converted string.
     */
    public function filter($str)
    {
        $format    = $this->getParameter('format', 'Y-m-d H:i:s');
        $timestamp = $this->getParameter('timestamp');

        if (!$timestamp) {
            $str = strtotime($str);
        }

        return date($format, $str);
    }
}
