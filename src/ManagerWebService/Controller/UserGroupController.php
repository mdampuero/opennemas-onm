<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ManagerWebService\Controller;

use Common\ORM\Entity\UserGroup;
use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Displays, saves, modifies and removes user groups.
 */
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
        $userGroup = new UserGroup($request->request->all());

        $this->get('orm.manager')->persist($userGroup);

        $response =  new JsonResponse(_('User group saved successfully'), 201);
        $response->headers->set(
            'Location',
            $this->generateUrl(
                'manager_ws_user_group_show',
                [ 'id' => $userGroup->pk_user_group ]
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
        $em        = $this->get('orm.manager');
        $userGroup = $em->getRepository('UserGroup')->find($id);

        $em->remove($userGroup);

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
        $oql = $request->query->get('oql', '');

        $repository = $this->get('orm.manager')->getRepository('UserGroup');

        $total      = $repository->countBy($oql);
        $userGroups = $repository->findBy($oql);

        $userGroups =  array_map(function ($a) {
            return $a->getData();
        }, $userGroups);

        return new JsonResponse(
            array(
                'results' => $userGroups,
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
        return new JsonResponse([
            'group'     => null,
            'template' => $this->templateParams()
        ]);
    }

    /**
     * Displays an user group.
     *
     * @param integer $id The group id.
     *
     * @return JsonResponse The response object.
     */
    public function showAction($id)
    {
        $group = $this->get('orm.manager')
            ->getRepository('UserGroup')
            ->find($id);

        return new JsonResponse([
            'group'    => $group->getData(),
            'template' => $this->templateParams()
        ]);
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
        $em   = $this->get('orm.manager');
        $data = $em->getConverter('UserGroup')
            ->objectify($request->request->all());

        $userGroup = $em->getRepository('UserGroup')->find($id);
        $userGroup->setData($data);

        $em->persist($userGroup);

        return new JsonResponse(_('User group updated successfully'));
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
