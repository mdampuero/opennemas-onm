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
$sessions = $GLOBALS['Session']->getSessions();

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'User Groups Management');

require_once(SITE_CORE_PATH.'user_group.class.php');
require_once(SITE_CORE_PATH.'privilege.class.php');
require_once(SITE_CORE_PATH.'privileges_check.class.php');

Acl::checkOrForward('GROUP_ADMIN');

if( isset($_REQUEST['action']) ) {
	switch($_REQUEST['action']) {
		case 'list':

			$user_group = new User_group();
			$user_groups = $user_group->get_user_groups();
			// FIXME: Set pagination
			$tpl->assign('user_groups', $user_groups);
			$tpl->display('acl/user_group/list.tpl');

		break;

		case 'read':

			$user_group = new User_group($_REQUEST['id']);
			$privilege = new Privilege();
			$tpl->assign('user_group', $user_group);
			$tpl->assign('modules', $privilege->getPrivilegesByModules());
			$tpl->display('acl/user_group/new.tpl');

		break;

        case 'new':

            Acl::checkOrForward('GROUP_GREATE');

            $user_group = new User_group();
			$privilege = new Privilege();
			$tpl->assign('user_group', $user_group);
			$tpl->assign('modules', $privilege->getPrivilegesByModules());
			$tpl->display('acl/user_group/new.tpl');

		break;

		case 'update':

            Acl::checkOrForward('GROUP_UPDATE');
			// TODO: validar datos
			$user_group = new User_group();
			$user_group->update( $_REQUEST );
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');

		break;

		case 'create':

			Acl::checkOrForward('GROUP_CREATE');

			$user_group = new User_group();
			if($user_group->create( $_POST )) {
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
			} else {
				$tpl->assign('errors', $user_group->errors);
			}
			$tpl->display('acl/user_group/user_group.tpl');

		break;

		case 'delete':

            Acl::checkOrForward('GROUP_DELETE');

			$user_group = new User_group();
			$user_group->delete( $_POST['id'] );
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');

		break;


		case 'validate':

			$user_group = new User_group();
			if(empty($_POST["id"])) {
                Acl::checkOrForward('GROUP_CREATE');
				if($user_group->create( $_POST ))
					$tpl->assign('errors', $user_group->errors);
			} else {
                Acl::checkOrForward('GROUP_UPDATE');
				$user_group = new User_group();
				$user_group->update( $_REQUEST );
			}
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$user_group->id);

		break;

		default:
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
		break;
	}

} else {
	Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
}
