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

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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
     * @Security("hasExtension('USER_GROUP_MANAGER')
     *     and hasPermission('GROUP_ADMIN')")
     */
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
     * @Security("hasExtension('USER_GROUP_MANAGER')
     *     and hasPermission('GROUP_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $id = $request->query->filter('id', FILTER_VALIDATE_INT);

        // Check if user group exists
        $userGroup = new \UserGroup($id);
        if (is_null($userGroup->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_("Unable to find user group with id '%d'"), $id)
            );

            return $this->redirect($this->generateUrl('admin_acl_usergroups'));
        }

        // Get all privileges groupd by module
        $privilege = new \Privilege();
        $allPrivilegesByModules = $privilege->getPrivilegesByModules();
        $totalPrivilegesByModule = [];
        foreach ($allPrivilegesByModules as $module => $elements) {
            $totalPrivilegesByModule[$module] = 0;
            foreach ($elements as $element) {
                if (is_array($userGroup) && in_array($element->id, $userGroup->privileges)) {
                    $totalPrivilegesByModule[$module]++;
                }
            }
        }

        return $this->render(
            'acl/user_group/new.tpl',
            array(
                'user_group'      => $userGroup,
                'modules'         => $allPrivilegesByModules,
                'total_activated' => $totalPrivilegesByModule,
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
     * @Security("hasExtension('USER_GROUP_MANAGER')
     *     and hasPermission('GROUP_CREATE')")
     */
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
     * @Security("hasExtension('USER_GROUP_MANAGER')
     *     and hasPermission('GROUP_UPDATE')")
     */
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
     * @Security("hasExtension('USER_GROUP_MANAGER')
     *     and hasPermission('GROUP_DELETE')")
     */
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
