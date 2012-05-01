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
class AclUserController extends Controller
{

    /**
     * Common code for all the actions
     *
     * @return void
     * @author
     **/
    public function init()
    {
        // Initializae the session manager
        require_once './session_bootstrap.php';

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
    }
    /**
     * Description of the action
     *
     * @return void
     **/
    public function defaultAction()
    {
        \Acl::checkOrForward('USER_ADMIN');

        $filter    = $this->request->query->get('filter', null);

        $cm        = new \ContentManager();
        $user      = new \User();

        $users     = $user->get_users($filter, ' ORDER BY login ');
        $users     = $cm->paginate_num($users, ITEMS_PAGE);

        $userGroup = new \UserGroup();
        $group     = $userGroup->get_user_groups();

        $groupsOptions = array();
        $groupsOptions[] = _('--All--');
        foreach ($group as $cat) {
            $groupsOptions[$cat->id] = $cat->name;
        }

        return $this->render('acl/user/list.tpl', array(
            'users'         => $users,
            'paginacion'    => $cm->pager,
            'user_groups'   => $group,
            'groupsOptions' => $groupsOptions,
        ));
    }


    /**
     * Shows the user information given its id
     *
     * @return void
     **/
    public function showAction()
    {
        //user can modify his data
        $id = $this->request->query->getDigits('id');

        // Check if the user is the same as the one that we want edit or if we have
        // permissions for editting other user information.
        if ($id != $_SESSION['userid']) {
            \Acl::checkOrForward('USER_UPDATE');
        }

        $ccm = new \ContentCategoryManager();

        $user = new \User($id);
        $user_group = new \UserGroup();
        $tree = $ccm->getCategoriesTree();

        return $this->render('acl/user/new.tpl', array(
            'user'                      => $user,
            'user_groups'               => $user_group->get_user_groups(),
            'content_categories'        => $tree,
            'content_categories_select' => $user->get_access_categories_id(),
        ));
    }


    /**
     * Handles the update action for a user given its id
     *
     * @return string the response string
     **/
    public function updateAction()
    {

        $userId = $this->request->request->getDigits('id');
        $action = $this->request->request->filter('action', 'update', FILTER_SANITIZE_STRING);

        if ($userId != $_SESSION['userid']) {
            \Acl::checkOrForward('USER_UPDATE');
        }
        // TODO: validar datos
        $user = new \User($userId);
        $user->update($_REQUEST);

        if ($action == 'validate') {
            return $this->redirect(url('admin_acl_user_show', array('id' => $userId)));
        } else {
            // If a regular user is upating him/her information redirect to welcome page
            if ( ($_REQUEST['id'] == $_SESSION['userid']) && !Acl::check('USER_UPDATE') ) {
                return $this->redirect(url('admin_welcome'));
            } else {
                return $this->redirect(url('admin_acl_user'));
            }
        }
    }

    /**
     * Creates an user give some information
     *
     * @return string the response string
     **/
    public function createAction()
    {
        \Acl::checkOrForward('USER_CREATE');

        if ($this->request->getMethod() == 'POST') {
            $user = new User();
            if ($user->create($_POST)) {
                if ($action == 'validate') {
                    return $this->redirect(url('admin_acl_user_show', array('id' => $user->id)));
                }
                return $this->redirect(url('admin_acl_user'));
            } else {
                $this->view->assign('errors', $user->errors);
            }
        }
        $this->view->display('acl/user/new.tpl');
    }

    /**
     * Deletes a user given its id
     *
     * @return string the response string
     **/
    public function deleteAction()
    {
        \Acl::checkOrForward('USER_DELETE');

        $userId = $this->request->query->getDigits('id');

        if (!is_null($userId)) {
            $user = new \User();
            $user->delete($userId);
            if (!$this->request->isXmlHttpRequest()) {
                return $this->redirect(url('admin_acl_user'));
            }
        }
    }

    /**
     * Deletes multiple users at once given their ids
     *
     * @return string the string resposne
     **/
    public function batchDeleteAction()
    {
        \Acl::checkOrForward('USER_DELETE');

        $selected = $this->request->query->get('selected');

        if (count($selected) > 0) {
            $user = new \User();
            foreach($selected as $userId) {
                $user->delete((int)$userId);
            }
            m::add(sprintf(_('You have deleted %d users.'), count($selected)), m::SUCCESS);
        } else {
            m::add(_('You haven\'t selected any user to delete.'), m::ERROR);
        }
        $this->redirect(url('admin_acl_user'));
    }

} // END class AclUserController