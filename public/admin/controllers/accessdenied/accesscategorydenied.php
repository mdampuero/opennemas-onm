<?php

/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

if (!isset($_REQUEST['category']) || empty($_REQUEST['category'])) {
    $_REQUEST['category'] = '';
}

$lastUrl = $_SESSION['lasturlcategory'];
$lastCategory = $_REQUEST['category'];

$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

$tpl->assign('linkReturn', $lastUrl);
$tpl->assign('category', $lastCategory);

$tpl->display('accessdenied/accesscategorydenied.tpl');
