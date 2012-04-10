<?php

use Onm\Settings as s,
    Onm\Message as m;

/**
 * Setup application
*/
require_once '../bootstrap.php';
require_once 'session_bootstrap.php';

/**
 * Set up view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

$ccm = new ContentCategoryManager();

// Initialize request parameters
global $request;
$action = $request->request->filter('action', null, FILTER_SANITIZE_STRING);
if (empty($action)) {
    $action = $request->query->filter('action', 'list', FILTER_SANITIZE_STRING);
}
// var_dump($action);die();

if (!array_key_exists('page', $_REQUEST)) {
    $page = 0;
} else {
    $page = $_REQUEST['page'] ?:0;
}
switch ($action) {
    case 'list':
        Acl::checkOrForward('USER_ADMIN');

        $cm = new ContentManager();
        $user = new User();

        $filters = (isset($_GET['filter']))? $_GET['filter']: null;
        $users = $user->get_users($filters, ' ORDER BY login ');

        $users = $cm->paginate_num($users, ITEMS_PAGE);

        $user_group = new UserGroup();
        $group      = $user_group->get_user_groups();

        $groupsOptions = array();
        $groupsOptions[] = _('--All--');
        foreach($group as $cat) {
            $groupsOptions[$cat->id] = $cat->name;
        }

        $tpl->assign( array(
            'users' => $users,
            'paginacion' => $cm->pager,
            'user_groups' => $group,
            'groupsOptions' => $groupsOptions,
        ));

        $tpl->display('acl/user/list.tpl');

    break;

    case 'new':
        Acl::checkOrForward('USER_CREATE');

        $user = new User( $_REQUEST['id'] );
        $userGroup = new UserGroup();
        $tpl->assign('user', $user);
        $tpl->assign('user_groups', $userGroup->get_user_groups());

        $tree = $ccm->getCategoriesTree();
        $tpl->assign('content_categories', $tree);

        $tpl->display('acl/user/new.tpl');
    break;

    case 'read': {
        //user can modify his data
        $id = $request->query->getDigits('id');
        // Check if the user is the same as the one that we want edit or if we have
        // permissions for editting other user information.
        if ($id != $_SESSION['userid']) { Acl::checkOrForward('USER_UPDATE'); }
        $user = new User($id);

        $user_group = new UserGroup();
        $tpl->assign('user', $user);
        $tpl->assign('user_groups', $user_group->get_user_groups());

        $tree = $ccm->getCategoriesTree();
        $tpl->assign('content_categories', $tree);
        $tpl->assign('content_categories_select', $user->get_access_categories_id());


        $tpl->display('acl/user/new.tpl');
    } break;

    case 'update':

        if ($_REQUEST['id'] != $_SESSION['userid']) {
            Acl::checkOrForward('USER_UPDATE');
        }
        // TODO: validar datos
        $user = new User($_REQUEST['id']);
        $user->update( $_REQUEST );

        if ( ($_REQUEST['id'] == $_SESSION['userid']) && !Acl::check('USER_UPDATE') ) {
            Application::forward('/admin/');
        } else {
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$page);
        }
    break;

    case 'create':
        Acl::checkOrForward('USER_CREATE');

        $user = new User();
        if($user->create( $_POST )) {
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$page);
        } else {
            $tpl->assign('errors', $user->errors);
        }
        $tpl->display('acl/user/new.tpl');
    break;

    case 'validate':

        if(empty($_POST["id"])) {
            Acl::checkOrForward('USER_CREATE');

            $user = new User();
            if(!$user->create( $_POST )) {
                $tpl->assign('errors', $user->errors);
            }

        } else {

            if ($_POST['id'] != $_SESSION['userid']) {
                Acl::checkOrForward('USER_UPDATE');
            }

            $user = new User($_POST['id']);
            $user->update( $_REQUEST );

        }

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$user->id);
    break;

    case 'change_authorize':

        $user = new User( $_REQUEST['id'] );
        //Autorizar o no , comprobar...
        $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
        if($status == 1){
            $user->unauthorize_user($user->id);
        } else {
            $user->authorize_user($user->id);
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$page);
    break;

    case 'delete':
        Acl::checkOrForward('USER_DELETE');

        $user = new User();
        $user->delete( $_POST['id'] );
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$page);
    break;

    case 'mdelete':
        Acl::checkOrForward('USER_DELETE');

        if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
        {
            $fields = $_REQUEST['selected_fld'];
            if(is_array($fields))
            {
                $user = new User();
                foreach($fields as $i )
                {
                    $user->delete( $i );
                }
            }
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$page);
        break;

    default: {
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$page);
    } break;
}

