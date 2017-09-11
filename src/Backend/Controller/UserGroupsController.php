<?php
/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Common\ORM\Core\Exception\EntityNotFoundException;
use Common\ORM\Entity\UserGroup;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 */
class UserGroupsController extends Controller
{
    /**
     * List all the user groups
     *
     * @return Response the response object
     *
     * @Security("hasExtension('USER_GROUP_MANAGER')
     *     and hasPermission('GROUP_ADMIN')")
     */
    public function listAction()
    {
        return $this->render('acl/user_group/list.tpl');
    }

    /**
     * Shows the form for editting a user group.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('USER_GROUP_MANAGER')
     *     and hasPermission('GROUP_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $id = $request->query->getInt('id');

        try {
            $userGroup = $this->get('orm.manager')->getRepository('UserGroup')
                ->find($id);
        } catch (EntityNotFoundException $e) {
            $request->getSession()->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the user group with the id "%d"'), $id)
            );
            return $this->redirect($this->generateUrl('admin_acl_usergroups'));
        }

        $privilege  = new \Privilege();
        $privileges = $privilege->getPrivilegesByModules();

        if (!$this->get('core.security')->hasPermission('MASTER')
            || !$this->get('core.security')->hasPermission('PARTNER')
        ) {
            unset($privileges['SECURITY']);
        }

        return $this->render('acl/user_group/new.tpl', [
            'user_group' => $userGroup,
            'modules'    => $privileges,
        ]);
    }

    /**
     * Handles the action of show creation form for user group and save it
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('USER_GROUP_MANAGER')
     *     and hasPermission('GROUP_CREATE')")
     */
    public function createAction(Request $request)
    {
        $privilege = new \Privilege();

        return $this->render('acl/user_group/new.tpl', [
            'modules' => $privilege->getPrivilegesByModules(),
        ]);
    }

    /**
     * Saves a new user group.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('USER_GROUP_MANAGER')
     *     and hasPermission('GROUP_CREATE')")
     */
    public function saveAction(Request $request)
    {
        $em        = $this->get('orm.manager');
        $converter = $em->getConverter('UserGroup');
        $data      = $request->request->all();

        // TODO: Remove when using SPA
        if (!empty($data['privileges'])) {
            $data['privileges'] = array_map(function ($a) {
                return (int) $a;
            }, explode(',', $data['privileges']));
        }

        $userGroup = new UserGroup($converter->objectify($data));

        try {
            $em->persist($userGroup);

            $level   = 'success';
            $message = _('User group created successfully.');
        } catch (\Exception $e) {
            $level   = 'error';
            $message = _('Unable to create the new user group');
        }

        $this->get('session')->getFlashBag()->add($level, $message);

        if ($level == 'success') {
            return $this->redirect(
                $this->generateUrl(
                    'admin_acl_usergroup_show',
                    [ 'id' => $userGroup->pk_user_group ]
                )
            );
        }
    }

    /**
     * Updates the user group information given its id and the new information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('USER_GROUP_MANAGER')
     *     and hasPermission('GROUP_UPDATE')")
     */
    public function updateAction(Request $request, $id)
    {
        $em        = $this->get('orm.manager');
        $userGroup = $em->getRepository('UserGroup')->find($id);
        $data      = $request->request->all();

        // TODO: Remove when using SPA
        if (!empty($data['privileges'])) {
            $data['privileges'] = array_map(function ($a) {
                return (int) $a;
            }, explode(',', $data['privileges']));
        }

        $userGroup->setData($em->getConverter('UserGroup')->objectify($data));

        try {
            $em->persist($userGroup);

            $level   = 'success';
            $message = _('User group updated successfully.');
        } catch (\Exception $e) {
            $level   = 'error';
            $message = sprintf(_('Unable to update the user group with id "%d"'), $data['id']);
        }

        $request->getSession()->getFlashBag()->add($level, $message);

        return $this->redirect(
            $this->generateUrl(
                'admin_acl_usergroup_show',
                [ 'id' => $userGroup->pk_user_group ]
            )
        );
    }
}
