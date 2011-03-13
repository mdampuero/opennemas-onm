<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNeMas project
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   OpenNeMas
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');



/**
 * Check privileges
*/
require_once(SITE_CORE_PATH.'privileges_check.class.php');
if(!Acl::check('OPINION_ADMIN')) {
    Acl::Deny();
}

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Gestión de Opinión');

// Register events
require_once('./opinion_events.php');

if (!isset($_SESSION['desde'])) {
    $_SESSION['desde'] = 'opinion';
}


if (!isset($_REQUEST['page'])) {
     $_REQUEST['page'] = 1;
}


if (!isset($_REQUEST['type_opinion'])) {
    $_REQUEST['type_opinion'] = -1;
}

$cm = new ContentManager();
$tpl->assign('type_opinion', $_REQUEST['type_opinion']);


if(isset($_REQUEST['action'])) {
    switch($_REQUEST['action']) {
        case 'list': {
            $order = ' position ASC, created DESC';
            $opinion = new Opinion();

            $algoritm = $opinion->get_opinion_algoritm();
            if($algoritm=='orden') {
                $order=' position ASC, created DESC';
            }
            

            $tpl->assign('algoritm', $algoritm);
            $comment = new Comment();


            $cm = new ContentManager();
            if($_REQUEST['type_opinion']!=-1) {
                //Para visualizar la HOME
                // ContentManager::find_pages(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>,<PAGE>,<ITEMS_PER_PAGE>,<CATEGORY>);
                list($opinions, $pager)= $cm->find_pages('Opinion', 'type_opinion=\''.$_REQUEST['type_opinion'].'\'',
                                                         'ORDER BY created DESC ', $_REQUEST['page'], 16);

                $tpl->assign('paginacion', $pager->links);
                $_SESSION['type'] = $_REQUEST['type_opinion'];

                $opinion=new Opinion();
                $total=$opinion->count_inhome_type($_REQUEST['type_opinion']);
                $alert="";
                if(($_REQUEST['type_opinion'] == 1) && ($total != 2)) {
                    $alert = 'Tiene que poner dos opiniones de editorial. Actualmente hay: '.$total.' editorial';
                } elseif(($_REQUEST['type_opinion'] == 2) && ($total != 1)) {
                     $alert = 'Tiene que poner una opinión del director. Actualmente hay: '.$total.' opinion del director';
                }
                $tpl->assign('msg_alert',$alert);
            } else {
                $opinions = $cm->find('Opinion', 'in_home=1 and available=1 and type_opinion=0',
                                      'ORDER BY type_opinion DESC, '.$order.' ');

                $editorial = $cm->find('Opinion', 'in_home=1 and available=1 and type_opinion=1',
                                       'ORDER BY created DESC LIMIT 0,2');

                $num_edit = count($editorial); //Para manipular el section
                $tpl->assign('num_edit', $num_edit);
                $director = $cm->find('Opinion', 'in_home=1 and available=1 and type_opinion=2',
                                      'ORDER BY created DESC LIMIT 0,1');

                $num_dir = count($director); //Para manipular el section
                $tpl->assign('num_dir', $num_dir);

                $rating = new Rating();

                foreach($editorial as $opin) {
                    $todos = $comment->get_comments( $opin->id );
                    $opin->comments = count($todos);
                    $opin->ratings = $rating->get_value($opin->id);
                }

                foreach($director as $opin) {
                    $todos = $comment->get_comments( $opin->id );
                    $opin->comments = count($todos);
                    $opin->ratings = $rating->get_value($opin->id);
                }
                $alert ="";
                 if($num_edit != 2) {
                    $alert .= 'Tiene que poner dos opiniones de editorial. Actualmente hay: '.$num_edit.' editorial <br /> ';
                }
                if($num_dir!= 1) {
                     $alert .= 'Tiene que poner una opinión del director. Actualmente hay: '.$num_dir.' opinion del director <br /> ';
                }
                $tpl->assign('msg_alert',$alert);
                $tpl->assign('editorial', $editorial);
                $tpl->assign('director', $director);

                $_SESSION['type'] = $_REQUEST['type_opinion'];
            }

            $tpl->assign('type_opinion', $_REQUEST['type_opinion']);
            $tpl->assign('algoritm', $algoritm);
            $rating = new Rating();

            $op_comment = $names = array();


            foreach ( $opinions as $opin) {
                $todos = $comment->get_comments( $opin->id );
                $aut = new Author($opin->fk_author);
                $names[] = $aut->name;
                $op_comment[] = count($todos);
                $ratings[] = $rating->get_value($opin->id);
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
        } break;

        case 'new': {
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
        } break;

        case 'read': {
            //habrá que tener en cuenta el tipo
            $opinion = new Opinion($_REQUEST['id']);
            $tpl->assign('opinion', $opinion);

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
        } break;

        case 'create': {
            $opin = new Opinion();
            $_POST['publisher'] = $_SESSION['userid'];

            $alert = '';
            
            if($opin->create($_POST)) {
                if($_SESSION['desde'] == 'index_portada') {
                    Application::forward('index.php');
                }
                $opinion = new Opinion($opin->id);
                $total = $opinion->count_inhome_type();

                if(($opinion->type_opinion == 1) && ($total != 2)) {
                    $alert = 'Tiene que poner dos opiniones de editorial. Actualmente hay: '.$total.' editorial';
                } elseif(($opinion->type_opinion == 2) && ($total != 1)) {
                     $alert = 'Tiene que poner una opinión del director. Actualmente hay: '.$total.' opinion del director';
                }

                Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&type_opinion=' .
                                     $_SESSION['type'] . '&alert=' . $alert . '&page=' . $_REQUEST['page']);
            } else {
                $tpl->assign('errors', $opinion->errors);
            }
        } break;

        case 'update': {

            //TODO : Revisar esto porque antes saltaba un warning
            $alert= '';

            $opinionCheck = new Opinion();
            $opinionCheck->read($_REQUEST['id']);

            if(!Acl::check('OPINION_ADMIN') && $opinionCheck->fk_user_last_editor != $_SESSION['userid']) {
                Acl::Deny('Acceso no permitido. Usted no es el editor de esta opinión');
            }

            $_REQUEST['fk_user_last_editor'] = $_SESSION['userid'];

            //Gestion del autor
            if($_REQUEST['type_opinion'] == 2){
                $_REQUEST['fk_author'] = 58; //Director, para que coja las fotos.
            } elseif ($_REQUEST['type_opinion'] == 1){
                $_REQUEST['fk_author'] = '';
            }

            $opinion = new Opinion();
            $opinion->update($_REQUEST);

            if($_SESSION['_from'] == 'search_advanced') {
                if($_GET['stringSearch']){
                    Application::forward('search_advanced.php?action=search&stringSearch=' .
                                         $_GET['stringSearch'] . '&category=' . $_SESSION['_from'] .
                                         '&page=' . $_REQUEST['page']);
                } else {
                    $_SESSION['desde'] = 'list';
                    $_SESSION['type'] = $_REQUEST['type_opinion'];
                }
            }

            if($_SESSION['desde'] == 'index_portada') {
                Application::forward('index.php');
            }elseif( $_SESSION['desde']=='list_pendientes'){
                Application::forward('article.php?action='.$_SESSION['desde'].'&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
            }else{
                Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&type_opinion=' .
                                 $_SESSION['type'] . '&alert=' . $alert . '&page=' . $_REQUEST['page']);
            }
        } break;

        case 'validate': {
            $opinion = null;
            if(empty($_POST["id"])) {
                $opinion = new Opinion();
                $_POST['publisher'] = $_SESSION['userid'];

                if($opinion->create( $_POST )) {
                    $tpl->assign('errors', $opinion->errors);
                }
            } else {
                $opinionCheck = new Opinion();
                $opinionCheck->read($_REQUEST['id']);

                if(!Acl::check('OPINION_ADMIN') && $opinionCheck->fk_user_last_editor != $_SESSION['userid']) {
                    Acl::Deny('Acceso no permitido. Usted no es el editor de esta opinión');
                }

                $_REQUEST['fk_user_last_editor'] = $_SESSION['userid'];
                if($_REQUEST['type_opinion'] == 2) {
                    $_REQUEST['fk_author'] = 58; //Director, para que coja las fotos.
                }elseif($_REQUEST['type_opinion'] == 1){
                      $_REQUEST['fk_author'] = '';
                }

                $opinion = new Opinion();
                $opinion->update($_REQUEST);
            }

            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=read&type_opinion=' .
                                 $_SESSION['type'] . '&id=' . $opinion->id);
        } break;

        case 'delete': {
            $opinionCheck = new Opinion();
            $opinionCheck->read($_REQUEST['id']);

            if(!Acl::check('OPINION_ADMIN') && $opinionCheck->fk_user_last_editor != $_SESSION['userid']) {
                Acl::Deny('Acceso no permitido. Usted no es el editor de esta opinión');
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
        } break;

        case 'yesdel': {
            if($_REQUEST['id']) {
                //Delete relations
                $rel= new Related_content();
                $rel->delete_all($_REQUEST['id']);

                $opinion = new Opinion($_REQUEST['id']);
                $opinion->delete($_REQUEST['id'], $_SESSION['userid']);
            }
            if( $_SESSION['desde']=='list_pendientes'){
                Application::forward('article.php?action='.$_SESSION['desde'].'&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
            }else{
                Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                                 $opinion->category . '&page=' . $_REQUEST['page']);
            }
        } break;

        case 'change_status': {
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
                Application::forward('article.php?action='.$_SESSION['desde'].'&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
            }else{
                Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&type_opinion=' .
                                 $_SESSION['type'] . '&page=' . $_REQUEST['page']);
            }
        } break;

        case 'mfrontpage': {
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
                                 $_SESSION['type'] . '&page=' . $_REQUEST['page']);
        } break;

        case 'mdelete': {
            if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld']) > 0) {
                $fields = $_REQUEST['selected_fld'];

                $msg = 'Las opiniones ';
                $alert = '';

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

            $msg .= " tienen relacionados.  !Elimínelos uno a uno!";
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&type_opinion=' .
                                 $_SESSION['type'] . '&msgdelete=' . $alert . '&msg=' . $msg .
                                 '&page=' . $_REQUEST['page']);
        } break;

        case 'inhome_status': {
            $opinion = new Opinion($_REQUEST['id']);
            $alert='';
            // FIXME: evitar otros valores erróneos
            $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
            $total = $opinion->count_inhome_type();
            if($status == 1) {
                if(($opinion->type_opinion == 1) && ($total >= 2)) {
                    $alert = 'No se pueden poner más opiniones de editorial';

                } elseif(($opinion->type_opinion == 2) && ($total >= 1)) {
                    $alert = 'Solo puede poner una opinión del director';
                } else {
                     $total++;
                    $opinion->set_inhome($status,$_SESSION['userid']);
                    $opinion->set_status($status, $_SESSION['userid']);
                    $opinion->set_available($status, $_SESSION['userid']);
                }
            } else {
                $opinion->set_inhome($status, $_SESSION['userid']);
                $total--;
            }
            if(($opinion->type_opinion == 1) && ($total < 2)) {
                 $alert = 'Tiene que poner dos opiniones de editorial';
            } elseif(($opinion->type_opinion == 2) && ($total < 1)) {
                 $alert = 'Tiene que poner una opinión del director';
            }

            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&type_opinion=' .
                                 $_SESSION['type'] . '&alert=' . $alert .
                                 '&page=' . $_REQUEST['page']);
        } break;

        case 'm_inhome_status': {
            $fields = $_REQUEST['selected_fld'];
            $alert = '';

            if(is_array($fields)) {
                $status = ($_REQUEST['id']==1)? 1: 0; // Evitar otros valores

                //Usa id para pasar el estatus
                foreach($fields as $i) {
                    $opinion = new Opinion($i);
                    // FIXME: evitar otros valores erróneos
                    if($status == 1) {
                        $total = $opinion->count_inhome_type();

                        if(($opinion->type_opinion == 1) && ($total >= 2)) {
                            $alert = 'No se pueden poner más opiniones de editorial';

                        } elseif(($opinion->type_opinion == 2) && ($total >= 1)) {
                            $alert = 'Solo puede poner una opinión del director';

                        } else {
                            $opinion->set_inhome($status, $_SESSION['userid']);
                            $opinion->set_status($status, $_SESSION['userid']);
                            $opinion->set_available($status, $_SESSION['userid']);
                            $total++;
                        }

                    } else {
                        $opinion->set_inhome($status, $_SESSION['userid']);
                        //$total--;
                    }
                    if(($opinion->type_opinion == 1) && ($total < 2)) {
                        $alert = 'Tiene que poner dos opiniones de editorial';
                    } elseif(($opinion->type_opinion == 2) && ($total < 1)) {
                        $alert = 'Tiene que poner una opinión del director';
                    }
                }
            }

            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&type_opinion=' .
                                 $_SESSION['type'] . '&alert=' . $alert . '&page=' . $_REQUEST['page']);
        } break;

        case 'change_algoritm': {
            if($_REQUEST['algoritm']) {
                $opinion = new Opinion();
                $opinion->set_opinion_algoritm($_REQUEST['algoritm']);
            }

            exit(0);
        } break;

        case 'save_positions': {
            if(isset($_REQUEST['orden'])){

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
        } break;

        case 'change_list_byauthor': {
            $cm = new ContentManager();
            if($_REQUEST['author'] == 0) {
                $_REQUEST['action'] = 'list'; //Para que sea correcta la paginacion.

                list($opinions, $pager)= $cm->find_pages('Opinion', 'type_opinion=0', 'ORDER BY  created DESC ',
                                                         $_REQUEST['page'], 16);
            } else {
                // $opinions = $cm->find('Opinion', 'opinions.fk_author=\''.$_REQUEST['author'].'\' and type_opinion=0',
                //                                  'ORDER BY created DESC LIMIT 0,20');
                list($opinions, $pager)= $cm->find_pages('Opinion', 'opinions.fk_author="'.$_REQUEST['author'].'" AND type_opinion=0',
                                                         'ORDER BY  created DESC ', $_REQUEST['page'], 16);

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
                $tpl->display('opinion/opinion_list.tpl');
                exit(0);
                // Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&type_opinion=0&page='.$_REQUEST['page']);
            }
        } break;

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
                 }else{
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

        case 'changeavailable': {
            $opinion->read($_REQUEST['id']);

            $available = ($opinion->available+1) % 2;
            $opinion->set_available($available, $_SESSION['userid']);

            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
                list($img, $text)  = ($available)? array('g', _('PUBLICADO')): array('r', _('PENDIENTE'));

                echo '<img src="' . $tpl->image_dir . 'publish_' . $img . '.png" border="0" title="' . $text . '" />';
                exit(0);
            }

            Application::forward(SITE_URL_ADMIN.'/article.php?action=list&category='.$_REQUEST['category']);
            break;
        }


        case 'unpublish': {
            //$widget->read($_REQUEST['id']);
            $cm->unpublishFromHomePage($_REQUEST['id']);

            Application::forward(SITE_URL_ADMIN.'/article.php?action=list&category='.$_REQUEST['category']);
            break;
        }

        case 'archive': {
            //$widget->read($_REQUEST['id']);
            $cm->dropFromHomePageOfCategory($_REQUEST['category'],$_REQUEST['id']);

            Application::forward(SITE_URL_ADMIN.'/article.php?action=list&category='.$_REQUEST['category']);
            break;
        }

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

        default: {
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&page=' . $_REQUEST['page']);
        } break;
    }
} else {
    Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list');
}

// FIXME: usar en template {$smarty.const.MEDIA_IMG_PATH_URL}
$tpl->assign('MEDIA_IMG_PATH_URL', MEDIA_IMG_PATH_WEB);

$tpl->display('opinion/opinion.tpl');
