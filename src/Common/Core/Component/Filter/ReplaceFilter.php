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

class ReplaceFilter extends Filter
{
    /**
     * Replaces a string with another string.
     *
     * @param string $str  The string to filter.
     * @param array  $args The arguments for the filter.
     *
     * @return string The string after replace.
     */
    public function filter($str, $args = [])
    {
        return preg_replace(
            '/' . preg_quote($args['pattern'], '/') . '/',
            $args['replacement'],
            $str
        );
    }
}
