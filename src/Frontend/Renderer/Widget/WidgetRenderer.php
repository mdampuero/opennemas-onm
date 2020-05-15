<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Renderer\Widget;

use Frontend\Renderer\Renderer;

class WidgetRenderer extends Renderer
{
    /**
     * {@inheritDoc}
     */
    public function render($content, $params)
    {
        switch ($content->renderlet) {
            case 'html':
                $output = $content->content;
                break;

            case 'smarty':
                $output = $this->renderletSmarty($content);
                break;

            case 'intelligentwidget':
                $output = $this->renderletIntelligentWidget($content, $params);
                break;
            default:
                $output = '';
                break;
        }

        return "<div class=\"widget\">" . $output . "</div>";
    }

    /**
     * Renders a HTML wiget
     *
     * @return string the generated HTML
     *
     * @see resource.string.php Smarty plugin
     * @see resource.widget.php Smarty plugin
     */
    protected function renderletSmarty($content)
    {
        $resource = 'string:' . $content;
        $wgtTpl   = $this->container->get('core.template');

        // no caching
        $wgtTpl->caching       = 0;
        $wgtTpl->force_compile = true;

        $output = $wgtTpl->fetch($resource, [ 'widget' => $content ]);

        return $output;
    }


    /**
     * Renders an intelligent wiget
     *
     * @param array $params parameters for rendering the widget
     *
     * @return string the generated HTML
     */
    protected function renderletIntelligentWidget($content, $params = null)
    {
        $widget = $this->factoryWidget($content, $params);

        if (is_null($widget)) {
            return sprintf(_('Widget %s not available'), $content);
        }

        return $widget->render($params);
    }


    /**
     * Returns an instance for a widget
     *
     * @param array $params parameters for rendering the widget
     *
     * @return Object the widget instance
     */
    protected function factoryWidget($content, $params = null)
    {
        $widget = $content->content;

        if (empty($widget)) {
            return null;
        }

        $this->container->get('core.loader.widget')->loadWidget($widget);

        $class = 'Widget' . $widget;

        if (!class_exists($class)) {
            return null;
        }

        $class = new $class($widget);

        $class->parseParams($params);

        return $class;
    }
}
