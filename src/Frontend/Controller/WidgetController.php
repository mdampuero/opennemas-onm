<?php
/**
 * Handles the actions for a widget
 *
 * @package Frontend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions to execute for a widget
 *
 * @package Frontend_Controllers
 **/
class WidgetController extends Controller
{
    /**
     * Get a widget and execute an action
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function executeAction(Request $request)
    {
        // Get url parameters
        $action = $request->query->filter('action', '', FILTER_SANITIZE_STRING);
        $id = $request->query->getDigits('id', 0);

        $response = new Response(
            _('Sorry, we were unable to complete your request'),
            400
        );

        // Verify parameters
        if (empty($id) || empty($action)) {
            return $response;
        }

        // Get widget object
        $widget = $this->get('entity_repository')->find('Widget', $id);

        // Check if widget exists
        if (is_null($widget)) {
            return $response;
        }

        // Get widget instance
        $widget = $widget->factoryWidget();

        // Validate widget action
        if (!method_exists($widget, $action)) {
            return $response;
        }

        // Execute widget action with data from the request
        return $widget->{$action}($request);
    }
}
