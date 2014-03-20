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

class ArticlesController extends ContentController
{
    /**
     * Deletes multiple articles at once give them ids
     *
     * @param  Request $request the request object
     * @return Response         the response object
     *
     * @Security("has_role('ARTICLE_DELETE')")
     */
    public function batchDeleteAction(Request $request)
    {
        $status  = 'OK';
        $errors  = array();
        $success = array();

        $ids = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $article = new \Article($id);

                if (!is_null($article->id)) {
                    try {
                        $article->remove($id);
                        $success[] = _('Article deleted successfully.');
                    } catch (Exception $e) {
                        $errors[] = sprintf(_('Unable to delete the article "%s".'), $article->name);
                    }
                }
            }
        }

        return new JsonResponse(array('status' => $status, 'errors' => $errors, 'success' => $success));
    }

    /**
     * Updates articles available property.
     *
     * @param  Request $request the request object
     * @return Response         the response object
     *
     * @Security("has_role('ARTICLE_AVAILABLE')")
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
                $article = new \Article($id);

                if (!is_null($article->id)) {
                    try {
                        $article->set_available($available, $_SESSION['userid']);
                        $success[] = sprintf(_('Successfully changed availability for "%s" article'), $article->title);
                    } catch (Exception $e) {
                        $errors[] = sprintf(_('Unable to change the article availability for "%s" article'), $article->name);
                    }
                }
            }
        }

        return new JsonResponse(array('status' => $status, 'errors' => $errors, 'success' => $success));
    }

    /**
     * Deletes a article.
     *
     * @param  integer      $id Menu id.
     * @return JsonResponse     The response of the current action.
     *
     * @Security("has_role('ARTICLE_DELETE')")
     */
    public function deleteAction($id)
    {
        $status  = 'ERROR';
        $message = _('You must give an id for delete the article.');

        $article = new \Article($id);

        if (!is_null($id)) {
            try {
                $article->remove($id);

                $status  = 'OK';
                $message = _('Article deleted successfully.');
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
     * @Security("has_role('ARTICLE_ADMIN')")
     */
    public function listAction(Request $request)
    {
        $results = $this->searchAction($request);
        return new JsonResponse($results);
    }

    /**
     * Toggles article availability.
     *
     * @param  integer      $id article id.
     * @return JsonResponse     The response of the current action.
     *
     * @Security("has_role('ARTICLE_AVAILABLE')")
     */
    public function toggleAvailableAction($id)
    {
        $status  = 'ERROR';
        $message = _('You must give an id for delete the article.');

        $em     = $this->get('entity_repository');
        $article = $em->find(\classify('article'), $id);

        if (!$article) {
            $message = sprintf(_('Unable to find article with id "%d"'), $id);
        } else {
            $article->toggleAvailable();

            $status  = 'OK';
            $message = sprintf(_('Successfully changed availability for "%s" article'), $article->title);
        }

        return new JsonResponse(
            array(
                'status'    => $status,
                'message'   => $message,
                'available' => $article->available
            )
        );
    }
}
