<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Migration\Filter;

class DateFilter extends MigrationFilter
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
