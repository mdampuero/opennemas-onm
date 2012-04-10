<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

/**
 * Set up view
*/
$tpl = new Template(TEMPLATE_USER);
 
$slug = $request->query->filter('$slug', null, FILTER_SANITIZE_STRING);

if(isset($slug) ) {

    $page = StaticPage::getPageBySlug($slug);

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
/********************************* ADVERTISEMENTS  *********************************************/
require_once ("statics_advertisement.php");
/********************************* ADVERTISEMENTS  *********************************************/


$tpl->display('static_pages/statics.tpl');