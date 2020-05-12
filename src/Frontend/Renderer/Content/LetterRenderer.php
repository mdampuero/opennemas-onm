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

class LetterRenderer extends ContentRenderer
{
    /**
     * Return the template and the params needed to render the letter.
     *
     * @param Array The array of parameters.
     *
     * @return Array An array with the template and the needed parameters.
     */
    protected function getTemplate($params)
    {
        return [ 'frontpage/contents/_content.tpl', $params ];
    }
}
