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

class BodyFilter extends MigrationFilter
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
            "/(\n)+/"
        ];

        $replacement = [ '</p><p>', '</p><p>', '<br>', '<br>' ];

        return '<p>'. preg_replace($needle, $replacement, $str) . '</p>';
    }
}
