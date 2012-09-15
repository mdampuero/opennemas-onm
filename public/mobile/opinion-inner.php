<?php
/**
 * Setup app
*/
require_once '../bootstrap.php';

/**
 * Setup view
*/
$tpl = new Template(TEMPLATE_USER);

// This page has category name opinion, always. Don't use redirection
$category_name = $_GET['category_name'] = 'opinion';

/**
 * Getting request params
 **/
$dirtyID = filter_var($_GET['pk_content'], FILTER_SANITIZE_STRING);
if (empty($dirtyID)) {
    $dirtyID = filter_input(INPUT_POST, 'pk_content', FILTER_SANITIZE_STRING);
}

$opinionID = Content::resolveID($dirtyID);

// Category manager to retrieve category of article
$ccm = ContentCategoryManager::get_instance();
$tpl->assign('ccm', $ccm);
$tpl->assign('section', 'opinion');

$opinion = new Opinion($opinionID);
$tpl->assign('opinion', $opinion);

$aut = new Author($opinion->fk_author);

$tpl->assign('author_name', $opinion->name);
$tpl->assign('condition', $opinion->condition);

$foto = $aut->get_photo($opinion->fk_author_img);
$tpl->assign('photo', $foto);

// Show in Frontpage
$tpl->display('mobile/opinion-inner.tpl');

