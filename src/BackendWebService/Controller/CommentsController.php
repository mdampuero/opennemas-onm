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

class CommentsController extends ContentController
{
    /**
     * Deletes multiple comments at once give them ids
     *
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function batchDeleteAction(Request $request, $contentType = null)
    {
        $em = $this->get('comment_repository');
        $errors  = array();
        $success = array();

        $ids = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $content = $em->find($id);

                if (!is_null($content->id)) {
                    try {
                        $content->delete($id);
                        $success[] = array(
                            'id'      => $id,
                            'message' => 'Selected items deleted successfully'
                        );
                    } catch (Exception $e) {
                        $errors[] = array(
                            'id'      => $id,
                            'message' => 'Unable to delete item with id "$id"'
                        );
                    }
                } else {
                    $errors[] = array(
                        'id'      => $id,
                        'message' => 'Unable to find item with id "$id"'
                    );
                }
            }
        }

        return new JsonResponse(
            array(
                'errors'  => $errors,
                'success' => $success
            )
        );
    }

    /**
     * Updates contents status property.
     *
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function batchToggleStatusAction(Request $request)
    {
        $em      = $this->get('comment_repository');
        $errors  = array();
        $success = array();

        $status = $request->request->get('status');
        $ids    = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $content = $em->find($id);

                if (!is_null($content->id)) {
                    try {
                        $content->setStatus($status);
                        $em->delete($id);

                        $success[] = array(
                            'id'   => $id,
                            'text' => _('Selected items updated successfully')
                        );
                    } catch (Exception $e) {
                        $errors[] = array(
                            'id'   => $id,
                            'text' => _('Unable to update item with id "$id"')
                        );
                    }
                } else {
                    $errors[] = array(
                        'id'   => $id,
                        'text' => _('Unable to find item with id "$id"')
                    );
                }
            }
        }

        return new JsonResponse(
            array(
                'errors'  => $errors,
                'success' => $success
            )
        );
    }

    /**
     * Deletes a comment.
     *
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function deleteAction($id, $contentType = null)
    {
        $em = $this->get('comment_repository');
        $errors  = array();
        $success = array();

        $comment = $em->find($id);

        if (!is_null($id)) {
            try {
                $comment->delete($id);

                $success[] = array(
                    'id'      => $id,
                    'message' => 'Item deleted successfully'
                );
            } catch (Exception $e) {
                $errors[] = array(
                    'id'      => $id,
                    'message' => 'Unable to delete item with id "$id"'
                );
            }
        } else {
            $errors[] = array(
                'id'      => $id,
                'message' => 'Unable to find item with id "$id"'
            );
        }

        return new JsonResponse(
            array(
                'errors'  => $errors,
                'success' => $success
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
        $total   = $em->countBy($search);

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
        $status  = null;
        $em      = $this->get('comment_repository');
        $errors  = array();
        $success = array();

        $comment = $em->find($id);

        if (!is_null($id)) {
            try {
                $status = $comment->status;

                if ($status != 'accepted') {
                    $status = 'accepted';
                } else {
                    $status = 'rejected';
                }

                $comment->setStatus($status);
                $em->delete($id);

                $success[] = array(
                    'id'      => $id,
                    'message' => 'Item updated successfully'
                );
            } catch (Exception $e) {
                $errors[] = array(
                    'id'      => $id,
                    'message' => 'Unable to update item with id "$id"'
                );
            }
        } else {
            $errors[] = array(
                'id'      => $id,
                'message' => 'Unable to find item with id "$id"'
            );
        }

        return new JsonResponse(
            array(
                'status'  => $status,
                'errors'  => $errors,
                'success' => $success
            )
        );
    }

        /**
     * Change  status some comments given its ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('COMMENT_AVAILABLE')")
     **/
    public function batchStatusAction(Request $request)
    {
        // Get request data
        $selected = $request->query->get('selected_fld');
        $status   = $request->query->filter('status', 'accepted');

        if (count($selected) > 0) {

            // Iterate over each comment and update its status
            $success = 0;
            foreach ($selected as $id) {
                try {

                    $comment = new \Comment($id);
                    $comment->setStatus($status);
                    $success++;
                } catch (\Exception $e) {
                    m::add(
                        sprintf(_('Comment id %s: ').$e->getMessage(), $id),
                        m::ERROR
                    );
                }
            }

            if ($success > 0) {
                m::add(
                    sprintf(
                        _("Successfully changed the status to '%s' to %d comments."),
                        $this->statuses[$status],
                        $success
                    ),
                    m::SUCCESS
                );
            }
        }

        $params = array(
            'page'     => $request->query->getDigits('page', 1),
            'filter_status'   => $status,
        );

        return $this->redirect($this->generateUrl('admin_comments_list', $params));

    }

    /**
     * Loads extra data related to the given contents.
     *
     * @param  array $contents Array of contents.
     * @return array           Array of extra data.
     */
    private function loadExtraData($comments)
    {
        $extra = array();

        $ids = array();
        foreach ($comments as $comment) {
            $ids[] = array($comment->content_type_referenced, $comment->content_id);
        }

        $contents = $this->get('entity_repository')->findMulti($ids);

        $extra['contents'] = array();
        foreach ($contents as $content) {
            $extra['contents'][$content->pk_content] = $content;
        }

        return $extra;
    }
}
