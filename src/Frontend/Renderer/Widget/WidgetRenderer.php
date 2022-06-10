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

use Common\Model\Entity\Content;
use Frontend\Renderer\Renderer;

class WidgetRenderer extends Renderer
{
    /**
     * Renders the esi code for the widget.
     *
     * @param Content $widget The widget to render the esi code.
     * @param array   $params The parameters to render the widget.
     *
     * @return string The esi code for the widget.
     */
    public function render($widget, $params)
    {
        $id     = [ 'widget_id' => $widget->pk_content ];
        $params = array_merge($id, array_filter($params, function ($param) {
            return is_string($param);
        }));

        $params = array_map(function ($param) {
            return urlencode($param);
        }, $params);

        $url = $this->container->get('router')->generate('frontend_widget_render', $params);

        return sprintf('<esi:include src="%s" />', $url);
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
        $class->hydrateShow();

        return $class;
    }
}
