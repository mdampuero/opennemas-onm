<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

// Ejemplo para tener objeto global
require_once('../core/application.class.php');
Application::import_libs('*');
$app = Application::load();

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Gesti&oacute;n de Grupos de Usuarios');

require_once('../core/content_manager.class.php');
require_once('../core/content.class.php');
require_once('../core/user_group.class.php');
require_once('../core/privilege.class.php');


if( isset($_REQUEST['action']) ) {
	switch($_REQUEST['action']) {
		case 'list':
			$user_group = new PCUser_group();
			$user_groups = $user_group->get_user_groups();
			// FIXME: Set pagination
			$tpl->assign('user_groups', $user_groups);
		break;

		case 'new':
			$user_group = new PCUser_group();
			$privilege = new Privilege();
			$tpl->assign('user_group', $user_group);
			$tpl->assign('privileges', $privilege->get_privileges());
		break;

		case 'read':
			$user_group = new PCUser_group($_REQUEST['id']);
			$privilege = new Privilege();
			$tpl->assign('user_group', $user_group);
			$tpl->assign('privileges', $privilege->get_privileges());
		break;

		case 'update':
			// TODO: validar datos
			$user_group = new PCUser_group();
			$user_group->update( $_REQUEST );
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
		break;

		case 'create':
			$user_group = new PCUser_group();
			if($user_group->create( $_POST )) {
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
			} else {
				$tpl->assign('errors', $user->errors);
			}
		break;

		case 'delete':
			$user_group = new PCUser_group();
			$user_group->delete( $_POST['id'] );
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
		break;
		default:
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
		break;
	}
} else {
	Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
}

$tpl->display('user_group.tpl');
?>