<?php
//error_reporting(E_ALL);
require('./config.inc.php');
require_once('./core/application.class.php');

Application::import_libs('*');
$app = Application::load();

// browser_detect.php, redirect to /mobile/ is mobile device
// require './browser_detect.php'; // is deprecated, use $app->mobileRouter
$app->mobileRouter();

/* ** BEGIN: control banner inicial ******************************************
// Si existe index.html es que hay una publi que mostrar antes de entrar
// en la portada del periódico
if( file_exists(dirname(__FILE__).'/sargadelos.html') && !isset($_COOKIE['indexpubli']) ) {
    // caducidade da cookie,  1 hora
    setcookie("indexpubli", "1",  time()+(1*60*60) );

    // Redirixir a sargadelos.html
    Application::forward('/sargadelos.html');
}
 ** END: control banner inicial ******************************************** */

require_once('./core/content_manager.class.php');
require_once('./core/content.class.php');
require_once('./core/article.class.php');
require_once('./core/advertisement.class.php');
require_once('./core/related_content.class.php');
require_once('./core/attachment.class.php');
require_once('./core/attach_content.class.php');
require_once('./core/rating.class.php');
require_once('./core/opinion.class.php');
require_once('./core/comment.class.php');
require_once('./core/album.class.php');
require_once('./core/video.class.php');

require('./core/photo.class.php');
require('./core/author.class.php');
require('./core/content_category.class.php');
require('./core/content_category_manager.class.php');
require('./core/pc_content_manager.class.php');

/**************************************  TEMPLATE CACHE  *******************************************/

//$filters = array('output' => array('fix_object_tags'));
$tpl = new Template(TEMPLATE_USER);
$tpl->setConfig('frontpages');

//$scripts = array('prototype.js','scriptaculous/scriptaculous.js?load=effects', 'validation.js', 'swfobject.js', );
//$tpl->removeScript( 'galiciabanner.js', 'head' );

//Is category initialized redirect the user to /
$category_name    = $_GET['category_name'];
$subcategory_name = $_GET['subcategory_name'];

// Engadido $_GET['page'] para ter en conta o tema da paxinación no cacheo
$cache_id = $tpl->generateCacheId($category_name, $subcategory_name, intval($_GET['page']));


//if(($tpl->caching == 0) || !$tpl->is_cached('index.tpl', $cache_id)) { // (1)
// BEGIN MUTEXT
Application::getMutex($cache_id);    
if(($tpl->caching == 0) || !$tpl->is_cached('index.tpl', $cache_id)) { // (2)

    /**************************************    *******************************************/

    // Incluir portada en PDF
    // require_once('pdf_portada.php');

    $ccm = ContentCategoryManager::get_instance();

    list($category_name, $subcategory_name) = $ccm->normalize($category_name, $subcategory_name);
    $section = (!empty($subcategory_name))? $subcategory_name: $category_name;
    $section = (is_null($section))? 'home': $section;

    $tpl->loadConfigOrDefault('template.conf', $section); // $category_name is a string
    unset($section);
    
    /**************************************  SECURITY - CATEGORYS *******************************************/
    if (isset($category_name) && !empty($category_name)) {

        if (!$ccm->exists($category_name)) {
            Application::forward301('/');
        } else {
            //If there is no any article in a category forward into the first subcategory
            if ($ccm->isEmpty($category_name) && !isset($subcategory_name)) {
                $subcategory_name = $ccm->get_first_subcategory($ccm->get_id($category_name));
                if (empty($subcategory_name )){
                     Application::forward301('/');
                }else{
                        Application::forward301('/seccion/'.$category_name.'/'.$subcategory_name.'/');
                    }

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

    } else {
        $_GET['category_name'] = $category_name = 'home';
    }

    /**************************************  SECURITY  *******************************************/

    /******************************  CATEGORIES & SUBCATEGORIES  *********************************/
    require_once ("index_sections.php");
    /******************************  CATEGORIES & SUBCATEGORIES  *********************************/

    /******************************  CAROUSEL DE OPINIONES  **************************************/
  //  require_once ("carousel.php");
    /******************************  CAROUSEL DE OPINIONES  **************************************/

    //Obtenemos los articulos
    $cm = new ContentManager();
   
    /****************************************  NOTICIAS  *******************************************/

    if ($_GET['category_name'] == 'home') {
        //$destaca = $cm->find('Article', ' home_pos=1 AND in_home=1 AND frontpage=1  AND available=1 AND content_status=1 AND fk_content_type=1', 'ORDER BY home_pos ASC, created DESC');
        $articles_home = $cm->find('Article', 'contents.in_home=1 AND contents.frontpage=1 AND contents.available=1 AND contents.content_status=1 AND contents.fk_content_type=1', 'ORDER BY home_pos ASC, created DESC');

        // Filter by scheduled {{{
        $articles_home = $cm->getInTime($articles_home);
        // }}}

        
    } else {
        if(!isset($_GET['page'])){
            $_GET['page'] = 0;
        }

        if (!isset ($_GET['subcategory_name'])) {
            $actual_category = $_GET['category_name'];
        } else {
            $actual_category = $_GET['subcategory_name'];
        }

        $tpl->assign('actual_category',$actual_category);
        $actual_category_id=$ccm->get_id($actual_category);

        if($_GET['page'] >= 1) {
            $items_page = 20;
            $_limit = 'LIMIT '.($_GET['page']-1)*$items_page.', '.($items_page);
            $articles_home = $cm->find_by_category_name('Article',$actual_category, 'contents.available = 1 and (contents.content_status = 0 OR (contents.content_status = 1 and contents.frontpage=0)) and contents.fk_content_type=1 ', 'ORDER BY content_status DESC, changed DESC, archive DESC '.$_limit);

            // Filter by scheduled {{{
            $articles_home = $cm->getInTime($articles_home);
            // }}}

            $total_articles = $cm->cache->count('Article', 'contents.available = 1 AND (contents.content_status = 0 OR (contents.content_status = 1 AND contents.frontpage=0)) AND contents.fk_content_type=1 ', $actual_category_id);

        } else {
            //$destaca=$cm->find_by_category_name('Article', $actual_category, 'position=1 AND frontpage=1 AND content_status=1 AND available=1 AND fk_content_type=1', 'ORDER BY position ASC, created DESC');
            $articles_home = $cm->find_by_category_name('Article', $actual_category, 'contents.frontpage=1 AND contents.content_status=1 AND contents.available=1  AND contents.fk_content_type=1', 'ORDER BY placeholder ASC, position ASC, created DESC');

            // Filter by scheduled {{{
            $articles_home = $cm->getInTime($articles_home);
            // }}}

            $total_articles = $cm->cache->count('Article', 'contents.available = 1 and (contents.content_status = 0 OR (contents.content_status = 1 and contents.frontpage=0)) and contents.fk_content_type=1 ',$actual_category_id);
            $items_page = 20;
        }

        if($total_articles > 400) {
            $total_articles = 400;
        }

        $params = '/seccion/'.$actual_category.'';
        $paginacion = $cm->create_paginate($total_articles, $items_page, 4, 'URL', $params);

        // FIXME: correción feita para paxinacións de portada nas que amosar a ligazón da primeira páxina
        // https://redmine.openhost.es/issues/show/1060
        if(empty($_REQUEST['page'])) {
            $matches = array();
            preg_match('@<a href="(?P<uri>.*?)/[0-9]/" title=@i', $paginacion->links, $matches);
            $paginacion->links = preg_replace('@^<b>1</b>@', '<a href="' . $matches['uri'] . '/1/" title="Página 1">1</a>', $paginacion->links);
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
    
    for ( $c = 0,$aux = 0; $articles_home[$aux]->title != "" ; $c++, $aux ++ ) {

        $column[$c] = $articles_home[$aux];
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
    require_once ("module_other_headlines.php");

    /************************************ TITULARES GENTE  ************************************/
    $titular_gente = $cm->find_by_category_name('Article','gente', 'content_status=1 AND frontpage=1 AND available=1 AND fk_content_type=1 AND  (starttime="0000-00-00 00:00:00" OR (starttime != "0000-00-00 00:00:00"  AND starttime<"'.$now.'")) AND (endtime="0000-00-00 00:00:00" OR (endtime != "0000-00-00 00:00:00"  AND endtime>"'.$now.'"))', 'ORDER BY position ASC LIMIT 0 , 6');

    foreach ($titular_gente as $gente) {
        $img = new Photo($gente->img2);
        $gente->path_img = $img->path_file."".$img->name;
    }
  
    $tpl->assign('titulares_gente', $titular_gente);

    /***********************************PHOTO-ALBUM  *********************************************/

    $album_photo = array();
    $lastAlbum = $cm->find('Album', ' contents.fk_content_type=7 and contents.available=1', 'ORDER BY favorite DESC, created DESC LIMIT 0 , 4');
    /* Don't need. Get crop /album/crops/album_id.jpg
   foreach($lastAlbum as $album){
        $album->photo = $album->get_firstfoto_album($album->id);
    } */
    $tpl->assign('lastAlbum', $lastAlbum);
    
 
    /***********************************  PHOTO-ALBUM  *********************************************/


        // ContentManager::find(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>);
        $videos = $cm->find('Video', 'contents.content_status=1', 'ORDER BY created DESC LIMIT 0 , 4');

        foreach($videos as $video){
            $videos_authors[] = new Author($video->fk_user);
        }

        $tpl->assign('videos', $videos);
        $tpl->assign('videos_authors', $videos_authors);

        /***********************************  VIDEOS  *********************************************/
   
    /*******************************  SUPLEMENTOS Column3 ******************************************
    require_once ("index_suplementos.php");

    /************************************ OPINION **************************************************
    require_once ("index_opinion.php");
    /************************************ OPINION **************************************************/

     


} // $tpl->is_cached('index.tpl') (1)

// END MUTEXT
Application::releaseMutex();
//} // $tpl->is_cached('index.tpl') (2)

/********************************* ADVERTISEMENTS  *********************************************/
//require_once ("index_advertisement.php");
/********************************* ADVERTISEMENTS  *********************************************/
$tpl->display('frontpage.tpl', $cache_id);