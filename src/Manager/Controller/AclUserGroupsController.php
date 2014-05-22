<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Manager\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;

/**
 * Handles the actions for the system information
 *
 * @package Manager_Controllers
 **/
class AclUserGroupsController extends Controller
{

    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \TemplateManager(TEMPLATE_MANAGER);

        $this->privilege = new \Privilege();
    }

    /**
     * List all the user groups
     *
     * @return void
     **/
    public function listAction(Request $request)
    {
        $userGroup  = new \UserGroup();
        $userGroups = $userGroup->find();

        return $this->render(
            'acl/user_group/list.tpl',
            array( 'user_groups' => $userGroups)
        );
    }

    /**
     * Shows the form for editting a user group
     *
     * @return string the response string
     **/
    public function showAction(Request $request)
    {
        $id = $request->query->filter('id', FILTER_VALIDATE_INT);

        $userGroup = new \UserGroup($id);
        if (is_null($userGroup->id)) {
            m::add(sprintf(_("Unable to find user group with id '%d'"), $id), m::ERROR);

            return $this->redirect($this->generateUrl('manager_acl_usergroups'));
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
     * @return string the response string
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
                // If user group was saved successfully and the action
                // is validate show again the form
                if ($this->request->get('action') == 'validate') {
                    return $this->redirect($this->generateUrl('manager_acl_usergroups'));
                } else {
                    return $this->redirect(
                        $this->generateUrl('manager_acl_usergroups_show')
                    );
                }
            } else {
                $this->view->assign('errors', $userGroup->errors);
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
     * @return string the return string
     **/
    public function updateAction(Request $request)
    {
        $userGroup = new \UserGroup();

        $data = array(
            'id'         => $request->query->getDigits('id'),
            'name'       => $request->request->filter('name', '', FILTER_SANITIZE_STRING),
            'privileges' => $request->request->get('privileges'),
        );

        if ($userGroup->update($data)) {
            m::add(_('User group updated successfully.'), m::SUCCESS);
        } else {
            m::add(
                sprintf(_('Unable to update the user group with id "%d"'), $data['id']),
                m::ERROR
            );
        }

        if ($request->request->filter('action') != 'validate') {
            return $this->redirect($this->generateUrl('manager_acl_usergroups'));
        }

        return $this->redirect(
            $this->generateUrl('manager_acl_usergroups_show', array('id' => $userGroup->id))
        );
    }

    /**
     * Deletes a user group given its id
     *
     * @return string the string response
     **/
    public function deleteAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $userGroup = new \UserGroup();
        $deleted = $userGroup->delete($id);
        if ($deleted) {
            m::add(_('User group deleted successfully.'));
        } else {
            m::add(
                sprintf(
                    _('Unable to delete the user group with id "%d"'),
                    $id
                )
            );
        }

        return $this->redirect($this->generateUrl('manager_acl_usergroups'));
    }
}
