<?php

// Start up and setup the app
require_once('../bootstrap.php');

// Redirect Mobile browsers to mobile site unless a cookie exists.
$app->mobileRouter();

// Fetch HTTP variables
$category_name    = filter_input(
    INPUT_GET, 'category_name', FILTER_SANITIZE_STRING,
    array('options' => array('default' => 'home'))
);
$subcategory_name = filter_input(INPUT_GET,'subcategory_name',FILTER_SANITIZE_STRING);

// Setup view
$tpl     = new Template(TEMPLATE_USER);
$tpl->setConfig('frontpages');
$cacheID = $tpl->generateCacheId($category_name, $subcategory_name, 0 /*$cache_page*/);

// Fetch information for Advertisements
require_once "index_advertisement.php";

// Avoid to run the entire app logic if is available a cache for this page
if (
    $tpl->caching == 0
    || !$tpl->isCached('frontpage/frontpage.tpl', $cacheID)
) {

    // Initialize the Content and Database object
    $ccm = ContentCategoryManager::get_instance();
    list($category_name, $subcategory_name) = $ccm->normalize($category_name, $subcategory_name);
    $section = (!empty($subcategory_name))? $subcategory_name: $category_name;
    $section = (is_null($section))? 'home': $section;

    $tpl->loadConfigOrDefault('template.conf', $section); // $category_name is a string
    unset($section);

    // If no home category name
    if ($category_name != 'home') {

        // Redirect to home page if the desired category doesn't
        // exist or  is empty this is a home page
        if (empty($category_name) || !$ccm->exists($category_name)) {
            Application::forward301('/');
        } else {
            // If there is no any article in a category forward into the first subcategory
            if ($ccm->isEmpty($category_name) && !isset($subcategory_name)) {
                $subcategory_name = $ccm->get_first_subcategory($ccm->get_id($category_name));

                $forwardUrl = '/';
                if (!empty($subcategory_name)) {
                    $forwardUrl = '/seccion/'.$category_name.'/'.$subcategory_name.'/';
                }
                Application::forward301($forwardUrl);

            } else {
                $category = $ccm->get_id($category_name);
            }

        }

        if (isset($subcategory_name) && !empty($subcategory_name)) {
            if (!$ccm->exists($subcategory_name)) {
                Application::forward301('/');
            } else {
                $subcategory = $ccm->get_id($subcategory_name);
            }
        }

    }

    $actual_category = (!isset($subcategory_name))? $category_name : $subcategory_name;

    $tpl->assign('actual_category', $actual_category);
    $actualCategoryId = $ccm->get_id($actual_category);

    require_once "index_sections.php";

    $cm = new ContentManager();

    $contentsInHomepage = $cm->getContentsForHomepageOfCategory($actualCategoryId);

    $articles_home = array();
    foreach ($contentsInHomepage as $content) {
        if(isset($content->home_placeholder)
           && !empty($content->home_placeholder)
           && ($content->home_placeholder != '')
           )
        {
            $articles_home[] = $content;
        }
    }
    // Filter articles if some of them has time scheduling and sort them by position
    $articles_home = $cm->getInTime($articles_home);
    $articles_home = $cm->sortArrayofObjectsByProperty($articles_home, 'position');

    /***** GET ALL FRONTPAGE'S IMAGES *******/
    $imagenes = array();
    foreach ($articles_home as $i => $art) {
        if (isset($art->img1)) {
            $imagenes[] = $art->img1;
        }
    }

    if (count($imagenes)>0) {
        $imagenes = $cm->find('Photo', 'pk_content IN ('. implode(',', $imagenes) .')');
    }

    $column = array();
    $relia  = new Related_content();

    $c = 0;
    $aux = 0;
    //Flag para saber si hay una noticia destacada
    while (isset($articles_home[$aux]) && $articles_home[$aux]->title != "") {
        $column[$c] = $articles_home[$aux];
        $column[$c]->category_name = $column[$c]->loadCategoryName($articles_home[$aux]->id);
        $column[$c]->category_title = $column[$c]->loadCategoryTitle($articles_home[$aux]->id);
        /*****  GET IMAGE DATA *****/
        if (isset($column[$c]->img1)) {
            // Buscar la imagen
            if (!empty($imagenes)) {
                foreach ($imagenes as $img) {
                    if ($img->pk_content == $column[$c]->img1) {
                        $column[$c]->img1_path = $img->path_file.$img->name;
                        $column[$c]->img1 = $img;
                        break;
                    }
                }
            }
        }

        /***** GET OBJECT VIDEO *****/
        if (empty($column[$c]->img1) and isset($column[$c]->fk_video) and (!empty($column[$c]->fk_video))) {
            $video=$column[$c]->fk_video;
            if (isset($video)) {
               $video1= new Video($video);
               $column[$c]->obj_video= $video1;
            }
        }

        /***** COLUMN1 RELATED NEWS  ****/
        $relations = $relia->get_relations($articles_home[$aux]->id);

        $relats = array();
        foreach ($relations as $i => $id_rel) { //Se recorre el array para sacar todos los campos.

            $obj = new Content($id_rel);
            // Filter by scheduled {{{
            if ($obj->isInTime() && $obj->available==1 && $obj->in_litter==0) {
                $obj->category_name = $ccm->get_name($obj->category);
                $relats[] = $obj;
            }
            // }}}
        }
        $column[$c]->related_contents = $relats;



        $c++; $aux ++;
    }

    $tpl->assign('column', $column);

    // Fetch information for Static Pages
     //TODO: Move to a widget. Used in all templates
    require_once "widget_static_pages.php";

} // $tpl->is_cached('index.tpl')

$tpl->display('frontpage/frontpage.tpl', $cacheID);
