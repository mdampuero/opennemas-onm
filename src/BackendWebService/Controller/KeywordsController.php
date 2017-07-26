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
        $search = $request->query->get('search');
        $page = $request->query->getDigits('page', 1);
        $elementsPerPage = $request->query->getDigits('elements_per_page', 10);

        $filter = '';
        if (is_array($search) && array_key_exists('title', $search)) {
            $name = $search['title'][0]['value'];
            $filter = '`pclave` LIKE "%' . $name . '%"';
        }

        $keywordManager = new \PClave();
        $keywords = $keywordManager->find($filter);

        $results = array_slice($keywords, ($page-1) * $elementsPerPage, $elementsPerPage);

        return new JsonResponse(
            array(
                'elements_per_page' => $elementsPerPage,
                'extra'             => array(
                    'types'      => \PClave::getTypes(),
                ),
                'page'              => $page,
                'results'           => $results,
                'total'             => count($keywords),
            )
        );
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
        $errors  = array();
        $success = array();

        $keyword = new \PClave();
        $keyword->read($id);

        if (!is_null($keyword->id)) {
            $deleted = $keyword->delete($id);

            if ($deleted) {
                $success[] = array(
                    'id'      => $id,
                    'message' => _('Item deleted successfully'),
                    'type'    => 'success'
                );
            } else {
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

        return new JsonResponse(
            array(
                'messages'  => array_merge($success, $errors),
            )
        );
    }

    /**
     * Deletes multiple menus at once give them ids
     *
     * @param  Request      $request     The request object.
     * @return JsonResponse              The response object.
     */
    public function batchDeleteAction(Request $request)
    {
        $errors  = array();
        $success = array();
        $updated = array();

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
}
