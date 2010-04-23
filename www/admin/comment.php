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

//error_reporting(E_ALL);
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

require_once('./core/application.class.php');
Application::import_libs('*');
$app = Application::load();

// Check ACL
require_once('./core/privileges_check.class.php');
if(!Acl::_('COMMENT_ADMIN')) {    
    Acl::deny();
}

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

$tpl->assign('titulo_barra', 'Comments Manager');

require_once './core/content_manager.class.php';
require_once './core/content.class.php';
require_once './core/content_category.class.php';
require_once './core/comment.class.php';
require_once './core/content_category.class.php';
require_once './core/content_category_manager.class.php';
require_once './core/content.class.php';
require_once './core/vote.class.php';


if(!isset($_REQUEST['category'])) {
    $_REQUEST['category'] = 'todos';
}
$tpl->assign('category', $_REQUEST['category']);

// Initialize $_REQUEST['page']
$_REQUEST['page'] = (isset($_REQUEST['page']))? $_REQUEST['page']: 0;

if(isset($_REQUEST['action'])) {
    
    switch($_REQUEST['action']) {
        
        case 'list': {
            $ccm = ContentCategoryManager::get_instance();
            list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu();
            
            $tpl->assign('subcat', $subcat);
            $tpl->assign('allcategorys', $parentCategories);
            $tpl->assign('datos_cat', $datos_cat);
         
            $cm = new ContentManager();
            if (!isset($_REQUEST['comment_status'])) {
                $_REQUEST['comment_status'] = 0;
            }
            if (!isset($_GET['page']) || empty($_GET['page'])) {$_GET['page'] = 1;}

            $tpl->assign('comment_status', $_REQUEST['comment_status']);
            $filter="content_status = ".$_REQUEST['comment_status'];
            $content_types=$cm->get_types();
            $tpl->assign('content_types', $content_types);
            if($_REQUEST['category'] == 'home') {
                $comment = new Comment();
                //Comentarios de las noticias in_home
                $comments = $comment->get_home_comments($filter);
                if($comments) {
                    $comments = $cm->paginate_num($comments,10);
                    $tpl->assign('paginacion', $cm->pager);
                    $tpl->assign('comments', $comments);
                }

            } elseif($_REQUEST['category'] == 'todos') {
                $comment = new Comment();
                // ContentManager::find_pages(<TIPO>, <WHERE>, <ORDER>, <PAGE>, <ITEMS_PER_PAGE>, <CATEGORY>);
                list($comments, $pager)= $cm->find_pages('Comment', 'content_status = 0',
                                                         'ORDER BY  created DESC ',
                                                         $_REQUEST['page'], 10);
                $tpl->assign('paginacion', $pager);
                $tpl->assign('comments', $comments);

                 
            } else {
                // ContentManager::find_pages(<TIPO>, <WHERE>, <ORDER>, <PAGE>, <ITEMS_PER_PAGE>, <CATEGORY>);
                list($comments, $pager) = $cm->find_pages('Comment', ' fk_content_type=6  and '.$filter.' ',
                                                          'ORDER BY content_status, created DESC ',
                                                          $_REQUEST['page'], 10, $_REQUEST['category']);
                $tpl->assign('paginacion', $pager);
                $tpl->assign('comments', $comments);

            }

            $content = array();
            $i = 0;  //Sacamos los articulos para el titulo
            if($comments) {
                // sql sobre content y content * IN ($prima->fk_content1, $prima->fk_content2, ...)
                $ids = array();
               
                foreach($comments as $prima){
                    $contents[$i] = new Content( $prima->fk_content );
                     
                    $content[$i]->category_name = $contents[$i]->loadCategoryName($prima->fk_content);
                    $votes[$i] =new Vote( $prima->pk_comment );
                     $i++;
                }
            }
            $tpl->assign('contents', $contents);
            $tpl->assign('votes', $votes);
 
        } break;
        
        case 'new': {
            // Nothing
        } break;
        
        case 'read': {
            //habrá que tener en cuenta el tipo
            $comment = new Comment( $_REQUEST['id'] );

            $tpl->assign('comment', $comment);

            $content = new Content( $comment->fk_content );
            $tpl->assign('content', $content);

        } break;
        
        case 'update': {
            $comment = new Comment($_REQUEST['id']);

            $comment->update( $_REQUEST );
            if($_REQUEST['content_status'] == 1) {
                $content=new content($comment->fk_content);
                //Para que cambie la fecha changed.
                $content->set_status($content->content_status, $content->fk_user_last_editor);
            }
            
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                                 $_REQUEST['category'] . '&page=' . $_REQUEST['page']);
        } break;
        
        case 'create': {
            $comment = new Comment();
            if($comment->create( $_POST )) {
                Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                                     $_REQUEST['category'] . '&page=' . $_REQUEST['page']);
            } else {
                $tpl->assign('errors', $comment->errors);
            }
        } break;
        
        case 'delete': {            

            $comment = new Comment();
            $comment->delete($_POST['id'], $_SESSION['userid']);

            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                                 $_REQUEST['category'] . '&page=' . $_REQUEST['page']);
        } break;
        
        case 'change_status': {

            $comment = new Comment($_REQUEST['id']);

            //Publicar o no,
            if($_REQUEST['status'] == 2) {
                $comment->set_status($_REQUEST['status'], $_SESSION['userid']);
            } else {
                // Ya se cambia en el set_available    $comment->set_status($status,$_SESSION['userid']);
                $comment->set_available($_REQUEST['status'], $_SESSION['userid']);
                $content=new content($comment->fk_content);
                //Para que cambie la fecha changed.
                $content->set_status($content->content_status, $content->fk_user_last_editor);
            }
            
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                                 $_REQUEST['category'] . '&comment_status=' . $_REQUEST['comment_status'] . '&page=' .
                                 $_REQUEST['page']);
        } break;
        
        case 'mfrontpage': {
            
            if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld']) > 0) {
                $fields = $_REQUEST['selected_fld'];
                $status = $_REQUEST['id'];
                
                if(is_array($fields)) {
                    foreach($fields as $i ) {
                        $comment = new Comment($i);
                        //Se reutiliza el id para pasar el estatus
                        $comment->set_available($status, $_SESSION['userid']);
                        $content = new content($comment->fk_content);

                        //Para que cambie la fecha changed.
                        $content->set_status($content->content_status, $content->fk_user_last_editor);

                    }
                }
            }
            
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                                 $_REQUEST['category'] . '&page=' . $_REQUEST['page']);
        } break;
        
        case 'mdelete': {
            if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld']) > 0) {
                $fields = $_REQUEST['selected_fld'];
                
                if(is_array($fields)) {
                    foreach($fields as $i ) {
                        $comment = new Comment($i);
                        $comment->delete( $i, $_SESSION['userid'] );

                    }
                }
            }
            
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                                 $_REQUEST['category'] . '&page=' . $_REQUEST['page']);
        } break;
        
        default: {
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                                 $_REQUEST['category'] . '&page=' . $_REQUEST['page']);
        } break;
    }
} else {
    Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                         $_REQUEST['category'] . '&page=' . $_REQUEST['page']);
}
 
$tpl->display('comment.tpl');