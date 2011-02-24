<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

/**
 * Redirect Mobile browsers to mobile site unless a cookie exists.
*/
$app->mobileRouter();

/**
 * Fetch HTTP variables
*/

$category_name = filter_input(INPUT_GET,'category_name',FILTER_SANITIZE_STRING);
if ( !(isset($category_name) && !empty($category_name)) ) {
    $category_name = 'home';
}

$subcategory_name = filter_input(INPUT_GET,'subcategory_name',FILTER_SANITIZE_STRING);
$cache_page = filter_input(INPUT_GET,'page',FILTER_VALIDATE_INT);
$cache_page = (is_null($cache_page))? 0 : $cache_page;


/**
 * Setup view
*/
$tpl = new Template(TEMPLATE_USER);
$tpl->setConfig('frontpages');
$cache_id = $tpl->generateCacheId($category_name, $subcategory_name, $cache_page);

/**
 * Fetch information for Advertisements
*/
require_once("index_advertisement.php");

/**
 * Avoid to run the entire app logic if is available a cache for this page
*/
if(($tpl->caching == 0)
   || !$tpl->isCached('frontpage/frontpage.tpl', $cache_id))
{

    /**
     * Init the Content and Database object
    */
    $ccm = ContentCategoryManager::get_instance();

    list($category_name, $subcategory_name) = $ccm->normalize($category_name, $subcategory_name);
    $section = (!empty($subcategory_name))? $subcategory_name: $category_name;
    $section = (is_null($section))? 'home': $section;

    $tpl->loadConfigOrDefault('template.conf', $section); // $category_name is a string
    unset($section);

    /**
     * If no home   category name
    */
    if (($category_name != 'home') ) {
        /**
         * Redirect to home page if the desired category doesn't exist or  is empty this is a home page
        */
        if ( empty($category_name) || !$ccm->exists($category_name)) {
            Application::forward301('/home/');

        } else {
            /**
             * If there is no any article in a category forward into the first subcategory
            */
            if ($ccm->isEmpty($category_name)
                && !isset($subcategory_name))
            {
                $subcategory_name =
                    $ccm->get_first_subcategory($ccm->get_id($category_name));
                if (empty($subcategory_name )) {
                    Application::forward301('/home/');
                } else {
                    Application::forward301('/seccion/'.$category_name.'/'.$subcategory_name.'/');
                }

            } else {
                $category = $ccm->get_id($category_name);
            }

        }

        if (isset($subcategory_name) && !empty($subcategory_name)) {
            if (!$ccm->exists($subcategory_name)) {
                Application::forward301('/home/');
            } else {
                $subcategory = $ccm->get_id($subcategory_name);
            }
        }

    }

    $actual_category =
               (!isset($subcategory_name))? $category_name
                                                : $subcategory_name;

    $tpl->assign('actual_category', $actual_category);
    $actual_category_id = $ccm->get_id($actual_category);

    require_once ("index_sections.php");

    //Obtenemos los articulos
    $cm = new ContentManager();



    /************************ FETCHING NEWS ***********************************/

    if ($actual_category == 'home') {

        /**
         * Get the articles in home page
        */
        $articles_home =
            $cm->find_all(  'Article',
                            'contents.in_home=1 AND contents.frontpage=1'
                            .' AND contents.available=1 AND contents.content_status=1'
                            .' AND contents.fk_content_type=1',
                            'ORDER BY home_pos ASC, created DESC');

        /**
         * Filter articles if some of them has time scheduling
        */
        $articles_home = $cm->getInTime($articles_home);

        $actual_category = 'home';
        $actual_category_id = 0;

        /// Adding Widgets {{{
        $contentsInHomepage = $cm->getContentsForHomepageOfCategory($actual_category_id);

        foreach($contentsInHomepage as $content) {
            $articles_home[] = $content;
        }

        $articles_home = $cm->sortArrayofObjectsByProperty($articles_home, 'position');
        // }}}

    }
    else
    {

        if($cache_page >= 1) {

            $_limit = 'LIMIT '.($cache_page - 1) * $items_page.', '.(ITEMS_PAGE);

            $articles_home =
                $cm->find_by_category_name( 'Article',
                                            $actual_category,
                                            'contents.available = 1 AND (contents.content_status = 0 OR (contents.content_status = 1 and contents.frontpage=0)) and contents.fk_content_type=1 '
                                            , 'ORDER BY content_status DESC, changed DESC, archive DESC '.$_limit);

            // Filter by scheduled {{{
            $articles_home = $cm->getInTime($articles_home);
            // }}}

            /// Adding Widgets {{{
            $contentsInHomepage = $cm->getContentsForHomepageOfCategory($actual_category_id);

            foreach($contentsInHomepage as $content) {
                $articles_home[] = $content;
            }

            $articles_home = $cm->sortArrayofObjectsByProperty($articles_home, 'position');
            // }}}

            $total_articles = $cm->cache->count('Article', 'contents.available = 1 AND (contents.content_status = 0 OR (contents.content_status = 1 AND contents.frontpage=0)) AND contents.fk_content_type=1 ', $actual_category_id);

        } else {

            $articles_home = $cm->find_by_category_name('Article', $actual_category, 'contents.frontpage=1 AND contents.content_status=1 AND contents.available=1  AND contents.fk_content_type=1', 'ORDER BY placeholder ASC, position ASC, created DESC');

            $articles_home = $cm->getInTime($articles_home);

            $total_articles = $cm->cache->count('Article', 'contents.available = 1 and (contents.content_status = 0 OR (contents.content_status = 1 and contents.frontpage=0)) and contents.fk_content_type=1 ',$actual_category_id);
            $items_page = 20;

            /// Adding Widgets {{{
            $contentsInHomepage = $cm->getContentsForHomepageOfCategory($actual_category_id);

            foreach($contentsInHomepage as $content) {
                $articles_home[] = $content;
            }

            $articles_home = $cm->sortArrayofObjectsByProperty($articles_home, 'position');
            // }}}
        }

        if($total_articles > 400) {
            $total_articles = 400;
        }

        $params = '/seccion/'.$actual_category.'';
        $paginacion = $cm->create_paginate($total_articles, $items_page, 4, 'URL', $params);

        // FIXME: correción feita para paxinacións de portada nas que amosar a ligazón da primeira páxina
        // https://redmine.openhost.es/issues/show/1060
        if(empty($cache_page)) {
            $matches = array();
            if ( preg_match('@<a href="(?P<uri>.*?)/[0-9]/" title=@i', $paginacion->links, $matches) ) {
                $paginacion->links = preg_replace('@^<b>1</b>@', '<a href="' . $matches['uri'] . '/1/" title="Página 1">1</a>', $paginacion->links);
            }
        }

        $tpl->assign('paginacion', $paginacion->links);
    }


    $tpl->assign('articles_home', $articles_home);

    /************************************ COLUMN1 **************************************************/

    /***** GET ALL FRONTPAGE'S IMAGES *******/
    $imagenes = array();
    foreach($articles_home as $i => $art) {
        if(isset($art->img1)) {
            $imagenes[] = $art->img1;
        }
    }

    if(count($imagenes)>0) {
        $imagenes = $cm->find('Photo', 'pk_content IN ('. implode(',', $imagenes) .')');
    }

    $column = array(); //Contendrá las noticias de la columna
    $relia  = new Related_content();
     // $rating_bar_col1 = array();//Array que contiene las barras de votación de las noticias de la columna1

    //for ( $c = 0,$aux = 0; $articles_home[$aux]->title != "" ; $c++, $aux ++ ) {
    $c = 0;
    $aux = 0;
    while(isset($articles_home[$aux]) && $articles_home[$aux]->title != "") {
        $column[$c] = $articles_home[$aux];
        $column[$c]->category_name = $column[$c]->loadCategoryName($articles_home[$aux]->id);
        $column[$c]->category_title = $column[$c]->loadCategoryTitle($articles_home[$aux]->id);
        /*****  GET IMAGE DATA *****/
        if(isset($column[$c]->img1)) {
                // Buscar la imagen
                if(!empty($imagenes)) {
                    foreach($imagenes as $img) {
                        if($img->pk_content == $column[$c]->img1) {
                         //   $photos[$art->id] = $img->path_file.$img->name;

                            $column[$c]->img1_path = $img->path_file.$img->name;
                            break;
                        }
                    }
                }
            }
        /***** GET OBJECT VIDEO *****/
        if (empty($column[$c]->img1) and isset($column[$c]->fk_video) and (!empty($column[$c]->fk_video))) {
            $video=$column[$c]->fk_video;
            if(isset($video)){
               $video1=new Video($video);
               $column[$c]->obj_video= $video1;
            }
        }

        /***** COLUMN1 RELATED NEWS  ****/
        $relationes = $relia->get_relations($articles_home[$aux]->id);

        ////se le pasa el id de cada noticia de la column1
        // devueve array con los id de las noticias relacionadas

        $relats = array();
        foreach($relationes as $i => $id_rel) { //Se recorre el array para sacar todos los campos.

            $obj = new Content($id_rel);
            // Filter by scheduled {{{
            if($obj->isInTime() && $obj->available==1 && $obj->in_litter==0) {
                $obj->category_name = $ccm->get_name($obj->category);
                $relats[] =$obj;
            }
            // }}}
        }
        $column[$c]->related_contents = $relats;

        /***** COLUMN1 COMMENTS *******
        if($articles_home[$aux]->with_comment) {
            $comment = new Comment();

            $numcomment1[$articles_home[$aux]->id] = $comment->count_public_comments($articles_home[$aux]->id);
            $tpl->assign('numcomment1', $numcomment1);
        }


        /******* COLUMN1 RATINGS ********
        $rating = new Rating($articles_home[$aux]->id);
        $rating_bar_col1[$articles_home[$aux]->id] = $rating->render('home','vote');
        /******* END COLUMN1 RATINGS **********/

        $c++; $aux ++;
    }

    //  $tpl->assign('rating_bar_col1', $rating_bar_col1);
    //  $tpl->assign('relationed_c1', $relat_c1);
    $tpl->assign('column', $column);




    /************************************ END COLUMN1 **************************************************/

    /************************************ ARTICLES EXPRESS **************************************/
    $now= date('Y-m-d H:m:s',time()); //2009-02-28 21:00:13
    $articles_home_express = $cm->find('Article', 'content_status=1 AND available=1 AND fk_content_type=1 AND (starttime="0000-00-00 00:00:00" OR (starttime != "0000-00-00 00:00:00"  AND starttime<"'.$now.'")) AND (endtime="0000-00-00 00:00:00" OR (endtime != "0000-00-00 00:00:00"  AND endtime>"'.$now.'")) ', 'ORDER BY created DESC LIMIT 0 , 5 ');

    $tpl->assign('articles_home_express', $articles_home_express);

    /************************************ TITULARES DEL DIA  ************************************/
    if ($category_name == 'home') {
        require_once ("module_other_headlines.php");
    }
    /************************************ TITULARES TENDENCIAS  ************************************/
    $titular_gente =
        $cm->find_by_category_name('Article',
                                    'tendencias'
                                    , 'content_status=1 AND frontpage=1'
                                    . ' AND available=1 AND fk_content_type=1'
                                    . ' AND (starttime="0000-00-00 00:00:00" '
                                    . '      OR (starttime != "0000-00-00 00:00:00" '
                                    . '      AND starttime<"'.$now.'"))'
                                    . ' AND (endtime="0000-00-00 00:00:00" OR (endtime != "0000-00-00 00:00:00"  AND endtime>"'.$now.'"))'
                                    , 'ORDER BY position ASC LIMIT 0 , 3');

    foreach ($titular_gente as $gente) {
        $img = new Photo($gente->img2);
        $gente->path_img = $img->path_file."".$img->name;
        $gente->category_name = $ccm->get_name($gente->category);
    }

    $tpl->assign('titulares_gente', $titular_gente);

    /**
     * Fetch information for Albums
    */
    $lastAlbum = $cm->find('Album',
                            ' contents.fk_content_type=7 AND'
                           .' contents.available=1'
                           , 'ORDER BY favorite DESC, created DESC'
                           .' LIMIT 0 , 5');
    $tpl->assign('lastAlbum', $lastAlbum);


    /**
     * Fetch information for opinion widget
     * require_once ("index_opinion.php");
    */

    /**
     * Fetch information for headlines
    */
    require_once('widget_headlines.php');
    require_once('widget_headlines_past.php');

    /**
     * Fetch information for Static Pages
    */
    require_once("widget_static_pages.php");

} // $tpl->is_cached('index.tpl')

$tpl->display('frontpage/frontpage.tpl', $cache_id);
