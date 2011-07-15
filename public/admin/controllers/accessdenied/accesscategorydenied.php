<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

if (!isset($_REQUEST['category']) || empty($_REQUEST['category'])) {
    $_REQUEST['category'] = '';
}

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('linkReturn', $_SESSION['lasturlcategory']);
$tpl->assign('category', $_REQUEST['category']);
$tpl->display('accesscategorydenied.tpl');