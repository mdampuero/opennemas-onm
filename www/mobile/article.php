<?php
//error_reporting(E_ALL);
require('../config.inc.php');
require_once('../core/application.class.php');

Application::import_libs('*');
$app = Application::load();

require_once('../core/content_manager.class.php');
require_once('../core/content.class.php');
require_once('../core/article.class.php');
require_once('../core/author.class.php');
require_once('../core/content_category.class.php');
require_once('../core/photo.class.php');
require_once('../core/content_category_manager.class.php');

// For performance
require_once('../core/method_cache_manager.class.php');

$tpl = new Template(TEMPLATE_USER);

// Category manager to retrieve category of article
$ccm = ContentCategoryManager::get_instance();
$tpl->assign('ccm', $ccm);

$article = new Article($_REQUEST['pk_content']);
$article->category_name = $ccm->get_name($article->category);

$tpl->assign('article', $article);

$tpl->assign('section', $article->category_name);

// Photo interior
if(isset($article->img2) and ($article->img2 != 0)){
    $photo = new Photo($article->img2);
    $tpl->assign('photo', $photo->path_file . '140x100-' . $photo->name);
}

//TODO: define cache system
$tpl->display('mobile/article.tpl');