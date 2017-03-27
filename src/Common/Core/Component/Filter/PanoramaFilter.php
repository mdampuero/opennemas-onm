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
