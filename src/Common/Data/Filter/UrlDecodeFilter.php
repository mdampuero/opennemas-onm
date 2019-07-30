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

class UrlDecodeFilter extends Filter
{
    /**
     * Decodes a URL-encoded string while it can be decoded.
     *
     * @param string $str The string to decode.
     *
     * @return The decoded string.
     */
    public function filter($str)
    {
        $decoded = urldecode($str);

        // Already decoded
        if ($str === $decoded) {
            return $str;
        }

        return $this->filter($decoded);
    }
}
