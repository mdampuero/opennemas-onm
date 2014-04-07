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
        $em      = $this->get('user_repository');
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
                'message' => _('Selected items deleted successfully'),
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
     * Updated the users activation status.
     *
     * @param  Request      $request The request object.
     * @return JsonResponse          The response object.
     */
    public function batchSetEnabledAction(Request $request)
    {
        list($hasRoles, $required) = $this->hasRoles(__FUNCTION__, 'user');

        if (!$hasRoles) {
            $roles = '';
            foreach ($required as $role) {
                $roles .= $role;
            }
            $roles = rtrim($roles, ',');

            return new JsonResponse(
                array(
                    'messages' => array(
                        array(
                            'id'      => '500',
                            'type'    => 'error',
                            'message' => sprintf(_('Access denied (%s)'), $roles)
                        )
                    )
                )
            );
        }

        $enabled = $request->request->getDigits('value');
        $ids     = $request->request->get('ids');
        $errors  = array();
        $success = array();
        $updated = array();

        foreach ($ids as $id) {
            if (!is_null($id)) {
                $user = new \User();
                if ($enabled) {
                    $user->activateUser($id);
                } else {
                    $user->deactivateUser($id);
                }

                $updated[] = $id;
            } else {
                $errors[] = array(
                    'id'      => $id,
                    'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
                    'type'    => 'error'
                );
            }
        }

        if (count($updated) > 0) {
            $success[] = array(
                'id'      => $updated,
                'message' => sprintf(_('%d item(s) updated successfully'), count($updated)),
                'type'    => 'success'
            );
        }

        return new JsonResponse(
            array(
                'activated'  => $enabled,
                'messages' => array_merge($success, $errors)
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
        $em       = $this->get('user_repository');
        $messages = array();

        $user = $em->find($id);

        if (!is_null($id)) {
            try {
                $user->delete($id);

                $success[] = array(
                    'id'      => $id,
                    'message' => _('Item deleted successfully'),
                    'type'    => 'success'
                );
            } catch (Exception $e) {
                $errors[] = array(
                    'id'      => $id,
                    'message' => sprintf(_('Unable to delete the item with the id "%d"'), $id),
                    'type'    => 'error'
                );
            }
        } else {
            $errors[] = array(
                'id'      => $id,
                'message' => sprintf(_('Unable to find the item with the id "%d"'), $id),
                'type'    => 'error'
            );
        }

        return new JsonResponse(array('messages'  => $messages));
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
     * Updated the users activation status.
     *
     * @param  Request      $request The request object.
     * @return JsonResponse          The response object.
     */
    public function setEnabledAction(Request $request, $id)
    {
        list($hasRoles, $required) = $this->hasRoles(__FUNCTION__, 'user');

        if (!$hasRoles) {
            $roles = '';
            foreach ($required as $role) {
                $roles .= $role;
            }
            $roles = rtrim($roles, ',');

            return new JsonResponse(
                array(
                    'messages' => array(
                        array(
                            'id'      => '500',
                            'type'    => 'error',
                            'message' => sprintf(_('Access denied (%s)'), $roles)
                        )
                    )
                )
            );
        }

        $enabled  = $request->request->getDigits('value');
        $messages = array();

        if (!is_null($id)) {
            $user = new \User();
            if ($enabled) {
                $user->activateUser($id);
            } else {
                $user->deactivateUser($id);
            }

            $messages[] = array(
                'id'      => $id,
                'message' => _('Item updated successfully'),
                'type'    => 'success'
            );
        } else {
            $messages[] = array(
                'id'      => $id,
                'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
                'type'    => 'error'
            );
        }

        return new JsonResponse(
            array(
                'activated' => $enabled,
                'messages'  => $messages
            )
        );
    }

    /**
     * Checks if the current user has roles to execute the required action.
     *
     * @param  string  $action      Required action.
     * @param  string  $contentType Content type name.
     * @return boolean              [description]
     */
    private function hasRoles($action)
    {
        $required = array();
        $roles    = $this->getUser()->getRoles();

        $required[] = 'USER_ADMIN';

        switch ($action) {
            case 'batchDeleteAction':
            case 'deleteAction':
                $required[] = 'USER_DELETE';
                break;
            case 'batchSetContentStatusAction':
            case 'setContentStatusAction':
                $required[] = 'USER_AVAILABLE';
                break;
        }

        return array(
            empty(array_diff($required, $roles)),
            array_diff($required, $roles)
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
