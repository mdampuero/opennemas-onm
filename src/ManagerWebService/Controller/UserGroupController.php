<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ManagerWebService\Controller;

use Common\Core\Annotation\Security;
use Common\ORM\Entity\UserGroup;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Displays, saves, modifies and removes user groups.
 */
class UserGroupController extends Controller
{
    /**
     * Deletes an user group.
     *
     * @param integer $id The user group id.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('GROUP_DELETE')")
     */
    public function deleteAction($id)
    {
        $em  = $this->get('orm.manager');
        $msg = $this->get('core.messenger');

        $userGroup = $em->getRepository('UserGroup', 'manager')->find($id);

        $em->remove($userGroup, $userGroup->getOrigin());
        $msg->add(_('User group deleted successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Deletes the selected user groups.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('GROUP_DELETE')")
     */
    public function deleteSelectedAction(Request $request)
    {
        $ids = $request->request->get('ids', []);
        $msg = $this->get('core.messenger');

        if (!is_array($ids) || empty($ids)) {
            $msg->add(_('Bad request'), 'error', 400);
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $em  = $this->get('orm.manager');
        $oql = sprintf('pk_user_group in [%s]', implode(',', $ids));

        $userGroups = $em->getRepository('UserGroup', 'manager')->findBy($oql);

        $deleted = 0;
        foreach ($userGroups as $userGroup) {
            try {
                $em->remove($userGroup, $userGroup->getOrigin());
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
     * Returns the list of user groups.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('GROUP_ADMIN')")
     */
    public function listAction(Request $request)
    {
        $oql = $request->query->get('oql', '');

        $repository = $this->get('orm.manager')->getRepository('UserGroup', 'manager');
        $converter  = $this->get('orm.manager')->getConverter('UserGroup');

        $total      = $repository->countBy($oql);
        $userGroups = $repository->findBy($oql);

        $userGroups = array_map(function ($a) use ($converter) {
            return $converter->responsify($a->getData());
        }, $userGroups);

        return new JsonResponse([
            'results' => $userGroups,
            'total'   => $total,
        ]);
    }

    /**
     * Returns the data to create a new user group.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('GROUP_CREATE')")
     */
    public function newAction()
    {
        return new JsonResponse([ 'extra' => $this->getExtraData() ]);
    }

    /**
     * Saves a new user group.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('GROUP_CREATE')")
     */
    public function saveAction(Request $request)
    {
        $em   = $this->get('orm.manager');
        $msg  = $this->get('core.messenger');
        $data = $em->getConverter('UserGroup')
            ->objectify($request->request->all());

        $userGroup = new UserGroup($data);

        $em->persist($userGroup, $userGroup->getOrigin());
        $msg->add(_('User group saved successfully'), 'success', 201);

        $response = new JsonResponse($msg->getMessages(), $msg->getCode());
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
     * Returns an user group.
     *
     * @param integer $id The group id.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('GROUP_UPDATE')")
     */
    public function showAction($id)
    {
        $group = $this->get('orm.manager')
            ->getRepository('UserGroup', 'manager')
            ->find($id);

        return new JsonResponse([
            'user_group' => $group->getData(),
            'extra'      => $this->getExtraData()
        ]);
    }

    /**
     * Updates the user group information given its id and the new information
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('GROUP_UPDATE')")
     */
    public function updateAction(Request $request, $id)
    {
        $em   = $this->get('orm.manager');
        $msg  = $this->get('core.messenger');
        $data = $em->getConverter('UserGroup')
            ->objectify($request->request->all());

        $userGroup = $em->getRepository('UserGroup', 'manager')->find($id);
        $userGroup->setData($data);

        $em->persist($userGroup, $userGroup->getOrigin());

        $msg->add(_('User group saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns a list of extra data.
     *
     * @return array The extra data.
     */
    private function getExtraData()
    {
        return [ 'modules' => \Privilege::getPrivilegesByModules() ];
    }
}
