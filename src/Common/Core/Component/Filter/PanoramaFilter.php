<?php

namespace Common\Core\Component\Filter;

use Opennemas\Data\Filter\Filter;
use Panorama\Video;

class PanoramaFilter extends Filter
{
    /**
     * Returns the video information using Panorama library.
     *
     * @param string $str The video URL.
     *
     * @return string The video information.
     */
    public function filter($str)
    {
        try {
            $params = $this->getParameter('panorama');
            $video  = new Video($str, $params);
            $data   = $video->getVideoDetails();

            return serialize($data);
        } catch (\Exception $e) {
        }

        return null;
    }
}
