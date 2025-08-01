<?php

namespace Common\Core\Component\Filter;

use Opennemas\Data\Filter\Filter;

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
