<?php
/**
 * Handles the actions for the keywords
 *
 * @package Backend_Controllers
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BackendWebService\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for the keywords
 *
 * @package Backend_Controllers
 */
class KeywordsController extends Controller
{
    /**
     * Lists all the keywords
     *
     * @param Request $request the request object
     *
     * @return Response
     *
     * @Security("hasExtension('KEYWORD_MANAGER')
     *     and hasPermission('PCLAVE_ADMIN')")
     */
    public function listAction(Request $request)
    {
        $oql = $request->query->get('oql', '');

        list($criteria, $order, $epp, $page) =
            $this->get('core.helper.oql')->getFiltersFromOql($oql);

        $km       = new \PClave();
        $keywords = $km->find($criteria, $order, $epp, $page);

        return new JsonResponse([
            'extra'   => [ 'types' => \PClave::getTypes() ],
            'results' => $keywords,
            'total'   => count($keywords),
        ]);
    }

    /**
     * Deletes a content.
     *
     * @param  integer      $id          Content id.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     *
     * @Security("hasExtension('KEYWORD_MANAGER')
     *     and hasPermission('PCLAVE_DELETE')")
     */
    public function deleteAction($id)
    {
        $errors  = [];
        $success = [];

        $keyword = new \PClave();
        $keyword->read($id);

        if (!is_null($keyword->id)) {
            $deleted = $keyword->delete($id);

            if ($deleted) {
                $success[] = [
                    'id'      => $id,
                    'message' => _('Item deleted successfully'),
                    'type'    => 'success'
                ];
            } else {
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

        return new JsonResponse([ 'messages'  => array_merge($success, $errors) ]);
    }

    /**
     * Deletes multiple menus at once give them ids
     *
     * @param  Request      $request     The request object.
     * @return JsonResponse              The response object.
     */
    public function batchDeleteAction(Request $request)
    {
        $errors  = [];
        $success = [];
        $updated = [];

        $ids = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $keyword = new \PClave();
                $keyword->read($id);

                if (!is_null($keyword->id)) {
                    $deleted = $keyword->delete($id);

                    if ($deleted) {
                        $updated++;
                    } else {
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

        return new JsonResponse([ 'messages' => array_merge($success, $errors) ]);
    }
}
