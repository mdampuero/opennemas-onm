<?php
//error_reporting(E_ALL); -> Have a look to the errors and Notice IMPORTANT
require('config.inc.php');
require_once('core/application.class.php');

Application::import_libs('*');
$app = Application::load();

require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/static_page.class.php');
require_once('core/advertisement.class.php');

$tpl = new Template(TEMPLATE_USER);

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
$ccm = ContentCategoryManager::get_instance();
require_once ("index_sections.php");
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/


if(isset($_REQUEST['slug']) ) {
    $page = Static_Page::getPageBySlug($_REQUEST['slug']);
    
    if(is_null($page) || (!$page->available)) {
        Application::forward('/404.html');
    }
    
    // increment visits
    $page->set_numviews();
    
    // Update head metatags for this page
    $tpl->setMeta('keywords', $page->metadata);
    $tpl->setMeta('description', $page->description);
    
    $tpl->assign('category_real_name', $page->title);
    $tpl->assign('page', $page);   

    $cm = new ContentManager();
    $articles_home_express = $cm->find('Article', 'content_status=1 AND available=1 AND fk_content_type=1', 'ORDER BY created DESC LIMIT 0 , 5 ');    
    $articles_home_express = $cm->getInTime($articles_home_express);
    $tpl->assign('articles_home_express', $articles_home_express);
    
    $pages = $cm->pager;
    $pages_home_express = $pages->_totalPages;
   
    $params = "'articles_home_express',''";
    $pages_home_express = $cm->create_paginate(40, 5, 2, 'get_paginate_articles', $params);
    $tpl->assign('pages_home_express', $pages_home_express);        
} else {
    Application::forward('/');
}

/********************************* ADVERTISEMENTS  *********************************************/
require_once ("statics_advertisement.php");
/********************************* ADVERTISEMENTS  *********************************************/


$tpl->display('statics.tpl');