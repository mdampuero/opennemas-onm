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
$tpl->assign('titulo_barra', _('Opinion Manager'));

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
 
if (!isset($_REQUEST['type_opinion'])) {
    $_REQUEST['type_opinion'] = -1;
}

$c = new Content();
$cm = new ContentManager();
$tpl->assign('type_opinion', $_REQUEST['type_opinion']);


$action = filter_input( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
if (!isset($action)) {
    $action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}


    switch($action) {
        case 'list':

            $configurations = s::get('opinion_settings');
            
            $numEditorial = $configurations['total_editorial'];
            $numDirector = $configurations['total_director'];

            $cm = new ContentManager();
            $rating = new Rating();
            $comment = new Comment();

            if($_REQUEST['type_opinion'] != -1) {
                //Para visualizar la HOME
                // ContentManager::find_pages(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>,<PAGE>,<ITEMS_PER_PAGE>,<CATEGORY>);
                list($opinions, $pager)= $cm->find_pages('Opinion', 'type_opinion=\''.$_REQUEST['type_opinion'].'\'',
                                                         'ORDER BY created DESC ', $page, 16);

                $tpl->assign('paginacion', $pager->links);
                $_SESSION['type'] = $_REQUEST['type_opinion'];
                $number = 2;

                $opinion=new Opinion();
                $total = $opinion->count_inhome_type($_REQUEST['type_opinion']);
                $alert="";

                if (($_REQUEST['type_opinion'] == 1) && ($total != $numEditorial)) {
                    $type = 'editorial';
                    $number = $numEditorial;
                } elseif (($_REQUEST['type_opinion'] == 2) && ($total != $numDirector)) {
                     $type = 'opinion del director';
                     $number = $numDirector;
                }
                if (!empty($type)) {
                    m::add( sprintf(_("You must put %d opinions %s in the home widget"), $number, $type) );
                }
            } else {

                $opinions = $cm->find('Opinion', 'in_home=1 and available=1 and type_opinion=0',
                                      'ORDER BY type_opinion DESC, position ASC, created DESC');
 
                $editorial = $cm->find('Opinion', 'in_home=1 and available=1 and type_opinion=1',
                                       'ORDER BY created DESC LIMIT 0,'.$numEditorial);


                $director = $cm->find('Opinion', 'in_home=1 and available=1 and type_opinion=2',
                                      'ORDER BY created DESC LIMIT 0,'.$numDirector);
 
                if ((count($editorial) != $numEditorial)) {
                    $type = 'editorial';
                    m::add( sprintf(_("You must put %d opinions %s in the home widget"), $numEditorial, $type) );
                }
                if ((count($director) != $numDirector)) {
                     $type = 'opinion del director';
                     m::add( sprintf(_("You must put %d opinions %s in the home widget"), $numDirector, $type) );
                }

                if (!empty($editorial)) {
                    foreach($editorial as $opin) {
                        $todos = $comment->get_comments( $opin->id );
                        $opin->comments = count($todos);
                        $opin->ratings = $rating->get_value($opin->id);
                    }
                }

                if (!empty($director)) {
                    foreach($director as $opin) {
                        $todos = $comment->get_comments( $opin->id );
                        $opin->comments = count($todos);
                        $opin->ratings = $rating->get_value($opin->id);
                    }
                }
              
                $tpl->assign('editorial', $editorial);
                $tpl->assign('director', $director);

                $_SESSION['type'] = $_REQUEST['type_opinion'];
            }

            $tpl->assign('type_opinion', $_REQUEST['type_opinion']);
 
            $op_comment = $names = array();

            if (!empty($opinions)) {
                foreach ( $opinions as $opin) {
                    $todos = $comment->get_comments( $opin->id );
                    $aut = new Author($opin->fk_author);
                    $names[] = $aut->name;
                    $op_comment[] = count($todos);
                    $ratings[] = $rating->get_value($opin->id);
                }
            }

            $tpl->assign('op_comment', $op_comment);
            $tpl->assign('names', $names);
            if(isset($op_rating)){
                $tpl->assign('ratings', $op_rating);
            }

            $aut = new Author();
            $autores = $aut->all_authors(NULL,'ORDER BY name');
            $tpl->assign('autores', $autores);

            $tpl->assign('opinions', $opinions);

            $_SESSION['desde'] = 'opinion';
            $_SESSION['_from'] = 'opinion.php';

            $tpl->display('opinion/list.tpl');
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
            /*    require_once(SITE_CORE_PATH.'template_cache_manager.class.php');
                $tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);
                $tplManager->delete('opinion|1');
                if($_SESSION['desde'] == 'index_portada') {
                    Application::forward('index.php');
                }
             * 
             */
                $opinion = new Opinion($opin->id);
                $total = $opinion->count_inhome_type();

                $configurations = s::get('opinion_settings');
                $numEditorial = $configurations['total_editorial'];
                $numDirector = $configurations['total_director'];

                if (($opinion->type_opinion == 1) && ($total != $numEditorial)) {
                    $type = 'editorial';
                    m::add( sprintf(_("You must put %d opinions %s in the home widget"), $numEditorial, $type) );
                } elseif (($opinion->type_opinion == 2) && ($total != $numDirector)) {
                     $type = 'opinion del director';
                     m::add( sprintf(_("You must put %d opinions %s in the home widget"), $numDirector, $type) );
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

            require_once(SITE_CORE_PATH.'template_cache_manager.class.php');
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
                    $_SESSION['type'] = $_REQUEST['type_opinion'];
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

        case 'delete':
            Acl::checkOrForward('OPINION_DELETE');
            $opinionCheck = new Opinion();
            $opinionCheck->read($_REQUEST['id']);

            if( !Acl::isAdmin() &&
                    !Acl::check('CONTENT_OTHER_UPDATE') &&
                    $opinionCheck->fk_user_last_editor != $_SESSION['userid']) {
                m::add(_("You can't delete this opinion because you don't have enought privileges.") );
            }

            $opinion = new Opinion($_REQUEST['id']);
            $rel = new Related_content();
            $relationes = array();
            $relationes = $rel->get_content_relations( $_REQUEST['id'] ); //de portada

            if(!empty($relationes)) {
                $msg = " La opini&oacute;n  '" . $opinion->title . "' , está relacionada con los siguientes articulos: \n";
                $cm = new ContentManager();
                $relat = $cm->getContents($relationes);

                foreach($relat as $contents) {
                    $msg .= " - " . strtoupper($contents->content_type) . " - " . $contents->category_name .
                            " " . $contents->title . "\n";
                }

                $msg .= "\n\n¡Ojo! Si la borra, se eliminaran las relaciones de la opinion con los articulos";
                $msg .= "\n¿Desea eliminarlo igualmente?";

                echo $msg;
                exit(0);
            } else {
                $msg =" ¿Está seguro que desea eliminar '" . $opinion->title . "' ?";

                echo $msg;
                exit(0);
            }
        break;

        case 'yesdel':
            Acl::checkOrForward('OPINION_DELETE');
            if($_REQUEST['id']) {
                //Delete relations
                $rel= new Related_content();
                $rel->delete_all($_REQUEST['id']);

                $opinion = new Opinion($_REQUEST['id']);
                $opinion->delete($_REQUEST['id'], $_SESSION['userid']);
                require_once(SITE_CORE_PATH.'template_cache_manager.class.php');
                $tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);
                $tplManager->delete('opinion|1');
            }
            if( $_SESSION['desde']=='list_pendientes'){
                Application::forward(SITE_URL_ADMIN.'/article.php?action='.$_SESSION['desde'].'&category='.$_REQUEST['category'].'&page='.$page);
            }else{
                Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                                 $opinion->category . '&page=' . $page);
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

        case 'mfrontpage':
            Acl::checkOrForward('OPINION_FRONTPAGE');
            if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld']) > 0) {
                $fields = $_REQUEST['selected_fld'];
                $status = ($_REQUEST['id']==1)? 1: 0; // Evitar otros valores
                if(is_array($fields)) {
                    foreach($fields as $i) {
                        $opinion = new Opinion($i);
                        $opinion->set_available($status, $_SESSION['userid']);
                        //  $opinion->set_status($_REQUEST['id'],$_SESSION['userid']);
                        //Se reutiliza el id para pasar el estatus
                    }
                }
            }

            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&type_opinion=' .
                                 $_SESSION['type'] . '&page=' . $page);
        break;

        case 'mdelete':
            Acl::checkOrForward('OPINION_DELETE');
            if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld']) > 0) {
                $fields = $_REQUEST['selected_fld'];

                $msg = 'Las opiniones ';

                if(is_array($fields)) {
                    foreach($fields as $i) {
                        $opinion = new Opinion($i);
                        $rel = new Related_content();
                        $relationes = array();
                        $relationes = $rel->get_content_relations( $i );//de portada

                        if(!empty($relationes)) {
                            $alert = 'ok';
                            $msg .= " \"" . $opinion->title . "\",    \n";
                        } else {
                            $opinion->delete($i, $_SESSION['userid'] );
                        }
                    }
                }
            }
            if($alert =='ok') {
                $msg .= " tienen relacionados.  !Elimínelos uno a uno!";
                m::add($msg);
            }
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&type_opinion=' .
                                 $_SESSION['type'] .'&page=' . $page);
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
                $type = 'editorial';
                m::add( sprintf(_("You must put %d opinions %s in the home widget"), $numEditorial, $type) );
            } elseif (($opinion->type_opinion == 2) && ($total != $numDirector)) {
                 $type = 'opinion del director';
                 m::add( sprintf(_("You must put %d opinions %s in the home widget"), $numDirector, $type) );
            }

            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&type_opinion=' .
                                 $_SESSION['type'] . '&alert=' . $alert .
                                 '&page=' . $page);
        break;

        case 'm_inhome_status':
            Acl::checkOrForward('OPINION_HOME');
            $fields = $_REQUEST['selected_fld'];
            $alert = '';

            if(is_array($fields)) {
                $status = ($_REQUEST['id']==1)? 1: 0; // Evitar otros valores

                //Usa id para pasar el estatus
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
                $total = $opinion->count_inhome_type(1);
                if ($total != $numEditorial) {
                    $type = 'editorial';
                    m::add( sprintf(_("You must put %d opinions %s in the home widget"), $numEditorial, $type) );
                }
                $total = $opinion->count_inhome_type(2);
                if ($total != $numDirector) {
                     $type = 'opinion del director';
                     m::add( sprintf(_("You must put %d opinions %s in the home widget"), $numDirector, $type) );
                }
            }

            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&type_opinion=' .
                                 $_SESSION['type'] . '&alert=' . $alert . '&page=' . $page);
        break;

        case 'save_positions':
            if (isset($_REQUEST['orden'])){
                $tok = strtok($_REQUEST['orden'], ",");
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
                require_once(SITE_CORE_PATH.'template_cache_manager.class.php');
                $tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);
                $tplManager->delete('home|0');
                /* }}} */
            }

            exit(0);
        break;

        case 'change_list_byauthor':
            $cm = new ContentManager();
            if($_REQUEST['author'] == 0) {
                $_REQUEST['action'] = 'list'; //Para que sea correcta la paginacion.

                list($opinions, $pager)= $cm->find_pages('Opinion', 'type_opinion=0', 'ORDER BY  created DESC ',
                                                         $page, 16);
            } else {
                // $opinions = $cm->find('Opinion', 'opinions.fk_author=\''.$_REQUEST['author'].'\' and type_opinion=0',
                //                                  'ORDER BY created DESC LIMIT 0,20');
                list($opinions, $pager)= $cm->find_pages('Opinion', 'opinions.fk_author="'.$_REQUEST['author'].'" AND type_opinion=0',
                                                         'ORDER BY  created DESC ', $page, 16);

                $params = $_REQUEST['author'];

                if($pager->_totalItems>16){
                    $pager = $cm->create_paginate($pager->_totalItems, 16, 2, 'changepageList', $params);
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
                // Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&type_opinion=0&page='.$page);
            }

        break;

        case 'update_title':
            $filter = '`pk_content` = ' . $_REQUEST['id'];
            $fields = array('title','fk_user_last_editor');
            $_REQUEST['fk_user_last_editor']=$_SESSION['userid'];
            SqlHelper::bindAndUpdate('contents', $fields, $_REQUEST, $filter);
            Application::ajax_out('ok');
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
            Application::ajax_out('ok');
        break;

        case 'get_authors_list':
            $aut = new Author();
            $autores = $aut->all_authors(NULL,'ORDER BY name');
            $autores = json_encode($autores);
            header('Content-type: application/json');
            Application::ajax_out($autores);
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
            Application::ajax_out($out);
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
            Application::forward($_SERVER['SCRIPT_NAME'] . '?'.String_Utils::toHttpParams($httpParams));

        break;

        default:
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&page=' . $page);
        break;
    }
 