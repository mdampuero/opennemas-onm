<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Filter;

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
