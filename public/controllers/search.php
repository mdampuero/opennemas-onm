<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

// redirect to /mobile/ if it's mobile device request
//$app->mobileRouter();

$tpl = new Template(TEMPLATE_USER);

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

// Get category and subcategory
$category_name = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
$subcategory_name = $request->query->filter('subcategory_name', null, FILTER_SANITIZE_STRING);

$actual_category = $category_name;
if (isset($subcategory_name)) {
    $actual_category =  $subcategory_name;
}

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
require_once ("index_advertisement.php");

// Visualizar
$tpl->display('search/search.tpl');
