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
        $tpl            = $this->container->get('core.template');
        $renderer       = $this->getRendererClass($content);
        $params['item'] = $content;

        try {
            if (!empty($renderer)) {
                list($template, $params) = $renderer->getTemplate($params);
                return $tpl->fetch($template, $params);
            }

            return $tpl->fetch('frontpage/contents/_' . strtolower(get_class($content)) . '.tpl', $params);
        } catch (\Exception $e) {
            getService('error.log')->error(
                $e->getMessage()
            );

            return _('Content not available');
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function getRendererClass($content)
    {
        $class     = get_class($content) . 'Renderer';
        $classPath = __NAMESPACE__ . '\\Content\\' . $class;

        if (class_exists($classPath)) {
            return new $classPath($this->container);
        }

        return null;
    }
}
