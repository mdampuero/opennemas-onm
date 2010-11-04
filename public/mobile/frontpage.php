<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');

/**
 * Setup view
*/
$tpl = new Template(TEMPLATE_USER);
$tpl->setConfig('frontpage-mobile');

//Is category initialized redirect the user to /
preg_match( '%^seccion/(?P<category_name>[a-z0-9\-\._]+)/$%i',
                    filter_input(INPUT_GET,'url',FILTER_SANITIZE_URL), $sectionssss);
$category_name = isset($sectionssss[1])? $sectionssss[1] : null;

$subcategory_name = filter_input(INPUT_GET, 'subcategory_name', FILTER_SANITIZE_STRING);
$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);

$ccm = ContentCategoryManager::get_instance();
list($category_name, $subcategory_name) = $ccm->normalize($category_name, $subcategory_name);

if(($category_name!='home') && ($category_name!='')) {
    if($ccm->isEmpty($category_name) && is_null($subcategory_name)) {
        $subcategory_name = $ccm->get_first_subcategory($ccm->get_id($category_name));
        if(is_null($subcategory_name)){
            Application::forward301('/mobile/');
        } else {
            Application::forward301('/mobile/seccion/'.$category_name.'/'.$subcategory_name.'/');
        }
    }
}

$tpl->assign('ccm', $ccm);

// Engadido $_GET['page'] para ter en conta o tema da paxinación no cacheo
$cache_id = $tpl->generateCacheId($category_name, $subcategory_name, $page);

if(($tpl->caching == 0) || !$tpl->isCached('mobile/mobile.index.tpl', $cache_id)) {

    $section = (!empty($subcategory_name))? $subcategory_name: $category_name;
    $section = (is_null($section))? 'home': $section;
    $tpl->assign('section', $section);
    
    //$tpl->loadConfigOrDefault('template.conf', $section); // $category_name is a string
    
    //Obtenemos los articulos
    $cm = new ContentManager();
    
    /****************************************  NOTICIAS  *******************************************/
    require('sections.php');
    
    $photos = array();
    if ($section == 'home') {
        
        $articles_home = $cm->find_all('Article',
                            'contents.in_home=1 AND contents.frontpage=1 '
                            .'AND contents.available=1 AND contents.content_status=1 '
                            .'AND contents.fk_content_type=1 ',
                            'ORDER BY home_pos ASC, created DESC');
        
        // Filter by scheduled {{{
        $articles_home = $cm->getInTime($articles_home);
        // }}}
        
        $destaca = array();
        foreach($articles_home as $i => $article) {
        $articles_home[$i]->category_name = $articles_home[$i]->loadCategoryName($articles_home[$i]->id);        
        $article->category_name = $articles_home[$i]->category_name;
        
        if($article->home_placeholder == 'placeholder_0_0') {
            $destaca[] = $article;
        }
        }
        
    } else {
        $tpl->assign('section', $category_name);
    
        $articles_home = $cm->find_by_category_name('Article', $section, 'frontpage=1 AND content_status=1 AND available=1  AND fk_content_type=1',
                                                    'ORDER BY placeholder ASC, position ASC, created DESC');
    
        // Filter by scheduled {{{
        $articles_home = $cm->getInTime($articles_home);
        // }}}
        
        $destaca = array();
        foreach($articles_home as $i => $article) {
        $articles_home[$i]->category_name = $articles_home[$i]->loadCategoryName($articles_home[$i]->id);        
        $article->category_name = $articles_home[$i]->category_name;
        
            if($article->placeholder == 'placeholder_0_0') {
                $destaca[] = $article;
            }                
        }
    }
    
    $tpl->assign('destaca', $destaca);
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
                        // Use thumbnails
                        $photos[$art->id] = $img->path_file . $img->name;
                        break;
                    }
                }
            }
        }
    }
    
    
    $tpl->assign('photosArticles', $photos);
    /**************************************  PHOTOS  ***********************************************/

} // $tpl->is_cached('mobile/portada.tpl') old version

$tpl->display('mobile/frontpage.tpl', $cache_id);