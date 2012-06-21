<?php

/**
 * Setup app
*/
require_once '../bootstrap.php';

/**
 * Setup view
*/
$tpl = new Template(TEMPLATE_USER);
$tpl->setConfig('frontpage-mobile');

$url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL);
//Is category initialized redirect the user to /
preg_match('%^seccion/(?P<category_name>[a-z0-9\-\._]+)/$%i', $url, $sections);

$category_name = isset($section[1])? $section[1] : null;

$subcategory_name = filter_input(INPUT_GET, 'subcategory_name', FILTER_SANITIZE_STRING);
$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);

$ccm = ContentCategoryManager::get_instance();
list($category_name, $subcategory_name) = $ccm->normalize($category_name, $subcategory_name);

if (($category_name!='home') && ($category_name!='')) {
    if ($ccm->isEmpty($category_name) && is_null($subcategory_name)) {
        $subcategory_name = $ccm->getFirstSubcategory($ccm->get_id($category_name));
        if (is_null($subcategory_name)) {
            Application::forward301('/mobile/');
        } else {
            Application::forward301('/mobile/seccion/'.$category_name.'/'.$subcategory_name.'/');
        }
    }
}

$tpl->assign('ccm', $ccm);

$cacheID = $tpl->generateCacheId($category_name, $subcategory_name, 0);

if (($tpl->caching == 0) || !$tpl->isCached('mobile/frontpage-mobile.tpl', $cacheID)) {

    $section = (!empty($subcategory_name))? $subcategory_name: $category_name;
    $section = (is_null($section))? 'home': $section;
    $tpl->assign('section', $section);

    //$tpl->loadConfigOrDefault('template.conf', $section); // $category_name is a string

    //Obtenemos los articulos
    $cm = new ContentManager();

    /****************************************  NOTICIAS  *******************************************/
    include 'sections.php';

    $photos = array();
    if ($section == 'home') {

        $actualCategoryId = 0;

        $contentsInHomepage = $cm->getContentsForHomepageOfCategory($actualCategoryId);

        // Filter articles if some of them has time scheduling and sort them by position
        $contentsInHomepage = $cm->getInTime($contentsInHomepage);
        $contentsInHomepage = $cm->sortArrayofObjectsByProperty($contentsInHomepage, 'position');


    } else {
        $tpl->assign('section', $category_name);
        $actualCategoryId =  $ccm->get_id($section);

        $contentsInHomepage = $cm->getContentsForHomepageOfCategory($actualCategoryId);

        // Filter articles if some of them has time scheduling and sort them by position
        $contentsInHomepage = $cm->getInTime($contentsInHomepage);
        $contentsInHomepage = $cm->sortArrayofObjectsByProperty($contentsInHomepage, 'position');
    }

    /// Deleting Widgets {{{
    $articles_home = array();
    foreach ($contentsInHomepage as $content) {

        if (isset($content->home_placeholder)
            && !empty($content->home_placeholder)
            && ($content->home_placeholder != '')
            && ($content->content_type != 'Widget')
        ) {
            if ($content->content_type == 4) {
                //Obtener los slug's de los autores
                $content->author_name_slug=StringUtils::get_title($content->author);
            }

            $articles_home[] = $content;
        }
    }
    $tpl->assign('articles_home', $articles_home);

    /**************************************  PHOTOS  ***********************************************/
    $imagenes = array();
    foreach ($articles_home as $i => $art) {
        if (isset($art->img1)) {
            $imagenes[] = $art->img1;
        }
    }

    if (count($imagenes)>0) {
        $imagenes = $cm->find('Photo', 'pk_content IN ('. implode(',', $imagenes) .')');

        $photos = array();
        foreach ($articles_home as $i => $art) {
            if (isset($art->img1)) {
                // Buscar la imagen
                foreach ($imagenes as $img) {
                    if ($img->pk_content == $art->img1) {
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

$tpl->display('mobile/frontpage-mobile.tpl', $cacheID);