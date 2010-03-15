<?php
//error_reporting(E_ALL);
require('./config.inc.php');
require_once('./core/application.class.php');

require_once('./admin/session_bootstrap.php');

Application::import_libs('*');
$app = Application::load();

////////////////////////////////////////////////////////////////////////////////
// Check admin session
if(!isset($_SESSION['userid']) || empty($_SESSION['userid'])) {
    Application::forward('/'); // Send to home page
}

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

require('core/string_utils.class.php');

// Merda de magic_quotes
String_Utils::disabled_magic_quotes();

/* Helpers functions ********************************************************* */
function sortArticles($articles, $layout, $section, $preview_time) {
    $items = array();
    $destacadas = array();
    
    foreach($layout as $pk => $placeholder) {
        foreach($articles as $article) {
            if($article->pk_content == $pk) {
                if($section == 'home') {
                    $article->home_placeholder = $placeholder;
                } else {
                    $article->placeholder = $placeholder;
                }
                
                // Só a primeira vai a destacada
                if(($placeholder == 'placeholder_0_0') && (count($destacadas) == 0) &&
                   ($article->isInTime(null, null, $preview_time))) {                    
                    $destacadas[] = $article;
                } else {
                    $items[] = $article;
                }                
            }
        }
    }
    
    return array($destacadas, $items);
}
/* END: Helpers functions ********************************************************* */


$tpl = new Template(TEMPLATE_USER);

$ccm = ContentCategoryManager::get_instance();
$category_name = $ccm->get_name($_REQUEST['category']);
$category_name = (empty($category_name))? 'home': $category_name;

list($category_name, $subcategory_name) = $ccm->normalize($category_name);

// Incluir portada en PDF
require_once('pdf_portada.php');

$ccm = ContentCategoryManager::get_instance();

$section = (!empty($subcategory_name))? $subcategory_name: $category_name;
$section = (is_null($section))? 'home': $section;

$tpl->loadConfigOrDefault('template.conf', $section); // $category_name is a string

if(!isset($_REQUEST['preview_time'])) {
    $_REQUEST['preview_time'] = date('Y-m-d H:i:s');
}
$preview_time = $_REQUEST['preview_time'];

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
require_once ("index_sections.php");
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

/******************************  CAROUSEL DE OPINIONES  **************************************/
require_once ("carousel.php");
/******************************  CAROUSEL DE OPINIONES  **************************************/

//Obtenemos los articulos
$cm = new ContentManager();


/****************************************  NOTICIAS  *******************************************/
$photos = array();
$pk2placeholder = json_decode($_REQUEST['id'], true);

$articles_home = $cm->find('Article', 'pk_content IN ("'.implode('","', array_keys($pk2placeholder)).'")');
list($destaca, $articles_home) = sortArticles($articles_home, $pk2placeholder, $section, $preview_time);

// Filter by scheduled {{{
$articles_home = $cm->getInTime($articles_home, $preview_time);
//$dest/aca = $cm->getInTime($destaca, $preview_time);
// }}}

//$tpl->assign('destaca', $destaca);
//$tpl->assign('articles_home', $articles_home);

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
                    $photos[$art->id] = $img->path_file.$img->name;
                    break;
                }
            }
        }
    }
}

$tpl->assign('photos', $photos);

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

 
//////////////////////////////////////////////////////////////////////////////////////////////////
$articles_home_express = $cm->find('Article', 'content_status=1 AND available=1 AND fk_content_type=1', 'ORDER BY created DESC LIMIT 0 , 5 ');
// Filter by scheduled {{{
$articles_home_express = $cm->getInTime($articles_home_express);
// }}}

$params="'articles_home_express',''";
//  $articles_home_express = $cm->paginate_num_js($articles_home_express, 5, 2, "get_paginate_articles", $params);
// $pages_home_express=$cm->pager;
$pages_home_express =$cm->create_paginate(40, 5, 2, 'get_paginate_articles', $params);
$tpl->assign('pages_home_express', $pages_home_express);
$tpl->assign('articles_home_express', $articles_home_express);

if ($_GET['category_name'] == 'home') {
    $deportes_id=$ccm->get_id('deportes');
    //$deportes_express = $cm->find_by_category_name('Article','deportes', 'available=1 AND fk_content_type=1', 'ORDER BY changed DESC LIMIT 0 , 42');
    $deportes_express = $cm->find_category_headline($deportes_id, 'available=1', 'ORDER BY changed DESC LIMIT 0 , 6');
    // Filter by scheduled {{{
    $deportes_express = $cm->getInTime($deportes_express);
    $tpl->assign('deportes_express', $deportes_express);


    $params="'deportes_express','deportes'";
    $pager_deportes =$cm->create_paginate(42, 6, 1, 'get_paginate_articles', $params);

    $tpl->assign('pages_deportes_express', $pager_deportes);
    
/////// TITULARES DEL DIA //////////////////////////////////////////////////////////
    // Filter by scheduled into method {{{
     $titulares = $cm->find_headlines();
    // }}}
    
    foreach ($titulares as $titul) {
        $tits[$titul['catName']][] = $titul;
    }

    foreach($tits as $cat => $t) {
       $tpl->assign('titulares_'.$cat, array_slice($t, 0, 5));
    }

  
   // $tpl->assign('titulares', $titulares);
}


// Mostrar solo gente ?????-- Se hace pq se necesita la imagen.y la consulta find_headlines no tiene consulta sobre la tabla article.
$titular_gente = $cm->find_by_category_name('Article','gente', 'content_status=1 AND frontpage=1 AND available=1 AND fk_content_type=1', 'ORDER BY position ASC LIMIT 0 , 8');

// Filter by scheduled {{{
$titular_gente = $cm->getInTime($titular_gente);
// }}}

$titular_gente_img = new Photo($titular_gente[0]->img2);
$tpl->assign('titular_gente', $titular_gente[0]);
$tpl->assign('titular_gente_img', $titular_gente_img);
$tpl->assign('titulares_gente', $titular_gente);

/***********************************  HEADLINES  ***********************************************/

/*******************************  SUPLEMENTOS Column3 ******************************************/

require_once ("index_suplementos.php");

/************************************ OPINION **************************************************/
require_once ("index_opinion.php");
/************************************ OPINION **************************************************/

/**********************************  CONECTA COLUMN3  *****************************************
require_once("index_conecta.php");
/**********************************  CONECTA COLUMN3  ******************************************/
$tpl->assign('MEDIA_IMG_PATH_WEB', MEDIA_IMG_PATH_WEB);
/******************************************************************************************************/

/***********************************PHOTO-ALBUM  *********************************************/
$lastAlbum = array();       
$lastAlbum = $cm->find('Album', ' albums.favorite=1 and contents.fk_content_type=7 and contents.available=1', 'ORDER BY created DESC LIMIT 0 , 1');

if(is_null($lastAlbum[0])){ //Si no hay favorito coge el ultimo
    $album_id=$ccm->get_id('album');
    $lastAlbum= $cm->find_by_category('Album', $album_id, 'contents.fk_content_type=7 and contents.available=1', 'ORDER BY created DESC LIMIT 0 , 1');
}
$tpl->assign('lastAlbumContent', $lastAlbum[0]);  
/***********************************  PHOTO-ALBUM  *********************************************/

/****************  SOLO HOME: VIDEOS   ************************************/
    if ($section == 'home')
    {
            // ContentManager::find(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>);
            $videos = $cm->find('Video', 'contents.content_status=1', 'ORDER BY created DESC LIMIT 0 , 6');

            foreach($videos as $video){
                $videos_authors[] = new Author($video->fk_user);
            }

            $tpl->assign('videos', $videos);
            $tpl->assign('videos_authors', $videos_authors);

            /***********************************  VIDEOS  *********************************************/

            /**************************** NOTICIAS + VISTAS *****************************************/
           /*
            *  No se visualizan
            *
            *
            *  $articles_viewed = $cm->getMostViewedContent('Article');
	    */
    }


/********************************* ADVERTISEMENTS  *********************************************/
require_once ("index_advertisement.php");
/********************************* ADVERTISEMENTS  *********************************************/

$tpl->display('frontpage.tpl');
