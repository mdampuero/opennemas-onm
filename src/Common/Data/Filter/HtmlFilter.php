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
