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

class Utf8Filter extends Filter
{
    /**
     * Returns the string converted to UTF-8 encoding.
     *
     * @param string $str The string to filter.
     *
     * @return string The UTF-8 encoded string.
     */
    public function filter($str)
    {
        return utf8_encode($str);
    }
}
