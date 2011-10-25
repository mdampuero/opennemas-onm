<?php

/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');


use Message as m;

//Check if module is activated in this onm instance
\Onm\Module\ModuleManager::checkActivatedOrForward('SCHEDULE_MANAGER');

 // Check if the user can admin album
Acl::checkOrForward('SCHEDULE_ADMIN');

/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Agenda de la Colectividad');

$action = filter_input ( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array( 'options' => array('default' => 'schedule')) );

switch($action) {

    case 'schedule':

        $tpl->display('schedule/schedule.tpl');

        break;

   
}
