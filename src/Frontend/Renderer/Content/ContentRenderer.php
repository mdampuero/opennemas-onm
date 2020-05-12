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
     * Initializes the content renderer.
     *
     * @param Container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function render($content, $params)
    {
        $tpl            = getService('core.template');
        $default        = 'frontpage/contents/_' . strtolower(get_class($content)) . '.tpl';
        $params['item'] = $content;

        try {
            if ($params['tpl']) {
                return $tpl->fetch($params['tpl'], $params);
            }

            return $tpl->fetch($default, $params);
        } catch (\Exception $e) {
            getService('error.log')->error(
                $e->getMessage()
            );

            return _('Content not available');
        }
    }
}
