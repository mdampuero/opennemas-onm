<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

$tpl = new Template(TEMPLATE_USER);

/******************** CATEGORIA ********************************************************/
$ccm = new ContentCategoryManager();
$cm = new ContentManager();

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
require_once ("index_sections.php");
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

$tpl->assign('img_playa', SITE_URL.'media/statics/tempo-veran/playas.jpg');
$tpl->assign('img_tiempo', SITE_URL.'media/statics/tempo-veran/tiempo.jpg');

require_once("weather_advertisement.php");
   
// Visualizar
$tpl->display('playas.tpl');
