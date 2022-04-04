<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Api\Exception\GetItemException;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Checks and executes widget actions.
 */
class WidgetController extends Controller
{
    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.widget';

    /**
     * Get a widget and execute an action.
     *
     * @param Request $request The request object.
     * @param integer $id      The widget id.
     * @param string $action   The widget action.
     *
     * @return Response The response object.
     */
    public function executeAction(Request $request, $id, $action)
    {
        $response = new Response(
            _('Sorry, we were unable to complete your request'),
            400
        );

        $widget = $this->get('entity_repository')->find('Widget', $id);

        if (is_null($widget)) {
            return $response;
        }

        $widget = $this->get('frontend.renderer.widget')->getWidget($widget);

        if (!method_exists($widget, $action)) {
            return $response;
        }

        return $widget->{$action}($request);
    }

    /**
     * Renders a widget.
     *
     * @param Request $request The request object.
     *
     * @return Response The response with the widget html.
     */
    public function renderAction(Request $request)
    {
        try {
            $params        = $request->query->all();
            $widgetService = $this->get('api.service.widget');

            $oql = 'content_type_name="widget" ' .
                'and content_status=1 ' .
                'and in_litter=0 ' .
                'and class="%s" limit 1';

            $widget = array_key_exists('widget_id', $params) ?
                $widgetService->getItem($params['widget_id']) :
                $widgetService->getItemBy(sprintf($oql, $params['widget_name']));

            $type = $widget->widget_type;

            $widgetRenderer = $this->get('frontend.renderer.widget');

            $widget = $widgetRenderer->getWidget($widget, $params);

            if (empty($widget)) {
                return new Response();
            }

            $html = sprintf(
                '<div class="widget">%s</div>',
                $type === 'intelligentwidget'
                ? $widget->render()
                : $widget->body ?? ''
            );

            if (!$widget->isCacheable()) {
                return new Response($html, 200, [ 'x-cacheable' => false ]);
            }

            $headers = [
                'x-cacheable' => true,
                'x-tags'      => implode(',', $widget->getXTags()),
                'x-cache-for' => $widget->getXCacheFor()
            ];

            return new Response($html, 200, $headers);
        } catch (GetItemException $e) {
            return new Response();
        } catch (\Exception $e) {
            $this->get('logger')->error(sprintf('Error rendering widget: %s', $e->getMessage()));

            return new Response();
        }
    }
}
