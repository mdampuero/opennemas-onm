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

use Api\Exception\GetItemException;
use Frontend\Renderer\Renderer;

class ContentRenderer extends Renderer
{
    /**
     * {@inheritDoc}
     */
    public function render($content, $params)
    {
        $tpl            = $this->container->get('core.template');
        $params['item'] = $content;

        try {
                $template = $this->getTemplate($params);
                return $tpl->fetch($template, $params);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error(
                $e->getMessage()
            );

            return _('Content not available');
        }
    }

    /**
     * Returns the specific template.
     *
     * @param Array     The array of parameters.
     */
    protected function getTemplate(&$params)
    {
        $contentType   = $params['item']->content_type_name;
        $default = 'frontpage/contents/_' . $contentType . '.tpl';

        if ($contentType == 'article' && !empty($params['tpl'])) {
            return $params['tpl'];
        }

        if ($contentType == 'opinion') {
            try {
                $author = $this->container->get('api.service.author')->getItem($params['item']->fk_author);

                if ($author->is_blog == 1) {
                    return 'frontpage/contents/_blog.tpl';
                }
            } catch (GetItemException $e) {
            }
        }

        if ($contentType == 'letter') {
            return 'frontpage/contents/_content.tpl';
        }

        return $default;
    }
}
