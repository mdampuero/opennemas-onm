<?php
/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');

/**
 * Setup view
*/
$tpl = new \TemplateManager(TEMPLATE_ADMIN);

$action = (isset($_REQUEST['action']))? $_REQUEST['action']: null;

switch($action) {
    
    
    case 'list':
    default: {
        
        $tpl->display('cache_manager/index.tpl');
        break;
    }
}