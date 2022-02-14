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
    public function render($widget, $params)
    {
        return sprintf(
            "<div class=\"widget\">%s</div>",
            $widget->widget_type === 'intelligentwidget'
            ? $this->renderletIntelligentWidget($widget, $params)
            : $widget->body ?? ''
        );
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
        $widget = $this->getWidget($content, $params);

        if (is_null($widget)) {
            return sprintf(_('Widget %s not available'), $content->class);
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
    public function getWidget($content, $params = null)
    {
        $widget = $content->class;

        if (empty($widget)) {
            return null;
        }

        $this->container->get('core.loader.widget')->loadWidget($widget);

        $class = 'Widget' . $widget;

        if (!class_exists($class)) {
            return null;
        }

        $class = new $class($content);

        $class->parseParams($params);

        return $class;
    }
}
