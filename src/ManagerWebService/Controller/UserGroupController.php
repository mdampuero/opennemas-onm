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
            return new JsonResponse(
                array(
                    'success' => false,
                    'message' => array(
                        'type' => 'error',
                        'text' => _('User group name cannot be empty')
                    )
                )
            );
        }

        if ($userGroup->create($data)) {
            $success = true;
            $message = array(
                'id'   => $userGroup->id,
                'type' => 'success',
                'text' => _('User group saved successfully')
            );
        } else {
            $message = array(
                'type' => 'error',
                'text' => _('Unable to create a new usergroup')
            );
        }

        return new JsonResponse(
            array(
                'success' => $success,
                'message' => $message
            )
        );
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
        $success = false;
        $message = array();

        $userGroup = new \UserGroup();
        $deleted   = $userGroup->delete($id);
        if ($deleted) {
            $success = true;
            $message = array(
                'type' => 'success',
                'text' => _('User group deleted successfully.')
            );
        } else {
            $message = array(
                'type' => 'success',
                'text' => sprintf(
                    _('Unable to delete the user group with id "%d"'),
                    $id
                )
            );
        }

        return new JsonResponse(
            array(
                'success' => $success,
                'message' => $message
            )
        );
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
        $message = array();
        $success  = false;
        $updated  = 0;

        $selected  = $request->request->get('selected', null);

        if (is_array($selected) && count($selected) > 0) {
            $userGroup = new \UserGroup();

            foreach ($selected as $id) {
                $deleted = $userGroup->delete($id);

                if ($deleted) {
                    $updated++;
                } else {
                    $message = array(
                        'type' => 'success',
                        'text' => sprintf(
                            _('Unable to delete the user group with id "%d"'),
                            $id
                        )
                    );
                }
            }
        }


        if (count($updated) > 0) {
            $success = true;

            array_unshift(
                $message,
                array(
                    'text' => sprintf(_('%s user groups deleted successfully.'), count($updated)),
                    'type' => 'success'
                )
            );
        }

        return new JsonResponse(
            array(
                'success' => $success,
                'message' => $message
            )
        );
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
        $epp      = $request->request->getDigits('epp', 10);
        $page     = $request->request->getDigits('page', 1);
        $criteria = $request->request->filter('criteria') ? : array();
        $orderBy  = $request->request->filter('sort_by') ? : array();

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
                'group'     => null,
                'template' => $this->templateParams()
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
                'group'    => $group,
                'template' => $this->templateParams()
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
        $success = false;
        $message = array();

        $data = array(
            'id'         => $id,
            'name'       => $request->request->filter('name', '', FILTER_SANITIZE_STRING),
            'privileges' => $request->request->get('privileges'),
        );

        if (!$data['name']) {
            return new JsonResponse(
                array(
                    'success' => false,
                    'message' => array(
                        'type' => 'error',
                        'text' => _('User group name cannot be empty')
                    )
                )
            );
        }

        $userGroup = new \UserGroup();
        if ($userGroup->update($data)) {
            $this->get('usergroup_repository')->deleteCache($id);

            $success = true;
            $message = array(
                'type' => 'success',
                'text' => _('User group updated successfully')
            );
        } else {
            $message = array(
                'type' => 'error',
                'text' => _('Unable to update the user group with id "%d"'), $id
            );
        }

        return new JsonResponse(
            array(
                'success' => $success,
                'message' => $message
            )
        );
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
