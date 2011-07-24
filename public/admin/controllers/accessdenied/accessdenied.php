<?php

/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

/**
 * Get the last url
 */
$lastUrl = $_SESSION['lasturl'];

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('linkReturn', $lastUrl);
$tpl->display('accessdenied/accessdenied.tpl');
