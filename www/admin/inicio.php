<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');
// TODO: control de sesión

// Ejemplo para tener objeto global
require_once('core/application.class.php');
Application::import_libs('*');
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

// Barra superior
$tpl->assign("imagen_barra", 'admin_general.gif');
$tpl->assign("titulo_barra", 'Panel de Control');

$tpl->display('inicio.tpl');

