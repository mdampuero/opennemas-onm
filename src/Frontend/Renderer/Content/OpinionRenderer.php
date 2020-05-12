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

class OpinionRenderer extends ContentRenderer
{
    /**
     * Return the template and the params needed to render the opinion.
     *
     * @param Array The array of parameters.
     *
     * @return Array An array with the template and the needed parameters.
     */
    protected function getTemplate($params)
    {
        $author = new \User($params['item']->fk_author);

        $params['item']->name             = \Onm\StringUtils::getTitle($author->name);
        $params['item']->author_name_slug = $params['item']->name;

        if (array_key_exists('is_blog', $author->meta) && $author->meta['is_blog'] == 1) {
            $template = 'frontpage/contents/_blog.tpl';

            if ($params['custom'] == 1) {
                $template = $params['tpl'];
            }

            return [ $template, $params ];
        }

        $params['cssclass'] = 'opinion';
        $template           = 'frontpage/contents/_opinion.tpl';

        if ($params['custom'] == 1) {
            $template = $params['tpl'];
        }

        return [ $template, $params ];
    }
}
