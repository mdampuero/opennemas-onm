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
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;

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
        // Check if this module is activated in this onm instance
        \Onm\Module\ModuleManager::checkActivatedOrForward('USER_GROUP_MANAGER');
    }

    /**
     * List all the user groups
     *
     * @return Response the response object
     *
     * @Security("has_role('GROUP_ADMIN')")
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
     **/
    public function showAction(Request $request)
    {
        $id = $request->query->filter('id', FILTER_VALIDATE_INT);

        $userGroup = new \UserGroup($id);
        if (is_null($userGroup->id)) {
            m::add(sprintf(_("Unable to find user group with id '%d'"), $id), m::ERROR);

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
                // If user group was saved successfully show again the form
                return $this->redirect(
                    $this->generateUrl(
                        'admin_acl_usergroup_show',
                        array('id' => $userGroup->id)
                    )
                );
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
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('GROUP_UPDATE')")
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

        return $this->redirect($this->generateUrl('admin_acl_usergroups'));
    }
}
