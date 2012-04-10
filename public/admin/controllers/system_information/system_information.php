<?php

/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');


use Message as m;

if(!Acl::isMaster()) {
    m::add("You don't have permissions");
    Application::forward('/admin/');
}

/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Mysql check');

$action = filter_input ( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array( 'options' => array('default' => 'apc_iframe')) );

switch($action) {

    case 'apc_iframe':

        $tpl->display('system_information/apc_iframe.tpl');

        break;
}
