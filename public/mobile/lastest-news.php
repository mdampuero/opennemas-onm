<?php

/**
 * Setup app
*/
require_once '../bootstrap.php';

/**
 * Setup view
*/
$tpl = new Template(TEMPLATE_USER);

//Is category initialized redirect the user to /
$category_name    = 'ultimas';
$subcategory_name = null;
$page = $_GET['page'] = 0;

$ccm = ContentCategoryManager::get_instance();
$tpl->assign('ccm', $ccm);

//Get rid of this when posible
require_once 'sections.php';

$section = (!empty($subcategory_name))? $subcategory_name: $category_name;
$section = (is_null($section))? 'home': $section;
$tpl->assign('section', $section);
//$tpl->loadConfigOrDefault('template.conf', $section); // $category_name is a string

//Get content manager instance
$cm = new ContentManager();

$articles_home = $cm->find(
    'Article',
    'available=1 AND content_status=1 AND fk_content_type=1',
    'ORDER BY created DESC, changed DESC LIMIT 0, 20'
);
if (empty($articles_home)) {

    //Fetching content
    $contentsInHomepage = $cm->getContentsForHomepageOfCategory(0);

    //Deleting widgets
    foreach ($contentsInHomepage as $content) {
        if (isset($content->home_placeholder)
           && !empty($content->home_placeholder)
           && ($content->home_placeholder != '')
           && ($content->content_type == 4)
        ) {
            $articles_home[] = $content;
        }
    }
}
//Filter by scheduled
$articles_home = $cm->getInTime($articles_home);

//Load category for articles
foreach ($articles_home as $i => $article) {
    $articles_home[$i]->category_name = $articles_home[$i]->loadCategoryName($articles_home[$i]->id);
}

$tpl->assign('articles_home', $articles_home);

//Get frontpage article image id, if not get inner image id
$imagenes = array();
foreach ($articles_home as $art) {
    if (isset($art->img1) && !empty($art->img1)) {
        $imagenes[] = $art->img1;
    } elseif (isset($art->img2) && !empty($art->img2)) {
        $imagenes[] = $art->img2;
    }
}

//Fetch the array of images
if (count($imagenes)>0) {
    $imagenes = $cm->find('Photo', 'pk_content IN ('. implode(',', $imagenes) .')');

    $photos = array();
    foreach ($articles_home as $art) {
        if ((isset($art->img1)  && !empty($art->img1))
            || (isset($art->img2) && !empty($art->img2))) {
            // Search the images and get path
            foreach ($imagenes as $img) {
                if ($img->pk_content == $art->img1 || $img->pk_content == $art->img2) {
                    // Use thumbnails
                    $photos[$art->id] = $img->path_file . $img->name;
                    break;
                }
            }
        }
    }
}

$tpl->assign('photosArticles', $photos);

// Without cache because is a lastest news section
$tpl->display('mobile/frontpage-mobile.tpl');

