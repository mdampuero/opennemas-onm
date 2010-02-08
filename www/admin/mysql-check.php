<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

require_once('core/privileges_check.class.php');
if( !Privileges_check::CheckPrivileges('NOT_ADMIN')) {
    Privileges_check::AccessDeniedAction();
}



// Ejemplo para tener objeto global
require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Mysql check');

if(isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
	case 'check':
        $mysqlcheck = "libs/tuning-primer.sh all";
	break;

	default:
        Application::forward('mysql-check.php');
	break;
	}

    exec($mysqlcheck, $return);
    $tpl->assign('return', $return);


} else {
        $mysqlcheck = "libs/tuning-primer.sh --help";
        exec($mysqlcheck, $return);
}


$tpl->display('mysql-check.tpl');

?>

