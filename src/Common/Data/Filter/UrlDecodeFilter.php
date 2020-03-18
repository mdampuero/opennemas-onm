<?php

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
