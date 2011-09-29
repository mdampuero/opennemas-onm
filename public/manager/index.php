<?php
/**
 * Setup app
*/
require_once('../bootstrap.php');
//require_once(SITE_PATH.DS.'admin'.DS.'session_bootstrap.php');
//$sessions = $GLOBALS['Session']->getSessions();

/**
 * Setup view
*/
$tpl = new \TemplateManager(TEMPLATE_ADMIN);

$tpl->display('index/index.tpl');
