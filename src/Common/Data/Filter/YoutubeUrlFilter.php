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

class YoutubeUrlFilter extends Filter
{
    /**
     * Returns the video URL for a youtube id.
     *
     * @param string $str The string to filter.
     *
     * @return string The youtube video URL.
     */
    public function filter($str)
    {
        if (!is_string($str)) {
            return false;
        }

        return 'http://www.youtube.com/watch?v=' . $str;
    }
}
