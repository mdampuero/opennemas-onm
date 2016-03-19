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
        $msg = $this->get('core.messenger');

        $userGroup = new UserGroup($request->request->all());

        $this->get('orm.manager')->persist($userGroup);
        $msg->add(_('User group saved successfully'), 'success', 201);

        $response = new JsonResponse($msg->getMessages(), $msg->getCode());
        $response->headers->set(
            'Location',
            $this->generateUrl(
                'manager_ws_user_group_show', [ 'id' => $userGroup->id ]
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
        $em  = $this->get('orm.manager');
        $msg = $this->get('core.messenger');

        $userGroup = $em->getRepository('UserGroup')->find($id);

        $em->remove($userGroup);
        $msg->add(_('User group deleted successfully.'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
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
        $ids = $request->request->get('ids', []);
        $msg = $this->get('core.messenger');

        if (!is_array($ids) || count($ids) === 0) {
            $msg->add(_('Bad request'), 'error', 400);
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $em  = $this->get('orm.manager');
        $oql = sprintf('id in [%s]', implode(',', $ids));

        $userGroups = $em->getRepository('UserGroup')->findBy($oql);

        $deleted = 0;
        foreach ($userGroups as $userGroup) {
            try {
                $em->remove($userGroup);
                $deleted++;
            } catch (\Exception $e) {
                $msg->add($e->getMessage(), 'error');
            }
        }

        if ($deleted > 0) {
            $msg->add(
                sprintf(_('%s user groups deleted successfully'), $deleted),
                'success'
            );
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
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

        $userGroups = array_map(function ($a) {
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
            'user_group' => null,
            'extra'      => $this->templateParams()
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
            'user_group' => $group->getData(),
            'extra'      => $this->templateParams()
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
        $msg  = $this->get('core.messenger');
        $data = $em->getConverter('UserGroup')
            ->objectify($request->request->all());

        $userGroup = $em->getRepository('UserGroup')->find($id);
        $userGroup->setData($data);

        $em->persist($userGroup);

        $msg->add(_('User group updated successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns a list of parameters for the template.
     *
     * @return array Array of template parameters.
     */
    private function templateParams()
    {
        $privilege = new \Privilege();

        return [ 'modules' => $privilege->getPrivilegesByModules() ];
    }
}
