<?php

namespace Common\Data\Filter;

class ReplaceFilter extends Filter
{
    /**
     * Replaces a string with another string.
     *
     * @param string $str The string to filter.
     *
     * @return string The string after replace.
     */
    public function filter($str)
    {
        $pattern     = $this->getParameter('pattern');
        $replacement = $this->getParameter('replacement');

        if (empty($pattern)) {
            return $str;
        }

        return str_replace(
            $pattern,
            $replacement,
            $str
        );
    }
}
