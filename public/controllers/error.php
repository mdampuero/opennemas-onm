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

$category_name = $request->query->filter('category_name', '', FILTER_SANITIZE_STRING);
$cache_page = $request->query->filter('page', 0, FILTER_VALIDATE_INT);

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

$page = new stdClass();

// Dummy content while testing this feature
$page->title = 'No hemos podido encontrar la página que buscas.';
$page->content = 'Whoups!';


$tpl->assign('category_real_name', $page->title);
$tpl->assign('page', $page);


/********************************* ADVERTISEMENTS  *********************************************/
require_once ("statics_advertisement.php");
/********************************* ADVERTISEMENTS  *********************************************/

$tpl->display('static_pages/statics.tpl');