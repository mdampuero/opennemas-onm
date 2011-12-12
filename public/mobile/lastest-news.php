<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');

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

require('sections.php');

$section = (!empty($subcategory_name))? $subcategory_name: $category_name;
$section = (is_null($section))? 'home': $section;
$tpl->assign('section', $section);

//$tpl->loadConfigOrDefault('template.conf', $section); // $category_name is a string

//Obtenemos los articulos
$cm = new ContentManager();

/****************************************  NOTICIAS  *******************************************/
$photos = array();
/*$articles_home = $cm->find('Article', 'in_home=1 AND frontpage=1 AND available=1 AND content_status=1 AND fk_content_type=1',
                           'ORDER BY placeholder ASC, home_pos ASC, created DESC');*/
$articles_home = $cm->find('Article', 'available=1 AND content_status=1 AND fk_content_type=1',
                           'ORDER BY created DESC, changed DESC LIMIT 0, 20');
if(empty($articles_home)) {
    $articles_home = $cm->find('Opinion', 'available=1 and type_opinion=0',
                                      'ORDER BY created DESC, position ASC LIMIT 0, 20');

}
// Filter by scheduled {{{
$articles_home = $cm->getInTime($articles_home);
// }}}

foreach($articles_home as $i => $article) {
    $articles_home[$i]->category_name = $articles_home[$i]->loadCategoryName($articles_home[$i]->id);        
}

$tpl->assign('articles_home', $articles_home);

/**************************************  PHOTOS  ***********************************************/
$imagenes = array();
foreach($articles_home as $i => $art) {
    if(isset($art->img1)) {
        $imagenes[] = $art->img1;
    }
}

if(count($imagenes)>0) {
    $imagenes = $cm->find('Photo', 'pk_content IN ('. implode(',', $imagenes) .')');

    $photos = array();
    foreach($articles_home as $i => $art) {    
        if(isset($art->img1)) {
            // Buscar la imagen
            foreach($imagenes as $img) {
                if($img->pk_content == $art->img1) {
                    // Load thumbnails
                    $photos[$art->id] = $img->path_file . '140x100-' . $img->name;
                    break;
                }
            }
        }
    }
}

$tpl->assign('photos', $photos);
/**************************************  PHOTOS  ***********************************************/


$tpl->display('mobile/latest-news.tpl'); // Without cache because is a lastest news section