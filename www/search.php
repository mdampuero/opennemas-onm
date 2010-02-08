<?php

require('config.inc.php');
require_once('core/application.class.php');

Application::import_libs('*');
$app = Application::load();

require_once('core/search.class.php');
require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/article.class.php');
require_once('core/advertisement.class.php');

require_once('core/img_galery.class.php');
require_once('core/photo.class.php');
require_once('core/comment.class.php');

require_once('core/content_category.class.php');
require_once('core/content_manager.class.php');
require_once('core/content_category_manager.class.php');

$tpl = new Template(TEMPLATE_USER);

$cm = new ContentManager();

/******************** CATEGORIA ********************************************************/
$ccm = new ContentCategoryManager();

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
require_once ("index_sections.php");
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

require_once ("search_advertisement.php");
               
// Visualizar
$tpl->display('search.tpl');
