<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManagerWebService\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Onm\Framework\Controller\Controller;

class UserGroupController extends Controller
{
    /**
     * Creates a new user group.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function createAction(Request $request)
    {
        $userGroup = new \UserGroup();

        $data = array(
            'name'       => $request->request->filter('name', '', FILTER_SANITIZE_STRING),
            'privileges' => $request->request->get('privileges'),
        );

        if (!$data['name']) {
            return new JsonResponse(_('User group name cannot be empty'), 400);
        }

        if (!$userGroup->create($data)) {
            return new JsonResponse(_('Unable to create a new usergroup'), 409);
        }

        $response =  new JsonResponse(_('User group saved successfully'), 201);
        $response->headers->set(
            'Location',
            $this->generateUrl(
                'manager_ws_user_group_show',
                [ 'id' =>$userGroup->id ]
            )
        );

        return $response;
    }

    /**
     * Deletes an user group.
     *
     * @param integer $id The user group id.
     *
     * @return JsonResponse The response object.
     */
    public function deleteAction($id)
    {
        $userGroup = new \UserGroup();
        if (!$userGroup->delete($id)) {
            return new JsonResponse(
                sprintf(_('Unable to delete the user group with id "%d"'), $id),
                409
            );
        }

        return new JsonResponse(_('User group deleted successfully.'));
    }

    /**
     * Deletes the selected user groups.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function deleteSelectedAction(Request $request)
    {
        $messages   = [ 'errors' => [], 'success' => [] ];
        $selected   = $request->request->get('selected', null);
        $statusCode = 200;
        $updated    = [];

        if (!is_array($selected)
            || (is_array($selected) && count($selected) == 0)
        ) {
            return new JsonResponse(
                _('Unable to find user groups for the given criteria'),
                409
            );
        }

        $userGroup = new \UserGroup();

        foreach ($selected as $id) {
            if ($userGroup->delete($id)) {
                $updated[] = $id;
            } else {
                $messages['errors'][] = [
                    'type' => 'success',
                    'text' => sprintf(
                        _('Unable to delete the user group with id "%d"'),
                        $id
                    )
                ];
            }
        }

        if (count($updated) > 0) {
            $messages['success'] = [
                'ids' => $updated,
                'message' => sprintf(_('%s user groups deleted successfully.'), count($updated)),
            ];
        }

        // Return the proper status code
        if (count($messages['errors']) > 0 && count($updated) > 0) {
            $statusCode = 207;
        } elseif (count($messages['errors']) > 0) {
            $statusCode = 409;
        }

        return new JsonResponse($messages, $statusCode);
    }

    /**
     * Returns the list of users as JSON.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function listAction(Request $request)
    {
        $epp      = $request->query->getDigits('epp', 10);
        $page     = $request->query->getDigits('page', 1);
        $criteria = $request->query->filter('criteria') ? : array();
        $orderBy  = $request->query->filter('orderBy') ? : array();

        $order = array();
        foreach ($orderBy as $value) {
            $order[$value['name']] = $value['value'];
        }

        $um     = $this->get('usergroup_repository');
        $groups = $um->findBy($criteria, $order, $epp, $page);
        $total  = $um->countBy($criteria);

        return new JsonResponse(
            array(
                'epp'     => $epp,
                'page'    => $page,
                'results' => $groups,
                'total'   => $total,
            )
        );
    }

    /**
     * Returns the data to create a new group.
     *
     * @return JsonResponse The response object.
     */
    public function newAction()
    {
        return new JsonResponse(
            array(
                'group' => null,
                'extra' => $this->templateParams()
            )
        );
    }

    /**
     * Returns a group as JSON.
     *
     * @param integer $id The group id.
     *
     * @return JsonResponse The response object.
     */
    public function showAction($id)
    {
        $group = $this->get('usergroup_repository')->find($id);

        return new JsonResponse(
            array(
                'group' => $group,
                'extra' => $this->templateParams()
            )
        );
    }

    /**
     * Updates the user group information given its id and the new information
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function updateAction(Request $request, $id)
    {
        $data = array(
            'id'         => $id,
            'name'       => $request->request->filter('name', '', FILTER_SANITIZE_STRING),
            'privileges' => $request->request->get('privileges'),
        );

        if (!$data['name']) {
            return new JsonResponse(_('User group name cannot be empty'), 400);
        }

        $userGroup = new \UserGroup();
        if ($userGroup->update($data)) {
            $this->get('usergroup_repository')->deleteCache($id);

            return new JsonResponse(_('User group updated successfully'));
        } else {
            return new JsonResponse(
                sprintf(_('Unable to update the user group with id "%d"'), $id),
                409
            );
        }
    }

    /**
     * Returns a list of parameters for the template.
     *
     * @return array Array of template parameters.
     */
    private function templateParams()
    {
        $privilege = new \Privilege();

        return array('modules' => $privilege->getPrivilegesByModules());
    }
}
