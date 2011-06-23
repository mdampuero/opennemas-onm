<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Setup application
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

// Check ACL {{{
require_once(SITE_CORE_PATH.'privileges_check.class.php');
if(!Acl::check('USER_ADMIN')) {
    Acl::deny();
    }
    // }}}
/**
 * Set up view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'User Management');

$ccm = new ContentCategoryManager();

if( isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
        case 'list': {
            $cm = new ContentManager();
            $user = new User();

            $filters = (isset($_REQUEST['filter']))? $_REQUEST['filter']: null;
            $users = $user->get_users($filters);

            $users = $cm->paginate_num($users,12);
            $tpl->assign('users', $users);

            $tpl->assign('paginacion', $cm->pager);

            $user_group = new User_group();
            $group      = $user_group->get_user_groups();

            $groupsOptions = array();
            $groupsOptions[] = '-- Seleccione un grupo --';
            foreach($group as $cat) {
                $groupsOptions[$cat->id] = $cat->name;
            }
            $tpl->assign('user_groups', $group);
            $tpl->assign('groupsOptions', $groupsOptions);
        } break;

        case 'new': {
            $user = new User( $_REQUEST['id'] );
            $user_group = new User_group();
            $tpl->assign('user', $user);
            $tpl->assign('user_groups', $user_group->get_user_groups());

            $tree = $ccm->getCategoriesTree();
            $tpl->assign('content_categories', $tree);
        } break;

        case 'read': {
            $user = new User( $_REQUEST['id'] );

            $user_group = new User_group();
            $tpl->assign('user', $user);
            $tpl->assign('user_groups', $user_group->get_user_groups());

            $tree = $ccm->getCategoriesTree();
            $tpl->assign('content_categories', $tree);
            $tpl->assign('content_categories_select', $user->get_access_categories_id());

            /*$tpl->assign('categories_options',  $user->get_categories_options());
            $tpl->assign('categories_selected', $user->get_access_categories_id());*/
        } break;

        case 'update': {
            // TODO: validar datos
            $user = new User();
            $user->update( $_REQUEST );
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
        } break;

        case 'create': {
            $user = new User();

            if($user->create( $_POST )) {
                Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
            } else {
                $tpl->assign('errors', $user->errors);
            }
        } break;

        case 'delete': {
            $user = new User();
            $user->delete( $_POST['id'] );
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
        } break;

        case 'change_authorize': {
            $user = new User( $_REQUEST['id'] );
            //Autorizar o no , comprobar...
            $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
            if($status == 1){
                $user->unauthorize_user($user->id);
            } else {
                $user->authorize_user($user->id);
            }
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
        } break;

        case 'validate': {
            $user = null;
            if(empty($_POST["id"])) {
                $user = new User();
                if(!$user->create( $_POST )) {
                    $tpl->assign('errors', $user->errors);
                }
            } else {
                $user = new User($_POST["id"]);
                $user->update( $_REQUEST );
            }
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$user->id);
        } break;

        case 'mdelete':
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

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
            break;

        default: {
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
        } break;
    }

} else {
    $page = (isset($_REQUEST['page'])?$_REQUEST['page']:"");
    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page=$page');
}

$tpl->display('acl/user/user.tpl');
