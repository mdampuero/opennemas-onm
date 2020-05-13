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
    public function getTemplate(&$params)
    {
        $class              = strtolower(get_class($params['item']));
        $default            = 'frontpage/contents/_' . $class . '.tpl';
        $params['cssclass'] = $class;

        if ($class == 'article' && !empty($params['tpl'])) {
            return $params['tpl'];
        }

        if ($class == 'opinion') {
            $author = new \User($params['item']->fk_author);

            if (array_key_exists('is_blog', $author->meta) && $author->meta['is_blog'] == 1) {
                return 'frontpage/contents/_blog.tpl';
            }
        }

        if ($class == 'letter') {
            return 'frontpage/contents/_content.tpl';
        }

        return $default;
    }
}
