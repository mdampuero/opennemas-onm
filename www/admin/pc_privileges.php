<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

// Ejemplo para tener objeto global
require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Gesti&oacute;n de Permisos');

require_once('core/privilege.class.php');


if( isset($_REQUEST['action']) ) {
	switch($_REQUEST['action']) {
		case 'list':
			$privilege = new Privilege();
			$privileges = $privilege->get_privileges();
			// FIXME: Set pagination
			$tpl->assign('privileges', $privileges);
		break;

		// Crear un nuevo permiso
		case 'new':
			$privilege = new Privilege( $_REQUEST['id'] );
			$tpl->assign('privilege', $privilege);
		break;

		case 'read':
			$privilege = new Privilege( $_REQUEST['id'] );
			$tpl->assign('privilege', $privilege);
		break;

		case 'update':
			// TODO: validar datos
			$privilege = new Privilege();
			$privilege->update( $_REQUEST );
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
		break;

		case 'create':
			$privilege = new Privilege();
			if($privilege->create( $_POST )) {
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
			} else {
				$tpl->assign('errors', $privilege->errors);
			}
		break;

		case 'delete':
			$privilege = new Privilege();
			$privilege->delete( $_POST['id'] );
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
		break;
		default:
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
		break;
	}
} else {
	Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
}

$tpl->display('privilege.tpl');
?>