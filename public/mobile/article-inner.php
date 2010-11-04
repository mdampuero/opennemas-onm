<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');

/**
 * Setup view
*/
$tpl = new Template(TEMPLATE_USER);

// Category manager to retrieve category of article
$ccm = ContentCategoryManager::get_instance();
$cm = new ContentManager();
$tpl->assign('ccm', $cm);
$tpl->assign('ccm', $ccm);

require('sections.php');

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
$tpl->display('mobile/article-inner.tpl');