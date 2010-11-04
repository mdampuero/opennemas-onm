<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('linkReturn', $_SESSION['lasturl']);
$tpl->display('accessdenied.tpl');


