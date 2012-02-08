<?php

require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

// Register events
require_once('articles_events.php');

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
use Onm\Settings as s,
    Onm\Message as m;

//require_once(SITE_LIBS_PATH.'Pager/Pager.php');
require_once('controllers/utils_content.php');

// Assign a content types for don't reinvent the wheel into template
$tpl->assign('content_types', array(1 => 'Noticia' , 7 => 'Galeria', 9 => 'Video', 4 => 'Opinion', 3 => 'Fichero'));

/**
 * Fetch request variables
*/
(!isset($_SESSION['desde'])) ? $_SESSION['desde'] = 'list' : null ;
(!isset($_REQUEST['page'])) ? $_REQUEST['page'] = 1 : null ;
(!isset($_REQUEST['action'])) ?  $_REQUEST['action'] = 'list' : null ;


if ($_REQUEST['action']=='list_pendientes') {
    if (!isset($_REQUEST['category'])) {
        $_REQUEST['category'] = 'todos';
    }
}

(!isset($_REQUEST['category']) || empty($_REQUEST['category'])) ?  $_REQUEST['category'] = 'home' :  null;
(!isset($_SESSION['_from'])) ? $_SESSION['_from'] = $_REQUEST['category'] : null ;

$tpl->assign('category', $_REQUEST['category']);

/**
 * Getting categories
*/
// Parse template.conf to assign
$ccm = ContentCategoryManager::get_instance();
$tplFrontend = new Template(TEMPLATE_USER);
$section = $ccm->get_name($_REQUEST['category']);
$section = (empty($section))? 'home': $section;
$categoryID = ($_REQUEST['category'] == 'home') ? 0 : $_REQUEST['category'];
list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu($categoryID);

$tpl->assign('subcat', $subcat);
$tpl->assign('allcategorys', $parentCategories);
$tpl->assign('datos_cat', $datos_cat);
$allcategorys = $parentCategories;


if (isset($_REQUEST['action']) ) {

    switch ($_REQUEST['action']) {

        case 'list':

            // Check if the user can edit frontpages
            if(!Acl::check('ARTICLE_FRONTPAGE')) {
                Acl::deny();
            } elseif (!Acl::_C($categoryID)) {
                $categoryID = $_SESSION['accesscategories'][0];
                $section = $ccm->get_name($categoryID);
                $_REQUEST['category'] = $categoryID;
                list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu();

                $tpl->assign('subcat', $subcat);
                $tpl->assign('allcategorys', $parentCategories);
                $tpl->assign('datos_cat', $datos_cat);
                $tpl->assign('category', $_REQUEST['category']);
            }



            $cm = new ContentManager();
            $rating = new Rating();
            $comment = new Comment();

            // Edit main frontpage or category frontpage
            if($_REQUEST['category'] == 'home') {

                $frontpage_articles =
                        $cm->find(  'Article',
                                    'in_home=1 AND frontpage=1 AND content_status=1
                                     AND available=1 AND fk_content_type=1',
                                    'ORDER BY home_pos ASC');
                $destacada =
                        $cm->find_by_category(  'Article',
                                                $_REQUEST['category'],
                                                'fk_content_type=1 AND content_status=1 AND available=1
                                                AND frontpage=1 AND home_placeholder="placeholder_0_0" ',
                                                'ORDER BY position ASC, created DESC');

                //Sugeridas -
                list($articles, $pages)=
                        $cm->find_pages('Article',
                                        'content_status=1 AND available=1 AND frontpage=1
                                        AND fk_content_type=1 AND in_home=2',
                                        'ORDER BY created DESC, title ASC ',$_REQUEST['page'],10);

                $params = "'".$_REQUEST['category']."'";
                $paginacion = $cm->makePagesLinkjs($pages, ' get_suggested_articles', $params);

                $tpl->assign('paginacion', $paginacion);
                $tpl->assign('other_category','suggested');

                // Get the suggested articles for homepage
                $aut=new User();
                $suggestedArticles = $cm->getSuggestedContentsForHomePage();
                foreach ($suggestedArticles as $key => $value) {
                    $suggestedArticles[$key]->catName = $ccm->get_title($value->catName);
                    $suggestedArticles[$key]->authorName = $aut->get_user_name($value->fk_publisher);
                    $suggestedArticles[$key]->lastEditorName = $aut->get_user_name($value->fk_user_last_editor);

                }
                $tpl->assign('suggestedArticles', $suggestedArticles);

            } else {
                    $frontpage_articles =
                            $cm->find_by_category(  'Article',
                                                    $_REQUEST['category'],
                                                    'fk_content_type=1 AND content_status=1  AND available=1
                                                    AND frontpage=1 ',
                                                    'ORDER BY position ASC, created DESC' );

                    $destacada = $cm->find_by_category('Article',
                                                       $_REQUEST['category'],
                                                       'fk_content_type=1 AND content_status=1 AND available=1 AND frontpage=1
                                                       AND placeholder="placeholder_0_0" ',
                                                       'ORDER BY position ASC, created DESC');

                    list($articles, $pages)= $cm->find_pages('Article',
                                                             'content_status=1 AND available=1 AND frontpage=0
                                                             AND fk_content_type=1 ',
                                                             'ORDER BY  created DESC,  title ASC ',
                                                             $_REQUEST['page'],
                                                             10,
                                                             $_REQUEST['category']);

                    $params=$_REQUEST['category'];
                    $paginacion=$cm->makePagesLinkjs($pages, ' get_others_articles', $params);
                    if($pages->_totalPages>1) {
                            $tpl->assign('paginacion', " ".$paginacion);
                    }
            }

            // Nombres de los publisher y editors
            $aut=new User();

            foreach ($frontpage_articles as $art){
                $art->category_name= $art->loadCategoryName($art->id);
                $art->publisher=$aut->get_user_name($art->fk_publisher);
                $art->editor=$aut->get_user_name($art->fk_user_last_editor);
                $art->rating= $rating->get_value($art->id);
                $art->comment = $comment->count_public_comments( $art->id );
                if($_REQUEST['category'] == 'home') {
                    $art->position = $art->home_pos;
                }
            }

            /// Adding Widgets {{{
            $contentsInHomepage = $cm->getContentsForHomepageOfCategory($categoryID);

            $contents_excluded_for_proposed = array();
            foreach($contentsInHomepage as $content) {
                $frontpage_articles[] = $content;
                $contents_excluded_for_proposed[] = $content->id;
            }

            $frontpage_articles = $cm->sortArrayofObjectsByProperty($frontpage_articles, 'position');
            // }}}

            if(!isset($destacado)){
                $destacado = null;
            }
            foreach ($articles as $key => $value) {
                $articles[$key]->catName = $ccm->get_title($value->catName);
                $articles[$key]->authorName = $aut->get_user_name($value->fk_publisher);
                $articles[$key]->lastEditorName = $aut->get_user_name($value->fk_user_last_editor);
            }
            $tpl->assign('destacado', $destacado);
            $tpl->assign('articles', $articles);
            $tpl->assign('frontpage_articles', $frontpage_articles);


            if(count($contents_excluded_for_proposed) >0) {
                $opinions_excluded = implode(', ', $contents_excluded_for_proposed);
                $sql_excluded_opinions = ' AND `pk_opinion` NOT IN ('.$opinions_excluded.')';
            } else {
                $sql_excluded_opinions = ' AND 1 = 1';
            }

            $opinions = $cm->find('Opinion',
                                  'contents.available = 1'
                                  .$sql_excluded_opinions
                                  ,' ORDER BY created DESC LIMIT 0,16');

            $rating = new Rating();
            foreach($opinions as $opinion) {
                $opinion->comments = count($comment->get_comments( $opinion->id ));
                $opinion->author = new Author($opinion->fk_author);
                $opinion->ratings = $rating->get_value($opinion->id);
            }

            if(count($contents_excluded_for_proposed) >0) {
                $widgets_excluded = implode(', ', $contents_excluded_for_proposed);
                $sql_excluded_widgets = ' AND `pk_widget` NOT IN ('.$widgets_excluded.')';
            } else {
                $sql_excluded_widgets  = ' AND 1 = 1';
            }

            $widgets = $cm->find('Widget',
                                 'fk_content_type=12 AND `available`=1 '
                                 .$sql_excluded_widgets,
                                 'ORDER BY created DESC ');


            $tpl->assign('widgets', $widgets);
            $tpl->assign('opinions', $opinions);
            $tpl->assign('category', $_REQUEST['category']);
            $_SESSION['desde']='list';
            $_SESSION['_from']=$_REQUEST['category'];

            $tpl->display('article/list.tpl');

        break;

        case 'list_pendientes':

            Acl::checkOrForward('ARTICLE_PENDINGS');

            //Comprobación si el usuario tiene acceso a esta categoria/seccion.
            if ($_REQUEST['category'] != 'todos') {
                 if(!Acl::_C( $_REQUEST['category'])){
                      m::add(_("you don't have enought privileges to see this category.") );
                      Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
                 }
            } elseif (!Acl::_C($categoryID)) {
                $categoryID = $_SESSION['accesscategories'][0];
                $section = $ccm->get_name($categoryID);
                $_REQUEST['category'] = $categoryID;
                list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu();
                $tpl->assign('subcat', $subcat);
                $tpl->assign('allcategorys', $parentCategories);
                $tpl->assign('datos_cat', $datos_cat);
                $tpl->assign('category', $_REQUEST['category']);
            }

            $cm = new ContentManager();
            if (!isset($_REQUEST['category']) || $_REQUEST['category']=='home' || $_REQUEST['category']=='0' ) {
                $_REQUEST['category'] = 'todos';
            }

            $names = array();
            $art_publishers = array();
            $art_editors = array();

            if ($_REQUEST['category'] == 'todos') {
                $articles = $cm->find('Article', 'fk_content_type=1 AND available=0', 'ORDER BY position ASC, created DESC ');
                $tpl->assign('articles', $articles);
                if(Acl::check('OPINION_ADMIN')){
                    $opinions = $cm->find('Opinion', 'fk_content_type=4 AND available=0 ',
                                            'ORDER BY created DESC, type_opinion DESC, title ASC');
                    $tpl->assign('opinions', $opinions);
                    if(!empty($opinions)){
                        $aut = new User();
                        foreach ($opinions as $opin) {
                            $autor = new Author($opin->fk_author);
                            $names[] = $autor->name;
                            $art_publishers[]=$aut->get_user_name($opin->fk_publisher);
                            $art_editors[]=$aut->get_user_name($opin->fk_user_last_editor);
                        }
                        $tpl->assign('opin_names', $names);
                        $tpl->assign('opin_editors', $art_publishers);
                        $tpl->assign('opin_editors', $art_editors);
                    }
                }


            }elseif($_REQUEST['category'] == 'opinion'){
                $opinions = $cm->find('Opinion', 'fk_content_type=4 AND available=0',
                                      'ORDER BY created DESC, type_opinion DESC, title ASC');
                $tpl->assign('opinions', $opinions);
                $aut = new User();
                foreach ( $opinions as $opin) {
                    $autor = new Author($opin->fk_author);
                    $names[] = $autor->name;
                    $art_publishers[]=$aut->get_user_name($opin->fk_publisher);
                    $art_editors[]=$aut->get_user_name($opin->fk_user_last_editor);
                }
                $tpl->assign('opin_names', $names);
                $tpl->assign('opin_editors', $art_publishers);
                $tpl->assign('opin_editors', $art_editors);
            }else{
                // ContentManager::find_pages(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>,<PAGE>,<ITEMS_PER_PAGE>,<CATEGORY>);
                list($articles, $pager)= $cm->find_pages('Article', 'available=0 AND fk_content_type=1 ', 'ORDER BY  created DESC, title ASC ',$_REQUEST['page'],10, $_REQUEST['category']);
                $tpl->assign('articles', $articles);
                $tpl->assign('paginacion', $pager);
            }

            if (!empty($articles)) {
                $aut = new User();
                $art_publishers = array();
                $art_editors = array();

                foreach ($articles as $art){
                    $art->category_name= $art->loadCategoryName($art->id);
                    $art_publishers[]=$aut->get_user_name($art->fk_publisher);
                    $art_editors[]=$aut->get_user_name($art->fk_user_last_editor);
                }
                $tpl->assign('art_publishers', $art_publishers);
                $tpl->assign('art_editors', $art_editors);
            }
            $tpl->assign('category', $_REQUEST['category']);
            $_SESSION['desde']='list_pendientes';
            $_SESSION['_from']=$_REQUEST['category'];

            $tpl->display('article/pending.tpl');

        break;

        case 'list_agency':
            Acl::checkOrForward('ARTICLE_PENDINGS');

            //Comprobación si el usuario tiene acceso a esta categoria/seccion.
            if ($_REQUEST['category'] != 'todos') {
                 if(!Acl::_C( $_REQUEST['category'])) {
                      m::add(_("you don't have enought privileges to see this category.") );
                      Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
                 }

            }
            $tpl->assign('titulo_barra', 'Gesti&oacute;n de Agencias');

            $cm = new ContentManager();
            if (!isset($_REQUEST['category']) || $_REQUEST['category']=='home' || $_REQUEST['category']=='home' ) {
                $_REQUEST['category'] = 'todos';
            }

            if ($_REQUEST['category'] == 'todos') {
                $articles = $cm->find('Article', 'fk_content_type=1 AND available=0 ', 'ORDER BY  position ASC, created DESC ');

                $tpl->assign('articles', $articles);
                $opinions = $cm->find('Opinion', 'fk_content_type=4 AND available=0 ',
                                      'ORDER BY created DESC, type_opinion DESC, title ASC');
                $tpl->assign('opinions', $opinions);
                if(!empty($opinions)){
                    $aut = new User();
                    foreach ($opinions as $opin) {
                        $autor = new Author($opin->fk_author);
                        $names[] = $autor->name;
                        $art_publishers[]=$aut->get_user_name($opin->fk_publisher);
                        $art_editors[]=$aut->get_user_name($opin->fk_user_last_editor);
                    }
                    $tpl->assign('opin_names', $names);
                    $tpl->assign('opin_editors', $art_publishers);
                    $tpl->assign('opin_editors', $art_editors);
                }
            }elseif($_REQUEST['category'] == 'opinion'){
                $opinions = $cm->find('Opinion', 'fk_content_type=4 AND available=0 ',
                                      'ORDER BY created DESC, type_opinion DESC, title ASC');
                $tpl->assign('opinions', $opinions);
                $aut = new User();
                foreach ( $opinions as $opin) {
                    $autor = new Author($opin->fk_author);
                    $names[] = $autor->name;
                    $art_publishers[]=$aut->get_user_name($opin->fk_publisher);
                    $art_editors[]=$aut->get_user_name($opin->fk_user_last_editor);
                }
                $tpl->assign('opin_names', $names);
                $tpl->assign('opin_editors', $art_publishers);
                $tpl->assign('opin_editors', $art_editors);
            }else{
                // ContentManager::find_pages(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>,<PAGE>,<ITEMS_PER_PAGE>,<CATEGORY>);
                list($articles, $pager)= $cm->find_pages('Article', 'available=0 AND fk_content_type=1 ', 'ORDER BY  created DESC, title ASC ',$_REQUEST['page'],10, $_REQUEST['category']);
                $tpl->assign('articles', $articles);
                $tpl->assign('paginacion', $pager);
            }

            if (!empty($articles)) {
                $aut = new User();
                $art_publishers = array();
                $art_editors = array();

                foreach ($articles as $art){
                    $art->category_name= $art->loadCategoryName($art->id);
                    $art_publishers[]=$aut->get_user_name($art->fk_publisher);
                    $art_editors[]=$aut->get_user_name($art->fk_user_last_editor);
                }
                $tpl->assign('art_publishers', $art_publishers);
                $tpl->assign('art_editors', $art_editors);
            }
            $tpl->assign('category', $_REQUEST['category']);
            $_SESSION['desde']='list_agency';
            $_SESSION['_from']=$_REQUEST['category'];

            $tpl->display('article/article.tpl');

        break;


        case 'list_hemeroteca':

            if (!isset($_REQUEST['category']) || $_REQUEST['category'] =='home') {
                $_REQUEST['category'] = 'todos';
            }
            $cm = new ContentManager();
            $rating = new Rating();
            list($articles, $pager)= $cm->find_pages('Article', 'content_status=0 AND available=1 AND fk_content_type=1 ', 'ORDER BY changed DESC, created DESC, title ASC ',$_REQUEST['page'],10, $_REQUEST['category']);
            $aut=new User();
            $comment = new Comment();
            foreach ($articles as $art){
                $art->category_name= $art->loadCategoryName($art->id);
                $art->publisher=$aut->get_user_name($art->fk_publisher);
                $art->editor=$aut->get_user_name($art->fk_user_last_editor);
                $art->rating= $rating->get_value($art->id);
                $art->comment = $comment->count_public_comments( $art->id );
            }
            $tpl->assign('articles', $articles);
            $tpl->assign('paginacion', $pager);
            $tpl->assign('category', $_REQUEST['category']);
            $_SESSION['desde']='list_hemeroteca';

            $tpl->display('article/library.tpl');

        break;

        case 'new':   // Crear un nuevo artículo

            Acl::checkOrForward('ARTICLE_CREATE');

            if(!isset($_REQUEST['category']) || $_REQUEST['category']=='' || $_REQUEST['category']=='home') {
                $_REQUEST['category']=$_SESSION['_from'];
            }
            $tpl->assign('category', $_REQUEST['category']);
            $cm = new ContentManager();
            //FIXME: cambiar por la llamada a vars php en smarty
            $tpl->assign('MEDIA_IMG_PATH_WEB', MEDIA_IMG_PATH_WEB);


            $tpl->assign(array( 'availableSizes'=>array(20,22,24,26,28,30,32,34)
                        ) );

            //TODO: AJAX
            // require_once('controllers/video/videoGallery.php');
            $tpl->display('article/new.tpl');

        break;

        case 'read':
        case 'only_read': {
            if($_REQUEST['action'] == 'read') {
                Acl::checkOrForward('ARTICLE_UPDATE');
                $tpl->assign('_from', $_SESSION['_from']);
            }
            $article = new Article( $_REQUEST['id'] );
            if (is_string($article->params)) {
                $article->params = unserialize($article->params);
            }

            $tpl->assign('article', $article);

            //Para usar el id de articulo al borrar un comentario
            $_SESSION['olderId']=$_REQUEST['id'];
            $cm = new ContentManager();


            //Photos de noticia
            $img1=$article->img1;
            if(!empty($img1)){
                $photo1 = new Photo($img1);
                $tpl->assign('photo1', $photo1);
            }

            $img2 = $article->img2;
            if(!empty($img2)){
                $photo2 = new Photo($img2);
                $tpl->assign('photo2', $photo2);
            }

            if(is_array($article->params) &&
                    (array_key_exists('imgHome', $article->params)) ) {
                $imgHome = $article->params['imgHome'];
                if(!empty($imgHome)){
                    $photoHome= new Photo($imgHome);
                    $tpl->assign('photoHome', $photoHome);
                }
            }

            $video = $article->fk_video;
            if(!empty($video)) {
                $video1 = new Video($video);
                $tpl->assign('video1', $video1);
            }

            $video = $article->fk_video2;
            if(!empty($video)) {
                $video2 = new Video($video);
                $tpl->assign('video2', $video2);
            }

            $rel= new Related_content();

            $relationes = array();
            $relationes = $rel->get_relations( $_REQUEST['id'] );//de portada
            $losrel = array();
            foreach($relationes as $aret) {
                $resul = new Content($aret);
                $losrel[] = $resul;
            }
            $tpl->assign('losrel', $losrel);

            // Relacionados de interior
            $intrelationes = array();
            $intrelationes = $rel->get_relations_int($_REQUEST['id']);//de interor
            $intrel = array();
            foreach($intrelationes as $aret) {
                $resul = new Content($aret);
                $intrel[] = $resul;
            }
            $tpl->assign('intrel', $intrel);

            //Comentarios
            $comment = new Comment();
            $comments = $cm->find('Comment', ' fk_content="'.$_REQUEST['id'].'"', NULL);
            $tpl->assign('comments', $comments);

            if(!isset($_SESSION['_from'])) {
                $_SESSION['_from'] = $article->category;
            }

            if(isset($_GET['desde']) && $_GET['desde'] == 'search') {
                $_SESSION['_from'] ='search_advanced';
            }

            $tpl->assign(
                array('availableSizes'=>array(20,22,24,26,28,30,32,34))
            );

            $tpl->display('article/new.tpl');
            // }}}
        } break;

        case 'create':

            Acl::checkOrForward('ARTICLE_CREATE');

            if (isset($_POST['with_comment'])) {$_POST['with_comment'] = 1;} else {$_POST['with_comment'] = 0;}
            if (isset($_POST['frontpage'])) {$_POST['frontpage'] = 1;} else {$_POST['frontpage'] = 0;}
            if (isset($_POST['in_home'])) {$_POST['in_home'] = 2;} else {$_POST['in_home'] = 0;}
            if (isset($_POST['content_status'])) {$_POST['content_status'] = 1;} else {$_POST['content_status'] = 0;}

            $article = new Article();
            $_POST['fk_publisher']=$_SESSION['userid'];

            if($article->create( $_POST )) {
                if($_SESSION['desde'] == 'index_portada') {
                    Application::forward('index.php');
                }

                Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
            } else {

                $tpl->assign('errors', $article->errors);
            }
            $tpl->display('article/article.tpl');

        break;

        case 'unlink':
            $article = new Article($_REQUEST['id']);
            $article->unlinkClone();

            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=read&id=' . $_REQUEST['id']);
        break;

        // Restore article and save content, see you that don't exist break.
        case 'restore':
            $_REQUEST['content_status'] = 1;
            // DON'T USE BREAK it must be updated

        case 'update':

           Acl::checkOrForward('ARTICLE_UPDATE');
           if ($_SESSION['desde'] != 'list_hemeroteca') {
                if (isset($_POST['with_comment'])) {$_POST['with_comment'] = 1;} else {$_POST['with_comment'] = 0;}
                if (isset($_POST['frontpage'])) {$_POST['frontpage'] = 1;} else {$_POST['frontpage'] = 0;}
                if (isset($_POST['in_home'])) {$_POST['in_home'] = 2;}
            }
            if (isset($_POST['content_status'])) {$_POST['content_status'] = 1;} else {$_POST['content_status'] = 0;}

            // Register cache control event for updating content
            $GLOBALS['application']->register('onAfterUpdate', 'onAfterUpdate_refreshCache');
            $GLOBALS['application']->register('onAfterUpdate', 'onAfterUpdate_saluda');

            $articleCheck = new Article();
            $articleCheck->read($_REQUEST['id']);

            if(!Acl::isAdmin() && !Acl::check('CONTENT_OTHER_UPDATE') && $articleCheck->fk_user != $_SESSION['userid']) {
                m::add(_("You can't modify this content because you don't have enought privileges.") );
                Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$_REQUEST['id']);

            } else {
                $article = new Article();
                $_REQUEST['fk_user_last_editor'] = $_SESSION['userid'];

                $article->update( $_REQUEST );
            }

            if( $_SESSION['desde']=='search_advanced'){
                if(isset($_GET['stringSearch'])){
                 Application::forward('controllers/search_advanced/search_advanced.php?action=search&stringSearch='.$_GET['stringSearch'].'&category='.$_SESSION['_from'].'&page='.$_REQUEST['page']);
                }else{
                    $_SESSION['desde']='list';
                    $_SESSION['_from']='home';
                }
            }

            if($_SESSION['desde']=='index_portada') {
                Application::forward('index.php');
            }elseif ($_SESSION['desde'] == 'europa_press_import') {
                Application::forward('controllers/agency_importer/europapress.php?action=list&page=0&message=');
            }elseif ($_SESSION['desde'] == 'list') {
                Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&category='.$_SESSION['_from'].'&page='.$_REQUEST['page']);
            }elseif ($_SESSION['desde'] == 'list_hemeroteca') {
                Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
            }
            if(isset($_REQUEST['available']) && $_REQUEST['available'] == 1){
                 $_SESSION['desde']='list';
                 $_SESSION['_from'] =$_REQUEST['category'];

            }else{
                $_SESSION['desde']='list_pendientes';
            }
            Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&category='.$_SESSION['_from'].'&page='.$_REQUEST['page']);
        break;

        case 'validate':

            if ($_SESSION['desde'] != 'list_hemeroteca') {
                if (isset($_POST['with_comment'])) {$_POST['with_comment'] = 1;} else {$_POST['with_comment'] = 0;}
                if (isset($_POST['frontpage'])) {$_POST['frontpage'] = 1;} else {$_POST['frontpage'] = 0;}
                if (isset($_POST['in_home'])) {$_POST['in_home'] = 2;}
            }
            if (isset($_POST['content_status'])) {$_POST['content_status'] = 1;}   else {$_POST['content_status'] = 0;}

            $article = new Article();
            $_REQUEST['fk_user_last_editor'] = $_SESSION['userid'];

            if(!$_POST["id"]) {
                Acl::checkOrForward('ARTICLE_CREATE');
                $_POST['fk_publisher'] = $_SESSION['userid'];

                //Estamos creando un nuevo artículo
                if(!$article->create( $_POST )) {
                    $tpl->assign('errors', $article->errors);
                }
            } else {
                Acl::checkOrForward('ARTICLE_UPDATE');
                $articleCheck = new Article();
                $articleCheck->read($_REQUEST['id']);
                if(!Acl::isAdmin() && !Acl::check('CONTENT_OTHER_UPDATE') && $articleCheck->fk_user != $_SESSION['userid']) {
                    m::add(_("You can't modify this content because you don't have enought privileges.") );
                    Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$_REQUEST['id']);

                } else {
                    $article->update( $_POST );
                }
            }

            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=read&category=' . $_SESSION['_from'] .
                                 '&id=' . $article->id);

         break;

        case 'preview':


            $_REQUEST['fk_user_last_editor']=$_SESSION['userid'];
            $article = new Article();
            if(!$_POST["id"] || empty($_POST["id"])) {
                Acl::checkOrForward('ARTICLE_UPDATE');
                $_POST['fk_publisher'] = $_SESSION['userid'];
                //Estamos creando un nuevo artículo

                if(!$article->create( $_POST ))
                      $tpl->assign('errors', $article->errors);
            } else {
                //Estamos atualizando un artículo
                 Acl::checkOrForward('ARTICLE_UPDATE');
                 $articleCheck = new Article();
                 $articleCheck->read($_REQUEST['id']);

                if(!Acl::isAdmin() && !Acl::check('CONTENT_OTHER_UPDATE') && $articleCheck->fk_user != $_SESSION['userid']) {
                    m::add(_("You can't modify this content because you don't have enought privileges.") );
                    Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$_REQUEST['id']);

                } else {
                    $article->update( $_REQUEST );
                }
            }
            Application::ajax_out($article->id);
        break;

        case 'delete':
                Acl::checkOrForward('ARTICLE_DELETE');
                $articleCheck = new Article();
                $articleCheck->read($_REQUEST['id']);

                if(!Acl::isAdmin() && !Acl::check('CONTENT_OTHER_UPDATE') && $articleCheck->fk_user != $_SESSION['userid']) {
                    m::add(_("You can't modify this content because you don't have enought privileges.") );
                    Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$id);

                } else {

                    $article = new Article($_REQUEST['id']);
                    $rel= new Related_content();
                    $relationes=array();

                    $relationes = $rel->get_content_relations( $_REQUEST['id'] );//de portada
                    $msg ='';
                    if(!empty($relationes)){
                         $msg = "El articulo \"".$article->title."\", está relacionado con los siguientes contenidos:  \n";
                         $cm= new ContentManager();
                         $relat = $cm->getContents($relationes);
                         foreach($relat as $contents) {
                               $msg.=" - ".strtoupper($contents->content_type)." - ".$contents->category_name." ".$contents->title. "\n";
                         }
                         $msg.="\n \n ¡Ojo! Si lo borra, se eliminar&aacute;n las relaciones con los articulos \n";
                         $msg.=" ¿Desea eliminarlo igualmente?";
                       /*  $msg.='<br /><a href="'.$_SERVER['SCRIPT_NAME'].'?action=yesdel&id='.$_REQUEST['id'].'">  <img src="themes/default/images/ok.png" title="SI">  </a> ';
                         $msg.='   <a href="#" onClick="hideMsgContainer(\'msgBox\');"> <img src="themes/default/images/no.png" title="NO">  </a></p>';
                       */
                    }else{
                       $msg.="¿Está seguro que desea eliminar \"".$article->title."\"?";
                     /*   $msg.='<br /><a href="'.$_SERVER['SCRIPT_NAME'].'?action=yesdel&id='.$_REQUEST['id'].'">  <img src="themes/default/images/ok.png" title="SI">  </a> ';
                        $msg.='   <a href="#" onClick="hideMsgContainer(\'msgBox\');"> <img src="themes/default/images/no.png" title="NO">  </a></p>';
                      */
                    }
                    echo $msg;
                    exit(0);
                }
        break;

        case 'delete_comment': {

            $category = filter_input ( INPUT_GET, 'category' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'todos')) );
            if($category == 'encuesta'){
                $comment = new PC_Comment();
                $comment->delete($_REQUEST['id']);
            } else {
                $comment = new Comment();
                $comment->delete($_POST['id'], $_SESSION['userid']);
            }


            Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$_SESSION['olderId'].'&stringSearch='.$_REQUEST['stringSearch'].'&page=#comments');
        } break;

        case 'yesdel':
            Acl::checkOrForward('ARTICLE_DELETE');
            if($_REQUEST['id']){
                $article = new Article($_REQUEST['id']);

                //Delete relations
                $rel= new Related_content();
                $rel->delete_all($_REQUEST['id']);
                $article->delete( $_REQUEST['id'], $_SESSION['userid'] );

                // If it's clone then remove
                if($article->isClone()) {
                    $article->unlinkClone();
                    $article->remove($_REQUEST['id']);
                }
            }
            Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
        break;

        case 'change_status':

            Acl::checkOrForward('ARTICLE_ARCHIVE');

            $article = new Article($_REQUEST['id']);

            // FIXME: evitar otros valores erróneos
            $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
            if($status==1){
                    //Recuperar de hemeroteca
                   //$article->set_available(0,$_SESSION['userid']);
                   //  $_position=array(100,'placeholder_0_1',$_REQUEST['id']);
                    // $article->set_inhome(0,$_SESSION['userid']);
                    $article->set_frontpage(0,$_SESSION['userid']);
                    // $article->set_position($_position,$_SESSION['userid']);
            }else{
                //Enviar a la hemeroteca eliminar caches.
                if($article->frontpage==1) {
                    $_position=array(100,'placeholder_0_1',$_REQUEST['id']);
                    $article->set_position($_position,$_SESSION['userid']);
                    if($article->in_home==1) {
                        $article->set_home_position($_position,$_SESSION['userid']);
                        $article->set_inhome(0,$_SESSION['userid']);
                    }
                    $article->set_frontpage(0,$_SESSION['userid']);
                }
            }
            $article->set_status($status,$_SESSION['userid']);
            if(isset($_REQUEST['desde']) && ($_REQUEST['desde']=='search')) {
                if($article->available==0){
                    $_SESSION['desde']='list_pendientes';
                }else{ $_SESSION['desde']='list';  }

            }
            Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
        break;

        case 'm_restore':

            Acl::checkOrForward('ARTICLE_ARCHIVE');

            if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0) {
                $fields = $_REQUEST['selected_fld'];
                if(is_array($fields)) {
                    $changed = date('Y-m-d H:i:s');
                    $_status = array();
                    $_available = array();
                    $_inhome = array();
                    $_position = array();
                    $_home = array();
                    $_frontpage = array();
                      //Restaurar la hemeroteca
                    foreach($fields as $i ) {
                        $_status[] = array(1, $_SESSION['userid'], $changed, $i);
                     /*   $_available[] = array(0, 0, $_SESSION['userid'], $changed, $i);
                        $_inhome[] = array(0, $i);
                        $_position[] = array(100,'placeholder_0_1', $i);
                        $_home[] = array(100, 'placeholder_0_1',$i);
                        $_frontpage[] = array(0, $i); */
                    }

                    $article = new Article();
                    $article->set_status($_status, $_SESSION['userid']);
                   //Lo restaura a no frontpage.
                   //$article->set_available($_available, $_SESSION['userid']);
                  //  $article->set_inhome($_inhome, $_SESSION['userid']);
                  //  $article->set_position($_position, $_SESSION['userid']);
                  //  $article->set_home_position($_home, $_SESSION['userid']);
                  //  $article->set_frontpage($_frontpage, $_SESSION['userid']);
                }
            }
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list_hemeroteca&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
        break;

        case 'frontpage_status':
            Acl::checkOrForward('ARTICLE_FRONTPAGE');

            $article = new Article($_REQUEST['id']);
            // FIXME: evitar otros valores erróneos
            $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
            $_position=array(100,'placeholder_0_1',$_REQUEST['id']);
            $article->set_frontpage($status,$_SESSION['userid']);
            $article->set_position($_position, $_SESSION['userid']);


            Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
        break;


        case 'available_status':
            Acl::checkOrForward('ARTICLE_AVAILABLE');

            $article = new Article($_REQUEST['id']);
            // FIXME: evitar otros valores erróneos
            $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
            $article->set_available($status,$_SESSION['userid']);

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list_pendientes&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
        break;

        case 'inhome_status':
            Acl::checkOrForward('ARTICLE_HOME');
            $article = new Article($_REQUEST['id']);

            // FIXME: evitar otros valores erróneos
            $status = array($_REQUEST['status'], $_REQUEST['id']); // Evitar otros valores
            $article->set_inhome($status,$_SESSION['userid']);
            //if($_REQUEST['status']==1){
            //    $_home[] = array(105, 'placeholder_0_1',$_REQUEST['id']);
            //    $article->set_home_position($_home, $_SESSION['userid']);
            //}
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
        break;

        case 'set_position':
            Acl::checkOrForward('ARTICLE_FRONTPAGE');
            $article = new Article($_REQUEST['id']);
            $article->set_position($_REQUEST['posicion'],$_SESSION['userid']);
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_GET['category'].'&page='.$_REQUEST['page']);
        break;

        case 'mstatus':
            //Enviar a la hemeroteca.
            Acl::checkOrForward('ARTICLE_ARCHIVE');
            if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0) {
                $fields = $_REQUEST['selected_fld'];
                if(is_array($fields)) {
                    $_status = array();
                    $_frontpage=array();
                    foreach($fields as $i ) {
                        $_status[] = array(0, $_SESSION['userid'], date('Y-m-d H:i:s'), $i);
                        $_position[]=array(100,'placeholder_0_1',$i);
                        $_frontpage[]=array(0,$i);
                    }
                    $article = new Article();
                    $article->set_status($_status, $_SESSION['userid']);
                    $article->set_frontpage($_frontpage,$_SESSION['userid']);
                    $article->set_position($_position, $_SESSION['userid']);

                 }
            }

            if(isset($_REQUEST['no_selected_fld']) && count($_REQUEST['no_selected_fld'])>0) {
                $fields = $_REQUEST['no_selected_fld'];
                if(is_array($fields)) {
                    $_status = array();
                    foreach($fields as $i ) {
                        $_status[] = array(0, $_SESSION['userid'], date('Y-m-d H:i:s'), $i);
                    }
                    $article = new Article();
                    $article->set_status($_status, $_SESSION['userid']);
                }
            }

            Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
        break;

        case 'mfrontpage':
            Acl::checkOrForward('ARTICLE_FRONTPAGE');
            $status = ($_REQUEST['id']==1)? 1: 0; // Evitar otros valores
            //Usa id para pasar el estatus

            if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0) {
                $fields = $_REQUEST['selected_fld'];
                $_status=array();
                if(is_array($fields)) {
                    $pos=100;
                    foreach($fields as $i ) {
                        $pos++;
                        $_position[]=array($pos,'placeholder_0_1',$i);
                        $_status[] = array($_REQUEST['id'], $i);
                    }
                    $article = new Article();
                    $article->set_frontpage($_status,$_SESSION['userid']);
                    $article->set_position($_position, $_SESSION['userid']);
                }
            }
            if(isset($_REQUEST['no_selected_fld']) && count($_REQUEST['no_selected_fld'])>0) {
                $fields = $_REQUEST['no_selected_fld'];
                $_status=array();
                $_position=array();
                if(is_array($fields)) {
                    $pos=100;
                    foreach($fields as $i ) {
                         $pos++;
                        $_status[] = array($_REQUEST['id'], $i);
                        $_position[]=array($pos,'placeholder_0_1',$i);
                    }
                    $article = new Article();
                    $article->set_frontpage($_status,$_SESSION['userid']);
                    $article->set_position($_position, $_SESSION['userid']);

                }
            }
            Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
         break;

         case 'mavailable':
             Acl::checkOrForward('ARTICLE_AVAILABLE');
             if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0) {
                 $fields = $_REQUEST['selected_fld'];

                 $_available=array();
                 $status=($_REQUEST['id']==1)? 1: 0; // Evitar otros valores
                  //Usa id para pasar el estatus
                 $changed = date('Y-m-d H:i:s');
                 if(is_array($fields)) {
                     foreach($fields as $i ) {
                         if($_REQUEST['permit_'.$i]==1){
                            $_available[] = array($status, $status, $_SESSION['userid'], $changed, $i);
                         }
                     }
                     $content = new Article();
                     $content->set_available($_available, $_SESSION['userid']);
                 }
             }

             Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&category='.$_SESSION['_from'].'&page='.$_REQUEST['page']);
             break;

         //New function -For XML articles. published directly in frontpages and no change position
         case 'mdirectly_frontpage':
             if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0) {
                 $fields = $_REQUEST['selected_fld'];

                 $_available=array();
                 $status=($_REQUEST['id']==1)? 1: 0; // Evitar otros valores
                  //Usa id para pasar el estatus
                 $changed = date('Y-m-d H:i:s');
                  $pos_opinion=1;
                 if(is_array($fields)) {
                  //    $ccm = ContentCategoryManager::get_instance();
                      $numCategories = array();
                      $numCategories = $ccm->get_all_categories();
                      $cm=new ContentManager();

                      foreach($fields as $i ) {
                             $content = new Content($i);
                             if($content->content_type==4){ //Es opinion
                                  $_available[] = array($status, $status, $status, $pos_opinion, $_SESSION['userid'], $changed, $i);
                                  $pos_opinion++;
                             }else{

                                 $actual_category=$ccm->get_name($content->category);

                                 $numCategories[$actual_category]+=1;
                                 if($numCategories[$actual_category]==1){
                                      //incluirla como destacada
                                     $destacadas = $cm->find_by_category('Article', $content->category, 'fk_content_type=1 AND content_status=1  AND available=1 AND frontpage=1  AND placeholder="placeholder_0_0" ', 'ORDER BY created DESC' );
                                     $destacadas = $cm->getInTime($destacadas);
                                     //quitamos las antiguas destacadas
                                     $_positions = array();
                                     $pos=2;
                                     foreach ($destacadas as $art){
                                        $_positions[] = array($pos, 'placeholder_0_1',  $art->id);
                                        $pos++;
                                     }

                                     $content->set_position($_positions, $_SESSION['userid']);
                                     $content->set_available(1, $_SESSION['userid']);
                                     $content->set_frontpage(1, $_SESSION['userid']);
                                     $params=array(1, 'placeholder_0_0', $i);
                                     $content->set_position($params, $_SESSION['userid']);

                                 }else{
                                     //Incluirla como noticia de la categoria.
                                      $_available[] = array($status, $status, $status, $numCategories[$actual_category], $_SESSION['userid'], $changed, $i);
                                 }
                             }
                      }


                    // Recorremos las categorias y las dejamos en 20. Eliminamos caches
                     $tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);
                     foreach($numCategories as $category=>$num){
                        if($num!=0 && $category!='UNKNOWN' && $category !='opinion'){
                             $category=strtolower(String_Utils::normalize_name($category));
                             if (($category == 'política') || ($category == 'polItica')|| ($category == 'politica')){
                                     $category = 'polItica';
                             }
                             $id_category=$ccm->get_id($category);

                             $total=$num;
                             $_frontpage = array();
                             $_positions = array();
                             //reducir a 20 noticias en portada
                             $articles= $cm->find_by_category('Article', $id_category, 'fk_content_type=1 AND content_status=1 AND available=1 AND frontpage=1   AND placeholder!="placeholder_0_0"', 'ORDER BY changed DESC, position ASC ');
                             $articles = $cm->getInTime($articles);
                             foreach($articles as $article){
                                 if($total<20){
                                     $_position[]= array($total,'placeholder_0_1', $article->id);
                                     $total++;
                                 }else{
                                      $_frontpage[] = array(0, $article->id);
                                 }

                             }
                             $article = new Article();
                             $article->set_frontpage($_frontpage, $_SESSION['userid']);
                            $article->set_position($_position, $_SESSION['userid']);
                             $tplManager->delete($category . '|RSS');
                             $delete = $tplManager->delete($category . '|0');
                        }
                    }
                      //actualizamos los frontpages.
                     $content = new Content();
                     $content->set_directly_frontpage($_available, $_SESSION['userid']);


             }
            }
            Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&category='.$_SESSION['_from'].'&page='.$_REQUEST['page']);
            break;

        case 'm_inhome_status':
            Acl::checkOrForward('ARTICLE_HOME');
            $fields = $_REQUEST['selected_fld'];
            if(is_array($fields)) {
                $status = $_REQUEST['id'];
                //Usa id para pasar el estatus
                $_status = array();
                $_inhome = array();
                $_available = array();
                $changed = date('Y-m-d H:i:s');

                $_status = array();
                foreach($fields as $i ) {
                   if($status==1) {
                        $_status[] = array(1, $_SESSION['userid'], $changed, $i);
                        $_inhome[] = array(1, $i);
                        $_available[] = array(1, $status, $_SESSION['userid'], $changed, $i);
                   }else{
                        $_inhome[] = array($status, $i);
                   }
                }
                $article = new Article();
                $article->set_inhome($_inhome, $_SESSION['userid']);
                //$article->set_status($_status, $_SESSION['userid']);
                //$article->set_available($_available, $_SESSION['userid']);
            }

              $fields = (isset($_REQUEST['no_selected_fld'])) ? ($_REQUEST['no_selected_fld']) : null;
              if(is_array($fields)) {
                $status = ($_REQUEST['id']==1)? 1: 0; // Evitar otros valores
                //Usa id para pasar el estatus
                $_inhome = array();
                $changed = date('Y-m-d H:i:s');
                $_status = array();
                foreach($fields as $i ) {
                   if($status==1) {
                        $_inhome[] = array(1, $i);
                   }else{
                        $_inhome[] = array(0, $i);
                   }
                }
                $article = new Article();
                $article->set_inhome($_inhome, $_SESSION['userid']);
            }

           Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);

        break;

        case 'mdelete':
            Acl::checkOrForward('ARTICLE_DELETE');
            if($_REQUEST['id']==6){ //Eliminar todos
                $cm = new ContentManager();
                if($_SESSION['_from']=='todos' || $_SESSION['_from']=='opinion'){
                   $articles = $cm->find('Article', 'available=0 AND content_status=0 AND fk_content_type=1', 'ORDER BY created DESC, title ASC ');
                   $opinions = $cm->find('Opinion', 'fk_content_type=4 AND available=0', 'ORDER BY created DESC, type_opinion DESC, title ASC');
                   if(count($opinions)>0){
                       foreach ($opinions as $art){
                                  $opinion = new Opinion($art->id);
                                  $opinion->delete($art->id,$_SESSION['userid'] );
                       }
                   }
                }else{
                   $articles = $cm->find_by_category('Article', $_SESSION['_from'], 'available=0 AND content_status=0 AND fk_content_type=1', 'ORDER BY created DESC, title ASC ');
                }
               if(count($articles)>0){
                   foreach ($articles as $art){
                              $article = new Article($art->id);
                              $article->delete($art->id,$_SESSION['userid'] );
                   }
               }
               Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&category='. $_SESSION['_from'].'&page='.$_REQUEST['page']);
            }

            if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0) {
                $fields = $_REQUEST['selected_fld'];
                  $msg="Los articulos ";
                    $nodels=array();
                    $alert='';
                if(is_array($fields)) {

                 /*  foreach($fields as $i ) {
                        $article = new Article($i);
                        $rel= new Related_content();
                        $relationes=array();

                        $relationes = $rel->get_content_relations( $i );//de portada

                        if(!empty($relationes)){
                             $nodels[] =$i;
                             $alert='ok';
                             $msg .= " \"".$article->title."\",    \n";

                        }else{
                            $article->delete($i,$_SESSION['userid'] );
                        }
                    }
*/
                     foreach($fields as $i ) {
                        $content = new Content($i);
                        $rel= new Related_content();
                        $relationes=array();

                        $relationes = $rel->get_content_relations( $i );//de portada

                        if(!empty($relationes)){
                             $nodels[] =$i;
                             $alert='ok';
                             $msg .= " \"".$content->title."\",    \n";

                        }else{
                            $content->delete($i,$_SESSION['userid'] );
                        }
                     }
                 }

            }
            if(isset($_REQUEST['no_selected_fld']) && count($_REQUEST['no_selected_fld'])>0) {
                $fields = $_REQUEST['no_selected_fld'];
                if(is_array($fields)) {
                    foreach($fields as $i ) {
                        $article = new Article($i);
                       $rel= new Related_content();
                        $relationes=array();

                        $relationes = $rel->get_content_relations($i );//de portada

                        if(!empty($relationes)){
                                $alert='ok';
                             $msg .= "  \"".$article->title."\"   ";

                        }else{
                          $article->delete($i,$_SESSION['userid'] );
                        }
                    }
                }
            }

            $msg.=" tiene relacionados.  !Eliminelos uno a uno!";

            Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&category='. $_SESSION['_from'].'&alert='.$alert.'&msg='.$msg.'&page='.$_REQUEST['page']);

        break;


        case 'get_others_articles':
            //Listado paginado de articulos no en portada de categoria.
            $cm = new ContentManager();
            list($articles, $pages)= $cm->find_pages('Article', 'content_status=1 AND available=1 AND frontpage=0 AND fk_content_type=1 ', 'ORDER BY  created DESC,  title ASC ',$_REQUEST['page'],10, $_REQUEST['category']);
            $params=$_GET['category'];
            //$paginacion=$cm->makePagesLinkjs($pages, 'savePos('.$_GET['category'].'); get_others_articles', $params);
            $paginacion=$cm->makePagesLinkjs($pages, ' get_others_articles', $params);
            $tpl->assign('paginacion', $paginacion);

            $rating = new Rating();
            $comment = new Comment();
            $aut=new User();
            foreach ($articles as $key => $value) {
                $articles[$key]->catName = $ccm->get_title($value->catName);
                $articles[$key]->authorName = $aut->get_user_name($value->fk_publisher);
                $articles[$key]->lastEditorName = $aut->get_user_name($value->fk_user_last_editor);
            }
            $tpl->assign('articles', $articles);

            $html_out=$tpl->fetch('frontpage/blocks/others_articles_in_category.tpl');
            Application::ajax_out($html_out);

        break;

        case 'get_suggested_articles':
        //Sugeridas -
            $cm = new ContentManager();
            list($articles, $pages)= $cm->find_pages('Article', 'content_status=1 AND available=1 AND frontpage=1 AND fk_content_type=1 AND in_home=2', 'ORDER BY  created DESC,  title ASC ',$_REQUEST['page'],10);
            $params="'".$_REQUEST['category']."'";
            //$paginacion=$cm->makePagesLinkjs($pages, 'savePos(\''.$_REQUEST['category'].'\');  get_suggested_articles', $params);
            $paginacion=$cm->makePagesLinkjs($pages, 'get_suggested_articles', $params);
            $tpl->assign('paginacion', $paginacion);

            $tpl->assign('other_category','suggested');
            $rating = new Rating();
            $comment = new Comment();
            $aut=new User();
            foreach ($articles as $key => $value) {
                $articles[$key]->catName = $ccm->get_title($value->catName);
                $articles[$key]->authorName = $aut->get_user_name($value->fk_publisher);
                $articles[$key]->lastEditorName = $aut->get_user_name($value->fk_user_last_editor);
            }

            $tpl->assign('articles', $articles);

            $html_out=$tpl->fetch('frontpage/blocks/others_articles_in_category.tpl');
            Application::ajax_out($html_out);

        break;

        case 'get_frontpage_articles':
             //Listado de portadas por secciones con que no están en home
            $cm = new ContentManager();
            $articles = $cm->find_by_category('Article', $_REQUEST['category'] ,'fk_content_type=1 AND in_home!=1 AND content_status=1 AND available=1 AND frontpage=1  ', 'ORDER BY placeholder ASC, position ASC,  title ASC ');

            $rating = new Rating();
            $comment = new Comment();
            $aut=new User();
            foreach ($articles as $art){
                $art->publisher=$aut->get_user_name($art->fk_publisher);
                $art->editor=$aut->get_user_name($art->fk_user_last_editor);
                $art->rating= $rating->get_value($art->id);
                $art->comment = $comment->count_public_comments( $art->id );
            }

            $tpl->assign('articles', $articles);

            $html_out=$tpl->fetch('article/list_frontpages.tpl');
            Application::ajax_out($html_out);

        break;

        case 'search_related':
            $cm = new ContentManager();
            $mySearch = cSearch::Instance();
            $where="content_status=1 AND available=1 ";
            $search=$mySearch->SearchRelatedContents($_REQUEST['metadata'], 'Article',NULL,$where);
            if(($search) && count($search)>0){
                $id=0;
                //Para evitar mostrar la misma noticia que se edita en sugeridas
                if($search[0]['id']){
                    $id=$search[0]['id'];
                }
                $params=$id.",'".$_REQUEST['metadata']."'";
                $search = $cm->paginate_array_num_js($search,20, 3, "search_related", $params);
                $pages=$cm->pager;
                $paginas='<p align="center">'.$pages->links.'</p>'	;
                $div = print_search_related($id, $search);
            } else{
                $div="<h3>No hay noticias sugeridas</h3>";
                $paginas='No hay noticias que se relacionen con las palabras clave: '.$_REQUEST['metadata'];
            }
            Application::ajax_out($div.$paginas);

        break;

        case 'search_adv':
            /* Buscador en pestaña de contenidos relacionandos*/
            $cm = new ContentManager();
            $mySearch = cSearch::Instance();
            //Transform the input string to search like: 'La via del tren' => '+via +tren'
            $szSourceTags = explode(', ', String_Utils::get_tags($_REQUEST['metadata']));
            $where="available=1 ";
            $search=$mySearch->SearchRelatedContents($szSourceTags, 'Article,Opinion',NULL,$where);
            $szSourceTags = explode(', ', htmlentities(String_Utils::get_tags($_REQUEST['metadata']),NULL,'UTF-8'));
            //Put searched words with diferent color
            $ind = 0; $indice = 0;
            $res = array();
            if ($search) {
                foreach ($search as $res ) {
                    $search[$indice]['metadata'] = htmlentities($search[$indice]['metadata'],NULL, 'UTF-8');
                    for($ind=0; $ind < sizeof($szSourceTags); $ind++){
                        $search[$indice]['title'] = String_Utils::ext_str_ireplace($szSourceTags[$ind], '<b><font color=blue>$1</font></b>', $search[$indice]['title']);
                    }
                    $indice++;
                }
            }

            if(($search) && count($search)>0){
                $params="0,'".$_REQUEST['metadata']."'";
                $search = $cm->paginate_array_num_js($search,20 , 3, "search_adv", $params);
                $pages=$cm->pager;
                $paginas='<p align="center">'.$pages->links.'</p>';
                $div=print_search_related(0, $search);
            } else{
                $div="<h3>No hay noticias sugeridas</h3>";
                $paginas='No hay noticias que se relacionen con las palabras clave: '.$_REQUEST['metadata'];
            }

            Application::ajax_out($div.$paginas);

        break;

        case 'get_noticias':
            $cm = new ContentManager();
            if (!isset($_GET['category'])
                || empty($_GET['category'])
                || ($_GET['category'] == 'home')
                || ($_GET['category'] == 'todos')
                || ($_GET['category'] == ' ')
            ) {
                $category = 36;//Galicia
                $datos_cat = $ccm->find('pk_content_category=36', NULL);
            } else {
                $category = $_GET['category'];
                $datos_cat = $ccm->find('pk_content_category='.$category, NULL);
            }
            $categorys=print_menu($allcategorys,$subcat,$datos_cat[0],'noticias');

            list($articles, $pages)= $cm->find_pages('Article', 'fk_content_type=1 and content_status=1 AND available=1 ', 'ORDER BY  created DESC,  contents.title ASC ',$_REQUEST['page'],20,$category);

            $params=$_REQUEST['id'].", 'noticias',$category";
            $paginacion=$cm->makePagesLinkjs($pages, ' get_div_contents', $params);
            $paginacionV='<p align="center">'.$paginacion .'</p>'	;

            $html_out = print_lists_related($_REQUEST['id'], $articles, 'noticias_div');
            Application::ajax_out("<h2>Publicadas</h2>".$categorys.$html_out.$paginacionV);

        break;

          case 'get_hemeroteca':
            $cm = new ContentManager();
            if (!isset($_GET['category'])
                || empty($_GET['category'])
                || ($_GET['category'] == 'home')
                || ($_GET['category'] == 'todos')
                || ($_GET['category'] == ' ')
            ) {
                $category = 36;//Galicia
                $datos_cat = $ccm->find('pk_content_category=36', NULL);
            } else {
                $category = $_GET['category'];
                $datos_cat = $ccm->find('pk_content_category='.$category, NULL);
            }

            $categorys=print_menu($allcategorys,$subcat,$datos_cat[0],'hemeroteca');

            list($articles, $pages)= $cm->find_pages('Article', 'fk_content_type=1 and content_status=0 AND available=1 ', 'ORDER BY  created DESC,  contents.title ASC ',$_REQUEST['page'],20,$category);

            $params=$_REQUEST['id'].", 'hemeroteca',$category";
            $paginacion=$cm->makePagesLinkjs($pages, ' get_div_contents', $params);
            $paginacionV='<p align="center">'.$paginacion .'</p>'	;
            $html_out = print_lists_related($_REQUEST['id'], $articles, 'hemeroteca_div');
            Application::ajax_out("<h2>Hemeroteca</h2>".$categorys.$html_out.$paginacionV);

            break;

         case 'get_pendientes':
            $cm = new ContentManager();
            if (!isset($_GET['category'])
                || empty($_GET['category'])
                || ($_GET['category'] == 'home')
                || ($_GET['category'] == 'todos')
                || ($_GET['category'] == ' ')
            ) {
                $category = 36;//Galicia
                $datos_cat = $ccm->find('pk_content_category=36', NULL);
            } else {
                $category = $_GET['category'];
                $datos_cat = $ccm->find('pk_content_category='.$category, NULL);
            }

            $categorys=print_menu($allcategorys,$subcat,$datos_cat[0],'pendientes');

            list($articles, $pages)= $cm->find_pages('Article', 'fk_content_type=1 and available=0', 'ORDER BY  created DESC,  contents.title ASC ',$_REQUEST['page'],20,$category);

            $params=$_REQUEST['id'].", 'pendientes',$category";
            $paginacion=$cm->makePagesLinkjs($pages, ' get_div_contents', $params);
            $paginacionV='<p align="center">'.$paginacion .'</p>'	;

            $html_out = print_lists_related($_REQUEST['id'], $articles, 'pendientes_div');
            Application::ajax_out("<h2>Noticias Pendientes:</h2>".$categorys.$html_out.$paginacionV);

        break;

        case 'reload_menu':
            $cm = new ContentManager();
            if (!isset($_GET['category'])
                || empty($_GET['category'])
                || ($_GET['category'] == 'home')
                || ($_GET['category'] == 'todos')
                || ($_GET['category'] == ' ')
            ) {
                $category = 36;//Galicia
                $datos_cat = $ccm->find('pk_content_category=36', NULL);
            } else {
                $category = $_GET['category'];
                $datos_cat = $ccm->find('pk_content_category='.$category, NULL);
            }
            $tpl->assign('category', $_GET['category']);
            $tpl->assign('home', '');
            $html_out=$tpl->fetch('menu_categorys.tpl');
            Application::ajax_out($html_out);

        break;

        case 'get_videos':
            $cm = new ContentManager();
            if (!isset($_GET['category'])
                || empty($_GET['category'])
                || ($_GET['category'] == 'home')
                || ($_GET['category'] == 'todos')
                || ($_GET['category'] == ' ')
            ) {
                $category = 36;//Galicia
                $datos_cat = $ccm->find('pk_content_category=36', NULL);
            } else {
                $category = $_GET['category'];
                $datos_cat = $ccm->find('pk_content_category='.$category, NULL);
            }

            $categorys=print_menu($allcategorys,$subcat,$datos_cat[0],'videos');

            list($videos, $pages)= $cm->find_pages('Video', 'available=1', 'ORDER BY  created DESC,  contents.title ASC ',$_REQUEST['page'],20, $category);

            $params=$_REQUEST['id'].", 'videos',0";
            $paginacion=$cm->makePagesLinkjs($pages, ' get_div_contents', $params);
            $paginacionV='<p align="center">'.$paginacion .'</p>'	;

            $html_out = print_lists_related($_REQUEST['id'], $videos, 'videos_div');
            Application::ajax_out("<h2>Videos: </h2>".$categorys.$html_out.$paginacionV);

        break;

        case 'get_albums':
            $cm = new ContentManager();
            if (!isset($_GET['category'])
                || empty($_GET['category'])
                || ($_GET['category'] == 'home')
                || ($_GET['category'] == 'todos')
                || ($_GET['category'] == ' ')
            ) {
                $category = 3;//Album
                $datos_cat = $ccm->find('pk_content_category=3', NULL);
            } else {
                $category = $_GET['category'];
                $datos_cat = $ccm->find('pk_content_category='.$category, NULL);
            }

           $categorys=print_menu($allcategorys,$subcat,$datos_cat[0],'albums');

           list($albums, $pages)= $cm->find_pages('Album', 'available=1  AND fk_content_type=7', 'ORDER BY  created DESC,  contents.title ASC ',$_REQUEST['page'],20,$category);

           $params=$_REQUEST['id'].", 'albums',".$category;
           $paginacion=$cm->makePagesLinkjs($pages, ' get_div_contents', $params);
           $paginacionV='<p align="center">'.$paginacion .'</p>'	;

           $html_out=print_lists_related($_REQUEST['id'], $albums, 'albums_div')	;
           Application::ajax_out("<h2>Galerias:</h2>".$categorys.$html_out.$paginacionV);

        break;
        case 'get_opinions':
            $cm = new ContentManager();
            $menu=print_menu_opinion($_GET['category']);
             list($opinions, $pages)= $cm->find_pages('Opinion', 'content_status=1  and available=1 and type_opinion='.$_GET['category'], 'ORDER BY  created DESC,  contents.title ASC ',$_REQUEST['page'],20);

            $params=$_REQUEST['id'].", 'opinions',".$_GET['category'];
            $paginacion=$cm->makePagesLinkjs($pages, ' get_div_contents', $params);
            $paginacionV='<p align="center">'.$paginacion .'</p>'	;
            $html_out=print_lists_related($_REQUEST['id'], $opinions, 'opinions_div')	;
            Application::ajax_out("<h2>Opiniones:</h2>".$menu.$html_out.$paginacionV);

        break;

        case 'get_adjuntos':
            $cm = new ContentManager();

            $cm = new ContentManager();
            if (!isset($_GET['category'])
                || empty($_GET['category'])
                || ($_GET['category'] == 'home')
                || ($_GET['category'] == 'todos')
                || ($_GET['category'] == ' ')
            ) {
                $category = 36;//Galicia
                $datos_cat = $ccm->find('pk_content_category=36', NULL);
            } else {
                $category = $_GET['category'];
                $datos_cat = $ccm->find('pk_content_category='.$category, NULL);
            }

            $categorys=print_menu($allcategorys,$subcat,$datos_cat[0],'adjuntos');

            list($attaches, $pages)= $cm->find_pages('Attachment', 'content_status=1  AND fk_content_type=3', 'ORDER BY  created DESC,  contents.title ASC ',$_REQUEST['page'],20,$category);

            $params=$_REQUEST['id'].", 'adjuntos',".$category;
            $paginacion=$cm->makePagesLinkjs($pages, ' get_div_contents', $params);
            $paginacionV='<p align="center">'.$paginacion .'</p>'	;

            $html_out=print_lists_related($_REQUEST['id'], $attaches, 'adjuntos_div');
            Application::ajax_out("<h2>Ficheros:</h2>".$categorys.$html_out.$paginacionV);
        break;

        case 'get_categorys_list':

             $allcategorys =$ccm->cache->renderCategoriesTree();
             $data=json_encode($allcategorys);
             header('Content-type: application/json');
             Application::ajax_out($data);


        break;

        case 'update_title':
            $filter = '`pk_content` = ' . $_REQUEST['id'];

            $_REQUEST['fk_user_last_editor']=$_SESSION['userid'];
             $content= new Content($_REQUEST['id']);
            $content_type = $GLOBALS['application']->conn->
            GetOne('SELECT name FROM `content_types` WHERE pk_content_type = "'. $content->content_type.'"');
            $_REQUEST['permalink'] = $content->put_permalink($content->id, $content_type, $_REQUEST['title'], $content->category) ;
            $fields = array('title','permalink','fk_user_last_editor');
            SqlHelper::bindAndUpdate('contents', $fields, $_REQUEST, $filter);
            Application::ajax_out('ok');
         break;

        case 'update_agency':
            $filter1 = '`pk_content` = ' . $_REQUEST['id'];
            $_REQUEST['fk_user_last_editor']=$_SESSION['userid'];
            $fields1 = array('fk_user_last_editor');
            SqlHelper::bindAndUpdate('contents', $fields1, $_REQUEST, $filter1);

            $filter = '`pk_article` = ' . $_REQUEST['id'];
            $fields = array('agency');
            SqlHelper::bindAndUpdate('articles', $fields, $_REQUEST, $filter);

            Application::ajax_out('ok');
        break;

        case 'update_category':
            $filter1 = '`pk_content` = ' . $_REQUEST['id'];
            $_REQUEST['fk_user_last_editor']=$_SESSION['userid'];
            $content= new Content($_REQUEST['id']);
            $content_type = $GLOBALS['application']->conn->
            GetOne('SELECT name FROM `content_types` WHERE pk_content_type = "'. $content->content_type.'"');
            $_REQUEST['permalink'] = $content->put_permalink($content->id, $content_type, $content->title, $_REQUEST['pk_fk_content_category']) ;
            $fields1 = array('fk_user_last_editor','permalink');
            SqlHelper::bindAndUpdate('contents', $fields1, $_REQUEST, $filter1);

            $filter2 = '`pk_fk_content` = ' . $_REQUEST['id'];
            $fields2 = array('pk_fk_content_category','catName');
            SqlHelper::bindAndUpdate('contents_categories', $fields2, $_REQUEST, $filter2);

            Application::ajax_out('ok');
        break;

        case 'clone': {
            $article = new Article();
            $clone   = $article->createClone($_REQUEST);

            /* Application::forward($_SERVER['SCRIPT_NAME'] . '?action=read&category=' . $clone->category .
                                 '&id=' . $clone->id); */
            $uri = $_SERVER['SCRIPT_NAME'] . '?action=read&category=' . $clone->category . '&id=' . $clone->id;
            Application::forward('index.php?go=' . urlencode($uri));
        } break;

        default: {
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
        } break;
    } //switch

} else {
    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
}
