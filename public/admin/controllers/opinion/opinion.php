<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Onm\Settings as s,
    Onm\Message as m;
/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');

//Check if module is activated in this onm instance
\Onm\Module\ModuleManager::checkActivatedOrForward('OPINION_MANAGER');

/**
 * Check privileges
*/
Acl::checkOrForward('OPINION_ADMIN');

/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

// FIXME: usar en template {$smarty.const.MEDIA_IMG_PATH_URL}
$tpl->assign('MEDIA_IMG_PATH_URL', MEDIA_IMG_PATH_WEB);

// Register events
require_once('./opinion_events.php');

if (!isset($_SESSION['desde'])) {
    $_SESSION['desde'] = 'opinion';
}

$page = filter_input(INPUT_GET,'page',FILTER_VALIDATE_INT, array('options' => array('default' => '1')) );

if (!isset($_SESSION['type'])) {
     $_SESSION['type'] = 0;
}
$type_opinion = filter_input(INPUT_GET,'type_opinion',FILTER_VALIDATE_INT );

if (!isset($type_opinion)) {
    $type_opinion = filter_input(INPUT_POST,'type_opinion',FILTER_VALIDATE_INT, array('options' => array('default' => '-1')) );
}

$c = new Content();
$cm = new ContentManager();

$tpl->assign('type_opinion', $type_opinion);

$action = filter_input( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
if (!isset($action)) {
    $action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}


switch ($action) {
    case 'list':

        $configurations = s::get('opinion_settings');

        $numEditorial = $configurations['total_editorial'];
        $numDirector = $configurations['total_director'];

        $cm = new ContentManager();
        $rating = new Rating();
        $comment = new Comment();

        if($type_opinion != -1) {
            //Para visualizar la HOME
            // ContentManager::find_pages(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>,<PAGE>,<ITEMS_PER_PAGE>,<CATEGORY>);
            list($opinions, $pager)= $cm->find_pages('Opinion', "type_opinion='".$type_opinion."'",
                                                         'ORDER BY created DESC ', $page, ITEMS_PAGE);


            $tpl->assign('paginacion', $pager->links);

            $number = 2;

            $opinion=new Opinion();
            $total = $opinion->count_inhome_type($type_opinion);
            $alert="";

            if (($numEditorial>0) && ($type_opinion == 1) && ($total != $numEditorial)) {
                $type = 'editorial';
                $number = $numEditorial;
            } elseif (($numDirector>0) && ($type_opinion == 2) && ($total != $numDirector)) {
                 $type = 'opinion del director';
                 $number = $numDirector;
            }
            if (!empty($type)) {
                m::add( sprintf(_("You must put %d opinions %s in the home widget"), $number, $type) );
            }
        } else {
            list($opinions, $pager)= $cm->find_pages('Opinion', 'in_home=1 and available=1 and type_opinion=0',
                                     'ORDER BY position ASC, created DESC ', $page, ITEMS_PAGE);
            $tpl->assign('paginacion', $pager->links);

            // $opinions = $cm->find('Opinion', 'in_home=1 and available=1 and type_opinion=0',
            //                       'ORDER BY type_opinion DESC, position ASC, created DESC');

            if($numEditorial > 0) {
                $editorial = $cm->find('Opinion', 'in_home=1 and available=1 and type_opinion=1',
                                   'ORDER BY position ASC, created DESC LIMIT 0,'.$numEditorial);
            }
            if($numDirector >0) {
                $director = $cm->find('Opinion', 'in_home=1 and available=1 and type_opinion=2',
                                  'ORDER BY created DESC LIMIT 0,'.$numDirector);
            }
            if (($numEditorial>0) && (count($editorial) != $numEditorial)) {
                $type = 'editorial';
                m::add( sprintf(_("You must put %d opinions %s in the home widget"), $numEditorial, $type) );
            }
            if (($numDirector>0) && (count($director) != $numDirector)) {
                 $type = 'opinion del director';
                 m::add( sprintf(_("You must put %d opinions %s in the home widget"), $numDirector, $type) );
            }

            if (!empty($editorial)) {
                foreach($editorial as $opin) {
                    $todos = $comment->get_comments( $opin->id );
                    $opin->comments = count($todos);
                    $opin->ratings = $rating->getValue($opin->id);
                }
                $tpl->assign('editorial', $editorial);
            }

            if (!empty($director)) {
                foreach($director as $opin) {
                    $todos = $comment->get_comments( $opin->id );
                    $opin->comments = count($todos);
                    $opin->ratings = $rating->getValue($opin->id);
                }
                $tpl->assign('director', $director);
            }
        }

        $tpl->assign('type_opinion', $type_opinion);

        $op_comment = $names = $op_ratings = array();

        if (!empty($opinions)) {
            foreach ( $opinions as $opin) {
                $todos = $comment->get_comments( $opin->id );
                $aut = new Author($opin->fk_author);
                $names[] = $aut->name;
                $op_comment[] = count($todos);
                $op_ratings[] = $rating->getValue($opin->id);
            }
        }

        $tpl->assign('op_comment', $op_comment);
        $tpl->assign('names', $names);
        $tpl->assign('op_rating', $op_ratings);

        $aut = new Author();
        $autores = $aut->all_authors(NULL,'ORDER BY name');
        $tpl->assign('autores', $autores);

        $tpl->assign('opinions', $opinions);

        $_SESSION['type'] = $type_opinion;

        $_SESSION['desde'] = 'opinion';
        $_SESSION['_from'] = 'opinion.php';

        $tpl->display('opinion/list.tpl');
    break;

    case 'change_list_byauthor':
        $cm = new ContentManager();
        if($_REQUEST['author'] == 0) {
            $_REQUEST['action'] = 'list'; //Para que sea correcta la paginacion.

            list($opinions, $pager)= $cm->find_pages('Opinion', 'type_opinion=0', 'ORDER BY  created DESC ',
                                                     $page, ITEMS_PAGE);
        } else {
            // $opinions = $cm->find('Opinion', 'opinions.fk_author=\''.$_REQUEST['author'].'\' and type_opinion=0',
            //                                  'ORDER BY created DESC LIMIT 0,20');
            list($opinions, $pager)= $cm->find_pages('Opinion', 'opinions.fk_author="'.$_REQUEST['author'].'" AND type_opinion=0',
                                                     'ORDER BY  created DESC ', $page, ITEMS_PAGE);

            $params = $_REQUEST['author'];

            if($pager->_totalItems>ITEMS_PAGE){
               $pager = $cm->create_paginate($pager->_totalItems, ITEMS_PAGE, 4, 'changepageList', $params);
                $tpl->assign('paginacion', $pager->links);
            }
        }


        $tpl->assign('opinions', $opinions);
        $tpl->assign('type_opinion', 0);

        $op_comment = array();
        $comment = new Comment();
        $names = array();
        foreach($opinions as $opin) {
            $todos = $comment->get_comments( $opin->id );
            $aut = new Author($opin->fk_author);
            $names[] = $aut->name;
            $op_comment[] = count($todos);
        }


        $tpl->assign('op_comment', $op_comment);
        $tpl->assign('names', $names);

        $tpl->assign('author', $_REQUEST['author']);
        $aut = new Author();
        $autores = $aut->all_authors(NULL,'ORDER BY name');
        $tpl->assign('autores', $autores);

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])  && ($_SERVER['HTTP_X_REQUESTED_WITH']=="XMLHttpRequest")){
            $tpl->display('opinion/partials/_opinion_list.tpl');
            exit(0);
        }

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&type_opinion=0&page='.$page);


    break;

    case 'new':
        Acl::checkOrForward('OPINION_CREATE');
        // Select de autores.
        $aut = new Author();
        $todos = $aut->all_authors(NULL,'ORDER BY name');
        $tpl->assign('todos', $todos);

        $opinion = new Opinion();
        $opinion->available = 1;
        $opinion->in_home   = 1;
        $opinion->with_comment = 1;
        $_REQUEST['category'] = 'opinion';
        $tpl->assign('todos', $todos);
        $tpl->assign('opinion', $opinion);
        $_SESSION['desde'] = 'new';
        $_SESSION['_from'] = 'opinion.php';
        $tpl->display('opinion/new.tpl');
    break;

    case 'read':
        Acl::checkOrForward('OPINION_UPDATE');
        //habrá que tener en cuenta el tipo
        $opinion = new Opinion($_REQUEST['id']);
        $tpl->assign('opinion', $opinion);
        if(isset($_REQUEST['category'])) {
            $_SESSION['categoria'] = $_REQUEST['category'];
        }
        $aut = new Author();
        $todos = $aut->all_authors(NULL,'ORDER BY name');
        $tpl->assign('todos', $todos);

        $aut = new Author($opinion->fk_author);
        $tpl->assign('author', $aut->name);

        $foto = $aut->get_photo($opinion->fk_author_img);
        $tpl->assign('foto', $foto);

        $fotowidget = $aut->get_photo($opinion->fk_author_img_widget);
        $tpl->assign('fotowidget', $fotowidget);

        $photos = $aut->get_author_photos($opinion->fk_author);
        $tpl->assign('photos', $photos);

        $tpl->display('opinion/new.tpl');
    break;

    case 'create':
        Acl::checkOrForward('OPINION_CREATE');
        $opin = new Opinion();
        $_POST['publisher'] = $_SESSION['userid'];

        if ($opin->create($_POST)) {
            // FIXME: buscar otra forma de hacerlo
            /* Eliminar caché opinion cuando se crean nuevas opiniones */

            $opinion = new Opinion($opin->id);
            $total = $opinion->count_inhome_type();

            $configurations = s::get('opinion_settings');
            $numEditorial = $configurations['total_editorial'];
            $numDirector = $configurations['total_director'];

            if (($opinion->type_opinion == 1) && ($total != $numEditorial)) {
                if($numEditorial > 0) {
                    $type = 'editorial';
                    m::add( sprintf(_("You must put %d opinions %s in the home widget"), $numEditorial, $type) );
                }
            } elseif (($opinion->type_opinion == 2) && ($total != $numDirector)) {
                if($numDirector > 0) {
                    $type = 'opinion del director';
                    m::add( sprintf(_("You must put %d opinions %s in the home widget"), $numDirector, $type) );
                }
            }

            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&type_opinion=' .
                                 $_SESSION['type'] .  '&page=' . $page);
        } else {
            $tpl->assign('errors', $opinion->errors);
        }
        $tpl->display('opinion/new.tpl');

    break;

    case 'update':
        Acl::checkOrForward('OPINION_UPDATE');
        //TODO : Revisar esto porque antes saltaba un warning
        $alert= '';

        $opinionCheck = new Opinion();
        $opinionCheck->read($_REQUEST['id']);

        if(!Acl::isAdmin() &&
                !Acl::check('CONTENT_OTHER_UPDATE') &&
                $opinionCheck->fk_user != $_SESSION['userid']) {
            $msg ="Only read";
        }
        if(!Acl::isAdmin() && !Acl::check('CONTENT_OTHER_UPDATE') && $opinionCheck->fk_user != $_SESSION['userid']) {
            m::add(_("You can't modify this opinion because you don't have enought privileges.") );
        }

        $_REQUEST['fk_user_last_editor'] = $_SESSION['userid'];

        $opinion = new Opinion();
        $opinion->update($_REQUEST);

        $tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);
        $tplManager->delete('opinion|1');

        if($_SESSION['desde'] == 'search_advanced') {
            if(isset($_GET['stringSearch'])){
                Application::forward('/admin/controllers/search_advanced/search_advanced.php?'.
                    'action=search&'.
                    'stringSearch='.$_GET['stringSearch'].'&'.
                    'category='.$_SESSION['_from'].'&'.
                    'page=' . $page);
            } else {
                $_SESSION['desde'] = 'list';
                $_SESSION['type'] = $type_opinion;
            }
        }

        if($_SESSION['desde'] == 'index_portada') {
            Application::forward('index.php');
        }elseif( $_SESSION['desde']=='list_pendientes'){
            Application::forward('/admin/article.php?action='.$_SESSION['desde'].'&category='.$_SESSION['categoria'].'&page='.$page);
        }elseif ($_SESSION['desde'] == 'list') {
            Application::forward('/admin/article.php?action='.$_SESSION['desde'].'&category='.$_SESSION['categoria'].'&page='.$page);
        }else{
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&type_opinion=' .
                             $_SESSION['type'] . '&alert=' . $alert . '&page=' . $page);
        }
    break;

    case 'validate':
        $opinion = null;
        if(empty($_POST["id"])) {
            Acl::checkOrForward('OPINION_CREATE');
            $opinion = new Opinion();
            $_POST['publisher'] = $_SESSION['userid'];

            if($opinion->create( $_POST )) {
                $tpl->assign('errors', $opinion->errors);
            }
        } else {
            Acl::checkOrForward('OPINION_UPDATE');
            $opinionCheck = new Opinion();
            $opinionCheck->read($_REQUEST['id']);

            if( !Acl::isAdmin() &&
                    !Acl::check('CONTENT_OTHER_UPDATE') &&
                    $opinionCheck->fk_user_last_editor != $_SESSION['userid']) {
                m::add(_("You can't modify this opinion because you don't have enought privileges.") );
            }

            $_REQUEST['fk_user_last_editor'] = $_SESSION['userid'];

            $opinion = new Opinion();
            $opinion->update($_REQUEST);
        }

        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=read&type_opinion=' .
                             $_SESSION['type'] . '&id=' . $opinion->id);
    break;

    case 'getRelations':

        $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);

        $relations=array();
        $msg ='';
        $relations = RelatedContent::getContentRelations($id);

        if (!empty($relations)) {
            $msg = sprintf(_("<br>The album has some relations"));
            $cm = new ContentManager();
            $relat = $cm->getContents($relations);
            foreach($relat as $contents) {
                $msg.=" <br>- ".strtoupper($contents->category_name).": ".$contents->title;
            }
            $msg.="<br> "._("Caution! Are you sure that you want to delete this opinion and its relations?");

            echo $msg;
        }

        exit(0);
        break;

    case 'delete':
        Acl::checkOrForward('OPINION_DELETE');
        $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
        $opinionCheck = new Opinion();
        $opinionCheck->read($id);

        if( !Acl::isAdmin() &&
                !Acl::check('CONTENT_OTHER_UPDATE') &&
                $opinionCheck->fk_user_last_editor != $_SESSION['userid']) {
            m::add(_("You can't delete this opinion because you don't have enought privileges.") );
        }

        if($id) {
            //Delete relations
            $rel= new RelatedContent();
            $rel->deleteAll($id);

            $opinion = new Opinion($id);
            $opinion->delete($id, $_SESSION['userid']);

            $tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);
            $tplManager->delete('opinion|1');
        }

        if( $_SESSION['desde']=='list_pendientes'){
            Application::forward(SITE_URL_ADMIN.'/article.php?action='.$_SESSION['desde'].'&category='.$_REQUEST['category'].'&page='.$page);
        }else{
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&type_opinion=' .
                             $_SESSION['type'] . '&page=' . $page);
        }
    break;

    case 'change_status':
        Acl::checkOrForward('OPINION_FRONTPAGE');
        $opinion = new Opinion($_REQUEST['id']);

        //Publicar o no,
        $status = ($_REQUEST['status'] == 1)? 1: 0; // Evitar otros valores
        // $opinion->set_status($status, $_SESSION['userid']);

        //Se hace en set_available
        $opinion->set_available($status, $_SESSION['userid']);

        if($status == 0) {
            $opinion->set_inhome($status,$_SESSION['userid']);
        }
        if( $_SESSION['desde']=='list_pendientes'){
            Application::forward(SITE_URL_ADMIN.'/article.php?action='.$_SESSION['desde'].'&category='.$_REQUEST['category'].'&page='.$page);
        }else{
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&type_opinion=' .
                             $_SESSION['type'] . '&page=' . $page);
        }
    break;

     case 'inhome_status':
        Acl::checkOrForward('OPINION_HOME');
        $opinion = new Opinion($_REQUEST['id']);
        $alert='';
        // FIXME: evitar otros valores erróneos
        $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
        $total = $opinion->count_inhome_type();
        if (($status == 1) && ($opinion->type_opinion != 1) && ($opinion->type_opinion != 2)) {
                $total++;
                $opinion->set_inhome($status,$_SESSION['userid']);
                $opinion->set_status($status, $_SESSION['userid']);
                $opinion->set_available($status, $_SESSION['userid']);
        } else {
            $opinion->set_inhome($status, $_SESSION['userid']);
            $total--;
        }
        $configurations = s::get('opinion_settings');
        $numEditorial = $configurations['total_editorial'];
        $numDirector = $configurations['total_director'];

        if (($opinion->type_opinion == 1) && ($total != $numEditorial)) {
            if($numEditorial > 0) {
                $type = 'editorial';
                m::add( sprintf(_("You must put %d opinions %s in the home widget"), $numEditorial, $type) );
            }
        } elseif (($opinion->type_opinion == 2) && ($total != $numDirector)) {
            if($numDirector > 0) {
                $type = 'opinion del director';
                m::add( sprintf(_("You must put %d opinions %s in the home widget"), $numDirector, $type) );
            }
        }

        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&type_opinion=' .
                             $_SESSION['type'] . '&alert=' . $alert .
                             '&page=' . $page);
    break;

    case 'batchFrontpage':

        Acl::checkOrForward('OPINION_AVAILABLE');

        if(isset($_POST['selected_fld']) && count($_POST['selected_fld']) > 0) {
            $fields = $_POST['selected_fld'];

            $status = filter_input ( INPUT_POST, 'status' , FILTER_SANITIZE_NUMBER_INT );
            if (is_array($fields)) {
                foreach ($fields as $i) {
                    $opinion = new Opinion($i);
                    $opinion->set_available($status, $_SESSION['userid']);
                    if ($status == 0) {
                        $opinion->set_favorite($status);
                    }
                }
            }
        }
        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&type_opinion=' .
                             $_SESSION['type'] . '&page=' . $page);
    break;

    case 'batchInHome':
        Acl::checkOrForward('OPINION_HOME');

        if(isset($_POST['selected_fld']) && count($_POST['selected_fld']) > 0) {
            $fields = $_POST['selected_fld'];

            $status = filter_input ( INPUT_POST, 'status' , FILTER_SANITIZE_NUMBER_INT );
            $alert = '';

            if(is_array($fields)) {
                foreach($fields as $i) {
                    $opinion = new Opinion($i);
                    // FIXME: evitar otros valores erróneos
                    if (($status == 1) && ($opinion->type_opinion != 1)  && ($opinion->type_opinion != 2))  {
                            $opinion->set_inhome($status, $_SESSION['userid']);
                            $opinion->set_status($status, $_SESSION['userid']);
                            $opinion->set_available($status, $_SESSION['userid']);
                    } else {
                        $opinion->set_inhome($status, $_SESSION['userid']);
                    }
                }
                $configurations = s::get('opinion_settings');
                $numEditorial = $configurations['total_editorial'];
                $numDirector = $configurations['total_director'];
                if($numEditorial > 0) {
                    $total = $opinion->count_inhome_type(1);
                    if ($total != $numEditorial) {
                        $type = 'editorial';
                        m::add( sprintf(_("You must put %d opinions %s in the home widget"), $numEditorial, $type) );
                    }
                }
                if($numDirector > 0) {
                    $total = $opinion->count_inhome_type(2);
                    if ($total != $numDirector) {
                        $type = 'opinion del director';
                        m::add( sprintf(_("You must put %d opinions %s in the home widget"), $numDirector, $type) );
                    }
                }
            }
        }
        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&type_opinion=' .
                             $_SESSION['type'] . '&alert=' . $alert . '&page=' . $page);
    break;


    case 'batchDelete':
        Acl::checkOrForward('OPINION_DELETE');
        if(isset($_POST['selected_fld']) && count($_POST['selected_fld']) > 0) {
            $fields = $_POST['selected_fld'];

            $msg = 'Las opiniones ';

            if(is_array($fields)) {
                foreach($fields as $i) {
                    $opinion = new Opinion($i);
                    $rel = new RelatedContent();
                    $relationes = array();
                    $relationes = $rel->getContentRelations($i);//de portada

                    if(!empty($relationes)) {
                        $alert = 'ok';
                        $msg .= " \"" . $opinion->title . "\",    \n";
                    } else {
                        $opinion->delete($i, $_SESSION['userid'] );
                    }
                }
            }
        }
        if(isset($alert) && $alert =='ok') {
            $msg .= " tienen relacionados.  !Elimínelos uno a uno!";
            m::add($msg);
        }
        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&type_opinion=' .
                             $_SESSION['type'] .'&page=' . $page);
    break;

    case 'save_positions':
        if (isset($_POST['orden'])){
            $tok = strtok($_POST['orden'], ",");
            $_positions = array();
            $pos = 1;

            while(($tok !== false) && ($tok !=" ")) {
                if($tok) {
                    $_positions[] = array($pos, '1', $tok);
                    $tok = strtok(",");
                    $pos += 1;
                }
            }

            $opinion = new Opinion();
            $opinion->set_position($_positions, $_SESSION['userid']);

            // FIXME: buscar otra forma de hacerlo
            /* Eliminar caché portada cuando actualizan orden opiniones {{{ */
            $tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);
            $tplManager->delete('home|0');
            /* }}} */
        }

        exit(0);
    break;

    case 'update_title':
        $filter = '`pk_content` = ' . $_REQUEST['id'];
        $fields = array('title','fk_user_last_editor');
        $_REQUEST['fk_user_last_editor']=$_SESSION['userid'];
        SqlHelper::bindAndUpdate('contents', $fields, $_REQUEST, $filter);
        Application::ajaxOut('ok');
    break;

    case 'update_author':
        //opinion.php?action=update_author&id=2009112623263810168&pk_author=Ana Pastor
        $aut = new Author();
        $photos=$aut->get_author_photos($data['fk_author']);
        foreach($photos as $photo) {
             if($photo->width < 70) {
                $_REQUEST['fk_author_img_widget']=$photo->pk_img;
             } else {
                  $_REQUEST['fk_author_img']= $photo->pk_img;
             }
        }

        $filter = '`pk_opinion` = ' . $_REQUEST['id'];
        $fields = array('fk_author','fk_author_img_widget','fk_author_img');
        SqlHelper::bindAndUpdate('opinions', $fields, $_REQUEST, $filter);

        $filter1 = '`pk_content` = ' . $_REQUEST['id'];
        $_REQUEST['fk_user_last_editor']=$_SESSION['userid'];
        $fields1 = array('fk_user_last_editor');
        SqlHelper::bindAndUpdate('contents', $fields1, $_REQUEST, $filter1);
        Application::ajaxOut('ok');
    break;

    case 'get_authors_list':
        $aut = new Author();
        $autores = $aut->all_authors(NULL,'ORDER BY name');
        $autores = json_encode($autores);
        header('Content-type: application/json');
        Application::ajaxOut($autores);
    break;

    case 'changeavailable':
        $opinion->read($_REQUEST['id']);

        $available = ($opinion->available+1) % 2;
        $opinion->set_available($available, $_SESSION['userid']);

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
            list($img, $text)  = ($available)? array('g', 'PUBLICADDO'): array('r', 'PENDIENTE');

            echo '<img src="' . $tpl->image_dir . 'publish_' . $img . '.png" border="0" title="' . $text . '" />';
            exit(0);
        }

        Application::forward(SITE_URL_ADMIN.'/article.php?action=list&category='.$_REQUEST['category']);
    break;

    case 'changeFavorite':

        Acl::checkOrForward('OPINION_ADMIN');
        $contentID = filter_input ( INPUT_GET, 'id' , FILTER_SANITIZE_NUMBER_INT);
        $status = filter_input ( INPUT_GET, 'status' , FILTER_SANITIZE_NUMBER_INT);

        $opinion = new Opinion($contentID);
        $opinion->set_favorite($status,$_SESSION['userid']);

        Application::forward($_SERVER['SCRIPT_NAME'].
                '?action=list&category='.$_REQUEST['category'].'&page='.$page);

    break;

    case 'unpublish':
        $opinion = new Opinion();
        $opinion->read($_REQUEST['id']);
        $opinion->dropFromHomePageOfCategory($_REQUEST['category'],$_REQUEST['id']);
        /* Limpiar la cache de portada de todas las categorias */
        $c->refreshFrontpage();
        //$refresh = Content::refreshFrontpageForAllCategories();

        Application::forward(SITE_URL_ADMIN.'/article.php?action=list&category='.$_REQUEST['category']);
    break;

    case 'archive':
        $opinion = new Opinion();
        $opinion->read($_REQUEST['id']);
        $opinion->dropFromHomePageOfCategory($_REQUEST['category'],$_REQUEST['id']);
        /* Limpiar la cache de portada de todas las categorias */
        $c->refreshFrontpage();
        //$refresh = Content::refreshFrontpageForAllCategories();

        Application::forward(SITE_URL_ADMIN.'/article.php?action=list&category='.$_REQUEST['category']);
    break;


    case 'change_photos':

        $fk_author=$_REQUEST['fk_author'];
        $aut = new Author($fk_author);
        $photos = $aut->get_author_photos($fk_author);
        $out= "<ul id='thelist'  class='gallery_list'> ";

        if($photos) {
            foreach ($photos as $as) {
                $out.= "<li><img src='".MEDIA_IMG_PATH_WEB.$as->path_img."' id='".$as->pk_img."'  border='1' /></li>";
            }
        }

        $out.= "</ul>";
        Application::ajaxOut($out);
    break;

    case 'content-list-provider':
    case 'related-provider':

        $items_page = s::get('items_per_page') ?: 20;
        $page = filter_input( INPUT_GET, 'page' , FILTER_SANITIZE_STRING, array('options' => array('default' => '1')) );
        $cm = new ContentManager();

        list($opinions, $pager)= $cm->find_pages('Opinion', "available=1",
                                                 'ORDER BY starttime DESC ',
                                                  $page, $items_page);

        $tpl->assign(array( 'contents'=>$opinions,
                            'pagination'=>$pager->links,
                            'contentType'=>'Opinion',
                    ));

        $html_out = $tpl->fetch("common/content_provider/_container-content-list.tpl");
        Application::ajaxOut($html_out);

    break;

    case 'config':

        $configurationsKeys = array('opinion_settings',);
        $configurations = s::get($configurationsKeys);
        $tpl->assign(array(
            'configs'   => $configurations,
        ));

        $tpl->display('opinion/config.tpl');

    break;

    case 'save_config':

        Acl::checkOrForward('OPINION_SETTINGS');

        unset($_POST['action']);
        unset($_POST['submit']);

        foreach ($_POST as $key => $value ) { s::set($key, $value); }

        m::add(_('Settings saved successfully.'), m::SUCCESS);

        $httpParams = array(array('action'=>'list'),);
        Application::forward($_SERVER['SCRIPT_NAME'] . '?'.StringUtils::toHttpParams($httpParams));

    break;

    case 'content-provider':

            $category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_STRING,   array('options' => array( 'default' => 'home')));
            if ($category == 'home') { $category = 0; }

            // Get contents for this home
            $contentElementsInFrontpage  = $cm->getContentsIdsForHomepageOfCategory($category);

            // Fetching opinions
            $sqlExcludedOpinions = '';
            if(count($contentElementsInFrontpage) > 0) {
                $opinionsExcluded    = implode(', ', $contentElementsInFrontpage);
                $sqlExcludedOpinions = ' AND `pk_opinion` NOT IN ('.$opinionsExcluded.')';
            }

            list($opinions, $pager) = $cm->find_pages(
                'Opinion',
                'contents.available=1'. $sqlExcludedOpinions,
                'ORDER BY created DESC ', $page, 5
            );

            foreach ($opinions as $opinion) {
                $opinion->author = new Author($opinion->fk_author);
            }

            $tpl->assign(array(
                'opinions' => $opinions,
                'pager'    => $pager,
            ));

            $tpl->display('opinion/content-provider.tpl');

            break;

    default:
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&page=' . $page);
        break;
}
