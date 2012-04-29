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


use Message as m;

if(!Acl::isMaster()) {
    m::add("You don't have permissions");
    Application::forward('/admin/');
}

function buildFilter() {

    if(isset($_REQUEST['module']) && !empty($_REQUEST['module'])) {
        return 'module="'.$_REQUEST['module'].'"';
    }

    return null;
}




$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Grant management');

$privilege = new Privilege();

$_REQUEST['action'] = isset($_REQUEST['action'])? $_REQUEST['action']: 'list';
switch($_REQUEST['action']) {
    case 'list': {
        $filter = buildFilter();

        $privileges = $privilege->get_privileges($filter);
        // FIXME: Set pagination
        $tpl->assign('privileges', $privileges);

        // To filter
        $modules = $privilege->getModuleNames();
        $tpl->assign('modules', $modules);
        $tpl->display('acl/privilege/list.tpl');
    } break;

    // Crear un nuevo permiso
    case 'new': {
        $modules = $privilege->getModuleNames();
        $tpl->assign('modules', $modules);
        $tpl->display('acl/privilege/new.tpl');
    } break;

    case 'read': {
        $privilege->read($_REQUEST['id']);
        $tpl->assign('privilege', $privilege);
        $tpl->assign('id', $privilege->pk_privilege);

        $modules = $privilege->getModuleNames();
        $tpl->assign('modules', $modules);
        $tpl->display('acl/privilege/new.tpl');
    } break;

    case 'create': {
        if($privilege->create( $_POST )) {
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
        } else {
            $tpl->assign('errors', $privilege->errors);
        }
        $tpl->display('acl/privilege/new.tpl');
    } break;

    case 'update': {

        m::add("You don't have permissions");
        //$privilege->update( $_REQUEST );
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');


    } break;

    case 'validate': {
        $privilege = null;
        if(empty($_POST["id"])) {
            $privilege = new Privilege();
            if(!$privilege->create( $_POST ))
                $tpl->assign('errors', $user->errors);
        } else {
            m::add("You don't have permissions");
             $privilege = new Privilege($_REQUEST["id"]);
         //   $privilege->update( $_REQUEST );
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$privilege->id);
    } break;

    case 'delete': {
        m::add("You don't have permissions");
      //  $privilege->delete( $_POST['id'] );
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
    } break;

    default: {
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
    } break;
}
