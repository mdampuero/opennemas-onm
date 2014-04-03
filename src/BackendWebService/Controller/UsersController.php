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

class UsersController extends Controller
{
    /**
     * Deletes multiple users at once give them ids
     *
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function batchDeleteAction(Request $request, $contentType = null)
    {
        $em = $this->get('user_repository');
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
     * Deletes a user.
     *
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function deleteAction($id, $contentType = null)
    {
        $em = $this->get('user_repository');
        $errors  = array();
        $success = array();

        $user = $em->find($id);

        if (!is_null($id)) {
            try {
                $user->delete($id);

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
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function listAction(Request $request, $contentType = null)
    {
        $elementsPerPage = $request->request->getDigits('elements_per_page', 10);
        $page            = $request->request->getDigits('page', 1);
        $search          = $request->request->get('search');
        $sortBy          = $request->request->filter('sort_by', null, FILTER_SANITIZE_STRING);
        $sortOrder       = $request->request->filter('sort_order', 'asc', FILTER_SANITIZE_STRING);
        $order           = null;

        $em = $this->get('user_repository');

        unset($search['content_type_name']);

        if (!$this->getUser()->isMaster()) {
            $search['fk_user_group'][] = array(
                'value' => '^4$|,4,|,4$',
                'operator' => 'not regexp'
            );
        }

        if ($sortBy) {
            $order = '`' . $sortBy . '` ' . $sortOrder;
        }

        $results = $em->findBy($search, $order, $elementsPerPage, $page);
        $total   = $em->countBy($search);

        return new JsonResponse(
            array(
                'elements_per_page' => $elementsPerPage,
                'page'              => $page,
                'results'           => $results,
                'extra'             => $this->loadExtraData($results),
                'total'             => $total
            )
        );
    }

    /**
     * Loads extra data related to the given users.
     *
     * @param  array $contents Array of users.
     * @return array           Array of extra data.
     */
    private function loadExtraData($results)
    {
        $extra = array();

        // Load groups information
        $ids = array();
        foreach ($results as $user) {
            $user->eraseCredentials();
            $ids = array_unique(array_merge($ids, $user->id_user_group));
        }

        if (($key = array_search('', $ids)) !== false) {
            unset($ids[$key]);
        }

        if (($key = array_search(0, $ids)) !== false) {
            unset($ids[$key]);
        }

        $groups = $this->get('usergroup_repository')->findMulti($ids);
        $extra['groups'] = array();
        foreach ($groups as $group) {
            $extra['groups'][$group->id] = $group;
        }

        // Load groups information
        $ids = array();
        foreach ($results as $user) {
            $ids[] = $user->avatar_img_id;
        }
        $ids = array_unique($ids);

        if (($key = array_search(0, $ids)) !== false) {
            unset($ids[$key]);
        }

        $contentIds = array();
        foreach ($ids as $photo) {
            $contentIds[] = array('photo', $photo);
        }

        $photos = $this->get('entity_repository')->findMulti($contentIds);
        $extra['photos'] = array();
        foreach ($photos as $photo) {
            $extra['photos'][$photo->id] = $photo;
        }

        return $extra;
    }
}
