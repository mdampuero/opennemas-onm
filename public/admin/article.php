<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
require_once '../bootstrap.php';
require_once './session_bootstrap.php';

// Register events
require_once('articles_events.php');

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
use Onm\Settings as s,
    Onm\Message as m;

require_once 'controllers/utils_content.php';

// Assign a content types for don't reinvent the wheel into template
$tpl->assign('content_types', array(1 => 'Noticia' , 7 => 'Galeria', 9 => 'Video', 4 => 'Opinion', 3 => 'Fichero'));

/**
 * Fetch request variables
*/
(!isset($_SESSION['desde'])) ? $_SESSION['desde'] = 'list_pendientes' : null ;
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
$tpl->assign('action', $_REQUEST['action']);

/**
 * Getting categories
*/
// Parse template.conf to assign
$ccm         = ContentCategoryManager::get_instance();
$tplFrontend = new Template(TEMPLATE_USER);
$section     = $ccm->get_name($_REQUEST['category']);
$section     = (empty($section))? 'home': $section;
$categoryID  = ($_REQUEST['category'] == 'home') ? 0 : $_REQUEST['category'];
list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu($categoryID);

$tpl->assign('subcat', $subcat);
$tpl->assign('allcategorys', $parentCategories);
$tpl->assign('datos_cat', $datos_cat);
$allcategorys = $parentCategories;


if (isset($_REQUEST['action']) ) {

    switch ($_REQUEST['action']) {

        case 'list':
            Application::forward('controllers/frontpagemanager/frontpagemanager.php?action=list&category='.$_REQUEST['category']);

            break;
        case 'list_pendientes':

            Acl::checkOrForward('ARTICLE_PENDINGS');

            //Comprobación si el usuario tiene acceso a esta categoria/seccion.
            if ($_REQUEST['category'] != 'todos') {
                 if(!Acl::_C( $_REQUEST['category'])) {
                      m::add(_("you don't have enought privileges to see this category.") );
                      Application::forward('/');
                 }
            } elseif (!Acl::_C($categoryID)) {
                $categoryID           = $_SESSION['accesscategories'][0];
                $section              = $ccm->get_name($categoryID);
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


            } elseif ($_REQUEST['category'] == 'opinion'){
                $opinions = $cm->find('Opinion', 'fk_content_type=4 AND available=0',
                                      'ORDER BY created DESC, type_opinion DESC, title ASC');
                $tpl->assign('opinions', $opinions);
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
            } else {
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
                      Application::forward($_SERVER['SCRIPT_NAME'].'?action=list_pendientes');
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
            } elseif ($_REQUEST['category'] == 'opinion'){
                $opinions = $cm->find('Opinion',
                    'fk_content_type=4 AND available=0 ',
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
            } else {
                list($articles, $pager)= $cm->find_pages('Article',
                    'available=0 AND fk_content_type=1 ',
                    'ORDER BY  created DESC, title ASC ',
                    $_REQUEST['page'], 10, $_REQUEST['category']);
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
            list($articles, $pager)= $cm->find_pages('Article',
                'content_status=0 AND available=1 AND fk_content_type=1 ',
                'ORDER BY changed DESC, created DESC, title ASC ',
                $_REQUEST['page'], 10, $_REQUEST['category']);
            $aut=new User();
            $comment = new Comment();
            foreach ($articles as $art){
                $art->category_name = $art->loadCategoryName($art->id);
                $art->publisher =$aut->get_user_name($art->fk_publisher);
                $art->editor    =$aut->get_user_name($art->fk_user_last_editor);
                $art->rating    = $rating->getValue($art->id);
                $art->comment   = $comment->count_public_comments( $art->id );
            }
            $tpl->assign('articles', $articles);
            $tpl->assign('paginacion', $pager);
            $tpl->assign('category', $_REQUEST['category']);
            $_SESSION['desde']='list_hemeroteca';

            $tpl->display('article/library.tpl');

            break;

        case 'new':   // Crear un nuevo artículo

            Acl::checkOrForward('ARTICLE_CREATE');

            if (!isset($_REQUEST['category'])
                || $_REQUEST['category']==''
                || $_REQUEST['category']=='home'
            ) {
                $_REQUEST['category']=$_SESSION['_from'];
            }
            $tpl->assign('category', $_REQUEST['category']);
            $cm = new ContentManager();
            //FIXME: cambiar por la llamada a vars php en smarty
            $tpl->assign('MEDIA_IMG_PATH_WEB', MEDIA_IMG_PATH_WEB);


            $tpl->assign(array(
                'availableSizes'=>array(16,18,20,22,24,26,28,30,32,34)
            ));
            $tpl->assign(array(
                'availableSizes'=> array(
                    16 => '16',
                    18 => '18',
                    20 => '20',
                    22 => '22',
                    24 => '24',
                    26 => '26',
                    28 => '28',
                    30 => '30',
                    32 => '32',
                    34 => '34'
                )
            ));


            //TODO: AJAX
            // require_once('controllers/video/videoGallery.php');
            $tpl->display('article/new.tpl');

            break;

        case 'read':
        case 'only_read':
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
                    (array_key_exists('imageHome', $article->params)) &&
                    !empty($article->params['imageHome']) ) {
                $photoHome= new Photo($article->params['imageHome']);
                $tpl->assign('photo3', $photoHome);
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

            $relationsHandler= new RelatedContent();

            $orderFront = array();
            $relations = $relationsHandler->getRelations( $_REQUEST['id'] );//de portada
            foreach($relations as $aret) {
                $orderFront[] =  new Content($aret);
            }
            $tpl->assign('orderFront', $orderFront);

            $orderInner = array();
            $relations = $relationsHandler->getRelationsForInner($_REQUEST['id']);//de interor
            foreach($relations as $aret) {
                $orderInner[] = new Content($aret);
            }
            $tpl->assign('orderInner', $orderInner);

            if(\Onm\Module\ModuleManager::isActivated('AVANCED_ARTICLE_MANAGER') && is_array($article->params)) {
                $galleries = array();
                $galleries['home'] = (array_key_exists('withGalleryHome',$article->params))? new Album($article->params['withGalleryHome']): null;
                $galleries['front'] = (array_key_exists('withGalleryHome',$article->params))? new Album($article->params['withGallery']): null;
                $galleries['inner'] = (array_key_exists('withGalleryHome',$article->params))? new Album($article->params['withGalleryInt']): null;
                $tpl->assign('galleries', $galleries);

                $orderHome = array();
                $relations = $relationsHandler->getHomeRelations( $_REQUEST['id'] );//de portada
                if (!empty($relations)) {
                    foreach($relations as $aret) {
                        $orderHome[] = new Content($aret);
                    }
                    $tpl->assign('orderHome', $orderHome);
                }

            }

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
                array('availableSizes'=>array(16=>'16',18=>'18',20=>'20',22=>'22',24=>'24',26=>'26',
                                            28=>'28',30=>'30',32=>'32',34=>'34'))
            );

            $tpl->display('article/new.tpl');
            // }}}
            break;

        case 'create':

            Acl::checkOrForward('ARTICLE_CREATE');

            if (isset($_POST['with_comment'])) {$_POST['with_comment'] = 1;} else {$_POST['with_comment'] = 0;}
      //      if (isset($_POST['frontpage'])) {$_POST['frontpage'] = 1;} else {$_POST['frontpage'] = 0;}
      //      if (isset($_POST['in_home'])) {$_POST['in_home'] = 2;} else {$_POST['in_home'] = 0;}
            if (isset($_POST['content_status'])) {$_POST['content_status'] = 1;} else {$_POST['content_status'] = 0;}

            $article = new Article();
            $_POST['fk_publisher']=$_SESSION['userid'];

            if ($article->create( $_POST )) {
                if ($_SESSION['desde'] == 'index_portada') {
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
                if (isset($_POST['with_comment'])) {
                    $_POST['with_comment'] = 1;
                } else {
                    $_POST['with_comment'] = 0;
                }
                if (isset($_POST['frontpage'])) {
                    $_POST['frontpage'] = 1;
                } else {
                    $_POST['frontpage'] = 0;
                }
                if (isset($_POST['in_home'])) {$_POST['in_home'] = 2;}
            }
            if (isset($_POST['content_status'])) {
                $_POST['content_status'] = 1;
            } else {
                $_POST['content_status'] = 0;
            }

            // Register cache control event for updating content
            $GLOBALS['application']->register('onAfterUpdate', 'onAfterUpdate_refreshCache');
            $GLOBALS['application']->register('onAfterUpdate', 'onAfterUpdate_saluda');

            $articleCheck = new Article();
            $articleCheck->read($_REQUEST['id']);

            if (!Acl::isAdmin()
                && !Acl::check('CONTENT_OTHER_UPDATE')
                && $articleCheck->fk_user != $_SESSION['userid']
            ) {
                m::add(_("You can't modify this content because you don't have enought privileges.") );
                Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$_REQUEST['id']);
            } else {
                $article = new Article();
                $_REQUEST['fk_user_last_editor'] = $_SESSION['userid'];
                $data = $_REQUEST;
                unset($data['action']);
                unset($data['stringVideoSearch']);
                unset($data['stringImageSearch']);
                unset($data['stringSearch']);

                $article->update($data);
                if (!array_key_exists('content_status', $data)) {
                    $article->dropFromAllHomePages();
                }
            }

            if ($_SESSION['desde'] =='search_advanced'){
                if(isset($_GET['stringSearch'])){
                 Application::forward('controllers/search_advanced/search_advanced.php?action=search&stringSearch='.$_GET['stringSearch'].'&category='.$_SESSION['_from'].'&page='.$_REQUEST['page']);
                }else{
                    $_SESSION['desde']='list_pendientes';
                    $_SESSION['_from']='home';
                }
            }

            if ($_SESSION['desde']=='index_portada') {
                Application::forward('index.php');
            }elseif ($_SESSION['desde'] == 'europa_press_import') {
                Application::forward('controllers/agency_importer/europapress.php?action=list&page=0&message=');
            }elseif ($_SESSION['desde'] == 'efe_press_import') {
                Application::forward('controllers/agency_importer/efe.php');
            }elseif ($_SESSION['desde'] == 'list') {
               // Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&category='.$_SESSION['_from'].'&page='.$_REQUEST['page']);
                 Application::forward('controllers/frontpagemanager/frontpagemanager.php?action='.$_SESSION['desde'].'&category='.$_SESSION['_from'].'&page='.$_REQUEST['page']);
            }elseif ($_SESSION['desde'] == 'list_hemeroteca') {
                Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
            }
            if(isset($_REQUEST['available']) && $_REQUEST['available'] == 1){
                 $_SESSION['desde']='list_pendientes';
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
                    $article->update($_POST);
                    if ($_POST['content_status'] == 0) {
                        $article->dropFromAllHomePages();
                    }
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
            Application::ajaxOut($article->id);
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
                    $rel= new RelatedContent();
                    $relationes=array();

                    $relationes = $rel->getContentRelations($_REQUEST['id']);//de portada
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
                $rel= new RelatedContent();
                $rel->deleteAll($_REQUEST['id']);
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
            $status = ($_REQUEST['status']==1) ? 1: 0; // Evitar otros valores
            if ($status==1){
                    //Recuperar de hemeroteca
                   //$article->set_available(0,$_SESSION['userid']);
                   //  $_position=array(100,'placeholder_0_1',$_REQUEST['id']);
                    // $article->set_inhome(0,$_SESSION['userid']);
                    $article->set_frontpage(0, $_SESSION['userid']);
                    // $article->set_position($_position,$_SESSION['userid']);
            } else {
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
                }else{
                    Application::forward('controllers/frontpagemanager/frontpagemanager.php?action=list&category='.$_REQUEST['category']);
                }

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
            Application::forward('controllers/frontpagemanager/frontpagemanager.php?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
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

           Application::forward('controllers/frontpagemanager/frontpagemanager.php?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);

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

                     foreach($fields as $i ) {
                        $content = new Content($i);
                        $rel= new RelatedContent();
                        $relationes=array();

                        $relationes = $rel->getContentRelations( $i );//de portada

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
                       $rel= new RelatedContent();
                        $relationes=array();

                        $relationes = $rel->getContentRelations($i );//de portada

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

        case 'get_categorys_list':

             $allcategorys =$ccm->cache->renderCategoriesTree();
             $data=json_encode($allcategorys);
             header('Content-type: application/json');
             Application::ajaxOut($data);


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
            Application::ajaxOut('ok');
         break;

        case 'update_agency':
            $filter1 = '`pk_content` = ' . $_REQUEST['id'];
            $_REQUEST['fk_user_last_editor']=$_SESSION['userid'];
            $fields1 = array('fk_user_last_editor');
            SqlHelper::bindAndUpdate('contents', $fields1, $_REQUEST, $filter1);

            $filter = '`pk_article` = ' . $_REQUEST['id'];
            $fields = array('agency');
            SqlHelper::bindAndUpdate('articles', $fields, $_REQUEST, $filter);

            Application::ajaxOut('ok');
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

            Application::ajaxOut('ok');
        break;

        case 'clone': {
            $article = new Article();
            $clone   = $article->createClone($_REQUEST);

            /* Application::forward($_SERVER['SCRIPT_NAME'] . '?action=read&category=' . $clone->category .
                                 '&id=' . $clone->id); */
            $uri = $_SERVER['SCRIPT_NAME'] . '?action=read&category=' . $clone->category . '&id=' . $clone->id;
            Application::forward('index.php?go=' . urlencode($uri));
        } break;

        case 'content-provider-suggested':

            $category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_STRING,   array('options' => array( 'default' => 'home')));
            $page     = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT,   array('options' => array( 'default' => 1)));

            if ($category == 'home') { $category = 0; }

            $cm = new  ContentManager();

            // Get contents for this home
            $contentElementsInFrontpage  = $cm->getContentsIdsForHomepageOfCategory($category);

            // Fetching opinions
            $sqlExcludedOpinions = '';
            if (count($contentElementsInFrontpage) > 0) {
                $contentsExcluded = implode(', ', $contentElementsInFrontpage);
                $sqlExcludedOpinions = ' AND `pk_article` NOT IN ('.$contentsExcluded.')';
            }

            list($articles, $pager) = $cm->find_pages(
                'Article',
                'contents.available=1 AND in_litter != 1 AND frontpage=1'. $sqlExcludedOpinions,
                ' ORDER BY created DESC ', $page, 5
            );

            $tpl->assign(array(
                'articles' => $articles,
                'pager'   => $pager,
            ));

            $tpl->display('article/content-provider-suggested.tpl');

            break;

        case 'content-provider-category':

            $category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_STRING,   array('options' => array( 'default' => 'home')));
            $page     = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT,   array('options' => array( 'default' => 1)));

            if ($category == 'home') { $category = 0; }

            $cm = new  ContentManager();

            // Get contents for this home
            $contentElementsInFrontpage  = $cm->getContentsIdsForHomepageOfCategory($category);

            // Fetching opinions
            $sqlExcludedOpinions = '';
            if (count($contentElementsInFrontpage) > 0) {
                $contentsExcluded = implode(', ', $contentElementsInFrontpage);
                $sqlExcludedOpinions = ' AND `pk_article` NOT IN ('.$contentsExcluded.')';
            }

            list($articles, $pager) = $cm->find_pages(
                'Article',
                'contents.available=1 '. $sqlExcludedOpinions, 'ORDER BY created DESC ', $page, 10, $category
            );

            $tpl->assign(array(
                'articles' => $articles,
                'pager'    => $pager,
            ));

            $tpl->display('article/content-provider-category.tpl');

            break;

		case 'content-list-provider':

        case 'related-provider-category':

            $items_page = s::get('items_per_page') ?: 20;
            $category   = filter_input( INPUT_GET, 'category' , FILTER_SANITIZE_STRING, array('options' => array('default' => '0')) );
            $page       = filter_input( INPUT_GET, 'page' , FILTER_SANITIZE_NUMBER_INT, array('options' => array('default' => '1')) );
            $cm = new ContentManager();

            list($articles, $pages) = $cm->find_pages('Article',
                        'fk_content_type=1 and  available=1 ',
                        'ORDER BY frontpage DESC, starttime DESC,  contents.title ASC ',
                        $page, $items_page, $category);

            $tpl->assign(array(
                'contents'=>$articles,
                'contentTypeCategories'=>$allcategorys,
                'category' =>$category,
                'contentType'=>'Article',
                'pagination'=>$pages->links
            ));

            $htmlOut = $tpl->fetch("common/content_provider/_container-content-list.tpl");
            Application::ajaxOut($htmlOut);

            break;

        case 'provider-frontpage':

            $items_page = s::get('items_per_page') ?: 20;
            $category   = filter_input( INPUT_GET, 'category' , FILTER_SANITIZE_STRING, array('options' => array('default' => '0')) );
            $page       = filter_input( INPUT_GET, 'page' , FILTER_SANITIZE_NUMBER_INT, array('options' => array('default' => '1')) );
            $cm = new ContentManager();
            $categoryID = (empty($category) || $category == 'home') ? '0' : $category;
            $placeholder = ($categoryID == 0) ? 'home_placeholder': 'placeholder';
            // Get contents for this home
            $contentElementsInFrontpage  = $cm->getContentsForHomepageOfCategory($categoryID);

            // Sort all the elements by its position
            $contentElementsInFrontpage  = $cm->sortArrayofObjectsByProperty($contentElementsInFrontpage, 'position');

            $articles = array();
            foreach($contentElementsInFrontpage as $content) {
                if($content->content_type =='1') {
                    $articles[] = $content;
                }
            }

            $home = new StdClass();
               $home->pk_content_category = '0';
               $home->title = 'Home';
               $home->name ='home';

            array_unshift($allcategorys, $home);

            $tpl->assign(array(
                'contents'              => $articles,
                'contentTypeCategories' => $allcategorys,
                'category'              => $category,
                'contentType'           => 'Article',
                'action'                => 'provider-frontpage',
            ));

            $htmlOut = $tpl->fetch("common/content_provider/_container-content-list.tpl");
            Application::ajaxOut($htmlOut);

            break;


        case 'related-provider-suggest':

            $items_page = s::get('items_per_page') ?: 20;
            $metadata   = filter_input( INPUT_GET, 'metadata' , FILTER_SANITIZE_STRING, array('options' => array('default' => '0')) );
            $page       = filter_input( INPUT_GET, 'page' , FILTER_SANITIZE_NUMBER_INT, array('options' => array('default' => '1')) );
            $cm = new ContentManager();

            $mySearch = cSearch::getInstance();
            $where = "content_status=1 AND available=1 ";
            $search = $mySearch->searchRelatedContents($metadata, 'Article,Opinion', NULL, $where);
            if(($search) && count($search)>0){
                var_dump($search);
            }
            $tpl->assign(array(
                'contents'=>$articles,
                'contentTypeCategories'=>$allcategorys,
                'category' =>$category,
                'pagination'=>$pages->links
            ));

            $htmlOut = $tpl->fetch("common/content_provider/_container-content-list.tpl");
            Application::ajaxOut($htmlOut);

            break;

        default: {
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list_pendientes&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
        } break;
    } //switch

} else {
    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list_pendientes&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
}
