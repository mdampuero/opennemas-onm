<?php
/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

// Barra superior
$tpl->assign("imagen_barra", 'admin_general.gif');
$tpl->assign("titulo_barra", 'Panel de Control');

$tpl->display('inicio.tpl');

