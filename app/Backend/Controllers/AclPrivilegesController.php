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
 * @author
 **/
class AclPrivilegesController extends Controller
{

    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        // Initializae the session manager
        require_once './session_bootstrap.php';

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        if(!\Acl::isMaster()) {
            m::add("You don't have permissions");

            return $this->redirect(url('admin_welcome'));
        }

        $this->privilege = new \Privilege();
    }

    /**
     * List all the privileges
     *
     * @return void
     **/
    public function listAction()
    {

        $filter = ' 1=1 ';
        if(isset($_REQUEST['module']) && !empty($_REQUEST['module'])) {
            $filter = 'module="'.$_REQUEST['module'].'"';
        }

        $privileges = $this->privilege->get_privileges($filter);

        return $this->render('acl/privilege/list.tpl', array(
            'privileges' => $privileges,
            'modules'    => $this->privilege->getModuleNames()
        ));
    }

    /**
     * Handles the action of show creation form for privileges
     *
     * @return string the response string
     **/
    public function createAction()
    {

        if ($this->request->getMethod() == 'POST') {
            // Try to save the new privilege
            if ($this->privilege->create( $_POST )) {
                // If privilege was saved successfully and the action is validate
                // show again the form
                if ($this->request->get('action') != 'validate') {
                    $this->redirect(url('admin_acl_privileges'));
                }
            } else {
                $this->view->assign('errors', $privilege->errors);
            }
        }
        $modules = $this->privilege->getModuleNames();

        return $this->render('acl/privilege/new.tpl', array(
            'modules' => $modules,
        ));
    }


    /**
     * Shows the form for editting a privilege
     *
     * @return string the response string
     **/
    public function showAction()
    {
        $id = $this->request->query->filter('id', FILTER_VALIDATE_INT);

        $this->privilege->read($id);
        $modules = $this->privilege->getModuleNames();

        return $this->render('acl/privilege/new.tpl',array(
            'privilege' => $this->privilege,
            'id'        => $this->privilege->pk_privilege,
            'modules'   => $modules,
        ));
    }

    /**
     * Updates the privilege information given its id and the new information
     *
     * @return string the return string
     **/
    public function updateAction()
    {
        m::add("For now is not possible to update privilege information.");
        //$privilege->update( $_REQUEST );
        return $this->redirect(url('admin_acl_privileges'));
    }


    /**
     * Deletes a privilege given its id
     *
     * @return string the string response
     **/
    public function deleteAction()
    {
        $id = $this->request->query->filter('id', FILTER_VALIDATE_INT);

        $deleted = $this->privilege->delete($id);
        if (!$deleted) {
            m::add(sprintf(_('Unable to delete privilege with id "%d"')), $id);
        }

        return $this->redirect(url('admin_acl_privileges'));
    }



} // END class AclPrivilegesController
