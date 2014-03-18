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
use Onm\Message as m;

class MenusController extends Controller
{
    /**
     * Deletes multiple menus at once give them ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('MENU_DELETE')")
     */
    public function batchDeleteAction(Request $request)
    {
        $status  = 'OK';
        $errors  = array();
        $success = array();

        $ids = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            $em = $this->get('menu_repository');

            try {
                foreach ($ids as $id) {
                    $menu = new \Menu($id);

                    if (!is_null($menu->pk_menu) && $menu->type == 'user') {
                        $em->delete($id);
                        $success[] = sprintf(_('Menu "%s" deleted successfully.'), $menu->name);
                    } else {
                        $errors[] = sprintf(_('Unable to delete the menu "%s" as is system internal.'), $menu->name);
                    }
                }
            } catch (Exception $e) {
                // Continue
            }
        }

        return new JsonResponse(array('status' => $status, 'errors' => $errors, 'success' => $success));
    }

    /**
     * Deletes a menu.
     *
     * @param  integer      $id Menu id.
     * @return JsonResponse     The response of the current action.
     *
     * @Security("has_role('MENU_DELETE')")
     */
    public function deleteAction($id)
    {
        $status  = 'ERROR';
        $message = _('You must give an id for delete the menu.');

        if ($id) {
            try {
                $em = $this->get('menu_repository');
                $em->delete($id);

                $status  = 'OK';
                $message = sprintf(_("Menu '%s' deleted successfully."), $id);
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
     * @Security("has_role('MENU_ADMIN')")
     */
    public function listAction(Request $request)
    {
        $results = array();

        $elementsPerPage = $request->request->getDigits('elements_per_page', 10);
        $page            = $request->request->getDigits('page', 1);
        $search          = $request->request->get('search');
        $sortBy          = $request->request->filter('sort_by', null, FILTER_SANITIZE_STRING);
        $sortOrder       = $request->request->filter('sort_order', 'asc', FILTER_SANITIZE_STRING);
        $order           = null;

        if ($sortBy) {
            $order = '`' . $sortBy . '` ' . $sortOrder;
        }

        $em      = $this->get('menu_repository');
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
}
