<?php

/**
 * Setup app
 **/
require_once('../bootstrap.php');

/**
 * Setup view
 **/
$tpl = new Template(TEMPLATE_USER);

/**
 * Setup cache
 **/
$tpl->setConfig('articles-mobile');

/**
 * Getting request params
 **/
$dirtyID = filter_var($_GET['pk_content'], FILTER_SANITIZE_STRING);
if(empty($dirtyID)) {
    $dirtyID = filter_input(INPUT_POST,'pk_content',FILTER_SANITIZE_STRING);
}

$articleID = Content::resolveID($dirtyID);
$cacheID = $tpl->generateCacheId('articles-mobile','',$articleID);

/**
 * if cache is enabled and this content has an available cache
 **/
if(($tpl->caching == 0)
    || ! $tpl->isCached('mobile/article-inner.tpl', $cacheID)) {

    //$articleID = filter_input(INPUT_GET,'pk_content',FILTER_SANITIZE_STRING);

    // Category manager to retrieve category of article
    $ccm = ContentCategoryManager::get_instance();
    $cm = new ContentManager();
    $tpl->assign('ccm', $cm);
    $tpl->assign('ccm', $ccm);

    require('sections.php');

    $article = new Article($articleID);
    $article->category_name = $ccm->get_name($article->category);


    /******* RELATED  CONTENT *******/
    $rel= new RelatedContent();

    $relationes = $rel->cache->getRelationsForInner($articleID);
    $relat = $cm->cache->getContents($relationes);
    $relat = $cm->getInTime($relat);
    //Filter availables and not inlitter.
    $relat = $cm->cache->getAvailable($relat);

    //Nombre categoria correcto.
    foreach($relat as $ril) {
        $ril->category_name = $ccm->get_title($ril->category_name);
    }

    $tpl->assign('related_articles', $relat);
    $tpl->assign('article', $article);
    $tpl->assign('section', $article->category_name);

    // Photo interior
    if(isset($article->img2) and ($article->img2 != 0)){
        $photo = new Photo($article->img2);
        $tpl->assign('photo', $photo->path_file . '140-100-' . $photo->name);
    }

}

//TODO: define cache system
$tpl->display('mobile/mobile-article-inner.tpl', $cacheID);
