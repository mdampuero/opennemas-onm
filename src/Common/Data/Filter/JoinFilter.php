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

        if (is_array($str) && count($str) > 0) {
            return implode($glue, $str);
        }

        return $str;
    }
}
