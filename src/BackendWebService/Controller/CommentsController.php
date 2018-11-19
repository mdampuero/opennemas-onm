<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BackendWebService\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CommentsController extends ContentController
{
    /**
     * Deletes multiple comments at once give them ids
     *
     * @param  Request      $request     The request object.
     * @return JsonResponse              The response object.
     */
    public function batchDeleteAction(Request $request)
    {
        $em = $this->get('comment_repository');

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

        return new JsonResponse([ 'messages' => array_merge($success, $errors) ]);
    }

    /**
     * Deletes a comment.
     *
     * @param  Request      $request     The request object.
     * @return JsonResponse              The response object.
     */
    public function deleteAction($id)
    {
        $em       = $this->get('comment_repository');
        $messages = [];
        $comment  = $em->find($id);

        if (!is_null($id)) {
            try {
                $comment->delete($id);

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
                'type'    => 'error'
            ];
        }

        return new JsonResponse([
            'messages' => $messages
        ]);
    }

    /**
     * Returns a list of contents in JSON format.
     *
     * @param  Request      $request     The request with the search parameters.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response in JSON format.
     */
    public function listAction(Request $request, $contentType = null)
    {
        $oql = $request->query->get('oql', '');
        $em  = $this->get('comment_repository');

        list($criteria, $order, $epp, $page) =
            $this->get('core.helper.oql')->getFiltersFromOql($oql);

        $results = $em->findBy($criteria, $order, $epp, $page);
        $results = \Onm\StringUtils::convertToUtf8($results);
        $total   = $em->countBy($criteria);

        return [
            'extra'      => $this->loadExtraData($results),
            'o-filename' => 'comments',
            'results'    => $results,
            'total'      => $total
        ];
    }

    /**
     * Updates contents status property.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function patchSelectedAction(Request $request)
    {
        $em      = $this->get('comment_repository');
        $errors  = [];
        $success = [];
        $updated = [];

        $status = $request->request->get('status');
        $ids    = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $content = $em->find($id);

                if (!is_null($content->id)) {
                    try {
                        $content->setStatus($status);
                        $em->delete($id);
                        $updated[] = $id;
                    } catch (Exception $e) {
                        $errors[] = [
                            'id'      => $id,
                            'message' => sprintf(_('Unable to update item with id "%d"'), $id),
                            'type'    => 'error'
                        ];
                    }
                } else {
                    $errors[] = [
                        'id'   => $id,
                        'text' => sprintf(_('Unable to find the item with id "%d"'), $id),
                        'type' => 'error'
                    ];
                }
            }
        }

        if (count($updated)) {
            $success[] = [
                'id'      => $updated,
                'message' => sprintf(_('%d item(s) updated successfully'), count($updated)),
                'type'    => 'success'
            ];
        }

        return new JsonResponse([ 'messages' => array_merge($success, $errors) ]);
    }


    /**
     * Toggle status in comment given its id.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function patchAction(Request $request, $id)
    {
        $em      = $this->get('comment_repository');
        $comment = $em->find($id);
        $status  = $request->request->get('status');

        $messages = [];
        if (!is_null($comment->id)) {
            try {
                $comment->setStatus($status);
                $em->delete($id);

                $messages[] = [
                    'id'      => $id,
                    'message' => _('Item updated successfully'),
                    'type'    => 'success'
                ];
            } catch (Exception $e) {
                $messages[] = [
                    'id'      => $id,
                    'message' => sprintf(_('Unable to update item with id "%d"'), $id),
                    'type'    => 'error'
                ];
            }
        } else {
            $messages[] = [
                'id'      => $id,
                'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
                'type'    => 'error'
            ];
        }

        return new JsonResponse([
            'status'   => $status,
            'messages' => $messages,
        ]);
    }

    /**
     * Loads extra data related to the given contents.
     *
     * @param  array $contents Array of contents.
     * @return array           Array of extra data.
     */
    protected function loadExtraData($comments = [])
    {
        $extra = [];
        $ids   = [];

        foreach ($comments as $comment) {
            if ($comment->content_type_referenced && $comment->content_id) {
                $ids[] = [ $comment->content_type_referenced, $comment->content_id ];
            }
        }

        $contents = $this->get('entity_repository')->findMulti($ids);

        $extra['contents'] = [];
        foreach ($contents as $content) {
            $extra['contents'][$content->pk_content] = $content;
        }

        $extra['dateTimezone'] = $this->container->get('core.locale')->getTimeZone();

        $this->get('core.locale')->setContext('frontend');

        return $extra;
    }
}
