<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BackendWebService\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;

class WidgetsController extends ContentController
{
    /**
     * Deletes multiple widgets at once give them ids
     *
     * @param  Request $request the request object
     * @return Response         the response object
     *
     * @Security("has_role('WIDGET_DELETE')")
     */
    public function batchDeleteAction(Request $request)
    {
        $status  = 'OK';
        $errors  = array();
        $success = array();

        $ids = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $widget = new \Widget($id);

                if (!is_null($widget->id)) {
                    try {
                        $widget->remove($id);
                        $success[] = _('Widget deleted successfully.');
                    } catch (Exception $e) {
                        $errors[] = sprintf(_('Unable to delete the widget "%s".'), $widget->name);
                    }
                }
            }
        }

        return new JsonResponse(array('status' => $status, 'errors' => $errors, 'success' => $success));
    }

    /**
     * Updates widgets available property.
     *
     * @param  Request $request the request object
     * @return Response         the response object
     *
     * @Security("has_role('WIDGET_AVAILABLE')")
     */
    public function batchToggleAvailableAction(Request $request)
    {
        $status  = 'OK';
        $errors  = array();
        $success = array();

        $ids       = $request->request->get('ids');
        $available = $request->request->get('available');

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $widget = new \Widget($id);

                if (!is_null($widget->id)) {
                    try {
                        $widget->set_available($available, $_SESSION['userid']);
                        $success[] = sprintf(_('Successfully changed availability for "%s" widget'), $widget->title);
                    } catch (Exception $e) {
                        $errors[] = sprintf(_('Unable to change the widget availability for "%s" widget'), $widget->name);
                    }
                }
            }
        }

        return new JsonResponse(array('status' => $status, 'errors' => $errors, 'success' => $success));
    }

    /**
     * Deletes a widget.
     *
     * @param  integer      $id Menu id.
     * @return JsonResponse     The response of the current action.
     *
     * @Security("has_role('WIDGET_DELETE')")
     */
    public function deleteAction($id)
    {
        $status  = 'ERROR';
        $message = _('You must give an id for delete the widget.');

        $widget = new \Widget($id);

        if (!is_null($id)) {
            try {
                $widget->remove($id);

                $status  = 'OK';
                $message = _('Widget deleted successfully.');
            } catch (Exception $e) {
                // Continue
            }
        }

        return new JsonResponse(array('status' => $status, 'message' => $message));
    }

    /**
     * Returns a list of contents in JSON format.
     *
     * @param  Request      $request The request with the search parameters.
     * @return JsonResponse          The response in JSON format.
     *
     * @Security("has_role('WIDGET_ADMIN')")
     */
    public function listAction(Request $request)
    {
        $elementsPerPage = $request->request->getDigits('elements_per_page', 10);
        $page            = $request->request->getDigits('page', 1);
        $search          = $request->request->get('search');
        $sortBy          = $request->request->filter('sort_by', null, FILTER_SANITIZE_STRING);
        $sortOrder       = $request->request->filter('sort_order', 'asc', FILTER_SANITIZE_STRING);

        $em = $this->get('widget_repository');

        $order = null;
        if ($sortBy) {
            $order = '`' . $sortBy . '` ' . $sortOrder;
        }

        $results = $em->findBy($search, $order, $elementsPerPage, $page);
        $total   = $em->countBy($search);

        return new JsonResponse(
            array(
                'elements_per_page' => $elementsPerPage,
                'page'              => $page,
                'results'           => $results,
                'total'             => $total
            )
        );
    }

    /**
     * Toggles widget availability.
     *
     * @param  integer      $id Widget id.
     * @return JsonResponse     The response of the current action.
     *
     * @Security("has_role('WIDGET_AVAILABLE')")
     */
    public function toggleAvailableAction($id)
    {
        $status  = 'ERROR';
        $message = _('You must give an id for delete the widget.');

        $em     = $this->get('entity_repository');
        $widget = $em->find(\classify('widget'), $id);

        if (!$widget) {
            $message = sprintf(_('Unable to find widget with id "%d"'), $id);
        } else {
            $widget->toggleAvailable();

            $status  = 'OK';
            $message = sprintf(_('Successfully changed availability for "%s" widget'), $widget->title);
        }

        return new JsonResponse(
            array(
                'status' => $status,
                'message' => $message,
                'available' => $widget->available
            )
        );
    }
}
