<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

require_once('../admin/session_bootstrap.php');

////////////////////////////////////////////////////////////////////////////////
// Check admin session
if(!isset($_SESSION['userid']) || empty($_SESSION['userid'])) {
    Application::forward('/'); // Send to home page
}
    if (!isset($_REQUEST['category'])) {
        $_REQUEST['category'] = 'home';
    }
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
            
                 $items[] = $article;
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


$ccm = ContentCategoryManager::get_instance();

$section = (!empty($subcategory_name))? $subcategory_name: $category_name;
$section = (is_null($section))? 'home': $section;

$tpl->loadConfigOrDefault('template.conf', $section); // $category_name is a string

if(!isset($_REQUEST['preview_time'])) {
    $_REQUEST['preview_time'] = date('Y-m-d H:i:s');
}
$preview_time = isset($_REQUEST['preview_time'])? $_REQUEST['preview_time'] : 20 ;

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
require_once ("index_sections.php");
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

/******************************  CAROUSEL DE OPINIONES  **************************************/
//require_once ("carousel.php");
/******************************  CAROUSEL DE OPINIONES  **************************************/

//Obtenemos los articulos
$cm = new ContentManager();


/****************************************  NOTICIAS  *******************************************/
$photos = array();
$pk2placeholder = json_decode($_REQUEST['articles'], true);

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

 

    $category_name = (isset($_GET['category_name']))? $_GET['category_name'] : "home" ;
    /************************************ TITULARES TENDENCIAS  ************************************/
    $now= date('Y-m-d H:m:s',time());

    $articles_home_express = $cm->find('Article', 'content_status=1 AND available=1 AND fk_content_type=1 AND (starttime="0000-00-00 00:00:00" OR (starttime != "0000-00-00 00:00:00"  AND starttime<"'.$now.'")) AND (endtime="0000-00-00 00:00:00" OR (endtime != "0000-00-00 00:00:00"  AND endtime>"'.$now.'")) ', 'ORDER BY created DESC LIMIT 0 , 5 ');

    $tpl->assign('articles_home_express', $articles_home_express);


    $titular_gente = $cm->find_by_category_name('Article','tendencias', 'content_status=1 AND frontpage=1 AND available=1 AND fk_content_type=1 AND  (starttime="0000-00-00 00:00:00" OR (starttime != "0000-00-00 00:00:00"  AND starttime<"'.$now.'")) AND (endtime="0000-00-00 00:00:00" OR (endtime != "0000-00-00 00:00:00"  AND endtime>"'.$now.'"))', 'ORDER BY position ASC LIMIT 0 , 6');

    foreach ($titular_gente as $gente) {
        $img = new Photo($gente->img2);
        $gente->path_img = $img->path_file."".$img->name;
    }

    $tpl->assign('titulares_gente', $titular_gente);

    /***********************************PHOTO-ALBUM  *********************************************/

    $album_photo = array();
    $lastAlbum = $cm->find('Album', ' contents.fk_content_type=7 and contents.available=1', 'ORDER BY favorite DESC, created DESC LIMIT 0 , 5');
    /* Don't need. Get crop /album/crops/album_id.jpg
   foreach($lastAlbum as $album){
        $album->photo = $album->get_firstfoto_album($album->id);
    } */
    $tpl->assign('lastAlbum', $lastAlbum);


    /***********************************  PHOTO-ALBUM  *********************************************/

    /***********************************  VIDEOS  *********************************************/
    require_once ("widget_videos.php");

/********************************* ADVERTISEMENTS  *********************************************/
require_once ("index_advertisement.php");
/********************************* ADVERTISEMENTS  *********************************************/

$tpl->display('frontpage/frontpage.tpl');
