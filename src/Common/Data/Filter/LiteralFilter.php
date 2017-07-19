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
