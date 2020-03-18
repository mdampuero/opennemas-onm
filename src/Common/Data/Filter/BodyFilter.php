<?php

namespace Common\Data\Filter;

class BodyFilter extends Filter
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
        $needle = [
            "/([\r\n])+/",
            "/([\n]{1,})/",
            "/([\n]{2,})/",
            "/(\n)+/",
            "/\[caption.*?\].*?(<img.*?\/?>).*?\[\/caption\]/"
        ];

        $replacement = [ '</p><p>', '</p><p>', '<br>', '<br>', '${1}' ];

        return '<p>'. preg_replace($needle, $replacement, $str) . '</p>';
    }
}
