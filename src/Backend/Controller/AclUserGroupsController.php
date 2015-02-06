<?php
/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Backend\Annotation\CheckModuleAccess;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
class AclUserGroupsController extends Controller
{
    /**
     * List all the user groups
     *
     * @return Response the response object
     *
     * @Security("has_role('GROUP_ADMIN')")
     *
     * @CheckModuleAccess(module="USER_GROUP_MANAGER")
     **/
    public function listAction()
    {
        return $this->render('acl/user_group/list.tpl');
    }

    /**
     * Shows the form for editting a user group
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('GROUP_UPDATE')")
     *
     * @CheckModuleAccess(module="USER_GROUP_MANAGER")
     **/
    public function showAction(Request $request)
    {
        $id = $request->query->filter('id', FILTER_VALIDATE_INT);

        $userGroup = new \UserGroup($id);
        if (is_null($userGroup->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_("Unable to find user group with id '%d'"), $id)
            );

            return $this->redirect($this->generateUrl('admin_acl_usergroups'));
        }
        $privilege = new \Privilege();

        return $this->render(
            'acl/user_group/new.tpl',
            array(
                'user_group' => $userGroup,
                'modules'    => $privilege->getPrivilegesByModules(),
            )
        );
    }

    /**
     * Handles the action of show creation form for user group and save it
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('GROUP_CREATE')")
     *
     * @CheckModuleAccess(module="USER_GROUP_MANAGER")
     **/
    public function createAction(Request $request)
    {
        $userGroup = new \UserGroup();
        $privilege = new \Privilege();

        $data = array(
            'name'       => $request->request->filter('name', '', FILTER_SANITIZE_STRING),
            'privileges' => $request->request->get('privileges'),
        );

        if ($this->request->getMethod() == 'POST') {
            // Try to save the new privilege
            if ($userGroup->create($data)) {
                $level = 'success';
                $message = _('User group created successfully.');
            } else {
                $level = 'error';
                $message = _('Unable to create the new user group');
            }

            $this->get('session')->getFlashBag()->add(
                $level,
                $message
            );

            // If user group was saved successfully show again the form
            if ($level == 'success') {
                return $this->redirect(
                    $this->generateUrl(
                        'admin_acl_usergroup_show',
                        array('id' => $userGroup->id)
                    )
                );
            }
        }

        return $this->render(
            'acl/user_group/new.tpl',
            array(
                'user_group' => $userGroup,
                'modules'    => $privilege->getPrivilegesByModules(),
            )
        );
    }

    /**
     * Updates the user group information given its id and the new information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('GROUP_UPDATE')")
     *
     * @CheckModuleAccess(module="USER_GROUP_MANAGER")
     **/
    public function updateAction(Request $request)
    {
        $userGroup = new \UserGroup();

        $data = [
            'id'         => $request->query->getDigits('id'),
            'name'       => $request->request->filter('name', '', FILTER_SANITIZE_STRING),
            'privileges' => $request->request->get('privileges'),
        ];

        if ($userGroup->update($data)) {
            $level = 'success';
            $message = _('User group updated successfully.');
        } else {
            $level = 'error';
            $message = sprintf(_('Unable to update the user group with id "%d"'), $data['id']);
        }

        $this->get('session')->getFlashBag()->add(
            $level,
            $message
        );

        return $this->redirect(
            $this->generateUrl('admin_acl_usergroup_show', array('id' => $userGroup->id))
        );
    }

    /**
     * Deletes a user group given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('GROUP_DELETE')")
     *
     * @CheckModuleAccess(module="USER_GROUP_MANAGER")
     **/
    public function deleteAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $userGroup = new \UserGroup();
        $deleted = $userGroup->delete($id);
        if ($deleted) {
            $this->get('session')->getFlashBag()->add(
                'success',
                _('User group deleted successfully.')
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to delete the user group with id "%d"'), $id)
            );
        }

        return $this->redirect($this->generateUrl('admin_acl_usergroups'));
    }
}
