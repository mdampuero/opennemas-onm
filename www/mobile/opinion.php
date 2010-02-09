<?php
//error_reporting(E_ALL);
require_once('../config.inc.php');
require_once('../core/application.class.php');

Application::import_libs('*');
$app = Application::load();

require_once('../core/content_manager.class.php');
require_once('../core/content.class.php');
require_once('../core/content_category.class.php');
require_once('../core/content_category_manager.class.php');

require_once('../core/article.class.php');
require_once('../core/author.class.php');
require_once('../core/opinion.class.php');
require_once('../core/photo.class.php');

$tpl = new Template(TEMPLATE_USER);

/******************** CATEGORIA ********************************************************/
$ccm = new ContentCategoryManager();

// This page has category name opinion, always. Don't use redirection
$category_name = $_GET['category_name'] = 'opinion';

// Category manager to retrieve category of article
$ccm = ContentCategoryManager::get_instance();
$tpl->assign('ccm', $ccm);
$tpl->assign('section', 'opinion');

$opinion = new Opinion( $_REQUEST['pk_content'] );
$tpl->assign('opinion', $opinion);

$aut = new Author($opinion->fk_author);

$tpl->assign('author_name', $aut->name);
$tpl->assign('condition', $aut->condition);
        
$foto = $aut->get_photo($opinion->fk_author_img);
$tpl->assign('photo', $foto);

// Show in Frontpage
$tpl->display('mobile/opinion.tpl');
