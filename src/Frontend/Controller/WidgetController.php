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

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Checks and executes widget actions.
 */
class WidgetController extends Controller
{
    /**
     * Get a widget and execute an action.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function executeAction($id, $action)
    {
        $response = new Response(
            _('Sorry, we were unable to complete your request'),
            400
        );

        $widget = $this->get('entity_repository')->find('Widget', $id);

        if (is_null($widget)) {
            return $response;
        }

        $widget = $widget->factoryWidget();

        if (!method_exists($widget, $action)) {
            return $response;
        }

        return $widget->{$action}($request);
    }
}
