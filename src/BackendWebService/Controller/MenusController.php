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

class MenusController extends ContentController
{
    /**
     * Deletes multiple menus at once give them ids
     *
     * @param  Request      $request     The request object.
     * @return JsonResponse              The response object.
     */
    public function batchDeleteAction(Request $request)
    {
        $em      = $this->get('menu_repository');
        $errors  = [];
        $success = [];
        $updated = [];

        $ids = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $content = $em->find($id);

                if (!is_null($content->id)) {
                    try {
                        $content->delete($id);
                        $updated[] = $id;
                    } catch (Exception $e) {
                        $errors[] = [
                            'id'      => $id,
                            'message' => sprintf(_('Unable to delete the item with id "%d"'), $id),
                            'type'    => 'error'
                        ];
                    }
                } else {
                    $errors[] = [
                        'id'      => $id,
                        'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
                        'type'    => 'error'
                    ];
                }
            }
        }

        if (count($updated) > 0) {
            $success[] = [
                'id'      => $updated,
                'message' => sprintf(_('%d item(s) deleted successfully'), count($updated)),
                'type'    => 'success'
            ];
        }

        return new JsonResponse([
            'messages' => array_merge($success, $errors)
        ]);
    }

    /**
     * Deletes a menu.
     *
     * @param  Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function deleteAction($id)
    {
        $em       = $this->get('menu_repository');
        $messages = [];

        $menu = $em->find($id);

        if (!is_null($id)) {
            try {
                $menu->delete($id);
                $em->delete($id);

                $messages[] = [
                    'id'      => $id,
                    'message' => _('Item deleted successfully'),
                    'type'    => 'success'
                ];
            } catch (Exception $e) {
                $messages[] = [
                    'id'      => $id,
                    'message' => sprintf(_('Unable to delete the item with id "%d"'), $id),
                    'type'    => 'error'
                ];
            }
        } else {
            $messages[] = [
                'id'      => $id,
                'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
            ];
        }

        return new JsonResponse([ 'messages' => $messages ]);
    }

    /**
     * Returns a list of contents in JSON format.
     *
     * @param Request $request     The request object.
     * @param string  $contentType Content type name.
     *
     * @return JsonResponse The response object.
     */
    public function listAction(Request $request, $contentType = null)
    {
        $oql = $request->query->get('oql', '');
        $em  = $this->get('menu_repository');

        list($criteria, $order, $epp, $page) =
            $this->get('core.helper.oql')->getFiltersFromOql($oql);

        $results = $em->findBy($criteria, $order, $epp, $page);
        $total   = $em->countBy($criteria);

        foreach ($results as &$result) {
            $result->items = $result->getRawItems();
        }

        $results = \Onm\StringUtils::convertToUtf8($results);

        return new JsonResponse([
            'results' => $results,
            'total'   => $total
        ]);
    }
}
