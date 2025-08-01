<?php

namespace Common\Core\Component\Filter;

use Opennemas\Data\Filter\Filter;

class YoutubeIdFilter extends Filter
{
    /**
     * Returns the video id for a Youtube URL.
     *
     * @param string $str The string to filter.
     *
     * @return string The youtube video id.
     */
    public function filter($str)
    {
        $pattern = '~(?:http|https|)(?::\/\/|)(?:www.|)(?:youtu\.be\
            /|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=|\/ytscreeni
            ngroom\?v=|\/feeds\/api\/videos\/|\/user\S*[^\w\-\s]|\S*
            [^\w\-\s]))([\w\-]{11})[a-z0-9;:@?&%=+\/\$_.-]*~i';

        preg_match($pattern, $str, $matches);

        if (!empty($matches)) {
            return $matches[1];
        }

        return false;
    }
}
