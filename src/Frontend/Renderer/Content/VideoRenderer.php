<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Renderer\Content;

class VideoRenderer extends ContentRenderer
{
    /**
     * Return the template and the params needed to render the video.
     *
     * @param Array The array of parameters.
     *
     * @return Array An array with the template and the needed parameters.
     */
    protected function getTemplate($params)
    {
        $template = 'frontpage/contents/_video.tpl';

        if ($params['custom'] == 1) {
            $template = $params['tpl'];
        }

        return [ $template, $params ];
    }
}
