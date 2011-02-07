<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

// redirect to /mobile/ if it's mobile device request
$app->mobileRouter();

$tpl = new Template(TEMPLATE_USER);

$cm  = new ContentManager();
$ccm = ContentCategoryManager::get_instance();

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

// Get category and subcategory
$category_name = (isset($_GET['category_name'])) ? $_GET['category_name'] : 'home';
$actual_category = $category_name;
if (isset ($_GET['subcategory_name'])) {
    $subcategory_name = $_GET['subcategory_name'];
    $actual_category = $_GET['subcategory_name'];
}

require_once ("index_sections.php");
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
require_once ("index_advertisement.php");
               
// Visualizar
$tpl->display('search/search.tpl');
