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
        $em = $this->get('menu_repository');
        $errors  = array();
        $success = array();
        $updated = array();

        $ids = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $content = $em->find($id);

                if (!is_null($content->id)) {
                    try {
                        $content->delete($id);
                        $updated[] = $id;
                    } catch (Exception $e) {
                        $errors[] = array(
                            'id'      => $id,
                            'message' => sprintf(_('Unable to delete the item with id "%d"'), $id),
                            'type'    => 'error'
                        );
                    }
                } else {
                    $errors[] = array(
                        'id'      => $id,
                        'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
                        'type'    => 'error'
                    );
                }
            }
        }

        if (count($updated) > 0) {
            $success[] = array(
                'id'      => $updated,
                'message' => sprintf(_('%d item(s) deleted successfully'), count($updated)),
                'type'    => 'success'
            );
        }

        return new JsonResponse(
            array(
                'messages' => array_merge($success, $errors)
            )
        );
    }

    /**
     * Deletes a menu.
     *
     * @param  Request      $request     The request object.
     * @return JsonResponse              The response object.
     */
    public function deleteAction($id)
    {
        $em       = $this->get('menu_repository');
        $messages = array();

        $menu = $em->find($id);

        if (!is_null($id)) {
            try {
                $menu->delete($id);
                $em->delete($id);

                $messages[] = array(
                    'id'      => $id,
                    'message' => _('Item deleted successfully'),
                    'type'    => 'success'
                );
            } catch (Exception $e) {
                $messages[] = array(
                    'id'      => $id,
                    'message' => sprintf(_('Unable to delete the item with id "%d"'), $id),
                    'type'    => 'error'
                );
            }
        } else {
            $messages[] = array(
                'id'      => $id,
                'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
            );
        }

        return new JsonResponse(
            array(
                'messages' => $messages,
            )
        );
    }

    /**
     * Returns a list of contents in JSON format.
     *
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function listAction(Request $request, $contentType = null)
    {
        $elementsPerPage = $request->query->getDigits('elements_per_page', 10);
        $page            = $request->query->getDigits('page', 1);
        $search          = $request->query->get('search');
        $sortBy          = $request->query->filter('sort_by', null, FILTER_SANITIZE_STRING);
        $sortOrder       = $request->query->filter('sort_order', 'asc', FILTER_SANITIZE_STRING);
        $order           = null;

        $em = $this->get('menu_repository');

        if ($sortBy) {
            $order = '`' . $sortBy . '` ' . $sortOrder;
        }

        $results = $em->findBy($search, $order, $elementsPerPage, $page);
        $results = \Onm\StringUtils::convertToUtf8($results);
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
