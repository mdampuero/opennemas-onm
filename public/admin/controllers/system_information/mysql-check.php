<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

require_once(SITE_CORE_PATH.'privileges_check.class.php');
if( !Privileges_check::CheckPrivileges('NOT_ADMIN')) {
    Privileges_check::AccessDeniedAction();
}

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Mysql check');

if(isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
	case 'check':
        $mysqlcheck = SITE_LIBS_PATH.'tuning-primer.sh all';
	break;

	default:
        Application::forward('mysql-check.php');
	break;
	}

    exec($mysqlcheck, $return);
    $tpl->assign('return', $return);


} else {
        $mysqlcheck = SITE_LIBS_PATH.'tuning-primer.sh --help';
        exec($mysqlcheck, $return);
}

$tpl->display('mysql-check.tpl');