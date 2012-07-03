<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controllers;

use Onm\Framework\Controller\Controller,
    Onm\Message as m;
/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
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
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        $this->checkAclOrForward('GROUP_ADMIN');

        $this->privilege = new \Privilege();
    }

    /**
     * List all the user groups
     *
     * @return void
     **/
    public function listAction()
    {
        $userGroup  = new \UserGroup();
        $userGroups = $userGroup->get_user_groups();

        return $this->render('acl/user_group/list.tpl', array(
            'user_groups' => $userGroups
        ));
    }

    /**
     * Shows the form for editting a user group
     *
     * @return string the response string
     **/
    public function showAction()
    {
        $id = $this->request->query->filter('id', FILTER_VALIDATE_INT);

        $userGroup = new \UserGroup($id);
        if (is_null($userGroup->id)) {
            m::add(sprintf(_("Unable to find user group with id '%d'"), $id), m::ERROR);

            return $this->redirect($this->generateUrl('admin_acl_usergroups'));
        }
        $privilege = new \Privilege();

        return $this->render('acl/user_group/new.tpl', array(
            'user_group' => $userGroup,
            'modules'    => $privilege->getPrivilegesByModules(),
        ));
    }

    /**
     * Handles the action of show creation form for user group and save it
     *
     * @return string the response string
     **/
    public function createAction()
    {
        $this->checkAclOrForward('GROUP_GREATE');

        $userGroup = new \UserGroup();
        $privilege = new \Privilege();

        if ($this->request->getMethod() == 'POST') {
            // Try to save the new privilege
            if ($userGroup->create( $_POST )) {
                // If user group was saved successfully and the action
                // is validate show again the form
                if ($this->request->get('action') == 'validate') {
                    return $this->redirect(url('admin_acl_usergroups'));
                } else {
                    return $this->redirect(url('admin_acl_usergroups_show',
                        array('id' => $userGroup->id)));
                }
            } else {
                $this->view->assign('errors', $userGroup->errors);
            }
        }

        return $this->render('acl/user_group/new.tpl', array(
            'user_group' => $userGroup,
            'modules'    => $privilege->getPrivilegesByModules(),
        ));
    }

    /**
     * Updates the user group information given its id and the new information
     *
     * @return string the return string
     **/
    public function updateAction()
    {
        $this->checkAclOrForward('GROUP_UPDATE');

        $userGroup = new \UserGroup();
        $userGroup->update($_REQUEST);

        return $this->redirect(url('admin_acl_usergroups'));
    }

    /**
     * Deletes a user group given its id
     *
     * @return string the string response
     **/
    public function deleteAction()
    {
        $this->checkAclOrForward('GROUP_DELETE');

        $id = $this->request->query->filter('id', FILTER_VALIDATE_INT);

        $userGroup = new UserGroup();
        $deleted = $userGroup->delete($id);
        if (!$deleted) {
            m::add(sprintf(
                _('Unable to delete the user group with id "%d"')),
                $id
            );
        }

        return $this->redirect(url('admin_acl_usergroups'));
    }

} // END class AclUserGroupsController
