<?php
/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

if( Privileges_check::CheckPrivileges('USER_ADMIN') ) {
    // Peticiones por Ajax
    if(
        isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        && ( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
    ) {
        $action = (isset($_REQUEST['action']))? $_REQUEST['action']: 'list';
        switch($action) {
            case 'purge':
                if($_SESSION['userid']!=$_REQUEST['userid']) {
                    $GLOBALS['Session']->purgeSession( intval($_REQUEST['userid']) );
                    $sessions = $GLOBALS['Session']->getSessions();
                }
            case 'show_panel':
                $sessions = $GLOBALS['Session']->getSessions();
                $tpl->assign('users', $sessions);
                $tpl->display('acl/panel/show_panel.ajax.html');
            break;

            case 'list':
            default:
                $sessions = $GLOBALS['Session']->getSessions();
                echo json_encode( $sessions );
            break;
        }

        exit(0); // Finalizar la petición por Ajax
    }

}