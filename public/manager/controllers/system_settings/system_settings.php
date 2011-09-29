<?php
/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');

use \Onm\Instance\InstanceManager as im;

/**
 * Setup view
*/
$tpl = new \TemplateManager(TEMPLATE_ADMIN);

$action = (isset($_REQUEST['action']))? $_REQUEST['action']: null;

switch($action) {
    
    
    case 'list':
    default: {
        
        $tpl->display('system_settings/index.tpl');
        break;
    }
}
