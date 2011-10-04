<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

/**
 * Set up view
*/
$tpl = new Template(TEMPLATE_USER);

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
$ccm = ContentCategoryManager::get_instance();
$cm = new ContentManager();
require_once ("index_sections.php");
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

$slug = filter_input(INPUT_GET,'slug',FILTER_SANITIZE_STRING);

if(isset($slug) ) {
    
    $page = Static_Page::getPageBySlug($slug);
 
    // if static page doesn't exist redirect to 404 error page.
    if(is_null($page) || (!$page->available)) {
        Application::forward('/404.html');
    }
    
    // increment visits for this page
    //$page->setNumViews();
    Content::setNumViews($page->pk_static_page);
    
    
    $tpl->assign('category_real_name', $page->title);
    $tpl->assign('page', $page);   

   
          
} else {
    Application::forward('/');
}
 require_once("widget_static_pages.php");
/********************************* ADVERTISEMENTS  *********************************************/
require_once ("statics_advertisement.php");
/********************************* ADVERTISEMENTS  *********************************************/


$tpl->display('static_pages/statics.tpl');