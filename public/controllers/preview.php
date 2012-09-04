<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
/**
 * Start up and setup the app
*/
require_once '../bootstrap.php';

require_once '../admin/session_bootstrap.php';
/**
 *  Check admin session
 */
if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) {
    // Send to home page
    Application::forward('/');
}

$tpl = new Template(TEMPLATE_USER);
$ccm = ContentCategoryManager::get_instance();
/**
 * Fetch HTTP variables
*/
$category_name = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_STRING);
if ( !(isset($category_name) && !empty($category_name)) ) {
    $category_name = 'home';
}

if (!isset($_REQUEST['preview_time'])) {
    $_REQUEST['preview_time'] = date('Y-m-d H:i:s');
}
$preview_time = isset($_REQUEST['preview_time'])? $_REQUEST['preview_time'] : 20 ;

/**
 * Fecth categories and subcategories
 */
require_once 'index_sections.php';

//Obtenemos los articulos
$cm = new ContentManager();

/****************************************  NOTICIAS  *******************************************/
if ($category_name == 'home') {
    /**
     * Get the articles in home page
    */
    $articles_home = $cm->find_all(
        'Article',
        'contents.in_home=1 AND contents.frontpage=1'
        .' AND contents.available=1 AND contents.content_status=1'
        .' AND contents.fk_content_type=1'
        .' AND contents.home_placeholder != \'\'',
        'ORDER BY home_pos ASC, created DESC'
    );

    /**
     * Filter articles if some of them has time scheduling
    */
    $articles_home = $cm->getInTime($articles_home);

    foreach ($articles_home as $article) {
        $article->position = $article->home_pos;
    }

    $category_name = 'home';
    $actual_category_id = 0;

    /// Adding Widgets {{{
    $contentsInHomepage = $cm->getContentsForHomepageOfCategory($actual_category_id);

    foreach ($contentsInHomepage as $content) {
        if (isset($content->home_placeholder)
           && !empty($content->home_placeholder)
           && ($content->home_placeholder != '')
        ) {
            $articles_home[] = $content;
        }
    }
} else {
    $articles_home =
        $cm->find_by_category(
            'Article',
            $category_name,
            'contents.frontpage=1 AND contents.content_status=1 '.
            'AND contents.available=1  AND contents.fk_content_type=1 ',
            'ORDER BY placeholder ASC, position ASC, created DESC'
        );

    $articles_home = $cm->getInTime($articles_home);

    /// Adding Widgets {{{
    $contentsInHomepage = $cm->getContentsForHomepageOfCategory($category_name);

    foreach ($contentsInHomepage as $content) {
        if (isset($content->placeholder)
           && !empty($content->placeholder)
           && ($content->placeholder != '')
        ) {
            $articles_home[] = $content;
        }
    }
}

$articles_home = $cm->sortArrayofObjectsByProperty($articles_home, 'position');
// }}}
$tpl->assign('articles_home', $articles_home);

/**************************************  PHOTOS  ***********************************************/
$imagenes = array();
foreach ($articles_home as $i => $art) {
    if (isset($art->img1)) {
        $imagenes[] = $art->img1;
    }
}

$photos = array();
if (count($imagenes)>0) {
    $imagenes = $cm->find('Photo', 'pk_content IN ('. implode(',', $imagenes) .')');

    foreach ($articles_home as $i => $art) {
        if (isset($art->img1)) {
            // Buscar la imagen
            foreach ($imagenes as $img) {
                if ($img->pk_content == $art->img1) {
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
foreach ($articles_home as $i => $art) {
    if (isset($art->img1)) {
        $imagenes[] = $art->img1;
    }
}

if (count($imagenes)>0) {
    $imagenes = $cm->find('Photo', 'pk_content IN ('. implode(',', $imagenes) .')');
}

$column = array(); //Contendrá las noticias de la columna
$relia  = new RelatedContent();
 // $rating_bar_col1 = array();//Array que contiene las barras de votación de las noticias de la columna1
$c = 0;
$aux = 0;
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
                    //   $photos[$art->id] = $img->path_file.$img->name;
                    $column[$c]->img1_path = $img->path_file.$img->name;
                    break;
                }
            }
        }
    }
    /***** GET OBJECT VIDEO *****/
    if (empty($column[$c]->img1)
        && isset($column[$c]->fk_video)
        && (!empty($column[$c]->fk_video))
    ) {
        $video=$column[$c]->fk_video;
        if (isset($video)) {
            $video1=new Video($video);
            $column[$c]->obj_video= $video1;
        }
    }

    /***** COLUMN1 RELATED NEWS  ****/
    $relationes = $relia->getRelations($articles_home[$aux]->id);
    ////se le pasa el id de cada noticia de la column1
    // devueve array con los id de las noticias relacionadas

    $relats = array();
    //Se recorre el array para sacar todos los campos.
    foreach ($relationes as $i => $id_rel) {

        $obj = new Content($id_rel);
        // Filter by scheduled {{{
        if ($obj->isInTime() && $obj->available==1 && $obj->in_litter==0) {
            $relats[] =$obj;
        }
        // }}}
    }
    $column[$c]->related_contents = $relats;

    /***** COLUMN1 COMMENTS *******/
    // if ($articles_home[$aux]->with_comment) {
    //     $comment = new Comment();

    //     $numcomment1[$articles_home[$aux]->id] = $comment->count_public_comments($articles_home[$aux]->id);
    //     $tpl->assign('numcomment1', $numcomment1);
    // }


    /******* COLUMN1 RATINGS ********/
    // $rating = new Rating($articles_home[$aux]->id);
    // $rating_bar_col1[$articles_home[$aux]->id] = $rating->render('home','vote');
    /******* END COLUMN1 RATINGS **********/

    $c++;
    $aux ++;
}

//  $tpl->assign('rating_bar_col1', $rating_bar_col1);
//  $tpl->assign('relationed_c1', $relat_c1);
$tpl->assign('column', $column);


/************************************ END COLUMN1 **************************************************/


/********************************* ADVERTISEMENTS  *********************************************/
require_once 'index_advertisement.php';
/********************************* ADVERTISEMENTS  *********************************************/

$tpl->display('frontpage/frontpage.tpl');

