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
require_once(dirname(__FILE__).'/../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');
 
// Check ACL
require_once(SITE_CORE_PATH.'privileges_check.class.php');
if(!Acl::check('COMMENT_ADMIN')) {    
    Acl::deny();
}

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

$tpl->assign('titulo_barra', 'Comment Management');



// Initialize request parameters
$page = filter_input ( INPUT_GET, 'page' , FILTER_SANITIZE_NUMBER_INT, array('options' => array('default' => 0)) );
$action = filter_input ( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
$category = filter_input ( INPUT_GET, 'category' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'todos')) );

$tpl->assign('category', $category);

if(isset($action)) {
    
    switch($action) {
        
        case 'list': {
            
            // Get all categories for the menu
            $ccm = ContentCategoryManager::get_instance();
            list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu();
            $tpl->assign('subcat', $subcat);
            $tpl->assign('allcategorys', $parentCategories);
            $tpl->assign('datos_cat', $datos_cat);

            // Set up the list of content types that could have comments
            $content_types = array(1 => _('Article') , 7 => _('Album'), 9 => _('Video'), 4 => _('Opinion'), 11 => _('Poll'));
            $tpl->assign('content_types', $content_types);
            
            $cm = new ContentManager();
            $commentStatus = filter_input ( INPUT_GET, 'comment_status' , FILTER_SANITIZE_NUMBER_INT, array('options' => array('default' => 0)) );

            $tpl->assign('comment_status', $commentStatus);
            $filter = "content_status = ".$commentStatus;
            
            if($category == 'encuesta') {
                
                $ccm = new ContentManager();
                list($comments, $pager)= $cm->find_pages('Comment',' '.$filter.' ', 'ORDER BY content_status, created DESC ',$page,10);
                $tpl->assign('paginacion', $pager);
                $tpl->assign('comments', $comments);
                
                $i = 0;
                $polls = array();
                $votes = array();
                foreach( $comments as $prima){
                    $polls[$i] = new Poll( $prima->fk_content );
                    $votes[$i] =new Vote( $prima->pk_comment );
                    $i++;
                }
                    
                $tpl->assign('articles', $polls);
                $tpl->assign('votes', $votes);
                
            } else {
                if($category == 'home') {
                    $comment = new Comment();
                    
                    //Comentarios de las noticias in_home
                    $comments = $comment->get_home_comments($filter);
                    if($comments) {
                        $comments = $cm->paginate_num($comments,10);
                        $tpl->assign('paginacion', $cm->pager);
                        $tpl->assign('comments', $comments);
                    }
                    
                } elseif($category == 'todos') {
                    $comment = new Comment();
                    // ContentManager::find_pages(<TIPO>, <WHERE>, <ORDER>, <PAGE>, <ITEMS_PER_PAGE>, <CATEGORY>);
                    list($comments, $pager)= $cm->find_pages('Comment', 'content_status = 0',
                                                             'ORDER BY  created DESC ',
                                                             $page, 10);
                    $tpl->assign('paginacion', $pager);
                    $tpl->assign('comments', $comments);
                    
                   
                } else {
                    // ContentManager::find_pages(<TIPO>, <WHERE>, <ORDER>, <PAGE>, <ITEMS_PER_PAGE>, <CATEGORY>);
                    list($comments, $pager) = $cm->find_pages('Comment', ' fk_content_type=6  and '.$filter.' ',
                                                              'ORDER BY content_status, created DESC ',
                                                              $page, 10, $category);
                    $tpl->assign('paginacion', $pager);
                    $tpl->assign('comments', $comments);
                    
                }
                
                $articles = array();
                $votes = array();
                $i = 0;  //Sacamos los articulos para el titulo
                if($comments) {
                    // sql sobre article y content * IN ($prima->fk_content1, $prima->fk_content2, ...)
                    $ids = array();
                   /* foreach($comments as $prima) {
                        $ids[] = $prima->fk_content;
                    }
                    */
                    foreach($comments as $prima){
                        $articles[$i] = new Content( $prima->fk_content );
                        $cat=$articles[$i]->loadCategoryName($prima->fk_content );
                        if (!$cat) {
                            $articles[$i]->category_name = 'Opinion';
                        }
                        $articles[$i]->category_name = $articles[$i]->loadCategoryName($prima->fk_content);
                        $votes[$i] =new Vote( $prima->pk_comment );
                         $i++;
                    }
                }
                $tpl->assign('articles', $articles);
                $tpl->assign('votes', $votes);
            }
        } break;
        
        case 'new': {
            // Nothing
        } break;
        
        case 'read': {
            
            $contentID = filter_input ( INPUT_GET, 'id' , FILTER_SANITIZE_NUMBER_INT);
            
            // habrá que tener en cuenta el tipo
            $comment = new Comment( $contentID );
            if(is_null($comment->pk_comment)) {                                  
                $comment = new Comment( $contentID );
                
                $tpl->assign('comment', $comment);
                $poll = new Poll( $comment->fk_content );
                $tpl->assign('article', $poll);
                
            } else {
                
                $tpl->assign('comment', $comment);
                
                $article = new Article( $comment->fk_content );
                $tpl->assign('article', $article);
                
                // ¿?¿?¿?
                $img1 = $article->img1;
                if(isset($img1)){
                    //Buscar foto where pk_foto=img1
                    $photo1 = new Photo($img1);
                }
                $tpl->assign('photo1', $photo1);
                
                $img2 = $article->img2;                
                if(isset($img2)) {
                    //Buscar foto where pk_foto=img2
                    $photo2 = new Photo($img2);
                }                
                $tpl->assign('photo2', $photo2);
                
            }
        } break;
        
        case 'update': {
            $comment = new Comment($_REQUEST['id']);
            
            if(!is_null($comment->pk_comment)) {
                $comment->update( $_REQUEST );
                if($_REQUEST['content_status'] == 1) {
                    $article=new Article($comment->fk_content);
                    //Para que cambie la fecha changed.
                    $article->set_status($article->content_status, $article->fk_user_last_editor);
                }
            } else {
                $comment = new PC_Comment();
                $comment->update( $_REQUEST );
            }
            
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                                 $category . '&page=' . $page);
        } break;
        
        case 'create': {
            $comment = new Comment();
            if($comment->create( $_POST )) {
                Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                                     $category . '&page=' . $page);
            } else {
                $tpl->assign('errors', $comment->errors);
            }
        } break;
        
        case 'delete': {            
            
            if($category == 'encuesta'){
                $comment = new PC_Comment();
                $comment->delete($_REQUEST['id']);
            } else {
                $comment = new Comment();
                $comment->delete($_POST['id'], $_SESSION['userid']);
            }
            
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                                 $category . '&page=' . $page);
        } break;
        
        case 'change_status': {
            if((isset ($_REQUEST['tipo']) && $_REQUEST['tipo'] == 'encuesta') || ($category == 'encuesta')){
                $comment = new PC_Comment($_REQUEST['id']);                     
                $comment->set_status($_REQUEST['status'], $_SESSION['userid']);
            } else {
                $comment = new Comment($_REQUEST['id']);
                
                //Publicar o no,
                if($_REQUEST['status'] == 2) {
                    $comment->set_status($_REQUEST['status'], $_SESSION['userid']);                               
                } else {
                    // Ya se cambia en el set_available    $comment->set_status($status,$_SESSION['userid']);
                    $comment->set_available($_REQUEST['status'], $_SESSION['userid']);
                    $article=new Article($comment->fk_content);
                    //Para que cambie la fecha changed.
                    $article->set_status($article->content_status, $article->fk_user_last_editor);
                }
            }
            
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                                 $category . '&comment_status=' . $_REQUEST['comment_status'] . '&page=' .
                                 $page);
        } break;
        
        case 'mfrontpage': {
            
            if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld']) > 0) {
                $fields = $_REQUEST['selected_fld'];
                $status = $_REQUEST['id'];
                
                if(is_array($fields)) {
                    foreach($fields as $i ) {
                        $comment = new Comment($i);
                        
                        if(!is_null($comment->pk_comment)) {
                            // Ya se cambia en el set_available  $comment->set_status($status,$_SESSION['userid']);
                            //Se reutiliza el id para pasar el estatus
                            $comment->set_available($status, $_SESSION['userid']);
                            $article = new Article($comment->fk_content);
                            
                            //Para que cambie la fecha changed.
                            $article->set_status($article->content_status, $article->fk_user_last_editor);
                        } else {
                            $comment = new PC_Comment($i);                                                   
                            $comment->set_status($status, $_SESSION['userid']);
                        }
                    }
                }
            }
            
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                                 $category . '&page=' . $page);
        } break;
        
        case 'mdelete': {
            if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld']) > 0) {
                $fields = $_REQUEST['selected_fld'];
                
                if(is_array($fields)) {
                    foreach($fields as $i ) {
                        $comment = new Comment($i);
                        if(!is_null($comment->pk_comment)) {
                            $comment->delete( $i, $_SESSION['userid'] );
                        } else {
                            $comment = new PC_Comment($i);
                            $comment->delete( $i, $_SESSION['userid'] ); 
                        }
                    }
                }
            }
            
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                                 $category . '&page=' . $page);
        } break;
        
        default: {
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                                 $category . '&page=' . $page);
        } break;
    }
} else {
    Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                         $category . '&page=' . $page);
}

$tpl->display('comment.tpl');