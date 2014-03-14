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

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;

class MenusController extends Controller
{
    /**
     * Returns a list of contents in JSON format.
     *
     * @param  Request      $request The request with the search parameters.
     * @return JsonResponse          The response in JSON format.
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

        foreach ($results as &$menu) {
            $menu->editUrl   = $this->generateUrl('admin_menu_show', array('id' => $menu->pk_menu));
            $menu->deleteUrl = $this->generateUrl('backend_ws_menu_delete', array('id' => $menu->pk_menu));
            $menu->selected  = 0;
        }

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
     * Deletes a menu.
     *
     * @param  integer      $id Menu id.
     * @return JsonResponse     The response of the current action.
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
}
