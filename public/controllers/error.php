<?php
/*
 * This file is part of the Onm package.
 *
 * (c)  Fran Diéguez <fran@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

/**
 * Setup view
*/
$tpl = new Template(TEMPLATE_USER);
$errorCode = filter_input(INPUT_GET, 'errordoc');

/**
 * Fetch HTTP variables
*/

$category_name = filter_input(INPUT_GET,'category_name',FILTER_SANITIZE_STRING);
if ( !(isset($category_name) && !empty($category_name)) ) {
    $category_name = 'home';
}

$subcategory_name = filter_input(INPUT_GET,'subcategory_name',FILTER_SANITIZE_STRING);
$cache_page = filter_input(INPUT_GET,'page',FILTER_VALIDATE_INT);
$cache_page = (is_null($cache_page))? 0 : $cache_page;

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
$ccm = ContentCategoryManager::get_instance();
$cm = new ContentManager();
require_once ("index_sections.php");

$page = new stdClass();

// Dummy content while testing this feature
$page->title = 'No hemos podido encontrar la página que buscas.';
$page->content = 'Whoups!';


$tpl->assign('category_real_name', $page->title);
$tpl->assign('page', $page);   


require_once("widget_static_pages.php");
/********************************* ADVERTISEMENTS  *********************************************/
require_once ("statics_advertisement.php");
/********************************* ADVERTISEMENTS  *********************************************/


$tpl->display('static_pages/statics.tpl');