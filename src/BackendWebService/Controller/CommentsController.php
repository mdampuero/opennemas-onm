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
                'messages'  => array_merge($success, $errors)
            )
        );
    }

    /**
     * Updates contents status property.
     *
     * @param  Request      $request     The request object.
     * @return JsonResponse              The response object.
     */
    public function batchToggleStatusAction(Request $request)
    {
        $em      = $this->get('comment_repository');
        $errors  = array();
        $success = array();
        $updated = array();

        $status = $request->request->get('value');
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
                        $errors[] = array(
                            'id'      => $id,
                            'message' => sprintf(_('Unable to update item with id "%d"'), $id),
                            'type'    => 'error'
                        );
                    }
                } else {
                    $errors[] = array(
                        'id'   => $id,
                        'text' => sprintf(_('Unable to find the item with id "%d"'), $id),
                        'type' => 'error'
                    );
                }
            }
        }

        if (count($updated)) {
            $success[] = array(
                'id'      => $updated,
                'message' => sprintf(_('%d item(s) updated successfully'), count($updated)),
                'type'    => 'success'
            );
        }

        return new JsonResponse(
            array(
                'messages'  => array_merge($success, $errors)
            )
        );
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
        $messages = array();

        $comment = $em->find($id);

        if (!is_null($id)) {
            try {
                $comment->delete($id);

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
                'type'    => 'error'
            );
        }

        return new JsonResponse(
            array(
                'messages'  => $messages
            )
        );
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
        $elementsPerPage = $request->request->getDigits('elements_per_page', 10);
        $page            = $request->request->getDigits('page', 1);
        $search          = $request->request->get('search');
        $sortBy          = $request->request->filter('sort_by', null, FILTER_SANITIZE_STRING);
        $sortOrder       = $request->request->filter('sort_order', 'asc', FILTER_SANITIZE_STRING);

        $order = null;
        if ($sortBy) {
            $order = '`' . $sortBy . '` ' . $sortOrder;
        }

        unset($search['content_type_name']);

        $em      = $this->get('comment_repository');
        $results = $em->findBy($search, $order, $elementsPerPage, $page);
        $results = $this->convertToUtf8($results);
        $total   = $em->countBy($search);

        foreach ($results as &$result) {
            $result->date = $result->date->format(\DateTime::ISO8601);
        }

        return new JsonResponse(
            array(
                'elements_per_page' => $elementsPerPage,
                'extra'             => $this->loadExtraData($results),
                'page'              => $page,
                'results'           => $results,
                'total'             => $total
            )
        );
    }


    /**
     * Toggle status in comment given its id
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
     */
    public function toggleStatusAction(Request $request, $id)
    {
        $status   = null;
        $em       = $this->get('comment_repository');
        $messages = array();

        $comment = $em->find($id);
        $status = $request->request->get('value');

        if (!is_null($id)) {
            try {
                $comment->setStatus($status);
                $em->delete($id);

                $messages[] = array(
                    'id'      => $id,
                    'message' => _('Item updated successfully'),
                    'type'    => 'success'
                );
            } catch (Exception $e) {
                $messages[] = array(
                    'id'      => $id,
                    'message' => sprintf(_('Unable to update item with id "%d"'), $id),
                    'type'    => 'error'
                );
            }
        } else {
            $messages[] = array(
                'id'      => $id,
                'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
                'type'    => 'error'
            );
        }

        return new JsonResponse(
            array(
                'status'   => $status,
                'messages' => $messages,
            )
        );
    }

    /**
     * Loads extra data related to the given contents.
     *
     * @param  array $contents Array of contents.
     * @return array           Array of extra data.
     */
    protected function loadExtraData($comments)
    {
        $extra = array();

        $ids = array();
        foreach ($comments as $comment) {
            if ($comment->content_type_referenced && $comment->content_id) {
                $ids[] = array($comment->content_type_referenced, $comment->content_id);
            }
        }

        $contents = $this->get('entity_repository')->findMulti($ids);

        $extra['contents'] = array();
        foreach ($contents as $content) {
            $extra['contents'][$content->pk_content] = $content;
        }

        return $extra;
    }
}
