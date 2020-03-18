<?php

namespace Common\Data\Filter;

class LiteralFilter extends Filter
{
    /**
     * Returns the literal value given in filter parameters.
     *
     * @para string The value to filter.
     *
     * @return string The literal value.
     */
    public function filter($str)
    {
        if (!$this->getParameter('value')) {
            return false;
        }

        return $this->getParameter('value');
    }
}
