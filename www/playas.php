<?php
//error_reporting(E_ALL);
require_once('config.inc.php');
require_once('core/application.class.php');

Application::import_libs('*');
$app = Application::load();

require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/content_category.class.php');
require_once('core/content_category_manager.class.php');
require_once('core/photo.class.php');

require_once('core/advertisement.class.php');

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

// FIXME: non facer esto, empregar na plantilla {$smarty.const.MEDIA_IMG_PATH_WEB}
$tpl->assign('MEDIA_IMG_PATH_WEB', MEDIA_IMG_PATH_WEB);

// Visualizar
$tpl->display('playas.tpl');
