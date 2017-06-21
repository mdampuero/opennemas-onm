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

class RegexReplaceFilter extends Filter
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

        return preg_replace(
            $pattern,
            $replacement,
            $str
        );
    }
}
